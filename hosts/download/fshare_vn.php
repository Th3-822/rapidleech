<?php

if (!defined('RAPIDLEECH')) {
    require_once("index.html");
    exit;
}

class fshare_vn extends DownloadClass {

    public function Download($link) {
        global $premium_acc;
        if (($_REQUEST["premium_acc"] == "on" && $_REQUEST["premium_user"] && $_REQUEST["premium_pass"]) || ($_REQUEST["premium_acc"] == "on" && $premium_acc["fshare_vn"]["user"] && $premium_acc["fshare_vn"]["pass"])) {
            $this->DownloadPremium($link);
        } elseif ($_POST['step'] == 1) {
            $this->DownloadFree($link);
        } else {
            $this->Retrieve($link);
        }
    }
    private function Retrieve($link){
        global $options;
        $page=$this->GetPage($link);
        $file_id=cut_str($page, 'name="file_id" value="', '"');
        $Cookies=GetCookies($page);
        is_notpresent($page, "k=6LctfsMSAAAAAHg-CL-YfZSqIBVlje9C8SOP3CFT", "Error 0x01: Plugin is out of date");
        $page = $this->GetPage("http://www.google.com/recaptcha/api/challenge?k=6LctfsMSAAAAAHg-CL-YfZSqIBVlje9C8SOP3CFT");
        $ch = cut_str($page, "challenge : '", "'");
        $img = "http://www.google.com/recaptcha/api/image?c=" . $ch;
        $page = $this->GetPage($img);
        $headerend = strpos($page, "\r\n\r\n");
        $pass_img = substr($page, $headerend + 4);
        write_file($options['download_dir'] . "fsharevn_captcha.jpg", $pass_img);
        $data = $this->DefaultParamArr($link);
        $data['step'] = 1;
        $data['file_id'] = $file_id;
        $data['recaptcha_challenge_field'] = $ch;
        $data['Cookies'] = $Cookies;
        $this->EnterCaptcha($options['download_dir'] . "fsharevn_captcha.jpg", $data, 10);
        exit;
    }
    private function DownloadFree($link){
        $post=array();
        $post['recaptcha_challenge_field']=$_POST['recaptcha_challenge_field'];
        $post['recaptcha_response_field']=$_POST['captcha'];
        $post['btn_download']="Download";
        $post['action']="download_file";
        $post['file_id']=$_POST['file_id'];
        $Cookies = $_POST['Cookies'];
        $page=$this->GetPage($link, $Cookies, $post, $link);
        insert_timer(30);
        if (!preg_match("#http://\w+.\w+.\w+/download/[^/]+/[^']+#", $page,$dlink)){
            html_error("Error 0x10: Plugin is out of date");
        }
        $this->RedirectDownload($dlink[0], "fshare_vn", $Cookies, 0, $link);
        exit;
    }
    private function DownloadPremium($link){
        html_error("Not support now");
    }
}
/*
 * by vdhdevil
 */
?>
