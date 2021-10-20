<?php
if (!defined('RAPIDLEECH')) {
	require_once('index.html');
	exit;
}
class transfer_sh extends DownloadClass {
	public function Download($link) {
        $page = $this->GetPage($link);
        $filename = parse_url($link);
        $filename = basename($filename['path']);
        $this->RedirectDownload($link, $filename, $cookie, 0, $link);
    }
}

//[11-08-2017]  Written by NimaH79.