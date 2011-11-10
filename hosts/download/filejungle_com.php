<?php
if (!defined('RAPIDLEECH')) {
    require_once ("index.html");
    exit();
}

class filejungle_com extends DownloadClass {

    public function Download($link) {
        global $premium_acc;
        // check link
        if (preg_match('@http:\/\/filejungle\.com\/l\/[^|\r|\n]+@i', $link, $dir)) {
            if (!$dir[0]) html_error('Filejungle folder link can\'t be found!');
            $check = $this->GetPage($link);
            preg_match_all('@http:\/\/www\.filejungle\.com\/f\/[^"]+@i', $check, $fj, PREG_SET_ORDER);
            $arr_link = array();
            foreach ($fj as $match) {
                $arr_link[] = $match[0];
            }
            if (!$arr_link) html_error('Can\'t find filejungle single link, probably folder is empty?');
            $this->moveToAutoDownloader($arr_link);
        }
        if (($_REQUEST['premium_acc'] == 'on' && $_REQUEST['premium_user'] && $_REQUEST['premium_pass']) || ($_REQUEST['premium_acc'] == 'on' && $premium_acc['filejungle_com']['user'] && $premium_acc['filejungle_com']['pass'])) {
            return $this->Premium($link);
        } else {
            return $this->Free($link);
        }
    }

    private function Premium($link) {
        $pA = ($_REQUEST["premium_user"] && $_REQUEST["premium_pass"] ? true : false);
        $cookie = $this->Login($pA);
        $page = $this->GetPage($link, $cookie);
        is_present($page, 'This file is no longer available.');
        if (!stristr($page, 'Location:')) {
            $page = $this->GetPage($link, $cookie, array('download' => 'premium'), $link);
        }
        $dlink = trim(cut_str($page, "Location: ", "\r\n"));
        if (empty($dlink)) html_error('Error: Premium download link not found, plugin need to be updated!');
        $FileName = urldecode(basename(parse_url($dlink, PHP_URL_PATH)));
        $this->RedirectDownload($dlink, $FileName, $cookie);
    }

    private function Login($pA = false) {
        global $premium_acc;
        $user = ($pA ? $_REQUEST["premium_user"] : $premium_acc["filejungle_com"]["user"]);
        $pass = ($pA ? $_REQUEST["premium_pass"] : $premium_acc["filejungle_com"]["pass"]);
        if (empty($user) || empty($pass)) {
            html_error("Login Failed: Username or Password is empty!");
        }

        $posturl = "http://filejungle.com/";
        $post['autoLogin'] = 'on';
        $post['loginUserName'] = $user;
        $post['loginUserPassword'] = $pass;
        $post['loginFormSubmit'] = 'Login';

        $page = $this->GetPage($posturl . "login.php", 0, $post, $posturl);
        is_present($page, "The length of user name should be larger than or equal to 6");
        is_present($page, "Username doesn't exist.");
        is_present($page, "Wrong password.");

        $cookie = GetCookies($page);
        is_notpresent($cookie, "cookie=", "Login error. Problem with cookie cache?");

        $page = $this->GetPage($posturl . 'dashboard.php', $cookie, 0, $posturl);
        is_present($page, "FREE<span>", "Account Free! What do you expect from that?");

        return $cookie;
    }

    private function Free($link) {
        if ($_REQUEST['step'] == 'Captcha') {
            $post['recaptcha_challenge_field'] = $_POST['recaptcha_challenge_field'];
            $post['recaptcha_response_field'] = $_POST['captcha'];
            $post['recaptcha_shortencode_field'] = $_POST['recaptcha_shortencode_field'];
            $cookie = urldecode($_POST['cookie']);
            $link = urldecode($_POST['link']);
            $check = $this->GetPage('http://www.filejungle.com/checkReCaptcha.php', $cookie, $post, $link . "\r\nX-Requested-With: XMLHttpRequest");
        } else {
            $this->page = $this->GetPage($link);
            is_present($this->page, 'This file is no longer available.');
            $cookie = GetCookies($this->page);
            $check = $this->GetPage($link, $cookie, array('checkDownload' => 'check'), $link . "\r\nX-Requested-With: XMLHttpRequest");
        }
        if (preg_match('@\{"(\w+)":"?([a-zA-Z0-9]+)"?(,"(\w+)":"?([a-zA-Z0-9\-]+)"?)?@i', $check, $match)) {
            if ($match[1] == 'success' && $match[4] !== 'error') {
                switch ($match[2]) {
                    case 'showCaptcha':
                        if (!preg_match('@reCAPTCHA_publickey=\'([^\']+)@i', $this->page, $cap)) html_error('Captcha not found!');
                        if (!preg_match('@shortencode_field" value="([^"]+)@i', $this->page, $rsc)) html_error('Link id not found!');

                        $ch = cut_str($this->GetPage("http://www.google.com/recaptcha/api/challenge?k=$cap[1]&ajax=1"), "challenge : '", "'");
                        if ($ch) {
                            $page = $this->GetPage("http://www.google.com/recaptcha/api/image?c=" . $ch);
                            $pass_img = substr($page, strpos($page, "\r\n\r\n") + 4);
                            $imgfile = DOWNLOAD_DIR . "filejungle_captcha.jpg";
                            if (file_exists($imgfile)) unlink($imgfile);
                            write_file($imgfile, $pass_img);
                        } else {
                            html_error('Can\'t find captcha image?');
                        }
                        $data = $this->DefaultParamArr($link, $cookie);
                        $data['recaptcha_shortencode_field'] = $rsc[1];
                        $data['recaptcha_challenge_field'] = $ch;
                        $data['step'] = 'Captcha';
                        $this->EnterCaptcha("$imgfile?" . time(), $data, 20);
                        exit();
                        break;
                    case 'showTimmer': case '1':
                        $check = $this->GetPage($link, $cookie, array('downloadLink' => 'wait'), $link . "\r\nX-Requested-With: XMLHttpRequest");
                        if (!preg_match('@waitTime":(\d+),@', $check, $wait)) html_error('Timer not found?');
                        $this->CountDown($wait[1]);
                        $check = $this->GetPage($link, $cookie, array('downloadLink' => 'show'), $link . "\r\nX-Requested-With: XMLHttpRequest");
                        is_present($check, 'forcePremiumDownload_exceed_4', 'You have reach download size limit for free user!');
                        $this->page = $this->GetPage($link, $cookie, array('download' => 'normal'), $link . "\r\nX-Requested-With: XMLHttpRequest");
                        break;
                }
            }
            is_present($match[2], 'timeLimit', 'Please wait for 1200 seconds to download the next file.');
            is_present($match[2], "captchaFail", "Your IP has failed the captcha too many times. Please retry again in $match[5] mins");
            is_present($match[5], "incorrect-captcha-sol", "Entered captcha was incorrect.");
        }
        if (!preg_match('@Location: (http:\/\/[a-zA-Z0-9]+\.filejungle\.com\/[^|\r|\n]+)@i', $this->page, $dl)) html_error('Error: Free download link not found, plugin need to be updated!');
        $dlink = trim($dl[1]);
        $FileName = urldecode(basename(parse_url($dlink, PHP_URL_PATH)));
        $this->RedirectDownload($dlink, $FileName, $cookie, 0, $link);
        exit();
    }

}

/*
 * filejungle download plugin by Ruud v.Tony 01/11/2011
 */
?>