<?php
if (!defined('RAPIDLEECH')) {
	require_once('index.html');
	exit();
}

class letitbit_net extends DownloadClass {
	private $page, $cookie;
	public $link;
	public function Download($link) {
		global $premium_acc;
		$this->cookie = array('lang' => 'en');
		// Check link
		if (empty($_REQUEST['step']) || $_REQUEST['step'] != '1') {
			$this->page = $this->GetPage($link, $this->cookie);
			$this->cookie = GetCookiesArr($this->page, $this->cookie);
			if (preg_match('@Location: ((https?://(?:[^/\r\n]+\.)?letitbit\.net)?/[^\r\n]+)@i', $this->page, $redir)) {
				$link = (empty($redir[2])) ? 'http://letitbit.net'.$redir[1] : $redir[1];
				$this->page = $this->GetPage($link, $this->cookie);
				$this->cookie = GetCookiesArr($this->page, $this->cookie);
			}
			if (stripos($this->page, 'File not found') !== false) {
				if ($this->cookie['country'] == 'US') html_error('The requested file was not found or isn\'t downloadable in your server\'s country.'); // It seems that lib blocks downloads from usa... I will check this later and add the error msg if it's true. - T8
				html_error('The requested file was not found.');
			}
		}
		$this->link = $link;
		if ($_REQUEST ['premium_acc'] == 'on' && (($_REQUEST['premium_user'] && $_REQUEST ['premium_pass']) || (!empty($premium_acc ['letitbit_net'] ['user']) && !empty($premium_acc ['letitbit_net'] ['pass'])))) {
			$user = $_REQUEST ["premium_user"] ? $_REQUEST ["premium_user"] : $premium_acc ["letitbit_net"] ["user"];
			$pass = $_REQUEST ["premium_pass"] ? $_REQUEST ["premium_pass"] : $premium_acc ["letitbit_net"] ["pass"];
			if (empty($user) || empty($pass)) html_error("Login Failed: Username or Password is empty. Please check login data.");
			return $this->SkipLoginC($user, $pass);
		} elseif ($_REQUEST['premium_acc'] == 'on' && (($_REQUEST['premium_pass'])||(!empty($premium_acc['letitbit_net']['pass'])))) {
			$key = ($_REQUEST ["premium_pass"] ? trim($_REQUEST ["premium_pass"]) : $premium_acc["letitbit_net"]["pass"]);
			return $this->Premium($key);
		} elseif (!empty($_REQUEST['step']) && $_REQUEST['step'] == '1') {
			return $this->Free();
		} else {
			return $this->Retrieve();
		}
	}

	private function Retrieve() {
		$form = cut_str($this->page, '<form id="ifree_form"', '<div class="wrapper-centered">');
		if (empty($form)) html_error("Error: Empty Free Form 1!");
		$post = $this->AutomatePost($form);
		$this->link = "http://letitbit.net" . cut_str($form, 'action="', '"');
		$page = $this->GetPage($this->link, $this->cookie, $post);
		$this->cookie = GetCookiesArr($page, $this->cookie);
		unset($post);
        if (!preg_match("/ajax_check_url = '((http:\/\/[a-z0-9]+\.[\w.]+)\/[^\r\n']+)';/", $page, $check)) html_error("Error: Redirect link [Free] not found!");
		$this->link = $check[1];
		$this->server = $check[2];
		// If you want, you can skip the countdown...
		if (preg_match('@(\d+)<\/span> seconds@', $page, $wait)) $this->CountDown($wait[1]);
		// end countdown timer...
		$this->GetPage($this->link, $this->cookie, array(), 0, 0, 1); //empty array in post variable needed...

		if (!preg_match('@https?://(?:[^/]+\.)?(?:(?:google\.com/recaptcha/api)|(?:recaptcha\.net))/(?:(?:challenge)|(?:noscript))\?k=([\w|\-]+)@i', $page, $pid)) html_error('reCAPTCHA not found.');
		if (!preg_match('@var[\s\t]+recaptcha_control_field[\s\t]*=[\s\t]*\'([^\'\;]+)\'@i', $page, $ctrl)) html_error('Captcha control field not found.');
 
		$data = $this->DefaultParamArr($this->server . "/ajax/check_recaptcha.php", $this->cookie);
		$data['step'] = '1';
		$data['recaptcha_control_field'] = rawurlencode($ctrl[1]);
		$this->Show_reCaptcha($pid[1], $data);
	}

