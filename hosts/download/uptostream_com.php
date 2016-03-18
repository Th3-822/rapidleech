<?php

if (!defined('RAPIDLEECH')) {
	require_once('index.html');
	exit();
}

class uptostream_com extends DownloadClass {
	public function Download($link) {
		if (!preg_match('@https?://(?:[\w\-]+\.)+[\w\-]+(?:\:\d+)?/(\w{12})(?=(?:[/\.]|(?:\.html?))?)@i', str_ireplace('/embed-', '/', $link), $link)) html_error('Invalid link?.');
		$vid = $link[1];
		$link = $link[0];

		$page = $this->GetPage($link);
		is_present($page, '404 (File not found)', 'File Not Found');

		if (!preg_match('@<title>(?>(.*?)</title>)@is', $page, $title)) html_error('Error: Video title not found.');
		if (!preg_match('@https?://(?:[\w\-]+\.)+[\w\-]+(?:\:\d+)?/\w+/(\d+)/0@i', $page, $DL)) html_error('Download link not found.');

		$filename = preg_replace('@[^ A-Za-z_\-\d\.,\(\)\[\]\{\}&\!\'\@\%\#]@u', '_', html_entity_decode(trim($title[1]), ENT_QUOTES, 'UTF-8'));
		$filename = preg_replace('@(?:\.(?:mp4|flv|mkv|webm|wmv|(m2)?ts|rm(vb)?|mpe?g?|vob|avi|[23]gp))+$@i', '', $filename);
		$filename .= " [UTS-{$DL[1]}p][$vid].mp4";

		$this->RedirectDownload($DL[0], $filename, 0, 0, 0, $filename);
	}
}

// [25-12-2015] Written by Th3-822.

?>