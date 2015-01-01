<?php

if (!defined('RAPIDLEECH')) {
	require_once('index.html');
	exit();
}

class dizzcloud_com extends DownloadClass {
	private $page, $cookie;
	public function Download($link) {
		global $premium_acc;
		$this->cookie = array();
		if (!preg_match('@^https?://(?:www\.)?dizzcloud\.com/dl/(\w{7})/?@i', $link, $fid)) html_error('Invalid Link?');
		$this->link = $fid[0];
		$this->fid = $fid[1];

		if (empty($_POST['step']) || $_POST['step'] != '1') {
			$this->page = $this->GetPage($link, $this->cookie);
			is_present($this->page, '>File not found<', 'The file you were looking for could not be found');
			is_present($this->page, 'File not found or deleted');
		}

		if ($_REQUEST['premium_acc'] == 'on' && ((!empty($_REQUEST['premium_user']) && !empty($_REQUEST['premium_pass'])) || (!empty($premium_acc['dizzcloud_com']['user']) && !empty($premium_acc['dizzcloud_com']['pass'])))) $this->Login();
		else $this->FreeDL();
	}

	private function Get_Reply($content) {
		if (!function_exists('json_decode')) html_error('Error: Please enable JSON in php.');
		$content = ltrim($content);
		if (($pos = strpos($content, "\r\n\r\n")) > 0) $content = trim(substr($content, $pos + 4));
		$cb_pos = strpos($content, '{');
		$sb_pos = strpos($content, '[');
		if ($cb_pos === false && $sb_pos === false) html_error('Json start braces not found.');
		$sb = ($cb_pos === false || $sb_pos < $cb_pos) ? true : false;
		$content = substr($content, strpos($content, ($sb ? '[' : '{')));$content = substr($content, 0, strrpos($content, ($sb ? ']' : '}')) + 1);
		if (empty($content)) html_error('No json content.');
		$rply = json_decode($content, true);
		if ($rply === NULL) html_error('Error reading json.');
		return $rply;
	}

	private function FreeDL() {
		if (empty($_POST['step']) || $_POST['step'] != '1') {
			if (preg_match('@Next free download from your ip will be available in <b>\d+ \w+@i', $this->page, $err)) html_error($err[0].'</b>');
			if (!preg_match('@https?://(?:[^/]+\.)?(?:(?:google\.com/recaptcha/api)|(?:recaptcha\.net))/(?:(?:challenge)|(?:noscript))\?k=([\w\.\-]+)@i', $this->page, $pid)) html_error('reCAPTCHA not found.');
			$this->cookie = GetCookiesArr($this->page, $this->cookie);

			$data = $this->DefaultParamArr($this->link, (empty($this->cookie)) ? 0 : encrypt(CookiesToStr($this->cookie)));
			$data['step'] = '1';

			$this->reCAPTCHA($pid[1], $data);
			exit;
		} else {
			if (empty($_POST['recaptcha_response_field'])) html_error('You didn\'t enter the image verification code.');
			if (!empty($_POST['cookie'])) $this->cookie = StrToCookies(decrypt(urldecode($_POST['cookie'])));

			$query = array();
			$query['type'] = 'recaptcha';
			$query['challenge'] = urlencode($_POST['recaptcha_challenge_field']);
			$query['capture'] = urlencode($_POST['recaptcha_response_field']);

			$page = $this->GetPage($this->link.'?'.http_build_query($query), $this->cookie);
			$reply = $this->Get_Reply($page);
			if (!empty($reply['err'])) html_error('Error: '.htmlentities($reply['err']));
			if (empty($reply['href']) || !preg_match('@https?://[\w\-]+\.cloudstoreservice\.net/[^\'\"\t<>\r\n]+@i', $reply['href'], $dlink)) {
				if (!empty($reply['href']) && stripos($reply['href'], '/dl/') === 0) html_error('Expired/Used/Invalid DL Session.');
				html_error('Error: Download link not found.');
			}

			$this->RedirectDownload($dlink[0], urldecode(basename(parse_url($dlink[0], PHP_URL_PATH))));
		}
	}

	private function PremiumDL() {
		$page = $this->GetPage($this->link, $this->cookie);
		if (!preg_match('@https?://[\w\-]+\.cloudstoreservice\.net/[^\'\"\t<>\r\n]+@i', $page, $dlink)) html_error('Error: Download-link not found.');

		$this->RedirectDownload($dlink[0], urldecode(basename(parse_url($dlink[0], PHP_URL_PATH))));
	}

	private function Login() {
		global $premium_acc;
		$pA = (!empty($_REQUEST['premium_user']) && !empty($_REQUEST['premium_pass']) ? true : false);
		$user = ($pA ? $_REQUEST['premium_user'] : $premium_acc['dizzcloud_com']['user']);
		$pass = ($pA ? $_REQUEST['premium_pass'] : $premium_acc['dizzcloud_com']['pass']);

		if (empty($user) || empty($pass)) html_error('Login Failed: User or Password is empty. Please check login data.');
		$post = array();
		$post['email'] = urlencode($user);
		$post['pass'] = urlencode($pass);

		$purl = 'http://dizzcloud.com/';
		$page = $this->GetPage($purl.'login', $this->cookie, $post, $purl.'login');

		$this->cookie = GetCookiesArr($page);
		if (empty($this->cookie['auth_uid']) || empty($this->cookie['auth_hash'])) html_error('Login failed: User/Password incorrect?.');

		$page = $this->GetPage($purl, $this->cookie, 0, $purl.'login');
		if (stripos($page, '/logout') === false) html_error('Login Error.');

		if (stripos($page, '>Premium till') === false) {
			$this->changeMesg(lang(300).'<br /><b>Account isn\\\'t premium</b><br />Using it as member.');
			$this->page = $this->GetPage($this->link, $this->cookie);
			return $this->FreeDL();
		} else return $this->PremiumDL();
	}
}

// [20-7-2013]  Written by Th3-822.
// [24-3-2014]  Fixed FreeDL. - Th3-822

?>