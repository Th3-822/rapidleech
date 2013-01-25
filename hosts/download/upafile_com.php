<?php
if (!defined('RAPIDLEECH')) {
	require_once 'index.html';
	exit;
}

class upafile_com extends DownloadClass {
	
	public function Download($link) {
		global $premium_acc;
		
		if (!$_REQUEST['step']) {
			$this->cookie = array('lang' => 'english');
			$this->page = $this->GetPage($link, $this->cookie);
			is_present($this->page, '<b>File Not Found</b>');
		}
		$this->link = $link;
		if ($_REQUEST['premium_acc'] == 'on' && (($_REQUEST['premium_user'] && $_REQUEST['premium_pass'])||($premium_acc['upafile_com']['user'] && $premium_acc['upafile_com']['pass']))) {
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
			foreach ($_POST['tmp'] as $k => $v) {
				$post[$k] = $v;
			}
			$post['recaptcha_challenge_field'] = $_POST['recaptcha_challenge_field'];
			$post['recaptcha_response_field'] = $_POST['recaptcha_response_field'];
			$page = $this->GetPage($this->link, $this->cookie, $post, $this->link);
		} else {
			$form = cut_str($this->page, '<Form name="F1" method="POST"', '</Form>');
			if (!preg_match_all('/<input type="hidden" name="([^"]+)" value="([^"]+)?">/', $form, $match)) html_error('Error[Post Data - FREE not found!]');
			$match = array_combine($match[1], $match[2]);
			if (!preg_match('/\/api\/challenge\?k=([^"]+)/', $form, $c)) html_error('Error[Captcha data not found!]');
			if (!preg_match('/(\d+)<\/span> seconds/', $form, $w)) html_error('Error[Timer not found!]');
			$this->CountDown($w[1]);
			
			$data = $this->DefaultParamArr($this->link, $this->cookie);
			$data['step'] = '1';
			foreach ($match as $k => $v) {
				$data["tmp[$k]"] = $v;
			}
			$this->Show_reCaptcha($c[1], $data);
			exit;
		}
		is_present($page, cut_str($page, '<div class="err">', '</div>'));
		if (!preg_match('/https?:\/\/s\d+\.upafile\.com(:\d+)?\/d\/[^\r\n\'"]+/', $page, $dl)) html_error('Error[Download Link - FREE not found!]');
		$dlink = trim($dl[0]);
		$filename = cut_str($page, 'Filename: <b>', '</b>');
		$this->RedirectDownload($dlink, $filename, $this->cookie, 0, $this->link, $filename);
		exit;
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
 * Written by Tony Fauzi Wihana/Ruud v.Tony 22-01-2013
 */
?>
