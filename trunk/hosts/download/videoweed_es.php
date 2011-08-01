<?php

if (!defined('RAPIDLEECH')) {
	require_once ("index.html");
	exit;
}

class videoweed_es extends DownloadClass {
	public function Download($link) {
		$page = $this->GetPage($link);
		is_present($page, "This file no longer exists", 'Video not found or it was deleted.');
		is_present($page, "The file is being transfered", 'Video is temporarily unavailable.');

		if (!preg_match('@name="title" content="([^"]+)"@i', $page, $fname)) html_error('Error: Video title not found.');
		if (!preg_match('@http://\w+\.videoweed\.es/dl/\w+/\w+/[^\'|\"|\r|\n|<|>]+@i', $page, $downl)) html_error('Error: Download link not found.');

		if(!preg_match('@\.[^\.]+$@i', basename($downl[0]), $ext)) $ext = array('.flv');
		$fname = str_replace(Array ("\\", "/", ":", "*", "?", "\"", "<", ">", "|"), "_", html_entity_decode(trim($fname[1]))) . $ext[0];
		$this->RedirectDownload($downl[0], $fname, 0, 0, 0, $fname);
	}
}

//[29-7-2011]  Written by Th3-822.

?>