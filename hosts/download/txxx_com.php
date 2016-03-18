<?php

if (!defined('RAPIDLEECH')) {
	require_once('index.html');
	exit;
}

class txxx_com extends DownloadClass {
	public function Download($link) {
		if (!preg_match('@(https?://(?:www\.)?txxx\.com/videos/)(\d+)(/[^/]+/)@i', $link, $vid)) html_error('Invalid Link.');
		$link = $GLOBALS['Referer'] = $vid[1] . $vid[2] . $vid[3];

		$page = $this->GetPage($link);
		if (!preg_match('@ class="video-info__title">\s*<h1>\s*([^"<>]+)\s*</h1>@i', $page, $title)) html_error('Error: Video title not found. Video deleted?');
		if (!preg_match('@https?://(?:[\w\-]+\.)+[\w\-]+(?:\:\d+)?/get_file/[^\'\"\t<>\r\n\\\]+@i', $page, $DL)) html_error('Error: Download link not found.');
		if (!preg_match('@\.(?:mp4|flv|webm|avi)(?=/?br=\d+)?$@i', basename($DL[0]), $ext)) $ext = array('.mp4');
		$filename = preg_replace('@(?:\.(?:mp4|flv|mkv|webm|wmv|(m2)?ts|rm(vb)?|mpe?g?|vob|avi|[23]gp))+$@i', '', preg_replace('@[^ A-Za-z_\-\d\.,\(\)\[\]\{\}&\!\'\@\%\#]@u', '_', html_entity_decode(trim($title[1]), ENT_QUOTES, 'UTF-8')));
		$filename .= sprintf(' [txxx][%s]%s', $vid[2], $ext[0]);

		$this->RedirectDownload($DL[0], $filename, 0, 0, 0, $filename);
	}
}

//[20-2-2016]  Written by Th3-822.

?>