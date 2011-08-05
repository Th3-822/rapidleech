<?php

if (!defined('RAPIDLEECH')) {
	require_once ("index.html");
	exit;
}

class movshare_net extends DownloadClass {
	public function Download($link) {
		$page = $this->GetPage($link, 0, array("submit.x"=>87,"submit.y"=>33));
		is_present($page, "This file no longer exists", 'Video not found or it was deleted.');
		is_present($page, "The file is being transfered", 'Video is temporarily unavailable.');

		if (!preg_match('@"/share\.php\?id=(\w+)\&title=([^"]+)"@i', $page, $fname)) html_error('Error: Video data not found.');
		//@type="video/divx" src="([^\'|\"|\r|\n|<|>]+)@i
		if (!preg_match('@=(?:"|\')(http://[^/|\'|\"|\r|\n]+/dl/\w+/\w+/([^\'|\"|\r|\n|<|>]+))(?:"|\')@i', $page, $downl)) html_error('Error: Download link not found.');

		if(!preg_match('@\.[^\.]+$@i', basename($downl[1]), $ext)) $ext = array('.avi');
		$fname = str_replace(Array ("\\", "/", ":", "*", "?", "\"", "<", ">", "|"), "_", html_entity_decode(trim($fname[2]))) . " [{$fname[1]}]" . $ext[0];
		$this->RedirectDownload($downl[1], $fname, 0, 0, 0, $fname);
	}
}

//[29-7-2011]  Written by Th3-822.
//[01-8-2011]  Fixed regexp...

?>