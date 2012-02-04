<?php
if (!defined('RAPIDLEECH')) {
    require_once("index.html");
    exit();
}

class dl_free_fr extends DownloadClass {
    
    public function Download($link) {
        $page = $this->GetPage($link);
        if (preg_match('@Location: (http(s)?:\/\/[^\r\n]+)@i', $page, $redir)) {
            $link = trim($redir[1]);
            $page = $this->GetPage($link);
        }
        is_present($page, "Fichier inexistant.", "The file could not be found. Please check the download link.");
        is_present($page, "Le fichier demand&eacute; n'a pas &eacute;t&eacute; trouv&eacute;.", "The file could not be found. Please check the download link.");
        is_present($page, "Erreur 404 - Document non trouv&eacute;", "The file could not be found. Please check the download link.");
        is_present($page, "Appel incorrect.", "Incorrect link.");
        $cookie = GetCookies($page);
        if (!preg_match('@<a style="text-decoration: underline" id="link" href="(http(s)?:\/\/[^|\r|\n|"]+)">@', $page, $dl)) html_error("Error: Download link not found!");
        $dlink = trim($dl[1]);
        $filename = basename(parse_url($dlink, PHP_URL_PATH));
        $this->RedirectDownload($dlink, $filename, $cookie, 0, $link);
        exit();
    }
}
?>