<?php
if (!defined('RAPIDLEECH')) {
    require_once ("index.html");
    exit();
}

class bitshare_com extends DownloadClass {

    public function Download($link) {
        global $premium_acc;
        
        if (!$_REQUEST['step']) {
            $this->page = $this->GetPage($link, "language_selection=EN"); //keep page in english
            is_present($this->page, "Error - File not available");
            $this->cookie = GetCookies($this->page). "; language_selection=EN";
        }
        if ($_REQUEST ["premium_acc"] == "on" && (($_REQUEST ["premium_user"] && $_REQUEST ["premium_pass"]) || ($premium_acc ["bitshare_com"] ["user"] && $premium_acc ["bitshare_com"] ["pass"]))) {
            $this->DownloadPremium($link);
        } else {
            $this->DownloadFree($link);
        }
    }
    
    private function DownloadFree($link) {
        if ($_REQUEST['step'] == '1') {
            $Url = urldecode($_POST['link']);
            $this->cookie = urldecode($_POST['cookie']);
            $recap = $_POST['recap']; //to be called when we failed to enter captcha code
            $ajaxid = $_POST['ajaxid'];
            
            $post['request'] = 'validateCaptcha';
            $post['ajaxid'] = $ajaxid;
            $post['recaptcha_challenge_field'] = $_POST['challenge'];
            $post['recaptcha_response_field'] = $_POST['captcha'];
            $check = $this->GetPage($Url, $this->cookie, $post,  $link , 0, 1);
        } else {
            is_present($this->page, "Your Traffic is used up for today. Upgrade to premium to continue!");
            if (preg_match('@<span id="blocktimecounter">(\d+) seconds<\/span>@', $this->page, $wait) && !strpos($this->page, "var blocktime = 0;")) {
                echo  ("<center><font color='red'><b>You reached your hourly traffic limit.</b></font></center>");
                $this->JSCountdown($wait[1]);
            }
            if (!preg_match('@url: "([^"]+)",@', $this->page, $rd)) html_error("Error: Post Link [FREE] not found!");
            if (!preg_match('@var ajaxdl = "([^"]+)";@i', $this->page, $ajax)) html_error("Error: Ajax ID [FREE] not found!");
            
            $post['request'] = 'generateID';
            $post['ajaxid'] = $ajax[1];
            $check = $this->GetPage($rd[1], $this->cookie, $post,  $link , 0, 1);
            if (!preg_match('@file:(\d+)\:[0-1]+@', $check, $wait)) html_error("Error: Timer not found!");
            $this->CountDown($wait[1]);
            if (!preg_match('@\/challenge\?k=([^"]+)"@', $this->page, $cap) && !stristr($this->page, "var captcha = 1;")) html_error("Error getting CAPTCHA Data!");
            $ch = cut_str($this->GetPage("http://www.google.com/recaptcha/api/challenge?k=$cap[1]"), "challenge : '", "'");
            $capt = $this->GetPage("http://www.google.com/recaptcha/api/image?c=" . $ch);
            $capt_img = substr($capt, strpos($capt, "\r\n\r\n") + 4);
            $imgfile = DOWNLOAD_DIR . "bitshare_captcha.jpg";

            if (file_exists($imgfile)) unlink($imgfile);
            if (empty($capt_img) || !write_file($imgfile, $capt_img)) html_error("Error getting CAPTCHA image.");
            
            $data = $this->DefaultParamArr($rd[1], $this->cookie);
            $data['step'] = '1';
            $data['challenge'] = $ch;
            $data['ajaxid'] = $ajax[1];
            $data['recap'] = $cap[1];
            $this->EnterCaptcha($imgfile, $data, 20);
            exit();
        }
        is_present($check, "We are sorry, but an error occured.");
        if (strpos($check, "SUCCESS") && !strpos($check, 'ERROR')) {
            unset($post);
            
            $post['request'] = 'getDownloadURL';
            $post['ajaxid'] = $ajaxid;
            $this->page = $this->GetPage($Url, $this->cookie, $post, $link, 0, 1);
            is_present($this->page, "ERROR#SESSION ERROR!");
            if (!preg_match('@(http(s)?:\/\/\w+\.bitshare\.com(:\d+)?\/[^\r\n]+)@i', $this->page, $dl)) html_error("Error: Download Link [FREE] not found!");
            $dlink = trim($dl[1]);
            $filename = basename(parse_url($dlink, PHP_URL_PATH));
            $this->RedirectDownload($dlink, $filename, $this->cookie, 0, $link);
            exit();
        } else {
            echo ("<center><font color='red'><b>".  cut_str($check, 'ERROR:', '\r\n')."</b></font></center>");
            $this->changeMesg("<font color='red'><b>".  cut_str($check, 'ERROR:', '\r\n')."</b></font>");
            
            $ch = cut_str($this->GetPage("http://www.google.com/recaptcha/api/challenge?k=$recap"), "challenge : '", "'");
            $capt = $this->GetPage("http://www.google.com/recaptcha/api/image?c=" . $ch);
            $capt_img = substr($capt, strpos($capt, "\r\n\r\n") + 4);
            $imgfile = DOWNLOAD_DIR . "bitshare_captcha.jpg";

            if (file_exists($imgfile)) unlink($imgfile);
            if (empty($capt_img) || !write_file($imgfile, $capt_img)) html_error("Error getting CAPTCHA image.");
            
            $data = $this->DefaultParamArr($Url, $this->cookie);
            $data['challenge'] = $ch;
            $data['step'] = '1';
            $data['ajaxid'] = $ajaxid;
            $data['recap'] = $recap;
            $this->EnterCaptcha($imgfile, $data, 20);
            exit();
        }
    }
    
