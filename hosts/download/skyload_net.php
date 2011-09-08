<?php

if (!defined('RAPIDLEECH')) {
	require_once ("index.html");
	exit;
}

class skyload_net extends DownloadClass {
	public function Download($link) {
		$page = $this->GetPage($link);
		is_present($page, "Datei wurde nicht gefunden!", 'File not found.');

		if (preg_match("@http://(www\.)?youtube\.com/watch\?v\=[^'|\"|\&]+@i", $page, $ytl)) html_error('Watch: '.$ytl[0]);

		if (!preg_match("@file=(http://[^'|\"|\r|\n]+)@i", $page, $downl)) html_error('Error: Download link not found.');
		if (!preg_match('@Sie sehen hier:</strong>([^<|\r|\n]+)@i', $page, $fname)) html_error('Error: Video title not found.');

		if(!preg_match('@\.[^\.]+$@i', basename($downl[1]), $ext)) $ext = array('.flv');
		$fname = str_replace(Array ("\\", "/", ":", "*", "?", "\"", "<", ">", "|"), "_", urldecode(trim($fname[1]))) . $ext[0];
		$this->RedirectDownload($downl[1], $fname, 0, 0, 0, $fname);
	}
}

//[14-8-2011]  Written by Th3-822.

?>