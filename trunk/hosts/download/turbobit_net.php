<?php
if (!defined('RAPIDLEECH')) {
    require_once("index.html");
    exit;
}

class turbobit_net extends DownloadClass {

    public function Download($link) {
        global $premium_acc, $options;
        $link=str_replace("www.", "", $link);
        if (strpos($link, "download/free/")){
            $link=  str_replace("download/free/", "", $link).".html";
        }
        if (($_REQUEST ["premium_acc"] == "on" && $_REQUEST ["premium_user"] && $_REQUEST ["premium_pass"]) || ($_REQUEST ["premium_acc"] == "on" && $premium_acc ["turbobit_net"] ["user"] && $premium_acc ["turbobit_net"] ["pass"])) {
            $this->DownloadPremium($link);
        } elseif ($_POST['step'] == "1") {
            $this->DownloadFree($link);
        } else {
            $this->Retrieve($link);
        }
    }

    private function Retrieve($link) {
        global $options;
        $page = $this->GetPage($link, "set_user_lang_change=en", 0, $link);
        is_present($page, "Please wait, searching file","Link is not available");
        is_present($page, "This document was not found in System","Link is not available");
        preg_match_all("#[\w-]+: (\w+)=([^;]+)#", $page, $tmp);
        $arrCookies = array_combine($tmp[1], $tmp[2]);
        $Cookies = urldecode(http_build_query($arrCookies, "", "; "));
        $Cookies = str_replace(array("user_isloggedin=deleted; ", "set_user_lang_change=deleted; "), "", $Cookies);
        if (!preg_match('#/[a-w+]+/[e-r]{4}/\w+#', $page, $tmp)) {
            html_error("Error 0x01: Plugin is out of date");
        }
        $flink = "http://turbobit.net" . $tmp[0];
        $page = $this->GetPage($flink, $Cookies, 0, $link);
        if (preg_match('#(\d+)</span> seconds#', $page, $count)) {
            html_error("You have reached the limit of connections,try downloading again after " . $count[1] . " seconds");
        }
        if (!preg_match("#value = '(.*)' name = 'captcha_type'#", $page, $captcha_type)) {
            html_error("Error 0x02:Plugin is out of date");
        }
        if (!preg_match("#value = '(.*)' name = 'captcha_subtype'#", $page, $captcha_subtype)) {
            html_error("Error 0x03: Plugin is out of date");
        }
        $data = $this->DefaultParamArr($link);
        $data['step'] = "1";
        $data['Cookies'] = $Cookies;
        $data['flink'] = $flink;
        $data['captcha_type'] = $captcha_type[1];
        $data['captcha_subtype'] = $captcha_subtype[1];
        if (!preg_match('#http.+/captcha/[^"]+#', $page, $img)) {
            if (strpos($page, "http://api.recaptcha.net/noscript?k=6LcTGLoSAAAAAHCWY9TTIrQfjUlxu6kZlTYP50_c")) {
                $page = $this->GetPage("http://www.google.com/recaptcha/api/challenge?k=6LcTGLoSAAAAAHCWY9TTIrQfjUlxu6kZlTYP50_c");
                $ch = cut_str($page, "challenge : '", "'");
                $data["recaptcha_challenge_field"] = $ch;
                $img = "http://www.google.com/recaptcha/api/image?c=" . $ch;
                $page = $this->GetPage($img);
                $headerend = strpos($page, "\r\n\r\n");
                $pass_img = substr($page, $headerend + 4);
                write_file($options['download_dir'] . "turbobit_captcha.jpg", $pass_img);
                $img_src = $options['download_dir'] . "turbobit_captcha.jpg";
            }else
                html_error("Error 0x04: Plugin is out of date");
        } else {
            $page = $this->GetPage($img[0], $Cookies, 0, $link);
            $headerend = strpos($page, "\r\n\r\n");
            $pass_img = (substr($page, $headerend + 4));
            if (preg_match("#\w{4}\r\n#", $pass_img)) {
                $t = strpos($pass_img, "P");
                $pass_img = ltrim(substr($pass_img, $t - 2), "\r\n");
            }
            write_file($options['download_dir'] . "turbobit_captcha.png", $pass_img);
            $img_src = $options['download_dir'] . "turbobit_captcha.png";
        }
        $this->EnterCaptcha($img_src, $data, '10');
        exit;
    }