    private function DownloadPremium($link) {
        global $premium_acc;
        
        $user = ($_REQUEST["premium_user"] ? trim($_REQUEST["premium_user"]) : $premium_acc ["bitshare_com"] ["user"]);
        $pass = ($_REQUEST["premium_pass"] ? trim($_REQUEST["premium_pass"]) : $premium_acc ["bitshare_com"] ["pass"]);
        if (empty($user) || empty($pass)) html_error("Login failed, $user [user] or $pass [password] is empty!");
        
        $Url = 'http://bitshare.com/';
        $post['user'] = $user;
        $post['password'] = $pass;
        $post['rememberlogin'] = '';
        $post['submit'] = 'Login';
        $check = $this->GetPage($Url."login.html", $this->cookie, $post, $Url);
        $this->cookie = $this->cookie. ";". GetCookies($check);
        is_notpresent($this->cookie, 'login=', "Wrong Username or Password!");
        is_present($check, "<a href=\"http://bitshare.com/myupgrade.html\">Free</a>", "Account type : FREE");
        
        $this->page = $this->GetPage($link, $this->cookie, 0, $link);
        if (!preg_match('@Location: (http(s)?:\/\/[^\r\n]+)@i', $this->page, $dl)) { // non direct link
            if (!preg_match('@url: "([^"]+)",@i', $this->page, $rd)) html_error("Error: Post Link [PREMIUM] not found!");
            if (!preg_match('@var ajaxdl = "([^"]+)";@i', $this->page, $ajax)) html_error("Error: Post Data [PREMIUM] not found!");
            
            unset($post);
            $post['request'] = 'generateID';
            $post['ajaxid'] = $ajax[1];
            $check = $this->GetPage($rd[1], $this->cookie, $post, $link, 0, 1);
            unset($post);
            $post['request'] = 'getDownloadURL';
            $post['ajaxid'] = $ajax[1];
            $this->page = $this->GetPage($rd[1], $this->cookie, $post, $link, 0, 1);
            if (!preg_match('@(http(s)?:\/\/\w+\.bitshare\.com(:\d+)?\/[^\r\n]+)@i', $this->page, $dl)) html_error("Error: Download Link [PREMIUM] not found!");
        }
        $dlink = trim($dl[1]);
        $filename = basename(parse_url($dlink, PHP_URL_PATH));
        $this->RedirectDownload($dlink, $filename, $this->cookie);
    }
}

/*
 * by vdhdevil Dec-11-2010
 * fixed by Ruud v.Tony 25-01-2012
 */
?>