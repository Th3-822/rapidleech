<?php

if (!defined('RAPIDLEECH')) {
	require_once ("index.html");
	exit;
}

class divxstage_eu extends DownloadClass {
	public function Download($link) {
		$page = $this->GetPage($link);
		is_present($page, "This file no longer exists", 'Video not found or it was deleted.');
		is_present($page, "The file is being transfered", 'Video is temporarily unavailable.');

		if (!preg_match('@"/share\.php\?id=(\w+)\&title=([^"]+)"@i', $page, $fname)) html_error('Error: Video data not found.');
		if (!preg_match('@type="video/divx" src="([^\'|\"|\r|\n|<|>]+)@i', $page, $downl)) html_error('Error: Download link not found.');

		if(!preg_match('@\.[^\.]+$@i', basename($downl[1]), $ext)) $ext = array('.avi');
		$fname = str_replace(Array ("\\", "/", ":", "*", "?", "\"", "<", ">", "|"), "_", html_entity_decode(trim($fname[2]))) . " [{$fname[1]}]" . $ext[0];
		$this->RedirectDownload($downl[1], $fname, 0, 0, 0, $fname);
	}
}

//[29-7-2011]  Written by Th3-822.

?>