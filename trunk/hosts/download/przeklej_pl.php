<?php
if (!defined('RAPIDLEECH')) {
    require_once("index.html");
    exit();
}

class przeklej_pl extends DownloadClass {
    
    public function Download($link) {
        $page = $this->GetPage($link);
        is_present($page, "Plik został usunięty przez użytkownika!", "File not found!");
        if (!preg_match('@<a href="(\/download\/[^"]+)" title@', $page, $rd)) html_error('Error: Redirect Link not found!');
        $rlink = 'http://www.przeklej.pl'.$rd[1];
        $page = $this->GetPage($rlink, 0, 0, $link);
        if (!preg_match('@Location: (http(s)?:\/\/[^\r\n]+)@i', $page, $dl)) html_error("Error: Download Link not found!");
        $dlink = trim($dl[1]);
        $filename = basename(parse_url($dlink, PHP_URL_PATH));
        $this->RedirectDownload($dlink, $filename, 0, 0, $link);
        exit();
    }
}

/*
 * by Ruud v.Tony 30-01-2012
 */
?>
