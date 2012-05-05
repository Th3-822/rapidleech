<?php
if (!defined('RAPIDLEECH')) {
    require_once("index.html");
    exit;
}

class freakshare_com extends DownloadClass {

    public function Download($link) {
        global $premium_acc;

        if (!$_REQUEST['step']) {
            $link = str_replace('freakshare.net/', 'freakshare.com/', $link);
            $this->page = $this->GetPage($link);
            is_present($this->page, "This file does not exist!");
            $this->cookie = GetCookiesArr($this->page);
        }
		$this->link = $link;
        if ($_REQUEST['premium_acc'] == 'on' && (($_REQUEST['premium_user'] && $_REQUEST['premium_pass']) || (!empty($premium_acc['freakshare_com']['user']) && !empty($premium_acc['freakshare_com']['pass'])))) {
            return $this->Login();
        } else {
            return $this->Free();
        }
    }

    private function Free() {
        global $PHP_SELF;

        switch ($_REQUEST['step']) {
            case 'captcha':
                $post['recaptcha_challenge_field'] = $_POST['recaptcha_challenge_field'];
                $post['recaptcha_response_field'] = $_POST['recaptcha_response_field'];
                $post['section'] = $_POST['section'];
                $post['did'] = $_POST['did'];
				$post['submit'] = $_POST['submit'];
                $this->link = urldecode($_POST['link']);
                $this->cookie = urldecode($_POST['cookie']);
                $page = $this->GetPage($this->link, $this->cookie, $post, $this->link);
                break;
            case 'countdown':
                $this->link = urldecode($_POST['link']);
                $this->cookie = StrToCookies(urldecode($_POST['cookie']));
                $page = $this->GetPage($this->link, $this->cookie, 0, $this->link);
				$this->cookie = GetCookiesArr($page, $this->cookie);
				$post = array();
				$post['section'] = $_POST['section'];
				$post['did'] = $_POST['did'];
				$post['submit'] = 'Free Download';
                $page = $this->GetPage($this->link, $this->cookie, $post, $this->link);
                break;
            default:
                if (!preg_match('@var time = (\d+)\.?[0-9]?@', $this->page, $w)) html_error("Error [Timer not found!]");
                $wait = trim($w[1]);
                $form = cut_str($this->page, '<td width="138" height="10"', '</form>');
                if (!preg_match('@<form action="([^"]+)"@', $form, $fl)) html_error('Error [Post Link - FREE not found!]');
				$this->link = trim($fl[1]);
                if (!preg_match_all('@<input type="hidden" value="([^"]+)" name="([^"]+)" \/>@', $form, $ck)) html_error("Error [Post Data 1 FREE not found!]");
                $match = array_combine($ck[2], $ck[1]);
                if ($wait > 70) {
                    $data = array_merge($this->DefaultParamArr($this->link, $this->cookie), $match);
                    $data['step'] = 'countdown';
                    $this->JSCountdown($wait, $data);
                } else {
                    $this->CountDown($wait);
                    $post = array();
                    foreach ($match as $key => $value) {
                        $post[$key] = $value;
                    }
                    $post['submit'] = 'Free Download';
                    $page = $this->GetPage($this->link, $this->cookie, $post, $this->link);
                }
                break;
        }
        if (preg_match('@\/challenge\?k=([^"]+)"@', $page, $cap) && preg_match('@\/noscript\?k=([^"]+)"@', $page, $cap) || strpos($page, 'Wrong Captcha!')) {
            if (!preg_match_all('@<input type="hidden" value="([^"]+)" name="([^"]+)" \/>@', $page, $ck)) html_error("Error [Post Data 2 FREE not found!]");
            $data = array_merge($this->DefaultParamArr($this->link, $this->cookie), array_combine($ck[2], $ck[1]));
            echo "<script language='JavaScript'>var RecaptchaOptions={theme:'white', lang:'en'};</script>\n";
            echo "\n<center><form name='dl' action='$PHP_SELF' method='post' ><br />\n";
            foreach ($data as $name => $input) {
                echo "<input type='hidden' name='$name' id='$name' value='$input' />\n";
            }
            echo "<script type='text/javascript' src='http://www.google.com/recaptcha/api/challenge?k=$cap[1]'></script>";
            echo "<noscript><iframe src='http://www.google.com/recaptcha/api/noscript?k=$cap[1]' height='300' width='500' frameborder='0'></iframe><br />";
            echo "<textarea name='recaptcha_challenge_field' rows='3' cols='40'></textarea><input type='hidden' name='recaptcha_response_field' value='manual_challenge' /></noscript><br />";
            echo "<input type='submit' name='submit' onclick='javascript:return checkc();' value='Download' />\n";
            echo "<script type='text/javascript'>/*<![CDATA[*/\nfunction checkc(){\nvar capt=document.getElementById('recaptcha_response_field');\nif (capt.value == '') { window.alert('You didn\'t enter the image verification code.'); return false; }\nelse { return true; }\n}\n/*]]>*/</script>\n";
            echo "</form></center>\n</body>\n</html>";
            exit();
        }
        if (!preg_match('@Location: (http:\/\/[^\r\n]+)@i', $page, $dl)) html_error('Error [Download Link FREE not found!]');
        $dlink = trim($dl[1]);
        $filename = basename(parse_url($dlink, PHP_URL_PATH));
        $this->RedirectDownload($dlink, $filename, $this->cookie, 0, $this->link);
        exit();
    }

