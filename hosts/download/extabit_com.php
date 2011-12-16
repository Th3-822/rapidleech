<?php

if (!defined('RAPIDLEECH')) {
    require_once("index.html");
    exit;
}

class extabit_com extends DownloadClass {

    public function Download($link) {
        global $premium_acc, $options;
        if (($_REQUEST ["premium_acc"] == "on" && $_REQUEST ["premium_user"] && $_REQUEST ["premium_pass"]) || ($_REQUEST ["premium_acc"] == "on" && $premium_acc ["extabit_com"] ["user"] && $premium_acc ["extabit_com"] ["pass"])) {
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
        is_present($page, "File not found", "File not found");        
        $Cookies = GetCookies($page);
        if (preg_match("#Location: (.*)#", $page, $tlink)) {
            $page = $this->GetPage(trim($tlink[1]), $Cookies, 0, $link);
            $Cookies.="; " . GetCookies($page);
            $link = trim($tlink[1]);
        }
        is_present($page, "Only premium users can download this file","Only premium users can download this file");
        if (!preg_match('#file_\w+/[^"]+#', $page, $tmp)) {
            $act = $link;
        } else {
            $act = "http://extabit.com/" . $tmp[0];
        }
        if (strpos($page, "30</div>")) {
            insert_timer(30);
        }
        if (!preg_match('#capture.gif?[^"]+#', $page, $tmp)) {            
            html_error("Error 0x02: Plugin is out of date");
        }
        $img = "http://extabit.com/$tmp[0]";
        $page = $this->GetPage($img, $Cookies);
        $headerend = strpos($page, "\r\n\r\n");
        $pass_img = substr($page, $headerend + 4);
        $t = strpos($pass_img, "GIF87");
        $pass_img = ltrim(substr($pass_img, $t - 2), "\r\n");
        write_file($options['download_dir'] . "extabit.gif", $pass_img);
        $data = array();
        $data['link'] = $link;
        $data['act'] = $act;
        $data['step'] = "1";
        $data['Cookies'] = $Cookies;
        $this->EnterCaptcha($options['download_dir'] . "extabit.gif", $data, 15);        
        exit;
    }

    private function DownloadFree($link) {
        $Cookies = $_POST['Cookies'];
        $Captcha = $_POST['captcha'];
        $actlink = $_POST['act'];
        $page = $this->GetPage($actlink . "?capture=$Captcha", $Cookies, 0, $link);
        preg_match("#fdl=\w+#", $page, $fdl);
        $Cookies.="; " . $fdl[0] . "; iua=1; show_part=1;";
        $page = $this->GetPage($link, $Cookies, 0, $link);
        if (!preg_match('#http://guest\d+.extabit.com/\w+/[^"]+#', $page, $dlink)) {            
            html_error("Error 0x10: Plugin is out of date");
        }
        $this->RedirectDownload($dlink[0], "extabit", $Cookies, 0, $link);
        exit;
    }
    private function DownloadPremium($link) {
        global $premium_acc;        
        $post=array();
        $post['email']=$_REQUEST["premium_user"] ? trim($_REQUEST["premium_user"]) : $premium_acc ["extabit_com"] ["user"];
        $post['pass']=$_REQUEST["premium_pass"] ? trim($_REQUEST["premium_pass"]) : $premium_acc ["extabit_com"] ["pass"];
        $post['remember']="1";
        $post['auth_submit_login.x']=rand(0,47);
        $post['auth_submit_login.y']=rand(0,9);
        $page=$this->GetPage("http://extabit.com/login.jsp", 0, $post, "http://extabit.com/");
        $Cookies.="; ".GetCookies($page);
        is_notpresent($page, "auth_uid", "Login Failed");        
        $page=$this->GetPage($link, $Cookies);
        is_present($page, "File not found", "File not found");        
        if (preg_match("#Location: (.*)#", $page,$tmp)){
            $page=$this->GetPage(trim($tmp[1]), $Cookies);
            $Cookies.="; ".GetCookies($page);            
        }        
        if (!preg_match('#http://\w+\d+.extabit.com/[^"]+#', $page, $dlink)){
            html_error("Error 1x01: Plugin is out of date");            
        }
        $this->RedirectDownload($dlink[0], "Extabit", $Cookies, 0, trim($tmp[1]));
        exit;                
    }
}
?>
