<?php

if (!defined('RAPIDLEECH')) {
	require_once('index.html');
	exit;
}

class openload_co extends DownloadClass {
	private $page, $cookie = array(), $elink, $pA, $DLRegexp = '@https?://\w+\.(?:openload\.co|oloadcdn\.net)/dl/[^\t\r\n\'\"<>\?]+@i';
	private $ignoreApiDLCaptcha = false;
	public function Download($link) {
		if (!preg_match('@https?://openload\.co/f/([\w-]+)@i', str_ireplace(array('://www.openload.co', '/embed/'), array('://openload.co', '/f/'), $link), $fid)) html_error('Invalid link?.');
		$this->link = $GLOBALS['Referer'] = str_ireplace('http://', 'https://', $fid[0]);
		$this->elink = str_ireplace('/f/', '/embed/', $this->link);
		$this->fid = $fid[1];

		if (empty($_POST['step'])) $this->testLink();
		else if ($_POST['step'] == '1') return $this->ApiDLPost();

		$this->pA = (empty($_REQUEST['premium_user']) || empty($_REQUEST['premium_pass']) ? false : true);
		if (($_REQUEST['premium_acc'] == 'on' && ($this->pA || (!empty($GLOBALS['premium_acc']['openload_co']['user']) && !empty($GLOBALS['premium_acc']['openload_co']['pass']))))) {
			$user = ($this->pA ? $_REQUEST['premium_user'] : $GLOBALS['premium_acc']['openload_co']['user']);
			$pass = ($this->pA ? $_REQUEST['premium_pass'] : $GLOBALS['premium_acc']['openload_co']['pass']);
			if ($this->pA && !empty($_POST['pA_encrypted'])) {
				$user = decrypt(urldecode($user));
				$pass = decrypt(urldecode($pass));
				unset($_POST['pA_encrypted']);
			}
			return $this->Login($user, $pass);
		} else return $this->AnonDL();
	}

	private function testLink() {
		$this->page = $this->GetPage($this->link, $this->cookie);
		$this->cookie = GetCookiesArr($this->page, $this->cookie);
		if (stripos($this->page, 'We can\'t find the file you are looking for.') !== false) {
			$this->page = $this->GetPage($this->elink, $this->cookie);
			is_present($this->page, 'We can\'t find the file you are looking for.', 'File Not Found.');
			$this->cookie = GetCookiesArr($this->page, $this->cookie);
		}
	}

	private function AnonDL() {
		// ApiDL
		try {
			return $this->tryApiDL();
		} catch (Exception $e) {
			$this->changeMesg(sprintf('<br /><b>Anon_ApiDL Failed: "%s"</b>', htmlspecialchars($e->getMessage(), ENT_QUOTES)), true);
		}
		// WebDL
		return $this->WebDL();
	}

	private function tryApiDL($login = 0, $key = 0) {
		$query = array('file' => $this->fid);
		if (!empty($login) && !empty($key)) {
			$query['login'] = $login;
			$query['key'] = $key;
		}
		$ticket = $this->ApiReq('file/dlticket', $query);
		if (empty($ticket['ticket'])) throw new Exception('Token not Found');
		if (!empty($ticket['wait_time']) && $ticket['wait_time'] > 0) $this->CountDown($ticket['wait_time']);
		if (!empty($ticket['captcha_url'])) {
			// Got CAPTCHA
			if ($this->ignoreApiDLCaptcha) throw new Exception('ignoreApiDLCaptcha is true');
			$data = $this->DefaultParamArr($this->link);
			$data['step'] = '1';
			$data['ticket'] = $ticket['ticket'];
			list($headers, $imgBody) = explode("\r\n\r\n", $this->GetPage($ticket['captcha_url']), 2);
			if (substr($headers, 9, 3) != '200') throw new Exception('Error downloading captcha img');
			$mimetype = (preg_match('@image/[\w+]+@', $headers, $mimetype) ? $mimetype[0] : 'image/png');
			return $this->EnterCaptcha("data:$mimetype;base64," . base64_encode($imgBody), $data);
		}
		// Download
		unset($query['login'], $query['key']);
		$query['ticket'] = $ticket['ticket'];
		$DL = $this->ApiReq('file/dl', $query);
		if (empty($DL['url'])) throw new Exception('Download Link not Found');
		return $this->RedirectDownload($DL['url'], 'T8_ol_adl');
	}

	private function ApiDLPost() {
		if (empty($_POST['ticket'])) html_error('ApiDLPost: Ticket not Found.');
		if (empty($_POST['captcha'])) html_error('You didn\'t enter the image verification code.');
		try {
			$DL = $this->ApiReq('file/dl', array('file' => $this->fid, 'ticket' => $_POST['ticket'], 'captcha_response' => $_POST['captcha']));
		} catch (Exception $e) {
			html_error(sprintf('ApiDLPost Error: "%s"</b>', htmlspecialchars($e->getMessage(), ENT_QUOTES)));
		}
		if (empty($DL['url'])) html_error('ApiDLPost: Download Link not Found.');
		return $this->RedirectDownload($DL['url'], 'T8_ol_adl_c');
	}

	private function ulDecode($str) {
		$len = strlen($str);
		for ($i = 0; $i < $len; $i++) {
			$y = ord($str[$i]);
			if (($y >= 33) && ($y <= 126)) $str[$i] = chr(33 + (($y + 14) % 94));
		}
		return substr($str, 0, $len - 1) . chr(ord(substr($str, -1)) + 2);
	}

