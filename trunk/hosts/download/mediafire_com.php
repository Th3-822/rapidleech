<?php

if (!defined('RAPIDLEECH')) {
	require_once("index.html");
	exit;
}

class mediafire_com extends DownloadClass {
	public function Download($link) {
		if (!empty($_POST['mfpassword'])) {
			$Cookies = urldecode($_POST['cookie']);
			$link = urldecode($_POST['link']);
			$page = $this->GetPage($link, $Cookies, array("downloadp" => $_POST['mfpassword']), $link);
		} else {
			$link = preg_replace("#http://(www.)?mediafire.com/(download.php)?#", "http://www.mediafire.com/", $link);
			$page = $this->GetPage($link);
			is_present($page, "error.php?errno=320", "Link is not available");
			$Cookies = GetCookies($page);
		}
		$this->MF_Captcha($link, $page);
		if (strpos($page, 'name="downloadp" id="downloadp"')) {
			$DefaultParam = $this->DefaultParamArr($link, $Cookies);
			$html = '<form action="index.php" method="POST">';
			foreach ($DefaultParam as $key => $value) {
				$html.='<input type="hidden" name="' . $key . '" value="' . $value . '"/>';
			}
			$html.='Enter your password here </br><input type="text" name="mfpassword" value="" placeholder="Enter your password here" autofocus="autofocus" required="required" /><input type="submit" name="action" value="Submit"/></form>';
			echo $html;
			exit;
		}
		if (preg_match('@Location: (http:\/\/[^\r\n]+)@i', $page, $dl) || preg_match('@<a [^>]*href="(https?://[^\"]+)"[^>]*>Download@i', $page, $dl)) {
			$dlink = trim($dl[1]);
			$this->RedirectDownload($dlink, "Mediafire.com", $Cookies);
			exit;
		} else {
			html_error("Error: Download link [FREE] not found!");
		}
	}

	private function MF_Captcha($link, &$page) {
		if (stripos($page, ">Authorize Download</a>") === false) return;
		if (!empty($_POST['step']) && $_POST['step'] == 1) {
			if (empty($_POST['recaptcha_response_field'])) html_error("You didn't enter the image verification code.");
			$post = array('recaptcha_challenge_field' => $_POST['recaptcha_challenge_field'], 'recaptcha_response_field' => $_POST['recaptcha_response_field']);
			$page = $this->GetPage($link, 0, $post);
			is_present($page, 'Your entry was incorrect, please try again!');
		} else {
			if (!preg_match('@https?://(?:[^/]+\.)?(?:(?:google\.com/recaptcha/api)|(?:recaptcha\.net))/(?:(?:challenge)|(?:noscript))\?k=([\w|\-]+)@i', $page, $pid)) html_error('Error: reCAPTCHA not found.');
			$data = $this->DefaultParamArr($link);
			$data['step'] = 1;
			$this->Show_reCaptcha($pid[1], $data);
		}
	}

	private function Show_reCaptcha($pid, $inputs) {
		global $PHP_SELF;

		if (!is_array($inputs)) {
			html_error("Error parsing captcha data.");
		}

		// Themes: 'red', 'white', 'blackglass', 'clean'
		echo "<script language='JavaScript'>var RecaptchaOptions={theme:'red', lang:'en'};</script>\n";

		echo "\n<center><form name='dl' action='$PHP_SELF' method='post'><br />\n";
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
}

/*
 * credit to farizemo [at] rapidleech forum
 * by vdhdevil
 * remove additional function for temporary fix until get finished - Ruud v.Tony 06-01-2011
 * fix for shared premium link by Ruud v.Tony 23-01-2012
 * regex fix for download link not found by Th3-822 24-02-2012
 * added support for captcha. by Th3-822 14-04-2012
 */
?>