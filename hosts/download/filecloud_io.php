<?php

if (!defined('RAPIDLEECH')) {
	require_once('index.html');
	exit;
}

class filecloud_io extends DownloadClass {
	private $page, $cookie, $redir, $_url, $_ukey, $dlreq, $dlurl, $ab1, $captcha;
	public function Download($link) {
		global $premium_acc;
		$this->cookie = array();

		if (empty($_POST['skip']) || $_POST['skip'] != 'true') {
			global $Referer;
			$this->page = $this->GetPage($link, $this->cookie);
			if (substr($this->page, 9, 3) == '404') html_error('File not Found or Deleted');
			$this->cookie = GetCookiesArr($this->page, $this->cookie);
			if (preg_match('@\r\nLocation: ((https?://(?:www\.)?filecloud\.io)?/[^\r\n]+)@i', $this->page, $redir)) {
				$this->redir = (empty($redir[2])) ? 'http://filecloud.io'.$redir[1] : $redir[1];
				$this->page = $this->GetPage($this->redir, $this->cookie, 0, $link);
				$this->cookie = GetCookiesArr($this->page, $this->cookie);
				$Referer = $this->redir;
			} else $Referer = $link;
			if (preg_match('@\nLocation: (https?://s\d+\.filecloud\.io/[^\r\n]+)@i', $this->page, $dllink)) {
				$filename = urldecode(basename(parse_url($dllink[1], PHP_URL_PATH)));
				return $this->RedirectDownload($dllink[1], $filename);
			}
		}

		// Check https support for login.
		$cantlogin = false;
		if (!extension_loaded('openssl')) {
			if (extension_loaded('curl')) {
				$cV = curl_version();
				if (!in_array('https', $cV['protocols'], true)) $cantlogin = true;
			} else $cantlogin = true;
			if ($cantlogin) $this->changeMesg(lang(300).'<br /><br />Https support: NO<br />Login disabled.');
		}

		if ($_REQUEST['premium_acc'] == 'on' && (((!empty($_REQUEST['premium_user']) && !empty($_REQUEST['premium_pass'])) || (!empty($premium_acc['filecloud_io']['user']) && !empty($premium_acc['filecloud_io']['pass']))) || !empty($premium_acc['filecloud_io']['apikey']))) $this->Login($cantlogin);
		elseif (!empty($_POST['skip']) && $_POST['skip'] == 'true') {
			$this->_ukey = urldecode($_POST['_ukey']);
			$this->ab1 = urldecode($_POST['_ab1']);
			$this->chkcaptcha($link, true);
		} else $this->Prepare($link);
	}

	private function Prepare($link) {
		$_s = '[\s\t]'; //Still lazy XD

		if (!preg_match("@$_s'ukey'$_s*:$_s*'([^']+)'@i", $this->page, $_ukey)) html_error('Error: File ID not found.');
		$this->_ukey = $_ukey[1];

		if (!preg_match("@var$_s+__requestUrl$_s*=$_s*'([^']+)'@i", $this->page, $_url)) html_error('Error: Url for posting data not found.');
		$this->_url = $_url[1];

		if (!preg_match("@var$_s+__downloadUrl$_s*=$_s*'([^']+)'@i", $this->page, $dlreq)) $dlreq = array(1=>false);
		$this->dlreq = $dlreq[1];

		if (!preg_match("@var$_s+__recaptcha_public$_s*=$_s*'([^']+)'@i", $this->page, $this->captcha)) $this->captcha = false;
		$this->captcha = $this->captcha[1];

		$ab_js = $this->GetPage('http://filecloud.io/ads/adframe.js', $this->cookie);
		if (!preg_match("@var$_s+__ab1$_s*=$_s*[\'\"]([^\'\"]+)[\'\"]@i", $ab_js, $ab1)) html_error('Error: "Session" code not found.');
		$this->ab1 = $ab1[1];

		$this->chkcaptcha($link);
	}

	private function FreeDL($rply) {
		if ($rply['dl'] != '1') {
			$err = (!empty($rply['message'])) ? ': '.htmlentities($rply['message']) : '.';
			html_error("Error getting download-link$err");
		}

		$page = $this->GetPage($this->dlreq, $this->cookie);

		if (!preg_match('@https?://s\d+\.filecloud\.io/'.$this->_ukey.'/[^\r\n\s\t<>\'\"]+@i', $page, $dllink)) html_error('Error: Download-link not found.');

		$filename = urldecode(basename(parse_url($dllink[0], PHP_URL_PATH)));
		return $this->RedirectDownload($dllink[0], $filename, $this->cookie);
	}

	private function Get_Reply($page) {
		if (!function_exists('json_decode')) html_error('Error: Please enable JSON in php.');
		// First time using json_decode in plugins. :)
		$json = substr($page, strpos($page, "\r\n\r\n") + 4);
		$json = substr($json, strpos($json, '{'));$json = substr($json, 0, strrpos($json, '}') + 1);
		$rply = json_decode($json, true);
		if (!$rply || count($rply) == 0) html_error('Error getting json data.');
		return $rply;
	}

