<?php

if (!defined('RAPIDLEECH')) {
	require_once('index.html');
	exit;
}

class nitroflare_com extends DownloadClass {
	private $page, $cookie = array(), $pA, $DLRegexp = '@https?://s\d+\.nitroflare\.com/d/\w+/[^\s\'\"<>]+@i';
	public function Download($link) {
		if (!preg_match('@https?://(?:www\.)?nitroflare\.com/view/(\w{15,})(?:/[^\s<>\'\"/]+)?@i', $link, $fid)) html_error('Invalid link?.');
		$this->fid = $fid[1];
		$this->link = $fid[0];

		if (empty($_POST['step'])) {
			$this->page = $this->GetPage($this->link, $this->cookie);
			$this->followLinkRedirect();
			is_present($this->page, 'This file has been removed due to inactivity.');
			is_present($this->page, 'This file has been removed due to infringement of copyright.');
			is_present($this->page, 'This file has been removed by its owner.');
			$this->cookie = GetCookiesArr($this->page, $this->cookie);
		}

		$this->pA = (empty($_REQUEST['premium_user']) || empty($_REQUEST['premium_pass']) ? false : true);
		if (($_REQUEST['premium_acc'] == 'on' && ($this->pA || (!empty($GLOBALS['premium_acc']['nitroflare_com']['user']) && !empty($GLOBALS['premium_acc']['nitroflare_com']['pass']))))) {
			$user = ($this->pA ? $_REQUEST['premium_user'] : $GLOBALS['premium_acc']['nitroflare_com']['user']);
			$pass = ($this->pA ? $_REQUEST['premium_pass'] : $GLOBALS['premium_acc']['nitroflare_com']['pass']);
			if ($this->pA && !empty($_POST['pA_encrypted'])) {
				$user = decrypt(urldecode($user));
				$pass = decrypt(urldecode($pass));
				unset($_POST['pA_encrypted']);
			}
			return $this->CookieLogin($user, $pass);
		} else return $this->FreeDL();
	}

	private function followLinkRedirect() {
		if (!preg_match("@\nLocation: ((https?://(?:www\.)?nitroflare\.com)?/view/{$this->fid}/[^\s<>\'\"/]+)@i", $this->page, $redir)) return;
		$redir = (empty($redir[2]) ? 'http://nitroflare.com'.$redir[1] : $redir[1]);


		// Follow Redirect
		$this->cookie = GetCookiesArr($this->page, $this->cookie);
		$this->page = $this->GetPage($redir, $this->cookie);
		// Update $this->link and $Referer
		$this->link = $GLOBALS['Referer'] = $redir;
	}

	private function FreeDL() {
		if (empty($_POST['step']) || $_POST['step'] != '1') {
			$page = $this->GetPage($this->link, $this->cookie, array('goToFreePage' => 'Submit'));
			$this->cookie = GetCookiesArr($page, $this->cookie);

			$page = $this->GetPage($this->link.'/free', $this->cookie);
			$this->cookie = GetCookiesArr($page, $this->cookie);

			if (!preg_match('@https?://(?:[^/]+\.)?(?:(?:google\.com/recaptcha/api)|(?:recaptcha\.net))/(?:(?:challenge)|(?:noscript))\?k=([\w\.\-]+)@i', $page, $pid)) html_error('CAPTCHA not found.');

			$post = array('method' => 'startTimer', 'fileId' => $this->fid);
			$page = $this->GetPage('http://nitroflare.com/ajax/freeDownload.php', $this->cookie, $post, $this->link . "\r\nX-Requested-With: XMLHttpRequest");
			list($ajaxHeader, $ajaxBody) = explode("\r\n\r\n", $page, 2);

			is_present($ajaxBody, 'This file is available with Premium only. Reason: this file is larger than 1024mb.');
			is_present($ajaxBody, 'This file is available with Premium only. Reason: the file\'s owner disabled free downloads.');
			if (preg_match('@Free downloading is not possible. You have to wait (\d+) \w+ to download your next file.@i', $ajaxBody, $err)) html_error($err[0]);

			$ajaxBody = trim($ajaxBody);
			if ($ajaxBody != "\xEF\xBB\xBF1" && $ajaxBody != '1') html_error('Unexpected result at freeDownload request.');

			if (preg_match('@id="CountDownTimer"\s+data-timer="(\d+)"@i', $page, $cD))$this->CountDown($cD[1]);

			$data = $this->DefaultParamArr($this->link, $this->cookie, 1, 1);
			$data['step'] = 1;
			return $this->reCAPTCHA($pid[1], $data);
		}

		$this->cookie = StrToCookies(decrypt(urldecode($_POST['cookie'])));
		if (empty($_POST['recaptcha_response_field'])) html_error('You didn\'t enter the image verification code.');

		$post = array();
		$post['method'] = 'fetchDownload';
		$post['recaptcha_challenge_field'] = urlencode($_POST['recaptcha_challenge_field']);
		$post['recaptcha_response_field'] = urlencode($_POST['recaptcha_response_field']);

		$page = $this->GetPage('http://nitroflare.com/ajax/freeDownload.php', $this->cookie, $post, $this->link . "\r\nX-Requested-With: XMLHttpRequest");
		is_present($page, 'Free users have to wait ', 'Error: Skipped CountDown?');
		is_present($page, 'The captcha wasn\'t entered correctly');

		if (!preg_match($this->DLRegexp, $page, $dl)) html_error('Download Link Not Found.');

		return $this->RedirectDownload($dl[0], urldecode(parse_url($dl[0], PHP_URL_PATH)));
	}

