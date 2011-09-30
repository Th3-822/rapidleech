<?php
if (!defined('RAPIDLEECH')) {
    require_once ("index.html");
    exit ();
}

class uploaded_to extends DownloadClass {

    public function Download($link) {
        global $premium_acc, $options;
        if (preg_match('/http:\/\/uploaded\.to\/folder\/[^"]+/i', $link, $dir)) {
            if (!$dir[0]) {
                html_error('Could\'nt find any link, please check again!');
            }
            $page = $this->GetPage($link);
            preg_match_all('%href="(file\/\w+\/from\/\w+)%', $page, $match, PREG_SET_ORDER);
            foreach ($match as $temp) {
                $arr_link[] = str_ireplace('href="', '', "http://uploaded.to/$temp[0]");
            }
            $this->moveToAutoDownloader($arr_link);
        } else {
            $page = $this->GetPage($link);
            is_present($page, "/404", "File not found");
        }
        unset($page);
        if (($_REQUEST["cookieuse"] == "on" && preg_match("/login\s?=\s?(\w{84})/i", $_REQUEST["cookie"], $c)) || ($_REQUEST["premium_acc"] == "on" && $premium_acc["uploaded_to"]["cookie"])) {
            $cookie = (empty($c[1]) ? $premium_acc["uploaded_to"]["cookie"] : $c[1]);
            $this->DownloadPremium($link, $cookie);
        } elseif (($_REQUEST ["premium_acc"] == "on" && $_REQUEST ["premium_user"] && $_REQUEST ["premium_pass"]) || ($_REQUEST ["premium_acc"] == "on" && $premium_acc ["uploaded_to"] ["user"] && $premium_acc ["uploaded_to"] ["pass"])) {
            $this->DownloadPremium($link);
        } elseif ($_POST['step'] == "1") {
            $this->DownloadFree($link);
        } else {
            $this->Retrieve($link);
        }
    }

    private function Retrieve($link) {
        global $options;
        $page = $this->GetPage($link);
        $Cookies = GetCookies($page);
        if (!preg_match('#(\d+)</span> seconds#', $page, $count)) {
            html_error("Error 0x01: Plugin is out of date");
        }
        insert_timer($count[1]);
        $page = $this->GetPage("http://www.google.com/recaptcha/api/challenge?k=6Lcqz78SAAAAAPgsTYF3UlGf2QFQCNuPMenuyHF3");
        $ch = cut_str($page, "challenge : '", "'");
        $img = "http://www.google.com/recaptcha/api/image?c=" . $ch;
        $page = $this->GetPage($img);
        $headerend = strpos($page, "\r\n\r\n");
        $pass_img = substr($page, $headerend + 4);
        write_file($options['download_dir'] . "uploaded_captcha.jpg", $pass_img);
        $data = $this->DefaultParamArr($link);
        $data["recaptcha_challenge_field"] = $ch;
        $data["step"] = "1";
        $data["Cookies"] = $Cookies;
        $this->EnterCaptcha($options['download_dir'] . "uploaded_captcha.jpg", $data, "10");
        exit;
    }

    private function DownloadFree($link) {
        $Cookies = $_POST["Cookies"];
        $post = array();
        $post["recaptcha_challenge_field"] = $_POST["recaptcha_challenge_field"];
        $post["recaptcha_response_field"] = $_POST["captcha"];
        $tmplink = str_replace("/file/", "/io/ticket/captcha/", $link);
        is_present($page, 'err:"limit-dl"', "You've reached your Free account limit");
        $page = $this->GetPage($tmplink, $Cookies, $post, $link);
        if (!preg_match("#http://.+/dl/[^']+#", $page, $dlink)) {
            html_error("Error 0x02: Plugin is out of date");
        }
        $this->RedirectDownload(trim($dlink[0]), "uploaded", $Cookies, 0, $link);
        exit;
    }

    private function DownloadPremium($link, $cookie = false) {
        $cookie = $this->login($cookie);
        $page = $this->GetPage($link, $cookie);
        is_present($page, "Traffic exhausted", "Premium account is out of Bandwidth");

        if (!preg_match('#http:\/\/stor(\d+)?\.uploaded\.to/dl\/[^\r"]+#', $page, $dlink)) {
            html_error("Error 1x01: Plugin is out of date");
        }
        $this->RedirectDownload(trim($dlink[0]), "uploaded", $cookie, 0, $link);
    }

    private function login($loginc = false) {
        global $premium_acc;
        if (!$loginc) {
            $user = ($_REQUEST["premium_user"] ? $_REQUEST["premium_user"] : $premium_acc["uploaded_to"]["user"]);
            $pass = ($_REQUEST["premium_pass"] ? $_REQUEST["premium_pass"] : $premium_acc["uploaded_to"]["pass"]);
            if (empty($user) || empty($pass)) {
                html_error("Login Failed: Username or Password is empty. Please check login data.");
            }
            $post = array();
            $post["id"] = $user;
            $post["pw"] = $pass;
            $page = $this->GetPage("http://uploaded.to/io/login", 0, $post, 'http://uploaded.to/\r\nX-Requested-With: XMLHttpRequest'); //other way add xml request without edit http.php
            $cookie = GetCookies($page);
            is_present($page, 'err:"User and password do not match', 'Login Failed, please check your account');
        } elseif (strlen($loginc) == 84) {
            $cookie = 'login=' . $loginc;
        } else {
            html_error("[Cookie] Invalid cookie (" . strlen($loginc) . " != 84). Try to encode your cookie first!");
        }

        $page = $this->GetPage('http://uploaded.to/me', $cookie);
        $cookie = $cookie . '; ' . GetCookies($page);
        is_present($page, '<em>Free</em>', 'Account free, please check ur premium account');
        is_present($page, 'ocation: http://uploaded.to', 'Cookie failed, please check ur account');

        return $cookie;
    }

}

/*
 * by vdhdevil 15-March-2011
 * Updated 01-May-2011
 * Fixed by Ruud v.Tony also add some improvement 11-09-2011
 *
 */
?>
