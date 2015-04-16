<?php
if (!defined('RAPIDLEECH')) {
    require_once('index.html');
    exit();
}

class datafilehost_com extends DownloadClass {
    
    public function Download($link) {
        $page = $this->GetPage($link);
        is_present($page, cut_str($page, '<div style="text-align:center">', '<br>'));
        $cookie = GetCookiesArr($page);
        if (!preg_match('/https?:\/\/(?:www\.)?datafilehost\.com\/get\.php\?file=[^\'\"\t<>\r\n\\\]+/i', $page, $dl)) html_error("Error[DownloadLink] not found!");
        $dlink = trim($dl[0]);
        $filename = basename(parse_url($dlink, PHP_URL_PATH));
        $this->RedirectDownload($dlink, $filename, $cookie, 0, $link);
        exit();
    }
}

/*
 * by Ruud v.Tony 06-02-2012 (while chatting with Palooo & Th3-822, :D)
 * fix small regex by Tony Fauzi Wihana/Ruud v.Tony 04/01/2013
 */
?>
