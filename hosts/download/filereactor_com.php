<?php

if (!defined('RAPIDLEECH')) {
	require_once ("index.html");
	exit();
}

class filereactor_com extends DownloadClass {
	private $page, $cookie;
	public function Download($link) {
		global $premium_acc;
		$this->cookie = array('lang' => 'english');

		if (stripos($link, '://filereactor.com') !== false) $link = str_replace('://', '://www.', $link);
		$this->page = $this->GetPage($link, $this->cookie);
		is_present($this->page, "The file you were looking for could not be found");
		is_present($this->page, "No such file with this filename", 'Error: Invalid filename, check your link and try again.');
		is_present($this->page, "The file was removed by administrator");

		if ($_REQUEST["premium_acc"] == "on" && ((!empty($_REQUEST["premium_user"]) && !empty($_REQUEST["premium_pass"])) || (!empty($premium_acc["filereactor_com"]["user"]) && !empty($premium_acc["filereactor_com"]["pass"])))) {
			$this->Login($link);
		} else {
			$this->FreeDL($link);
		}
	}

	private function FreeDL($link) {
		if (empty($_POST['step']) || $_POST['step'] != '1') {
			$page2 = cut_str($this->page, 'Form method="POST" action=\'', '</form>'); //Cutting page
			$post = array();
			$post['op'] = cut_str($page2, 'name="op" value="', '"');
			is_notpresent($post['op'], 'download', 'Error parsing download post data.');
			$post['usr_login'] = (empty($this->cookie['xfss'])) ? '' : $this->cookie['xfss'];
			$post['id'] = cut_str($page2, 'name="id" value="', '"');
			$post['fname'] = cut_str($page2, 'name="fname" value="', '"');
			$post['referer'] = '';
			$post['method_free'] = cut_str($page2, 'name="method_free" value="', '"');

			$page = $this->GetPage($link, $this->cookie, $post);
			if (preg_match('@You can download files up to \d+ [K|M|G]b only.@i', $page, $err)) html_error("Error: ".$err[0]);
			if (preg_match('@You (?:(?:have to)|(?:must)) wait (?:\d+ \w+,\s)?\d+ \w+ (?:un)?till? (?:your )?next download@', $page, $err)) html_error("Error: ".$err[0]);

			$page2 = cut_str($page, '<form name="F1" method="POST"', '</form>'); //Cutting page

			if (!preg_match('@<span id="countdown_str">[^<|>]+<span[^>]*>(\d+)</span>[^<|>]+</span>@i', $page2, $count)) $count = array(1=>30);
			if ($count[1] > 0) $this->CountDown($count[1]);

			if (!preg_match('@https?://(?:[^/]+\.)?(?:(?:google\.com/recaptcha/api)|(?:recaptcha\.net))/(?:(?:challenge)|(?:noscript))\?k=([\w|\-]+)@i', $page, $pid)) html_error('Error: reCAPTCHA not found.');

			$data = $this->DefaultParamArr($link, (empty($this->cookie['xfss'])) ? 0 : encrypt(CookiesToStr($this->cookie)));
			$post = array();
			$data['T8[op]'] = cut_str($page2, 'name="op" value="', '"');
			is_notpresent($data['T8[op]'], 'download', 'Error parsing download post data (2).');
			$data['T8[id]'] = cut_str($page2, 'name="id" value="', '"');
			$data['T8[rand]'] = cut_str($page2, 'name="rand" value="', '"');
			$data['T8[method_free]'] = cut_str($page2, 'name="method_free" value="', '"');
			$data['step'] = 1;
			$this->Show_reCaptcha($pid[1], $data);
		} else {
			if (empty($_POST['recaptcha_response_field'])) html_error("You didn't enter the image verification code.");

			$this->cookie = (!empty($_POST['cookie'])) ? StrToCookies(decrypt(urldecode($_POST['cookie']))) : array();
			$this->cookie['lang'] = 'english';

			$post = array('recaptcha_challenge_field' => $_POST['recaptcha_challenge_field'], 'recaptcha_response_field' => $_POST['recaptcha_response_field']);
			$post['op'] = $_POST['T8']['op'];
			$post['id'] = $_POST['T8']['id'];
			$post['rand'] = $_POST['T8']['rand'];
			$post['referer'] = '';
			$post['method_free'] = $_POST['T8']['method_free'];
			$post['down_script'] = 1;

			$page = $this->GetPage($link, $this->cookie, $post);
			is_present($page, "Wrong captcha", "Error: Wrong captcha.");
			is_present($page, "Skipped countdown", "Error: Skipped countdown?.");
			if (preg_match('@You (?:(?:have to)|(?:must)) wait (?:\d+ \w+,\s)?\d+ \w+ (?:un)?till? (?:your )?next download@', $page, $err)) html_error("Error: ".$err[0]);
			if (!preg_match('@href="(https?://[^/|\"]+/(?:(?:files)|(?:dl?))/[^\"|>]+)"@i', $page, $dlink)) html_error('Error: Download link not found.');

			$FileName = urldecode(basename(parse_url($dlink[1], PHP_URL_PATH)));
			$this->RedirectDownload($dlink[1], $FileName);
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

	private function PremiumDL($link) {
		$page = $this->GetPage($link, $this->cookie);
		if (!preg_match('@Location: (https?://[^/|\r|\n]+/(?:(?:files)|(?:dl?))/[^\r|\n]+)@i', $page, $dlink)) {
			$page2 = cut_str($page, '<form name="F1" method="POST"', '</form>'); //Cutting page

			$post = array();
			$post['op'] = cut_str($page2, 'name="op" value="', '"');
			is_notpresent($post['op'], 'download', 'Error parsing download-post data.');
			$post['id'] = cut_str($page2, 'name="id" value="', '"');
			$post['rand'] = cut_str($page2, 'name="rand" value="', '"');
			$post['referer'] = '';
			$post['method_premium'] = cut_str($page2, 'name="method_premium" value="', '"');
			$post['down_direct'] = 1;

			$page = $this->GetPage($link, $this->cookie, $post);

			if (!preg_match('@href="(https?://[^/|\"]+/(?:(?:files)|(?:dl?))/[^\"|>]+)"@i', $page, $dlink)) html_error('Error: Download-link not found.');
		}

		$FileName = urldecode(basename(parse_url($dlink[1], PHP_URL_PATH)));
		$this->RedirectDownload($dlink[1], $FileName);
	}

	private function Login($link) {
		global $premium_acc;
		$pA = (!empty($_REQUEST["premium_user"]) && !empty($_REQUEST["premium_pass"]) ? true : false);
		$user = ($pA ? $_REQUEST["premium_user"] : $premium_acc["filereactor_com"]["user"]);
		$pass = ($pA ? $_REQUEST["premium_pass"] : $premium_acc["filereactor_com"]["pass"]);

		if (empty($user) || empty($pass)) html_error("Login Failed: User or Password is empty. Please check login data.", 0);
		$post = array();
		$post['login'] = urlencode($user);
		$post['password'] = urlencode($pass);
		$post['op'] = "login";
		$post['redirect'] = "";

		$purl = 'http://www.filereactor.com/';
		$page = $this->GetPage($purl, $this->cookie, $post, $purl);
		is_present($page, "Incorrect Login or Password", "Login Failed: User/Password incorrect.");

		$this->cookie = GetCookiesArr($page);
		if (empty($this->cookie['xfss'])) html_error("Login Error: Cannot find session cookie.");
		$this->cookie['lang'] = 'english';

		$page = $this->GetPage("$purl?op=my_account", $this->cookie, 0, $purl);
		//is_notpresent($page, '/?op=logout', 'Login Error.');
		is_notpresent($page, '/logout', 'Login Error.');

		if (stripos($page, "Premium Account:") === false) {
			$this->changeMesg(lang(300)."<br /><b>Account isn\\\'t premium</b><br />Using it as member.");
			return $this->FreeDL($link);
		} else return $this->PremiumDL($link);
	}
}

// [15-5-2012]  Written by Th3-822. (XFS... XFS everywhere. :D)

?>