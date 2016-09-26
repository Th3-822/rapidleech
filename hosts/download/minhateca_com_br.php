<?php

if (!defined('RAPIDLEECH')) {
	require_once('index.html');
	exit;
}

class minhateca_com_br extends DownloadClass {
	private $page, $cookie = array(), $pA, $DLRegexp = '@https?://s\d+\.minhateca\.com\.br/File\.aspx\?[^\t\r\n\'\"<>]+@i';
	public function Download($link) {
		if (!preg_match('@(https?://minhateca\.com\.br)/(?:[^/]+/)+[^\r\n\t/<>\"]+,(\d+)[^\r\n\t/<>\"]*@i', str_ireplace('://www.minhateca.com.br', '://minhateca.com.br', $link), $fid)) html_error('Invalid link?.');
		$this->link = str_ireplace('https://', 'http://', $fid[0]);
		$this->baseurl = $fid[1];
		$this->fid = $fid[2];

		$this->pA = (empty($_REQUEST['premium_user']) || empty($_REQUEST['premium_pass']) ? false : true);
		if (($_REQUEST['premium_acc'] == 'on' && ($this->pA || (!empty($GLOBALS['premium_acc']['minhateca_com_br']['user']) && !empty($GLOBALS['premium_acc']['minhateca_com_br']['pass']))))) {
			$user = ($this->pA ? $_REQUEST['premium_user'] : $GLOBALS['premium_acc']['minhateca_com_br']['user']);
			$pass = ($this->pA ? $_REQUEST['premium_pass'] : $GLOBALS['premium_acc']['minhateca_com_br']['pass']);
			return $this->Login($user, $pass);
		} else return $this->TryDL();
	}

	private function TryDL() {
		$this->page = $this->GetPage($this->link, $this->cookie);
		$this->cookie = GetCookiesArr($this->page, $this->cookie);

		if (!preg_match($this->DLRegexp, $this->page, $DL)) {
			if (substr($this->page, 9, 3) != '200') html_error('File Not Found or Private.');
			$download = $this->reqAction('License/Download', array('fileId' => $this->fid, '__RequestVerificationToken' => $this->getCSRFToken($this->page)));
			if (empty($download['redirectUrl']) || !preg_match($this->DLRegexp, $download['redirectUrl'], $DL)) {
				if (empty($this->cookie['RememberMe']) && $download['Type'] == 'Window') html_error('Login is Required to Download This File.');
				html_error('Download-Link Not Found.');
			}
		}
		return $this->RedirectDownload($DL[0], 'minhateca_placeholder_fname');
	}

	private function getCSRFToken($page, $errorPrefix = 'Error') {
		if (!preg_match('@name="__RequestVerificationToken"\s+type="hidden"\s+value="([\w\-+/=]+)"@i', $page, $token)) return html_error("[$errorPrefix]: Request Token Not Found.");
		return $token[1];
	}

	private function Login($user, $pass) {
		$page = $this->GetPage($this->baseurl . '/', $this->cookie);
		$this->cookie = GetCookiesArr($page, $this->cookie);

		$login = $this->reqAction('login/login', array('Login' => $user, 'Password' => $pass, 'FileId' => 0, '__RequestVerificationToken' => $this->getCSRFToken($page, 'Login Error')));
		if (empty($login['IsSuccess'])) html_error('Login Error: IsSuccess is false.');
		is_present($login['Content'], 'Connta com este nome n&#227;o existe.', 'Login Error: Wrong Username/Email.');
		is_present($login['Content'], 'A senha indicada n&#227;o &#233; a senha correcta do propriet&#225;rio desta conta.', 'Login Error: Wrong Password.');
		if (empty($this->cookie['RememberMe'])) html_error('Login Error: "RememberMe" cookie not found.');

		return $this->TryDL();
	}

	private function reqAction($path, $post = array()) {
		$page = $this->GetPage("{$this->baseurl}/action/$path", $this->cookie, array_map('urlencode', $post), "{$this->baseurl}/\r\nX-Requested-With: XMLHttpRequest");
		$reply = $this->json2array($page, "reqAction($path) Error");
		if (empty($reply)) html_error("[reqAction($path) Error] Empty Response.");
		$this->cookie = GetCookiesArr($page, $this->cookie);

		return $reply;
	}
}

// [20-3-2016] Written by Th3-822.
// [22-4-2016] Renamed to minhateca_com_br. - Th3-822

?>