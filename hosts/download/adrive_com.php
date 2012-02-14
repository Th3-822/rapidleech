<?php
if (!defined('RAPIDLEECH')) {
    require_once ('index.html');
    exit();
}

class adrive_com extends DownloadClass {
    
    public function Download($link) {
        $page = $this->GetPage($link);
        is_present($page, 'The file you are trying to access is no longer available publicly.');
        $cookie = GetCookies($page);
        if (!preg_match('%<a href="(http:\/\/[^\r\n"]+)">here</a>%', $page, $dl)) html_error('Error [Download Link not found!]');
        $dlink = trim($dl[1]);
        $filename = basename(parse_url($dlink, PHP_URL_PATH));
        $this->RedirectDownload($dlink, $filename, $cookie);
        exit();
    }
}

/*
 * Converted & fixed into OOP format by Ruud v.Tony 10-02-2012
 */
?>