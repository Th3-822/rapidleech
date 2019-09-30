<?php

if (!defined('RAPIDLEECH')) {
	require_once('index.html');
	exit();
}

class samsung_firmware_org extends DownloadClass {
	public function Download($link) {textarea($link);
		if (!preg_match('@(https://samsung-firmware\.org/)(?:\w+/)?(download/[\w\-\%() ]+/[\w\-\.]+/\w{3}/(?:\w+/){1,2})@i', $link, $_link)) html_error('Invalid Link?.');
		$link = $GLOBALS['Referer'] = $_link[1] . $_link[2];

		$pA = (empty($_REQUEST['premium_user']) || empty($_REQUEST['premium_pass']) ? false : true);
		if (($_REQUEST['premium_acc'] == 'on' && ($pA || (!empty($GLOBALS['premium_acc']['samsung_firmware_org']['user']) && !empty($GLOBALS['premium_acc']['samsung_firmware_org']['pass']))))) {
			$user = ($pA ? $_REQUEST['premium_user'] : $GLOBALS['premium_acc']['samsung_firmware_org']['user']);
			$pass = ($pA ? $_REQUEST['premium_pass'] : $GLOBALS['premium_acc']['samsung_firmware_org']['pass']);
		} else return html_error('This Site Requires Login to Download');

		$cookie = array('hl' => 'en');
		$purl = 'https://samsung-firmware.org/login';
		$page = $this->GetPage($purl, $cookie);
		if (!($csrf = trim(cut_str($page, 'name="_csrf_token" value="', '"')))) html_error('Login CSRF Token Not Found');
		$loginCookie = GetCookiesArr($page, $cookie);

		$post = array();
		$post['_csrf_token'] = html_entity_decode($csrf);
		$post['_username'] = $user;
		$post['_password'] = $pass;
		$post['_submit'] = 'Login';

		$page = $this->GetPage('https://samsung-firmware.org/login_check', $loginCookie, array_map('urlencode', $post), $purl);
		is_present($page, $purl, 'Invalid Login?');
		$cookie = GetCookiesArr($page, $cookie);
		if (empty($cookie['PHPSESSID'])) html_error('Login Cookie Not Found');

		$page = $this->GetPage($link, $cookie);

		if (!preg_match('@https://dl\.samsung-firmware\.org/\w+/\w+/[^\r\n\t\"\'<>]+@i', $page, $DL)) html_error('Download Link Not Found');

		$this->RedirectDownload($DL[0]);
	}
}

//[30-12-2016] Written by Th3-822.
//[01-4-2017] Updated Link Regexp. - Th3-822