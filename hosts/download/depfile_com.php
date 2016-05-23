<?php

if (!defined('RAPIDLEECH')) {
	require_once('index.html');
	exit;
}

class depfile_com extends DownloadClass {
	private $page, $cookie = array('sdlanguageid' => '2'), $pA, $DLRegexp = '@https?://(?:[\w\-]+\.)+[\w\-]+(?:\:\d+)?/\d/\d+/\d/\w+/[^\t\r\n\'\"<>]+@i';
	public function Download($link) {
		if (!preg_match('@(https://depfile\.com)/(?>downloads|([a-zA-Z\d]{8,})(?=/|$))(?(2)|/i/(\d+)/f/[^\s]+\.html)@i', str_ireplace(array('http://', '//www.depfile.com'), array('https://', '//depfile.com'), $link), $fid)) html_error('Invalid link?.');
		$this->link = $GLOBALS['Referer'] = $fid[0];
		$this->baseUrl = $fid[1];
		$this->fid = empty($fid[3]) ? $fid[2] : $fid[3];

		if (empty($_POST['step'])) {
			$this->page = $this->GetPage($this->link, $this->cookie);
			$this->cookie = GetCookiesArr($this->page, $this->cookie);

			if (substr($this->page, 9, 3) == '404') html_error('Page Not Found.');

			if (is_numeric($this->fid)) is_present($this->page, 'File was not found in the DepFile database.', 'File Not Found or Link Invalid.');
			else is_present($this->page, "\nLocation: /premium", 'File Not Found.');
			$this->cookie['sdlanguageid'] = '2';

			if (stripos($this->page, '<h1>Download folder</h1>') !== false) return $this->Folder();
		}

		$this->pA = (empty($_REQUEST['premium_user']) || empty($_REQUEST['premium_pass']) ? false : true);
		if (($_REQUEST['premium_acc'] == 'on' && ($this->pA || (!empty($GLOBALS['premium_acc']['depfile_com']['user']) && !empty($GLOBALS['premium_acc']['depfile_com']['pass']))))) {
			$user = ($this->pA ? $_REQUEST['premium_user'] : $GLOBALS['premium_acc']['depfile_com']['user']);
			$pass = ($this->pA ? $_REQUEST['premium_pass'] : $GLOBALS['premium_acc']['depfile_com']['pass']);
			if ($this->pA && !empty($_POST['pA_encrypted'])) {
				$user = decrypt(urldecode($user));
				$pass = decrypt(urldecode($pass));
				unset($_POST['pA_encrypted']);
			}
			return $this->CookieLogin($user, $pass);
		} else return $this->FreeDL();
	}

	private function Folder() {
		if (isset($_GET['audl'])) return html_error('Cannot check folder in audl.');
		if (!preg_match_all('@https?://(?:www\.)?depfile\.com/[a-zA-Z\d]{8,}(?=\')@i', $this->page, $links)) html_error('Empty folder?');
		return $this->moveToAutoDownloader($links[0]);
	}

	private function FreeDL() {
		if (empty($_POST['step']) || $_POST['step'] != '1') {
			is_present($this->page, 'File is available only for Premium users', 'This File Is Only Available For Premium Users.');
			if (!preg_match('@/includes/vvc\.php\?vvcid=(\d+)@i', $this->page, $vvc)) html_error('CAPTCHA not Found.');
			$data = $this->DefaultParamArr($this->link, $this->cookie, 1, 1);
			$data['step'] = '1';
			$data['vvcid'] = $vvc[1];
			list($headers, $imgBody) = explode("\r\n\r\n", $this->GetPage($this->baseUrl.$vvc[0]), 2);
			if (substr($headers, 9, 3) != '200') html_error('FreeDL: Error downloading captcha img.');
			$mimetype = (preg_match('@image/[\w+]+@', $headers, $mimetype) ? $mimetype[0] : 'image/png');
			return $this->EnterCaptcha("data:$mimetype;base64," . base64_encode($imgBody), $data);
		}
		if (empty($_POST['vvcid'])) html_error('FreeDL: Captcha\'s vvcid not Found.');
		if (empty($_POST['captcha'])) html_error('FreeDL: You didn\'t enter the image verification code.');
		$this->cookie = StrToCookies(decrypt(urldecode($_POST['cookie'])));
		$this->cookie['sdlanguageid'] = '2';

		$post = array('vvcid' => urlencode($_POST['vvcid']), 'verifycode' => urlencode($_POST['captcha']), 'FREE' => 'Low+Speed+Download');
		$page = $this->GetPage($this->link, $this->cookie, $post);
		is_present($page, 'Wrong CAPTCHA', 'Incorrect CAPTCHA Answer.');
		is_present($page, '>Download limit for free user.<', 'FreeDL Limit Reached.');
		if (preg_match('@A file was recently downloaded from your IP address. No less than \d+ \w+ should pass before next download.@i', $page, $err)) html_error($err[0]);

		// Search Download Link
		if (!preg_match($this->DLRegexp, $page, $DL)) {
			if (!preg_match('@document\.getElementById\("wait_url"\)\.innerHTML\s*=\s*unescape\(\'([^\']+)\'\)@', $page, $inner)) html_error('Download Link Container not Found.');
			$inner = rawurldecode($inner[1]);
			if (!preg_match($this->DLRegexp, $inner, $DL)) html_error('Download Link not Found.');
		}

		// Countdown
		if (!preg_match('@var\s+sec\s*=\s*(\d+)\s*;@', $page, $cD)) html_error('Countdown not Found.');
		$this->CountDown($cD[1]);

		return $this->RedirectDownload($DL[0], urldecode(parse_url($DL[0], PHP_URL_PATH)));
	}

