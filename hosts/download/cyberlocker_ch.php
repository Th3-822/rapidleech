<?php
if (!defined('RAPIDLEECH')) {
	require_once 'index.html';
	exit;
}

class cyberlocker_ch extends DownloadClass {

	public function Download($link) {
		global $premium_acc;

		if (!$_REQUEST['step']) {
			$this->cookie = array('lang' => 'english');
			$this->page = $this->GetPage($link, $this->cookie);
			is_present($this->page, 'HTTP/1.1 502 Bad Gateway', 'Unknown Error from cyberlocker.ch');
			is_present($this->page, 'The file you were looking for could not be found, sorry for any inconvenience.');
		}
		$this->link = $link;
		if ($_REQUEST['premium_acc'] == 'on' && (($_REQUEST['premium_user'] && $_REQUEST['premium_pass']) || ($premium_acc['cyberlocker_ch']['user'] && $premium_acc['cyberlocker_ch']['pass']))) {
			return $this->Premium();
		} else {
			return $this->Free();
		}
	}

	private function Free() {
		$form = cut_str($this->page, '<Form method="POST" action=\'\'>', '</Form>');
		if (!preg_match_all('/type="hidden" name="([^"]+)" value="([^"]+)?"/', $form, $one) || !preg_match_all('/type="submit" name="(\w+_free)" value="([^"]+)"/', $form, $two)) html_error('Error[Form Post Data 1 FREE not found!]');
		$match = array_merge(array_combine($one[1], $one[2]), array_combine($two[1], $two[2]));
		$post = array();
		foreach ($match as $key => $value) {
			$post[$key] = $value;
		}
		$page = $this->GetPage($this->link, $this->cookie, $post, $this->link);
		unset($post);
		$form = cut_str($page, '<Form name="F1" method="POST" action="">', '</Form>');
		$form = str_replace('<input type="hidden" name="op" value="register_save">', '', $form);
		if (!preg_match_all('/type="(hidden|text|password)" name="([^"]+)" value="([^"]+)?"/', $form, $match)) html_error('Error[Post Data 2 - FREE not found!]');
		$match = array_combine($match[2], $match[3]);
		$post = array();
		foreach ($match as $key => $value) {
			$post[$key] = $value;
		}
		$page = $this->GetPage($this->link, $this->cookie, $post, $this->link);
		if (!preg_match('/Location: (https?:\/\/[^\r\n]+)/i', $page, $dl)) html_error('Error[Download Link FREE not found!]');
		$dlink = trim($dl[1]);
		$filename = basename(parse_url($dlink, PHP_URL_PATH));
		$this->RedirectDownload($dlink, $filename, $this->cookie, 0, $this->link);
	}

	private function Premium() {

		$cookie = $this->login();
		$page = $this->GetPage($this->link, $cookie);
		if (!preg_match('/Location: (https?:\/\/[^\r\n]+)/i', $page, $dl)) {
			$form = cut_str($page, '<Form name="F1" method="POST" action="">', '</Form>');
			if (!preg_match_all('/type="hidden" name="([^"]+)" value="([^"]+)?"/', $form, $match)) html_error('Error[Post Data - PREMIUM not found!]');
			$match = array_combine($match[1], $match[2]);
			$post = array();
			foreach ($match as $key => $value) {
				$post[$key] = $value;
			}
			$page = $this->GetPage($this->link, $cookie, $post, $this->link);
			if (!preg_match('/Location: (https?:\/\/[^\r\n]+)/i', $page, $dl)) html_error('Error[Download Link PREMIUM not found!]');
		}
		$dlink = trim($dl[1]);
		$filename = basename(parse_url($dlink, PHP_URL_PATH));
		$this->RedirectDownload($dlink, $filename, $cookie);
	}

	private function login() {
		global $premium_acc;

		$user = ($_REQUEST["premium_user"] ? trim($_REQUEST["premium_user"]) : $premium_acc ["cyberlocker_ch"] ["user"]);
		$pass = ($_REQUEST["premium_pass"] ? trim($_REQUEST["premium_pass"]) : $premium_acc ["cyberlocker_ch"] ["pass"]);
		if (empty($user) || empty($pass)) html_error("Login failed, $user [user] or $pass [password] is empty!");

		$posturl = 'http://cyberlocker.ch/';
		$post['op'] = 'login';
		$post['redirect'] = '';
		$post['login'] = $user;
		$post['password'] = $pass;
		$page = $this->GetPage($posturl, $this->cookie, $post, $posturl);
		is_present($page, 'Incorrect Login or Password');
		$cookie = GetCookiesArr($page, $this->cookie);

		//check account
		$page = $this->GetPage($posturl . '?op=my_account', $cookie);
		is_notpresent($page, '>Premium account expire:<', 'Error[Account isn\'t premium!]');

		return $cookie;
	}

	public function CheckBack($header) {
		is_present($header, 'HTTP/1.1 502 Bad Gateway', 'Unknown Error from Cyberlocker.ch');
	}

}

/*
 * Written by Tony Fauzi Wihana/Ruud v.Tony 05/03/2013
 */
?>