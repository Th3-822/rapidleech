<?php
if (!defined('RAPIDLEECH')) {
	require_once("index.html");
	exit;
}

class filesmonster_com extends DownloadClass {

	public function Download($link) {
		global $premium_acc;

		if (!$_REQUEST['step']) {
			$this->cookie = array('yab_mylang' => 'en');
			$this->page = $this->GetPage($link, $this->cookie);
			is_present($this->page, 'File not found');
			$this->cookie = GetCookiesArr($this->page, $this->cookie);
		}
		$this->link = $link;
		if ($_REQUEST['premium_acc'] == 'on' && (($_REQUEST['premium_user'] && $_REQUEST['premium_pass']) || ($premium_acc['filesmonster_com']['user'] && $premium_acc['filesmonster_com']['pass']))) {
			return $this->Premium();
		} else {
			return $this->Free();
		}
	}

	private function Free() {
		if ($_REQUEST['step'] == '1') {
			$this->link = urldecode($_POST['link']);
			$this->cookie = urldecode($_POST['cookie']);

			$post = array();
			$post['recaptcha_challenge_field'] = $_POST['recaptcha_challenge_field'];
			$post['recaptcha_response_field'] = $_POST['recaptcha_response_field'];
			$page = $this->GetPage($this->link, $this->cookie, $post);
		} else {
			is_present($this->page, "You need Premium membership to download files larger than 1.0 GB.");
			//check the file size
			$flsize = cut_str($this->page, 'File size:</td>', '</tr>');
			preg_match('/(\d+\.\d+) MB/', $flsize, $match);
			if (round($match[0]) > 100) html_error("Error[You need to split the file first directly from filesmonster!]");
			$freeform = cut_str($this->page, "<form id='slowdownload'", "</form>");
			if (!preg_match('/action="([^\r\n\s\t"]+)"/', $freeform, $fl)) html_error('Error[Free link 1 not found!]');
			$page = $this->GetPage('http://filesmonster.com' . $fl[1], $this->cookie, array(), $this->link);
			$this->cookie = GetCookiesArr($page, $this->cookie);
			$rfturl = cut_str($page, "rftUrl = '", "'");
			$step2url = cut_str($page, "step2UrlTemplate = '", "'");
			$page = $this->GetPage('http://filesmonster.com' . $rfturl, $this->cookie, 0, 'http://filesmonster.com' . $fl[1]);
			$this->cookie = GetCookiesArr($page, $this->cookie);
			$step2url = str_replace('!!!', cut_str($page, '"dlcode":"', '"'), $step2url);
			$this->link = "http://filesmonster.com" . $step2url;
			$page = $this->GetPage($this->link, $this->cookie, array(), 'http://filesmonster.com' . $rfturl);
		}
		if (preg_match('/Next free download will be available in (\d+) min/', $page, $msg)) html_error($msg[0]);
		if (stripos($page, "Enter Captcha code below")) {
			if (stripos($page, cut_str($page, '<p class="error">', '</p>'))) echo "<center><font color='red'><b>Wrong captcha text or captcha expired</b></font></center>";
			if (!preg_match('/\/challenge\?k=([^"]+)"/', $page, $c)) html_error('Error[Captcha Data not found!]');

			$data = $this->DefaultParamArr($this->link, $this->cookie);
			$data['step'] = "1";
			$this->Show_reCaptcha($c[1], $data);
			exit();
		}
		if (!preg_match("/<span id='sec'>(\d+)<\/span>/", $page, $w)) html_error('Error[Timer not found!]');
		$this->CountDown($w[1]);
		if (!preg_match("/get_link\('([^\r\n\s\t']+)'\)/", $page, $get_link)) html_error('Error[Free Link 2 not found!]');
		$page = $this->GetPage('http://filesmonster.com' . $get_link[1], $this->cookie, 0, $this->link);
		$dlink = cut_str($page, '"url":"', '"');
		if (!$dlink) html_error("Error, Free Download link not found");
		$dlink = str_replace("\/", "/", $dlink);
		$FileName = basename(parse_url($dlink, PHP_URL_PATH));
		$this->RedirectDownload($dlink, $FileName, $this->cookie, 0, $this->link);
	}

	private function Premium() {

		$cookie = $this->login();
		$page = $this->GetPage($this->link, $cookie);
		is_notpresent($page, '<span class="em lightblack">Premium</span>', 'Error[Account isn\'t Premium!]');
		is_present($page, cut_str($page, '<div id="error">', '<br>'));
		if (!preg_match('%<a href="(https?:\/\/[^\r\n\s\t"]+)"><span class="huge_button_green_left">%', $page, $rd)) html_error('Error[Redirect link PREMIUM 1 not found!]');
		$page = $this->GetPage($rd[1], $cookie, 0, $this->link);
		if (!preg_match('/get_link\("([^\r\n\s\t"]+)"\)/', $page, $rdd)) html_error('Error[Redirect link PREMIUM 2 not found!]');
		$page = $this->GetPage('http://filesmonster.com' . $rdd[1], $cookie, 0, $rd[1]);
		$dlink = cut_str($page, 'url":"', '"');
		if (!$dlink) html_error("Error, Premium Download link not found");
		$dlink = str_replace('\/', '/', $dlink);
		$FileName = basename(parse_url($dlink, PHP_URL_PATH));
		$this->RedirectDownload($dlink, $FileName, $cookie);
	}

	private function login() {
		global $premium_acc;

		$user = ($_REQUEST["premium_user"] ? trim($_REQUEST["premium_user"]) : $premium_acc ["filesmonster_com"] ["user"]);
		$pass = ($_REQUEST["premium_pass"] ? trim($_REQUEST["premium_pass"]) : $premium_acc ["filesmonster_com"] ["pass"]);
		if (empty($user) || empty($pass)) html_error("Login failed, username or password is empty!");

		$posturl = 'http://filesmonster.com/';
		$post = array();
		$post['act'] = "login";
		$post['user'] = $user;
		$post['pass'] = $pass;
		$post['login'] = "Login";
		$page = $this->GetPage($posturl . 'login.php', $this->cookie, $post, $posturl);
		is_present($page, 'Username/Password can not be found in our database!');
		$cookie = GetCookiesArr($page, $this->cookie);

		return $cookie;
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

}

//filesmonster free download plugin by Ruud v.Tony 23-06-2011
//updated 11-07-2011 by Ruud v.Tony for checking link
//updated 30-08-2011 by Ruud v.Tony to support premium
//fixed 06-03-2013 by Tony Fauzi Wihana/Ruud v.Tony
?>
