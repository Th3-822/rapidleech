<?php
if (!defined('RAPIDLEECH')) {
	require_once ('index.html');
	exit;
}

class uploaded_net extends DownloadClass {

	private $cookie, $page;
	public $link;
	public function Download($link) {
		global $premium_acc;

		if (stristr($link, '/folder/')) {
			$link = str_replace(array('ul.to/folder/', 'uploaded.to/folder/'), 'uploaded.net/folder/', $link);
		} else {
			$link = str_replace(array('ul.to/', 'uploaded.to/file/'), 'uploaded.net/file/', $link);
		}
		$this->link = $link;
		if (!$_REQUEST['step']) {
			$this->page = $this->GetPage($this->link);
			if (preg_match_all('/href="([^\r\n"]+)" class="file"/', $this->page, $match, PREG_SET_ORDER)) {
				$arr_link = array();
				foreach ($match as $tmp) $arr_link[] = "http://uploaded.net/$tmp[1]";
				$this->moveToAutoDownloader($arr_link);
			}
			is_present($this->page, 'doesn\'t contain files', 'The folder link doesn\'t contain any files!');
			is_present($this->page, '/404', 'File not found or set to private only');
			is_present($this->page, 'This file was protected by a password against unauthorised downloads');
			$this->cookie = GetCookiesArr($this->page);
		}
		if (($_REQUEST["cookieuse"] == "on" && preg_match("/login\s?=\s?(\w{84})/i", $_REQUEST["cookie"], $c)) || ($_REQUEST["premium_acc"] == "on" && $premium_acc["uploaded_net"]["cookie"])) {
			$loginc = (empty($c[1]) ? $premium_acc["uploaded_net"]["cookie"] : $c[1]);
			return $this->login($loginc);
		} elseif ($_REQUEST['premium_acc'] == 'on' && (($_REQUEST['premium_user'] && $_REQUEST['premium_pass']) || ($premium_acc['uploaded_net']['user'] && $premium_acc['uploaded_net']['pass']))) {
			return $this->login();
		} else {
			return $this->Free();
		}
	}

	private function login($loginc=false) {
		global $premium_acc;

		$posturl = 'http://uploaded.net/';
		if (!$loginc) {
			$id = ($_REQUEST["premium_user"] ? trim($_REQUEST["premium_user"]) : $premium_acc ["uploaded_net"] ["user"]);
			$pw = ($_REQUEST["premium_pass"] ? trim($_REQUEST["premium_pass"]) : $premium_acc ["uploaded_net"] ["pass"]);
			if (empty($id) || empty($pw)) html_error("Login failed, username or password is empty!");

			$post = array();
			$post['id'] = $id;
			$post['pw'] = $pw;
			$page = $this->GetPage($posturl . 'io/login', $this->cookie, $post, $posturl, 0, 1);
			is_present($page, "Error[" . cut_str($page, 'err":"', '"')) . "]";
			$this->cookie = GetCookiesArr($page, $this->cookie);
		} elseif (strlen($loginc) == 84) {
			$this->cookie['login'] = $loginc;
		} else {
			html_error("Error[Cookie Invalid(" . strlen($loginc) . " != 84). Try to encode your cookie first!]");
		}

		$page = $this->GetPage($posturl . 'me', $this->cookie, 0, $posturl);
		$this->cookie = GetCookiesArr($page, $this->cookie, true, array('', 'deleted', '""'));
		is_present($page, '<em>Free</em>', 'Error[Account isn\'t Premium!]');
		is_present($page, 'ocation: http://uploaded.to', 'Error[Cookie Failed!]');

		$quota = cut_str($page, '<div id="traffic"', '</table>');
		if (!preg_match_all('/class="cB">(\d.+)\s? (\wB)<\/\w+><\/th>/i', $quota, $tr)) html_error('Error[Form account profile may have changed!]');

		$this->changeMesg(lang(300) . "<br />Uploaded.net Premium Download<br />Traffic: For Downloading: {$tr[1][0]} {$tr[2][0]}, Flexible usable contingent for DDL: {$tr[1][1]} {$tr[2][1]}, Total: {$tr[1][2]} {$tr[2][2]}.");
		if ((round($tr[1][2]) <= 100) && ($tr[2][2] != 'GB')) html_error("Error[Traffic is exhausted! Total Traffic Left: {$tr[1][2]} {$tr[2][2]}]");

		return $this->Premium();
	}

	private function Premium() {

		$page = $this->GetPage($this->link, $this->cookie);
		is_present($page, "Traffic exhausted", "Premium account is out of Bandwidth");
		if (!preg_match('/https?:\/\/stor\d+\.uploaded\.net(:\d+)?\/dl\/[^\r\n\'"]+/', $page, $dl)) html_error('Error[Download link - PREMIUM not found!]');
		$dlink = trim($dl[0]);
		$this->RedirectDownload($dlink, "uploaded", $this->cookie);
	}

	private function Free() {
		if ($_REQUEST['step'] == '1') {
			$post['recaptcha_challenge_field'] = $_POST['recaptcha_challenge_field'];
			$post['recaptcha_response_field'] = $_POST['recaptcha_response_field'];
			$this->link = urldecode($_POST['link']);
			$this->cookie = urldecode($_POST['cookie']);
			$recap = $_POST['recap'];
			$page = $this->GetPage(str_replace('/file/', '/io/ticket/captcha/', $this->link), $this->cookie, $post, $this->link, 0, 1);
		} else {
			is_present($this->page, 'This file exceeds the max. filesize which can be downloaded by free users.');
			if (!preg_match('/(\d+)<\/span> seconds/', $this->page, $w)) html_error('Error[Timer not found!]');
			if (!preg_match('/Recaptcha\.create\("([^"]+)/i', $this->GetPage('http://uploaded.net/js/download.js'), $c)) html_error('Error[Captcha data not found!]');
			// first post
			$page = $this->GetPage(str_replace('/file/', '/io/ticket/slot/', $this->link), $this->cookie, array(), $this->link, 0, 1);
			if (!stripos($page, 'succ:true')) html_error('Error[Unknown error for FREE Download!]');
			$this->CountDown($w[1]);

			$data = $this->DefaultParamArr($this->link, $this->cookie);
			$data['step'] = '1';
			$data['recap'] = $c[1]; // to enable captcha retry
			$this->Show_reCaptcha($c[1], $data);
			exit;
		}
		if (stripos($page, 'err')) {
			$json = $this->Get_Reply($page);
			if ($json['err'] == 'captcha') {
				echo "<center><font color='red'><b>Entered CAPTCHA was incorrect, please try again!</b></font></center>";
				$data = $this->DefaultParamArr($this->link, $this->cookie);
				$data['step'] = '1';
				$data['recap'] = $recap;
				$this->Show_reCaptcha($recap, $data);
				exit;
			} else {
				html_error($json['err']);
			}
		}
		if (!preg_match("/url:'(https?:\/\/[^\r\n\']+)'/", $page, $dl)) html_error('Error[Download link - FREE not found!]');
		$dlink = trim($dl[1]);
		$this->RedirectDownload($dlink, 'uploaded', $this->cookie, 0, $this->link);
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

/*
 * Written by Tony Fauzi Wihana/Ruud v.Tony 18-01-2013
 */
?>
