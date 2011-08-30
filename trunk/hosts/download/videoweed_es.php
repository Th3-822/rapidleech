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
		if (!preg_match('@flashvars.domain="([^"]+)";\s+flashvars.file="([^"]+)";\s+flashvars.filekey="([^"]+)"@', $page, $matches)) html_error('Error: Download link not found.');
    
		$url = $matches[1] . '/api/player.api.php?user=undefined&codes=1&file=' . $matches[2] . '&pass=undefined&key=' . urlencode($matches[3]);
		$page2 = $this->GetPage($url); 
    
		if (!preg_match('@url=(http://[^&]+)@', $page2, $downl)) html_error('Error: Download link not found.');  

		if(!preg_match('@\.[^\.]+$@i', basename($downl[1]), $ext)) $ext = array('.flv');
		$fname = str_replace(Array ("\\", "/", ":", "*", "?", "\"", "<", ">", "|"), "_", html_entity_decode(trim($fname[1]))) . $ext[0];
		$this->RedirectDownload($downl[1], $fname, 0, 0, 0, $fname);
	}
}

//[29-7-2011]  Written by Th3-822.
//[21-8-2011]  Patched by Reetus.

?>