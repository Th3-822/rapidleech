<?php
if (!defined('RAPIDLEECH')) {
	require_once ('index.html');
	exit();
}

class slingfile_com extends DownloadClass {

	public function Download($link) {
		global $premium_acc, $Referer;
		$Referer = '';
		if (!$_REQUEST['step']) {
			$this->page = $this->GetPage($link);
			is_present($this->page, 'The file was deleted due to a DMCA complaint.');
			$this->cookie = GetCookiesArr($this->page);
		}
		$this->link = $link;
		if ($_REQUEST['premium_acc'] == 'on' && (($_REQUEST['premium_user'] && $_REQUEST['premium_pass']) || ($premium_acc['slingfile_com']['user'] && $premium_acc['slingfile_com']['pass']))) {
			return $this->Premium();
		} else {
			return $this->Free();
		}
	}

	private function Free() {
		if (!preg_match('/var seconds =(\d+);/', $this->page, $wait)) html_error("Error: Timer not found!");
		$this->CountDown($wait[1]);
		$page = $this->GetPage($this->link, $this->cookie, array('download' => 'yes'), $this->link);
		if (!preg_match('@http:\/\/sf\d+[-]\d+\.slingfile\.com\/gdl\/[^"]+@', $page, $dl)) html_error("Error: Download link not found, result : $dl[0]");
		$dlink = trim($dl[0]);
		$FileName = basename(parse_url($dlink, PHP_URL_PATH));
		$this->RedirectDownload($dlink, $FileName, $this->cookie, 0, $link);
		exit();
	}

	private function Premium() {
		$cookie = $this->login();
		$page = $this->GetPage($this->link, $cookie);
		if (!preg_match('/http:\/\/sf\d+\-\d+(:\d+)?\.slingfile\.com\/\w+\/[^\r\n"\']+/', $page, $dl)) html_error('Error[Download link not found - PREMIUM!]');
		$dlink = trim($dl[0]);
		$filename = basename(parse_url($dlink, PHP_URL_PATH));
		$this->RedirectDownload($dlink, $filename, $cookie);
	}

	private function login() {
		global $premium_acc;

		$url = 'http://www.slingfile.com/';
		$page = $this->GetPage($url . 'login');
		$this->cookie = GetCookiesArr($page);

		$pA = ($_REQUEST["premium_user"] && $_REQUEST["premium_pass"] ? true : false);
		$user = ($pA ? $_REQUEST["premium_user"] : $premium_acc["slingfile_com"]["user"]);
		$pass = ($pA ? $_REQUEST["premium_pass"] : $premium_acc["slingfile_com"]["pass"]);
		if (empty($user) || empty($pass)) html_error("Login Failed: Email or Password is empty. Please check login data.", 0);

		$post = array();
		$post['f_user'] = $user;
		$post['f_password'] = $pass;
		$post['f_keepMeLoggedIn'] = '1';
		$post['submit'] = urlencode('Login &raquo;');
		$page = $this->GetPage($url . 'login', $this->cookie, $post, $url . 'login');
		$cookie = GetCookiesArr($page, $this->cookie);
		if (!array_key_exists('cookielogin', $cookie)) html_error('Invalid account');

		$page = $this->GetPage($url . 'dashboard', $cookie);
		is_notpresent($page, '<span>Premium</span>', 'Account is not premium!');

		return $cookie;
	}

}

/*
 * slingfile.com free download plugin by Ruud v.Tony 23-Aug-2011
 * fix the countdown numeration(I should have know this when qqqw mention that before, sorry mate :( ) by Ruud v.Tony 30-Dec-2011
 * add premium account support by Ruud v.Tony 16-04-2012
 * fix free download code by Ruud v.Tony 08-05-2012
 * fix free download code by Tony Fauzi Wihana/Ruud v.Tony 10-01-2013
 */
?>
