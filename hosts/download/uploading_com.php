<?php
if (!defined('RAPIDLEECH')) {
	require_once ("index.html");
	exit();
}
class uploading_com extends DownloadClass {
	public function Download($link) {
		global $premium_acc;
		if (empty($_REQUEST['step'])) {
			$this->page = $this->GetPage($link);
			is_present($this->page, 'OOPS! Looks like file not found.', 'The requested file is not found');
			$this->cookie = GetCookiesArr($this->page);
			if (empty($this->cookie['SID'])) html_error('Error: Cookie [SID] not found!');
		}
		$this->link = $link;
		if ((isset($_REQUEST['cookieuse']) && $_REQUEST["cookieuse"] == 'on' && preg_match('/remembered_user=([\w.]+);?/i', $_REQUEST['cookie']) !== false) || ($_REQUEST['premium_acc'] == 'on' && !empty($premium_acc['uploading_com']['cookie']))) {
			$this->changeMesg(lang(300) . '<br />Uploading.com Premium Download [Cookie]');
			return $this->Login();
		} elseif (($_REQUEST['premium_acc'] == 'on' && (($_REQUEST['premium_user'] && $_REQUEST['premium_pass']) || (!empty($premium_acc['uploading_com']['user']) && !empty($premium_acc['uploading_com']['pass']))))) {
			$this->changeMesg(lang(300) . '<br />Uploading.com Premium Download');
			return $this->Login();
		} elseif (isset($_REQUEST['step']) && $_REQUEST['step'] == 'captcha') {
			return $this->Login(true);
		} elseif (isset($_REQUEST['step']) && $_REQUEST['step'] == 'passpre') {
			return $this->Premium(true);
		} else {
			$this->changeMesg(lang(300) . '<br />Uploading.com Free Download');
			return $this->Free();
		}
	}

	private function Free() {
		$post_url = 'http://uploading.com/files/get/?ajax';
		if (isset($_REQUEST['step']) && $_REQUEST['step'] == 'passfree') {
			$code = urlencode($_POST['code']);
			$pass = urlencode($_POST['password']);

			$post = array();
			$post['action'] = 'check_pass';
			$post['code'] = $code;
			$post['pass'] = $pass;
			$this->cookie = StrToCookies(urldecode($_POST['cookie']));
			$page = $this->GetPage($post_url, $this->cookie, $post, $this->link."\r\nX-Requested-With: XMLHttpRequest");
			$json = $this->Get_Reply($page);

			if (!empty($json['error'])) html_error('Password Error: '.$json['error']);
			if (empty($json['answer']['success'])) html_error('Unknown response while checking password.');
		} else {
			is_present($this->page, "The file owner set up a limitation<br />that only premium members are<br />able download this file.");
			is_present($this->page, "Sorry, you have reached your daily download limit.<br />Please try again tomorrow or acquire a premium membership."); // T8: I need to check this.
			if (!preg_match('@code: "([^"]+)",@', $this->page, $cd)) html_error("Error: Link id not found");
			$code = $cd[1];

			if (stripos($this->page, 'data-pass="true"') !== false) {
				$data = $this->DefaultParamArr($this->link, $this->cookie);
				$data['code'] = $code;
				$data['step'] = 'passfree';
				$this->EnterPassword($data);
				exit();
			} else $pass = 'false';
		}

		$post = array();
		$post['action'] = 'second_page';
		$post['code'] = $code;
		//$post['pass'] = $pass;
		$page = $this->GetPage($post_url, $this->cookie, $post, $this->link."\r\nX-Requested-With: XMLHttpRequest");
		$json = $this->Get_Reply($page);

		if (!empty($json['error'])) html_error('Error while getting countdown: '.$json['error']);
		if (!isset($json['answer']['wait_time'])) html_error('Countdown not found.');
		if ($json['answer']['wait_time'] > 0) $this->CountDown($json['answer']['wait_time']);

		$post = array();
		$post['action'] = 'get_link';
		$post['code'] = $code;
		$post['pass'] = $pass;
		$page = $this->GetPage($post_url, $this->cookie, $post, $this->link."\r\nX-Requested-With: XMLHttpRequest");
		$json = $this->Get_Reply($page);

		if (!empty($json['error'])) html_error('Error while getting free download link: '.$json['error']);
		if (empty($json['answer']['link'])) html_error('Free dl link not found.');

		if (!preg_match('@https?://([^/\r\n\"\'<>\s\t]+\.)?uploading\.com/get_file/[^\r\n\"\'<>\s\t]+@i', $json['answer']['link'], $dl)) {
			$page = $this->GetPage($json['answer']['link'], $this->cookie);
			if (!preg_match('@https?://([^/\r\n\"\'<>\s\t]+\.)?uploading\.com/get_file/[^\r\n\"\'<>\s\t]+@i', $page, $dl)) html_error('Download link not found.');
		}

		//$filename = basename(parse_url($json['answer']['link'], PHP_URL_PATH));
		$this->RedirectDownload($dl[0], 'UP_free', $this->cookie);
	}

