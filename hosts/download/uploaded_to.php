<?php

if (!defined('RAPIDLEECH')) {
    require_once ("index.html");
    exit ();
}

class uploaded_to extends DownloadClass {

    public function Download($link) {
        global $premium_acc, $options;
        if (($_REQUEST ["premium_acc"] == "on" && $_REQUEST ["premium_user"] && $_REQUEST ["premium_pass"]) || ($_REQUEST ["premium_acc"] == "on" && $premium_acc ["uploaded_to"] ["user"] && $premium_acc ["uploaded_to"] ["pass"])) {
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
        is_present($page, "/404", "File not found");
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
        $data = array();
        $data["recaptcha_challenge_field"] = $ch;
        $data["step"] = "1";
        $data["link"] = $link;
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

    private function DownloadPremium($link) {
        global $premium_acc;
        $post = array();
        $post["id"] = $_REQUEST["premium_user"] ? trim($_REQUEST["premium_user"]) : $premium_acc ["uploaded_to"] ["user"];
        $post["pw"] = $_REQUEST["premium_pass"] ? trim($_REQUEST["premium_pass"]) : $premium_acc ["uploaded_to"] ["pass"];
        $page = $this->GetPage("http://uploaded.to/io/login", 0, $post, $link);
        $Cookies = GetCookies($page);
        $page = $this->GetPage($link, $Cookies, 0, $link);
		is_present($page, "/404", "File not found");
		is_present($page,"Traffic exhausted","Premium account is out of Bandwidth");
        if (!preg_match('#http://.+/dl?[^\r"]+#', $page, $dlink)) {
            html_error("Error 1x01: Plugin is out of date");
        }
        $this->RedirectDownload(trim($dlink[0]), "uploaded", $Cookies, 0, $link);
        exit;
    }

}

/*
 * by vdhdevil 15-March-2011
 * Updated 01-May-2011
 * 
 */
?>
