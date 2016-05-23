<?php

if (!defined('RAPIDLEECH')) {
	require_once('index.html');
	exit;
}

class trainbit_com extends DownloadClass {
	private $page, $DLRegexp = '@https?://tb\d+\.trainbit\.com(?::\d+)?/files/\d+/\w+/[^\t\r\n\'\"<>]*@i';
	public function Download($link) {
		if (!preg_match('@https?://trainbit\.com/files/(\d+)@i', str_ireplace('://www.', '://', $link), $fid)) html_error('Invalid link?.');
		$this->link = $GLOBALS['Referer'] = $fid[0] . '/';
		$this->fid = $fid[1];

		$this->page = $this->GetPage($this->link);
		is_present($this->page, 'Desired file is removed.', 'File Not Found.');

		return $this->FreeDL();
	}

	private function FreeDL() {
		$post = array();
		$post['__VIEWSTATE'] = urlencode(cut_str($this->page, '="__VIEWSTATE" value="', '"')) or html_error('__VIEWSTATE Token Not Found.');
		$post['__VIEWSTATEGENERATOR'] = urlencode(cut_str($this->page, '="__VIEWSTATEGENERATOR" value="', '"')) or html_error('__VIEWSTATEGENERATOR Token Not Found.');
		$post['__EVENTVALIDATION'] = urlencode(cut_str($this->page, '="__EVENTVALIDATION" value="', '"')) or html_error('__EVENTVALIDATION Token Not Found.');
		$post['btnDownload'] = 'Get%20Link';

		/* Longer Regex: '@<div\s+id="indicator">(?>.*?<h5[^>]+?>)\s*(\d+)\s*</h5>(?>.*?</div>)@si' */
		if (!preg_match('@<h5(?:\s[^>]+)?>\s*(\d+)\s*</h5>@i', $this->page, $count)) html_error('Countdown Not Found.');
		if ($count[1] > 0) $this->CountDown($count[1] + 2);

		$page = $this->GetPage($this->link, 0, $post);

		if (!preg_match($this->DLRegexp, $page, $DL)) {
			textarea($this->page);
			textarea($page);
			html_error('Download-Link Not Found.');
		}
		return $this->RedirectDownload($DL[0], 'trainbit_placeholder_name');
	}
}

// [14-5-2016] Written by Th3-822.

?>