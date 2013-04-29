<?php
if (!defined('RAPIDLEECH')) {
	require_once 'index.html';
	exit;
}

class datafile_com extends DownloadClass {

	public function Download($link) {
		global $premium_acc;

		if (!$_REQUEST['step']) {
			$this->page = $this->GetPage($link);
			is_present($this->page, 'File not found');
			$this->cookie = GetCookiesArr($this->page);
		}
		$this->link = $link;
		$this->posturl = 'https://www.datafile.com';
		if ($_REQUEST['premium_acc'] == 'on' && (($_REQUEST['premium_user'] && $_REQUEST['premium_pass']) || ($premium_acc['datafile_com']['user'] && $premium_acc['datafile_com']['pass']))) {
			return $this->Premium();
		} elseif ($_REQUEST['step'] == 1) {
			return $this->DownloadFree();
		} else {
			return $this->PrepareFree();
		}
	}

	private function Premium() {
		$filename = cut_str($this->page, '<div class="file-name">', '</div>');
		$cookie = $this->login();
		$page = $this->GetPage($this->link, $cookie);
		if (!preg_match('/Location: (\/[^\s\t\r\n]+)/i', $page, $rd)) html_error('Error[Redirect Link - PREMIUM not found!]');
		$page = $this->GetPage($this->posturl . $rd[1], $cookie, 0, $this->link);
		if (!preg_match('/Location: (https?:\/\/n\d+\.datafile\.com\/[^\s\t\r\n]+)/i', $page, $dl)) html_error('Error[Download Link - PREMIUM not found!]');
		$dlink = trim($dl[1]);
		$this->RedirectDownload($dlink, $filename, $cookie, 0, $this->link, $filename);
	}

	private function login() {
		global $premium_acc;

		$user = ($_REQUEST["premium_user"] ? trim($_REQUEST["premium_user"]) : $premium_acc ["datafile_com"] ["user"]);
		$pass = ($_REQUEST["premium_pass"] ? trim($_REQUEST["premium_pass"]) : $premium_acc ["datafile_com"] ["pass"]);
		if (empty($user) || empty($pass)) html_error("Login failed, username or password is empty!");

		$post = array();
		$post['login'] = $user;
		$post['password'] = $pass;
		$post['remember_me'] = 0;
		$post['remember_me'] = 1;
		$post['btn'] = '';
		$page = $this->GetPage($this->posturl . '/login.html', $this->cookie, $post, $this->posturl . '/login.html');
		$cookie = GetCookiesArr($page, $this->cookie);
		is_present($page, 'Incorrect login or password!');

		//check account
		$page = $this->GetPage($this->posturl . '/profile.html', $cookie, 0, $this->posturl . '/index.html');
		is_notpresent($page, 'Premium Expires', 'Account isn\'t Premium?');

		return $cookie;
	}

	private function DownloadFree() {
		$post = array();
		foreach ($_POST['temp'] as $k => $v) {
			$post[$k] = $v;
		}
		$post['recaptcha_challenge_field'] = $_POST['recaptcha_challenge_field'];
		$post['recaptcha_response_field'] = $_POST['recaptcha_response_field'];
		$recap = $_POST['recap'];
		$filename = $_POST['filename'];
		$this->cookie = urldecode($_POST['cookie']);
		$page = $this->GetPage($this->posturl . '/files/ajax.html', $this->cookie, $post, $this->link, 0, 1);
		$json = $this->Get_Reply($page);
		if ($json['success'] == 0) {
			echo "<div align='center'><font color='red'><b>{$json['msg']}</b></font></div>";
			$data = $this->DefaultParamArr($this->link, $this->cookie);
			foreach ($_POST['temp'] as $k => $v) {
				$data["temp[{$k}]"] = $v;
			}
			$data['step'] = 1;
			$data['recap'] = $recap;
			$data['filename'] = $filename;
			$this->Show_reCaptcha($recap, $data);
			exit();
		}
		if (empty($json['link'])) html_error('Error[Download Link - FREE not found!]');
		$this->RedirectDownload($json['link'], $filename, $this->cookie, 0, $this->link, $filename);
	}

	private function PrepareFree() {
		if ($_REQUEST['step'] == 'countdown') {
			$this->cookie = urldecode($_POST['cookie']);
			$this->page = $this->GetPage($this->link, $this->cookie);
		} else {
			if (!preg_match("/counter\.contdownTimer\('(\d+)'/", $this->page, $w)) html_error('Error[Timer not found!]');
			if ($w[1] < 120) $this->CountDown($w[1]);
			else {
				$data = $this->DefaultParamArr($this->link, $this->cookie);
				$data['step'] = 'countdown';
				$this->JSCountdown($w[1], $data);
			}
		}
		if (stripos($this->page, 'Recaptcha')) {
			$data = $this->DefaultParamArr($this->link, $this->cookie);
			$data['temp[doaction]'] = 'getFileDownloadLink';
			$data['temp[fileid]'] = cut_str($this->page, "getFileDownloadLink('", "'");

			if (!preg_match('/\/challenge\?k=([^"]+)"/', $this->page, $c) || !preg_match('/\/noscript\?k=([^"]+)"/', $this->page, $c)) html_error('Error[Captcha Data not found!]');
			$data['step'] = 1;
			$data['filename'] = cut_str($this->page, '<div class="file-name">', '</div>');
			$data['recap'] = $c[1];
			$this->Show_reCaptcha($c[1], $data);
			exit();
		}
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
			echo "<input type='hidden' name='$name' value='$input' />\n";
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
 * Written by Tony Fauzi Wihana/Ruud v.Tony 24/04/2013
 */
?>
