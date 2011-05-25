<?php

if (!defined('RAPIDLEECH')) {
	require_once ("index.html");
	exit;
}

class uploadstation_com extends DownloadClass {
	public function Download($link) {
		global $premium_acc;
		if (!$_REQUEST["step"]) { // Check link
			$this->page = $this->GetPage($link);
			is_present($page, "The file could not be found.", "The file could not be found. Please check the download link.");
			$this->cookie = GetCookies($this->page);
		}

		$this->link = $link;
		if (($_REQUEST["premium_acc"] == "on" && $_REQUEST["premium_user"] && $_REQUEST["premium_pass"]) ||
			($_REQUEST["premium_acc"] == "on" && $premium_acc["uploadstation_com"]["user"] && $premium_acc["uploadstation_com"]["pass"])) {
			return $this->Download_Premium();
		} elseif ($_REQUEST['step'] == 1) {
			return $this->Check_Captcha();
		} else {
			return $this->Prepare_Free();
		}
	}

	private function Download_Premium() {
		$cookie = $this->Login();
		$page = $this->GetPage($this->link, $cookie);

		if (stristr($page, "HTTP/1.1 200 OK\r\n")) {
			$page = $this->GetPage($this->link, $cookie, array('download'=>'premium'));
		}
		if (preg_match('/Location: (http:\/\/d\d+.uploadstation.com[^\r\n]+)/i', $page, $D)) {
			$dllink = $D[1];
		} else {
			is_present($page, "You are not able to download", "Error: Account isn't premium. [P6c]");
			html_error("Download-link not found. [P7]");
		}

		$filename = parse_url($dllink);
		$filename = urldecode(basename($filename["path"]));
		$this->RedirectDownload($dllink, $filename, $cookie);
	}

	private function Login() {
		global $premium_acc;

		$pA = ($_REQUEST["premium_user"] && $_REQUEST["premium_pass"] ? true : false);
		$user = ($pA ? $_REQUEST["premium_user"] : $premium_acc["uploadstation_com"]["user"]);
		$pass = ($pA ? $_REQUEST["premium_pass"] : $premium_acc["uploadstation_com"]["pass"]);
		if (empty($user) || empty($pass)) {
			html_error("Login Failed: Username or Password is empty. Please check login data. [P1]");
		}

		$postURL = "http://www.uploadstation.com/login.php";
		$post['loginUserName'] = $user;
		$post['loginUserPassword'] = $pass;
		$post['autoLogin'] = 'on';
		$post['loginFormSubmit'] = 'Login';

		$page = $this->GetPage($postURL, 0, $post, 'http://www.uploadstation.com/');
		is_present($page, "should be larger than or equal to 6", "Username or password too short. [P2]");
		is_present($page, "Username doesn't exist.", "Username doesn't exist. [P3]");
		is_present($page, "Wrong password.", "Wrong password. [P4]");
		is_notpresent($page, "Logging in", "Login error. [P5]");

		$cookie = GetCookies($page);
		is_notpresent($cookie, "Cookie=", "Login error. Cookie not found. [P5b]");

		$page = $this->GetPage("http://www.uploadstation.com/dashboard.php", $cookie, 0, 'http://www.uploadstation.com/');
		is_present($page, "acctype_free", "Login error. Account isn't premium. [P6]");
		is_notpresent($page, "Expiry date: ", "Login error. Account isn't premium? [P6b]");

		return $cookie;
	}

	private function Prepare_Free() {
		$check = $this->GetPage($this->link, $this->cookie, array('checkDownload' => 'check'));
		if (!preg_match('/\{"([^"]+)":"([^"]+)"(?:,"([^"]+)":(\d+))?\}/i', $check, $j)) {
			is_present($check, "fail404", "Error checking for time/captcha. [F1]");
			html_error("Error. [F2]");
		}
		if ($j[1] == 'success') {
			switch ($j[2]) {
				case 'showCaptcha':
					return $this->showCaptcha();
					break;
				case 'showTimmer':
					return $this->showTimmer();
					break;
			}
		}
		is_present($j[2], "timeLimit", "You need to wait 300 seconds to download next file.");
		is_present($j[2], "captchaFail", "Your IP has failed the captcha too many times. Please retry later. (~{$j[4]} mins)");

		html_error("Error checking for time/captcha. {$j[0]} [F3]");
	}

