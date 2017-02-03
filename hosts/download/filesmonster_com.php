<?php

if (!defined('RAPIDLEECH')) {
	require_once('index.html');
	exit;
}

class filesmonster_com extends DownloadClass {
	private $link, $baseUrl, $fid, $page, $cookie = array('yab_ulanguage' => 'en'), $pA;
	public function Download($link) {
		$link = parse_url($link);
		$link['scheme'] = 'https';
		$link['host'] = 'filesmonster.com';
		$link = rebuild_url($link);
		if (!preg_match('@(https?://filesmonster\.com)/download\.php\?id=([\w\-\.%]+)@i', $link, $fid)) html_error('Invalid link?.');
		$this->link = $GLOBALS['Referer'] = str_ireplace('http://', 'https://', $fid[0]);
		$this->baseUrl = $fid[1];
		$this->fid = $fid[2];

		$this->page = $this->GetPage($this->link, $this->cookie);
		if (substr($this->page, 9, 3) == '404') html_error('File Not Found.');
		$this->cookie = GetCookiesArr($this->page, $this->cookie);

		$this->pA = (empty($_REQUEST['premium_user']) || empty($_REQUEST['premium_pass']) ? false : true);
		if (($_REQUEST['premium_acc'] == 'on' && ($this->pA || (!empty($GLOBALS['premium_acc']['filesmonster_com']['user']) && !empty($GLOBALS['premium_acc']['filesmonster_com']['pass']))))) {
			$user = ($this->pA ? $_REQUEST['premium_user'] : $GLOBALS['premium_acc']['filesmonster_com']['user']);
			$pass = ($this->pA ? $_REQUEST['premium_pass'] : $GLOBALS['premium_acc']['filesmonster_com']['pass']);
			if ($this->pA && !empty($_POST['pA_encrypted'])) {
				$user = decrypt(urldecode($user));
				$pass = decrypt(urldecode($pass));
				unset($_POST['pA_encrypted']);
			}
			return $this->Login($user, $pass);
		} else {
			// There is not FreeDL support yet, due to lack of "public" downloadable links
			is_present($this->page, 'You need Premium membership to download', 'Only Premium users can download this file.');
			html_error('FreeDL Support Not Added Yet, Please Report Any Link Which Supports It On The Forum.');
		}
	}

	private function PremiumDL() {
		$page = $this->GetPage($this->link, $this->cookie);

		if (!preg_match('@/get/[\w\-\.%]+/@', $page, $redir)) html_error('PremiumDL Error: Redirect not Found.');
		$page = $this->GetPage($this->baseUrl . $redir[0], $this->cookie);

		if (!preg_match('@/dl/gpl/[\w\-\.%]+/@', $page, $redir)) html_error('PremiumDL Error: Redirect 2 not Found.');
		$page = $this->GetPage($this->baseUrl . $redir[0], $this->cookie);
		$reply = $this->json2array($page, 'PremiumDL Error');

		if (empty($reply['url']) || strpos($reply['url'], '://') === false) html_error('Download-Link Not Found.');
		return $this->RedirectDownload($reply['url'], urldecode(parse_url($reply['url'], PHP_URL_PATH)));
	}

	private function Login($user, $pass) {
		$lUrl = $this->baseUrl . '/login.php?return=%2F';
		$hUrl = $this->baseUrl . '/';

		$post = array('act' => 'login', 'captcha_shown' => 0);
		$post['user'] = urlencode($user);
		$post['pass'] = urlencode($pass);

		$page = $this->GetPage($lUrl, $this->cookie, $post, $hUrl);
		is_present($page, 'Username/Password can not be found', 'Login Error: Wrong Login/Password.');
		$this->cookie = GetCookiesArr($page, $this->cookie);
		if (empty($this->cookie['yab_logined'])) html_error('Login Error: Cookie "yab_logined" not Found.');
		$this->cookie['yab_ulanguage'] = 'en';

		$page = $this->GetPage($hUrl, $this->cookie, 0, $lUrl);
		is_notpresent($page, '<span class="expire-date', 'Login Error: Account Isn\'t Premium?');

		return $this->PremiumDL();
	}
}

//[21-01-2017] Rewritten by Th3-822.
