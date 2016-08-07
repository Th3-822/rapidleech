<?php

if (!defined('RAPIDLEECH')) {
	require_once('index.html');
	exit;
}

class my_pcloud_com extends DownloadClass {
	public function Download($link) {
		if (!preg_match('@https?://my\.pcloud\.com/publink/show\?code=\w+@i', $link, $_link)) html_error('Invalid link?.');
		$link = $GLOBALS['Referer'] = $_link[0];

		if (!function_exists('json_decode')) html_error('Error: Please enable JSON in php.');

		$page = $this->GetPage($link);
		if (!preg_match('@var\s+publinkData\s*=\s*(\{.+?\})\s*;@s', $page, $data)) html_error('Filedata Not Found.');
		$data = json_decode($data[1], true);
		if ($data === NULL) html_error('Error while parsing Filedata JSON.');

		if (!empty($data['result'])) {
			$data['result'] = htmlspecialchars($data['result'], ENT_QUOTES);
			if (!empty($data['error'])) {
				$data['error'] = htmlspecialchars($data['error'], ENT_QUOTES);
				html_error("[Error {$data['result']}] File Error: {$data['error']}");
			}
			html_error('Unknown File Error: ' . $data['result']);
		}

		if (empty($data['downloadlink'])) html_error('Download-Link Not Found.');
		return $this->RedirectDownload($data['downloadlink'], 'my_pcloud_com_placeholder');
	}
}

// [08-7-2016] Written by Th3-822.

?>