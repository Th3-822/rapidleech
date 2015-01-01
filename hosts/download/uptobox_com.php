<?php

if (!defined('RAPIDLEECH')) {
	require_once('index.html');
	exit();
}

class uptobox_com extends DownloadClass {
	private $page, $cookie;
	public function Download($link) {
		global $premium_acc;
		$this->DLregexp = '@https?://(?:[\w\-]+\.)+[\w\-]+(?:\:\d+)?/(?:files|dl?|cgi-bin/dl\.cgi)/[^\'\"\t<>\r\n]+@i';
		$this->cookie = array('lang' => 'english');
		if (empty($_POST['step']) || $_POST['step'] != '1') {
			$this->page = $this->GetPage($link, $this->cookie);
			is_present($this->page, 'The file you were looking for could not be found');
			is_present($this->page, 'No such file with this filename', 'Error: Invalid filename, check your link and try again.');
		}

		if ($_REQUEST['premium_acc'] == 'on' && ((!empty($_REQUEST['premium_user']) && !empty($_REQUEST['premium_pass'])) || (!empty($premium_acc['uptobox_com']['user']) && !empty($premium_acc['uptobox_com']['pass'])))) $this->Login($link);
		else $this->FreeDL($link);
	}

	private function FreeDL($link) {
		if (!empty($_POST['step']) && $_POST['step'] == '1') {
			if (!empty($_POST['cookie'])) $this->cookie = StrToCookies(decrypt(urldecode($_POST['cookie'])));

			$post = $this->Solvemedia_post();
			foreach ($_POST['T8'] as $k => $v) $post[$k] = $v;
		} else {
			if (preg_match('@You have to wait (?:\d+ \w+,\s)?\d+ \w+ till next download@', $this->page, $err)) html_error('Error: '.$err[0]);

			$page2 = cut_str($this->page, '<form name="F1" method="POST"', '</form>'); //Cutting page
			$post = array();
			$post['op'] = cut_str($page2, 'name="op" value="', '"');
			$post['id'] = cut_str($page2, 'name="id" value="', '"');
			$post['rand'] = cut_str($page2, 'name="rand" value="', '"');
			$post['referer'] = '';
			$post['method_free'] = cut_str($page2, 'name="method_free" value="', '"');
			$post['down_script'] = 1;

			// Test decodeable captcha
			if (preg_match_all("@<span style='[^\'>]*padding-left\s*:\s*(\d+)[^\'>]*'[^>]*>((?:&#\w+;)|(?:\d))</span>@i", $page2, $spans)) {
				$spans = array_combine($spans[1], $spans[2]);
				ksort($spans, SORT_NUMERIC);
				$captcha = '';
				foreach ($spans as $digit) $captcha .= $digit;
				$post['code'] = html_entity_decode($captcha);
			}

			// Countdown
			if (preg_match('@<span id="\w+"[^>]*>(\d+)</span>\s*seconds@i', $page2, $count) && $count[1] > 0) $this->CountDown($count[1]);

			// Test for solvemedia captcha
			if (empty($post['code']) && preg_match('@https?://api\.solvemedia\.com/papi/challenge\.(?:no)?script\?k=([\w\.-]+)@i', $this->page, $skey)) {
				$data = $this->DefaultParamArr($link, (empty($this->cookie['xfss'])) ? 0 : encrypt(CookiesToStr($this->cookie)));
				foreach ($post as $k => $v) $data["T8[$k]"] = $v;
				$data['step'] = '1';
				$this->Solvemedia($skey[1], $data);
				exit;
			}
		}

		$page = $this->GetPage($link, $this->cookie, $post);

		is_present($page, '>Skipped countdown', 'Error: Skipped countdown?.');
		is_present($page, '>Wrong captcha<', 'Error: Wrong Captcha Entered.');
		is_present($page, '>Expired session<', 'Error: Expired Download Session.');
		if (preg_match('@You can download files up to \d+ [KMG]b only.@i', $page, $err)) html_error('Error: '.$err[0]);

		if (!preg_match($this->DLregexp, $page, $dlink)) html_error('Error: Download link not found.');

		$dlink[0] = str_ireplace('&subid=uptobox', '', $dlink[0]); // Lazy fix :P
		$this->RedirectDownload($dlink[0], urldecode(basename(parse_url($dlink[0], PHP_URL_PATH))));
	}

