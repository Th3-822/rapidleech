<?php

if (!defined('RAPIDLEECH')) {
	require_once ("index.html");
	exit;
}

class nowvideo_ch extends DownloadClass {
	public function Download($link) {
		$page = $this->GetPage($link);
		is_present($page, "This file no longer exists", 'Video not found or it was deleted.');
		is_present($page, "The file is being transfered", 'Video is temporarily unavailable.');

		if (!preg_match('@<h4>\s*([^"<>]+)\s*</h4>@i', $page, $fname)) html_error('Error: Video title not found.');
		if (!preg_match('@flashvars.domain="([^"]+)";\s+flashvars.file="([^"]+)";\s+flashvars.filekey=(?:"([^"]+)"|([\$_A-Za-z][\$\w]*))@', $page, $matches)) html_error('Error: Download link not found.');
		if (empty($matches[3])) {
			if (!preg_match('@var\s+'.$matches[4].'\s*=\s*"([^"]+)"\s*;@i', $page, $fkey)) html_error('FileKey not Found.');
			$matches[3] = $fkey[1];
		}

		$url = $matches[1] . '/api/player.api.php?user=undefined&codes=1&file=' . $matches[2] . '&pass=undefined&key=' . urlencode($matches[3]);
		$page2 = $this->GetPage($url); 
    
		if (!preg_match('@url=(http://[^&]+)@', $page2, $downl)) html_error('Error: Download link not found.');  

		if(!preg_match('@\.[^\.]+$@i', basename($downl[1]), $ext)) $ext = array('.flv');
		$fname = preg_replace('@\.(mp4|flv|mkv|webm|wmv|(m2)?ts|rm(vb)?|mpe?g?|vob|avi|[23]gp)$@i', '', str_replace(Array ("\\", "/", ":", "*", "?", "\"", "<", ">", "|"), "_", html_entity_decode(trim($fname[1])))) . $ext[0];
		$this->RedirectDownload($downl[1], $fname, 0, 0, 0, $fname);
	}
}

//[26-8-2015]  Written by Th3-822.

?>