<?php

if (!defined('RAPIDLEECH')) {
	require_once('index.html');
	exit;
}

class zippyshare_com extends DownloadClass {
	public function Download($link) {
		$cookie = array('ziplocale' => 'en');
		if (empty($_POST['step']) || $_POST['step'] != 1) {
			$page = $this->GetPage($link, $cookie);
			is_present('>File does not exist on this server<', 'File does not exist.');
			$cookie = GetCookiesArr($page, $cookie);

			if (!preg_match('@/d/\d+/(\d+)/[^\r\n\t\'\"<>\;]+@i', $page, $dlpath)) html_error('Download Link Not Found.');
			if (!preg_match('@Recaptcha\.create[\s\t]*\([\s\t]*\"([\w\-]+)\"@i', $page, $cpid)) html_error('reCAPTCHA Not Found.');
			//if (!preg_match('@\Wshortencode[\s\t]*:[\s\t]*\'?(\d+)\'?@i', $page, $short)) html_error('Captcha Data Not Found.');

			$data = $this->DefaultParamArr($link, $cookie);
			$data['step'] = '1';
			$data['dlpath'] = urlencode($dlpath[0]);
			$data['shortencode'] = urlencode($dlpath[1]);

			$this->Show_reCaptcha($cpid[1], $data);
		} else {
			if (empty($_POST['recaptcha_response_field'])) html_error('You didn\'t enter the image verification code.');
			$host = 'http://' . parse_url($link, PHP_URL_HOST);
			$cookie = urldecode($_POST['cookie']);

			$post = array();
			$post['challenge'] = $_POST['recaptcha_challenge_field'];
			$post['response'] = $_POST['recaptcha_response_field'];
			$post['shortencode'] = $_POST['shortencode'];

			$page = $this->GetPage($host . '/rest/captcha/test', $cookie, $post, $link . "\r\nX-Requested-With: XMLHttpRequest");
			$body = strtolower(trim(substr($page, strpos($page, "\r\n\r\n"))));

			if ($body == 'false') html_error('Error: Wrong CAPTCHA Entered.');
			elseif ($body != 'true') html_error('Unknown Reply from Server.');

			$dlink = $host . urldecode($_POST['dlpath']);
			$fname = urldecode(basename(parse_url($dlink, PHP_URL_PATH)));
			$this->RedirectDownload($dlink, $fname, $cookie);
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

// [24-11-2012]  Written by Th3-822. (Only for recaptcha download & only for rev43 :D)

?>