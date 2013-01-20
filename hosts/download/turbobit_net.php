<?php

if (!defined('RAPIDLEECH')) {
	require_once('index.html');
	exit;
}

class turbobit_net extends DownloadClass {
	private $link, $page, $cookie, $pA, $DLregexp;
	public function Download($link) {
		global $premium_acc, $Referer;
		if (!preg_match('@^https?://(?:[^/]+\.)?turbobit\.(?:(?:net)|(?:ru))/(?:download/free/)?(\w+)(?:\.html)?@i', str_ireplace('unextfiles.com', 'turbobit.net', $link), $id)) html_error('Error: Invalid link entered.');
		$this->link = $Referer = 'http://turbobit.net/' . $id[1] . '.html';
		$this->pA = (empty($_REQUEST['premium_user']) || empty($_REQUEST['premium_pass']) ? false : true);
		$this->cookie = array('user_lang' => 'en');
		$this->RDregexp = '@(https?://(?:[^/\"\'\t\r\n<>]+\.)?turbobit\.(?:(?:ru)|(:?net))(?:\:\d+)?)?//?download/redirect/[^\"\'\t\r\n<>]+@i';
		$this->DLregexp = '@https?://s\d+\.turbobit\.(?:(?:ru)|(:?net))(?:\:\d+)?/download\.php\?[^\"\'\t\r\n<>]+@i';

		if (empty($_POST['step'])) {
			$this->page = $this->GetPage($this->link);
			is_present($this->page, 'This document was not found in System', 'File Not Found.'); // 404
			is_present($this->page, 'File was not found. It could possibly be deleted.', 'The requested file is not found.');
			$this->cookie = GetCookiesArr($this->page, $this->cookie);
		}

		if (($_REQUEST['premium_acc'] == 'on' && ($this->pA || (!empty($premium_acc['turbobit_net']['user']) && !empty($premium_acc['turbobit_net']['pass']))))) {
			$user = ($this->pA ? $_REQUEST['premium_user'] : $premium_acc['turbobit_net']['user']);
			$pass = ($this->pA ? $_REQUEST['premium_pass'] : $premium_acc['turbobit_net']['pass']);
			if ($this->pA && !empty($_POST['pA_encrypted'])) {
				$user = decrypt(urldecode($user));
				$pass = decrypt(urldecode($pass));
				unset($_POST['pA_encrypted']);
			}
			return $this->CookieLogin($user, $pass);
		} else {
			$this->link = 'http://turbobit.net/download/free/' . $id[1];
			return $this->FreeDL();
		}
	}

	private function FreeDL() {
		$_POST['step'] = empty($_POST['step']) ? '' : $_POST['step'];
		switch ($_POST['step']) {
			case '1': { // Send Captcha && Get Countdown
				if (empty($_POST['captcha'])) html_error('You didn\'t enter the image-verification code.');
				$this->cookie = StrToCookies(decrypt(urldecode($_POST['cookie'])));

				$post = array();
				$post['captcha_response'] = urlencode($_POST['captcha']);
				$post['captcha_type'] = urlencode($_POST['c_type']);
				$post['captcha_subtype'] = (!empty($_POST['c_subtype']) ? urlencode($_POST['c_subtype']) : '');
				$page = $this->GetPage($this->link, $this->cookie, $post);

				is_present($page, '>Incorrect, try again!<', 'Error: Wrong CAPTCHA entered.');
				is_present($page, 'Looks like your browser has disabled cookies.', 'Error: Invalid cookies.');

				if (!preg_match('@\W(?:min)?Limit[\s\t]*:[\s\t]*([\d\s\t\r\n\+\-\*/\(\)]+)[\s\t]*[\,\;]@i', $page, $count)) html_error('Countdown not found.');
				$data = $this->DefaultParamArr($this->link, encrypt(CookiesToStr($this->cookie)));
				$data['step'] = '2';
				$this->JSCountdown($count[1], $data);
				break;
			}
			case '2': { // Download
				$this->cookie = StrToCookies(decrypt(urldecode($_POST['cookie'])));
				$page = $page = $this->GetPage(str_replace('/download/free/', '/download/getLinkTimeout/', $this->link), $this->cookie, 0, $this->link . "\r\nX-Requested-With: XMLHttpRequest");

				if (preg_match($this->RDregexp, $page, $redir)) {
					$redir = (empty($redir[1])) ? 'http://turbobit.net'.$redir[0] : $redir[0];
					$page = $this->GetPage($redir, $this->cookie);
					if (!preg_match($this->DLregexp, $page, $dllink)) html_error('Download-Link not Found.');
				} elseif (!preg_match($this->DLregexp, $page, $dllink)) html_error('Redirect-Link not Found.');
				$this->RedirectDownload(html_entity_decode($dllink[0]), 'turbobit_fr', $this->cookie);
				break;
			}
			default : { // Get Captcha
				$page = $this->GetPage($this->link, $this->cookie);

				if (preg_match('@\W(?:min)?Limit[\s\t]*:[\s\t]*([\d\s\t\r\n\+\-\*/\(\)]+)[\s\t]*[\,\;]@i', $page, $count)) {
					$data = $this->DefaultParamArr($this->link, encrypt(CookiesToStr($this->cookie)));
					$this->JSCountdown($count[1], $data, 'FreeDL limit reached');
				}

				if (!preg_match('@(https?://[^/\r\n\t\s\'\"<>]+)?/captcha/[^\r\n\t\s\'\"<>]+@i', $page, $imgurl)) html_error('Error: CAPTCHA not found.');
				$imgurl = (empty($imgurl[1])) ? 'http://turbobit.net'.$imgurl[0] : $imgurl[0];
				$imgurl = html_entity_decode($imgurl);

				if (!preg_match('@\Wvalue\s*=\s*[\'\"]([^\'\"\r\n<>]+)[\'\"]\s+name\s*=\s*[\'\"]captcha_type[\'\"]@i', $page, $c_type) || !preg_match('@\Wvalue\s*=\s*[\'\"]([^\'\"\r\n<>]*)[\'\"]\s+name\s*=\s*[\'\"]captcha_subtype[\'\"]@i', $page, $c_subtype)) html_error('CAPTCHA data not found');

				//Download captcha img.
				$capt_page = $this->GetPage($imgurl, $this->cookie);
				$capt_img = substr($capt_page, strpos($capt_page, "\r\n\r\n") + 4);
				$imgfile = DOWNLOAD_DIR . 'turbobit_captcha.png';

				if (file_exists($imgfile)) unlink($imgfile);
				if (!write_file($imgfile, $capt_img)) html_error('Error getting CAPTCHA image');
				unset($capt_page, $capt_img);

				$data = $this->DefaultParamArr($this->link, encrypt(CookiesToStr($this->cookie)));
				$data['step'] = '1';
				$data['c_type'] = urlencode($c_type[1]);
				$data['c_subtype'] = urlencode($c_subtype[1]);
				$this->EnterCaptcha($imgfile.'?'.time(), $data);
				exit;
			}
		}
	}

