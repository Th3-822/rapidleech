<?php
if (!defined('RAPIDLEECH')) {
	require_once('index.html');
	exit;
}
class porntrex_com extends DownloadClass {
	public function Download($link) {
        $page = $this->GetPage(str_replace('http', 'https', $link));
        if (!preg_match_all('/\'(https?:.*?\.mp4\/)\'/', $page, $dlink)) html_error('Video not found!');
        $dlink = $dlink[1];
        $dlink = $dlink[count($dlink)-1];
        preg_match('/<title>(.*?)</', $page, $filename);
        $filename = $filename[1];
        $filename = $filename.'.mp4';
        $this->RedirectDownload($dlink, $filename, 0, 0, $link);
    }
}

// [07-12-2017] Written by NimaH79.