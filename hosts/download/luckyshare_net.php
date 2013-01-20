<?php
if (!defined('RAPIDLEECH')) {
	require_once('index.html');
	exit();
}

class luckyshare_net extends DownloadClass {

	public function Download($link) {
		global $premium_acc;

		if (!$_REQUEST['step']) {
			$this->page = $this->GetPage($link);
			is_present($this->page, 'There is no such file available.');
		}
		$this->link = $link;
		if ($_REQUEST['premium_acc'] == 'on' && (($_REQUEST['premium_user'] && $_REQUEST['premium_pass']) || ($premium_acc['luckyshare_net']['user'] && $premium_acc['luckyshare_net']['pass']))) {
			return $this->Premium();
		} else {
			return $this->Free();
		}
	}

	private function Free() {
		if ($_REQUEST['step'] == '1') {
			$this->link = urldecode($_POST['link']);
			$cookie = urldecode($_POST['cookie']);
			$challenge = $_POST['recaptcha_challenge_field'];
			$response = $_POST['recaptcha_response_field'];
			$hash = $_POST['hash'];
			$page = $this->GetPage("http://luckyshare.net/download/verify/challenge/$challenge/response/$response/hash/$hash", $cookie, 0, $this->link . "\r\nX-Requested-With: XMLHttpRequest");
			is_present($page, "Verification failed", "Wrong CAPTCHA!");
			$json = $this->Get_Reply($page);
			if (!array_key_exists('link', $json)) html_error('Error[Undetected Download Link - FREE!]');
			$dlink = $json['link'];
			$filename = basename(parse_url($dlink, PHP_URL_PATH));
			$this->RedirectDownload($dlink, $filename, $cookie);
			exit();
		} else {
			is_present($this->page, 'This file is Premium only. Only Premium Users can download this file.');
			$cookie = GetCookies($this->page);
			if (!preg_match('/getJSON\(\'([^\r\n\']+)\', function\(data\)/i', $this->page, $f)) html_error('Error[Undetected the free link regex!]');
			if (!preg_match('/Recaptcha\.create\("([^\r\n"]+)",/i', $this->page, $c)) html_error('Error[Undetected Captcha Challenge for free!]');
			$flink = 'http://luckyshare.net' . $f[1];
			$page = $this->GetPage($flink, $cookie, 0, $this->link);
			$json = $this->Get_Reply($page);
			if (empty($json['hash']) || !array_key_exists('hash', $json)) html_error('Error [Hash Data not found!]');
			$this->CountDown($json['time']);
			// I THINK WE SHOULD SEND DIRECTLY TO THE CAPTCHA...
			$data = $this->DefaultParamArr($this->link, $cookie);
			$data['step'] = '1';
			$data['hash'] = $json['hash'];
			$this->Show_reCaptcha($c[1], $data);
			exit();
		}
	}

	private function Premium() {
		$cookie = $this->login();
		$page = $this->GetPage($this->link, $cookie);
		if (!preg_match('/Location: (https?:\/\/[^\r\n]+)/i', $page, $dl)) html_error('Error[Download link - PREMIUM not found!]');
		$dlink = trim($dl[1]);
		$filename = basename(parse_url($dlink, PHP_URL_PATH));
		$this->RedirectDownload($dlink, $filename, $cookie);
	}

	private function login() {
		global $premium_acc;

		$user = ($_REQUEST["premium_user"] ? trim($_REQUEST["premium_user"]) : $premium_acc ["luckyshare_net"] ["user"]);
		$pass = ($_REQUEST["premium_pass"] ? trim($_REQUEST["premium_pass"]) : $premium_acc ["luckyshare_net"] ["pass"]);
		if (empty($user) || empty($pass)) html_error("Login failed, username[$user] or password[$pass] is empty!");

		$posturl = 'http://luckyshare.net/';
		$page = $this->GetPage($posturl . 'auth/login');
		$cookie = GetCookies($page);
		$post = array();
		$post['username'] = $user;
		$post['password'] = $pass;
		$post['remember'] = '';
		$post['token'] = cut_str($page, 'name="token" value="', '"');
		$page = $this->GetPage($posturl . 'auth/login', $cookie, $post, $posturl . 'auth/login');
		is_present($page, 'Invalid username or password.');
		$cookie = GetCookies($page);

		$page = $this->GetPage($posturl . 'account/', $cookie, 0, $posturl . 'auth/login');
		is_notpresent($page, '>Premium	<', 'Error[Account isn\'t Premium!]');

		//show daily usage
		if (!preg_match('/\d+ hours quota used:<\/strong><br \/><span>([\d|\.]+)(\wB)\/(\d+)(\wB)<\/span>/', $page, $quota)) html_error('Error[Can\'t find Premium Daily Usage!]');
		$this->changeMesg(lang(300) . "<br />Luckyshare.net Premium Download<br />You have used {$quota[1]} {$quota[2]} of {$quota[3]} {$quota[4]} Premium Daily Usage.");

		return $cookie;
	}

	public function CheckBack($header) {
		is_present($header, 'HTTP/1.1 500 Internal Server Error', 'Server have an internal error, please retry again in the few hour!');
	}

	private function Show_reCaptcha($pid, $inputs) {
		global $PHP_SELF;
		if (!is_array($inputs)) {
			html_error("Error parsing captcha data.");
		}
		// Themes: 'red', 'white', 'blackglass', 'clean'
		echo "<script language='JavaScript'>var RecaptchaOptions={theme:'white', lang:'en'};</script>\n";
		echo "\n<center><form name='dl' action='$PHP_SELF' method='post' ><br />\n";
		foreach ($inputs as $name => $input) {
			echo "<input type='hidden' name='$name' id='$name' value='$input' />\n";
		}
		echo "<script type='text/javascript' src='http://www.google.com/recaptcha/api/challenge?k=$pid'></script>";
		echo "<noscript><iframe src='http://www.google.com/recaptcha/api/noscript?k=$pid' height='300' width='500' frameborder='0'></iframe><br />";
		echo "<textarea name='recaptcha_challenge_field' rows='3' cols='40'></textarea><input type='hidden' name='recaptcha_response_field' value='manual_challenge' /></noscript><br />";
		echo "<input type='submit' name='submit' onclick='javascript:return checkc();' value='Enter Captcha' />\n";
		echo "<script type='text/javascript'>/*<![CDATA[*/\nfunction checkc(){\nvar capt=document.getElementById('recaptcha_response_field');\nif (capt.value == '') { window.alert('You didn\'t enter the image verification code.'); return false; }\nelse { return true; }\n}\n/*]]>*/</script>\n";
		echo "</form></center>\n</body>\n</html>";
		exit;
	}

	private function Get_Reply($page) {
		if (!function_exists('json_decode')) html_error("Error: Please enable JSON in php.");
		$json = substr($page, strpos($page, "\r\n\r\n") + 4);
		$json = substr($json, strpos($json, "{"));
		$json = substr($json, 0, strrpos($json, "}") + 1);
		$rply = json_decode($json, true);
		if (!$rply || (is_array($rply) && count($rply) == 0)) html_error("Error getting json data.");
		return $rply;
	}

}

/*
 * by Ruud v.Tony 16-04-2012
 * Updated to support premium by Tony Fauzi Wihana/Ruud v.Tony 19/01/2013
 */
?>