    private function Login() {
        global $premium_acc;

        $user = ($_REQUEST["premium_user"] ? $_REQUEST["premium_user"] : $premium_acc["freakshare_com"]["user"]);
        $pass = ($_REQUEST["premium_pass"] ? $_REQUEST["premium_pass"] : $premium_acc["freakshare_com"]["pass"]);
        if (empty($user) || empty($pass)) html_error("Login Failed: Username[$user] or Password[$pass] is empty. Please check login data.");

        $posturl = 'http://freakshare.com/';
        $post['user'] = $user;
        $post['pass'] = $pass;
        $post['submit'] = 'Login';
        $check = $this->GetPage($posturl . "login.html", $this->cookie, $post, $posturl . "login.html");
        $this->cookie = GetCookiesArr($check, $this->cookie);
        if (!stripos($check, 'Location:') || !array_key_exists('login', $this->cookie)) html_error("Wrong Username or Password!");

        //check account
        $check = $this->GetPage($posturl, $this->cookie, 0, $posturl . "login.html");
        if (strpos($check, 'Member (free)')) { //freakshare give only 60 second delay time for free user, so free account is useful too...
            $this->changeMesg(lang(300) . "<br />Freakshare.com Free Account");
            $this->page = $this->GetPage($this->link, $this->cookie, 0, $this->link);
            return $this->Free();
        } else {
            $this->changeMesg(lang(300) . "<br />Freakshare.com Premium Account");
            $this->page = $this->GetPage($this->link, $this->cookie, $post, $this->link);
            return $this->Premium();
        }
    }

    private function Premium() {

        is_present($this->page, "Your Traffic is used up for today!");
        //there is option for disable direct link, seem rd27 forget to check this, since I dont have premium account, I cant check this setting, sorry...
        if (!preg_match('@Location: (http:\/\/[^\r\n]+)@i', $this->page, $dl)) html_error("Error [Download Link PREMIUM not found! Please try to enable direct download setting in your premium account!]");
        $dlink = trim($dl[1]);
        $filename = basename(parse_url($dlink, PHP_URL_PATH));
        $this->RedirectDownload($dlink, $filename, $this->cookie);
    }

}

/*
 * originally written by rd27
 * converted into OOP format also add support for free account by Ruud v.Tony 07-02-2012
 */
?>