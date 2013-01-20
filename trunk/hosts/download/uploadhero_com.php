<?php

if (!defined('RAPIDLEECH')) {
	require_once ('index.html');
	exit();
}

class uploadhero_com extends DownloadClass {
	private $page, $cookie;
	public function Download($link) {
		global $premium_acc;
		$this->cookie = array('lang' => 'en');
		if (empty($_POST['step']) || $_POST['step'] != 1) {
			$this->page = $this->GetPage($link, $this->cookie);
			is_present($this->page, 'The link file above no longer exists.', 'File not found.');
			$this->cookie = GetCookiesArr($this->page, $this->cookie);
		}

		if ($_REQUEST['premium_acc'] == 'on' && ((!empty($_REQUEST['premium_user']) && !empty($_REQUEST['premium_pass'])) || (!empty($premium_acc['uploadhero_com']['user']) && !empty($premium_acc['uploadhero_com']['pass'])))) $this->Login($link);
		else $this->FreeDL($link);
	}

	private function FreeDL($link) {
		if (empty($_POST['step']) || $_POST['step'] != 1) {
			if (preg_match('@/lightbox_block_download\.php\?(min=-?\d+&)?sec=\d+@i', $this->page)) {
				$page = $this->GetPage('http://uploadhero.com/lightbox_block_download.php', $this->cookie);
				if (!preg_match('@(?:id="minn">(\d+)</span>[\r\n\s\r]*)*<span [^<>]*id="secondss">(\d+)</span>@i', $page, $timer)) html_error('The last download was performed fewer than 30 minutes, you have to wait.');
				$wait = $timer[2];
				if (!empty($timer[1])) $wait += ($timer[1] * 60);
				$data = $this->DefaultParamArr($link);
				return $this->JSCountdown($wait, $data, 'You have to wait before downloading again');
			}
			if (!preg_match('@<img src="((https?://(?:[^/\r\n<>\"]+\.)?uploadhero\.com)?/captchadl\.php\?[^\r\n<>\"]+)"@i', $this->page, $cimg)) html_error('Error: CAPTCHA not found.');
			$cimg = (empty($cimg[2])) ? 'http://uploadhero.com'.$cimg[1] : $cimg[1];

			//Download captcha img.
			$page = $this->GetPage($cimg, $this->cookie);
			$capt_img = substr($page, strpos($page, "\r\n\r\n") + 4);
			$imgfile = DOWNLOAD_DIR . get_class($this) .'_captcha.jpg';

			if (file_exists($imgfile)) unlink($imgfile);
			if (!write_file($imgfile, $capt_img)) html_error('Error getting CAPTCHA image.');

			$data = $this->DefaultParamArr($link, encrypt(CookiesToStr($this->cookie)));
			$data['step'] = 1;

			$this->EnterCaptcha($imgfile.'?'.time(), $data, 20);
			exit;
		}
		if (empty($_POST['captcha'])) html_error('You didn\'t enter the image verification code.');
		$this->cookie = (!empty($_POST['cookie'])) ? StrToCookies(decrypt(urldecode($_POST['cookie']))) : array();
		$this->cookie['lang'] = 'en';

		$query = 'code='.urlencode($_POST['captcha']);
		$page = $this->GetPage($link.(strpos($link, '?') ? '&' : '?').$query, $this->cookie);

		is_present($page, 'border: solid 1px #c60000;', 'Wrong captcha entered.');

		if (!preg_match('@setTimeout[\s\t]*\([\r\n\s\t]*function[\s\t]*\(\)[\r\n\s\t]*{[\r\n\s\t]*omfg\(\);[\r\n\s\t]*},[\s\t]*(\d+)[\s\t]*\);@i', $page, $count)) html_error('Countdown not found.');
		$count[1] /= 1000;
		if ($count[1] > 0) $this->CountDown($count[1]);

		if (!preg_match('@https?://[^/\r\n\'\"\t<>]+/\?d=[^\r\n\'\"\t<>]+/[^\r\n\'\"\t<>]+@i', $page, $dlink)) html_error('Error: Download link not found.');

		$this->RedirectDownload($dlink[0], 'uploadhero_fr');
	}

	private function PremiumDL($link) {
		$page = $this->GetPage($link, $this->cookie);

		if (!preg_match('@https?://[^/\r\n\'\"\t<>]+/\?d=[^\r\n\'\"\t<>]+/[^\r\n\'\"\t<>]+@i', $page, $dlink)) html_error('Error: Download-link not found.');

		$this->RedirectDownload($dlink[0], 'uploadhero_pr');
	}

	private function Login($link) {
		global $premium_acc;
		$pA = (!empty($_REQUEST['premium_user']) && !empty($_REQUEST['premium_pass']) ? true : false);
		$user = ($pA ? $_REQUEST['premium_user'] : $premium_acc['uploadhero_com']['user']);
		$pass = ($pA ? $_REQUEST['premium_pass'] : $premium_acc['uploadhero_com']['pass']);

		if (empty($user) || empty($pass)) html_error('Login Failed: User or Password is empty. Please check login data.');
		$post = array();
		$post['pseudo_login'] = urlencode($user);
		$post['password_login'] = urlencode($pass);

		$purl = 'http://uploadhero.com/';
		$page = $this->GetPage($purl.'lib/connexion.php', $this->cookie, $post, $purl);
		$this->cookie = GetCookiesArr($page, $this->cookie);

		is_present($page, 'Username or password invalid.', 'Login Failed: Invalid Username or Password.');

		if (!preg_match('@<div id="cookietransitload"[^>]*>([^<>]+)</div>@i', $page, $uh)) html_error('Login Error: Cannot find \'uh\' cookie.'); // Why in many sites at the code the site is called as "transitfiles.com"?
		$this->cookie['uh'] = urlencode(html_entity_decode($uh[1]));
		$this->cookie['lang'] = 'en';

		$page = $this->GetPage($purl, $this->cookie, 0, $purl);
		if (stripos($page, '>Logout</a>') === false) html_error('Login Error.');

		if (stripos($page, '<td>Premium</td>') === false) {
			$this->changeMesg(lang(300).'<br /><b>Account isn\\\'t premium?</b><br />Using it as member.');
			$this->page = $this->GetPage($link, $this->cookie);
			return $this->FreeDL($link);
		} else return $this->PremiumDL($link);
	}

	public function CheckBack($headers) {
		if (preg_match('@\r\nLocation: https?://(www\.)?uploadhero\.com/optmizing@i', $headers)) html_error('[UploadHero] Server in Maintenance.');
	}
}

// [08-11-2012]  Written by Th3-822.

?>