	private function Get_Reply($page) {
		if (!function_exists('json_decode')) html_error("Error: Please enable JSON in php.");
		$json = substr($page, strpos($page,"\r\n\r\n") + 4);
		$json = substr($json, strpos($json, "{"));$json = substr($json, 0, strrpos($json, "}") + 1);
		$rply = json_decode($json, true);
		if (!$rply || (is_array($rply) && count($rply) == 0)) html_error("Error getting json data.");
		return $rply;
	}

	private function Login($captcha = false) {
		global $premium_acc;
		if ((isset($_REQUEST["cookieuse"]) && $_REQUEST["cookieuse"] == "on" && preg_match("/remembered_user\s*=\s*([\w|\%]+)\s*;?/i", $_REQUEST["cookie"], $c)) || ($_REQUEST["premium_acc"] == "on" && !empty($premium_acc["uploading_com"]["cookie"]))) {
			$usecookie = (empty($c[1]) ? !empty($premium_acc["uploading_com"]["cookie"]) : $c[1]);
		} else $usecookie = false;
		$posturl = 'http://uploading.com/';
		if (!$usecookie) {
			$pA = !empty($_REQUEST["premium_user"]) && !empty($_REQUEST["premium_pass"]) ? true : false;
			$email = ($pA ? trim($_REQUEST["premium_user"]) : $premium_acc ["uploading_com"] ["user"]);
			$password = ($pA ? trim($_REQUEST["premium_pass"]) : $premium_acc ["uploading_com"] ["pass"]);
			$post = array();
			if ($captcha == true) {
				if (empty($_POST['recaptcha_response_field'])) html_error("You didn't enter the image verification code.");
				$post['recaptcha_challenge_field'] = $_POST['recaptcha_challenge_field'];
				$post['recaptcha_response_field'] = $_POST['recaptcha_response_field'];
				$this->cookie = StrToCookies(decrypt(urldecode($_POST['cookie'])));
				if (!empty($_POST['cuser']) && !empty($_POST['cpass'])) {
					$email = decrypt(urldecode($_POST['cuser']));
					$password = decrypt(urldecode($_POST['cpass']));
				}
			}
			// This check is important incase there's conflict in post account data, do look in the bracket at error message...
			if (empty($email) || empty($password)) html_error('Login failed, email or password is empty!');
			$post['email'] = urlencode($email);
			$post['password'] = urlencode($password);
			$post['remember'] = 'on';
			$page = $this->GetPage($posturl.'general/login_form/?ajax', $this->cookie, $post, $posturl."\r\nX-Requested-With: XMLHttpRequest");
			$json = $this->Get_Reply($page);
			if (!empty($json['error'])) html_error('Login Error: '.$json['error']);
			if (!empty($json['answer']['captcha'])) {
				if (!preg_match('@\(\'recaptcha_block\', \'([^\']+)\'\);@', $this->page, $c)) html_error('Error: Login captcha data not found.');
				$data = $this->DefaultParamArr($this->link, encrypt(CookiesToStr($this->cookie)));
				$data['step'] = 'captcha';
				if ($pA) {
					$data['cuser'] = urlencode(encrypt($email));
					$data['cpass'] = urlencode(encrypt($password));
				}
				$this->Show_reCaptcha($c[1], $data);
				exit();
			}
			if (!empty($json['redirect'])) {
				$this->cookie = GetCookiesArr($page, $this->cookie);
			} else html_error("Error [Login Page Response UNKNOWN!]");
		} else {
			$this->cookie['remembered_user'] = $usecookie;
			$this->cookie['u'] = 1;
			$this->cookie['autologin'] = 1;
		}

		$page = $this->GetPage($posturl, $this->cookie, 0, $posturl);
		is_present($page, 'class="i_premium"', 'Error: Account isn\'t premium?');
		$this->cookie = GetCookiesArr($page, $this->cookie);

		return $this->Premium();
	}

