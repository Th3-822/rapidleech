<?php

if (!defined('RAPIDLEECH')) {
    require_once("index.html");
    exit;
}

class zippyshare_com extends DownloadClass {

    public function Download($link) {
        $page = $this->GetPage($link);
        is_present($page, "File does not exist on this server", "File does not exist on this server");
        $cookies = GetCookies($page);
        $FileName = trim(cut_str($page, 'addthis:title=""','"'));
        if (preg_match_all("#var (\w) = (\d+);#", $page, $temp)){
            $a=$temp[2][0];
            $b=$temp[2][1];
            $a=floor($temp[2][0]/3);
            $dlink=str_replace("/v/", "/d/", $link);
            $dlink=str_replace("file.html", $a+$temp[2][0]%$temp[2][1], $dlink);
            if (!preg_match('#\(\'dlbutton\'\).href.*"([^"]+)".+"([^"]+)";#', $page, $temp)){
                html_error("Error 0x01:Plugin is out of date");
            }
            $dlink.=$temp[2];
            //html_error($dlink);
        } else if (preg_match("/url: '([^']+)', seed: (\d+)}/i", $page, $L)) {
            $dlink = $L[1] . "&time=" . 24 * $L[2] % 6743256; // return 24 * param1 % 6743256;
		} else if (preg_match('/\/([0-9]+)\/"\+\(([0-9]+)\%([0-9]+) \+ ([0-9]+)\%([0-9]+)\)\+"\/(.+)";/', $page, $L)) {
			$server = cut_str($link,'http://','.zippyshare.com');
			$dlink = "http://" . $server . ".zippyshare.com/d/" . $L[1] . "/" . (($L[2]%$L[3])+($L[4]%$L[5])) . "/" . $L[6];
			$replace = array("%20", "%28", "%29", "%26");
			$replacewith = array(" ", "(", ")", "&");
			$FileName = str_replace($replace, $replacewith, $L[6]);
        } else {
            html_error("Error 0x02: Plugin is out of date");
        }
        $Url=parse_url($dlink);
        if (!$FileName) $FileName=basename($Url['path']);
        $this->RedirectDownload(trim($dlink), $FileName, $cookies, 0, $link, $FileName);
    }

}

/*
 * By vdhdevil Jan-12-2010
 * Updated March-8-2011
 * Fixed Mai-22-2011 by defport
 * Credit to  Th3-822, motor
 */
?>