	private function PremiumDL() {
		$page = $this->GetPage($this->link, $this->cookie);
		$this->cookie = GetCookiesArr($page, $this->cookie);

		if (!preg_match($this->DLRegexp, $page, $dl)) html_error('Download-Link Not Found.');

		return $this->RedirectDownload($dl[0], urldecode(parse_url($dl[0], PHP_URL_PATH)));
	}

	private function Login($user, $pass) {
		$purl = 'https://nitroflare.com/login';
		if (!empty($_POST['step']) && $_POST['step'] == '1') {
			$post = $this->verifyReCaptchav2();
			$post['email'] = urlencode($user);
			$post['password'] = urlencode($pass);
			$post['login'] = 'Submit';
			$post['token'] = urlencode($_POST['token']);

			$this->cookie = StrToCookies(decrypt(urldecode($_POST['cookie'])));
			$page = $this->GetPage($purl, $this->cookie, $post, $purl);
			$this->cookie = GetCookiesArr($page, $this->cookie);

			is_present($page, 'Account does not exist.', 'Login Error: Invalid or Inexistent Email.');
			is_present($page, 'Wrong Email / Password', 'Login Error: Incorrect Email / Password.');
			if (preg_match('@Please try again in \d+ minutes@i', $page, $err)) html_error("Login Error: {$err[0]}.");
			is_present($page, 'CAPTCHA error', 'Login Error: Unknown Captcha Error.');
		} else {
			$post = array();
			$post['email'] = urlencode($user);
			$post['password'] = urlencode($pass);
			$post['login'] = 'Submit';

			$page = $this->GetPage($purl, $this->cookie);
			if (!($post['token'] = urlencode(cut_str($page, 'name="token" value="', '"')))) html_error('Login Error: Cannot find login token');
			$this->cookie = GetCookiesArr($page, $this->cookie);

			$page = $this->GetPage($purl, $this->cookie, $post, $purl);
			$this->cookie = GetCookiesArr($page, $this->cookie);

			is_present($page, 'Account does not exist.', 'Login Error: Invalid or Inexistent Email');
			is_present($page, 'Wrong Email / Password', 'Login Error: Incorrect Email / Password');
			if (preg_match('@Please try again in \d+ minutes@i', $page, $err)) html_error('Login Error: ' . $err[0]);

			if (!preg_match('@class="g-recaptcha" data-sitekey="([\w\.\-]+)"@i', $page, $cpid) && stripos($page, 'CAPTCHA error') !== false) html_error('reCAPTCHA2 Not Found.');

			if (!empty($cpid[1])) {
				// Captcha Required
				$data = $this->DefaultParamArr($this->link, $this->cookie, true, true);
				$data['step'] = '1';
				if (!($data['token'] = html_entity_decode(cut_str($page, 'name="token" value="', '"'), ENT_QUOTES))) html_error('Login Error: Cannot find login token.');
				$data['premium_acc'] = 'on';
				if ($this->pA) {
					$data['pA_encrypted'] = 'true';
					$data['premium_user'] = urlencode(encrypt($user));
					$data['premium_pass'] = urlencode(encrypt($pass));
				}
				return $this->reCAPTCHAv2($cpid[1], $data, 0, 'Login');
			}
		}

		if (empty($this->cookie['user'])) html_error('Login Error: Cannot find "user" cookie.');
		$this->SaveCookies($user, $pass); // Update cookies file

		// Test Login
		$page = $this->GetPage('https://nitroflare.com/member', $this->cookie, 0, $purl);
		is_present($page, '>Inactive</', 'Login Error: Account isn\'t premium.');

		return $this->PremiumDL();
	}

