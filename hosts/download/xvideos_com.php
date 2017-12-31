<?php

if (!defined('RAPIDLEECH')) {
	require_once('index.html');
	exit;
}

class xvideos_com extends DownloadClass {
	public function Download($link) {
		if (!preg_match('@https?://(?:[\w\-]+\.)+[\w\-]+(?:\:\d+)?/(?:(?:video|embedframe/|swf/xv-player\.swf\?id_video=)(\d+)|video-(\w+)/)@i', $link, $vid)) html_error('Invalid Link.');
		$domain = strtolower(parse_url($link, PHP_URL_HOST));
		if (strpos($domain, 'www.') !== 0) $domain = "www.$domain";
		$link = $GLOBALS['Referer'] = "http://$domain/video" . (empty($vid[2]) ? $vid[1] : '-' . $vid[2]) . "/";

		$page = $this->GetPage($link);
		if (preg_match("@\nLocation: ((https?://(?:[\w\-]+\.)+[\w\-]+(?:\:\d+)?)?/video" . (empty($vid[2]) ? $vid[1] : '-' . $vid[2]) . "/\S+)@i", $page, $redir)) {
			$redir = empty($redir[2]) ? "http://$domain".$redir[1] : $redir[1];
			$page = $this->GetPage($redir, GetCookiesArr($page));
		}

		if (!preg_match('@setVideoTitle\(\'(.+?)\'\)@', $page, $title)) {
			is_present($page, 'Sorry but the page you requested was not found.', 'Video not found or it was deleted.');
			is_present($page, 'We received a request to have this video deleted.', 'Video disabled for dispute.');
			html_error('Error: Video title not found.');
		}

		if (!preg_match('@setVideoUrlHigh\(["\'](https?://[^"\'\s<>]+)@i', $page, $DL) && !preg_match('@setVideoUrl(?:Low)?\(["\'](https?://[^"\'\s<>]+)@i', $page, $DL) && !(preg_match('@(?<=flv_url=)(https?[^\"\'&]+)@', $page, $DL) && ($DL[1] = urldecode($DL[1])))) html_error('Error: Download link not found.');

		if (empty($vid[1])) $vid[1] = (preg_match('@id_video=(\d+)@i', $page, $_vid) ? $_vid[1] : -1);

		if (!preg_match('@\.(?:mp4|flv|webm|avi)$@i', basename(parse_url($DL[1], PHP_URL_PATH)), $ext)) $ext = array('.mp4');
		$filename = preg_replace('@(?:\.(?:mp4|flv|mkv|webm|wmv|(m2)?ts|rm(vb)?|mpe?g?|vob|avi|[23]gp))+$@i', '', preg_replace('@[^ A-Za-z_\-\d\.,\(\)\[\]\{\}&\!\'\@\%\#]@u', '_', stripslashes(html_entity_decode(trim($title[1]), ENT_QUOTES, 'UTF-8'))));
		$filename .= sprintf(' [xvideos][%d]%s', $vid[1], $ext[0]);

		$this->RedirectDownload($DL[1], $filename, 0, 0, 0, $filename);
	}
}

//[25-1-2016]  Written by Th3-822.
//[30-12-2017] Updated. - Th3-822