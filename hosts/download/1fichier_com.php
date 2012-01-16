<?php
if (!defined('RAPIDLEECH')) {
    require_once ("index.html");
    exit ();
}

class d1fichier_com extends DownloadClass {

    public function Download($link) {
        //Define the existing domain for easier check in regex download link
        if (!preg_match('@http:\/\/\w+\.([^\/]+)\/?@', $link, $match)) html_error("Can't determine 1fichier domains in link!");
        $this->domain = trim($match[1]);
        //Get the link
        $page = $this->GetPage($link);
        is_present($page, "The requested file could not be found ");
        is_present($page, "Le fichier demandé n'existe pas.", "The requested file could not be found "); //Francais
        is_present($page,"You already downloading some files","You already downloading some files.Please wait a few seconds before downloading new ones");
        $Cookies=GetCookies($page);
        if (!preg_match("@http://.+$this->domain/get/[^\"]+@", $page,$dl)) html_error("Error: Download link not found!");
        $dlink = trim($dl[0]);
        $filename = basename(parse_url($dlink, PHP_URL_PATH));
        $this->RedirectDownload($dlink, $filename, $Cookies);
        exit;
    }

}

//by vdhdevil
//Updated by Ruud v.Tony for another 1fichier domain without adding new plugin 13-01-2012
?>
