<?php

if (!defined('RAPIDLEECH')) {
	require_once('index.html');
	exit;
}

class littlebyte_net extends DownloadClass {
	private $page, $cookie = array('lng' => 'EN'), $link, $baseUrl, $fid, $fuid;
	public function Download($link) {
		if (!preg_match('@(http://littlebyte\.net/)download/(\d+)\.(\w+)/[^/\r\n\"\'<>]+\.html@i', $link, $fid)) html_error('Invalid Link?.');
		$this->link = $GLOBALS['Referer'] = $fid[0];
		$this->baseUrl = $fid[1];
		$this->fid = $fid[2];
		$this->fuid = $fid[3];

		$this->page = $this->GetPage($this->link, $this->cookie);
		is_present($this->page, 'There are no files to be shown', 'File Not Found or Deleted.');
		$this->cookie = GetCookiesArr($this->page, $this->cookie);

		// Only FreeDL at the Moment.
		return $this->FreeDL();
	}

	private function FreeDL() {
		$post = array(
			'action' => 'getSlowDownload',
			'params%5Bfile_id%5D' => $this->fid,
			'params%5Bfile_uid%5D' => $this->fuid,
		);
		$page = $this->GetPage($this->baseUrl . 'ajax.php', $this->cookie, $post);
		if (preg_match('@Next free download will be available for you after \d+ minutes@', $page, $err)) html_error("FreeDL Error: {$err[0]}");

		$post = array('action' => 'checkCaptcha');
		if (!preg_match('@\s*(captchaId)\s*:\s*(\d+)\s*,\s*(captchaHash)\s*:\s*\'(\w+)\'@', $page, $captchaData)) html_error('CAPTCHA Data Not Found.');
		$post[$captchaData[1]] = $captchaData[2];
		$post[$captchaData[3]] = $captchaData[4];
		if (preg_match('@\.html(\'<h1>(\d{4})</h1>\');@i', $page, $captcha)) {
			$captcha = $captcha[1];
		} else if (preg_match('@://((?:[\w\-]+\.)+[\w\-]+(?:\:\d+)?)/(?:(?:frame)?video|embed)/([\w\.\-]+)@i', $page, $captcha)) {
			$video = $this->GetPage("http://{$captcha[1]}/video/{$captcha[2]}");
			if (preg_match("@\nLocation: https?://((?:[\w\-]+\.)+[\w\-]+(?:\:\d+)?)/video/({$captcha[2]})@i", $video, $captcha)) {
				$video = $this->GetPage("http://{$captcha[1]}/video/{$captcha[2]}");
			}
			if (!preg_match('@[\s\,]title\s*:\s*"([^"]+)"\s*,@i', $video, $captcha) || !preg_match('@\b(\d{4})\b@', preg_replace('@\D@', ' ', $captcha[1]), $captcha)) html_error('Video "CAPTCHA" Response Not Found.');
			$captcha = $captcha[1];
		} else html_error('Video "CAPTCHA" Not Found.');
		$post['captcha'] = $captcha;

		//if (!preg_match('@[\s,]freeRemind\s*=\s*(\d+)\s*;@i', $page, $cD)) html_error('Countdown Not Found.');
		//if ($cD[1] > 0) $this->Countdown($cD[1] + 2);

		$page = $this->GetPage($this->baseUrl . 'ajax.php', $this->cookie, $post);
		$reply = $this->json2array($page, 'Error At FreeDL Post');
		if (empty($reply['status'])) html_error('Unexpected Response At FreeDL.');

		if ($reply['status'] != 'OK') {
			if ($reply['status'] == 'ERROR') {
				if (array_key_exists('content', $reply)) {
					if (is_null($reply['content'])) html_error('Invalid "CAPTCHA" Response"');
					else textarea($reply['content']);
				}
				html_error('Unknown FreeDL Error.');
			} else if ($reply['status'] == 'RELOAD') html_error('FreeDL Limit Reached.');
			else html_error('Unknown FreeDL Error: ' . htmlspecialchars($reply['status']));
		} else if (empty($reply['content']) || !preg_match('@https?://cdn\d+.littlebyte\.net/files/[^\r\n\t\"\'<>]+@i', $reply['content'], $DL)) html_error('Download Link Not Found.');

		return $this->RedirectDownload($DL[0]);
	}
}

// [31-12-2016] Written by Th3-822.

?>