<?php

if (!defined('RAPIDLEECH')) {
	require_once('index.html');
	exit();
}

class storagon_com extends DownloadClass {
	private $page, $cookie;
	public function Download($link) {
		global $premium_acc;
		$this->cookie = array('lang' => 'english');
		$link = str_ireplace('https://', 'http://', $link);
		$this->page = $this->GetPage($link, $this->cookie);
		is_present($this->page, 'The file you were looking for could not be found');
		is_present($this->page, 'No such file with this filename', 'Error: Invalid filename, check your link and try again.');

		if ($_REQUEST['premium_acc'] == 'on' && ((!empty($_REQUEST['premium_user']) && !empty($_REQUEST['premium_pass'])) || (!empty($premium_acc['storagon_com']['user']) && !empty($premium_acc['storagon_com']['pass'])))) $this->Login($link);
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

		if (preg_match_all("@<span style='[^\'>]*padding-left\s*:\s*(\d+)[^\'>]*'[^>]*>((?:&#\w+;)|(?:\d))</span>@i", $page2, $spans)) {
			$spans = array_combine($spans[1], $spans[2]);
			ksort($spans, SORT_NUMERIC);
			$captcha = '';
			foreach ($spans as $digit) $captcha .= $digit;
			$post['code'] = html_entity_decode($captcha);
		}

		$post['down_script'] = 1;

		if (preg_match('@<span id="\w+"[^>]*>(\d+)</span>\s*seconds@i', $page2, $count) && $count[1] > 0) $this->CountDown($count[1]);

		$page = $this->GetPage($link, $this->cookie, $post);

		is_present($page, '>Skipped countdown', 'Error: Skipped countdown?.');
		is_present($page, '>Wrong captcha<', 'Error: Unknown error after sending decoded captcha.'); // The sended captcha it's correct... But sometimes it's showed.
		if (preg_match('@You can download files up to \d+ [KMG]b only.@i', $page, $err)) html_error('Error: '.$err[0]);

		if (!preg_match('@https?://[^/\r\n]+/(?:files|dl?|cgi-bin/dl\.cgi)/[^\'\"\t<>\r\n]+@i', $page, $dlink)) html_error('Error: Download link not found.');

		$this->RedirectDownload($dlink[0], urldecode(basename(parse_url($dlink[0], PHP_URL_PATH))));
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
			$post['method_premium'] = cut_str($page2, 'name="method_premium" value="', '"');
			$post['down_direct'] = 1;

			$page = $this->GetPage($link, $this->cookie, $post);

			if (!preg_match('@https?://[^/\r\n]+/(?:(?:files)|(?:dl?))/[^\'\"\t<>\r\n]+@i', $page, $dlink)) html_error('Error: Download-link not found.');
		}

		$this->RedirectDownload($dlink[0], urldecode(basename(parse_url($dlink[0], PHP_URL_PATH))));
	}

	private function Login($link) {
		global $premium_acc;
		$pA = (!empty($_REQUEST['premium_user']) && !empty($_REQUEST['premium_pass']) ? true : false);
		$user = ($pA ? $_REQUEST['premium_user'] : $premium_acc['storagon_com']['user']);
		$pass = ($pA ? $_REQUEST['premium_pass'] : $premium_acc['storagon_com']['pass']);

		if (empty($user) || empty($pass)) html_error('Login Failed: User or Password is empty. Please check login data.');
		$post = array();
		$post['login'] = urlencode($user);
		$post['password'] = urlencode($pass);
		$post['op'] = 'login';
		$post['redirect'] = '';

		$purl = 'https://storagon.com/';
		$page = $this->GetPage($purl, $this->cookie, $post, $purl);
		if (preg_match('@Incorrect ((Username)|(Login)) or Password@i', $page)) html_error('Login failed: User/Password incorrect.');
		is_present($page, 'op=resend_activation', 'Login failed: Your account isn\'t confirmed yet.');

		$this->cookie = GetCookiesArr($page);
		if (empty($this->cookie['xfss'])) html_error('Login Error: Cannot find session cookie.');
		$this->cookie['lang'] = 'english';

		$page = $this->GetPage("$purl/account/", $this->cookie, 0, $purl);
		if (stripos($page, '/?op=logout') === false && stripos($page, '/logout') === false) html_error('Login Error.');

		if (stripos($page, 'Upgrade to premium') !== false) {
			$this->changeMesg(lang(300).'<br /><b>Account isn\\\'t premium</b><br />Using it as member.');
			return $this->FreeDL($link);
		} else return $this->PremiumDL($link);
	}
}

// [05-7-2013]  Written by Th3-822. (XFS, XFS everywhere. D:)
// [05-1-2014]  Fixed FreeDL. - Th3-822

?>