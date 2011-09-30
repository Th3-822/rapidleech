<?php
if (!defined('RAPIDLEECH')) {
    require_once("index.html");
    exit;
}

class sourceforge_net extends DownloadClass {

    public function Download($link) {
        $page = $this->GetPage($link);
        $cookie = GetCookies($page);
        if (!preg_match('%<a href="(.*)" class="direct-download">%', $page, $tmp)) html_error('Can\'t find sourceforge redirect link!');
        $temp = html_entity_decode(urldecode(trim($tmp[1])), ENT_QUOTES, 'UTF-8');
        $t1 = cut_str($temp, 'http://downloads.', '?r');
        $t2 = cut_str($temp, 'use_mirror=', '\n');
        $dlink = "http://$t2.dl.$t1";
        $filename = parse_url($dlink);
        $FileName = basename($filename['path']);
        $this->RedirectDownload($dlink, $FileName, $cookie, 0, $link);
    }
}

/*
 * rewritten into OOP format by Ruud v.Tony 27-09-2011
 */
?>