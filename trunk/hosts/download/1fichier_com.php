<?php

if (!defined('RAPIDLEECH')) {
    require_once ("index.html");
    exit ();
}

class d1fichier_com extends DownloadClass {

    public function Download($link) {
        $page = $this->GetPage($link);
        is_present($page,"You already downloading some files","You already downloading some files.Please wait a few seconds before downloading new ones");
        $Cookies=GetCookies($page);
        if (!preg_match('#http://.+1fichier.com/get/[^"]+#', $page,$dlink)){
            html_error("Error 0x01: Plugin is out of date");
        }
        $Url =parse_url(trim($dlink[0]));
        $FileName=basename($Url['path']);
        $this->RedirectDownload(trim($dlink[0]),$FileName , $Cookies);
        exit;
    }

}

//by vdhdevil
?>
