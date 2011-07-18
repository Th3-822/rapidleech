<?php
if (!defined('RAPIDLEECH')) {
	require_once ("index.html");
	exit();
}

class plunder_com extends DownloadClass {

    public function Download($link) {

        $page = $this->GetPage($link);
        is_present($page, 'File or directory not found', 'The resource you are looking for might have been removed, had its name changed, or is temporarily unavailable.');

        if (preg_match('/Object moved to <a href="(.*)">/', $page, $temp)) {
            $link = 'http://www.plunder.com'.urldecode($temp[1]);
            $page = $this->GetPage($link);
        }
        if (!preg_match('#(http:\/\/tesla\.plunder\.com\/x\/[^"]+)"#', $page, $dl)) {
            html_error("Error : Plugin out of date!!!");
        }

        $dlink = trim($dl[1]);
        $Url = parse_url($dlink);
        $FileName = basename($Url['path']);
        $this->RedirectDownload($dlink, $FileName);
        exit();
    }
}

//Plunder free download plugin by Ruud v.Tony 24/04/2011
//updated 13/07/2011 by Ruud v.Tony
?>
