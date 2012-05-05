<?php

if (!defined('RAPIDLEECH')) {
	require_once ("index.html");
	exit;
}

class fileover_net extends DownloadClass {
	public function Download($link) {
		global $premium_acc;
		if ($_REQUEST["premium_acc"] == "on" && ((!empty($_REQUEST["premium_user"]) && !empty($_REQUEST["premium_pass"])) || ($premium_acc["fileover_net"]["user"] && $premium_acc["fileover_net"]["pass"]))) {
			return $this->Download_Premium($link);
		} elseif ($_POST['step'] == 1) {
			return $this->Download_Free($link);
		} else {
			return $this->Prepare_Free($link);
		}
	}

	private function Prepare_Free($link) {
		$page = $this->GetPage($link);
		$cookie = GetCookies($page);
		is_present($page, "Location: /deleted/", 'Error: File not found.');
		if (preg_match('@You have to wait: [^\.]+.@i', $page, $dlt)) html_error("Download limit exceeded: {$dlt[0]}");
		if (!preg_match('@fileover\.net/(\d+)@i', $link, $lid)) html_error('Error: Link ID not found. Malformed link?');
		if (!preg_match('@"wseconds">(\d+)<@i', $page, $cD)) html_error('Error: Timer not found.');

		$page = $this->GetPage("http://fileover.net/ax/timereq.flo?{$lid[1]}", $cookie);
		if (!preg_match('@"hash":"([^"]+)"@i', $page, $lhs)) html_error('Error: Timehash not found.');
		$this->CountDown($cD[1]+1);

		$page = $this->GetPage("http://fileover.net/ax/timepoll.flo?file={$lid[1]}&hash={$lhs[1]}", $cookie);
		if (!preg_match('@/challenge\?k=([^"|\']+)(?:"|\')@i', $page, $rpid)) html_error('Error: Captcha not found.');

		$data = $this->DefaultParamArr($link, $cookie);
		$data['step'] = '1';
		$data['fo_file'] = $lid[1];
		$data['fo_hash'] = $lhs[1];

		$this->Show_reCaptcha($rpid[1], $data);
	}

	private function Show_reCaptcha($pid, $inputs) {
		global $PHP_SELF;

		if (!is_array($inputs)) {
			html_error("Error parsing captcha data.");
		}

		// Themes: 'red', 'white', 'blackglass', 'clean'
		echo "<script type='text/javascript'>var RecaptchaOptions={theme:'white', lang:'en'};</script>\n";

		echo "\n<center><form name='dl' action='$PHP_SELF' method='post' ><br />\n";
		echo "<script type='text/javascript' src='http://www.google.com/recaptcha/api/challenge?k=$pid'></script>";
		echo "<noscript><iframe src='http://www.google.com/recaptcha/api/noscript?k=$pid' height='300' width='500' frameborder='0'></iframe><br />";
		echo "<textarea name='recaptcha_challenge_field' rows='3' cols='40'></textarea><input type='hidden' name='recaptcha_response_field' value='manual_challenge' /></noscript><br />\n";
		foreach ($inputs as $name => $input) {
			echo "<input type='hidden' name='$name' id='$name' value='$input' />\n";
		}
		echo "<input type='submit' name='submit' onclick='javascript:return checkc();' value='Download File' />\n";
		echo "<script type='text/javascript'>/*<![CDATA[*/\nfunction checkc(){\nvar capt=document.getElementById('recaptcha_response_field');\nif (capt.value == '') { window.alert('You didn\'t enter the image verification code.'); return false; }\nelse { return true; }\n}\n/*]]>*/</script>\n";
		echo "</form></center>\n</body>\n</html>";
		exit;
	}

	private function Download_Free($link) {
		if (empty($_POST['recaptcha_response_field'])) {
			html_error("Error: You didn't enter the image verification code.");
		}

		$post = array();
		$post['recaptcha_challenge_field'] = $_POST['recaptcha_challenge_field'];
		$post['recaptcha_response_field'] = $_POST['recaptcha_response_field'];
		$post['recaptcha_shortencode_field'] = $_POST['recaptcha_shortencode_field'];
		$post['file'] = $_POST['fo_file'];
		$post['hash'] = $_POST['fo_hash'];
		$cookie = urldecode($_POST['cookie']);

		$page = $this->GetPage("http://fileover.net/ax/timepoll.flo", $cookie, $post);
		is_present($page, "/recaptcha/api/challenge?k=", 'Error: Entered captcha was incorrect.');

		if (!preg_match('@(?:"|\')(http://[^/]+\.fileover\.net/\d+/\w+/([^"|\']+))(?:"|\')@i', $page, $downl)) html_error('Error: Download link not found.');

		$this->RedirectDownload($downl[1], $downl[2], $cookie);
	}

	private function Download_Premium($link) {
		$page = $this->GetPage($link);
		is_present($page, "Location: /deleted/", 'Error: File not found.');

		$cookie = $this->Login();
		$page = $this->GetPage($link, $cookie);

		if (!preg_match('@Location: (https?://(?:[^/|\r|\n]+\.)?fileover.net/[^\r|\n]+)@i', $page, $dllink)) html_error("Error: Download-link not found.");

		$FileName = urldecode(basename(parse_url($dllink[1], PHP_URL_PATH)));
		$this->RedirectDownload($dllink[1], $FileName, $cookie);
	}

	private function Login() {
		global $premium_acc;
		$pA = (empty($_REQUEST["premium_user"]) || empty($_REQUEST["premium_pass"])) ? false : true;
		$email = ($pA ? $_REQUEST["premium_user"] : $premium_acc["fileover_net"]["user"]);
		$pass = ($pA ? $_REQUEST["premium_pass"] : $premium_acc["fileover_net"]["pass"]);
		if (empty($email) || empty($pass)) html_error("Login Failed: EMail or Password is empty. Please check login data.");

		$post = array();
		$post["email"] = urlencode($email);
		$post["password"] = urlencode($pass);
		$page = $this->GetPage("http://fileover.net/api/users/login/", 0, $post);
		is_notpresent($page, "Set-Cookie: email=", "Login failed: Check your login details.");
		$cookie = GetCookiesArr($page);

		$page = $this->GetPage("http://fileover.net/user/account", $cookie);
		is_notpresent($page, "<h2>Expires</h2>", "Login error: Account isn't premium?.");
		return $cookie;
	}
}

//[13-6-2011]  Written by Th3-822 (Free download only).
//[18-4-2012]  Added premium download support. - Th3-822

?>