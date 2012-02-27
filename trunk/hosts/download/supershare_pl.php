<?php
if (!defined('RAPIDLEECH')) {
    require_once("index.html");
    exit;
}

class supershare_pl extends DownloadClass {
    
    public function Download($link) {
        $page = $this->GetPage($link);
        is_present($page, 'DL_FileNotFound', 'File not found!');
        $cookie = GetCookies($page);
        if (preg_match('/var odl = (\d+);/', $page, $w)) $this->CountDown ($w[1]);
        if (!preg_match("@var downloadlink = '([^\r\n']+)'@", $page, $dl)) html_error('Error [Download Link not found!]');
        $filename = urldecode(cut_str($dl[1], "name=", "\r\n"));
        $this->RedirectDownload($dl[1], $filename, $cookie, 0, $link);
        exit();
    }
}
/****************************************************\
  WRITTEN BY KAOX 07-oct-09
  CONVERTED INTO OOP FORMAT BY Ruud v.Tony 14-02-2012
\****************************************************/
?>