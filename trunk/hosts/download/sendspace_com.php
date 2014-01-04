<?php
if (!defined('RAPIDLEECH')) {
	require_once("index.html");
	exit;
}

class sendspace_com extends DownloadClass {
	private $page, $cookie;
	public function Download($link) {
		global $premium_acc;
		$this->link = str_ireplace('://sendspace.com', '://www.sendspace.com', $link);
		$this->pA = (empty($_GET['premium_user']) || empty($_GET['premium_pass']) ? false : true);
		if ($_GET['premium_acc'] == 'on' && ($this->pA || (!empty($premium_acc['sendspace_com']['user']) && !empty($premium_acc['sendspace_com']['pass'])))) $this->Login();
		else $this->Free();
	}

	private function Free() {
		$this->page = $this->GetPage($this->link);
		$this->cookie = GetCookiesArr($this->page);
		$this->chkCaptcha();

		if (preg_match('@\nLocation: ((https?://www\.sendspace\.com)?/[^\r\n\"\'\t<>]+)@i', $this->page, $check)) {
			$check[1] = (empty($check[2])) ? 'http://www.sendspace.com'.$check[1] : $check[1];
			$this->page = $this->GetPage($check[1]);
			$this->cookie = GetCookiesArr($this->page, $this->cookie);
		}
		is_present($this->page, 'Sorry, the file you requested is not available.');

		if (!preg_match('@https?://(?:[a-zA-Z\d\-]+\.)*sendspace\.com/dl/[^\r\n\"\'\t<>]+@i', $this->page, $dl)) html_error('Download Link Not Found.');

		$this->RedirectDownload($dl[0], basename(parse_url($dl[0], PHP_URL_PATH)), $this->cookie);
	}

	private function Premium() {
		$this->page = $this->GetPage($this->link, $this->cookie);
		$this->chkCaptcha();
		if (preg_match('@\nLocation: ((https?://www\.sendspace\.com)?/[^\r\n\"\'\t<>]+)@i', $this->page, $check)) {
			$check[1] = (empty($check[2])) ? 'http://www.sendspace.com'.$check[1] : $check[1];
			$this->page = $this->GetPage($check[1]);
			$this->cookie = GetCookiesArr($this->page, $this->cookie);
		}
		is_present($this->page, 'Sorry, the file you requested is not available.');

		if (!preg_match('@https?://(?:[a-zA-Z\d\-]+\.)*sendspace\.com/dlp/[^\r\n\"\'\t<>]+@i', $this->page, $dl)) html_error('Download-Link Not Found.');

		$this->RedirectDownload($dl[0], basename(parse_url($dl[0], PHP_URL_PATH)), $this->cookie);
	}

	private function Login() {
		global $premium_acc;
		$site = 'http://www.sendspace.com';
		$post = array();
		$post['action'] = 'login';
		$post['submit'] = 'login';
		$post['target'] = '%252F';
		$post['action_type'] = 'login';
		$post['remember'] = '1';
		$post['username'] = urlencode($this->pA ? $_GET['premium_user'] : $premium_acc['sendspace_com']['user']);
		$post['password'] = urlencode($this->pA ? $_GET['premium_pass'] : $premium_acc['sendspace_com']['pass']);
		$post['remember'] = 'on';
		$page = $this->GetPage("$site/login.html", 0, $post, "$site/");

		is_present($page, 'check your username and password', 'Login Failed: Invalid User/Password.');
		$this->cookie = GetCookiesArr($page);
		if (empty($this->cookie['ssal'])) html_error('Login Error: Cannot find "ssal" cookie.');

		$page = $this->GetPage("$site/mysendspace/myindex.html", $this->cookie, 0, "$site/");
		is_notpresent($page, 'Your account needs to be renewed in', 'Login Failed: Account Isn\'t Premium.');

		$this->Premium();
	}

	private function Show_reCaptcha($pid, $inputs, $sname = 'Download File') {
		global $PHP_SELF;
		if (!is_array($inputs)) html_error('Error parsing captcha data.');

		// Themes: 'red', 'white', 'blackglass', 'clean'
		echo "<script language='JavaScript'>var RecaptchaOptions = {theme:'red', lang:'en'};</script>\n\n<center><form name='recaptcha' action='$PHP_SELF' method='POST'><br />\n";
		foreach ($inputs as $name => $input) echo "<input type='hidden' name='$name' id='C_$name' value='$input' />\n";
		echo "<script type='text/javascript' src='http://www.google.com/recaptcha/api/challenge?k=$pid'></script><noscript><iframe src='http://www.google.com/recaptcha/api/noscript?k=$pid' height='300' width='500' frameborder='0'></iframe><br /><textarea name='recaptcha_challenge_field' rows='3' cols='40'></textarea><input type='hidden' name='recaptcha_response_field' value='manual_challenge' /></noscript><br /><input type='submit' name='submit' onclick='javascript:return checkc();' value='$sname' />\n<script type='text/javascript'>/*<![CDATA[*/\nfunction checkc(){\nvar capt=document.getElementById('recaptcha_response_field');\nif (capt.value == '') { window.alert('You didn\'t enter the image verification code.'); return false; }\nelse { return true; }\n}\n/*]]>*/</script>\n</form></center>\n</body>\n</html>";
		exit;
	}

	private function chkCaptcha() {
		if (stripos($this->page, 'Please complete the form below:') === false) return;
		if (!empty($_POST['step']) && $_POST['step'] == '1') {
			if (empty($_POST['recaptcha_response_field'])) html_error('You didn\'t enter the image verification code.');
			$this->cookie = StrToCookies(decrypt(urldecode($_POST['cookie'])));
			$post = array('recaptcha_challenge_field' => $_POST['recaptcha_challenge_field'], 'recaptcha_response_field' => $_POST['recaptcha_response_field']);
			$this->page = $this->GetPage($this->link, $this->cookie, $post);
			if (stripos($this->page, 'You entered an invalid captcha') !== false) {
				echo "\n<span class='htmlerror'><b>You entered an invalid captcha, please try again.</b></span><br />";
				unset($_POST['step']);
				$this->chkCaptcha();
			}
		} else {
			if (!preg_match('@https?://(?:[a-zA-Z\d\-]+\.)*(?:google\.com/recaptcha/api|recaptcha\.net)/(?:challenge|noscript)\?k=([\w|\-]+)@i', $this->page, $cpid)) html_error('reCaptcha Not Found.');
			$data = $this->DefaultParamArr($this->link, encrypt(CookiesToStr($this->cookie)));
			$data['step'] = '1';
			$this->Show_reCaptcha($cpid[1], $data);
			exit;
		}
	}
}

// Use PREMIUM? [szalinski 09-May-09]
// fix free download by kaox 19-dec-2009
// Fix premium & free by Ruud v.Tony 03-Okt-2011
// [16-6-2013] Rewritten & Added captcha support. - Th3-822

?>