<?php
if (!defined('RAPIDLEECH')) {
	require_once ('index.html');
	exit();
}

class howfile_com extends DownloadClass {

	public function Download($link) {
		global $premium_acc;

		if (!$_REQUEST['step']) {
			$tcookie['language'] = 'en_us';
			$this->page = $this->GetPage($link, $tcookie);
			is_present($this->page, "Been deleted", "File not found");
			$tcookie = GetCookiesArr($this->page, $tcookie);
			if (!preg_match_all('/setCookie\("(v\w+)", "(\w+)",/i', $this->page, $cb)) html_error("Error: vid\vid1 Cookie not found!");
			$this->cookie = array_merge($tcookie, array_combine($cb[1], $cb[2]));
		}
		$this->link = $link;
		if ($_REQUEST['premium_acc'] == 'on' && (($_REQUEST['premium_user'] && $_REQUEST['premium_pass']) || ($premium_acc['howfile_com']['user'] && $premium_acc['howfile_com']['pass']))) {
			return $this->Premium();
		} else {
			return $this->Free();
		}
	}

	private function Free() {

		if (!preg_match("/<a href=\"([^\"]+)\" onclick='setCookie/", $this->page, $dl)) html_error("Error: Download Link not found!");
		$dlink = trim($dl[1]);
		$this->RedirectDownload($dlink, 'howfile', $this->cookie, 0, $this->link);
		exit();
	}

	private function Premium() {

		$cookie = $this->login();
		$page = $this->GetPage($this->link, $cookie);
		if (!preg_match("/<a href=\"([^\"]+)\" onclick='setCookie/", $this->page, $dl)) html_error("Error: Download Link not found!");
		$dlink = trim($dl[1]);
		$this->RedirectDownload($dlink, 'howfile', $cookie);
	}

	private function login() {
		global $premium_acc;

		$user = ($_REQUEST["premium_user"] ? trim($_REQUEST["premium_user"]) : $premium_acc ["howfile_com"] ["user"]);
		$pass = ($_REQUEST["premium_pass"] ? trim($_REQUEST["premium_pass"]) : $premium_acc ["howfile_com"] ["pass"]);
		if (empty($user) || empty($pass)) html_error("Login failed, username or password is empty!");

		$posturl = 'http://www.howfile.com/';
		$post = array();
		$post['returnPath'] = '/desktop/into.html';
		$post['username'] = $user;
		$post['password'] = $pass;
		$post['remember'] = 'on';
		$page = $this->GetPage($posturl . 'view?module=member&action=validateLogin', $this->cookie, $post, $posturl);
		is_present($page, 'Wrong username or password');
		$cookie = GetCookiesArr($page, $this->cookie);

		$page = $this->GetPage($posturl . 'desktop/into.html', $cookie, 0, $posturl);
		is_present($page, 'Upgrade</a>', 'Account isn\'t Premium!');

		return $cookie;
	}

}

/*
 * howfile.com free download plugin by Ruud v.Tony 18-01-2012
 * Updated to support premium by Tony Fauzi Wihana/Ruud v.Tony 26-12-2012
 */
?>
