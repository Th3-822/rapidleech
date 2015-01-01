<?php

if (!defined('RAPIDLEECH')) {
	require_once ('index.html');
	exit();
}

class freakshare_com extends DownloadClass {
	private $page, $cookie, $pA;
	public function Download($link) {
		global $premium_acc;
		$this->link = $link = str_ireplace('freakshare.net/', 'freakshare.com/', $link);
		$this->cookie = array();
		$this->DLRegexp = '@https?://\w+\.freakshare\.com/get\.php\?dlid=\w+@i';
		$this->pA = (empty($_REQUEST['premium_user']) || empty($_REQUEST['premium_pass']) ? false : true);

		$_POST['step'] = empty($_POST['step']) ? false : $_POST['step'];
		if (empty($_POST['step']) || !in_array($_POST['step'], array('1', '2'))) {
			$this->page = $this->GetPage($this->link, $this->cookie);
			if (stripos($this->page, 'selected="selected">English<') === false) {
				$this->cookie = GetCookiesArr($this->GetPage('http://freakshare.com/index.php?language=EN', $this->cookie), $this->cookie);
				$this->page = $this->GetPage($this->link, $this->cookie);
			}
			is_present($this->page, 'This file does not exist!');
			$this->cookie = GetCookiesArr($this->page, $this->cookie);
		} elseif (!empty($_POST['cookie'])) $this->cookie = StrToCookies(decrypt(urldecode($_POST['cookie'])));

		if (($_REQUEST['premium_acc'] == 'on' && ($this->pA || (!empty($premium_acc['freakshare_com']['user']) && !empty($premium_acc['freakshare_com']['pass']))))) {
			return $this->Login(($this->pA ? $_REQUEST['premium_user'] : $premium_acc['freakshare_com']['user']), ($this->pA ? $_REQUEST['premium_pass'] : $premium_acc['freakshare_com']['pass']));
		} else return $this->FreeDL();
	}

	private function FreeDL() {
		switch ($_POST['step']) {
			default:
				is_present($this->page, 'Your Traffic is used up for today!');
				if (!preg_match('@\svar\s+time\s*=\s*(\d+)@i', $this->page, $cD)) html_error('Countdown not found.');
				$this->cookie['ads_download'] = '1';
				if ($cD[1] >= 0) {
					if ($cD[1] > 59) {
						$data = $this->DefaultParamArr($this->link, encrypt(CookiesToStr($this->cookie)));
						$data['step'] = '1';
						return $this->JSCountdown($cD[1] + 1, $data);
					} else $this->CountDown($cD[1] + 1);
				}
			case '1':
				$page = $this->GetPage($this->link, $this->cookie, array('section' => 'benefit', 'did' => '0'));
				if (!preg_match('@https?://(?:[^/]+\.)?(?:(?:google\.com/recaptcha/api)|(?:recaptcha\.net))/(?:(?:challenge)|(?:noscript))\?k=([\w\.\-]+)@i', $page, $cpid)) html_error('CAPTCHA not found.');
				$data = $this->DefaultParamArr($this->link, encrypt(CookiesToStr($this->cookie)));
				$data['step'] = '2';
				return $this->reCAPTCHA($cpid[1], $data);
			case '2': break;
		}

		if (empty($_POST['recaptcha_response_field'])) html_error('You didn\'t enter the image verification code.');
		$post = array('recaptcha_challenge_field' => $_POST['recaptcha_challenge_field'], 'recaptcha_response_field' => $_POST['recaptcha_response_field']);
		$post['section'] = 'waitingtime';
		$post['did'] = '0';

		$page = $this->GetPage($this->link, $this->cookie, $post);
		is_present($page, 'Wrong Captcha!', 'Login Failed: Wrong CAPTCHA entered.');

		if (!preg_match($this->DLRegexp, $page, $DL)) html_error('Download Link Not Found.');
		$this->RedirectDownload($DL[0], 'T8_Freakshare_FDL');
	}

	private function PremiumDL() {
		$page = $this->GetPage($this->link, $this->cookie);
		if (!preg_match($this->DLRegexp, $page, $DL)) {
			$this->cookie = GetCookiesArr($page, $this->cookie);
			$page = $this->GetPage($this->link, $this->cookie, array('section' => 'waitingtime', 'did' => '0'));
			if (!preg_match($this->DLRegexp, $page, $DL)) html_error('Download-Link Not Found.');
		}
		$this->RedirectDownload($DL[0], 'T8_Freakshare_PDL');
	}

	private function Login($user, $pass) {
		if (empty($user) || empty($pass)) html_error('Login Failed: User or Password is empty. Please check login data.');
		$post = array();
		$post['user'] = urlencode($user);
		$post['pass'] = urlencode($pass);
		$post['submit'] = 'Login';

		$purl = 'http://freakshare.com/';
		$page = $this->GetPage($purl.'login.html', $this->cookie, $post, $purl);
		if (substr($page, 9, 3) == '200') is_present($page, 'Wrong Username or Password!', 'Login failed: User/Password incorrect.');

		$this->cookie = GetCookiesArr($page);
		if (empty($this->cookie['login'])) html_error('Login Error: Cannot find session cookie.');

		$page = $this->GetPage($purl, $this->cookie, 0, $purl.'login.html');
		is_notpresent($page, '/logout.html', 'Login Error.');

		if (stripos($page, 'selected="selected">English<') === false) {
			$this->cookie = GetCookiesArr($this->GetPage($purl.'index.php?language=EN', $this->cookie, 0, $purl), $this->cookie);
			$page = $this->GetPage($purl, $this->cookie, 0, $purl.'index.php?language=EN');
		}

		if (stripos($page, 'Member (free)') !== false) {
			$this->changeMesg(lang(300).'<br /><b>Account isn\\\'t premium</b><br />Using it as member.');
			return $this->FreeDL();
		}

		if (preg_match('@>\s*Traffic\s+left:\s*</td>\s*<td>\s*(\d+(?:\.\d+)?)\s+([KMGT]?B)@i', $page, $traffic)) {
			$traffic = array($traffic[1], strtoupper($traffic[2]));
			switch ($traffic[1]) { // KbOrMbOrGbToBytes :D
				case 'GB': $traffic[0] *= 1024;
				case 'MB': $traffic[0] *= 1024;
				case 'KB': $traffic[0] *= 1024;
			}
			$this->changeMesg(lang(300) . '<br />Acc. Traffic: ' . bytesToKbOrMbOrGb($traffic[0]));
			if (preg_match('@\s(\d+(?:\.\d+)?)\s+([KMGT]?B)(?:ytes?)?</h1>@i', $this->page, $fs)) {
				$fs = array($fs[1], strtoupper($fs[2]));
				switch ($fs[1]) { // KbOrMbOrGbToBytes :D
					case 'GB': $fs[0] *= 1024;
					case 'MB': $fs[0] *= 1024;
					case 'KB': $fs[0] *= 1024;
				}
				if ($fs[0] > $traffic[0]) html_error('Insufficient account traffic for download this file ('.bytesToKbOrMbOrGb($fs[0]).')');
			} elseif ($traffic[0] < 2 * 1024 * 1024 * 1024) html_error('Remaining Traffic < 2GB.');
		}

		return $this->PremiumDL();
	}
}

// [03-1-2014] Written by Th3-822.
// [09-2-2014] Fixed PremiumDL with DD off. - Th3-822

?>