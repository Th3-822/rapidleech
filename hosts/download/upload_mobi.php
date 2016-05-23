<?php

if (!defined('RAPIDLEECH')) {
	require_once('index.html');
	exit;
}

class upload_mobi extends DownloadClass {
	public function Download($link) {
		if (!preg_match('@https?://upload\.mobi/(?:(\d+)/(\w+)|(?:index\.php)?\?page=file&id=(\d+)&code=(\w+))@i', str_ireplace('://www.', '://', $link), $_fid)) html_error('Invalid link?.');
		$link = $GLOBALS['Referer'] = $_fid[0];
		$fid = !empty($_fid[3]) ? $_fid[3] : $_fid[1];
		$fcode = !empty($_fid[4]) ? $_fid[4] : $_fid[2];

		$page = $this->GetPage($link);
		is_present($page, 'File does not exist', 'File Not Found.');

		if (!preg_match("@https?://(?:[\w\-]+\.)+[\w\-]+(?:\:\d+)?/download/$fid/$fcode/\w+@i", $page, $DL)) html_error('Download-Link Not Found.');
		return $this->RedirectDownload($DL[0], 'upload_mobi-placeholder-name');
	}
}

// [18-5-2016] Written by Th3-822.

?>