<?php

if (!defined('RAPIDLEECH')) {
	require_once('index.html');
	exit;
}

class fileboom_me extends DownloadClass {
	private $page, $cookie = array(), $pA;
	public function Download($link) {
		$this->LnkRegexp = '@https?://(?:www\.)?(f(?:ile)?boom\.me)/file(?:/info)?/(\w+)@i';
		$this->RDRegexp = '@/file/url.html\?file=\w+@i';
		$this->DLRegexp = '@https?://(?:free|premium)-\d+\.(?:f(?:ile)?boom\.me)/[^\s\'\"<>]+@i';

		if (!preg_match($this->LnkRegexp, $link, $fid)) html_error('Invalid link?.');
		$this->domain = $fid[1];
		$this->link = $GLOBALS['Referer'] = 'http://'.$fid[1].'/file/'.$fid[2];

		if (empty($_POST['step'])) {
			$this->page = $this->GetPage($this->link, $this->cookie);
			if (preg_match($this->LnkRegexp, $this->page, $fid)) {
				$this->domain = $fid[1];
				$this->link = $GLOBALS['Referer'] = 'http://'.$fid[1].'/file/'.$fid[2];
				$this->cookie = GetCookiesArr($this->page, $this->cookie);
				$this->page = $this->GetPage($this->link, $this->cookie);
			}
			is_present($this->page, 'File not found or deleted');
			$this->cookie = GetCookiesArr($this->page, $this->cookie);
		}

		$this->pA = (empty($_REQUEST['premium_user']) || empty($_REQUEST['premium_pass']) ? false : true);
		if (($_REQUEST['premium_acc'] == 'on' && ($this->pA || (!empty($GLOBALS['premium_acc']['fileboom_me']['user']) && !empty($GLOBALS['premium_acc']['fileboom_me']['pass']))))) {
			$user = ($this->pA ? $_REQUEST['premium_user'] : $GLOBALS['premium_acc']['fileboom_me']['user']);
			$pass = ($this->pA ? $_REQUEST['premium_pass'] : $GLOBALS['premium_acc']['fileboom_me']['pass']);
			if ($this->pA && !empty($_POST['pA_encrypted'])) {
				$user = decrypt(urldecode($user));
				$pass = decrypt(urldecode($pass));
				unset($_POST['pA_encrypted']);
			}
			return $this->CookieLogin($user, $pass);
		} else return $this->FreeDL();
	}

