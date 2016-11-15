<?php
if (!defined('RAPIDLEECH')) {
	require_once 'index.html';
	exit;
}

class datafile_com extends DownloadClass {

	public function Download($link) {
		global $premium_acc;

		if (!$_REQUEST['step']) {
			$this->page = $this->GetPage($link);
			is_present($this->page, 'File not found');
			$this->cookie = GetCookiesArr($this->page);
		}
		$this->link = $link;
		$this->posturl = 'https://www.datafile.com';
		if ($_REQUEST['premium_acc'] == 'on' && (($_REQUEST['premium_user'] && $_REQUEST['premium_pass']) || ($premium_acc['datafile_com']['user'] && $premium_acc['datafile_com']['pass']))) {
			return $this->Premium();
		} elseif ($_REQUEST['step'] == 1) {
			return $this->DownloadFree();
		} else {
			return $this->PrepareFree();
		}
	}

	private function Premium() {
		$filename = cut_str($this->page, '<div class="file-name">', '</div>');
		$cookie = $this->login();
		$page = $this->GetPage($this->link, $cookie);
		if (!preg_match('/Location: (\/[^\s\t\r\n]+)/i', $page, $rd)) html_error('Error[Redirect Link - PREMIUM not found!]');
		$page = $this->GetPage($this->posturl . $rd[1], $cookie, 0, $this->link);
		if (!preg_match('/Location: (https?:\/\/n\d+\.datafile\.com\/[^\s\t\r\n]+)/i', $page, $dl)) html_error('Error[Download Link - PREMIUM not found!]');
		$dlink = trim($dl[1]);
		$this->RedirectDownload($dlink, $filename, $cookie, 0, $this->link, $filename);
	}

	private function login() {
		global $premium_acc;

		$user = ($_REQUEST["premium_user"] ? trim($_REQUEST["premium_user"]) : $premium_acc ["datafile_com"] ["user"]);
		$pass = ($_REQUEST["premium_pass"] ? trim($_REQUEST["premium_pass"]) : $premium_acc ["datafile_com"] ["pass"]);
		if (empty($user) || empty($pass)) html_error("Login failed, username or password is empty!");

		$post = array();
		$post['login'] = $user;
		$post['password'] = $pass;
		$post['remember_me'] = 0;
		$post['remember_me'] = 1;
		$post['btn'] = '';
		$page = $this->GetPage($this->posturl . '/login.html', $this->cookie, $post, $this->posturl . '/login.html');
		$cookie = GetCookiesArr($page, $this->cookie);
		is_present($page, 'Incorrect login or password!');

		//check account
		$page = $this->GetPage($this->posturl . '/profile.html', $cookie, 0, $this->posturl . '/index.html');
		is_notpresent($page, 'Premium Expires', 'Account isn\'t Premium?');

		return $cookie;
	}

	private function DownloadFree() {
		$post = array();
		foreach ($_POST['temp'] as $k => $v) {
			$post[$k] = $v;
		}
		$post['recaptcha_challenge_field'] = $_POST['recaptcha_challenge_field'];
		$post['recaptcha_response_field'] = $_POST['recaptcha_response_field'];
		$recap = $_POST['recap'];
		$filename = $_POST['filename'];
		$this->cookie = urldecode($_POST['cookie']);
		$page = $this->GetPage($this->posturl . '/files/ajax.html', $this->cookie, $post, $this->link, 0, 1);
		$json = $this->json2array($page);
		if ($json['success'] == 0) {
			echo "<div align='center'><font color='red'><b>{$json['msg']}</b></font></div>";
			$data = $this->DefaultParamArr($this->link, $this->cookie);
			foreach ($_POST['temp'] as $k => $v) {
				$data["temp[{$k}]"] = $v;
			}
			$data['step'] = 1;
			$data['recap'] = $recap;
			$data['filename'] = $filename;
			$this->Show_reCaptcha($recap, $data);
			exit();
		}
		if (empty($json['link'])) html_error('Error[Download Link - FREE not found!]');
		$this->RedirectDownload($json['link'], $filename, $this->cookie, 0, $this->link, $filename);
	}

	private function PrepareFree() {
		if ($_REQUEST['step'] == 'countdown') {
			$this->cookie = urldecode($_POST['cookie']);
			$this->page = $this->GetPage($this->link, $this->cookie);
		} else {
			if (!preg_match("/counter\.contdownTimer\('(\d+)'/", $this->page, $w)) html_error('Error[Timer not found!]');
			if ($w[1] < 120) $this->CountDown($w[1]);
			else {
				$data = $this->DefaultParamArr($this->link, $this->cookie);
				$data['step'] = 'countdown';
				$this->JSCountdown($w[1], $data);
			}
		}
		if (stripos($this->page, 'Recaptcha')) {
			$data = $this->DefaultParamArr($this->link, $this->cookie);
			$data['temp[doaction]'] = 'getFileDownloadLink';
			$data['temp[fileid]'] = cut_str($this->page, "getFileDownloadLink('", "'");

			if (!preg_match('/\/challenge\?k=([^"]+)"/', $this->page, $c) || !preg_match('/\/noscript\?k=([^"]+)"/', $this->page, $c)) html_error('Error[Captcha Data not found!]');
			$data['step'] = 1;
			$data['filename'] = cut_str($this->page, '<div class="file-name">', '</div>');
			$data['recap'] = $c[1];
			$this->reCAPTCHA($c[1], $data);
			exit();
		}
	}

}

/*
 * Written by Tony Fauzi Wihana/Ruud v.Tony 24/04/2013
 */
?>