	private function showCaptcha() {
		global $Referer;
		if (!preg_match("/reCAPTCHA_publickey='([^\']+)/i", $this->page, $A)) {
			html_error("Error: CAPTCHA not found. [F4]");
		}
		$pid = $A[1];

		$data['recaptcha_shortencode_field'] = cut_str($this->page,'name="recaptcha_shortencode_field" value="','"');;
		$data['step'] = '1';
		$data['link'] = urlencode($this->link);
		$data['referer'] = urlencode($Referer);
		$data['cookie'] = urlencode($this->cookie);

		$this->Show_reCaptcha($pid, $data);
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

	private function showTimmer() {
		$check = $this->GetPage($this->link, $this->cookie, array('downloadLink' => 'wait'));
		if (!preg_match('/\r\n\r\n(?:\w|\d)+\r\n[^\d]+(\d+)/i', $check, $j)) {
			is_present($check, "fail\r\n", "Error checking for time/captcha... Maybe is overloaded, please try again. [F7]");
			is_present($check, "fail404\r\n", "Error checking for time/captcha. [F8]");
			html_error("Failed to get link time-lock. [F9]");
		}

		$this->CountDown($j[1] + 2); // Added 2 seconds...

		$check = $this->GetPage($this->link, $this->cookie, array('downloadLink' => 'show'));
		is_present($check, "fail\r\n", "Error getting download link... Maybe is overloaded, please try again. [F10]");
		is_present($check, "fail404\r\n", "Error getting download link. [F11]");

		$this->page = $this->GetPage($this->link, $this->cookie, array('download' => 'normal'));
		return $this->Download_Free();
	}

	private function Download_Free() {
		if (preg_match('/Location: (http:\/\/d\d+.uploadstation.com[^\r\n]+)/i', $this->page, $D)) {
			$dllink = $D[1];
		} else {
			is_present($this->page, "<h1>You need to wait", "You need to wait 300 seconds to download next file.");
			html_error("Download-link not found. [F12]");
		}

		$filename = parse_url($dllink);
		$filename = urldecode(basename($filename["path"]));
		$this->RedirectDownload($dllink, $filename);
	}

	private function Check_Captcha() {
		$this->cookie = urldecode($_POST['cookie']);
		if (empty($_POST['recaptcha_response_field'])) {
			html_error("You didn't enter the image verification code.");
		}

		$post = array();
		$post['recaptcha_challenge_field'] = $_POST['recaptcha_challenge_field'];
		$post['recaptcha_response_field'] = $_POST['recaptcha_response_field'];
		$post['recaptcha_shortencode_field'] = $_POST['recaptcha_shortencode_field'];
		$page = $this->GetPage('http://www.uploadstation.com/checkReCaptcha.php', $this->cookie, $post);
		is_present($page, "fail\r\n", "Error checking captcha.... Maybe is overloaded, please try again. [F5]");
		is_present($page, "fail404\r\n", "Error checking captcha. [F5b]");

		if (!preg_match('/\{"success":(\d)(?:,"([^"]+)":"([^"]+)")?\}/i', $page, $stat)) {
			is_present($page, '"error":"captcha-fail"', "Your IP has failed the captcha too many times. Please retry later.");
			html_error("Error validating CAPTCHA. [F6]");
		}

		if ($stat[1] == '1') {
			return $this->showTimmer();
		} elseif (stristr($stat[3], 'incorrect-captcha-sol')) {
			html_error("Entered code was incorrect.");
		}

		html_error("Error validating CAPTCHA: {$stat[0]}.. Please try again. [F6b]");
	}
}

//[08-4-2011]  Written by Th3-822 (Free download only).
//[09-4-2011]  Added & checked error msgs && 2 more secs to the countdown (no more error F10 (old F9)). - Th3-822.
//[16-4-2011]  Added support for download with Premium Account. - Th3-822.

?>