	private function FreeDL() {
		if (empty($_POST['step']) || !in_array($_POST['step'], array('1', '2'))) {
			is_present($this->page, 'This file is available<br>only for premium members.', 'This file is available only for premium members.');
			$post = array();
			$post['slow_id'] = cut_str($this->page, 'data-slow-id="', '"');
			if (empty($post['slow_id'])) html_error('FreeDL ID don\'t found.');

			$page = $this->GetPage($this->link, $this->cookie, $post);
			$this->cookie = GetCookiesArr($page, $this->cookie);

			// Check freedl limit timer
			if (preg_match('@Please wait (?:(\d{1,2})\:)?(\d{2}):(\d{2}) to download this file@i', $page, $timer)) {
				$timer = ($timer[1] * 3600) + ($timer[2] * 60) + $timer[3];
				return $this->JSCountdown($timer, 0, 'FreeDL limit reached.');
			}

			// Check direct link
			if (preg_match($this->RDRegexp, $page, $idDl)) {
				$page = $this->GetPage('http://'.$this->domain.$idDl[0], $this->cookie);
				if (!preg_match($this->DLRegexp, $page, $dl)) html_error('Download Link Not Found.');
				return $this->RedirectDownload($dl[0], 'T8_fb_fr2', $this->cookie);
			}

			$data = $this->DefaultParamArr($this->link, encrypt(CookiesToStr($this->cookie)));
			$data['step'] = '1';
			$data['uniqueId'] = $post['slow_id'];

			if (preg_match('@https?://(?:[^/]+\.)?(?:(?:google\.com/recaptcha/api)|(?:recaptcha\.net))/(?:(?:challenge)|(?:noscript))\?k=([\w\.\-]+)@i', $page, $cpid)) {
				$data['step'] = '1';
				$this->reCAPTCHA($pid[1], $data);
			} elseif (preg_match('@\W(file/captcha\.html\?v=\w+)@i', $page, $cpid)) {
				$data['step'] = '2';
				list($headers, $imgBody) = explode("\r\n\r\n", $this->GetPage('http://'.$this->domain.'/'.$cpid[1], $this->cookie), 2);
				if (substr($headers, 9, 3) != '200') html_error('Error downloading captcha img.');
				$mimetype = (preg_match('@image/[\w+]+@', $headers, $mimetype) ? $mimetype[0] : 'image/png');
				$this->EnterCaptcha("data:$mimetype;base64,".base64_encode($imgBody), $data, 20);
			} else html_error('CAPTCHA not found.');
			return;
		}

		$this->cookie = StrToCookies(decrypt(urldecode($_POST['cookie'])));
		$uniqueId = !empty($_POST['uniqueId']) ? trim($_POST['uniqueId']) : false;
		if (empty($uniqueId)) html_error('Error: Empty "uniqueId".');

		$post = array('free' => 1, 'freeDownloadRequest' => 1, 'uniqueId' => $uniqueId);
		if ($_POST['step'] == '1') {
			if (empty($_POST['recaptcha_response_field'])) html_error('You didn\'t enter the image verification code.');
			$post['CaptchaForm%5Bcode%5D'] = '';
			$post['recaptcha_challenge_field'] = urlencode($_POST['recaptcha_challenge_field']);
			$post['recaptcha_response_field'] = urlencode($_POST['recaptcha_response_field']);
		} else {
			if (empty($_POST['captcha'])) html_error('You didn\'t enter the image verification code.');
			$post['CaptchaForm%5Bcode%5D'] = urlencode($_POST['captcha']);
		}

		$page = $this->GetPage($this->link, $this->cookie, $post);
		$this->cookie = GetCookiesArr($page, $this->cookie);

		is_present($page, 'The verification code is incorrect.');

		if (!preg_match('@\sid="download-wait-timer"[^>]*>\s*(?:<\w+(?:\s[^>]+)?>)?(\d+)\s*</@i', $page, $cD)) html_error('Countdown not found.');
		if ($cD[1] > 0) $this->CountDown($cD[1]);

		$post = array('uniqueId' => $uniqueId, 'free' => 1);
		$page = $this->GetPage($this->link, $this->cookie, $post);
		$this->cookie = GetCookiesArr($page, $this->cookie);

		if (!preg_match($this->RDRegexp, $page, $idDl)) html_error('Redirect Link Not Found.');
		$page = $this->GetPage('http://'.$this->domain.$idDl[0], $this->cookie);

		if (!preg_match($this->DLRegexp, $page, $dl)) html_error('Download Link Not Found.');

		$this->RedirectDownload($dl[0], 'T8_fb_fr', $this->cookie);
	}

	private function checkAntiBotCaptcha($page) {
		if (!empty($_POST['step']) && $_POST['step'] == '-1') return; // Don't recheck.
		if (stripos($page, 'Your account is suspected in using illegal software') !== false) {
			$data = $this->DefaultParamArr($this->link);
			$data['premium_acc'] = 'on';
			if ($this->pA) {
				$data['pA_encrypted'] = 'true';
				$data['premium_user'] = urlencode(encrypt($user)); // encrypt() will keep this safe.
				$data['premium_pass'] = urlencode(encrypt($pass)); // And this too.
			}
			if (preg_match('@https?://(?:[^/]+\.)?(?:(?:google\.com/recaptcha/api)|(?:recaptcha\.net))/(?:(?:challenge)|(?:noscript))\?k=([\w\.\-]+)@i', $page, $pkey)) {
				$data['step'] = '3';
				return $this->reCAPTCHA($pkey[1], $data);
			} elseif (preg_match('@(https?://(?:www\.)?(?:f(?:ile)?boom\.me))?(?(1)/)(?:[^/\"\'<>\s]+/)*captcha\.html\?v=\w+@i', $page, $imgcap)) {
				$imgcap = empty($imgcap[1]) ? 'http://'.$this->domain.'/'.$imgcap[0] : $imgcap[0];
				$data['step'] = '4';
				list($headers, $imgBody) = explode("\r\n\r\n", $this->GetPage($purl . $cpid[1], $this->cookie), 2);
				if (substr($headers, 9, 3) != '200') html_error('Error downloading captcha img.');
				$mimetype = (preg_match('@image/[\w+]+@', $headers, $mimetype) ? $mimetype[0] : 'image/png');

				return $this->EnterCaptcha("data:$mimetype;base64,".base64_encode($imgBody), $data, 20);
			}
			html_error('AntiBot captcha not found.');
		}
	}

