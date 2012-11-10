<?php

if (!defined('RAPIDLEECH')) {
	require_once ('index.html');
	exit();
}

class ddlstorage_com extends DownloadClass {
	private $page, $cookie;
	public function Download($link) {
		global $premium_acc;
		$this->cookie = array('lang' => 'english');
		if (empty($_POST['step']) || $_POST['step'] != 1) {
			$this->page = $this->GetPage($link, $this->cookie);
			if (preg_match('@^[\s\t]*(Access from \w+ is not allowed)@i', substr($this->page, strpos($this->page, "\r\n\r\n") + 4), $err)) html_error('ddlstorage: '.$err[1]);
			is_present($this->page, 'The file you were looking for could not be found');
			is_present($this->page, 'No such file with this filename', 'Error: Invalid filename, check your link and try again.');
		}

		if ($_REQUEST['premium_acc'] == 'on' && ((!empty($_REQUEST['premium_user']) && !empty($_REQUEST['premium_pass'])) || (!empty($premium_acc['ddlstorage_com']['user']) && !empty($premium_acc['ddlstorage_com']['pass'])))) $this->Login($link);
		else $this->FreeDL($link);
	}

	private function FreeDL($link) {
		if (!empty($_POST['step']) && $_POST['step'] == 1) {
			if (empty($_POST['captcha'])) html_error('You didn\'t enter the image verification code.');
			$this->cookie = (!empty($_POST['cookie'])) ? StrToCookies(decrypt(urldecode($_POST['cookie']))) : array();
			$this->cookie['lang'] = 'english';

			$post = array('recaptcha_challenge_field' => $_POST['challenge'], 'recaptcha_response_field' => $_POST['captcha']);
			$post['op'] = $_POST['T8']['op'];
			$post['id'] = $_POST['T8']['id'];
			$post['rand'] = $_POST['T8']['rand'];
			$post['referer'] = '';
			$post['method_free'] = $_POST['T8']['method_free'];
			$post['down_script'] = 1;

			$page = $this->GetPage($link, $this->cookie, $post);

			is_present($page, '>Skipped countdown', 'Error: Skipped countdown?.');
			is_present($page, '>Wrong captcha<', 'Error: Wrong Captcha Entered.');
			if (preg_match('@You can download files up to \d+ [KMG]b only.@i', $page, $err)) html_error('Error: '.$err[0]);

			if (!preg_match('@https?://[^/\r\n]+/(?:(?:files)|(?:dl?))/[^\'\"\t<>\r\n]+@i', $page, $dlink)) html_error('Error: Download link not found.');

			$FileName = urldecode(basename(parse_url($dlink[0], PHP_URL_PATH)));
			$this->RedirectDownload($dlink[0], $FileName);
		} else {
			$page2 = cut_str($this->page, 'Form method="POST" action=', '</form>'); //Cutting page
			$post = array();
			$post['op'] = cut_str($page2, 'name="op" value="', '"');
			$post['usr_login'] = (empty($this->cookie['xfss'])) ? '' : $this->cookie['xfss'];
			$post['id'] = cut_str($page2, 'name="id" value="', '"');
			$post['fname'] = cut_str($page2, 'name="fname" value="', '"');
			$post['referer'] = '';
			$post['method_free'] = urlencode(html_entity_decode(cut_str($page2, 'name="method_free" value="', '"')));

			$page = $this->GetPage($link, $this->cookie, $post);
			if (preg_match('@You have to wait (?:\d+ \w+,\s)?\d+ \w+ till next download@', $page, $err)) html_error('Error: '.$err[0]);

			$page2 = cut_str($page, '<form name="F1" method="POST"', '</form>'); //Cutting page

			if (!preg_match('@https?://(?:[^/]+\.)?(?:(?:google\.com/recaptcha/api)|(?:recaptcha\.net))/(?:(?:challenge)|(?:noscript))\?k=([\w|\-]+)@i', $page, $pid)) html_error('Error: reCAPTCHA not found.');

			if (!preg_match('@<div id="countdown_str"[^>]*>[^<>]+<p>[^<>]*<h2[^>]*>(\d+)</h2>[^<>]+</p>[^<>]+</div>@i', $page2, $count)) html_error('Countdown not found.');
			$this->CountDown($count[1]);

			$data = $this->DefaultParamArr($link, (empty($this->cookie['xfss'])) ? 0 : encrypt(CookiesToStr($this->cookie)));
			$data['T8[op]'] = cut_str($page2, 'name="op" value="', '"');
			is_notpresent($data['T8[op]'], 'download', 'Error parsing download post data (2).');
			$data['T8[id]'] = cut_str($page2, 'name="id" value="', '"');
			$data['T8[rand]'] = cut_str($page2, 'name="rand" value="', '"');
			$data['T8[method_free]'] = urlencode(html_entity_decode(cut_str($page2, 'name="method_free" value="', '"')));
			$data['step'] = 1;
			$this->DL_reCaptcha($pid[1], $data);
		}
	}

