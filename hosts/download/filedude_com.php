<?php

if (!defined('RAPIDLEECH')) {
    require_once("index.html");
    exit;
}

class filedude_com extends DownloadClass {

    public function Download($link) {
        global $premium_acc;
        if (($_REQUEST["premium_acc"] == "on" && $_REQUEST["premium_user"] && $_REQUEST["premium_pass"]) || ($_REQUEST["premium_acc"] == "on" && $premium_acc["filedude_com"]["user"] && $premium_acc["filedude_com"]["pass"])) {
            $this->DownloadPremium($link);
        } elseif ($_POST['step'] == 1) {
            return $this->DownloadFree($link);
        } else {
            return $this->Prepare($link);
        }
    }

    private function Prepare($link) {
        $page = $this->GetPage($link);
        is_present($page, "The file you've requested doesn't exist ", "The file you've requested doesn't exist ");
        if (!preg_match('#(http://www.filedude.com/captcha/.*)"#', $page, $temp)) {
            html_error("Error: Image not found");
        }
        $fraction = cut_str($page, '<form action="', '"');
        $session = cut_str($page, 'name="session" value="', '" ');
        $data = $this->DefaultParamArr($link, 0, $link);
        $data['step'] = '1';
        $data['session'] = $session;
        $data['fraction'] = urlencode("http://www.filedude.com" . $fraction);
        $this->EnterCaptcha($temp[1], $data);
        echo $code;
    }

    private function DownloadFree($link) {
        if (empty($_POST['captcha'])) {
            html_error("You didn't enter the image verification code.");
        }
        $post = array();
        $post['session'] = $_POST['session'];
        $post['captcha'] = $_POST['captcha'];
        $Referer = $_POST['referer'];
        $dlink = urldecode($_POST['fraction']);
        $page = $this->GetPage($dlink, 0, $post, $Referer);
        if (!preg_match('#(http://.*/getN/.*)">#', $page, $temp)) {
            html_error("Error: Download link not found");
        }
        $Url = parse_url($temp[1]);
        $FileName = basename($Url["path"]);
        $this->RedirectDownload($temp[1], $FileName);
        exit;
    }
    private function DownloadPremium($link){
        global $premium_acc;
        $post=array();
        $post['user']=$_REQUEST["premium_user"] ? trim($_REQUEST["premium_user"]) : $premium_acc ["filedude_com"] ["user"];
        $post['pass']=$_REQUEST["premium_pass"] ? trim($_REQUEST["premium_pass"]) : $premium_acc ["filedude_com"] ["pass"];
        $page=$this->GetPage("http://www.filedude.com/premium_login", 0, $post, $link);
        $Cookies=GetCookies($page);
        $page=$this->GetPage($link, $Cookies, 0, $link);
        is_present($page, "The file you've requested doesn't exist ", "The file you've requested doesn't exist ");
        if (!preg_match("#Location: (.+)#", $page,$dlink)){
            html_error("Error 1x01: Plugin is out of date");
        }
        $Url=parse_url(trim($dlink[1]));
        $FileName=basename($Url['path']);
        $this->RedirectDownload(trim($dlink[1]), $FileName, $Cookies, 0, $link);
        exit();
    }

}

//by VDHDEVIL
?>