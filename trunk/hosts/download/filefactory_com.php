<?php

if (!defined('RAPIDLEECH')) {
	require_once ("index.html");
	exit();
}

class filefactory_com extends DownloadClass {
	public function Download($link) {
		global $premium_acc;

		if (($_REQUEST["premium_acc"] == "on" && $_REQUEST["premium_user"] && $_REQUEST["premium_pass"]) ||
			($_REQUEST["premium_acc"] == "on" && $premium_acc["filefactory_com"]["user"] && $premium_acc["filefactory_com"]["pass"])) {
			$this->Download_Premium($link);
		} elseif ($_POST['step'] == 1) {
			return $this->Download_Free($link);
		} else {
			return $this->Prepare_Free($link);
		}

	}

	private function Prepare_Free($link) {
		$page = $this->GetPage($link);
		if (preg_match('/Location: .*(\/file\/.+)/i', $page, $RD)) {
			$link = "http://www.filefactory.com" . $RD[1];
			$page = $this->GetPage($link);
		}
		$cookie = GetCookies($page);

		is_present($page, "This file has been deleted", "File deleted.");
		is_present($page, "this file is no longer available", "The file is no longer available.");
		is_present($page, "<strong>temporarily limited</strong>",
			"Your access to the free download service has been <strong>temporarily limited</strong> to prevent abuse... Please wait 10 minutes or more and try again.");

		if (preg_match('/check:\'([^\']+)/i', $page, $ck) && preg_match('/Recaptcha\.create\("([^"]+)/i', $page,
			$pid)) {
			$this->getcaptcha($ck[1], $pid[1], $link, $cookie);
		} else {
			html_error("Error getting CAPTCHA.");
		}
	}

	private function getcaptcha($check, $ppid, $link, $cookie) {
		global $Referer;

		$data['check'] = $check;
		$data['step'] = '1';
		$data['link'] = urlencode($link);
		$data['referer'] = urlencode($Referer);
		$data['cookie'] = urlencode($cookie);

		$this->Show_reCaptcha($ppid, $data);
	}

	private function Show_reCaptcha($pid, $inputs) {
		global $PHP_SELF;

		if (!is_array($inputs)) {
			html_error("Error parsing captcha data.");
		}

		// Themes: 'red', 'white', 'blackglass', 'clean'
		echo "<script language='JavaScript'>var RecaptchaOptions={theme:'red', lang:'en'};</script>\n";

		echo "\n<center><form name='dl' action='$PHP_SELF' method='post' ><br />\n";
		foreach ($inputs as $name => $input) {
			echo "<input type='hidden' name='$name' id='$name' value='$input' />\n";
		}
		echo "<script type='text/javascript' src='http://www.google.com/recaptcha/api/challenge?k=$pid'></script>";
		echo "<noscript><iframe src='http://www.google.com/recaptcha/api/noscript?k=$pid' height='300' width='500' frameborder='0'></iframe><br />";
		echo "<textarea name='recaptcha_challenge_field' rows='3' cols='40'></textarea><input type='hidden' name='recaptcha_response_field' value='manual_challenge' /></noscript><br />";
		echo "<input type='submit' name='submit' onclick='javascript:return checkc();' value='Download File' />\n";
		echo "<script type='text/javascript'>/*<![CDATA[*/\nfunction checkc(){\nvar capt=document.getElementById('recaptcha_response_field');\nif (capt.value == '') { window.alert('You didn\'t enter the image verification code.'); return false; }\nelse { return true; }\n}\n/*]]>*/</script>\n";
		echo "</form></center>\n</body>\n</html>";
		exit;
	}

