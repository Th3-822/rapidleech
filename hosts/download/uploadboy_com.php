<?php

if (!defined('RAPIDLEECH')) {
	require_once('index.html');
	exit();
}

if (!file_exists(HOST_DIR . 'download/GenericXFS_DL.php')) html_error('Cannot load "'.htmlentities(HOST_DIR).'download/GenericXFS_DL.php" (File doesn\'t exists)');
require_once(HOST_DIR . 'download/GenericXFS_DL.php');

class uploadboy_com extends GenericXFS_DL {
	public $pluginVer = 10;
	public function Download($link) {
		$this->wwwDomain = false; // Switch to true if filehost forces it's domain with www.
		$this->cname = 'xfss'; // Session cookie name
		$this->sslLogin = false; // Force https on login.
		$this->embedDL = false; // Try to unpack player's js for finding download link. (Only hosts with video player)
		$this->unescaper = false; // Enable JS unescape decoder.

		$link = parse_url($link);
		$link['host'] = 'uploadboy.com';
		$link = rebuild_url($link);
		$this->Start($link);
	}

	// Edited to add login captcha decoder.
	protected function sendLogin($post) {
		if (empty($_POST['step']) || $_POST['step'] != 'L') {
			$purl = (!empty($this->sslLogin) ? 'https://'.$this->host.'/' : $this->purl) . '?op=login';
			$page = $this->GetPage($purl, $this->cookie);
			if (!($form = cut_str($page, '<form', '</form>'))) html_error('Cannot find login form.');
			$post['rand'] = cut_str($form, 'name="rand" value="', '"');

			if (!empty($post['rand'])) {
				if (substr_count($form, "<span style='position:absolute;padding-left:") > 3 && preg_match_all("@<span style='[^\'>]*padding-left\s*:\s*(\d+)[^\'>]*'[^>]*>((?:&#\w+;)|(?:\d))</span>@i", $form, $txtCaptcha)) {
					// Text Captcha (decodeable)
					$txtCaptcha = array_combine($txtCaptcha[1], $txtCaptcha[2]);
					ksort($txtCaptcha, SORT_NUMERIC);
					$txtCaptcha = trim(html_entity_decode(implode($txtCaptcha), ENT_QUOTES, 'UTF-8'));
					$post['code'] = $txtCaptcha;

					// Don't remove this sleep or you will only see "Error Decoding Captcha. [Login]"
					sleep(3); // 2 or 3 seconds.
				} else if (preg_match('@(https?://(?:[\w\-]+\.)+[\w\-]+(?:\:\d+)?)?/captchas/[\w\-]+\.(?:jpe?g|png|gif)@i', $form, $gdCaptcha)) {
					// gd Captcha (untested, but should work)
					if (empty($gdCaptcha[1])) $gdCaptcha[0] = $this->scheme . '://' . $this->host . $gdCaptcha[0];
					list($headers, $imgBody) = explode("\r\n\r\n", $this->GetPage($gdCaptcha[0]), 2);
					if (substr($headers, 9, 3) != '200') html_error('[Login] Error downloading CAPTCHA img.');
					$mimetype = (preg_match('@image/[\w+]+@', $headers, $mimetype) ? $mimetype[0] : 'image/jpg');

					$data = $this->DefaultParamArr($this->link);
					$data['rand'] = $post['rand'];
					$data['step'] = 'L';
					if ($this->pA) {
						$data['pA_encrypted'] = 'true';
						$data['premium_user'] = urlencode(encrypt($_REQUEST['premium_user'])); // encrypt() will keep this safe.
						$data['premium_pass'] = urlencode(encrypt($_REQUEST['premium_pass'])); // And this too.
					}
					return $this->EnterCaptcha("data:$mimetype;base64,".base64_encode($imgBody), $data);
				} else html_error('Login captcha not found.');
			}
		} else {
			$_POST['step'] = false;
			if (empty($_POST['captcha'])) html_error('[Login] You didn\'t enter the image verification code.');
			$post['rand'] = urlencode($_POST['rand']);
			$post['code'] = urlencode($_POST['captcha']);
		}
		return parent::sendLogin($post);
	}

	// Added login captcha error msg.
	protected function checkLogin($page) {
		is_present($page, 'Wrong%20captcha%20code', (empty($_POST['rand']) ? 'Error: Error Decoding Captcha. [Login]' : 'Error: Wrong Captcha Entered. [Login]'));
		return parent::checkLogin($page);
	}

	// They force https only on this page, it's weird.
	protected function isLoggedIn() {
		$page = $this->GetPage('https://'.$this->host.'/?op=my_account', $this->cookie, 0, $this->purl);
		if (stripos($page, '/?op=logout') === false && stripos($page, '/logout') === false) return false;
		return $page;
	}
}

// Written by Th3-822.

?>