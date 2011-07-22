<?php
if (!defined('RAPIDLEECH')) {
    require_once ("index.html");
    exit ();
}

class bitshare_com extends DownloadClass {

    public function Download($link) {
        global $premium_acc, $options;
        if (($_REQUEST ["premium_acc"] == "on" && $_REQUEST ["premium_user"] && $_REQUEST ["premium_pass"]) || ($_REQUEST ["premium_acc"] == "on" && $premium_acc ["bitshare_com"] ["user"] && $premium_acc ["bitshare_com"] ["pass"])) {
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
        is_present($page, "Error - File not available", "Error - File not available");
        if (preg_match('#<span id="blocktimecounter">(\d+) seconds#', $page, $wait)) {
            //html_error("Please wait " . $wait[1] . " seconds to start the download");
            insert_timer($wait[1]);
        }
        $Cookies = GetCookies($page);
        if (!preg_match('#var ajaxdl = "(.*)"#', $page, $temp)) {
            html_error("Error 0x01- Plugin is out of date", 0);
        }
        if (!preg_match('#(http.*files-ajax.*)"#', $page, $UrlPost)) {
            html_error("Error 0x02- Plugin is out of date", 0);
        }
        $ajaxdl = $temp[1];
        $post = array();
        $post["request"] = "generateID";
        $post["ajaxid"] = $ajaxdl;
        $page = $this->GetPage(trim($UrlPost[1]), $Cookies, $post, $link);
        if (preg_match("#(\d+)</span> seconds#", $page, $wait)) {
            insert_timer($wait[1]);
        } else {
            insert_timer(60);
        }
        $page = $this->GetPage("http://www.google.com/recaptcha/api/challenge?k=6LdtjrwSAAAAACepq37DE6GDMp1TxvdbW5ui0rdE");
        $ch = cut_str($page, "challenge : '", "'");
        $img = "http://www.google.com/recaptcha/api/image?c=" . $ch;
        $page = $this->GetPage($img);
        $headerend = strpos($page, "\r\n\r\n");
        $pass_img = substr($page, $headerend + 4);
        write_file($options['download_dir'] . "bitshare_captcha.jpg", $pass_img);
        $randnum = rand(10000, 100000);
        $img_data = explode("\r\n\r\n", $page);
        $header_img = $img_data[0];
        $data = $this->DefaultParamArr($link, $Cookies);
        $data["request"] = "validateCaptcha";
        $data["ajaxid"] = $ajaxdl;
        $data["recaptcha_challenge_field"] = $ch;
        $data['step'] = '1';
        $data['urlpost']=urlencode(trim($UrlPost[1]));
        $this->EnterCaptcha($options['download_dir'] . "bitshare_captcha.jpg", $data, 20);
        exit;
    }

    private function DownloadFree($link) {
        $post = array();
        $ajaxid = $_POST["ajaxid"];
        $post["request"] = "validateCaptcha";
        $post["ajaxid"] = $ajaxid;
        $post["recaptcha_challenge_field"] = $_POST["recaptcha_challenge_field"];
        $post["recaptcha_response_field"] = $_POST['captcha'];
        $Cookies = urldecode($_POST['cookie']);
        $UrlPost=urldecode($_POST['urlpost']);
        $page = $this->GetPage($UrlPost, $Cookies, $post, $link);
        if (!preg_match("#SUCCESS#", $page)) {
            html_error("Wrong captcha");
        }
        unset($post);
        $post["request"] = "getDownloadURL";
        $post["ajaxid"] = $ajaxid;
        $page = $this->GetPage($UrlPost, $Cookies, $post, $link);
        if (!preg_match('#(http://.*)#', $page, $dlink)) {
            html_error("Error 0x10- Plugin is out of date");
        }
        $this->RedirectDownload(trim($dlink[1]), "FileName", $Cookies, 0, $link);
        exit;
    }

    private function DownloadPremium($link) {
        global $premium_acc;
        $urllogin = "http://bitshare.com/login.html";
        $post = array();
        $post["user"] = $_REQUEST["premium_user"] ? trim($_REQUEST["premium_user"]) : $premium_acc ["bitshare_com"] ["user"];
        $post["password"] = $_REQUEST["premium_pass"] ? trim($_REQUEST["premium_pass"]) : $premium_acc ["bitshare_com"] ["pass"];
        $post["rememberlogin"] = "";
        $post["submit"] = "Login";
        $page = $this->GetPage($urllogin, 0, $post, "http://bitshare.com");
        $cookies = GetCookies($page);
        is_notpresent($cookies, "login", "Login Failed , Bad username/password combination");
        $page = $this->GetPage($link, $cookies, 0, "http://bitshare.com");
        is_present($page, "Error - File not available", "Error - File not available");
        if (preg_match("#Location: (.*)#", $page, $temp)) {
        } else {
            if (!preg_match('#http:\/\/.+files-ajax.+\w#', $page,$UrlPost)){
                html_error("Error 1x01: Plugin is out of date");
            }
            if (!preg_match('#ajaxdl = "(.*)"#', $page,$ajaxid)){
                html_error("Error 1x02: Plugin is out of date");
            }
            unset($post);
            $post['request']="generateID";
            $post['ajaxid']=$ajaxid[1];
            $page=$this->GetPage($UrlPost[0], $cookies, $post, $link);
            unset($post);
            $post['request']="getDownloadURL";
            $post['ajaxid']=$ajaxid[1];
            $page=$this->GetPage($UrlPost[0],$cookies,$post,$link);
            if (!preg_match("/(http:\/\/.*\w)/", $page,$temp)){
                html_error("Error 1x03: Plugin is out of date");
            }
        }
        $this->RedirectDownload(trim($temp[1]), "Bitshare");
        exit();
    }
}

/*
 * by vdhdevil Dec-11-2010
 */
?>