	private function chkcaptcha($link, $send = false) {
		$post = array();
		$post['ukey'] = $this->_ukey;
		$post['__ab1'] = $this->ab1; // More annoying ad-block trap.
		if ($send) {
			if (empty($_POST['captcha'])) html_error('You didn\'t enter the image verification code.');
			$post['ctype'] = 'recaptcha';
			$post['recaptcha_response'] = $_POST['captcha'];
			$post['recaptcha_challenge'] = $_POST['challenge'];

			$this->_url = urldecode($_POST['_url']);
			$this->dlreq = urldecode($_POST['_dlreq']);
			$this->cookie = urldecode($_POST['cookie']);

			$page = $this->GetPage($this->_url, $this->cookie, $post);
			$rply = $this->Get_Reply($page);

			if ($rply['captcha'] == 0) $this->FreeDL($rply);
			elseif ($rply['retry'] == 1) html_error('Error: Wrong Captcha Entered.');
			else html_error('Error Sending Captcha.');
		} else {
			$page = $this->GetPage($this->_url, $this->cookie, $post);
			$rply = $this->Get_Reply($page);

			if ($rply['status'] == 'ok') {
				if ($rply['captcha'] == 0) {
					$this->FreeDL($rply);
				} else {
					if (!$this->captcha || empty($this->captcha)) html_error('Error: Captcha not found.');
					$data = $this->DefaultParamArr($link, $this->cookie);
					$data['_ukey'] = urlencode($this->_ukey);
					$data['_url'] = urlencode($this->_url);
					$data['_dlreq'] = urlencode($this->dlreq);
					$data['_ab1'] = urlencode($this->ab1);
					$data['skip'] = 'true';
					$this->DL_reCaptcha($this->captcha, $data);
				}
			} else html_error("Error getting download data ('{$rply['status']}' => '{$rply['message']}').");
		}
		return false;
	}

	private function DL_reCaptcha($pid, $data) {
		$page = $this->GetPage('http://www.google.com/recaptcha/api/challenge?k=' . $pid);
		if (!preg_match('/challenge \: \'([^\']+)/i', $page, $ch)) html_error('Error getting CAPTCHA data.');
		$challenge = $ch[1];

		$data['challenge'] = $challenge;

		//Download captcha img.
		$page = $this->GetPage('http://www.google.com/recaptcha/api/image?c=' . $challenge);
		$capt_img = substr($page, strpos($page, "\r\n\r\n") + 4);
		$imgfile = DOWNLOAD_DIR . 'filecloud_captcha.jpg';

		if (file_exists($imgfile)) unlink($imgfile);
		if (!write_file($imgfile, $capt_img)) html_error('Error getting CAPTCHA image.');

		$this->EnterCaptcha($imgfile.'?'.time(), $data, 20);
		exit;
	}

	private function Login($cantlogin) {
		global $premium_acc;
		if ($cantlogin && empty($premium_acc['filecloud_io']['apikey'])) html_error('Login Error: Empty apikey.');

		// Ping api
		$page = $this->GetPage('http://api.filecloud.io/api-ping.api');
		is_notpresent($page, '"message":"pong"', 'Error: filecloud.io api is down?.');

		if (!empty($_REQUEST['premium_user']) && !empty($_REQUEST['premium_pass'])) $pA = true;
		else $pA = false;
		if (empty($premium_acc['filecloud_io']['apikey']) || $pA) {
			$user = ($pA ? $_REQUEST['premium_user'] : $premium_acc['filecloud_io']['user']);
			$pass = ($pA ? $_REQUEST['premium_pass'] : $premium_acc['filecloud_io']['pass']);
			if (empty($user) || empty($pass)) html_error('Login Failed: Username or Password are empty. Please check login data.');

			$post = array();
			$post['username'] = urlencode(strtolower($user));
			$post['password'] = urlencode($pass);
			$page = $this->GetPage('https://secure.filecloud.io/api-fetch_apikey.api', 0, $post);
			$rply = $this->Get_Reply($page);

			if ($rply['status'] != 'ok') html_error('Login Failed: '.htmlentities($rply['message']));
			if (empty($rply['akey'])) html_error('Login Failed: Akey not found.');
		} else $rply = array('akey' => urldecode($premium_acc['filecloud_io']['apikey']));

		$this->cookie = array('auth' => urlencode($rply['akey']));
		return $this->PremiumDL();
	}

	private function PremiumDL() {
		if (!preg_match('@\nLocation: (https?://s\d+\.filecloud\.io/[^\r\n]+)@i', $this->page, $dllink)) {
			if (!preg_match("@[\s\t]'ukey'[\s\t]*:[\s\t]*'([^']+)'@i", $this->page, $_ukey)) html_error('Error: FileID not found.');
			$page = $this->GetPage('http://api.filecloud.io/api-fetch_download_url.api', 0, array('akey' => $this->cookie['auth'], 'ukey' => $_ukey[1]));
			$rply = $this->Get_Reply($page);

			if ($rply['status'] != 'ok') html_error('Error getting premium dlink: '.htmlentities($rply['message']));
			if (empty($rply['download_url'])) html_error('Error getting premium dlink... Empty?');
		} else $rply = array('download_url' => $dllink[1]);

		$filename = urldecode(basename(parse_url($rply['download_url'], PHP_URL_PATH)));
		return $this->RedirectDownload($rply['download_url'], $filename);
	}
}

//[26-Oct-2012] (Re)Written by Th3-822.
//[17-Feb-2013] Login Fixes. - Th3-822
//[16-Oct-2013] Fixed support for direct-links. - Th3-822

?>