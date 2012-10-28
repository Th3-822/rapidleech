<?php

if (!defined('RAPIDLEECH')) {
	require_once('index.html');
	exit;
}

class mediafire_com extends DownloadClass {
	public function Download($link) {
		if (!empty($_POST['mfpassword'])) {
			$Cookies = urldecode($_POST['cookie']);
			$link = urldecode($_POST['link']);
			$page = $this->GetPage($link, $Cookies, array("downloadp" => $_POST['mfpassword']), $link);
		} else {
			$link = preg_replace('@https?://([^/]+\.)?mediafire\.com/((download\.php)|(file/))?\??@i', 'http://www.mediafire.com/?', $link);
			$page = $this->GetPage($link);
			if (preg_match('@/error\.php\?errno=\d+@i', $page, $redir)) {
				$page = $this->GetPage('http://www.mediafire.com'.$redir[0]);
				if (preg_match('@error_msg_title">\s*([^\r\n<>]+)\s*<@i', $page, $err)) html_error($err[1]);
				html_error('Link is not available');
			}
			$Cookies = GetCookiesArr($page);
		}
		$this->MF_Captcha($link, $page);
		if (strpos($page, 'name="downloadp" id="downloadp"')) {
			$DefaultParam = $this->DefaultParamArr($link, $Cookies);
			$html = '<form action="index.php" method="POST">';
			foreach ($DefaultParam as $key => $value) $html.='<input type="hidden" name="' . $key . '" value="' . $value . '"/>';
			$html.='Enter your password here </br><input type="text" name="mfpassword" value="" placeholder="Enter your password here" autofocus="autofocus" required="required" /><input type="submit" name="action" value="Submit"/></form>';
			echo $html;
			exit;
		}
		if (preg_match('@Location: (http:\/\/[^\r\n]+)@i', $page, $dl) || preg_match('@\w+\s*=\s*\"(https?://[^\"]+)\"\s*;@i', $page, $dl)) {
			$dlink = trim($dl[1]);
			$this->RedirectDownload($dlink, "Mediafire.com", $Cookies);
		} else html_error("Error: Download link [FREE] not found!");
	}

	private function MF_Captcha($link, &$page) {
		if (stripos($page, ">Authorize Download</a>") === false) return;
		if (!empty($_POST['step']) && $_POST['step'] == 1) {
			if (empty($_POST['recaptcha_response_field']) && empty($_POST['captcha'])) html_error('You didn\'t enter the image verification code.');
			if (empty($_POST['captcha'])) $post = array('recaptcha_challenge_field' => $_POST['recaptcha_challenge_field'], 'recaptcha_response_field' => $_POST['recaptcha_response_field']);
			else {
				$post = array();
				foreach ($_POST['T8'] as $n => $v) $post[urlencode($n)] = $v;
				$post['adcopy_response'] = $_POST['captcha'];

				$url = 'http://api.solvemedia.com/papi/verify.noscript';
				$page = $this->GetPage($url, 0, $post, $link);

				if (!preg_match('@(https?://[^/\'\"<>\r\n]+)?/papi/verify\.pass\.noscript\?[^/\'\"<>\r\n]+@i', $page, $resp)) {
					is_present($page, '/papi/challenge.noscript', 'Wrong CAPTCHA entered.');
					html_error('Error sending CAPTCHA.');
				}
				$resp = (empty($resp[1])) ? 'http://api.solvemedia.com'.$resp[0] : $resp[0];

				$page = $this->GetPage($resp, 0, 0, $url);
				if (!preg_match('@>[\s\t\r\n]*([^<>\r\n]+)[\s\t\r\n]*</textarea>@i', $page, $gibberish)) html_error('CAPTCHA response not found.');

				$post = array('adcopy_challenge' => urlencode($gibberish[1]), 'adcopy_response' => 'manual_challenge');
			}
			$page = $this->GetPage($link, 0, $post);
			is_present($page, 'Your entry was incorrect, please try again!');
		} else {
			if (!preg_match('@https?://(?:[^/]+\.)?(?:(?:google\.com/recaptcha/api)|(?:recaptcha\.net))/(?:(?:challenge)|(?:noscript))\?k=([\w|\-]+)@i', $page, $pid) && !preg_match('@http://api\.solvemedia\.com/papi/challenge\.noscript\?k=[\w\.-]+@i', $page, $spid)) html_error('Error: CAPTCHA not found.');
			$data = $this->DefaultParamArr($link);
			$data['step'] = 1;
			if (!empty($spid)) {
				$page = $this->GetPage($spid[0], 0, 0, $link);
				if (!preg_match('@<img [^/<>]*src\s?=\s?\"((https?://[^/\"<>]+)?/papi/media[^\"<>]+)\"@i', $page, $imgurl)) html_error('CAPTCHA img not found.');
				$imgurl = (empty($imgurl[2])) ? 'http://api.solvemedia.com'.$imgurl[1] : $imgurl[1];

				if (!preg_match_all('@<input [^/|<|>]*type\s?=\s?\"?hidden\"?[^/<>]*\s?name\s?=\s?\"(\w+)\"[^/<>]*\s?value\s?=\s?\"([^\"<>]+)\"[^/<>]*/?\s*>@i', $page, $forms)) html_error('CAPTCHA data not found.');
				$forms = array_combine($forms[1], $forms[2]);
				foreach ($forms as $n => $v) $data["T8[$n]"] = urlencode($v);

				//Download captcha img.
				$page = $this->GetPage($imgurl);
				$capt_img = substr($page, strpos($page, "\r\n\r\n") + 4);
				$imgfile = DOWNLOAD_DIR . 'mediafire_captcha.gif';

				if (file_exists($imgfile)) unlink($imgfile);
				if (!write_file($imgfile, $capt_img)) html_error('Error getting CAPTCHA image.');

				$this->EnterCaptcha($imgfile.'?'.time(), $data, 20);
				exit();
			} else $this->Show_reCaptcha($pid[1], $data);
		}
	}

	private function Show_reCaptcha($pid, $inputs, $sname = 'Download File') {
		global $PHP_SELF;
		if (!is_array($inputs)) html_error('Error parsing captcha data.');

		// Themes: 'red', 'white', 'blackglass', 'clean'
		echo "<script language='JavaScript'>var RecaptchaOptions = {theme:'red', lang:'en'};</script>\n";

		echo "\n<center><form name='recaptcha' action='$PHP_SELF' method='post'><br />\n";
		foreach ($inputs as $name => $input) echo "<input type='hidden' name='$name' id='$name' value='$input' />\n";
		echo "<script type='text/javascript' src='http://www.google.com/recaptcha/api/challenge?k=$pid'></script>";
		echo "<noscript><iframe src='http://www.google.com/recaptcha/api/noscript?k=$pid' height='300' width='500' frameborder='0'></iframe><br /><textarea name='recaptcha_challenge_field' rows='3' cols='40'></textarea><input type='hidden' name='recaptcha_response_field' value='manual_challenge' /></noscript><br />";
		echo "<input type='submit' name='submit' onclick='javascript:return checkc();' value='$sname' />\n";
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
 * added support for captcha by Th3-822 14-04-2012
 * freedl regexp fixed again by Th3-822 10-05-2012
 * incomplete fix for getting dllink by Th3-822 14-05-2012
 * added solvemedia captcha support && fixed dead link msgs by Th3-822 06-10-2012
 */
 
?>