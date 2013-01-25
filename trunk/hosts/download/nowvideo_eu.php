<?php
if (!defined('RAPIDLEECH')) {
	require_once 'index.html';
	exit;
}

class nowvideo_eu extends DownloadClass {
	
	public function Download($link) {
		
		$page = $this->GetPage($link);
		is_present($page, 'This file no longer exists on our servers.');
		$domain = cut_str($page, 'flashvars.domain="', '"');
		$file = cut_str($page, 'flashvars.file="', '"');
		$key = cut_str($page, 'flashvars.filekey="', '"');
		$code = cut_str($page, 'flashvars.cid="', '"');
		$page = $this->GetPage("$domain/api/player.api.php?pass=undefined&file=$file/&user=undefined&key=".urlencode($key)."&codes=$code", 0, 0, $domain."/player/nowvideo.swf");
		$dlink = cut_str(urldecode($page), 'url=', '&');
		$filename = cut_str(urldecode($page), 'title=', '&');
		if (empty ($dlink) || empty($filename)) html_error("Error[Download link : {$dlink} or Filename : {$filename} is empty!]");
		$this->RedirectDownload($dlink, $filename, 0, 0, $domain."/player/nowvideo.swf");
		exit;
		
	}
}

/*
 * Written by Tony Fauzi Wihana/Ruud v.Tony 22/01/2013
 */
?>
