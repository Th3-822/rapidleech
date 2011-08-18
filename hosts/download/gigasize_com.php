<?php

if (!defined('RAPIDLEECH')) {
	require_once("index.html");
	exit;
}

class gigasize_com extends DownloadClass {
	public function Download($link) {
		global $premium_acc;
		if ($_REQUEST["premium_acc"] == "on" && (($_REQUEST["premium_user"] && $_REQUEST["premium_pass"]) || ($premium_acc["gigasize_com"]["user"] && $premium_acc["gigasize_com"]["pass"]))) {
			$this->Premium($link);
		} elseif ($_POST['step'] == '1') {
			$this->Free($link);
		} else {
			$this->Retrieve($link);
		}
	}

	private function Retrieve($link) {
		$page = $this->GetPage($link);
		is_present($page, "The file you are looking for is not available");
		is_present($page, "has been removed because we have received", "The file you are looking was removed");
		is_present($page, "<strong>DOWNLOAD LIMIT</strong>", "Download limit reached... Please try again 8-10 hours later.");
		$cookie = GetCookies($page);

		if (!$fname = cut_str($page, '<strong title="','"')) html_error("Filename not found.", 0);
		if (!preg_match('/name="fileId" value="([^"]+)"/i', $page, $fileid)) html_error("Link fileId not found.", 0);
		$link = 'http://www.gigasize.com/get/'.$fileid[1];

		if (!$k = cut_str($page, "adscaptcha.com/Get.aspx?", "'")) html_error("Error getting CAPTCHA data.", 0);
		$page = $this->GetPage("http://api.adscaptcha.com/Get.aspx?$k");

		if (!$ch = cut_str($page, "challenge: '","'")) html_error("Error getting CAPTCHA image.", 0);

		$data = $this->DefaultParamArr($link, $cookie);
		$data['step'] = '1';
		$data['fileId'] = urlencode($fileid[1]);
		$data['fname'] = urlencode($fname);
		$data['adscaptcha_challenge_field'] = urlencode($ch);
		$this->EnterCaptcha("http://api.adscaptcha.com/Challenge.aspx?cid=$ch&w=180", $data, 25);
		exit();
	}

	private function Free($link) {
		if (!$fname = urldecode($_POST['fname'])) html_error("Cannot get filename.", 0);
		$post = array();
		$post['fileId'] = $_POST['fileId'];
		$post['adUnder'] = '';
		$post['adscaptcha_response_field'] = $_POST['captcha'];
		$post['adscaptcha_challenge_field'] = $_POST['adscaptcha_challenge_field'];
		$cookie = urldecode($_POST['cookie']);

		$page = $this->GetPage("http://www.gigasize.com/getoken", $cookie, $post);
		if (!preg_match('/"status":(\d+)/i', $page, $st)) html_error("Token status not found.", 0);

		if ($st[1] != 1) {
			if ($st[1] == 0) return $this->Retrieve($link); // Bad CAPTCHA
			if ($st[1] == 2) html_error("Password protected files aren't supported.", 0);
			html_error("Unknown error. ({$st[1]})");
		}

		$token = $this->GetPage('http://www.gigasize.com/formtoken', $cookie);
		$token = trim(substr($token, strpos($token, "\r\n\r\n") + 4));

		$this->CountDown(32);

		$post = array();
		$post['fileId'] = $_POST['fileId'];
		$post['token'] = $token;
		$page = $this->GetPage("http://www.gigasize.com/getoken", $cookie, $post);
		if (!preg_match('@"status":(\d+)(?:,"redirect":"([^"]+)")?@i', $page, $dl)) html_error("Download data not found.", 0);
		if ($dl[1] == 0) html_error("Error... Limit reached or need premium for download this file.", 0);
		if ($dl[1] != 1) html_error("Unknown error 2. ({$dl[1]})", 0);

		$page = $this->GetPage(str_replace('\\', '', $dl[2]), $cookie);
		if (!preg_match('@Location: (http://(www\d+\.)?gigasize\.com(:\d+)?/[^\r|\n]+)@i', $page, $dllink)) html_error("Download link not found.", 0);

		$this->RedirectDownload($dllink[1], $fname, $cookie);
	}

	private function Premium($link) {
		$cookie = $this->login();
		$page = $this->GetPage($link, $cookie);
		is_present($page, "The file you are looking for is not available");
		is_present($page, "has been removed because we have received", "The file you are looking was removed");

		if (!$fname = cut_str($page, '<strong title="','"')) html_error("Filename not found.", 0);
		if (!preg_match('/name="fileId" value="([^"]+)"/i', $page, $fileid)) html_error("Link fileId not found.", 0);
		$link = 'http://www.gigasize.com/get/'.$fileid[1];

		$post = array();
		$post['fileId'] = $fileid[1];
		$post['fileExt'] = cut_str($page, 'name="fileExt" value="','"');
		$page = $this->GetPage("http://www.gigasize.com/getoken", $cookie, $post);
		if (!preg_match('/"status":(\d+)/i', $page, $st)) html_error("Token status not found.", 0);
		if ($st[1] != 1) {
			if ($st[1] == 2) html_error("Password protected files aren't supported.", 0);
			html_error("Unknown error. ({$st[1]})");
		}

		$token = $this->GetPage('http://www.gigasize.com/formtoken', $cookie, $post);
		$token = trim(substr($token, strpos($token, "\r\n\r\n") + 4));
		$post['token'] = $token;
		$page = $this->GetPage("http://www.gigasize.com/getoken", $cookie, $post);
		if (!preg_match('@"status":(\d+)(?:,"redirect":"([^"]+)")?@i', $page, $dl)) html_error("Download data not found.", 0);
		$page = $this->GetPage(str_replace('\\', '', $dl[2]), $cookie);
		if (!preg_match('@Location: (https?://(www\d+\.)?gigasize\.com(:\d+)?/[^\r|\n]+)@i', $page, $dllink)) html_error("Download link not found.", 0);
		if (!extension_loaded('openssl')) $dllink[1] = str_replace('https://', 'http://', $dllink[1]);

		$this->RedirectDownload($dllink[1], $fname, $cookie);
	}

	private function login() {
		global $premium_acc;
		$pA = ($_REQUEST["premium_user"] && $_REQUEST["premium_pass"] ? true : false);
		$email = ($pA ? $_REQUEST["premium_user"] : $premium_acc["gigasize_com"]["user"]);
		$pass = ($pA ? $_REQUEST["premium_pass"] : $premium_acc["gigasize_com"]["pass"]);

		if (empty($email) || empty($pass)) html_error("Login Failed: Email or Password is empty. Please check login data.", 0);

		$token = $this->GetPage('http://www.gigasize.com/formtoken');
		$token = trim(substr($token, strpos($token, "\r\n\r\n") + 4));

		$post = array('func'=>'');
		$post["token"] = $token;
		$post["signRem"] = 1;
		$post["email"] = $email;
		$post["password"] = $pass;

		$page = $this->GetPage("http://www.gigasize.com/signin", 0, $post, "http://www.gigasize.com/\r\nX-Requested-With: XMLHttpRequest"); // Don't change this line...
		$cookie = GetCookiesArr($page);

		is_present($page, '"status":0', "Login Failed: Invalid Email or Password.");
		is_notpresent($page, '"premium":1', "Login Failed: Account isn't premium?.");
		is_notpresent($page, "Set-Cookie: MIIS_GIGASIZE_AUTH=", "Login Failed: Auth cookie not found.");

		return $cookie;
	}
}

// [22-7-2011]  - Written for Free download. - Th3-822
// [16-8-2011]  - Added premium support. - Th3-822

?>