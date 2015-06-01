<?php

if (!defined('RAPIDLEECH')) {
	require_once ("index.html");
	exit;
}

class movshare_net extends DownloadClass {
	public function Download($link) {
		$page = $this->GetPage($link);
		is_present($page, "This file no longer exists", 'Video not found or it was deleted.');
		is_present($page, "The file is being transfered", 'Video is temporarily unavailable.');

		if (!preg_match('@\.file="(\w+)"@i', $page, $fid)) html_error('Error: Videoid not found.');
		if (!preg_match('@\.filekey="([^"]+)"@i', $page, $fkey)) html_error('Error: Filekey not found.');
		if (!preg_match('@"/share\.php\?id=\w+/?\&title=([^"]+)"@i', $page, $fname)) html_error('Error: Video title not found.');

		$page = $this->GetPage("http://www.movshare.net/api/player.api.php?user=undefined&codes=1&file={$fid[1]}&pass=undefined&key={$fkey[1]}");
		if (!preg_match('@url=(http://[^\&|\r|\n]+)@i', $page, $downl)) html_error('Error: Download link not found.');

		if(!preg_match('@\.[^\.]+$@i', basename($downl[1]), $ext)) $ext = array('.avi');
		$fname = str_replace(Array ("\\", "/", ":", "*", "?", "\"", "<", ">", "|"), "_", urldecode(trim($fname[1]))) . " [{$fid[1]}]" . $ext[0];
		$this->RedirectDownload($downl[1], $fname, 0, 0, 0, $fname);
	}
}

//[29-7-2011]  Written by Th3-822.
//[25-1-2012]  Fixed Filename regexp. -Th3-822

?>