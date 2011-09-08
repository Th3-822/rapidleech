<?php

if (!defined('RAPIDLEECH')) {
	require_once ("index.html");
	exit();
}

class filedino_com extends DownloadClass {
	public function Download($link) {
		global $premium_acc;

		// Checking link...
		if (substr($link, -1) == '/') $link = substr($link, 0, -1);
		if (!$_POST['step']) {
			$page = $this->GetPage($link, 'lang=english');
			is_present($page, "File Not Found");
		}

		if ($_REQUEST["premium_acc"] == "on" && (($_REQUEST["premium_user"] && $_REQUEST["premium_pass"]) || ($premium_acc["filedino_com"]["user"] && $premium_acc["filedino_com"]["pass"]))) {
			$this->Premium($link);
		} elseif ($_POST['step'] == '1') {
			$this->FreeDL($link);
		} else {
			$this->Free($link, $page);
		}
	}

	private function Free($link, $page) {
		$post = array();
		$post['op'] = cut_str($page, 'name="op" value="', '"');
		$post['usr_login'] = cut_str($page, 'name="usr_login" value="', '"');
		$post['id'] = cut_str($page, 'name="id" value="', '"');
		$post['fname'] = cut_str($page, 'name="fname" value="', '"');
		$post['referer'] = urlencode(cut_str($page, 'name="referer" value="', '"'));
		$post['method_free'] = '+';

		$page = $this->GetPage($link, 'lang=english', $post);
		if (!preg_match('@/challenge\?k=([^"|\']+)(?:"|\')@i', $page, $rpid)) html_error('Error: Captcha not found.', 0);

		$capt = $this->GetPage("http://www.google.com/recaptcha/api/challenge?k=" . $rpid[1]);
		if (!preg_match('/challenge \: \'([^\']+)/i', $capt, $ch)) html_error("Error getting CAPTCHA data.", 0);

		$this->CountDown(10);

		$data = $this->DefaultParamArr($link);
		$data['step'] = '1';
		$data['op'] = urlencode(cut_str($page, 'name="op" value="', '"'));
		$data['id'] = urlencode(cut_str($page, 'name="id" value="', '"'));
		$data['rand'] = urlencode(cut_str($page, 'name="rand" value="', '"'));
		$data['d_referer'] = urlencode(cut_str($page, 'name="referer" value="', '"'));
		$data['challenge'] = $ch[1];

		//Download captcha image.
		$page = $this->GetPage("http://www.google.com/recaptcha/api/image?c=" . $ch[1]);
		$capt_img = substr($page, strpos($page, "\r\n\r\n") + 4);
		$imgfile = DOWNLOAD_DIR . "filedino_captcha.jpg";

		if (file_exists($imgfile)) unlink($imgfile);
		if (!write_file($imgfile, $capt_img)) html_error("Error getting CAPTCHA image.", 0);

		$this->EnterCaptcha("$imgfile?".time(), $data, 20);
		exit;
	}

	private function FreeDL($link) {
		$post = array();
		$post['op'] = $_POST['op'];
		$post['id'] = $_POST['id'];
		$post['rand'] = $_POST['rand'];
		$post['referer'] = $_POST['d_referer'];
		$post['method_free'] = '+';
		$post['method_premium'] = '';
		$post['recaptcha_challenge_field'] = $_POST['challenge'];
		$post['recaptcha_response_field'] = $_POST['captcha'];
		$post['down_script'] = 1;

		$page = $this->GetPage($link, 'lang=english', $post);
		is_present($page, "Wrong captcha", "Error: Entered CAPTCHA was incorrect.");
		if (!preg_match('@Location: ([^\r|\n]+)@i', $page, $dllink)) html_error("Error: Download link not found.", 0);

		$filename = parse_url($dllink[1]);
		$filename = urldecode(basename($filename["path"]));

		$this->RedirectDownload($dllink[1], $filename);
	}

	private function Premium($link) {
		$cookie = $this->Login();
		$page = $this->GetPage($link, $cookie);

		$post = array();
		$post['op'] = cut_str($page, 'name="op" value="', '"');
		$post['id'] = cut_str($page, 'name="id" value="', '"');
		$post['rand'] = cut_str($page, 'name="rand" value="', '"');
		$post['referer'] = cut_str($page, 'name="referer" value="', '"');
		$post['method_free'] = '';
		$post['method_premium'] = 1;
		$post['down_direct'] = 1;

		$page = $this->GetPage($link, $cookie, $post);

		if(!preg_match('@https?://(?:www\.)?filedino\.com/files/\w+/\w+/[^"|\'|<]+@i', $page, $dllink)) html_error("Error: Download link not found.", 0);

		$filename = parse_url($dllink[0]);
		$filename = urldecode(basename($filename["path"]));

		$this->RedirectDownload($dllink[0], $filename, $cookie);
	}

	private function Login() {
		global $premium_acc;
		$pA = ($_REQUEST["premium_user"] && $_REQUEST["premium_pass"] ? true : false);
		$user = ($pA ? $_REQUEST["premium_user"] : $premium_acc["filedino_com"]["user"]);
		$pass = ($pA ? $_REQUEST["premium_pass"] : $premium_acc["filedino_com"]["pass"]);

		if (empty($user) || empty($pass)) html_error("Login Failed: User or Password is empty. Please check login data.", 0);
		$post = array();
		$post['login'] = $user;
		$post['password'] = $pass;
		$post['op'] = "login";
		$post['redirect'] = "";

		$page = $this->GetPage('http://www.filedino.com/', 'lang=english', $post, 'http://www.filedino.com/login.html');
		is_present($page, "Incorrect Login or Password", "Login failed: User/Password incorrect.");
		is_notpresent($page, 'Set-Cookie: xfss=', 'Error: Cannot find session cookie.');
		$cookie = "lang=english; " . GetCookies($page);

		$page = $this->GetPage('http://www.filedino.com/?op=my_account', $cookie);
		is_notpresent($page, 'Premium account expire', 'Error: Account isn\'t premium?.');
		return $cookie;
	}

}

//[07-9-2011]  Written by Th3-822.

?>