	private function Download_Free($link) {
		if (empty($_POST['recaptcha_response_field'])) {
			html_error("You didn't enter the image verification code.");
		}
		$post['recaptcha_challenge_field'] = $_POST['recaptcha_challenge_field'];
		$post['recaptcha_response_field'] = $_POST['recaptcha_response_field'];
		$post['recaptcha_shortencode_field'] = $_POST['recaptcha_shortencode_field'];
		$post['check'] = $_POST['check'];
		$cookie = urldecode($_POST['cookie']);

		$page = $this->checkcaptcha($post, $cookie);

		is_present($page, "<strong>temporarily limited</strong>",
			"Your access to the free download service has been <strong>temporarily limited</strong> to prevent abuse... Please wait 10 minutes or more and try again.");

		if (preg_match('/href="([^"]+)" id="downloadLinkTarget"/i', $page, $D)) {
			$dllink = $D[1];
		} else {
			html_error("Download-link not found.");
		}

		if (preg_match('/"countdown">(\d+)/i', $page, $C)) {
			$wait = $C[1];
			$this->CountDown($wait);
		} else {
			html_error("Failed to get link time-lock.");
		}

		$filename = parse_url($dllink);
		$filename = html_entity_decode(basename($filename["path"]));

		if (stristr($dllink, ";")) {
			$dllink = str_replace(array('&',';'), '', $dllink);
		}

		$this->RedirectDownload($dllink, $filename, $cookie, 0, 0, $filename);
	}

	private function checkcaptcha($post, $cookie) {
		$page = $this->GetPage("http://www.filefactory.com/file/checkCaptcha.php", $cookie, $post);
		if (!preg_match('/\{status:"([^"]+)",(path|message):"([^"]+)"\}/i', $page, $stat)) {
			html_error("Error validating CAPTCHA.");
		}

		if ($stat[1] == 'ok') {
			return $this->GetPage("http://www.filefactory.com/" . $stat[3], $cookie);
		} elseif (stristr($stat[3], 'incorrect')) {
			html_error("Entered code was incorrect.");
		}

		html_error("Error validating CAPTCHA: {$stat[3]}.. Please try again.");
	}

	private function Download_Premium($link) {
		global $premium_acc;

		$email = $_REQUEST["premium_user"] ? $_REQUEST["premium_user"] : $premium_acc["filefactory_com"]["user"];
		$password = $_REQUEST["premium_pass"] ? $_REQUEST["premium_pass"] : $premium_acc["filefactory_com"]["pass"];
		$auth = base64_encode($email . ":" . $password);

		$cookie = $this->login($email, $password);

		$page = $this->GetPage($link);
		if (preg_match('/Location: .*(\/file\/.+)/i', $page, $RD)) {
			$link = "http://www.filefactory.com" . $RD[1];
			$page = $this->GetPage($link);
		}
		is_present($page, "This file has been deleted", "File deleted.");
		is_present($page, "this file is no longer available", "The file is no longer available.");

		$page = $this->GetPage($link, 0, 0, 0, $auth);

		if (stristr($page, "Location:")) {
			$dllink = trim(cut_str($page, "Location:", "\n"));
		} else {
			html_error("Download-link not found.");
		}

		$filename = parse_url($dllink);
		$filename = html_entity_decode(basename($filename["path"]));

		if (stristr($dllink, ";")) {
			$dllink = str_replace(array('&',';'), '', $dllink);
		}

		$this->RedirectDownload($dllink, $filename, $cookie, 0, 0, $filename);
	}

	private function login($email, $password, $chkprem = true) {
		if (empty($email) || empty($password)) {
			html_error("Login Failed: Email or Password is empty. Please check login data.");
		}

		$postURL = "http://www.filefactory.com/member/login.php";
		$post["redirect"] = "/member/";
		$post["email"] = $email;
		$post["password"] = $password;
		$page = $this->GetPage($postURL, 0, $post, $postURL);
		$cookie = GetCookies($page);

		is_present($page, "?err=", "Login Failed: The email or password you have entered is incorrect.");
		is_notpresent($cookie, "ff_membership=", "Login Failed.");

		if ($chkprem) {
			$page = $this->GetPage("http://www.filefactory.com/?login=1", $cookie);
			is_present($page, '<span>Free <a href="/premium/"', "Login Failed: The account isn't premium.");
		}

		return $cookie;
	}

}

//[26-Nov-2010] Written by Th3-822.
//[ 20-Feb-2011 ] Fixed regex for redirect & Fixed errors in filename & Changed captcha funtion for show reCaptcha. - Th3-822


?>