	private function testStreamToken($token) {
		if (!preg_match("@^{$this->fid}~\d{10}~(?:(?:25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)\.){3}(?:25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)~[\w\-]{8}$@", $token)) return false;
		$page = $this->GetPage("https://openload.co/stream/$token?mime=true");
		if (!preg_match($this->DLRegexp, $page, $DL)) return html_error('Stream Download-Link Not Found.');
		if (strpos($DL[0], '/KDA_8nZ2av4/x.mp4') !== false) return false; // No, no Pidgeons
		return $DL[0];
	}

	private function WebDL() {
		if (preg_match($this->DLRegexp, $this->page, $DL)) return $this->RedirectDownload($DL[0], urldecode(parse_url($DL[0], PHP_URL_PATH)));
		if (!preg_match('@<span\s+id="(\w+)">\s*(.+?)\s*</span>\s*<span\s+id="\1x">\s*(.+?)\s*</span>@', $this->page, $token)) html_error('Obsfuscated Download-Token Not Found.');
		$token[2] = $this->ulDecode(html_entity_decode($token[2], ENT_QUOTES, 'UTF-8')); // 'y' token
		$token[3] = $this->ulDecode(html_entity_decode($token[3], ENT_QUOTES, 'UTF-8')); // 'x' token
		if (!($DL = $this->testStreamToken($token[2])) && !($DL = $this->testStreamToken($token[3]))) html_error('Token Decode Failed, Plugin Needs to be Updated.');
		return $this->RedirectDownload($DL, urldecode(parse_url($DL, PHP_URL_PATH)));
	}

	private function Login($user, $pass) {
		$isApiLogin = (strpos($user, '@') === false ? true : false);

		// ApiDL
		if ($isApiLogin || (!$this->pA && !empty($GLOBALS['premium_acc']['openload_co']['apiuser']) && !empty($GLOBALS['premium_acc']['openload_co']['apipass']))) {
			try {
				if ($isApiLogin) return $this->tryApiDL($user, $pass);
				else return $this->tryApiDL($GLOBALS['premium_acc']['openload_co']['apiuser'], $GLOBALS['premium_acc']['openload_co']['apipass']);
			} catch (Exception $e) {
				if ($this->pA) html_error('Login_ApiDL Failed: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES));
				else $this->changeMesg(sprintf('<br /><b>Login_ApiDL Failed: "%s"</b>', htmlspecialchars($e->getMessage(), ENT_QUOTES)), true);
			}
		}

		// Login
		if (!preg_match('@<meta name="csrf-token" content="([\w+/=]+)"@i', $this->page, $token)) {
			$page = $this->GetPage('https://openload.co/login', $this->cookie);
			$this->cookie = GetCookiesArr($page, $this->cookie);
			if (!preg_match('@<meta name="csrf-token" content="([\w+/=]+)"@i', $page, $token)) html_error('Login CSRF Token not Found.');
		}
		$post = array('_csrf' => $token[1]);
		$post['LoginForm%5Bemail%5D'] = urlencode($user);
		$post['LoginForm%5Bpassword%5D'] = urlencode($pass);
		$post['LoginForm%5BrememberMe%5D'] = 1;

		$page = $this->GetPage('https://openload.co/login', $this->cookie, $post);
		is_present($page, 'Incorrect username or password.', 'Login Error: Wrong Email/Password.');
		$this->cookie = GetCookiesArr($page, $this->cookie);
		if (empty($this->cookie['_identity'])) html_error('Login Error: Cookie "_identity" not Found.');

		// Update $this->page
		$this->testLink();
		// WebDL
		return $this->WebDL();
	}

	private function is_present($lpage, $mystr, $strerror = '') {
		if (stripos($lpage, $mystr) !== false) throw new Exception(!empty($strerror) ? $strerror : $mystr);
	}

	private function ApiReq($path, $query = array()) {
		if (!is_array($query)) $query = array();

		$query = !empty($query) ? '?'.http_build_query($query, '', '&') : '';
		$page = $this->GetPage("https://api.openload.co/1/$path$query", 0, 0, 'https://openload.co/');
		$reply = $this->json2array($page, "ApiReq($path) Error");

		switch ($reply['status']) {
			case 200: break;
			case 404: case 451: return html_error("[ApiReq($path)] File Deleted or Not Found.");
			case 509: throw new Exception(stripos($reply['msg'], 'bandwidth usage too high (peak hours)') !== false ? 'BW Usage Too High' : 'BW Limit Reached');
			case 403:
				$this->is_present($reply['msg'], 'Authentication failed', 'Incorrect API Login');
				$this->is_present($reply['msg'], 'Captcha not solved correctly', 'Incorrect CAPTCHA Answer');
				$this->is_present($reply['msg'], 'the owner of this file doesn\'t allow API', 'API Download Disabled for This File');
			default: throw new Exception("[ApiReq($path) Error {$reply['status']}] " . htmlspecialchars($reply['msg'], ENT_QUOTES));
		}

		return $reply['result'];
	}
}

//[21-04-2016]  Written by Th3-822.
//[15-10-2016]  Rewritten Decoding Functions. - Th3-822

?>