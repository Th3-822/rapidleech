<?php
if (!defined('RAPIDLEECH')) {
	require_once('index.html');
	exit;
}
class tinyupload_com extends DownloadClass {
	public function Download($link) {
        $page = $this->GetPage($link);
        if (!preg_match('/<br \/><a href="(.*?)"><b>(.*?)<\/b><\/a>/', $page, $info)) html_error('File not found!');
        $dlink = $info[1];
        preg_match('/(.*?)index\.php.*/', $link, $domain);
        $domain = $domain[1];
        $dlink = $domain.$dlink;
        $filename = $info[2];
        $this->RedirectDownload($dlink, $filename, 0, 0, $link);
    }
}

// [11-08-2017] Written by NimaH79.