    private function DownloadFree($link) {
        $post = array();
        if (!empty($_POST["recaptcha_challenge_field"])) {
            $post['recaptcha_challenge_field'] = $_POST["recaptcha_challenge_field"];
            $post['recaptcha_response_field'] = $_POST['captcha'];
        } else {
            $post['captcha_response'] = urlencode($_POST['captcha']);
        }
        $post['captcha_type'] = $_POST['captcha_type'];
        $post['captcha_subtype'] = $_POST['captcha_subtype'];
        $Cookies = $_POST['Cookies'];
        $flink = $_POST['flink'];
        $page = $this->GetPage($flink, $Cookies, $post, $link);
        is_present($page, "Incorrect, try again!", "Incorrect Captcha");
        if (!strpos($page, "imit : 60")) {
            if (preg_match("#limit: (\d+)#", $page, $count)) {
                html_error("Please wait {$count[1]} for next download");
            }
            html_error("Error 0x11: Plugin is out of date");
        }
        insert_timer(60);
        $tmp = cut_str($page, '$("#timeoutBox").load("', '"');
        $rlink = "http://turbobit.net" . $tmp;
        $page = $this->GetPage($rlink, $Cookies, 0, $flink . "\r\nX-Requested-With: XMLHttpRequest", 0);
        if (!preg_match("#/download/[^']+#", $page, $tmp)) {
            html_error("Error 0x13: Plugin is out of date");
        }
        $dlink = "http://turbobit.net" . $tmp[0];
        $page = $this->GetPage($dlink, $Cookies, 0, $link);
        if (!preg_match("#Location: (.*)#", $page, $rlink)) {
            html_error("Error 0x14: Plugin is out of date");
        }
        $this->RedirectDownload(trim($rlink[1]), "turbobit", 0, 0, $link);
        exit;
    }

    private function DownloadPremium($link) {
        global $premium_acc;
        $post = array();
        $post["user[login]"] = $_REQUEST["premium_user"] ? trim($_REQUEST["premium_user"]) : $premium_acc ["turbobit_net"] ["user"];
        $post["user[pass]"] = $_REQUEST["premium_user"] ? trim($_REQUEST["premium_user"]) : $premium_acc ["turbobit_net"] ["pass"];
        $post["user[memory]"] = "on";
        $post["user[submit]"] = "Login";
        $page = $this->GetPage("http://turbobit.net/user/login", 0, $post, $link);
        preg_match_all("#Set-Cookie: ([^;]+)#", $page, $tmp);
        $Cookies = $tmp[1][1] . "; " . $tmp[1][3] . "; " . $tmp[1][4];
        $page = $this->GetPage($link, $Cookies, 0, $link);
        $Cookies = $tmp[1][3] . "; " . $tmp[1][4];
        preg_match_all("#Set-Cookie: ([^;]+)#", $page, $tmp);
        $Cookies.="; " . $tmp[1][1];
        if (preg_match("#http.+download/redirect[^']+#", $page, $tmp)) {
            $page = $this->GetPage($tmp[0], $Cookies, 0, $link);
            if (!(preg_match("#Location: (.+)#", $page, $dlink))) {
                html_error("Error 1x02: Plugin is out of date");
            }
            $this->RedirectDownload(trim($dlink[1]), "turbobit", $Cookies, 0, $link);
        } else {
            html_error("Error 1x01: Plugin is out of date");
        }
        exit;
    }

}

/*
 * -- by vdhdevil --
 */
?>