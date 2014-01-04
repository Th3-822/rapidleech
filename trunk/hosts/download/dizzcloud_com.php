<?php

if (!defined('RAPIDLEECH')) {
	require_once('index.html');
	exit();
}

class dizzcloud_com extends DownloadClass {
	private $page, $cookie;
	public function Download($link) {
		global $premium_acc;
		$this->cookie = array();
		if (!preg_match('@^https?://(?:www\.)?dizzcloud\.com/dl/(\w{7})/?@i', $link, $fid)) html_error('Invalid Link?');
		$this->link = $fid[0];
		$this->fid = $fid[1];

		if (empty($_POST['step']) || $_POST['step'] != '1') {
			$this->page = $this->GetPage($link, $this->cookie);
			is_present($this->page, '>File not found<', 'The file you were looking for could not be found');
		}

		if ($_REQUEST['premium_acc'] == 'on' && ((!empty($_REQUEST['premium_user']) && !empty($_REQUEST['premium_pass'])) || (!empty($premium_acc['dizzcloud_com']['user']) && !empty($premium_acc['dizzcloud_com']['pass'])))) $this->Login();
		else $this->FreeDL();
	}

	private function Show_reCaptcha($pid, $inputs, $sname = 'Download File') {
		global $PHP_SELF;
		if (!is_array($inputs)) html_error('Error parsing captcha data.');
		// Themes: 'red', 'white', 'blackglass', 'clean'
		echo "<script language='JavaScript'>var RecaptchaOptions = {theme:'red', lang:'en'};</script>\n\n<center><form name='recaptcha' action='$PHP_SELF' method='POST'><br />\n";
		foreach ($inputs as $name => $input) echo "<input type='hidden' name='$name' id='C_$name' value='$input' />\n";
		echo "<script type='text/javascript' src='http://www.google.com/recaptcha/api/challenge?k=$pid'></script><noscript><iframe src='http://www.google.com/recaptcha/api/noscript?k=$pid' height='300' width='500' frameborder='0'></iframe><br /><textarea name='recaptcha_challenge_field' rows='3' cols='40'></textarea><input type='hidden' name='recaptcha_response_field' value='manual_challenge' /></noscript><br /><input type='submit' name='submit' onclick='javascript:return checkc();' value='$sname' />\n<script type='text/javascript'>/*<![CDATA[*/\nfunction checkc(){\nvar capt=document.getElementById('recaptcha_response_field');\nif (capt.value == '') { window.alert('You didn\'t enter the image verification code.'); return false; }\nelse { return true; }\n}\n/*]]>*/</script>\n</form></center>\n</body>\n</html>";
		exit;
	}

	private function Get_Reply($content) {
		if (!function_exists('json_decode')) html_error('Error: Please enable JSON in php.');
		if (($pos = strpos($content, "\r\n\r\n")) > 0) $content = substr($content, $pos + 4);
		$cb_pos = strpos($content, '{');
		$sb_pos = strpos($content, '[');
		if ($cb_pos === false && $sb_pos === false) html_error('Json start braces not found.');
		$sb = ($cb_pos === false || $sb_pos < $cb_pos) ? true : false;
		$content = substr($content, strpos($content, ($sb ? '[' : '{')));$content = substr($content, 0, strrpos($content, ($sb ? ']' : '}')) + 1);
		if (empty($content)) html_error('No json content.');
		$rply = json_decode($content, true);
		if (!$rply || count($rply) == 0) html_error('Error reading json.');
		return $rply;
	}

	private function FreeDL() {
		if (empty($_POST['step']) || $_POST['step'] != '1') {
			if (!preg_match('@https?://(?:[^/]+\.)?(?:(?:google\.com/recaptcha/api)|(?:recaptcha\.net))/(?:(?:challenge)|(?:noscript))\?k=([\w|\-]+)@i', $this->page, $pid)) $pid = array(1 => '6LcEvs0SAAAAAAykpzcaaxpegnSndWcEWYsSMs0M');

			$data = $this->DefaultParamArr($this->link, (empty($this->cookie)) ? 0 : encrypt(CookiesToStr($this->cookie)));
			$data['step'] = '1';

			$this->Show_reCaptcha($pid[1], $data);
			exit;
		} else {
			if (empty($_POST['recaptcha_response_field'])) html_error('You didn\'t enter the image verification code.');
			if (!empty($_POST['cookie'])) $this->cookie = StrToCookies(decrypt(urldecode($_POST['cookie'])));

			$query = array();
			$query['type'] = 'recaptcha';
			$query['challenge'] = $_POST['recaptcha_challenge_field'];
			$query['capture'] = $_POST['recaptcha_response_field'];

			$page = $this->GetPage($this->link.'?'.http_build_query($query), $this->cookie);
			$reply = $this->Get_Reply($page);
			if (!empty($reply['err'])) html_error('Error: '.htmlentities($reply['err']));
			if (empty($reply['href'])) html_error('Error: Download link not found.');

			$this->RedirectDownload($reply['href'], urldecode(basename(parse_url($reply['href'], PHP_URL_PATH))));
		}
	}

	private function PremiumDL() {
		$page = $this->GetPage($this->link, $this->cookie);
		if (!preg_match('@https?://p\w+\.cloudstoreservice\.net/[^\'\"\t<>\r\n]+@i', $page, $dlink)) html_error('Error: Download-link not found.');

		$this->RedirectDownload($dlink[0], urldecode(basename(parse_url($dlink[0], PHP_URL_PATH))));
	}

	private function Login() {
		global $premium_acc;
		$pA = (!empty($_REQUEST['premium_user']) && !empty($_REQUEST['premium_pass']) ? true : false);
		$user = ($pA ? $_REQUEST['premium_user'] : $premium_acc['dizzcloud_com']['user']);
		$pass = ($pA ? $_REQUEST['premium_pass'] : $premium_acc['dizzcloud_com']['pass']);

		if (empty($user) || empty($pass)) html_error('Login Failed: User or Password is empty. Please check login data.');
		$post = array();
		$post['email'] = urlencode($user);
		$post['pass'] = urlencode($pass);

		$purl = 'http://dizzcloud.com/';
		$page = $this->GetPage($purl.'login', $this->cookie, $post, $purl.'login');

		$this->cookie = GetCookiesArr($page);
		if (empty($this->cookie['auth_uid']) || empty($this->cookie['auth_hash'])) html_error('Login failed: User/Password incorrect?.');

		$page = $this->GetPage($purl, $this->cookie, 0, $purl.'login');
		if (stripos($page, '/logout') === false) html_error('Login Error.');

		if (stripos($page, '>Premium till') === false) {
			$this->changeMesg(lang(300).'<br /><b>Account isn\\\'t premium</b><br />Using it as member.');
			$this->page = $this->GetPage($this->link, $this->cookie);
			return $this->FreeDL();
		} else return $this->PremiumDL();
	}
}

// [20-7-2013]  Written by Th3-822.

?>