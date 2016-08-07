<?php

if (!defined('RAPIDLEECH')) {
	require_once('index.html');
	exit;
}

class namasha_com extends DownloadClass {
	public function Download($link) {
		if (!preg_match('@https?://www\.namasha\.com/v/(\w+)@i', str_ireplace('//namasha.com', '//www.namasha.com', $link), $vid)) html_error('Invalid Link.');
		$link = $GLOBALS['Referer'] = $vid[0];

		$page = $this->GetPage($link);
		if (substr($page, 9, 3) == '404') html_error('404 Video Not Found');

		if (!preg_match('@<h1\s+style="font-size: 24px">\s*([^"<>]+?)\s*</h1>@i', $page, $title)) html_error('Error: Video title not found.');
		if (!preg_match('@https?://s\d+\.namasha\.com/videos/\d+\.mp4@i', $page, $DL)) html_error('Error: Download link not found.');
		$DL = urldecode($DL[0]);

		if (!preg_match('@\.(?:mp4|flv|webm|avi)$@i', basename($DL), $ext)) $ext = array('.flv');
		$filename = preg_replace('@(?:\.(?:mp4|flv|mkv|webm|wmv|(m2)?ts|rm(vb)?|mpe?g?|vob|avi|[23]gp))+$@i', '', preg_replace('@[^ A-Za-z_\-\d\.,\(\)\[\]\{\}&\!\'\@\%\#]@u', '_', html_entity_decode(trim($title[1]), ENT_QUOTES, 'UTF-8')));
		$filename .= sprintf(' [namasha][%s]%s', $vid[1], $ext[0]);

		$this->RedirectDownload($DL, $filename, 0, 0, 0, $filename);
	}
}

//[10-7-2016]  Written by Th3-822.

?>