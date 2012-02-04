<?php

if (!defined('RAPIDLEECH')) {
	require_once ("index.html");
	exit;
}

class bayfiles_com extends DownloadClass {
	private $link, $page, $cookie, $fid, $token;
	public function Download($link) {
		global $premium_acc;
		$this->link = $link;
		$this->cookie = array();

		if (empty($_REQUEST["step"]) || $_REQUEST['step'] != 1) { // Check link
			$this->page = $this->GetPage($link);
			is_present($this->page, "Invalid security token", "The link is incorrect or it has been deleted.");
			is_present($this->page, "The requested file could not be found.", "The requested file could not be found. Please check the download link.");
			$this->cookie = GetCookiesArr($this->page);
		}

		if ($_REQUEST["premium_acc"] == "on" && ((!empty($_REQUEST["premium_user"]) && !empty($_REQUEST["premium_pass"])) || (!empty($premium_acc["bayfiles_com"]["user"]) && !empty($premium_acc["bayfiles_com"]["pass"])))) {
			$this->Login();
		} elseif (isset($_REQUEST['step']) && $_REQUEST['step'] == 1) {
			$this->Captcha();
		} else {
			$this->Prepare();
		}
	}

	private function Prepare() {
		is_present($this->page, 'has recently downloaded a file. Upgrade to premium or wait ', 'Your IP has recently downloaded a file. '.(($err = cut_str($this->page, 'Upgrade to premium or wait ', '.')) ? "Wait $err before trying again." : 'Try again later.'));
		if (!preg_match('@var vfid = (\d+);@i', $this->page, $fid)) html_error("Error: Fileid not found");
		$this->fid = $fid[1];

		$page = $this->GetPage('http://bayfiles.com/ajax_download?action=startTimer&vfid='.$this->fid);

		if (!preg_match('@"token":"([^\"]+)"@i', $page, $token)) html_error("Error: Countdown token not found");
		$this->token = $token[1];

		if (!preg_match("@var delay = (\d+);@i", $this->page, $CD)) html_error("Error: Countdown not found");
		$this->CountDown($CD[1]+2);

		// Uncomment next line when bayfiles have added a reCaptcha for download
		// return $this->Captcha();

		$this->FreeDL();
	}

	private function Captcha() {
		if (isset($_REQUEST['step']) && $_REQUEST['step'] == 1) {
			if (empty($_POST['fid']) || empty($_POST['fid']) || empty($_POST['cookie'])) html_error("Error: Invalid Captcha form data.");
			if (empty($_POST['captcha'])) html_error("Error: You didn't enter the image verification code.");

			$this->cookie = decrypt(urldecode($_POST['cookie']));

			$post = array();
			$post['action'] = 'verifyCaptcha';
			$post['challenge'] = $_POST['challenge'];
			$post['response'] = $_POST['captcha'];
			$post['token'] = $this->token = $_POST['token'];
			$this->fid = $_POST['fid'];

			$page = $this->GetPage('http://bayfiles.com/ajax_captcha', $this->cookie, $post);
			is_present($page, 'Invalid captcha', 'Error: Wrong Captcha Entered.');

			if (!preg_match('@"token":"([^\"]+)"@i', $page, $token)) html_error("Error: Captcha token not found");
			$this->token = $token[1];

			return $this->FreeDL();
		} else {
			$page = $this->GetPage('http://bayfiles.com/ajax_captcha', $this->cookie, array('action' => 'getCaptcha'));
			if (!preg_match('@Recaptcha\.create\s*\(\s*[\"|\']([^\"|\'|\)]+)[\"|\']@i', $page, $pid)) html_error("Error: reCaptcha not found");

			$page = $this->GetPage("http://www.google.com/recaptcha/api/challenge?k=" . $pid[1]);
			if (!preg_match('/challenge \: \'([^\']+)/i', $page, $ch)) html_error("Error getting Captcha data.");

			$data = $this->DefaultParamArr($this->link, encrypt(CookiesToStr($this->cookie)));
			$data['challenge'] = $ch[1];
			$data['fid'] = $this->fid;
			$data['token'] = $this->token;
			$data['step'] = '1';

			//Download captcha img.
			$page = $this->GetPage("http://www.google.com/recaptcha/api/image?c=" . $ch[1]);
			$capt_img = substr($page, strpos($page, "\r\n\r\n") + 4);
			$imgfile = DOWNLOAD_DIR . "bayfiles_captcha.jpg";

			if (file_exists($imgfile)) unlink($imgfile);
			if (!write_file($imgfile, $capt_img)) html_error("Error getting CAPTCHA image.", 0);

			$this->EnterCaptcha($imgfile.'?'.time(), $data, 20);
			exit;
		}
	}

