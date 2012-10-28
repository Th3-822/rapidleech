<?php

if (!defined('RAPIDLEECH')) {
	require_once ('index.html');
	exit();
}

class lumfile_com extends DownloadClass {
	private $page, $cookie;
	public function Download($link) {
		global $premium_acc;
		$this->cookie = array('lang' => 'english');
		$this->page = $this->GetPage($link, $this->cookie);
		is_present($this->page, 'The file you were looking for could not be found');
		//is_present($this->page, 'No such file with this filename', 'Error: Invalid filename, check your link and try again.');

		if ($_REQUEST['premium_acc'] == 'on' && ((!empty($_REQUEST['premium_user']) && !empty($_REQUEST['premium_pass'])) || (!empty($premium_acc['lumfile_com']['user']) && !empty($premium_acc['lumfile_com']['pass'])))) $this->Login($link);
		else $this->FreeDL($link);
	}

	private function FreeDL($link) {
		$page2 = cut_str($this->page, 'Form method="POST" action=', '</form>'); //Cutting page
		$post = array();
		$post['op'] = cut_str($page2, 'name="op" value="', '"');
		$post['usr_login'] = (empty($this->cookie['xfss'])) ? '' : $this->cookie['xfss'];
		$post['id'] = cut_str($page2, 'name="id" value="', '"');
		$post['fname'] = cut_str($page2, 'name="fname" value="', '"');
		$post['referer'] = '';
		$post['method_free'] = cut_str($page2, 'name="method_free" value="', '"');

		$page = $this->GetPage($link, $this->cookie, $post);
		if (preg_match('@You have to wait (?:\d+ \w+,\s)?\d+ \w+ till next download@', $page, $err)) html_error('Error: '.$err[0]);

		$page2 = cut_str($page, '<form name="F1" method="POST"', '</form>'); //Cutting page
		$post = array();
		$post['op'] = cut_str($page2, 'name="op" value="', '"');
		$post['id'] = cut_str($page2, 'name="id" value="', '"');
		$post['rand'] = cut_str($page2, 'name="rand" value="', '"');
		$post['referer'] = '';
		$post['method_free'] = cut_str($page2, 'name="method_free" value="', '"');

		if (!preg_match_all("@<span style='[^\'>]*padding-left\s*:\s*(\d+)[^\'>]*'[^>]*>((?:&#\w+;)|(?:\d))</span>@i", $page2, $spans)) html_error('Error: Cannot decode captcha.');
		$spans = array_combine($spans[1], $spans[2]);
		ksort($spans, SORT_NUMERIC);
		$captcha = '';
		foreach ($spans as $digit) $captcha .= $digit;
		$post['code'] = html_entity_decode($captcha);

		$post['down_script'] = 1;

		if (preg_match('@<span id="countdown_str">[^<>]+<span[^>]*>(\d+)</span>[^<>]+</span>@i', $page2, $count) && $count[1] > 0) $this->CountDown($count[1]);

		$page = $this->GetPage($link, $this->cookie, $post);

		is_present($page, '>Skipped countdown', 'Error: Skipped countdown?.');
		is_present($page, '>Wrong captcha<', 'Error: Unknown error after sending decoded captcha.');
		if (preg_match('@You can download files up to \d+ [KMG]b only.@i', $page, $err)) html_error('Error: '.$err[0]); // The sended captcha it's correct... But sometimes it's showed.

		if (!preg_match('@https?://[^/\r\n]+/(?:(?:files)|(?:dl?))/[^\'\"\s\t<>\r\n]+@i', $page, $dlink)) html_error('Error: Download link not found.');

		$FileName = urldecode(basename(parse_url($dlink[0], PHP_URL_PATH)));
		$this->RedirectDownload($dlink[0], $FileName);
	}

	private function PremiumDL($link) {
		$page = $this->GetPage($link, $this->cookie);
		if (!preg_match('@https?://[^/\r\n]+/(?:(?:files)|(?:dl?))/[^\'\"\s\t<>\r\n]+@i', $page, $dlink)) {
			$page2 = cut_str($page, '<form name="F1" method="POST"', '</form>'); //Cutting page

			$post = array();
			$post['op'] = cut_str($page2, 'name="op" value="', '"');
			$post['id'] = cut_str($page2, 'name="id" value="', '"');
			$post['rand'] = cut_str($page2, 'name="rand" value="', '"');
			$post['referer'] = '';
			$post['method_premium'] = cut_str($page2, 'name="method_premium" value="', '"');
			$post['down_direct'] = 1;

			$page = $this->GetPage($link, $this->cookie, $post);

			if (!preg_match('@https?://[^/\r\n]+/(?:(?:files)|(?:dl?))/[^\'\"\s\t<>\r\n]+@i', $page, $dlink)) html_error('Error: Download-link not found.');
		}

		$FileName = urldecode(basename(parse_url($dlink[0], PHP_URL_PATH)));
		$this->RedirectDownload($dlink[0], $FileName);
	}

	private function Login($link) {
		global $premium_acc;
		$pA = (!empty($_REQUEST['premium_user']) && !empty($_REQUEST['premium_pass']) ? true : false);
		$user = ($pA ? $_REQUEST['premium_user'] : $premium_acc['lumfile_com']['user']);
		$pass = ($pA ? $_REQUEST['premium_pass'] : $premium_acc['lumfile_com']['pass']);

		if (empty($user) || empty($pass)) html_error('Login Failed: User or Password is empty. Please check login data.');
		$post = array();
		$post['login'] = urlencode($user);
		$post['password'] = urlencode($pass);
		$post['op'] = 'login';
		$post['redirect'] = '';

		$purl = 'http://lumfile.com/';
		$page = $this->GetPage($purl, $this->cookie, $post, $purl);
		if (preg_match('@Incorrect ((Username)|(Login)) or Password@i', $page)) html_error('Login failed: User/Password incorrect.');
		is_present($page, 'op=resend_activation', 'Login failed: Your account isn\'t confirmed yet.');

		$this->cookie = GetCookiesArr($page);
		if (empty($this->cookie['xfss'])) html_error('Login Error: Cannot find session cookie.');
		$this->cookie['lang'] = 'english';

		$page = $this->GetPage("$purl?op=my_account", $this->cookie, 0, $purl);
		if (stripos($page, '/?op=logout') === false && stripos($page, '/logout') === false) html_error('Login Error.');

		if (stripos($page, 'Premium account expire') === false) {
			$this->changeMesg(lang(300).'<br /><b>Account isn\'t premium</b><br />Using it as member.');
			return $this->FreeDL($link);
		} else return $this->PremiumDL($link);
	}
}

// [28-8-2012]  Written by Th3-822. (XFS, XFS everywhere. D:)

?>