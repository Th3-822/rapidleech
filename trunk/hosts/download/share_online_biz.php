<?php

if (!defined('RAPIDLEECH')) {
    require_once ("index.html");
    exit();
}

class share_online_biz extends DownloadClass {
  
    public function Download($link) {
        global $premium_acc;
        if (($_REQUEST ["premium_acc"] == "on" && $_REQUEST ["premium_user"] && $_REQUEST ["premium_pass"]) || ($_REQUEST ["premium_acc"] == "on" && $premium_acc ["share-online_biz"] ["user"] && $premium_acc ["share-online_biz"] ["pass"])) {
            $this->DownloadPremium($link);
        } else if ($_POST["step"] == "1") {
            $this->DownloadLink($link);
        } else if ($_POST["step"] =="captcha"){
            $this->DownloadFree($link);
        }else{
            $this->Retrieve($link);
        }
    }

    private function DownloadLink($link) {
        $Cookies = decrypt(urldecode($_POST['cookie']));
        $Referer = $_POST['referer'];
        $FileName = $_POST['filename'];
        $this->RedirectDownload(trim($link), $FileName, $Cookies, 0, $Referer);
        exit;
    }

    private function Retrieve($link) {
        $page = $this->GetPage($link);
        if (preg_match("#Location: (.+)#", $page, $temp)) {
            $link = "http://www.share-online.biz" . $temp[1];
            $page = $this->GetPage($link);
        }
        $Cookies = GetCookies($page);
        $post = array();
        $post['dl_free'] = "1";
        $post['choice'] = "free";
        $page = $this->GetPage(trim($link) . "/free/", $Cookies, $post, $link);
        //$nfo = getinfo(cut_str($page, 'var nfo="', '"'));

        is_present($page, "failure/full/", "No free slots for free users");
        if (!preg_match("#var wait=(\d+)#", $page, $count)) {
            html_error("Error 0x01: Plugin is out of date");
        }
        $dl = cut_str($page, 'dl="', '"');
        insert_timer($count[1]);
        $data = array();
        $data['dl']=$dl;
        $data['captchaid'] = str_replace("chk||", "", base64_decode($dl));        
        $data['Cookies'] = $Cookies;
        $data['checkcaptchaurl']=  str_replace("///","/free/captcha/", cut_str($page, ";var url='", "'"));
        $data['step']='captcha'; //overwrite step=1
        $this->Recaptcha($link, "6LdatrsSAAAAAHZrB70txiV5p-8Iv8BtVxlTtjKX", "share-online", $data);
        exit;
    }

    private function DownloadFree($link) {
        $post = array();
        $post['dl_free'] = "1";
        $post['captcha'] = $_POST['captchaid'];
        $post['recaptcha_challenge_field'] = $_POST['recaptcha_challenge_field'];
        $post['recaptcha_response_field'] = $_POST['captcha'];
        //$dl=$_POST['dl'];
        $Cookies=$_POST['Cookies'];
        $page=$this->GetPage($_POST['checkcaptchaurl'], $Cookies, $post, $referer);        
        preg_match("#[\r\n](\w{5,})[\r\n]#",$page,$tmp);        
        if ($tmp[1]=="0") html_error("Wrong captcha");        
        $dl=  base64_decode($tmp[1]);        
        insert_timer(30);
        $this->RedirectDownload($dl, "share-online", $Cookies,0,$link);
        exit;
    }

    private function DownloadPremium($link) {
        global $premium_acc;
        preg_match('#([A-Z0-9]+)#', $link, $link_id);
        $username = $_REQUEST["premium_user"] ? trim($_REQUEST["premium_user"]) : $premium_acc["share-online_biz"]["user"];
        $password = $_REQUEST["premium_pass"] ? trim($_REQUEST["premium_pass"]) : $premium_acc["share-online_biz"]["pass"];
        $url = 'http://api.share-online.biz/account.php?username=' . $username . '&password=' . $password . '&act=userDetails';
        $page = $this->GetPage($url, 0, 0, 0);
        is_present($page, "EXCEPTION input data invalid", "Check your login details!");
        preg_match('#(a=.+)#', $page, $details);
        $url = 'http://api.share-online.biz/account.php?username=' . $username . '&password=' . $password . '&act=download&lid=' . $link_id[1] . '';
        $page = $this->GetPage($url, $details[1], 0, $url);
        preg_match('#URL: (.+)#', $page, $newurl);
        $this->RedirectDownload(trim($newurl[1]), "share-online", $details[1], 0, $link);
        exit;        
    }
    
    private function getinfo($a) {
        $a = explode("a|b", implode("", array_reverse(preg_split("##", $a))));
        $a[1] = str_split($a[1], 3);
        $a[0] = preg_split("##", $a[0], -1, PREG_SPLIT_NO_EMPTY);
        $b = array();
        foreach ($a[1] as $key => $value) {
            $b[hexdec(strtoupper($value))] = $key;
        }
        ksort($b);
        $a[1] = "";
        foreach ($b as $key => $value) {
            if (isset($a[0][$value]))
                $a[1].=$a[0][$value];
            else
                $a[1].=" ";
        }
        return $a[1];
    }
}
