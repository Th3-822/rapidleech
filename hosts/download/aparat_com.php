<?php

if (!defined('RAPIDLEECH')) {
	require_once('index.html');
	exit;
}

class aparat_com extends DownloadClass {
	public function Download($link) {
		if (!preg_match('@aparat\.com/v/(\w+)@i', $link, $vid)) html_error('Video ID not found.');
		$vid = $vid[1];

		$page = $this->GetPage("http://www.aparat.com/v/$vid");
		is_present($page, "ویدیو مشابهی یافت نشد.", 'Video not found or it was deleted.');
		if (!preg_match('@<title>(?>(.*?)</title>)@is', $page, $title)) html_error('Error: Video title not found.');
		if (!preg_match('@https?://(?:[\w-]+\.)*aparat\.com/aparat-video/\w+(?:-(\d+p))?__\w+\.mp4@i', $page, $DL)) html_error('Download link not found.');

		$filename = preg_replace('@[^ A-Za-z_\-\d\.,\(\)\[\]\{\}&\!\'\@\%\#]@u', '_', html_entity_decode(trim($title[1]), ENT_QUOTES, 'UTF-8'));
		$filename = preg_replace('@(?:\.(?:mp4|flv|mkv|webm|wmv|(m2)?ts|rm(vb)?|mpe?g?|vob|avi|[23]gp))+$@i', '', $filename);
		$filename .= sprintf(' [Aparat%s][%s].mp4', (empty($DL[1]) ? '' : '-' . $DL[1]), $vid);

		$this->RedirectDownload($DL[0], $filename, 0, 0, 0, $filename);
	}
}

//[23-12-2015]  Written by Th3-822.
//[27-12-2015]  Fixed Regexp. - Th3-822

?>