<?php

if (!defined('RAPIDLEECH')) {
	require_once('index.html');
	exit;
}

class cloud_mail_ru extends DownloadClass {
	public function Download($link) {
		if (!preg_match('@https?://cloud\.mail\.ru/public/(\w+)/[^<>\s\"\']+@i', $link, $fid)) html_error('Invalid Link.');
		$link = $GLOBALS['Referer'] = $fid[0];
		$fid = $fid[1];

		if (!function_exists('json_decode')) html_error('Error: Please enable JSON in php.');

		$page = $this->GetPage($link);
		is_present($page, 'Возможно, указан неправильный адрес страницы.', 'File Not Found.');

		if (!preg_match('@https?://\w+\.cldmail\.ru/\w+/\w+@i', $page, $dServer)) html_error('Error: Download Server not Found.');
		if (!preg_match('@"(?:uri|query_string)"\s*:\s*"(?:/?public)?(/' . $fid . '/[^\s\"]+)"@i', $page, $dPath)) html_error('Error: Download Path not Found.');

		if (preg_match('@"download"\s*:\s*"(\w+)"@i', $page, $dToken)) $token = $dToken[1];
		else {
			$post = array('api' => 2);
			if (!preg_match('@"BUILD"\s*:\s*"([\w\-\.]+)"@', $page, $dummy)) html_error('Error: Build Code not Found.');
			$post['build'] = urlencode($dummy[1]);
			if (!preg_match('@"x-page-id"\s*:\s*"(\w+)"@', $page, $dummy)) html_error('Error: Page ID not Found.');
			$post['x-page-id'] = urlencode($dummy[1]);

			list($headers, $body) = explode("\r\n\r\n", $this->GetPage('https://cloud.mail.ru/api/v2/tokens/download', 0, $post, "$link\r\nX-Requested-With: XMLHttpRequest"));
			$data = json_decode($body, true);
			if ($data === NULL) html_error('Error while parsing Download Token JSON.');

			if (empty($data['status']) || $data['status'] != 200) html_error('Error: Failed Download Token Request.');
			if (empty($data['body']['token'])) html_error('Error: Download Token not Found.');
			$token = $data['body']['token'];
		}

		$DL = $dServer[0] . $dPath[1] . '?key=' . urlencode($token);

		$this->RedirectDownload($DL, basename(urldecode(parse_url($DL, PHP_URL_PATH))));
	}
}

//[17-7-2016]  Written by Th3-822.

?>