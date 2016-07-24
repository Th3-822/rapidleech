<?php

if (!defined('RAPIDLEECH')) {
	require_once('index.html');
	exit;
}


class solidfiles_com extends DownloadClass {

	public function Download($link) {
		
		echo 'Solidfiles.com Download Plugin by <b>The Devil</b>';
		$this->page = $this->GetPage($link);
		is_present($this->page,'The page you were looking for appears to no longer be there','File Unavailable');
		$devil = preg_match('~"download_url":"(.*)"~U',$this->page,$dlocs);
		(!$devil)?html_error('[0]Error: Unable to Find Download Location'):'';
		is_notpresent($dlocs[1],'solidfilesusercontent.com','[1]Error: Check FileHost CDN');
		return $this->RedirectDownload($dlocs[1], urldecode(basename(parse_url($dlocs[1], PHP_URL_PATH))));
	}
	
}

// Written by The Devil
// [2016-06-09] Code Clean Up

?>