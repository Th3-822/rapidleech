<?php
if (!defined('RAPIDLEECH')) {
	require_once ('index.html');
	exit;
}

class filemates_com extends DownloadClass {

	public function Download($link) {
		global $premium_acc;

		if (!$_REQUEST['step']) {
			$this->cookie['lang'] = 'english';
			$this->page = $this->GetPage($link, $this->cookie);
			is_present($this->page, 'The file you were looking for could not be found, sorry for any inconvenience.');
			is_present($this->page, 'This server is in maintenance mode. Refresh this page in some minutes.');
		}
		$this->link = $link;
		if ($_REQUEST['premium_acc'] == 'on' && (($_REQUEST['premium_user'] && $_REQUEST['pass']) || ($premium_acc['filemates_com']['user'] && $premium_acc['filemates_com']['pass']))) {
			return $this->Premium();
		} elseif ($_REQUEST['step'] == 'passpre') {
			return $this->Premium(true);
		} else {
			return $this->Free();
		}
	}

	private function Free() {
		if ($_REQUEST['step'] == 'passfree') {
			$this->link = urldecode($_POST['link']);
			$this->cookie = StrToCookies(urldecode($_POST['cookie']));
			$post = array();
			foreach ($_POST['tmp'] as $k => $v) {
				$post[$k] = $v;
			}
			$post['code'] = $_POST['code'];
			$post['password'] = $_POST['password'];
			$page = $this->GetPage($this->link, $this->cookie, $post, $this->link);
		} else {
			$form = cut_str($this->page, '<Form method="POST" action=\'\'>', '</Form>');
			if (!preg_match_all('/<input type="hidden" name="([^"]+)" value="([^"]+)?">/', $form, $one) || !preg_match_all('/<input type="submit" class="button_sbmt" name="(\w+_free)" value="([^"]+)">/', $form, $two)) html_error('Error[Post Data 1 - FREE not found!]');
			$match = array_merge(array_combine($one[1], $one[2]), array_combine($two[1], $two[2]));
			$post = array();
			foreach ($match as $key => $value) {
				$post[$key] = $value;
			}
			$page = $this->GetPage($this->link, $this->cookie, $post, $this->link);
			is_present($page, cut_str($page, '<div class="err" style="font-size:16px; text-align:center;">', '<br>'));
			unset($post);
			$form = cut_str($page, '<Form name="F1" method="POST"', '</Form>');
			if (!preg_match('/(\d+)<\/span> seconds/', $form, $w)) html_error('Error[Timer not found!]');
			$this->CountDown($w[1]);
			if (!preg_match_all('/<input type="hidden" name="([^"]+)" value="([^"]+)?">/', $form, $match)) html_error('Error[Post Data - PREMIUM not found!]');
			$match = array_combine($match[1], $match[2]);
			if (!preg_match_all("@<span style='[^\'|>]*padding-left\s*:\s*(\d+)[^\'|>]*'[^>]*>((?:&#\w+;)|(?:\d))</span>@i", $form, $spans)) html_error('Error: Cannot decode captcha.');
			$spans = array_combine($spans[1], $spans[2]);
			ksort($spans);
			$captcha = '';
			foreach ($spans as $digit) $captcha .= $digit;
			if (stripos($form, '<input type="password" name="password" class="myForm">')) {
				$data = $this->DefaultParamArr($this->link, $this->cookie);
				foreach ($match as $k => $v) {
					$data["tmp[$k]"] = $v;
				}
				$data['step'] = 'passfree';
				$data['code'] = html_entity_decode($captcha);
				$this->EnterPassword($data);
				exit;
			} else {
				$post = array();
				foreach ($match as $key => $value) {
					$post[$key] = $value;
				}
				$post['code'] = html_entity_decode($captcha);
				$page = $this->GetPage($this->link, $cookie, $post, $this->link);
			}
		}
		is_present($page, cut_str($page, '<div class="err" style="font-size:16px; text-align:center;">', '</div>'));
		if (!preg_match("/downLinkDo\('(https?:\/\/[^\r\n']+)','([^\r\n']+)'\)\">Click here to start/", $page, $dl)) html_error('Error[Download Link - FREE not found!]');
		$dlink = trim($dl[1]);
		$filename = trim($dl[2]);
		$this->RedirectDownload($dlink, $filename, $this->cookie, 0, $this->link);
		exit;
	}

