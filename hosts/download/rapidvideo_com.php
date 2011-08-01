<?php

if (!defined('RAPIDLEECH')) {
	require_once ("index.html");
	exit;
}

class rapidvideo_com extends DownloadClass {
	public function Download($link) {
		$page = $this->GetPage($link);
		is_present($page, "Die angefordete Video wurde nicht gefunden", 'Video not found.');

		if (!preg_match('@"og:title" content="([^"]+)"@i', $page, $fname)) html_error('Error: Video title not found.');
		if (!preg_match("@'file','([^']+)'@i", $page, $downl)) html_error('Error: Download link not found.');

		if(!preg_match('@\.[^\.]+$@i', basename($downl[1]), $ext)) $ext = array('.mp4');
		$fname = str_replace(Array ("\\", "/", ":", "*", "?", "\"", "<", ">", "|"), "_", html_entity_decode(trim($fname[1]))) . $ext[0];
		$this->RedirectDownload($downl[1], $fname, 0, 0, 0, $fname);
	}
}

//[29-7-2011]  Written by Th3-822.

?>