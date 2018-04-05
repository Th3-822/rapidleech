<?php
if (!defined('RAPIDLEECH')) {
	require_once('index.html');
	exit;
}
class pornhub_com extends DownloadClass {
	public function Download($link) {
        $page = $this->GetPage(str_replace('http', 'https', $link));
        if (!preg_match('/"videoUrl":"(.*?)"/', $page, $dlink)) html_error('Video not found!');
        $dlink = $dlink[1];
        $dlink = str_replace('\\', '', $dlink);
        preg_match('/data-video-title="(.*?)"/', $page, $filename);
        $filename = $filename[1];
        $this->RedirectDownload($dlink, $filename, 0, 0, $link);
    }
}

// [11-08-2017] Written by NimaH79.