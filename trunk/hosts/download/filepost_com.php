<?php
if (!defined('RAPIDLEECH')) {
    require_once ("index.html");
    exit();
}

class filepost_com extends DownloadClass {

    public function Download($link) {
        global $premium_acc;

        if (($_REQUEST ["premium_acc"] == "on" && $_REQUEST ["premium_user"] && $_REQUEST ["premium_pass"]) || ($_REQUEST ["premium_acc"] == "on" && $premium_acc ["filepost_com"] ["user"] && $premium_acc ["filepost_com"] ["pass"])) {
            $this->DownloadPremium($link);
        } else if ($_POST['step'] == '1') {
            $this->Free($link);
        } else {
            $this->Retrieve($link);
        }
    }

    private function Retrieve($link) {
        global $Referer;

        $page = $this->GetPage($link);
        $cookie = GetCookies($page);
        $sid = cut_str($cookie, 'SID=', ';');
        $tid = str_replace(".", "", microtime(true));
        $link = "http://filepost.com/files/get/?SID=$sid&JsHttpRequest=$tid-xml";
        // Prepare for the captcha if it's needed
        $k = cut_str($page, "key:			'", "'");
        $token = cut_str($page, "'flp_token', '", "');");
        $code = cut_str($page, "{code: '", "',");
        $post = array('action' => 'set_download', 'download' => $token, 'code' => $code);
        $page = $this->GetPage($link, $cookie, $post, $Referer);
        if (!preg_match('%wait_time":"(\d+)"%', $page, $wait)) html_error('Error: Timer id not found!');
        $this->CountDown($wait[1]);
        $page = $this->GetPage("http://www.google.com/recaptcha/api/challenge?k=$k&ajax=1");
        $ch = cut_str($page, "challenge : '", "'");
        if ($ch) {
            $page = $this->GetPage("http://www.google.com/recaptcha/api/image?c=" . $ch);
            $pass_img = substr($page, strpos($page, "\r\n\r\n") + 4);
            $imgfile = DOWNLOAD_DIR . "filepost_capcay.jpg";
            if (file_exists($imgfile)) {
                unlink($imgfile);
            }
            write_file($imgfile, $pass_img);
        } else {
            html_error('Can\'t find captcha image?');
        }
        $data = $this->DefaultParamArr($link, $cookie);
        $data['step'] = '1';
        $data['recaptcha_challenge_field'] = $ch;
        $data['code'] = $code;
        $data['download'] = $token;
        $this->EnterCaptcha($imgfile, $data, 10);
        exit();
    }

    private function Free($link) {
        global $Referer;

        $post['code'] = $_POST['code'];
        $post['download'] = $_POST['download'];
        $post['recaptcha_challenge_field'] = $_POST['recaptcha_challenge_field'];
        $post['recaptcha_response_field'] = $_POST['captcha'];
        $link = urldecode($_POST['link']);
        $cookie = urldecode($_POST['cookie']);
        $page = $this->GetPage($link, $cookie, $post, $Referer);
        if (strpos($page, 'You entered a wrong CAPTCHA code. Please try again.')) {
            return $this->Retrieve($link);
        }
        $dlink = cut_str($page, '"link":"', '"}');
        $dlink = str_replace('\/', '/', $dlink);
        if (!isset($dlink)) html_error('Error: Download link not found!');
        $filename = parse_url($dlink);
        $FileName = basename($filename['path']);
        $this->RedirectDownload($dlink, $FileName, $cookie, 0, $link);
        exit();
    }

    private function DownloadPremium($link) {
        global $premium_acc;

        $Url = "http://filepost.com/files/checker/?JsHttpRequest=" . round(microtime(true) * 1000) . "-xml";
        $page = $this->GetPage($Url, 0, array('urls' => $link));
        is_notpresent($page, 'Active', "This link is not available");
        $page = $this->GetPage($link);
        preg_match_all("#Set-Cookie: (\w+)=([^;]+)#", $page, $tmp);
        $CookiesArr = array_combine($tmp[1], $tmp[2]);
        $Cookies = urldecode(http_build_query($CookiesArr, "", ";"));
        if (!preg_match("#SID=(\w+)#", $Cookies, $sid)) {
            html_error("Error 1x01: Plugin is out of date");
        }
        $post = array();
        $post['email'] = $_GET ["premium_user"] ? $_GET ["premium_user"] : $premium_acc['filepost_com']['user'];
        $post['password'] = $_GET ["premium_user"] ? $_GET ["premium_user"] : $premium_acc['filepost_com']['pass'];
        $post['recaptcha_response_field'] = "";
        $page = $this->GetPage("http://filepost.com/general/login_form/?SID={$sid[1]}&JsHttpRequest=" . round(microtime(true) * 1000) . "-xml", $Cookies, $post, $link);
        if (!strpos($page, '"answer":{"success":true')) {
            html_error("Login Failed");
        }
        preg_match_all("#Set-Cookie: (\w+)=([^;]+)#", $page, $tmp);
        $CookiesArr = array_merge($CookiesArr, array_combine($tmp[1], $tmp[2]));
        $Cookies = urldecode(http_build_query($CookiesArr, "", ";"));
        $page = $this->GetPage($link, $Cookies, 0, $link);
        if (!preg_match('#http://filepost.com/files/\w+/([^/"]+)#', $page, $tmp)) {
            html_error("Error 1x02: Plugin is out of date");
        }
        $FileName = $tmp[1] ? $tmp[1] : "FilePost";
        $dlink = cut_str($page, "download_file('", "'");
        $this->RedirectDownload($dlink, $FileName, $Cookies, 0, $link, $FileName);
    }
}

/*
 * Filepost.com free download plugin by Ruud v.Tony 29-09-2011
 * Updated to support premium by vdhdevil 12-10-2011
 */
?>