	private function Free() {
		if (empty($_POST['recaptcha_response_field'])) html_error('You didn\'t enter the image verification code.');
		$post = array();
		$post['recaptcha_challenge_field'] = $_POST['recaptcha_challenge_field'];
		$post['recaptcha_response_field'] = $_POST['recaptcha_response_field'];
		$post['recaptcha_control_field'] = rawurlencode(rawurldecode($_POST['recaptcha_control_field']));

		$this->cookie = urldecode($_POST['cookie']);
		$page = $this->GetPage($this->link, $this->cookie, $post, 0, 0, 1); //too many XML request needed so I used default http.php function in geturl...
		is_present($page, 'error_wrong_captcha', 'Error: Wrong Captcha Entered.');
		is_present($page, 'error_free_download_blocked', 'Error: FreeDL limit reached.');
		if (!preg_match_all('/"(https?:[^\|\r\n\"\']+)"?/', $page, $dl)) html_error('Error: Download link [Free] not found.');
		$dlink = str_replace('\\', '', trim($dl[1][array_rand($dl[1])]));
		$FileName = urldecode(basename(parse_url($dlink, PHP_URL_PATH)));
		$this->RedirectDownload($dlink, $FileName, $this->cookie, 0);
		exit();
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

	private function Premium($premiumkey = false) {
		if ($premiumkey) {
			$form = cut_str($this->page, '<div class="hide-block" id="password_area">', '<div class="column label" style="width:200px">');
			if (empty($form)) html_error("Error: Empty Premium Key Form!");
			$post = $this->AutomatePost($form);
			$post['pass'] = $premiumkey;
			$post['submit_sms_ways_have_pass'] = 'Download file';
			$this->link = "http://letitbit.net" . cut_str($form, '<form action="', '"');
			$this->page = $this->GetPage($this->link, $this->cookie, $post, $Referer);
		} else {
			$this->page = $this->GetPage($this->link, $this->cookie, 0, $this->link);
		}
		$this->cookie = GetCookiesArr($this->page, $this->cookie);
		if (preg_match('@Location: (http(s)?:\/\/[^\r\n]+)@i', $this->page, $redir)) {
			$this->link = trim($redir[1]);
			$this->page = $this->GetPage($this->link, $this->cookie, 0, $this->link);
		}
		is_present($this->page, 'The premium key has been banned for sharing with other people.');
		is_present($this->page, 'This premium key is attached to a registered account', 'You need to use your username and password not the premium key!');
		is_present($this->page, 'callback_no_pass', 'This premium key does not exist.');
		if (!preg_match_all('@"(https?://[^/]+/[^\"]+)"\s*\:\s*"direct\_link\_\d+"@', $this->page, $dl)) html_error('Error: Download Link [Premium] not found!');
		$dlink = trim($dl[1][array_rand($dl[1])]);
		$FileName = urldecode(basename(parse_url($dlink, PHP_URL_PATH)));
		$this->RedirectDownload($dlink, $FileName, $this->cookie, 0, $this->link);
	}

	private function login($user, $pass) {
		$post = array();
		$post['act'] = 'login';
		$post['login'] = $user;
		$post['password'] = $pass;
		$check = $this->GetPage("http://letitbit.net/ajax/auth.php", $this->cookie, $post, "http://letitbit.net/");
		is_present($check, 'Authorization data is invalid');
		is_present($check, 'Your login attempts have been made more than 100 times in 24 hours, the next attempt will be available only tomorrow.');
		$this->cookie = GetCookiesArr($check, array('lang' => 'en'));
		if (empty($this->cookie['log']) || empty($this->cookie['pas'])) html_error('Error [log/pass cookie not found!]');

		$this->SaveCookies($user, $pass);

		return $this->Premium();
	}

	private function IWillNameItLater($cookie, $decrypt=true) {
		if (!is_array($cookie)) {
			if (!empty($cookie)) return $decrypt ? decrypt(urldecode($cookie)) : urlencode(encrypt($cookie));
			return '';
		}
		if (count($cookie) < 1) return $cookie;
		$keys = array_keys($cookie);
		$values = array_values($cookie);
		$keys = $decrypt ? array_map('decrypt', array_map('urldecode', $keys)) : array_map('urlencode', array_map('encrypt', $keys));
		$values = $decrypt ? array_map('decrypt', array_map('urldecode', $values)) : array_map('urlencode', array_map('encrypt', $values));
		return array_combine($keys, $values);
	}

	private function SkipLoginC($user, $pass, $filename = 'letitbit_dl.php') {
		global $secretkey;
		$this->maxdays = 3; // Max days to keep cookies saved

		$filename = DOWNLOAD_DIR . basename($filename);
		if (!file_exists($filename)) return $this->login($user, $pass);

		$file = file($filename);
		$savedcookies = unserialize($file[1]);
		unset($file);

		$hash = hash('crc32b', $user . ':' . $pass);
		if (array_key_exists($hash, $savedcookies)) {
			if (time() - $savedcookies[$hash]['time'] >= ($this->maxdays * 24 * 60 * 60)) return $this->login($user, $pass); // Ignore old cookies
			$_secretkey = $secretkey;
			$secretkey = sha1($user . ':' . $pass);
			$this->cookie = (decrypt(urldecode($savedcookies[$hash]['enc'])) == 'OK') ? $this->IWillNameItLater($savedcookies[$hash]['cookie']) : '';
			$secretkey = $_secretkey;
			if (is_array($this->cookie)) unset($this->cookie['PHPSESSID']);
			if ((is_array($this->cookie) && count($this->cookie) < 1) || empty($this->cookie)) return $this->login($user, $pass);

			$check = $this->GetPage("http://letitbit.net/", $this->cookie, 0, "http://letitbit.net/");
			if (stripos($check, 'title="Logout">Logout</a>') === false) return $this->login($user, $pass);

			$this->SaveCookies($user, $pass); // Update cookies file

			return $this->Premium();
		}
		return $this->login($user, $pass);
	}

	private function SaveCookies($user, $pass, $filename = 'letitbit_dl.php') {
		global $secretkey;
		$filename = DOWNLOAD_DIR . basename($filename);
		if (file_exists($filename)) {
			$file = file($filename);
			$savedcookies = unserialize($file[1]);
			unset($file);

			// Remove old cookies
			foreach ($savedcookies as $k => $v) if (time() - $v['time'] >= ($this->maxdays * 24 * 60 * 60)) unset($savedcookies[$k]);
		} else $savedcookies = array();
		$hash = hash('crc32b', $user . ':' . $pass);
		$_secretkey = $secretkey;
		$secretkey = sha1($user . ':' . $pass);
		$savedcookies[$hash] = array('time' => time(), 'enc' => urlencode(encrypt('OK')), 'cookie' => $this->IWillNameItLater($this->cookie, false));
		$secretkey = $_secretkey;

		write_file($filename, "<?php exit(); ?>\r\n" . serialize($savedcookies));
	}

	private function AutomatePost($form) {
		if (!preg_match_all('@<input type="hidden" name="([^"]+)" value="([^"]+)" \/>@i', $form, $match)) html_error("Error: Post Data not found!");
		$post = array();
		$match = array_combine($match[1], $match[2]);
		foreach ($match as $k => $v) $post[$k] = ($v == "") ? 1 : $v;
		return $post;
	}

}

/***********************************************************************************************\
  WRITTEN BY VinhNhaTrang 15-11-2010
  Fix the premium code by code by vdhdevil
  Fix the free download code by vdhdevil & Ruud v.Tony 25-3-2011
  Updated the premium code by Ruud v.Tony 19-5-2011
  Updated for site layout change by Ruud v.Tony 24-7-2011
  Updated for joining between premium user & pass with only single key by Ruud v.Tony 13-10-2011
  Small fix in post form by Ruud v.Tony 16-12-2011 (sorry for the delay, I'm busy with my real life)
  Fix free code by Ruud v.Tony & Th3-822 for letitbit new layout 31-12-2011 (Happy new year everyone)
  Fix new login policy & free download code from letitbit by Ruud v.Tony & Th3-822 18-02-2012
  Fixed free download code by Ruud v.Tony 16-04-2012
  Fixed for redirects in download links by Th3-822 16-10-2012
  reCaptcha support added at freedl by Th3-822 24-11-2012
\***********************************************************************************************/

?>