	private function IWillNameItLater($cookie, $decrypt=true) {
		if (!is_array($cookie)) {
			if (!empty($cookie)) return $decrypt ? decrypt(urldecode($cookie)) : urlencode(encrypt($cookie));
			return '';
		}
		if (count($cookie) < 1) return $cookie;
		$keys = array_keys($cookie);
		$values = array_values($cookie);
		$keys = $decrypt ? array_map('decrypt', array_map('urldecode', $keys)) : array_map('urlencode', array_map('encrypt', $keys));
		$values = $decrypt ? array_map('decrypt', array_map('urldecode', $values)) : array_map('urlencode', array_map('encrypt', $values));
		return array_combine($keys, $values);
	}

	private function CookieLogin($user, $pass, $filename = 'nitroflare_dl.php') {
		global $secretkey;
		if (empty($user) || empty($pass)) html_error('Login Failed: User or Password is empty.');

		$filename = DOWNLOAD_DIR . basename($filename);
		if (!file_exists($filename)/* || (!empty($_POST['step']) && $_POST['step'] == '1')*/) return $this->Login($user, $pass);

		$file = file($filename);
		$savedcookies = unserialize($file[1]);
		unset($file);

		$hash = hash('crc32b', $user.':'.$pass);
		if (array_key_exists($hash, $savedcookies)) {
			$_secretkey = $secretkey;
			$secretkey = sha1($user.':'.$pass);
			$testCookie = (decrypt(urldecode($savedcookies[$hash]['enc'])) == 'OK') ? $this->IWillNameItLater($savedcookies[$hash]['cookie']) : '';
			$secretkey = $_secretkey;
			if (empty($testCookie) || (is_array($testCookie) && count($testCookie) < 1)) return $this->Login($user, $pass);

			$page = $this->GetPage('https://nitroflare.com/member', $testCookie);
			if (stripos($page, '>Logout</a>') === false) return $this->Login($user, $pass);
			$this->cookie = GetCookiesArr($page, $testCookie); // Update cookies
			$this->SaveCookies($user, $pass); // Update cookies file
			is_present($page, '>Inactive</', 'Account isn\'t premium');
			return $this->PremiumDL();
		}
		return $this->Login($user, $pass);
	}

	private function SaveCookies($user, $pass, $filename = 'nitroflare_dl.php') {
		global $secretkey;
		$maxdays = 31; // Max days to keep cookies saved
		$filename = DOWNLOAD_DIR . basename($filename);
		if (file_exists($filename)) {
			$file = file($filename);
			$savedcookies = unserialize($file[1]);
			unset($file);

			// Remove old cookies
			foreach ($savedcookies as $k => $v) if (time() - $v['time'] >= ($maxdays * 24 * 60 * 60)) unset($savedcookies[$k]);
		} else $savedcookies = array();
		$hash = hash('crc32b', $user.':'.$pass);
		$_secretkey = $secretkey;
		$secretkey = sha1($user.':'.$pass);
		$savedcookies[$hash] = array('time' => time(), 'enc' => urlencode(encrypt('OK')), 'cookie' => $this->IWillNameItLater($this->cookie, false));
		$secretkey = $_secretkey;

		file_put_contents($filename, "<?php exit(); ?>\r\n" . serialize($savedcookies), LOCK_EX);
	}

	// Special Function Called by verifyReCaptchav2 When Captcha Is Incorrect, To Allow Retry. - Required
	protected function retryReCaptchav2() {
		$data = $this->DefaultParamArr($this->link);
		$data['cookie'] = (!empty($_POST['cookie']) ? $_POST['cookie'] : '');
		$data['step'] = '1';
		$data['token'] = (!empty($_POST['cookie']) ? $_POST['token'] : '');
		$data['premium_acc'] = 'on';
		if ($this->pA && !empty($_GET['pA_encrypted']) && !empty($_REQUEST['premium_user']) && !empty($_REQUEST['premium_pass'])) {
			$data['pA_encrypted'] = 'true';
			$data['premium_user'] = $_REQUEST['premium_user'];
			$data['premium_pass'] = $_REQUEST['premium_pass'];
		}
		return $this->reCAPTCHAv2($_POST['recaptcha2_public_key'], $data, 0, 'Retry Login');
	}
}

//[18-10-2015] Written by Th3-822.
//[13-12-2015] Fixed FreeDL CountDown. - Th3-822

?>