	private function DL_reCaptcha($pid, $data) {
		$page = $this->GetPage('http://www.google.com/recaptcha/api/challenge?k=' . $pid);
		if (!preg_match('/challenge \: \'([^\']+)/i', $page, $ch)) html_error('Error getting CAPTCHA data.');
		$challenge = $ch[1];

		$data['challenge'] = $challenge;

		//Download captcha img.
		$page = $this->GetPage('http://www.google.com/recaptcha/api/image?c=' . $challenge);
		$capt_img = substr($page, strpos($page, "\r\n\r\n") + 4);
		$imgfile = DOWNLOAD_DIR . get_class($this) .'_captcha.jpg';

		if (file_exists($imgfile)) unlink($imgfile);
		if (!write_file($imgfile, $capt_img)) html_error('Error getting CAPTCHA image.');

		$this->EnterCaptcha($imgfile.'?'.time(), $data, 20);
		exit;
	}

	private function PremiumDL($link) {
		$page = $this->GetPage($link, $this->cookie);
		if (!preg_match('@https?://[^/\r\n]+/(?:(?:files)|(?:dl?))/[^\'\"\t<>\r\n]+@i', $page, $dlink)) {
			$page2 = cut_str($page, '<form name="F1" method="POST"', '</form>'); //Cutting page

			$post = array();
			$post['op'] = cut_str($page2, 'name="op" value="', '"');
			$post['id'] = cut_str($page2, 'name="id" value="', '"');
			$post['rand'] = cut_str($page2, 'name="rand" value="', '"');
			$post['referer'] = '';
			$post['method_premium'] = urlencode(html_entity_decode(cut_str($page2, 'name="method_premium" value="', '"')));
			$post['down_direct'] = 1;

			$page = $this->GetPage($link, $this->cookie, $post);

			if (!preg_match('@https?://[^/\r\n]+/(?:(?:files)|(?:dl?))/[^\'\"\t<>\r\n]+@i', $page, $dlink)) html_error('Error: Download-link not found.');
		}

		$FileName = urldecode(basename(parse_url($dlink[0], PHP_URL_PATH)));
		$this->RedirectDownload($dlink[0], $FileName);
	}

	private function Login($link) {
		global $premium_acc;
		$pA = (!empty($_REQUEST['premium_user']) && !empty($_REQUEST['premium_pass']) ? true : false);
		$user = ($pA ? $_REQUEST['premium_user'] : $premium_acc['ddlstorage_com']['user']);
		$pass = ($pA ? $_REQUEST['premium_pass'] : $premium_acc['ddlstorage_com']['pass']);

		if (empty($user) || empty($pass)) html_error('Login Failed: User or Password is empty. Please check login data.');
		$post = array();
		$post['login'] = urlencode($user);
		$post['password'] = urlencode($pass);
		$post['op'] = 'login';
		$post['redirect'] = '';

		$purl = 'http://www.ddlstorage.com/';
		$page = $this->GetPage("$purl?op=login", $this->cookie, $post, $purl);
		if (preg_match('@^[\s\t]*(Access from \w+ is not allowed)@i', substr($page, strpos($page, "\r\n\r\n") + 4), $err)) html_error('ddlstorage: '.$err[1]);
		if (preg_match('@Incorrect ((Username)|(Login)) or Password@i', $page)) html_error('Login failed: User/Password incorrect.');
		is_present($page, 'op=resend_activation', 'Login failed: Your account isn\'t confirmed yet.');

		$this->cookie = GetCookiesArr($page);
		if (empty($this->cookie['xfss'])) html_error('Login Error: Cannot find session cookie.');
		$this->cookie['lang'] = 'english';

		$page = $this->GetPage("$purl?op=my_account", $this->cookie, 0, $purl);
		if (stripos($page, '/?op=logout') === false && stripos($page, '/logout') === false) html_error('Login Error.');

		if (stripos($page, 'Premium account expire') === false) {
			$this->changeMesg(lang(300).'<br /><b>Account isn\\\'t premium</b><br />Using it as member.');
			return $this->FreeDL($link);
		} else return $this->PremiumDL($link);
	}
}

// [06-11-2012]  Written by Th3-822. (XFS, XFS everywhere. D:)

?>