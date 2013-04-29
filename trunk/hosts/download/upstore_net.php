<?php
if (!defined('RAPIDLEECH')) {
	require_once ('index.html');
	exit;
}

class upstore_net extends DownloadClass {

	public function Download($link) {
		global $premium_acc;

		$link = str_replace('/upsto.re/', '/upstore.net/', $link);
		if (!$_REQUEST['step']) {
			$this->cookie['lang'] = 'en';
			$this->page = $this->GetPage($link, $this->cookie);
			is_present($this->page, 'File not found');
			$this->cookie = GetCookiesArr($this->page, $this->cookie);
		}
		$this->link = $link;
		if ($_REQUEST['premium_acc'] == 'on' && (($_REQUEST['premium_user'] && $_REQUEST['premium_pass']) || ($premium_acc['upstore_net']['user'] && $premium_acc['upstore_net']['pass']))) {
			return $this->Premium();
		} else {
			return $this->Free();
		}
	}

	private function Premium() {
		$cookie = $this->login();
		$page = $this->GetPage($this->link, $cookie);
		if (preg_match('/https?:\/\/d\d+\.upsto\.re\/[a-zA-Z0-9]\/[^|\s|\t|\r|\n"]+/i', $page, $dl)) {
			$dlink = trim($dl[0]);
		} else {
			$postlink = 'http://upstore.net'.cut_str($page, '<form action="', '"');
			$post = array();
			$post['hash'] = cut_str($page, 'name="hash" value="', '"');
			$post['antispam'] = urlencode('BU?8I1H64w2zWz(;nkMM');
			$post['js'] = 1;
			$page = $this->GetPage($postlink, $cookie, $post, $this->link, 0, 1);
			$json = $this->Get_Reply($page);
			if (!empty($json['errors'])) html_error($json['errors']);
			if (empty($json['ok'])) html_error('Error[Download Link - PREMIUM not found!]');
			$dlink = trim($json['ok']);
		}
		$filename = basename(parse_url($dlink, PHP_URL_PATH));
		$this->RedirectDownload($dlink, $filename, $cookie);
	}

	private function login() {
		global $premium_acc;

		$user = ($_REQUEST["premium_user"] ? trim($_REQUEST["premium_user"]) : $premium_acc ["upstore_net"] ["user"]);
		$password = ($_REQUEST["premium_pass"] ? trim($_REQUEST["premium_pass"]) : $premium_acc ["upstore_net"] ["pass"]);
		if (empty($user) || empty($password)) html_error("Login failed, username or password is empty!");

		$posturl = 'http://upstore.net/';
		$post = array();
		$post['url'] = urlencode($posturl);
		$post['email'] = $user;
		$post['password'] = $password;
		$post['send'] = 'Login';
		$page = $this->GetPage($posturl . 'account/login/', $this->cookie, $post, $posturl);
		is_present($page, 'Wrong email or password.');
		$cookie = GetCookiesArr($page, $this->cookie);

		//account check
		$page = $this->GetPage($posturl . 'account', $cookie, 0, $posturl);
		is_notpresent($page, 'premium till', 'Account isn\'t premium?');

		return $cookie;
	}

	private function Free() {
		if ($_REQUEST['step'] == 1) {
			$this->cookie = urldecode($_POST['cookie']);
			$post = array();
			foreach ($_POST['temp'] as $k => $v) {
				$post[$k] = $v;
			}
			$post['recaptcha_challenge_field'] = $_POST['recaptcha_challenge_field'];
			$post['recaptcha_response_field'] = $_POST['recaptcha_response_field'];
			$page = $this->GetPage($this->link, $this->cookie, $post, $this->link);
		} else {
			$form = cut_str($this->page, '<form action="" method="post">', '</form>');
			if (!preg_match_all('/name="([^"]+)" value="([^"]+)"/i', $form, $match)) html_error('Error[Post Data 1 - FREE not found!]');
			$match = array_combine($match[1], $match[2]);
			$post = array();
			foreach ($match as $key => $value) {
				$post[$key] = $value;
			}
			$page = $this->GetPage($this->link, $this->cookie, $post, $this->link);
		}
		is_present($page, cut_str($page, '<span class="error">', '</span>'));
		if (stripos($page, 'Please wait %s before downloading')) {
			if (!preg_match('/var sec = (\d+)/', $page, $w)) html_error('Error[Timer not found!]');
			$this->CountDown($w[1]);
			if (!preg_match("/Recaptcha\.create\('([^\s\t\r\n']+)',/", $page, $c)) html_error('Error[Captcha Data not found!]');

			$data = $this->DefaultParamArr($this->link, $this->cookie);
			$data['step'] = 1;
			$data['temp[antispam]'] = urlencode('BU?8I1H64w2zWz(;nkMM');
			$data['temp[hash]'] = cut_str($page, 'name="hash" value="', '"');
			$data['temp[free]'] = cut_str($page, 'class="submit" value="', '"');
			$this->Show_reCaptcha($c[1], $data);
			exit();
		}
		if (!preg_match('/https?:\/\/d\d+\.upsto\.re\/[a-zA-Z0-9]\/[^|\s|\t|\r|\n"]+/i', $page, $dl)) html_error('Error[Download Link - FREE not found!]');
		$dlink = trim($dl[0]);
		$filename = basename(parse_url($dlink, PHP_URL_PATH));
		$this->RedirectDownload($dlink, $filename, $this->cookie, 0, $this->link);
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
			echo "<input type='hidden' name='$name' value='$input' />\n"; //id from input can't contain array, so I remove the id :D
		}
		echo "<script type='text/javascript' src='http://www.google.com/recaptcha/api/challenge?k=$pid'></script>";
		echo "<noscript><iframe src='http://www.google.com/recaptcha/api/noscript?k=$pid' height='300' width='500' frameborder='0'></iframe><br />";
		echo "<textarea name='recaptcha_challenge_field' rows='3' cols='40'></textarea><input type='hidden' name='recaptcha_response_field' value='manual_challenge' /></noscript><br />";
		echo "<input type='submit' name='submit' onclick='javascript:return checkc();' value='Download File' />\n";
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
 * Written by Tony Fauzi Wihana / Ruud v.Tony  23/04/2013
 */
?>