	private function Solvemedia($skey, $data, $referer = 0, $sname = 'Download File') {
		if (!is_array($data)) html_error('Post needs to be sended in a array.');
		if (empty($skey) || preg_match('@[^\w\-\.]@', $skey)) html_error('Invalid value for $skey');
		$page = $this->GetPage("http://api.solvemedia.com/papi/challenge.noscript?k=$skey", 0, 0, $referer);
		if (!preg_match('@<img [^/<>]*src\s?=\s?\"((https?://[^/\"<>]+)?/papi/media[^\"<>]+)\"@i', $page, $imgurl)) html_error('CAPTCHA img not found.');
		$imgurl = (empty($imgurl[2])) ? 'http://api.solvemedia.com'.$imgurl[1] : $imgurl[1];

		if (!preg_match_all('@<input [^/|<|>]*type\s?=\s?\"?hidden\"?[^/<>]*\s?name\s?=\s?\"(\w+)\"[^/<>]*\s?value\s?=\s?\"([^\"<>]+)\"[^/<>]*/?\s*>@i', $page, $forms)) html_error('CAPTCHA data not found.');
		$forms = array_combine($forms[1], $forms[2]);
		foreach ($forms as $n => $v) $data["T8_smc[$n]"] = urlencode($v);

		//Download captcha img.
		list($headers, $imgBody) = explode("\r\n\r\n", $this->GetPage($imgurl), 2);
		if (substr($headers, 9, 3) != '200') html_error('Error downloading CAPTCHA img.');
		$mimetype = (preg_match('@image/[\w+]+@', $headers, $mimetype) ? $mimetype[0] : 'image/gif');

		$this->EnterCaptcha("data:$mimetype;base64,".base64_encode($imgBody), $data, 20, $sname, 'adcopy_response');
		exit;
	}

	private function Solvemedia_post($referer = 0) {
		if (empty($_POST['adcopy_response'])) html_error('You didn\'t enter the image verification code.');
		$post = array();
		foreach ($_POST['T8_smc'] as $n => $v) $post[urlencode($n)] = $v;
		$post['adcopy_response'] = urlencode($_POST['adcopy_response']);

		$url = 'http://api.solvemedia.com/papi/verify.noscript';
		$page = $this->GetPage($url, 0, $post, $referer);

		if (!preg_match('@(https?://[^/\'\"<>\r\n]+)?/papi/verify\.pass\.noscript\?[^/\'\"<>\r\n]+@i', $page, $resp)) {
			is_present($page, '/papi/challenge.noscript', 'Wrong CAPTCHA entered.');
			html_error('Error sending CAPTCHA.');
		}
		$resp = (empty($resp[1])) ? 'http://api.solvemedia.com'.$resp[0] : $resp[0];

		$page = $this->GetPage($resp, 0, 0, $url);
		if (!preg_match('@>[\s\t\r\n]*([^<>\r\n]+)[\s\t\r\n]*</textarea>@i', $page, $gibberish)) html_error('CAPTCHA response not found.');

		return array('adcopy_challenge' => urlencode($gibberish[1]), 'adcopy_response' => 'manual_challenge');
	}

	private function PremiumDL($link) {
		$page = $this->GetPage($link, $this->cookie);
		if (!preg_match($this->DLregexp, $page, $dlink)) {
			$page2 = cut_str($page, '<form name="F1" method="POST"', '</form>'); //Cutting page

			$post = array();
			$post['op'] = cut_str($page2, 'name="op" value="', '"');
			$post['id'] = cut_str($page2, 'name="id" value="', '"');
			$post['rand'] = cut_str($page2, 'name="rand" value="', '"');
			$post['referer'] = '';
			$post['method_premium'] = cut_str($page2, 'name="method_premium" value="', '"');
			$post['down_direct'] = 1;

			$page = $this->GetPage($link, $this->cookie, $post);

			if (!preg_match($this->DLregexp, $page, $dlink)) html_error('Error: Download-link not found.');
		}

		$this->RedirectDownload($dlink[0], urldecode(basename(parse_url($dlink[0], PHP_URL_PATH))));
	}

	private function Login($link) {
		global $premium_acc;
		$pA = (!empty($_REQUEST['premium_user']) && !empty($_REQUEST['premium_pass']) ? true : false);
		$user = ($pA ? $_REQUEST['premium_user'] : $premium_acc['uptobox_com']['user']);
		$pass = ($pA ? $_REQUEST['premium_pass'] : $premium_acc['uptobox_com']['pass']);

		if (empty($user) || empty($pass)) html_error('Login Failed: User or Password is empty. Please check login data.');
		$post = array();
		$post['login'] = urlencode($user);
		$post['password'] = urlencode($pass);
		$post['op'] = 'login';
		$post['redirect'] = '';

		$purl = 'http://uptobox.com/';
		$page = $this->GetPage($purl, $this->cookie, $post, $purl);
		if (preg_match('@Incorrect ((Username)|(Login)) or Password@i', $page)) html_error('Login failed: User/Password incorrect.');
		is_present($page, 'op=resend_activation', 'Login failed: Your account isn\'t confirmed yet.');

		$this->cookie = GetCookiesArr($page);
		if (empty($this->cookie['xfss'])) html_error('Login Error: Cannot find session cookie.');
		$this->cookie['lang'] = 'english';

		$page = $this->GetPage("$purl?op=my_account", $this->cookie, 0, $purl);
		if (stripos($page, '/?op=logout') === false && stripos($page, '/logout') === false) html_error('Login Error.');

		if (stripos($page, '>Free member<') !== false) {
			$this->changeMesg(lang(300).'<br /><b>Account isn\\\'t premium</b><br />Using it as member.');
			return $this->FreeDL($link);
		} else return $this->PremiumDL($link);
	}
}

// [05-7-2013]  Written by Th3-822. (XFS, XFS everywhere. D:)
// [15-5-2014]  Fixed FreeDL. - Th3-822
// [17-5-2014]  Added solvemedia captcha support. - Th3-822

?>