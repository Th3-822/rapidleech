<?php

if (!defined('RAPIDLEECH')) {
	require_once('index.html');
	exit;
}

class zippyshare_com extends DownloadClass {
	public $link;
	private $page, $cookie;
	public function Download($link) {
		$this->link = $link;
		$this->cookie = array('ziplocale' => 'en');

		if (!empty($_POST['step'])) switch ($_POST['step']) {
			case '1': return $this->CheckCaptcha();
			case '2': return $this->GetDecodedLink();
		}

		$this->page = $this->GetPage($this->link, $this->cookie);
		is_present($this->page, '>File does not exist on this server<', 'File does not exist.');
		$this->cookie = GetCookiesArr($this->page, $this->cookie);

		if (($pos = stripos($this->page, 'getElementById(\'dlbutton\').href')) !== false || ($pos = stripos($this->page, 'getElementById("dlbutton").href')) !== false) return $this->GetJSEncodedLink($pos);
		else return $this->GetCaptcha();
	}

	private function GetDecodedLink() {
		if (empty($_POST['dllink']) || ($dlink = parse_url($_POST['dllink'])) == false || empty($dlink['path'])) html_error('Empty decoded link field.');
		$this->cookie = urldecode($_POST['cookie']);

		$dlink = 'http://' . parse_url($this->link, PHP_URL_HOST) . $dlink['path'] . (!empty($dlink['query']) ? '?' . $dlink['query'] : '');
		$fname = urldecode(basename(parse_url($dlink, PHP_URL_PATH)));
		$this->RedirectDownload($dlink, $fname, $this->cookie);
	}

	private function GetJSEncodedLink($pos) {
		global $PHP_SELF;
		$script = substr(strstr(substr($this->page, strripos(substr($this->page, 0, $pos), '<script ')), '>'), 1);
		$script = rtrim(str_replace(array(').href', "'dlbutton'", '"dlbutton"', '    '), array(').value', "'T8_dllink'", '"T8_dllink"', "\t\t"), substr($script, 0, strpos($script, '</script>'))));
		if (empty($script)) html_error('Error while getting js code.');

		$data = $this->DefaultParamArr($this->link, $this->cookie);
		$data['step'] = '2';
		$data['dllink'] = '';
		echo "\n<form name='zs_dcode' action='$PHP_SELF' method='POST'><br />\n";
		foreach ($data as $name => $input) echo "<input type='hidden' name='$name' id='T8_$name' value='$input' />\n";
		echo("</form>\n<span id='T8_emsg' class='htmlerror' style='text-align: center;display: none;'></span>\n<noscript><span class='htmlerror'><b>Sorry, this code needs JavaScript enabled to work.</b></span></noscript>\n<script type='text/javascript'>/* <![CDATA[ */\n\ttry {{$script}\n\t} catch(e) {\n\t\t$('#T8_emsg').html('<b>Cannot decode link: ['+e.name+'] '+e.message+'</b>').show();\n\t}\n\twindow.setTimeout(\"$('form[name=zs_dcode]').submit();\", 300); // 300 µs to make sure that the value was decoded and added.\n/* ]]> */</script>\n\n</body>\n</html>");
		exit;
	}

	private function GetCaptcha() {
		if (!preg_match('@/d/\d+/(\d+)/[^\r\n\t\'\"<>\;]+@i', $this->page, $dlpath)) html_error('Download Link Not Found.');
		if (!preg_match('@Recaptcha\.create[\s\t]*\([\s\t]*\"([\w\-]+)\"@i', $this->page, $cpid)) html_error('reCAPTCHA Not Found.');
		//if (!preg_match('@\Wshortencode[\s\t]*:[\s\t]*\'?(\d+)\'?@i', $this->page, $short)) html_error('Captcha Data Not Found.');

		$data = $this->DefaultParamArr($this->link, $this->cookie);
		$data['step'] = '1';
		$data['dlpath'] = urlencode($dlpath[0]);
		$data['shortencode'] = urlencode($dlpath[1]);

		$this->Show_reCaptcha($cpid[1], $data);
	}

	private function CheckCaptcha() {
		if (empty($_POST['recaptcha_response_field'])) html_error('You didn\'t enter the image verification code.');
		$host = 'http://' . parse_url($this->link, PHP_URL_HOST);
		$this->cookie = urldecode($_POST['cookie']);

		$post = array();
		$post['challenge'] = $_POST['recaptcha_challenge_field'];
		$post['response'] = $_POST['recaptcha_response_field'];
		$post['shortencode'] = $_POST['shortencode'];

		$page = $this->GetPage($host . '/rest/captcha/test', $this->cookie, $post, $this->link . "\r\nX-Requested-With: XMLHttpRequest");
		$body = strtolower(trim(substr($page, strpos($page, "\r\n\r\n"))));

		if ($body == 'false') html_error('Error: Wrong CAPTCHA Entered.');
		elseif ($body != 'true') html_error('Unknown Reply from Server.');

		$dlink = $host . urldecode($_POST['dlpath']);
		$fname = urldecode(basename(parse_url($dlink, PHP_URL_PATH)));
		$this->RedirectDownload($dlink, $fname, $this->cookie);
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
}

// [24-11-2012]  Written by Th3-822. (Only for rev43 :D)
// [05-2-2013]  Added support for links that need user-side decoding of the link. - Th3-822

?>