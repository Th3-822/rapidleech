<?php

if (!defined('RAPIDLEECH')) {
	require_once('index.html');
	exit;
}

class rapidu_net extends DownloadClass {
	private $page, $cookie = array(), $pA, $login, $DLRegexp = '@https?://\w+\d+\.rapiduservers\.net/download/[^\t\r\n\'\"<>]+@i';
	public function Download($link) {
		if (!preg_match('@(https?://rapidu.net/)(\d+)@i', str_ireplace('://www.rapidu.net', '://rapidu.net', $link), $fid)) html_error('Invalid link?.');
		$this->link = $GLOBALS['Referer'] = str_ireplace('http://', 'https://', $fid[0]);
		$this->baseurl = $fid[1];
		$this->fid = $fid[2];

		if (empty($_POST['step'])) {
			$this->page = $this->GetPage($this->link, $this->cookie);
			is_present($this->page, '404 - File not found', 'File Not Found.');
			$this->cookie = GetCookiesArr($this->page, $this->cookie);
		}

		$this->pA = (empty($_REQUEST['premium_user']) || empty($_REQUEST['premium_pass']) ? false : true);
		if (($_REQUEST['premium_acc'] == 'on' && ($this->pA || (!empty($GLOBALS['premium_acc']['rapidu_net']['user']) && !empty($GLOBALS['premium_acc']['rapidu_net']['pass']))))) {
			$user = ($this->pA ? $_REQUEST['premium_user'] : $GLOBALS['premium_acc']['rapidu_net']['user']);
			$pass = ($this->pA ? $_REQUEST['premium_pass'] : $GLOBALS['premium_acc']['rapidu_net']['pass']);
			if ($this->pA && !empty($_POST['pA_encrypted'])) {
				$user = decrypt(urldecode($user));
				$pass = decrypt(urldecode($pass));
				unset($_POST['pA_encrypted']);
			}
			return $this->Login($user, $pass);
		} else return $this->AnonDL();
	}

	private function AnonDL() {
		if (empty($_POST['step']) || $_POST['step'] != '1') {
			$page = $this->GetPage($this->baseurl . 'ajax.php?a=getLoadTimeToDownload', $this->cookie, '_go=', $this->link . "\r\nX-Requested-With: XMLHttpRequest");
			$this->cookie = GetCookiesArr($page, $this->cookie);
			$start_timer = $this->json2array($page, 'getLoadTimeToDownload Error');

			if (!isset($start_timer['timeToDownload']) || !is_numeric($start_timer['timeToDownload'])) {
				if (!empty($start_timer['timeToDownload']) && $start_timer['timeToDownload'] == 'stop') html_error('Your daily transfer limit has been reached');
				textarea($start_timer);
				html_error('Unknown error @ getLoadTimeToDownload');
			}
			$start_timer['timeToDownload'] -= time();

			$js = $this->GetPage($this->baseurl . 'js/global.engine.js', $this->cookie);
			if (!preg_match('@Recaptcha\.create\s*\(\s*\'([\w\.\-]+)\'@i', $js, $recaptcha)) html_error('Captcha Not Found.');

			if (!empty($start_timer['timeToDownload']) && $start_timer['timeToDownload'] > 0) {
				if ($start_timer['timeToDownload'] > 100) {
					return $this->JSCountdown($start_timer['timeToDownload'], $this->DefaultParamArr($this->link), 'AnonDL limit reached.');
				} else $this->CountDown($start_timer['timeToDownload']);
			}

			$data = $this->DefaultParamArr($this->link, $this->cookie, 1, true);
			$data['step'] = '1';
			return $this->reCAPTCHA($recaptcha[1], $data);
		} else {
			if (empty($_POST['recaptcha_response_field'])) html_error('You didn\'t enter the image verification code.');
			if (empty($_POST['recaptcha_challenge_field'])) html_error('Empty reCAPTCHA challenge.');
			$this->cookie = StrToCookies(decrypt(urldecode($_POST['cookie'])));

			$post = array();
			$post['captcha1'] = $_POST['recaptcha_challenge_field'];
			$post['captcha2'] = $_POST['recaptcha_response_field'];
			$post['fileId'] = $this->fid;
			$post['_go'] = '';

			$page = $this->GetPage($this->baseurl . 'ajax.php?a=getCheckCaptcha', $this->cookie, array_map('urlencode', $post));
			$chkCaptcha = $this->json2array($page, 'getCheckCaptcha Error');

			if (empty($chkCaptcha['message']) || $chkCaptcha['message'] != 'success') html_error('Wrong captcha entered.');
			if (empty($chkCaptcha['url'])) html_error('Download Link Not Found.');

			return $this->RedirectDownload($chkCaptcha['url'], urldecode(parse_url($chkCaptcha['url'], PHP_URL_PATH)));
		}
	}

	private function ApiDL() {
		$DL = $this->ApiReq('getFileDownload', array('id' => $this->fid));
		if (!empty($DL['message'])) {
			if (!empty($DL['message']['errorDateNextDownload'])) {
				$data = $this->DefaultParamArr($this->link);
				$data['premium_acc'] = 'on';
				if ($this->pA) {
					$data['pA_encrypted'] = 'true';
					$data['premium_user'] = urlencode($_REQUEST['premium_user']); // encrypt() will keep this safe.
					$data['premium_pass'] = urlencode($_REQUEST['premium_pass']); // And this too.
				}
				$waitTime = strtotime($DL['message']['errorDateNextDownload'] . ' UTC') - time();
				return $this->JSCountdown($waitTime, $data, 'FreeDL limit reached.');
			} else if ($DL['message']['error'] == 'errorAccountNotHaveDayTransfer') html_error('Your daily transfer limit has been reached.');
			else html_error('[ApiDL Error] ' . htmlspecialchars($DL['message']['error'], ENT_QUOTES));
		}

		if (empty($DL['fileLocation'])) html_error('Download-Link Not Found.');
		return $this->RedirectDownload($DL['fileLocation'], urldecode(parse_url($DL['fileLocation'], PHP_URL_PATH)));
	}

	private function Login($user, $pass) {
		$this->login = array('login' => $user, 'password' => $pass);
		$reply = $this->ApiReq('getAccountDetails');

		if (!empty($reply['message'])) html_error('[Login Error] ' . htmlspecialchars($reply['message']['error'], ENT_QUOTES));
		if (empty($reply['userPremium'])) $this->changeMesg('<br /><b>Account isn\'t premium</b>', true);

		return $this->ApiDL();
	}

	private function ApiReq($path, $post = array()) {
		if (!is_array($post)) $post = array();
		if ($path != 'getFileDetails') $post = array_merge($this->login, $post);

		$page = $this->GetPage("https://rapidu.net/api/$path/", 0, array_map('urlencode', $post), 'https://rapidu.net/');
		return $this->json2array($page, "ApiReq($path) Error");
	}
}

//[21-2-2016]  Written by Th3-822.

?>