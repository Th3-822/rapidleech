<?php
if (!defined('RAPIDLEECH')) {
    require_once("index.html");
    exit;
}

class extabit_com extends DownloadClass {

    public function Download($link) {
        global $premium_acc, $options;
        if ($_REQUEST ["premium_acc"] == "on" && ((!empty($_REQUEST ["premium_user"]) && !empty($_REQUEST ["premium_pass"])) || (!empty($premium_acc ["extabit_com"] ["user"]) && !empty($premium_acc ["extabit_com"] ["pass"])))) {
            $this->DownloadPremium($link);
        } elseif ($_REQUEST['step'] == '1') {
            $this->DownloadFree($link);
        } else {
            $this->Retrieve($link);
        }
    }

    private function DownloadFree($link) {
        $captcha = $_POST['captcha'];
		$challenge = $_POST['challenge'];
        $link = urldecode($_POST['link']);
        $cookie = StrToCookies(urldecode($_POST['cookie']));
        $page = $this->GetPage("$link?type=recaptcha&challenge=$challenge&capture=$captcha", $cookie, 0, $link, 0, 1);
        $cookie = GetCookiesArr($page, $cookie);
        if (preg_match('@\{"(\w+)":(\w+)?,?"?(\w+)?"?:?"([^"]+)"\}@', $page, $ck)) {
            if ($ck[1] == 'ok') {
                switch ($ck[3]) {
                    case 'href':
                        $Url = $link . $ck[4];
                        $page = $this->GetPage($Url, $cookie, 0, $link);
                        if (!preg_match('@http:\/\/guest\d+\.extabit\.com\/[^"]+@', $page, $dl)) html_error("Error[DownloadLink - FREE] not found!");
                        $dlink = trim($dl[0]);
                        $filename = basename(parse_url($dlink, PHP_URL_PATH));
                        $this->RedirectDownload($dlink, $filename, $cookie, 0, $link);
                        break;
                }
            }
            is_present($ck[1], 'err', $ck[4]);
        }
    }

    private function Retrieve($link) {
        $page = $this->GetPage($link);
        if (preg_match("@Location: (http(s)?:\/\/[^\r\n]+)@i", $page, $redir)) {
            $link = trim($redir[1]);
            $page = $this->GetPage($link);
        }
        $cookie = GetCookiesArr($page);
        is_present($page, "File not found");
        is_present($page, "Only premium users can download this file");
        is_notpresent($page, 'I have no money', cut_str($page, '<div class="b-download-free download-link">', '<a class="b-get-premium" href="/premium.jsp">'));
		if (stripos($page, 'Please wait until download starts')) {
			if (!preg_match('/<div id="\w+">(\d+)<\/div>/', $page, $wait)) html_error('Error [Timer not found!]');
			$this->CountDown($wait[1]);
		}
        if (stripos($page, '<div id="download_link_captcha"')) {
            $form = cut_str($page, 'class="download_link_captcha_en">', '</form>');
            $Url = 'http://extabit.com' . cut_str($form, '<form action="', '"');
			if (!preg_match('/api\/challenge\?k=([^"]+)/', $form, $k) && !preg_match('/api\/noscript\?k=([^"]+)/', $form, $k)) html_error('Error [Captcha Data not found!]');

            //Download captcha img.
            $ch = cut_str($this->GetPage('http://www.google.com/recaptcha/api/challenge?k='. $k[1], $cookie),  "challenge : '", "'");
            $cap = $this->GetPage("http://www.google.com/recaptcha/api/image?c=" . $ch);
            $capt_img = substr($cap, strpos($cap, "\r\n\r\n") + 4);
            $imgfile = DOWNLOAD_DIR . "extabit_captcha.jpg";

            if (file_exists($imgfile)) unlink($imgfile);
            if (empty($capt_img) || !write_file($imgfile, $capt_img)) html_error("Error getting CAPTCHA image.", 0);
            // Captcha img downloaded

            $data = $this->DefaultParamArr($Url, $cookie);
            $data['step'] = '1';
			$data['challenge'] = $ch;
            $this->EnterCaptcha($imgfile, $data);
            exit();
        }
    }

    private function DownloadPremium($link) {
        global $premium_acc;
        
        $post = array();
        $post['email'] = $_REQUEST["premium_user"] ? trim($_REQUEST["premium_user"]) : $premium_acc ["extabit_com"] ["user"];
        $post['pass'] = $_REQUEST["premium_pass"] ? trim($_REQUEST["premium_pass"]) : $premium_acc ["extabit_com"] ["pass"];
        $post['remember'] = "1";
        $post['auth_submit_login.x'] = rand(0, 47);
        $post['auth_submit_login.y'] = rand(0, 9);
        $page = $this->GetPage("http://extabit.com/login.jsp", 0, $post, "http://extabit.com/");
        $Cookies.="; " . GetCookies($page);
        is_notpresent($page, "auth_uid", "Login Failed");
        $page = $this->GetPage($link, $Cookies);
        is_present($page, "File not found", "File not found");
        if (preg_match("#Location: (.*)#", $page, $tmp)) {
            $page = $this->GetPage(trim($tmp[1]), $Cookies);
            $Cookies.="; " . GetCookies($page);
        }
        if (!preg_match('#http://\w+\d+.extabit.com/[^"]+#', $page, $dlink)) {
            html_error("Error 1x01: Plugin is out of date");
        }
		$filename = basename(parse_url($dlink[0], PHP_URL_PATH));
        $this->RedirectDownload($dlink[0], $filename, $Cookies, 0, trim($tmp[1]));
        exit;
    }
}

/*
 * by vdhdevil
 * fixed captcha also redirect link by Ruud v.Tony 06-02-2012
 * fixed filename error in premium also recaptcha in free by Ruud v.Tony 28-03-2012
 */

?>
