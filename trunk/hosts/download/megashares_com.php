<?php

if (!defined('RAPIDLEECH')) {
    require_once("index.html");
    exit;
}

class megashares_com extends DownloadClass {

    public function Download($link) {
        global $premium_acc;
        if (( $_REQUEST ["premium_acc"] == "on" && $_REQUEST ["premium_user"] && $_REQUEST ["premium_pass"] ) ||( $_REQUEST ["premium_acc"] == "on" && $premium_acc ["megashares_com"] ["user"] && $premium_acc ["megashares_com"] ["pass"] )) {
            $this->DownloadPremium($link);
        } else if ($_POST['step'] == "1") {
            $this->DownloadFree($link);
        } else {
            $this->Retrieve($link);
        }
    }

    private function Retrieve($link) {
        global $options;
        $page = $this->GetPage($link);
        is_present($page, "Link was removed due to violation of our TOS", "Link is not invalid");
        $Cookies = GetCookies($page);
        $random_num = cut_str($page, 'id="random_num" value="', '"');
        $passport_num=cut_str($page, 'id="passport_num" value="', '"');
        preg_match('#http://[a-z]{7}\d+.megashares.com/[^"]+#', $page, $dlink);
        $img = "http://d01.megashares.com/index.php?secgfx=gfx&random_num={$random_num}";
        $page = $this->GetPage($img, $Cookies, 0, $link);
        $headerend = strpos($page, "\r\n\r\n");
        $pass_img = (substr($page, $headerend + 4));
        if (preg_match("#\w{4}\r\n#", $pass_img)) {
            $t = strpos($pass_img, "P");
            $pass_img = ltrim(substr($pass_img, $t - 2), "\r\n");
        }
        write_file($options['download_dir'] . "megashares_captcha.png", $pass_img);
        $data=array();
        $data['step']="1";
        $data['Cookies']=$Cookies;
        $data['link']=$link;
        $data['random_num']=$random_num;
        $data['passport_num']=$passport_num;
        $data['dlink']=$dlink[0];
        $this->EnterCaptcha($options['download_dir'] . "megashares_captcha.png", $data, "7");
        exit;
    }
    private function DownloadFree($link){
        $glink="{$link}&rs=check_passport_renewal&rsargs[]={$_POST['captcha']}&rsargs[]={$_POST['random_num']}&rsargs[]={$_POST['passport_num']}&rsargs[]=replace_sec_pprenewal&rsrnd=".  round(microtime(true))*1000;
        $Cookies=$_POST['Cookies'];
        $page=$this->GetPage($glink, $Cookies, 0, $link);
        $this->RedirectDownload($_POST['dlink'], "Megashares", $Cookies, 0, $link);
        exit;
        
    }
    private function DownloadPremium($link) {
        global $premium_acc;
        $post = array();
        $post['httpref'] = "";
        $post['mymslogin_name'] = $_REQUEST ["premium_user"] ? $_REQUEST ["premium_user"] : $premium_acc ["megashares_com"] ["user"];
        $post['mymspassword'] = $_REQUEST ["premium_pass"] ? $_REQUEST ["premium_pass"] : $premium_acc ["megashares_com"] ["pass"];
        $post['myms_login'] = "Login";
        $page = $this->GetPage("http://d01.megashares.com/myms_login.php", 0, $post);
        $Cookies = GetCookies($page);
        is_notpresent($Cookies, "myms", "Login Failed");
        $page = $this->GetPage($link, $Cookies, 0, $link);
        if (preg_match("#Location: (.+)#", $page, $match)) {
            $page = $this->GetPage(trim($match[1]), $Cookies, 0, $link);
            $link = $match[1];
        }
        is_present($page, "Link was removed due to violation of our TOS", "Link is not invalid");
        $Cookies.="; " . GetCookies($page);
        if (!preg_match('#http://webprod\d+.\w+.\w+[^"]+#', $page, $dlink)) {
            html_error("Error: Plugin is out of date or Account is expired");
        }
        $this->RedirectDownload($dlink[0], "megashares", $Cookies, 0, $link);
        exit();
    }

}

?>