	private function PremiumDL() {
		$page = $this->GetPage($this->link, $this->cookie);
		$this->chkPremCaptcha($page);
		is_present($page, 'Sorry, you spent downloads limit on urls/files per 24 hours.', 'Premium Download Limit Reached.');
		if (!preg_match($this->DLRegexp, $page, $DL)) html_error('Download Link not Found.');
		return $this->RedirectDownload($DL[0], urldecode(parse_url($DL[0], PHP_URL_PATH)));
	}

	private function chkPremCaptcha(&$page) {
		if (!preg_match('@/includes/vvc\.php\?vvcid=(\d+)@i', $page, $vvc)) return false;

		if (empty($_POST['step']) || $_POST['step'] != '3') {
			$data = $this->DefaultParamArr($this->link, $this->cookie, 1, 1);
			$data['step'] = '3';
			$data['vvcid'] = $vvc[1];
			if ($this->pA) {
				$data['pA_encrypted'] = 'true';
				$data['premium_user'] = urlencode(encrypt($_REQUEST['premium_user']));
				$data['premium_pass'] = urlencode(encrypt($_REQUEST['premium_pass']));
			}
			list($headers, $imgBody) = explode("\r\n\r\n", $this->GetPage($this->baseUrl.$vvc[0]), 2);
			if (substr($headers, 9, 3) != '200') html_error('PremiumDL: Error downloading captcha img.');
			$mimetype = (preg_match('@image/[\w+]+@', $headers, $mimetype) ? $mimetype[0] : 'image/png');
			return $this->EnterCaptcha("data:$mimetype;base64," . base64_encode($imgBody), $data);
		}
		if (empty($_POST['vvcid'])) html_error('PremiumDL: Captcha\'s vvcid not Found.');
		if (empty($_POST['captcha'])) html_error('PremiumDL: You didn\'t enter the image verification code.');

		$post = array('vvcid' => urlencode($_POST['vvcid']), 'verifycode' => urlencode($_POST['captcha']), 'prem_plus' => 'Next');
		$page = $this->GetPage($this->link, $this->cookie, $post);
		is_present($page, 'Wrong CAPTCHA', 'Incorrect CAPTCHA Answer.');
		return true;
	}

	private function loginTOTP(&$page) {
		if (empty($_POST['step']) || $_POST['step'] != '2') {
			if (strpos($page, "' name='user_otp_code'") === false) return false;
			// Found otp input field
			$data = $this->DefaultParamArr($this->link, $this->cookie, 1, 1);
			$data['step'] = '2';
			if ($this->pA) {
				$data['pA_encrypted'] = 'true';
				$data['premium_user'] = urlencode(encrypt($_REQUEST['premium_user']));
				$data['premium_pass'] = urlencode(encrypt($_REQUEST['premium_pass']));
			}
			echo "<form action='{$_SERVER['SCRIPT_NAME']}' method='POST'>\n";
			foreach ($data as $key => $value) echo "<input type='hidden' name='$key' value='" . htmlspecialchars($value, ENT_QUOTES) . "' />\n";
			echo 'Enter login TOTP' . ($this->pA ? ' for ' . htmlspecialchars($_REQUEST['premium_user']) : '') . ":<br />\n<input type='text' pattern='[0-9]{6}' name='totp_code' placeholder='Enter OTP token here' autofocus='autofocus' required='required' />\n<input type='submit' />\n</form>";
			return html_error('Login TOTP (Time-based One-Time Password) Required');
		}
		$_POST['step'] = false;
		if (empty($_POST['totp_code']) || !preg_match('/^\d{6}$/', $_POST['totp_code'])) html_error('Empty TOTP Code or Incomplete.');

		$page = $this->GetPage($this->baseUrl . '/', $this->cookie, array('user_otp_code' => $_POST['totp_code']));
		is_present($page, "' name='user_otp_code'", 'Incorrect Login TOTP Token.');
		$this->cookie = GetCookiesArr($page, $this->cookie); // Update cookies
		$this->cookie['sdlanguageid'] = '2';

		return true;
	}

