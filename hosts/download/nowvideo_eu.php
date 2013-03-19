<?php
if (!defined('RAPIDLEECH')) {
	require_once 'index.html';
	exit;
}

class nowvideo_eu extends DownloadClass {

	public function Download($link) {
		global $premium_acc;

		if (!$_REQUEST['step']) {
			$this->page = $this->GetPage($link);
			is_present($this->page, 'This file no longer exists on our servers.');
			is_present($this->page, 'The file has failed to convert!');
		}
		$this->link = $link;
		if ($_REQUEST['premium_acc'] == 'on' && (($_REQUEST['premium_user'] && $_REQUEST['premium_pass']) || ($premium_acc['nowvideo_eu']['user'] && $premium_acc['nowvideo_eu']['pass']))) {
			return $this->Premium();
		} else {
			return $this->Free();
		}
	}

	private function Free() {
		$domain = cut_str($this->page, 'flashvars.domain="', '"');
		$file = cut_str($this->page, 'flashvars.file="', '"');
		$key = cut_str($this->page, 'flashvars.filekey="', '"');
		$code = cut_str($this->page, 'flashvars.cid="', '"');
		$page = $this->GetPage("$domain/api/player.api.php?pass=undefined&file=$file/&user=undefined&key=" . urlencode($key) . "&codes=$code", 0, 0, $domain . "/player/nowvideo.swf");
		$dlink = cut_str(urldecode($page), 'url=', '&');
		$filename = cut_str(urldecode($page), 'title=', '&');
		if (empty($dlink) || empty($filename)) html_error("Error[FREE - Download link : {$dlink} or Filename : {$filename} is empty!]");
		$this->RedirectDownload($dlink, $filename, 0, 0, $domain . "/player/nowvideo.swf");
		exit;
	}

	private function Premium() {
		$cookie = $this->login();
		$page = $this->GetPage($this->link, $cookie);
		if (!preg_match('/https?:\/\/[a-z]\d+\.nowvideo\.eu(:\d+)?\/dl\/[^\r\n\s\t"]+/', $page, $dl)) html_error('Error[PREMIUM - Download link not found!]');
		$dlink = trim($dl[0]);
		$filename = basename(parse_url($dlink, PHP_URL_PATH));
		$this->RedirectDownload($dlink, $filename, $cookie);
	}

	private function login() {
		global $premium_acc;

		$user = ($_REQUEST["premium_user"] ? trim($_REQUEST["premium_user"]) : $premium_acc ["nowvideo_eu"] ["user"]);
		$pass = ($_REQUEST["premium_pass"] ? trim($_REQUEST["premium_pass"]) : $premium_acc ["nowvideo_eu"] ["pass"]);
		if (empty($user) || empty($pass)) html_error("Login failed, username or password is empty!");

		$posturl = 'http://www.nowvideo.eu/';
		$post['user'] = $user;
		$post['pass'] = $pass;
		$post['register'] = 'Login';
		$page = $this->GetPage($posturl . 'login.php?return=', 0, $post, $posturl . 'login.php');
		$cookie = GetCookies($page);
		is_present($cookie, 'user=deleted', 'Error[Invalid login details]');

		$page = $this->GetPage($posturl . 'premium.php', $cookie);
		is_notpresent($page, 'Your premium membership expires on', 'Account isn\'t premium?');

		return $cookie;
	}

}

/*
 * Written by Tony Fauzi Wihana/Ruud v.Tony 22/01/2013
 * Updated premium by Tony Fauzi Wihana/Ruud v.Tony 13/03/2013
 */
?>
