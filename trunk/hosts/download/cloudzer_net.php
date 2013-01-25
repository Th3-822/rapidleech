<?php
if (!defined('RAPIDLEECH')) {
	require_once 'index.html';
	exit;
}

class cloudzer_net extends DownloadClass {
	
	public function Download($link) {
		global $premium_acc;
		
		$link = str_replace('clz.to/', 'cloudzer.net/file/', $link);
		$this->link = $link;
		if (!$_REQUEST['step']) {
			$this->page = $this->GetPage($this->link);
			is_present($this->page, '/404', 'Error[File not found!]');
			$this->cookie = GetCookiesArr($this->page);
		}
		if ($_REQUEST['premium_acc'] == 'on' && (($_REQUEST['premium_user'] && $_REQUEST['premium_pass'])||($premium_acc['cloudzer_net']['user'] && $premium_acc['cloudzer_net']['pass']))) {
			return $this->Premium();
		} else {
			return $this->Free();
		}
	}
	
	private function Free() {
		
		if ($_REQUEST['step'] == '1') {
			$this->link = urldecode($_POST['link']);
			$this->cookie = urldecode($_POST['cookie']);
			$post['recaptcha_challenge_field'] = $_POST['recaptcha_challenge_field'];
			$post['recaptcha_response_field'] = $_POST['recaptcha_response_field'];
			$recap = $_POST['recap'];
			$page = $this->GetPage(str_replace('/file/', '/io/ticket/captcha/', $this->link), $this->cookie, $post, $this->link, 0, 1);
		} else {
			if (!preg_match('/(\d+) Sekunden/i', $this->page, $w)) html_error('Error[Timer not found!]');
			if (!preg_match('/Recaptcha\.create\("([^"]+)/i', $this->GetPage('http://cloudzer.net/js/download.js'), $c)) html_error('Error[Captcha data not found!]');
			// first post
			$page = $this->GetPage(str_replace('/file/', '/io/ticket/slot/', $this->link), $this->cookie, array(), $this->link, 0, 1);
			if (!stripos($page, '"succ":true')) html_error('Error[Unknown error for FREE Download!]');
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
		$this->RedirectDownload($dlink, 'cloudzer', $this->cookie, 0, $this->link);
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
 * Rename uploaded.net into cloudzer.net since most of the template is the same by Tony Fauzi Wihana/Ruud v.Tony 21-01-2013 (must edit hosts.php first to work with short link)
 */
?>