	private function login() {
		global $premium_acc;

		$user = ($_REQUEST["premium_user"] ? trim($_REQUEST["premium_user"]) : $premium_acc["filemates_com"]["user"]);
		$pass = ($_REQUEST["premium_pass"] ? trim($_REQUEST["premium_pass"]) : $premium_acc["filemates_com"]["pass"]);
		if (empty($user) || empty($pass)) html_error("Login Failed: User [$user] or Password [$pass] is empty. Please check login data.");

		$posturl = 'http://filemates.com/';
		$post['op'] = 'login';
		$post['redirect'] = urlencode($posturl);
		$post['login'] = urlencode($user);
		$post['password'] = urlencode($pass);
		$page = $this->GetPage($posturl, $this->cookie, $post, $posturl . 'login.html');
		is_present($page, 'Incorrect Login or Password');
		is_present($page, 'Your account was banned by administrator.');
		$cookie = GetCookiesArr($page, $this->cookie);

		$page = $this->GetPage($posturl . '?op=my_account', $cookie, 0, $posturl);
		is_notpresent($page, 'Extend</a>', 'Account isn\'t Premium!');

		return $cookie;
	}

	private function Premium($passfile=false) {
		if ($passfile == true) {
			$this->link = urldecode($_POST['link']);
			$cookie = StrToCookies(decrypt(urldecode($_POST['cookie'])));
			$post = array();
			foreach ($_POST['tmp'] as $k => $v) {
				$post[$k] = $v;
			}
			$post['password'] = $_POST['password'];
			$page = $this->GetPage($this->link, $cookie, $post, $this->link);
		} else {
			$cookie = $this->login();
			$page = $this->GetPage($this->link, $cookie);
		}
		if (!preg_match('/https?:\/\/files\-dl[0-9]\.com(:\d+)?\/\w+\/[0-9]\/[^\r\n\'"]+/', $page, $dl)) {
			$form = cut_str($page, '<Form name="F1" method="POST"', '</Form>');
			if (!preg_match_all('/<input type="hidden" name="([^"]+)" value="([^"]+)?">/', $form, $match)) html_error('Error[Post Data - PREMIUM not found!]');
			$match = array_combine($match[1], $match[2]);
			if (stripos($form, '<input type="password" name="password" class="myForm">')) {
				$data = $this->DefaultParamArr($this->link, encrypt(CookiesToStr($cookie)));
				foreach ($match as $k => $v) {
					$data["tmp[$k]"] = $v;
				}
				$data['step'] = 'passpre';
				$this->EnterPassword($data);
				exit;
			} else {
				$post = array();
				foreach ($match as $key => $value) {
					$post[$key] = $value;
				}
				$page = $this->GetPage($this->link, $cookie, $post, $this->link);
				if (!preg_match('/https?:\/\/files\-dl[0-9]\.com(:\d+)?\/\w+\/[0-9]\/[^\'"]+/', $page, $dl)) html_error('Error[Download Link - PREMIUM not found!]');
			}
		}
		$dlink = trim($dl[0]);
		$filename = basename(parse_url($dlink, PHP_URL_PATH));
		$this->RedirectDownload($dlink, $filename, $cookie, 0, $this->link);
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

}

/*
 * Written by Ruud v.Tony/Tony Fauzi Wihana 22-11-2012
 * Fixed by Tony Fauzi Wihana/Ruud v.Tony 16-01-2013
 */
?>
