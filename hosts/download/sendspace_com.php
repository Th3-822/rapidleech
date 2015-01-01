<?php
if (!defined('RAPIDLEECH')) {
	require_once("index.html");
	exit;
}

class sendspace_com extends DownloadClass {
	private $page, $cookie;
	public function Download($link) {
		global $premium_acc;
		$this->link = str_ireplace(array('https://', '://sendspace.com'), array('http://', '://www.sendspace.com'), $link);
		$this->pA = (empty($_GET['premium_user']) || empty($_GET['premium_pass']) ? false : true);
		if ($_GET['premium_acc'] == 'on' && ($this->pA || (!empty($premium_acc['sendspace_com']['user']) && !empty($premium_acc['sendspace_com']['pass'])))) $this->Login();
		else $this->Free();
	}

	private function Free() {
		$this->page = $this->GetPage($this->link);
		$this->cookie = GetCookiesArr($this->page);

		if (preg_match('@\nLocation: ((https?://www\.sendspace\.com)?/[^\r\n\"\'\t<>]+)@i', $this->page, $check)) {
			$check[1] = (empty($check[2])) ? 'http://www.sendspace.com'.$check[1] : $check[1];
			$this->page = $this->GetPage($check[1], $this->cookie);
			$this->cookie = GetCookiesArr($this->page, $this->cookie);
		}
		$this->chkCaptcha();

		is_present($this->page, 'Sorry, the file you requested is not available.');

		if (!preg_match('@https?://(?:[a-zA-Z\d\-]+\.)*sendspace\.com/dl/[^\r\n\"\'\t<>]+@i', $this->page, $dl)) html_error('Download Link Not Found.');

		$this->RedirectDownload($dl[0], basename(parse_url($dl[0], PHP_URL_PATH)), $this->cookie);
	}

	private function Premium() {
		$this->page = $this->GetPage($this->link, $this->cookie);
		if (preg_match('@\nLocation: ((https?://www\.sendspace\.com)?/[^\r\n\"\'\t<>]+)@i', $this->page, $check)) {
			$check[1] = (empty($check[2])) ? 'http://www.sendspace.com'.$check[1] : $check[1];
			$this->page = $this->GetPage($check[1], $this->cookie);
			$this->cookie = GetCookiesArr($this->page, $this->cookie);
		}
		$this->chkCaptcha();
		is_present($this->page, 'Sorry, the file you requested is not available.');

		if (!preg_match('@https?://(?:[a-zA-Z\d\-]+\.)*sendspace\.com/dlp/[^\r\n\"\'\t<>]+@i', $this->page, $dl)) html_error('Download-Link Not Found.');

		$this->RedirectDownload($dl[0], basename(parse_url($dl[0], PHP_URL_PATH)), $this->cookie);
	}

	private function Login() {
		global $premium_acc;
		$site = 'https://www.sendspace.com';
		$post = array();
		$post['action'] = 'login';
		$post['submit'] = 'login';
		$post['target'] = '%252F';
		$post['action_type'] = 'login';
		$post['remember'] = '1';
		$post['username'] = urlencode($this->pA ? $_GET['premium_user'] : $premium_acc['sendspace_com']['user']);
		$post['password'] = urlencode($this->pA ? $_GET['premium_pass'] : $premium_acc['sendspace_com']['pass']);
		$post['remember'] = 'on';
		$page = $this->GetPage("$site/login.html", 0, $post, "$site/");

		is_present($page, 'check your username and password', 'Login Failed: Invalid User/Password.');
		$this->cookie = GetCookiesArr($page);
		if (empty($this->cookie['ssal'])) html_error('Login Error: Cannot find "ssal" cookie.');

		$page = $this->GetPage("$site/mysendspace/myindex.html", $this->cookie, 0, "$site/");
		is_notpresent($page, 'Your account needs to be renewed in', 'Login Failed: Account Isn\'t Premium.');

		$this->Premium();
	}

	private function chkCaptcha() {
		if (stripos($this->page, 'Please complete the form below:') === false) return;
		if (!empty($_POST['step']) && $_POST['step'] == '1') {
			if (empty($_POST['recaptcha_response_field'])) html_error('You didn\'t enter the image verification code.');
			$this->cookie = StrToCookies(decrypt(urldecode($_POST['cookie'])));
			$post = array('recaptcha_challenge_field' => $_POST['recaptcha_challenge_field'], 'recaptcha_response_field' => $_POST['recaptcha_response_field']);
			$this->page = $this->GetPage($this->link, $this->cookie, $post);
			if (stripos($this->page, 'You entered an invalid captcha') !== false) {
				echo "\n<span class='htmlerror'><b>You entered an invalid captcha, please try again.</b></span><br />";
				unset($_POST['step']);
				$this->chkCaptcha();
			}
		} else {
			if (!preg_match('@https?://(?:[a-zA-Z\d\-]+\.)*(?:google\.com/recaptcha/api|recaptcha\.net)/(?:challenge|noscript)\?k=([\w\.\-]+)@i', $this->page, $cpid)) html_error('reCaptcha Not Found.');
			$data = $this->DefaultParamArr($this->link, encrypt(CookiesToStr($this->cookie)));
			$data['step'] = '1';
			$this->reCAPTCHA($cpid[1], $data);
			exit;
		}
	}
}

// Use PREMIUM? [szalinski 09-May-09]
// fix free download by kaox 19-dec-2009
// Fix premium & free by Ruud v.Tony 03-Okt-2011
// [16-6-2013] Rewritten & Added captcha support. - Th3-822

?>