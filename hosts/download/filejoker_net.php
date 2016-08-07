<?php

if (!defined('RAPIDLEECH')) {
	require_once('index.html');
	exit();
}

if (!file_exists(HOST_DIR . 'download/GenericXFS_DL.php')) html_error('Cannot load "'.htmlentities(HOST_DIR).'download/GenericXFS_DL.php" (File doesn\'t exists)');
require_once(HOST_DIR . 'download/GenericXFS_DL.php');

class filejoker_net extends GenericXFS_DL {
	public $pluginVer = 14;
	public function Download($link) {
		$this->wwwDomain = false; // Switch to true if filehost forces it's domain with www.
		$this->cname = 'xfss'; // Session cookie name
		$this->sslLogin = false; // Force https on login.
		$this->embedDL = false; // Try to unpack player's js for finding download link. (Only hosts with video player)
		$this->unescaper = false; // Enable JS unescape decoder.

		// Custom Download Regexp
		$this->DLregexp = '@https?://(?:[\w\-]+\.)+[\w\-]+(?:\:\d+)?/\w{72}/[^\t\r\n<>\'\"\?\&]+@i';

		$this->Start($link);
	}

	protected function sendLogin($post) {
		$page = $this->GetPage($this->purl . '/login', $this->cookie, $post, $this->purl . "\r\nX-Requested-With: XMLHttpRequest");
		return $page;
	}

	protected function Login() {
		$this->pA = (empty($_REQUEST['premium_user']) || empty($_REQUEST['premium_pass']) ? false : true);
		$pkey = str_ireplace(array('www.', '.'), array('', '_'), $this->domain);
		if (($_REQUEST['premium_acc'] != 'on' || (!$this->pA && (empty($GLOBALS['premium_acc'][$pkey]['user']) || empty($GLOBALS['premium_acc'][$pkey]['pass']))))) return $this->FreeDL();
	
		$user = ($this->pA ? $_REQUEST['premium_user'] : $GLOBALS['premium_acc'][$pkey]['user']);
		$pass = ($this->pA ? $_REQUEST['premium_pass'] : $GLOBALS['premium_acc'][$pkey]['pass']);
		if ($this->pA && !empty($_POST['pA_encrypted'])) {
			$user = decrypt(urldecode($user));
			$pass = decrypt(urldecode($pass));
			unset($_POST['pA_encrypted']);
		}
		if (empty($user) || empty($pass)) html_error('Login Failed: User or Password is empty. Please check login data.');

		if (!($page = $this->CookieLogin($user, $pass))) {
			$is_step = (!empty($_POST['step']) && $_POST['step'] == 'L');
			if ($is_step) {
				$_POST['step'] = false;
				if (empty($_POST['recaptcha_response_field'])) html_error('You didn\'t enter the image verification code.');
			}

			$post = array();
			$post['op'] = 'login';
			$post['redirect'] = '';
			$post['email'] = urlencode($user);
			$post['password'] = urlencode($pass);
			$post['recaptcha_response_field'] = !empty($_POST['recaptcha_response_field']) ? urlencode($_POST['recaptcha_response_field']) : '';
			$post['recaptcha_challenge_field'] = !empty($_POST['recaptcha_challenge_field']) ? urlencode($_POST['recaptcha_challenge_field']) : '';
			$post['rand'] = '';

			$page = $this->sendLogin($post);

			if (stripos($page, 'data-captcha="yes"') !== false) {
				if ($is_step) html_error('Wrong Captcha Entered.');
				$data = $this->DefaultParamArr($this->link, $this->cookie, 1, 1);
				$data['step'] = 'L';
				if ($this->pA) {
					$data['pA_encrypted'] = 'true';
					$data['premium_user'] = urlencode(encrypt($user)); // encrypt() will keep this safe.
					$data['premium_pass'] = urlencode(encrypt($pass)); // And this too.
				}
				$this->reCAPTCHA('6LetAu0SAAAAACCJkqZLvjNS4L7eSL8fGxr-Jzy2', $data); // Hardcoded reCAPTCHA id.
				exit();
			}

			if (!$this->checkLogin($page)) html_error('Login Error: checkLogin() returned false.');

			$this->cookie = GetCookiesArr($page);
			if (empty($this->cookie[(!empty($this->cname) ? $this->cname : 'xfss')])) html_error('Login Error: Cannot find session cookie.');
			$this->cookie['lang'] = 'english';

			$page = $this->isLoggedIn();
			if (!$page) html_error('Login Error: isLoggedIn() returned false.');
		}

		return $this->checkAccount($page);
	}

	protected function isLoggedIn() {
		$page = $this->GetPage($this->purl.'profile', $this->cookie, 0, $this->purl.'login');
		if (stripos($page, '>Logout</a>') === false) return false;
		return $page;
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

	// Edited
	private function CookieLogin($user, $pass, $filename = 'filejoker_dl.php') {
		global $secretkey;
		if (empty($user) || empty($pass)) html_error('Login Failed: User or Password is empty.');

		$filename = DOWNLOAD_DIR . basename($filename);
		if (!file_exists($filename)) return false;

		$file = file($filename);
		$savedcookies = unserialize($file[1]);
		unset($file);

		$hash = hash('crc32b', $user.':'.$pass);
		if (array_key_exists($hash, $savedcookies)) {
			$_secretkey = $secretkey;
			$secretkey = hash('crc32b', $pass).sha1($user.':'.$pass).hash('crc32b', $user);
			$testCookie = (decrypt(urldecode($savedcookies[$hash]['enc'])) == 'OK') ? $this->IWillNameItLater($savedcookies[$hash]['cookie']) : '';
			$secretkey = $_secretkey;
			if (empty($testCookie) || (is_array($testCookie) && count($testCookie) < 1)) return false;

			$oCookie = $this->cookie;
			$this->cookie = $testCookie;
			if ($page = $this->isLoggedIn()) {
				$this->cookie['lang'] = 'english';
				$this->SaveCookies($user, $pass); // Update cookies file
				return $page;
			}
			$this->cookie = $oCookie;
		}
		return false;
	}

	private function SaveCookies($user, $pass, $filename = 'filejoker_dl.php') {
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
		$secretkey = hash('crc32b', $pass).sha1($user.':'.$pass).hash('crc32b', $user);
		$savedcookies[$hash] = array('time' => time(), 'enc' => urlencode(encrypt('OK')), 'cookie' => $this->IWillNameItLater($this->cookie, false));
		$secretkey = $_secretkey;

		file_put_contents($filename, "<?php exit(); ?>\r\n" . serialize($savedcookies), LOCK_EX);
	}
}

// Written by Th3-822.

?>