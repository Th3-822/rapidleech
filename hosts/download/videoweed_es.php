<?php

if (!defined('RAPIDLEECH')) {
	require_once('index.html');
	exit;
}

class videoweed_es extends DownloadClass {
	public function Download($link) {
		$page = $this->GetPage($link);
		is_present($page, 'This file no longer exists', 'Video not found or it was deleted.');
		is_present($page, 'The file is being transfered', 'Video is temporarily unavailable.');

		if (($stepkey = cut_str($page, '"stepkey" value="', '"'))) {
			$post = array('stepkey' => $stepkey, 'submit' => 'submit');
			$cookie = GetCookiesArr($page);
			$page = $this->GetPage($link, $cookie, $post);
			is_present($page, 'This file no longer exists', 'Video not found or it was deleted..');
			is_present($page, 'The file is being transfered', 'Video is temporarily unavailable..');
		}

		if (!preg_match('@<h1 class="text_shadow">\s*([^"<>]+)\s*</h1>@i', $page, $title)) html_error('Error: Video title not found.');
		if (!preg_match('@flashvars.domain\s*=\s*"([^"]+)";\s+flashvars.file\s*=\s*"([^"]+)";\s+flashvars.filekey\s*=\s*(?:"([^"]+)"|([\$_A-Za-z][\$\w]*))@', $page, $matches)) html_error('Error: Download link not found.');
		if (empty($matches[3])) {
			if (!preg_match('@var\s+'.$matches[4].'\s*=\s*"([^"]+)"\s*;@i', $page, $fkey)) html_error('FileKey not Found.');
			$matches[3] = $fkey[1];
		}

		$url = $matches[1] . '/api/player.api.php?user=undefined&codes=1&file=' . $matches[2] . '&pass=undefined&key=' . urlencode($matches[3]);
		$page2 = $this->GetPage($url);
		is_present($page2, ' is being transfered', 'Video is temporarily unavailable');
    
		if (!preg_match('@url=(http://[^&]+)@', $page2, $downl)) html_error('Error: Download link not found.');  

		if (!preg_match('@\.(?:mp4|flv|webm|avi)$@i', basename($downl[1]), $ext)) $ext = array('.flv');
		$filename = preg_replace('@(?:\.(?:mp4|flv|mkv|webm|wmv|(m2)?ts|rm(vb)?|mpe?g?|vob|avi|[23]gp))+$@i', '', preg_replace('@[^ A-Za-z_\-\d\.,\(\)\[\]\{\}&\!\'\@\%\#]@u', '_', html_entity_decode(trim($title[1]), ENT_QUOTES, 'UTF-8')));
		$filename .= sprintf(' [VideoWeed][%s]%s', $matches[2], $ext[0]);

		$this->RedirectDownload($downl[1], $filename, 0, 0, 0, $filename);
	}
}

//[28-12-2015]  ReWritten by Th3-822.

?>