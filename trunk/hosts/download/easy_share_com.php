<?php

if (!defined('RAPIDLEECH')) {
    require_once ("index.html");
    exit ();
}

class easy_share_com extends DownloadClass {

    public function Download($link) {
        global $premium_acc;
        if (( $_REQUEST ["premium_acc"] == "on" && $_REQUEST ["premium_user"] && $_REQUEST ["premium_pass"] ) ||
                ( $_REQUEST ["premium_acc"] == "on" && $premium_acc ["easyshare_com"] ["user"] && $premium_acc ["easyshare_com"] ["pass"] )) {
            $this->DownloadPremium($link);
        } else if ($_POST['easy_share'] == "ok") {
            $this->DownloadFree($link);
        } else {
            $this->Retrieve($link);
        }
    }

    private function Retrieve($link) {
      global $options;
        $page = $this->GetPage($link);
        is_present($page, "The file could not be found", "The file could not be found. Please check the download link.");
        is_present($page, "File not available", "File not available");
        is_present($page, "Page not found", "The file could not be found. Please check the download link.");
        $cookie = GetCookies($page);
        $FileName = cut_str($page, 'Download ', ',');
        $FileName = trim($FileName);
        $linkcaptcha = cut_str($page, "/file_contents/captcha/", "'");
        if (!strpos($page, 'method="post" action="')) {
            $time = cut_str($page, "w='", "'");
            insert_timer($time);
            $randnum = rand(10000, 100000);
            $Referer = $link;
            $linkcaptcha = "http://www.easy-share.com/file_contents/captcha/" . $linkcaptcha;
            $page = $this->GetPage($linkcaptcha, $cookie, 0, $Referer);
            $cookie .='; ' . GetCookies($page);
        }
        $linkpost = cut_str($page, 'method="post" action="', '"');
        $linkcaptcha = cut_str($page, 'Recaptcha.create("', '"');
        $valid = cut_str($page, 'name="id" value="', '"');
        $page = $this->GetPage('http://www.google.com/recaptcha/api/challenge?k=' . $linkcaptcha . '&ajax=1');
        $challenge = cut_str($page, "challenge : '", "'");
        $img = 'http://www.google.com/recaptcha/api/image?c=' . $challenge;
        $page = $this->GetPage($img);
        $headerend = strpos($page, "\r\n\r\n");
        $pass_img = substr($page, $headerend + 4);
        write_file($options['download_dir'] . "easyshare_captcha.jpg", $pass_img);
        $data = $this->DefaultParamArr($linkpost, $cookie, $referer);
        $data['challenge'] = $challenge;
        $data['valid'] = $valid;
        $data['easy_share'] = "ok";
        $data['FileName'] = $FileName;
        $this->EnterCaptcha($options['download_dir'] . "easyshare_captcha.jpg", $data, 5);
    }

    private function DownloadFree($link) {
        $post = array();
        $post["recaptcha_challenge_field"] = $_POST['challenge'];
        $post["recaptcha_response_field"] = $_POST['captcha'];
        $post["id"] = $_POST['valid'];
        $cookie = $_POST['cookie'];
        $Referer = $_POST['referer'];
        $FileName = $_POST["FileName"];
        $Url = parse_url($link);
        $FileName = !$FileName ? basename($Url["path"]) : $FileName;
        $this->RedirectDownload($link, $FileName, $cookie, $post, $Referer);
    }

    private function DownloadPremium($link) {
        global $premium_acc, $pauth, $Referer;
        $Referer = "http://www.easy-share.com/";
        $page = $this->GetPage($link, 0, 0, 0, $pauth);
        is_present($page, 'File was deleted');
        is_present($page, 'File not found');
        $FileName = trim(cut_str($page, "<title>Download ", ","));
        $FileName = str_replace(" ", ".", $FileName);
        $login = "http://www.easy-share.com/accounts/login";
        $post = array();
        $post ["login"] = $_REQUEST ["premium_user"] ? $_REQUEST ["premium_user"] : $premium_acc ["easyshare_com"] ["user"];
        $post ["password"] = $_REQUEST ["premium_pass"] ? $_REQUEST ["premium_pass"] : $premium_acc ["easyshare_com"] ["pass"];
        $post ["remember"] = "1";
        $page = $this->GetPage($login, 0, $post, "http://www.easy-share.com/", $pauth);
        $cookies = GetCookies($page);
        if (!preg_match("#PREMIUM=[\w%]+#", $cookies, $Premium)) {
            html_error("Login Failed , Bad username/password combination");
        }
        preg_match("#PHPSESSID=\w+#", $cookies, $PhpSessId);
        $page = $this->GetPage($link, $cookies, 0, $Referer, $pauth);
        $cookies = $PhpSessId[0] . "; " . $Premium[0] . "; " . GetCookies($page);
        if (preg_match("#Location: (.*)#", $page, $prelink)) {
            if (function_exists(encrypt) && $cookies != "") {
                $cookies = encrypt($cookies);
            }
            $Url = parse_url($prelink[1]);
            insert_location("$PHP_SELF?filename=" . urlencode($FileName) .
                    "&host=" . $Url["host"] .
                    "&path=" . urlencode($Url["path"] . ($Url["query"] ? "?" . $Url["query"] : "")) .
                    "&referer=" . urlencode($Referer) .
                    "&cookie=" . urlencode($cookies) .
                    "&email=" . ($_GET["domail"] ? $_GET["email"] : "") .
                    "&partSize=" . ($_GET["split"] ? $_GET["partSize"] : "") .
                    "&method=" . $_GET["method"] . "&proxy=" . ($_GET["useproxy"] ? $_GET["proxy"] : "") .
                    "&saveto=" . $_GET["path"] . "&link=" . $link . ($_GET["add_comment"] == "on" ? "&comment=" . urlencode($_GET["comment"]) : "") .
                    "&pauth=" . (isset($_GET["audl"]) ? "&audl=doum" : ""));
        }
        exit();
    }

}

/* * ************************************************\
  FIXED by kaox 04/07/2009
  FIXED and RE-WRITTEN by rajmalhotra on 10 Jan 2010
  FIXED by rajmalhotra on 12 Feb 2010 => FIXED downloading from Premium Account
  FIXED by vdhdevil on 01 Dec 2010 => Fixed Premium for v42
  FIXED by Ruud v.Tony on 6 Feb 2011 => Fixed the free codes, my first rapidleech code made, lol :D
  \************************************************* */
?>