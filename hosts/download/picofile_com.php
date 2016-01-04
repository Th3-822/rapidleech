<?php

if (!defined('RAPIDLEECH')) {
	require_once('index.html');
	exit();
}

class picofile_com extends DownloadClass {
	public function Download($link) {
		$link = explode('|', str_ireplace('%7C', '|', $link), 2);
		$lpass = (count($link) > 1 ? rawurldecode($link[1]) : '');
		$link = $GLOBALS['Referer'] = $link[0];

		$page = $this->GetPage($link);
		$cookie = GetCookiesArr($page);
		if (substr($page, 9, 3) == '404') html_error('File Not Found.');
		if (!preg_match('@file/GenerateDownloadLink\?fileId=\d+@i', $page, $ajaxurl)) html_error('FileID Not Found.');
		$ajaxurl = $ajaxurl[0];

		$url = parse_url($link);
		$page = $this->GetPage(sprintf('%s://%s/%s', $url['scheme'], $url['host'], $ajaxurl), $cookie, array('password' => urlencode($lpass)), "$link\r\nX-Requested-With: XMLHttpRequest");
		$cookie = GetCookiesArr($page, $cookie);

		switch (intval(substr($page, 9, 3))) {
			case 200: break;
			case 404: html_error('File Not Found');break;
			case 403: html_error('Wrong File Password, Please add password on the format: link|password');break;
			default: html_error('Error While Requesting Download Link.');
		}

		if (!preg_match('@https?://(?:[\w\-]+\.)+[\w\-]+(?:\:\d+)?/d/[\w\-]+/[^\'\"\t<>\r\n\\\]+@i', $page, $DL)) html_error('Download Link Not Found.');
		$this->RedirectDownload($DL[0], urldecode(basename(parse_url($DL[0], PHP_URL_PATH))), $cookie);
	}
}

// [30-12-2015] Written by Th3-822.

?>