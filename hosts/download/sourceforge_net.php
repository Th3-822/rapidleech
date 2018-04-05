<?php
if (!defined('RAPIDLEECH')) {
    require_once("index.html");
    exit;
}
class sourceforge_net extends DownloadClass {
    public function Download($link) {
        $page = $this->GetPage($link);
        $cookie = GetCookies($page);
        if(!preg_match('%<a href="(.*)" class="direct-download">%', $page, $tmp) && !preg_match('%<meta http-equiv="refresh" content="5; url=(.*?)"%', $page, $tmp)) html_error('Can\'t find sourceforge redirect link!');
        $temp = html_entity_decode(urldecode(trim($tmp[1])), ENT_QUOTES, 'UTF-8');
        preg_match('/https?:\/\/downloads(.*?)\?r/', $temp, $t1);
        $t1 = $t1[1];
        preg_match('/use_mirror=(.*?)$/', $temp, $t2);
        $t2 = $t2[1];
        $dlink = "https://$t2.dl$t1";
        $filename = parse_url($dlink);
        $FileName = basename($filename['path']);
        $this->RedirectDownload($dlink, $FileName, $cookie, 0, $link);
    }
}

// [27-09-2011] Rewritten into OOP format by Ruud v.Tony.
// [10-08-2017] Fixed RegEx by NimaH79.
// [12-01-2018] Updated by NimaH79.
// [06-04-2018] Fixed RegEx by NimaH79.