	private function postAntiBotCaptcha() {
		if (empty($_POST['step']) || !in_array($_POST['step'], array('3', '4'))) return $_POST['step'] = false;
		$post = array();
		if ($_POST['step'] == '3') {
			if (empty($_POST['recaptcha_response_field'])) html_error('You didn\'t enter the image verification code.');
			if (empty($_POST['recaptcha_challenge_field'])) html_error('Empty reCAPTCHA challenge.');
			$post['CoreRobotsCheckForm%5BverifyCode%5D'] = '';
			$post['recaptcha_challenge_field'] = urlencode($_POST['recaptcha_challenge_field']);
			$post['recaptcha_response_field'] = urlencode($_POST['recaptcha_response_field']);
		} else {
			if (empty($_POST['captcha'])) html_error('You didn\'t enter the image verification code.');
			$post['CoreRobotsCheckForm%5BverifyCode%5D'] = urlencode($_POST['captcha']);
		}
		$_POST['step'] = '-1';

		return $this->GetPage($this->link, $this->cookie, $post);
	}

	private function PremiumDL() {
		if (!($page = $this->postAntiBotCaptcha())) $page = $this->GetPage($this->link, $this->cookie);
		if (preg_match($this->LnkRegexp, $page, $fid)) {
			$this->domain = $fid[1];
			$this->link = $GLOBALS['Referer'] = 'http://'.$fid[1].'/file/'.$fid[2];
			$this->cookie = GetCookiesArr($page, $this->cookie);
			$page = $this->GetPage($this->link, $this->cookie);
		}
		$this->cookie = GetCookiesArr($page, $this->cookie);

		$this->checkAntiBotCaptcha($page);

		// Check direct link
		if (preg_match($this->DLRegexp, $page, $dl)) return $this->RedirectDownload($dl[0], 'T8_fb_pr2', $this->cookie);

		is_present($page, 'Traffic limit exceed!');

		if (!preg_match($this->RDRegexp, $page, $idDl)) html_error('Redirect-Link Not Found.');
		$page = $this->GetPage('http://'.$this->domain.$idDl[0], $this->cookie);
		if (!preg_match($this->DLRegexp, $page, $dl)) html_error('Download-Link Not Found.');
		return $this->RedirectDownload($dl[0], 'T8_fb_pr', $this->cookie);
	}