	private function Premium($password = false) {
		$post_url = "http://uploading.com/files/get/?ajax";
		if ($password == true) {
			$post = array(); 
			$post['action'] = $_POST['action'];
			$post['code'] = $_POST['code'];
			$post['pass'] = $_POST['password'];
			$this->cookie = decrypt(urldecode($_POST['cookie']));
			$page = $this->GetPage($post_url, $this->cookie, $post, $this->link."\r\nX-Requested-With: XMLHttpRequest");
		} else {
			$this->page = $this->GetPage($this->link, $this->cookie, 0, $this->link);
			is_present($this->page, 'Your account premium traffic has been limited');
			if (preg_match('@https?://([^/\r\n\"\'<>\s\t]+\.)?uploading\.com/get_file/[^\r\n\"\'<>\s\t]+@i', $this->page, $dl)) { //this for direct link file
				$this->RedirectDownload($dl[0], 'UP_premium', $this->cookie, 0, $this->link);
				return; // T8: return is better :D
			} else {
				if (!preg_match('@code: "([^"]+)",@', $this->page, $cd)) html_error("Error: Link id not found.");

				if (stripos($this->page, 'data-pass="true"') !== false) {
					$data = $this->DefaultParamArr($this->link, encrypt(CookiesToStr($this->cookie)));
					$data['action'] = 'get_link';
					$data['code'] = $cd[1];
					$data['step'] = 'passpre';
					$this->EnterPassword($data);
					exit();
				} else { // no password
					$post['action'] = 'get_link';
					$post['code'] = $cd[1];
					$post['pass'] = 'false';
					$page = $this->GetPage($post_url, $this->cookie, $post, $this->link."\r\nX-Requested-With: XMLHttpRequest");
				}
			}
		}
		$json = $this->Get_Reply($page);
		if (!empty($json['error'])) html_error('Error at download: '.$json['error']);

		if (!preg_match('@https?://([^/\r\n\"\'<>\s\t]+\.)?uploading\.com/get_file/[^\r\n\"\'<>\s\t]+@i', $json['answer']['link'], $dl)) {
			$page = $this->GetPage($json['answer']['link'], $this->cookie);
			if (!preg_match('@https?://([^/\r\n\"\'<>\s\t]+\.)?uploading\.com/get_file/[^\r\n\"\'<>\s\t]+@i', $page, $dl)) html_error('Download-link not found.');
		}

		//$filename = basename(parse_url($json['answer']['link'], PHP_URL_PATH));
		$this->RedirectDownload($dl[0], 'UP_premium', $this->cookie);
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

	private function EnterPassword($inputs) {
		global $PHP_SELF;
		if (!is_array($inputs)) {
			html_error("Error parsing password data.");
		}
		echo "\n" . '<center><form action="' . $PHP_SELF . '" method="post" >' . "\n";
		foreach ($inputs as $name => $input) {
			echo "<input type='hidden' name='$name' id='$name' value='$input' />\n";
		}
		echo '<h4>Enter password here: <input type="text" name="password" id="filepass" size="13" />&nbsp;&nbsp;<input type="submit" onclick="return check()" value="Submit" /></h4>' . "\n";
		echo "<script type='text/javascript'>\nfunction check() {\nvar pass=document.getElementById('filepass');\nif (pass.value == '') { window.alert('You didn\'t enter the password'); return false; }\nelse { return true; }\n}\n</script>\n";
		echo "\n</form></center>\n</body>\n</html>";
		exit();
	}

	public function CheckBack($header) {
		is_present($header, 'HTTP/1.1 302 Moved', urldecode(cut_str($header, "Set-Cookie: error=", ";")));
	}
}

/*
 * Written by Ruud v.Tony 10-02-2012
 * Premium Dl and Login fixed by Th3-822 14-03-2012
 * Premium Dl after new template fixed by IndoLeech.Com 21-05-2012
 * Free DL, Login and Premium DL fixed by Th3-822 05-07-2012
 * Free DL and Premium DL links/filenames fixed by Th3-822 01-09-2012
 */

?>