	private function Login($user, $pass) {
		$post = array('login' => 'login');
		$post['loginemail'] = urlencode($user);
		$post['loginpassword'] = urlencode($pass);
		$post['submit'] = 'login';
		$post['rememberme'] = 'on';
		$page = $this->GetPage($this->baseUrl . '/', $this->cookie, $post);
		is_present($page, 'E-mail not found.', 'Login Error: Wrong Email.');
		is_present($page, 'Incorrect password', 'Login Error: Wrong Password.');
		$this->cookie = GetCookiesArr($page, $this->cookie); // Update cookies
		$this->cookie['sdlanguageid'] = '2';
		if (empty($this->cookie['sduserid'])) html_error('Login Error: "sduserid" cookie not found.');
		if (empty($this->cookie['sdpassword'])) html_error('Login Error: "sdpassword" cookie not found.');

		$this->SaveCookies($user, $pass); // Update cookies file

		if ($this->loginTOTP($page)) $this->SaveCookies($user, $pass);

		if (stripos($page, "<img src='/images/i_premium.png'") === false) {
			$this->changeMesg('<br /><b>Account isn\'t premium?</b>', true);

			$this->page = $this->GetPage($this->link, $this->cookie);
			$this->cookie = GetCookiesArr($this->page, $this->cookie);
			$this->cookie['sdlanguageid'] = '2';

			return $this->FreeDL();
		} else return $this->PremiumDL();
	}

	private function IWillNameItLater($cookie, $decrypt = true) {
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

	private function CookieLogin($user, $pass, $filename = 'depfile_dl.php') {
		global $secretkey;
		if (empty($user) || empty($pass)) html_error('Login Failed: User or Password is empty.');
		$user = strtolower($user);

		$filename = DOWNLOAD_DIR . basename($filename);
		if (!file_exists($filename)) return $this->Login($user, $pass);

		$file = file($filename);
		$savedcookies = unserialize($file[1]);
		unset($file);

		$hash = hash('crc32b', $user.':'.$pass);
		if (is_array($savedcookies) && array_key_exists($hash, $savedcookies)) {
			$_secretkey = $secretkey;
			$secretkey = hash('crc32b', $pass).sha1($user.':'.$pass).hash('crc32b', $user); // A 56 char key should be safer. :D
			$testCookie = (decrypt(urldecode($savedcookies[$hash]['enc'])) == 'OK') ? $this->IWillNameItLater($savedcookies[$hash]['cookie']) : '';
			$secretkey = $_secretkey;
			if (empty($testCookie) || (is_array($testCookie) && count($testCookie) < 1)) return $this->Login($user, $pass);

			$page = $this->GetPage($this->baseUrl.'/', $testCookie);
			if (stripos($page, '\'/uploads/logout\'>') === false) return $this->Login($user, $pass);
			$this->cookie = GetCookiesArr($page, $testCookie); // Update cookies
			$this->cookie['sdlanguageid'] = '2';
			$this->SaveCookies($user, $pass); // Update cookies file
			if ($this->loginTOTP($page)) $this->SaveCookies($user, $pass);
			if (stripos($page, "<img src='/images/i_premium.png'") === false) {
				$this->changeMesg('<br /><b>Account isn\'t premium?</b>', true);
				$this->page = $this->GetPage($this->link, $this->cookie);
				$this->cookie = GetCookiesArr($this->page, $this->cookie);
				$this->cookie['sdlanguageid'] = '2';
				return $this->FreeDL();
			} else return $this->PremiumDL();
		}
		return $this->Login($user, $pass);
	}

	private function SaveCookies($user, $pass, $filename = 'depfile_dl.php') {
		global $secretkey;
		$maxdays = 31; // Max days to keep extra cookies saved
		$filename = DOWNLOAD_DIR . basename($filename);
		if (file_exists($filename)) {
			$file = file($filename);
			$savedcookies = unserialize($file[1]);
			unset($file);

			if (is_array($savedcookies)) {
				// Remove old cookies
				foreach ($savedcookies as $k => $v) if (empty($v['time']) || time() - $v['time'] >= ($maxdays * 24 * 60 * 60)) unset($savedcookies[$k]);
			} else $savedcookies = array();
		} else $savedcookies = array();
		$hash = hash('crc32b', $user.':'.$pass);
		$_secretkey = $secretkey;
		$secretkey = hash('crc32b', $pass).sha1($user.':'.$pass).hash('crc32b', $user); // A 56 char key should be safer. :D
		$savedcookies[$hash] = array('time' => time(), 'enc' => urlencode(encrypt('OK')), 'cookie' => $this->IWillNameItLater($this->cookie, false));
		$secretkey = $_secretkey;

		file_put_contents($filename, "<?php exit(); ?>\r\n" . serialize($savedcookies), LOCK_EX);
	}
}

// [28-4-2016] Written by Th3-822.

?>