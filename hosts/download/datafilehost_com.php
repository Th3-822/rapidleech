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
        if (!preg_match('@Download: <a href="([^"]+)">Click Here@', $page, $dl)) html_error("Error[DownloadLink] not found!");
        $dlink = trim($dl[1]);
        $filename = basename(parse_url($dlink, PHP_URL_PATH));
        $this->RedirectDownload($dlink, $filename, $cookie, 0, $link);
        exit();
    }
}

/*
 * by Ruud v.Tony 06-02-2012 (while chatting with Palooo & Th3-822, :D)
 */
?>
