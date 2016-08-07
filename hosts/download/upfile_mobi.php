<?php

if (!defined('RAPIDLEECH')) {
	require_once('index.html');
	exit;
}

class upfile_mobi extends DownloadClass {
	public function Download($link) {
		if (!preg_match('@https?://upfile\.mobi/([\w\-]+)(?:\.([\dA-Fa-f]{32}))?@i', str_ireplace(array('://www.', 'index.php', '?page=file&f='), array('://'), $link), $fid)) html_error('Invalid link?.');
		$link = explode('|', str_ireplace('%7C', '|', $link), 2);
		if (count($link) > 1) $lpass = md5(rawurldecode($link[1]));
		$link = $GLOBALS['Referer'] = !empty($lpass) ? (empty($fid[2]) ? "{$fid[0]}.$lpass" : str_replace(".{$fid[2]}", ".$lpass", $fid[0])) : $fid[0];
		$fid = $fid[1];

		$page = $this->GetPage($link);
		is_present($page, 'File does not exist', 'File Not Found.');
		is_present($page, 'Enter password:', 'Incorrect Link Password.');

		if (!preg_match("@(https?://upfile\.mobi)?/(?:download/)?(?:index\.php)?\?page=download&server=\d+&f=$fid&[^\'\"\t<>\r\n]+@i", $page, $RD)) html_error('Redirect-Link Not Found.');
		$RD = (empty($RD[1])) ? parse_url($link, PHP_URL_SCHEME).'://upfile.mobi'.$RD[0] : $RD[0];
		$page = $this->GetPage(str_ireplace('/download/', '/', $RD));

		// Fix Bad Link Formatting.
		$page = preg_replace('@(https?://(?:[\w\-]+\.)+[\w\-]+(?:\:\d+)?)([^/])@i', '$1/$2', $page);

		if (!preg_match("@https?://(?:[\w\-]+\.)+[\w\-]+(?:\:\d+)?/(?:index\.php)?\?page=download_start&server=\d+&f=$fid&[^\'\"\t<>\r\n]+@i", $page, $DL)) html_error('Download-Link Not Found.');
		return $this->RedirectDownload($DL[0], 'upfile_mobi-placeholder-name');
	}
}

// [18-5-2016] Written by Th3-822.
// [04-8-2016] Fixed Link & Download Regexp. - Th3-822

?>