<?php

if (!defined('RAPIDLEECH')) {
	require_once('index.html');
	exit;
}

class d4sync_com extends DownloadClass {
	public function Download($link) {
		if (!preg_match('@(https?://www\.4sync\.com)/file/([\w\-\.]+)/@i', preg_replace('@://(?:[\w\-]+\.)*4sync\.com/\w+/@i', '://www.4sync.com/file/', $link, 1), $fid)) html_error('Invalid link?.'); // [^/\"\'\s<>?]+
		$link = $GLOBALS['Referer'] = $fid[0];
		$fid = empty($fid[3]) ? $fid[2] : $fid[3];

		$DL_regexp = '@https?://dc\d+\.4sync\.com/download/[^/\"\'\s<>]+/[^/\"\'\s<>]+@i';
		$cookie = array('4langcookie' => 'en');

		$page = $this->GetPage($link, $cookie);
		if (!preg_match($DL_regexp, $page, $DL)) html_error('Download-Link Not Found.');

		return $this->RedirectDownload($DL[0], '4sync_placeholder');
	}
}

// [21-1-2017] Written by Th3-822