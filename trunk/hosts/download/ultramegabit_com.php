<?php

if (!defined('RAPIDLEECH')) {
	require_once ('index.html');
	exit();
}

class ultramegabit_com extends DownloadClass {
	private $page, $cookie;
	public function Download($link) {
		global $premium_acc;

		$link = str_ireplace('http://', 'https://', $link);
		$this->page = $this->GetPage($link, $this->cookie);
		is_present($this->page, "\r\nContent-Length: 0\r\n", 'Invalid link?');
		is_present($this->page, '>File has been deleted.<', 'File deleted');
		$this->cookie = GetCookiesArr($this->page);

		if ($_REQUEST['premium_acc'] == 'on' && ((!empty($_REQUEST['premium_user']) && !empty($_REQUEST['premium_pass'])) || (!empty($premium_acc['ultramegabit_com']['user']) && !empty($premium_acc['ultramegabit_com']['pass'])))) $this->Login($link);
		else $this->FreeDL($link);
	}

	private function FreeDL($link, $acc=false) {
		is_present($this->page, '>This download server is overloaded<', 'There are too many free users downloading from this server at this time.');
		$post = array();
		$post['csrf_token'] = cut_str($this->page, 'name="csrf_token" value="', '"');
		$post['encode'] = cut_str($this->page, 'name="encode" value="', '"');

		$page = $this->GetPage('https://ultramegabit.com/file/download', $this->cookie, $post);
		is_present('/user/confirm', 'Your account isn\'t validated.');
		if (preg_match('@/alert/delay/(\d+)@i', $page, $time)) {
			$wtime = $acc ? 20 : 60;
			$msg = $acc ? 'Free users' : 'Guests';
			$wait = ($time[1] - time()) + ($wtime * 60);

			$this->JSCountdown($wait, 0, "$msg are only able to download 1 file every $wtime minutes");
			return;
		}
		if (!preg_match('@https?://[^/\r\n]+/files/[^\'\"\s\t<>\r\n]+@i', $page, $dlink)) html_error('Error: Download link not found.');

		$FileName = urldecode(basename(parse_url($dlink[0], PHP_URL_PATH)));
		$this->RedirectDownload($dlink[0], $FileName);
	}

	private function PremiumDL($link) {
		if (!preg_match('@https?://[^/\r\n]+/files/[^\'\"\s\t<>\r\n]+@i', $this->page, $dlink)) {
			$post = array();
			$post['csrf_token'] = cut_str($this->page, 'name="csrf_token" value="', '"');
			$post['encode'] = cut_str($this->page, 'name="encode" value="', '"');

			$page = $this->GetPage('https://ultramegabit.com/file/download', $this->cookie, $post);

			if (!preg_match('@https?://[^/\r\n]+/files/[^\'\"\s\t<>\r\n]+@i', $page, $dlink)) html_error('Error: Download-link not found.');
		}

		$FileName = urldecode(basename(parse_url($dlink[0], PHP_URL_PATH)));
		$this->RedirectDownload($dlink[0], $FileName);
	}

	private function Login($link) {
		global $premium_acc;
		$pA = (!empty($_REQUEST['premium_user']) && !empty($_REQUEST['premium_pass']) ? true : false);
		$user = ($pA ? $_REQUEST['premium_user'] : $premium_acc['ultramegabit_com']['user']);
		$pass = ($pA ? $_REQUEST['premium_pass'] : $premium_acc['ultramegabit_com']['pass']);
		if (empty($user) || empty($pass)) html_error('Login Failed: User or Password is empty. Please check login data.');

		$purl = 'http://ultramegabit.com/';
		$post = array();
		$post['csrf_token'] = cut_str($this->page, 'name="csrf_token" value="', '"');
		$post['submit'] = 'Login';
		$post['return_url'] = urlencode($purl.'user/details');
		$post['username'] = urlencode($user);
		$post['password'] = urlencode($pass);

		$page = $this->GetPage($purl.'login', $this->cookie, $post, $purl);
		is_present($page, 'Invalid username or password', 'Login failed: User/Password incorrect.');
		is_notpresent($page, "\r\nContent-Length: 0\r\n", 'Login failed.');
		$this->cookie = GetCookiesArr($page);

		$page = $this->GetPage($purl.'user/details', $this->cookie, 0, $purl.'login');

		$this->page = $this->GetPage($link, $this->cookie);

		if (stripos($page, '"Premium Member"') === false) {
			$this->changeMesg(lang(300).'<br /><b>Account isn\'t premium</b><br />Using it as member.');
			return $this->FreeDL($link, true);
		} else return $this->PremiumDL($link);
	}
}

// [06-9-2012]  Written by Th3-822.

?>