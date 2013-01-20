<?php
if (!defined('RAPIDLEECH')) {
	require_once ('index.html');
	exit();
}

class hipfile_com extends DownloadClass {

	public function Download($link) {
		global $premium_acc;

		if (!$_REQUEST['step']) {
			$this->cookie = array('lang' => 'english');
			$this->page = $this->GetPage($link, $this->cookie);
			is_present($this->page, 'File Not Found');
		}
		$this->link = $link;
		if ($_REQUEST['premium_acc'] == 'on' && (($_REQUEST['premium_user'] && $_REQUEST['premium_pass']) || ($premium_acc['hipfile_com']['user'] && $premium_acc['hipfile_com']['pass']))) {
			return $this->Premium();
		} else {
			return $this->Free();
		}
	}

	private function Free() {
		$form = cut_str($this->page, '<Form method="POST" action=\'\'>', '</Form>');
		if (!preg_match_all('/<input type="hidden" name="([^"]+)" value="([^"]+)?">/', $form, $one) || !preg_match_all('/<input type="submit" name="(\w+_free)" value="([^"]+)">/', $form, $two)) html_error('Error[Post Form Data 1 not found!]');
		$match = array_merge(array_combine($one[1], $one[2]), array_combine($two[1], $two[2]));
		$post = array();
		foreach ($match as $key => $value) {
			$post[$key] = $value;
		}
		$page = $this->GetPage($this->link, $this->cookie, $post, $this->link);
		is_present($page, '<p class="err">', '</p>');
		unset($post);
		$form = cut_str($page, '<Form name="F1" method="POST"', '</Form>');
		if (preg_match('/(\d+)<\/span> seconds/', $form, $wait)) $this->CountDown($wait[1]);
		if (!preg_match_all('/<input type="hidden" name="([^"]+)" value="([^"]+)?">/', $form, $match)) html_error('Error[Post Data 2 not found!]');
		$match = array_combine($match[1], $match[2]);
		$post = array();
		foreach ($match as $key => $value) {
			$post[$key] = $value;
		}
		$page = $this->GetPage($this->link, $this->cookie, $post, $this->link);
		if (!preg_match('/https?:\/\/[\w.]+(:\d+)\/d\/[^\r\n"]+/', $page, $dl)) html_error('Error[Download Link FREE not found!]');
		$dlink = trim($dl[0]);
		$filename = basename(parse_url($dlink, PHP_URL_PATH));
		$this->RedirectDownload($dlink, $filename, $this->cookie, 0, $this->link);
		exit();
	}

	private function Premium() {

		$cookie = $this->login();
		$page = $this->GetPage($this->link, $cookie);
		if (!preg_match('/Location: (https?:\/\/[^\r\n]+)/i', $page, $dl)) {
			$form = cut_str($page, '<Form name="F1" method="POST"', '</Form>');
			if (!preg_match_all('/<input type="hidden" name="([^"]+)" value="([^"]+)?">/', $form, $match)) html_error('Error[Post Data PREMIUM not found!]');
			$match = array_combine($match[1], $match[2]);
			$post = array();
			foreach ($match as $key => $value) {
				$post[$key] = $value;
			}
			$page = $this->GetPage($this->link, $cookie, $post, $this->link);
			if (!preg_match('/(https?:\/\/[\w.]+(:\d+)\/d\/[^\r\n"]+)/', $page, $dl)) html_error('Error[Download Link PREMIUM not found!]');
		}
		$dlink = trim($dl[1]);
		$filename = basename(parse_url($dlink, PHP_URL_PATH));
		$this->RedirectDownload($dlink, $filename, $cookie);
	}

	private function login() {
		global $premium_acc;

		$user = ($_REQUEST["premium_user"] ? trim($_REQUEST["premium_user"]) : $premium_acc ["hipfile_com"] ["user"]);
		$password = ($_REQUEST["premium_pass"] ? trim($_REQUEST["premium_pass"]) : $premium_acc ["hipfile_com"] ["pass"]);
		if (empty($user) || empty($password)) html_error("Login failed, username or password is empty!");

		$posturl = 'http://hipfile.com/';
		$post['op'] = 'login';
		$post['redirect'] = urlencode($posturl);
		$post['login'] = $user;
		$post['password'] = $password;
		$post['x'] = rand(11, 48);
		$post['y'] = rand(11, 20);
		$page = $this->GetPage($posturl, $this->cookie, $post, $posturl . 'login.html');
		is_present($page, 'Incorrect Login or Password');
		$cookie = GetCookiesArr($page, $this->cookie);

		//check account
		$page = $this->GetPage($posturl . '?op=my_account', $cookie, 0, $posturl);
		is_notpresent($page, 'Premium account expire', 'Account isn\'t Premium!');

		return $cookie;
	}

}

/*
 * Hipfile free download plugin by Ruud v.Tony 02/08/2012
 * Updated to support premium by Tony Fauzi Wihana/Ruud v.Tony 09/08/2012
 */
?>