	private function Login($user, $pass) {
		$purl = 'http://'.$this->domain.'/';

		$post = array();
		$post['LoginForm%5Busername%5D'] = urlencode($user);
		$post['LoginForm%5Bpassword%5D'] = urlencode($pass);
		$post['LoginForm%5BrememberMe%5D'] = 1;
		if (empty($_POST['step']) || !in_array($_POST['step'], array('1', '2'))) {
			$page = $this->GetPage($purl.'login.html', $this->cookie, $post, $purl);
			$this->cookie = GetCookiesArr($page, $this->cookie);

			if (stripos($page, 'The verification code is incorrect.') !== false) {
				$data = $this->DefaultParamArr($this->link, encrypt(CookiesToStr($this->cookie)));
				$data['premium_acc'] = 'on';
				if ($this->pA) {
					$data['pA_encrypted'] = 'true';
					$data['premium_user'] = urlencode(encrypt($user)); // encrypt() will keep this safe.
					$data['premium_pass'] = urlencode(encrypt($pass)); // And this too.
				}
				if (preg_match('@https?://(?:[^/]+\.)?(?:(?:google\.com/recaptcha/api)|(?:recaptcha\.net))/(?:(?:challenge)|(?:noscript))\?k=([\w\.\-]+)@i', $page, $cpid)) {
					$data['step'] = '1';
					$this->reCAPTCHA($pid[1], $data, 0, 'Login');
				} elseif (preg_match('@\W(auth/captcha\.html\?v=\w+)@i', $page, $cpid)) {
					$data['step'] = '2';

					list($headers, $imgBody) = explode("\r\n\r\n", $this->GetPage($purl . $cpid[1], $this->cookie), 2);
					if (substr($headers, 9, 3) != '200') html_error('Error downloading captcha img.');
					$mimetype = (preg_match('@image/[\w+]+@', $headers, $mimetype) ? $mimetype[0] : 'image/png');

					$this->EnterCaptcha("data:$mimetype;base64,".base64_encode($imgBody), $data, 20);
				} else html_error('Login CAPTCHA not found.');
				exit;
			}
		} else {
			if ($_POST['step'] == '1') {
				if (empty($_POST['recaptcha_response_field'])) html_error('You didn\'t enter the image verification code.');
				$post['LoginForm%5BverifyCode%5D'] = '';
				$post['recaptcha_challenge_field'] = urlencode($_POST['recaptcha_challenge_field']);
				$post['recaptcha_response_field'] = urlencode($_POST['recaptcha_response_field']);
			} else {
				if (empty($_POST['captcha'])) html_error('You didn\'t enter the image verification code.');
				$post['LoginForm%5BverifyCode%5D'] = urlencode($_POST['captcha']);
			}

			$_POST['step'] = false;
			$this->cookie = StrToCookies(decrypt(urldecode($_POST['cookie'])));

			$page = $this->GetPage($purl.'login.html', $this->cookie, $post, $purl);
			$this->cookie = GetCookiesArr($page, $this->cookie);

			is_present($page, 'The verification code is incorrect.');
		}
		is_present($page, 'Incorrect username or password', 'Login Failed: Email/Password incorrect.');
		is_present($page, 'You logged in from different country IP', 'Login Failed: Your account was locked for security reasons, to unlock your account check your email.');
		if (empty($this->cookie['c903aeaf0da94d1b365099298d28f38f'])) html_error('Login Cookie Not Found.');

		$page = $this->GetPage($purl.'site/profile.html', $this->cookie, 0, $purl.'login.html');
		is_notpresent($page, '/auth/logout.html">Logout', 'Login Error.');
		$this->SaveCookies($user, $pass); // Update cookies file
		if (preg_match('@Account type:\s*<span(?:\s[^>]+)?>\s*Free\s*</span>@i', $page)) html_error('Account isn\'t premium.');

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

	private function CookieLogin($user, $pass, $filename = 'fileboom_dl.php') {
		global $secretkey;
		if (empty($user) || empty($pass)) html_error('Login Failed: User or Password is empty.');
		$user = strtolower($user);

		$filename = DOWNLOAD_DIR . basename($filename);
		if (!file_exists($filename) || (!empty($_POST['step']) && in_array($_POST['step'], array('1', '2')))) return $this->Login($user, $pass);

		$file = file($filename);
		$savedcookies = unserialize($file[1]);
		unset($file);

		$hash = hash('crc32b', $user.':'.$pass);
		if (array_key_exists($hash, $savedcookies)) {
			$_secretkey = $secretkey;
			$secretkey = hash('crc32b', $pass).sha1($user.':'.$pass).hash('crc32b', $user); // A 56 char key should be safer. :D
			$testCookie = (decrypt(urldecode($savedcookies[$hash]['enc'])) == 'OK') ? $this->IWillNameItLater($savedcookies[$hash]['cookie']) : '';
			$secretkey = $_secretkey;
			if (empty($testCookie) || (is_array($testCookie) && count($testCookie) < 1)) return $this->Login($user, $pass);

			$page = $this->GetPage('http://'.$this->domain.'/site/profile.html', $testCookie);
			if (stripos($page, '/auth/logout.html">Logout') === false) return $this->Login($user, $pass);
			$this->cookie = GetCookiesArr($page, $testCookie); // Update cookies
			$this->SaveCookies($user, $pass); // Update cookies file
			if (preg_match('@Account type:\s*<span(?:\s[^>]+)?>\s*Free\s*</span>@i', $page)) html_error('Account isn\'t premium');
			return $this->PremiumDL();
		}
		return $this->Login($user, $pass);
	}

	private function SaveCookies($user, $pass, $filename = 'fileboom_dl.php') {
		global $secretkey;
		$maxdays = 31; // Max days to keep extra cookies saved
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
		$secretkey = hash('crc32b', $pass).sha1($user.':'.$pass).hash('crc32b', $user); // A 56 char key should be safer. :D
		$savedcookies[$hash] = array('time' => time(), 'enc' => urlencode(encrypt('OK')), 'cookie' => $this->IWillNameItLater($this->cookie, false));
		$secretkey = $_secretkey;

		file_put_contents($filename, "<?php exit(); ?>\r\n" . serialize($savedcookies), LOCK_EX);
	}
}

//[16-2-2014] Written by Th3-822.
//[07-3-2014] Fixed login captcha. - Th3-822
//[08-6-2014] Added support for Anti bot captcha at premium Dl. (Untested) - Th3-822
//[02-8-2014] Fixed FreeDL captcha. - Th3-822
//[02-1-2016] Fixed FreeDL countdown. - Th3-822
//[17-1-2016] Copied keep2share_cc to fileboom_me & fixed. - Th3-822

?>