	private function FreeDL($act='getLink') {
		$post = array('action' => $act, 'vfid' => $this->fid, 'token' => $this->token);
		$page = $this->GetPage('http://bayfiles.com/ajax_download', $this->cookie, $post);

		if (!preg_match('@https?://([^/]+\.)?baycdn\.com/dl/[^\'|\"|<|>|\r|\n]+@i', $page, $dlink)) html_error("Error: Download link not found");

		$url = parse_url(html_entity_decode($dlink[0]));
		$FileName = urldecode(basename($url["path"]));
		$this->RedirectDownload($dlink[0], $FileName, $this->cookie);
	}

	private function PremiumDL() {
		$page = $this->GetPage($this->link, $this->cookie);

		if (!preg_match('@https?://([^/]+\.)?baycdn\.com/dl/[^\'|\"|<|>|\r|\n]+@i', $page, $dlink)) html_error("Error: Download link not found.");

		$url = parse_url(html_entity_decode($dlink[0]));
		$FileName = urldecode(basename($url["path"]));
		$this->RedirectDownload($dlink[0], $FileName, $this->cookie);
	}

	private function Login() {
		global $premium_acc;
		if (!empty($_REQUEST["premium_user"]) && !empty($_REQUEST["premium_pass"])) $pA = true;
		else $pA = false;
		$user = ($pA ? $_REQUEST["premium_user"] : $premium_acc["bayfiles_com"]["user"]);
		$pass = ($pA ? $_REQUEST["premium_pass"] : $premium_acc["bayfiles_com"]["pass"]);
		if (empty($user) || empty($pass)) html_error("Login Failed: Username or Password are empty. Please check login data.");

		$post = array('action'=>'login','next'=>'%252F');
		$post["username"] = urlencode($user);
		$post["password"] = urlencode($pass);

		$page = $this->GetPage('http://bayfiles.com/ajax_login', $this->cookie, $post, 'http://bayfiles.com/');
		is_present($page, 'Login failed. Please try again', 'Login Failed: Invalid username and/or password.');
		if ($err = cut_str($page, '"error":"', '"')) html_error("Login Failed: $err.");
		is_notpresent($page, 'Set-Cookie: SESSID=', 'Login Failed: Cannot get cookie.');
		$this->cookie = array_merge($this->cookie, GetCookiesArr($page));

		$page = $this->GetPage('http://bayfiles.com/account', $this->cookie, 0, 'http://bayfiles.com/');
		if (preg_match('@<div class="account-content">[\s|\t|\r|\n]+<p>((Normal)|(Premium))</p>@i', $page, $acctype) && $acctype[1] == 'Normal') {
			$this->changeMesg(lang(300)."<br /><br /><b>Account isn\\\'t premium</b><br />Using Free Download.");
			$this->page = $this->GetPage($this->link, $this->cookie);
			return $this->Prepare();
		}
		return $this->PremiumDL();
	}
}

//[28-Jan-2012]  Written by Th3-822.
//[01-Feb-2012]  Added premium support. -Th3-822

?>