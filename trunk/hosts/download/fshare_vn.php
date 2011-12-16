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
        } else {
            $this->DownloadFree($link);
        }
    }

    private function DownloadFree($link) {
        global $options;
        $page = $this->GetPage($link);
        $file_id = cut_str($page, 'name="file_id" value="', '"');
        is_present($page, "Vui lòng chờ lượt download kế tiếp ", "Vui lòng chờ lượt download kế tiếp ");
        $Cookies = GetCookies($page);
        $post = array();
        $post['link_file_pwd_dl']="";
        $post['action']="download_file";
        $post['file_id']=$file_id;        
        $page = $this->GetPage($link, $Cookies, $post, $link);
        is_notpresent($page, "var count = 30;", "Error 0x01: Plugin is out of date");
        insert_timer(30);
        if (!preg_match("#http://\w+.\w+.\w+/download/[^/]+/[^']+#", $page, $dlink)) {
            html_error("Error 0x10: Plugin is out of date");
        }
        $this->RedirectDownload($dlink[0], "fshare_vn", $Cookies, 0, $link);
        exit;
    }

    private function DownloadPremium($link) {
        html_error("Not support now");
    }

}

/*
 * by vdhdevil
 */
?>