	private function PremiumDL($usercookie = false) {
		$page = $this->GetPage($this->link, $this->cookie);
		if (preg_match($this->RDregexp, $page, $redir)) {
			$redir = (empty($redir[1])) ? 'http://turbobit.net'.$redir[0] : $redir[0];
			$page = $this->GetPage($redir, $this->cookie);

			if (!preg_match($this->DLregexp, $page, $dllink)) html_error('Download Link not Found.');
		} elseif (!preg_match($this->DLregexp, $page, $dllink)) html_error('Redirect Link not Found.');
		$this->RedirectDownload(html_entity_decode($dllink[0]), 'turbobit_pr', $this->cookie);
	}

	private function Login($user, $pass) {
		$purl = 'http://turbobit.net/';
		if (!empty($_POST['step']) && $_POST['step'] == '1') {
			if (empty($_POST['captcha'])) html_error('You didn\'t enter the image verification code.');
			$this->cookie = StrToCookies(decrypt(urldecode($_POST['cookie'])));

			$post = array();
			$post['user%5Blogin%5D'] = urlencode($user);
			$post['user%5Bpass%5D'] = urlencode($pass);
			$post['user%5Bcaptcha_response%5D'] = urlencode($_POST['captcha']);
			$post['user%5Bcaptcha_type%5D'] = urlencode($_POST['c_type']);
			$post['user%5Bcaptcha_subtype%5D'] = (!empty($_POST['c_subtype']) ? urlencode($_POST['c_subtype']) : '');
			$post['user%5Bmemory%5D'] = 'on';
			$post['user%5Bsubmit%5D'] = 'Login';

			$page = $this->GetPage($purl.'user/login', $this->cookie, $post, $purl.'login');
			$this->cookie = GetCookiesArr($page, $this->cookie);

			$x = 0;
			while ($x < 3 && stripos($page, "\nLocation: ") !== false && preg_match('@\nLocation: ((https?://[^/\r\n]+)?/[^\r\n]*)@i', $page, $redir)) {
				$redir = (empty($redir[2])) ? 'http://turbobit.net'.$redir[1] : $redir[1];
				$page = $this->GetPage($redir, $this->cookie);
				$this->cookie = GetCookiesArr($page, $this->cookie);
				$x++;
			}

			is_present($page, 'Incorrect login or password', 'Login Failed: Login/Password incorrect.');
			is_present($page, 'Incorrect verification code', 'Login Failed: Wrong CAPTCHA entered.');

			if(empty($redir) || $redir != $purl) $page = $this->GetPage($purl, $this->cookie, 0, $purl);
			if (stripos($page, '/user/logout">Logout<') === false) html_error('Login Failed.');

			$this->SaveCookies($user, $pass); // Update cookies file
			is_present($page, '<u>Turbo Access</u> denied', 'Login Failed: Account isn\'t premium.');
			return $this->PremiumDL();
		} else {
			$post = array();
			$post['user%5Blogin%5D'] = urlencode($user);
			$post['user%5Bpass%5D'] = urlencode($pass);
			$post['user%5Bmemory%5D'] = 'on';
			$post['user%5Bsubmit%5D'] = 'Login';
			$page = $this->GetPage($purl.'user/login', $this->cookie, $post, $purl);
			$this->cookie = GetCookiesArr($page, $this->cookie);

			if (!empty($this->cookie['user_isloggedin']) && $this->cookie['user_isloggedin'] == '1') {
				$page = $this->GetPage($purl, $this->cookie, 0, $purl);
				$this->SaveCookies($user, $pass); // Update cookies file
				is_present($page, '<u>Turbo Access</u> denied', 'Login Failed: Account isn\'t premium');
				return $this->PremiumDL();
			}

			$x = 0;
			while ($x < 3 && stripos($page, "\nLocation: ") !== false && preg_match('@\nLocation: ((https?://[^/\r\n]+)?/[^\r\n]*)@i', $page, $redir)) {
				$redir = (empty($redir[2])) ? 'http://turbobit.net'.$redir[1] : $redir[1];
				$page = $this->GetPage($redir, $this->cookie);
				$this->cookie = GetCookiesArr($page, $this->cookie);
				$x++;
			}
			if ($x < 1) html_error('Login redirect not found');

			is_present($page, 'Incorrect login or password', 'Login Failed: Login/Password incorrect');

			if (!preg_match('@(https?://[^/\r\n\t\s\'\"<>]+)?/captcha/[^\r\n\t\s\'\"<>]+@i', $page, $imgurl)) {
				if (stripos($page, '/user/logout">Logout<') !== false) {
					$this->SaveCookies($user, $pass); // Update cookies file
					is_present($page, '<u>Turbo Access</u> denied', 'Login Failed: Account isn\'t premium');
					return $this->PremiumDL();
				} else html_error('CAPTCHA not found.');
			}
			$imgurl = (empty($imgurl[1])) ? 'http://turbobit.net'.$imgurl[0] : $imgurl[0];
			$imgurl = html_entity_decode($imgurl);

			if (!preg_match('@\Wvalue\s*=\s*[\'\"]([^\'\"\r\n<>]+)[\'\"]\s+name\s*=\s*[\'\"]user\[captcha_type\][\'\"]@i', $page, $c_type) || !preg_match('@\Wvalue\s*=\s*[\'\"]([^\'\"\r\n<>]*)[\'\"]\s+name\s*=\s*[\'\"]user\[captcha_subtype\][\'\"]@i', $page, $c_subtype)) html_error('CAPTCHA data not found.');


			//Download captcha img.
			$capt_page = $this->GetPage($imgurl, $this->cookie);
			$capt_img = substr($capt_page, strpos($capt_page, "\r\n\r\n") + 4);
			$imgfile = DOWNLOAD_DIR . 'turbobit_captcha.png';

			if (file_exists($imgfile)) unlink($imgfile);
			if (!write_file($imgfile, $capt_img)) html_error('Error getting CAPTCHA image.');
			unset($capt_page, $capt_img);

			$data = $this->DefaultParamArr($this->link, encrypt(CookiesToStr($this->cookie)));
			$data['step'] = '1';
			$data['c_type'] = urlencode($c_type[1]);
			$data['c_subtype'] = urlencode($c_subtype[1]);
			$data['premium_acc'] = 'on'; // I should add 'premium_acc' to DefaultParamArr()
			if ($this->pA) {
				$data['pA_encrypted'] = 'true';
				$data['premium_user'] = urlencode(encrypt($user)); // encrypt() will keep this safe.
				$data['premium_pass'] = urlencode(encrypt($pass)); // And this too.
			}
			$this->EnterCaptcha($imgfile.'?'.time(), $data);
			exit;
		}
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

	private function CookieLogin($user, $pass, $filename = 'turbobit_dl.php') {
		global $secretkey;
		if (empty($user) || empty($pass)) html_error('Login Failed: User or Password is empty.');

		$filename = DOWNLOAD_DIR . basename($filename);
		if (!file_exists($filename)) return $this->Login($user, $pass);

		$file = file($filename);
		$savedcookies = unserialize($file[1]);
		unset($file);

		$hash = hash('crc32b', $user.':'.$pass);
		if (array_key_exists($hash, $savedcookies)) {
			$_secretkey = $secretkey;
			$secretkey = sha1($user.':'.$pass);
			$this->cookie = (decrypt(urldecode($savedcookies[$hash]['enc'])) == 'OK') ? $this->IWillNameItLater($savedcookies[$hash]['cookie']) : '';
			$secretkey = $_secretkey;
			if (empty($this->cookie) || (is_array($this->cookie) && count($this->cookie) < 1)) return $this->Login($user, $pass);

			$page = $this->GetPage('http://turbobit.net/', $this->cookie);
			if (stripos($page, '/user/logout">Logout<') === false) return $this->Login($user, $pass);
			is_present($page, '<u>Turbo Access</u> denied', 'Account isn\'t premium.');
			$this->SaveCookies($user, $pass); // Update cookies file
			return $this->PremiumDL();
		}
		return $this->Login($user, $pass);
	}

	private function SaveCookies($user, $pass, $filename = 'turbobit_dl.php') {
		global $secretkey;
		$maxdays = 7; // Max days to keep cookies saved
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
}

//[9-01-2013] Written by Th3-822.

?>