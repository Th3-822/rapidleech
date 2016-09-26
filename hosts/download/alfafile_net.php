<?php

if (!defined('RAPIDLEECH')) {
	require_once('index.html');
	exit;
}

class alfafile_net extends DownloadClass {
	private $page, $cookie = array('lang' => 'en'), $pA, $login, $DLRegexp = '@https?://a\d+\.alfafile\.net/dl/\w+/[^\t\r\n\'\"<>]+@i';
	public function Download($link) {
		if (!preg_match('@(https?://alfafile\.net)/file/(\w+)@i', str_ireplace('://www.alfafile.net', '://alfafile.net', $link), $fid)) html_error('Invalid link?.');
		$this->link = str_ireplace('http://', 'https://', $fid[0]);
		$this->baseurl = $fid[1];
		$this->fid = $fid[2];

		if (empty($_POST['step'])) {
			$this->page = $this->GetPage($this->link, $this->cookie);
			if (substr($this->page, 9, 3) == '404') html_error('File Not Found.');
			$this->cookie = GetCookiesArr($this->page, $this->cookie);
		}

		$this->pA = (empty($_REQUEST['premium_user']) || empty($_REQUEST['premium_pass']) ? false : true);
		if (($_REQUEST['premium_acc'] == 'on' && ($this->pA || (!empty($GLOBALS['premium_acc']['alfafile_net']['user']) && !empty($GLOBALS['premium_acc']['alfafile_net']['pass']))))) {
			$user = ($this->pA ? $_REQUEST['premium_user'] : $GLOBALS['premium_acc']['alfafile_net']['user']);
			$pass = ($this->pA ? $_REQUEST['premium_pass'] : $GLOBALS['premium_acc']['alfafile_net']['pass']);
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
			$page = $this->GetPage($this->baseurl . '/download/start_timer/' . $this->fid, $this->cookie, 0, $this->link . "\r\nX-Requested-With: XMLHttpRequest");
			$this->cookie = GetCookiesArr($page, $this->cookie);
			$start_timer = $this->json2array($page, 'StartTimer Error');

			if (empty($start_timer) || (empty($start_timer['show_timer']) && empty($start_timer['redirect_url']))) {
				if (!empty($start_timer['html'])) {
					if (preg_match('@Delay between downloads must be not less than \d+ minutes.\s+Try again in \d+ minutes@i', $page, $err)) html_error("[AnonDL] {$err[0]}");
					html_error('Error @ StartTimer: ' . htmlspecialchars(strip_tags($start_timer['html']), ENT_QUOTES));
				} else { //  if (substr($page, 9, 3) == '200')
					textarea($start_timer);
					html_error('Unknown error @ StartTimer');
				}
			}
			if (strpos($start_timer['redirect_url'], '://') === false) $start_timer['redirect_url'] = $this->baseurl . $start_timer['redirect_url'];

			if (!empty($start_timer['show_timer']) && $start_timer['timer'] > 0) $this->CountDown($start_timer['timer']);

			$page = $this->GetPage($start_timer['redirect_url'], $this->cookie, 0, $this->link);
			$this->cookie = GetCookiesArr($page, $this->cookie);

			if (!preg_match('@https?://api(?:-secure)?\.solvemedia\.com/papi/challenge\.(?:no)?script\?k=([\w\-\.]+)@i', $page, $smKey)) {
				if (preg_match($this->DLRegexp, $page, $DL)) return $this->RedirectDownload($DL[0], urldecode(parse_url($DL[0], PHP_URL_PATH)));
				html_error('CAPTCHA not found.');
			}

			$data = $this->DefaultParamArr($this->link, $this->cookie, $start_timer['redirect_url'], true);
			$data['step'] = '1';
			$data['posturl'] = urldecode(parse_url($start_timer['redirect_url'], PHP_URL_PATH));
			return $this->SolveMedia($smKey[1], $data, $start_timer['redirect_url']);
		} else {
			$post = $this->verifySolveMedia();
			$post['send'] = 'Submit';
			$this->cookie = StrToCookies(decrypt(urldecode($_POST['cookie'])));

			$page = $this->GetPage($this->baseurl . $_POST['posturl'], $this->cookie, $post);
			$this->cookie = GetCookiesArr($page, $this->cookie);

			if (preg_match("@\nLocation: (?:https?://(?:www\.)?alfafile\.net)?/file/{$this->fid}/?\r?\n@i", $page)) {
				$page = $this->GetPage($this->link, $this->cookie);
			}

			if (!preg_match($this->DLRegexp, $page, $DL)) html_error('Download Link Not Found.');

			return $this->RedirectDownload($DL[0], urldecode(parse_url($DL[0], PHP_URL_PATH)));
		}
	}

	private function ApiDL() {
		$DL = $this->ApiReq('file/download', array('file_id' => $this->fid));

		if (empty($DL['download_url'])) html_error('Download-Link Not Found.');

		return $this->RedirectDownload($DL['download_url'], urldecode(parse_url($DL['download_url'], PHP_URL_PATH)));
	}

	private function Login($user, $pass) {
		$this->login = $this->ApiReq('user/login', array('login' => $user, 'password' => $pass));
		if (empty($this->login['token'])) html_error('Login Token Not Found.');
		if (empty($this->login['user'])) html_error('User Data Not Found.');
		if (empty($this->login['user']['is_premium'])) $this->changeMesg('<br /><b>Account isn\'t premium</b>', true);

		return $this->ApiDL();
	}

	private function ApiReq($path, $post = array()) {
		if (!is_array($post)) $post = array();
		if ($path != 'user/login' && !empty($this->login['token'])) $post['token'] = $this->login['token'];

		$page = $this->GetPage("https://alfafile.net/api/v1/$path", 0, array_map('urlencode', $post), 'https://alfafile.net/');
		$reply = $this->json2array($page, "ApiReq($path) Error");

		if (empty($reply['response']) && !empty($reply['details'])) html_error("[ApiReq($path) Error] " . htmlspecialchars($reply['details'], ENT_QUOTES));

		return $reply['response'];
	}

	// Special Function Called by verifySolveMedia When Captcha Is Incorrect, To Allow Retry. - Optional
	protected function retrySolveMedia() {
		$data = $this->DefaultParamArr($this->link);
		$data['cookie'] = (!empty($_POST['cookie']) ? $_POST['cookie'] : '');
		$data['step'] = '1';
		$data['posturl'] = $_POST['posturl'];

		return $this->SolveMedia($_POST['sm_public_key'], $data, $this->baseurl . $_POST['posturl']);
	}
}

//[20-12-2015]  Written by Th3-822.

?>