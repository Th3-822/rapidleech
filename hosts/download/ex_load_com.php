<?php
if (!defined('RAPIDLEECH')) {
	require_once 'index.html';
	exit;
}

class ex_load_com extends DownloadClass {

	public function Download($link) {
		global $premium_acc;

		if (!$_REQUEST['step']) {
			$this->cookie['lang'] = 'english';
			$this->page = $this->GetPage($link, $this->cookie);
			is_present($this->page, 'The file you were looking for could not be found, sorry for any inconvenience.');
		}
		$this->link = $link;
		if ($_REQUEST['premium_acc'] == 'on' && (($_REQUEST['premium_user'] && $_REQUEST['premium_pass'])||($premium_acc['ex-load_com']['user'] && $premium_acc['ex-load_com']['pass']))) {
			return $this->Premium();
		} else {
			return $this->Free();
		}
	}

	private function Premium() {
		$cookie = $this->login();
		$page = $this->GetPage($this->link, $cookie);
		if (!preg_match('/https?:\/\/f\d+\.ex-load\.com(:\d+)?\/d\/[^\s\t\r\n\'"]+/', $page, $dl)) {
			$form = cut_str($page, '<Form name="F1" method="POST"', '</Form>');
			if (!preg_match_all('/name="([^"]+)" ((id|class)="[^"]+" )?value="([^"]+)?"/i', $form, $match)) html_error('Error[Post Data PREMIUM not found!]');
			$match = array_combine($match[1], $match[4]);
			$post = array();
			foreach ($match as $k => $v) {
				$post[$k] = $v;
			}
			$page = $this->GetPage($this->link, $cookie, $post, $this->link);
			if (!preg_match('/https?:\/\/f\d+\.ex-load\.com(:\d+)?\/d\/[^\s\t\r\n\'"]+/', $page, $dl)) html_error('Error[Download Link PREMIUM not found!]');
		}
		$dlink = trim($dl[0]);
		$filename = basename(parse_url($dlink, PHP_URL_PATH));
		$this->RedirectDownload($dlink, $filename, $cookie);
	}

	private function login() {
		global $premium_acc;

		$user = ($_REQUEST["premium_user"] ? trim($_REQUEST["premium_user"]) : $premium_acc ["ex-load_com"] ["user"]);
		$pass = ($_REQUEST["premium_pass"] ? trim($_REQUEST["premium_pass"]) : $premium_acc ["ex-load_com"] ["pass"]);
		if (empty($user) || empty($pass)) html_error("Login failed, username or password is empty!");

		$posturl = 'http://ex-load.com/';
		$post = array();
		$post['op'] = 'login';
		$post['redirect'] = '';
		$post['login'] = $user;
		$post['password'] = $pass;
		$post['x'] = rand(11, 41);
		$post['y'] = rand(11, 23);
		$page = $this->GetPage($posturl, $this->cookie, $post, $posturl);
		is_present($page, 'Incorrect Login or Password');
		$cookie = GetCookiesArr($page, $this->cookie);

		//check account
		$page = $this->GetPage($posturl.'?op=my_account', $cookie, 0, $posturl);
		is_notpresent($page, 'Premium account expire', 'Account isn\'t Premium?');

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
			$this->cookie = GetCookiesArr($this->page, $this->cookie);
			$form = cut_str($this->page, '<Form method="POST" action=\'\'>', '</Form>');
			if (!preg_match_all('/type="hidden"[\s\t\r\n]? name="([^"]+)"[\s\t\r\n]? value="([^"]+)"/', $form, $one) || !preg_match_all('/name="(\w+_free)" .+ value="([^"]+)"/', $form, $two)) html_error('Error[Post Data 1 - FREE not found!]');
			$match = array_merge(array_combine($one[1], $one[2]), array_combine($two[1], $two[2]));
			$post = array();
			foreach ($match as $key => $value) {
				$post[$key] = $value;
			}
			$page = $this->GetPage($this->link, $this->cookie, $post, $this->link);
		}
		is_present($page, 'This file is available for Premium Users only');
		if (stripos($page, 'Recaptcha')) {
			$form = cut_str($page, '<Form name="F1" method="POST"', '</Form>');
			if (stripos($form, 'Wrong captcha')) echo "<div align='center'><font color='red'><b>Entered CAPTCHA was incorrect, please try again!</b></font></div>";
			if (!preg_match('/(\d+)<\/span> seconds/', $form, $w)) html_error ('Error[Timer not found!]');
			$this->CountDown($w[1]);
			if (!preg_match('/\/api\/challenge\?k=([^"]+)"/', $form, $c)) html_error('Error[Captcha Data not found!]');
			if (!preg_match_all('/name="([^"]+)" ((id|class)="[^"]+" )?value="([^"]+)?"/i', $form, $match)) html_error('Error[Post Data 2 - FREE not found!]');
			$match = array_combine($match[1], $match[4]);
			$data = $this->DefaultParamArr($this->link, $this->cookie);
			$data['step'] = 1;
			foreach ($match as $k => $v) {
				$data["temp[{$k}]"] = $v;
			}
			$this->Show_reCaptcha($c[1], $data);
			exit;
		}
		is_present($page, cut_str($page, '<div class="err">', '<br>'));
		if (!preg_match('/https?:\/\/f\d+\.ex-load\.com(:\d+)?\/d\/[^\s\t\r\n"]+/', $page, $dl)) html_error('Error[Download Link - FREE not found!]');
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

}

/*
 * Written By Tony Fauzi Wihana / Ruud v.Tony 24/04/2013
 */
?>
