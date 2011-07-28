<?php
if (!defined('RAPIDLEECH')) {
	require_once("index.html");
	exit();
}

class ifile_it extends DownloadClass {

    public function Download($link) {
        $page = $this->GetPage($link);
        is_present($page, "File Not Found", "File Not Found");
        is_present($page, "no such file", "File Not Found");
        $cookie = GetCookies($page);

        $FileName = cut_str($page, 'addthis:title="','"');
        $filekey = cut_str($page, "var __file_key				=	'","'");
        $alias_id = cut_str($page, "var __alias_id				=	'","'");
        $page = $this->GetPage("http://ifile.it/download:dl_request?alias_id=".$alias_id."&type=na&kIjs09=845&e94fa1af87=35490", $cookie, 0, $link);
        $cur_url = "http://ifile.it/$filekey/$FileName";
        if (strpos($page, '"status":"ok"')) {
            if (strpos($page, '"captcha":1')) {
                html_error("Error, ur host get captcha, I will try to support captcha later!");
            }else {
                $page = $this->GetPage($cur_url, $cookie, 0, $link);
            }
        }
        $page = $this->GetPage($cur_url, $cookie, 0, $link);
        $dlink = cut_str($page,'<a target="_blank" href="','"');
        if (!$dlink) html_error("Error: Plugin is out of date!");
        $Url = parse_url($dlink);
        if (!$FileName) $FileName = basename($Url['path']);
        $this->RedirectDownload($dlink, $FileName, $cookie, 0, $link);
        exit();
    }
}

/*
 * ifile.it free download plugin by Shy2play(untamedsolitude.co.cc) for kaskus.us
 * rewritten in OOP format by Ruud v.Tony 25-07-2011
 */
?>