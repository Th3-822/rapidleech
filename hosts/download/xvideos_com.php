<?php

if (!defined('RAPIDLEECH')) {
	require_once('index.html');
	exit;
}

class xvideos_com extends DownloadClass {
	public function Download($link) {
		if (!preg_match('@(https?://(?:[\w\-]+\.)+[\w\-]+(?:\:\d+)?/)(?:video|embedframe/|swf/xv-player\.swf\?id_video=)(\d+)@i', $link, $vid)) html_error('Invalid Link.');
		$domain = 'www.xvideos.com';
		$link = $GLOBALS['Referer'] = "http://$domain/video{$vid[2]}/";

		$page = $this->GetPage($link);
		if (preg_match("@\nLocation: ((https?://(?:[\w\-]+\.)+[\w\-]+(?:\:\d+)?)?/video{$vid[2]}/\S+)@i", $page, $redir)) {
			$redir = empty($redir[2]) ? "http://$domain".$redir[1] : $redir[1];
			$page = $this->GetPage($redir, GetCookiesArr($page));
		}

		if (!preg_match('@<h2>\s*([^"<>]+?)\s*<span[ >]@i', $page, $title)) {
			is_present($page, 'Sorry but the page you requested was not found.', 'Video not found or it was deleted.');
			is_present($page, 'We received a request to have this video deleted.', 'Video disabled for dispute.');
			html_error('Error: Video title not found.');
		}

		if (!preg_match('@(?<=flv_url=)https?[^\"\'&]+@', $page, $DL)) html_error('Error: Download link not found.');
		$DL = urldecode($DL[0]);

		if (!preg_match('@\.(?:mp4|flv|webm|avi)$@i', basename($DL), $ext)) $ext = array('.flv');
		$filename = preg_replace('@(?:\.(?:mp4|flv|mkv|webm|wmv|(m2)?ts|rm(vb)?|mpe?g?|vob|avi|[23]gp))+$@i', '', preg_replace('@[^ A-Za-z_\-\d\.,\(\)\[\]\{\}&\!\'\@\%\#]@u', '_', html_entity_decode(trim($title[1]), ENT_QUOTES, 'UTF-8')));
		$filename .= sprintf(' [xvideos][%d]%s', $vid[2], $ext[0]);

		$this->RedirectDownload($DL, $filename, 0, 0, 0, $filename);
	}
}

//[25-1-2016]  Written by Th3-822.

?>