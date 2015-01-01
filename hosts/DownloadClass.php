<?php
if (!defined('RAPIDLEECH')) {
	require('../deny.php');
	exit;
}

class DownloadClass {
	/*
	 * Prints the initial form for displaying messages
	 * @return void
	 */

	public function __construct($echo = true) {
		if (!$echo) return;
		echo('<table width="600" align="center">');
		echo('<tr>');
		echo('<td align="center">');
		echo('<div id="mesg" width="100%" align="center">' . lang(300) . '</div>');
	}

	/*
	 * You can use this function to retrieve pages without parsing the link
	 * @param string $link -> The link of the page to retrieve
	 * @param string $cookie -> The cookie value if you need
	 * @param array $post -> Array name=>value of the post data
	 * @param string $referer -> The referer of the page, it might be the value you are missing if you can't get plugin to work
	 * @param string $auth -> Page authentication, unneeded in most circumstances
	 */

	public function GetPage($link, $cookie = 0, $post = 0, $referer = 0, $auth = 0, $XMLRequest = 0) {
		global $options;
		if (!$referer) {
			global $Referer;
			$referer = $Referer;
		}
		$cURL = $options['use_curl'] && extension_loaded('curl') && function_exists('curl_init') && function_exists('curl_exec') ? true : false;
		$Url = parse_url(trim($link));
		if ($Url ['scheme'] == 'https') {
			$chttps = false;
			if ($cURL) {
				$cV = curl_version();
				if (in_array('https', $cV['protocols'], true)) $chttps = true;
			}
			if (!extension_loaded('openssl') && !$chttps) html_error('This server doesn\'t support https connections.');
			elseif (!$chttps) $cURL = false;
		}

		if ($cURL) {
			if ($XMLRequest) $referer .= "\r\nX-Requested-With: XMLHttpRequest";
			$page = cURL($link, $cookie, $post, $referer, $auth);
		} else {
			global $pauth;
			$page = geturl($Url['host'], defport($Url), $Url['path'] . (!empty($Url['query']) ? '?' . $Url['query'] : ''), $referer, $cookie, $post, 0, !empty($_GET['proxy']) ? $_GET['proxy'] : '', $pauth, $auth, $Url['scheme'], 0, $XMLRequest);
			is_page($page);
		}
		return $page;
	}

	/*
	 * Use this function instead of insert_location so that we can improve this feature in the future
	 * @param string $link -> The download link of the file
	 * @param string $FileName -> The name of the file
	 * @param string $cookie -> The cookie value
	 * @param array $post -> The post value will be serialized here
	 * @param string $referer -> The page that refered to this link
	 * @param string $auth -> In format username:password
	 * @param array $params -> This parameter allows you to add extra _GET values to be passed on
	 */

	public function RedirectDownload($link, $FileName, $cookie = 0, $post = 0, $referer = 0, $force_name = 0, $auth = 0, $addon = array()) {
		global $pauth;
		if (!$referer) {
			global $Referer;
			$referer = $Referer;
		}
		$url = parse_url($link);
		$params = $this->DefaultParamArr($link, (!empty($cookie) ? (is_array($cookie) ? encrypt(CookiesToStr($cookie)) : encrypt($cookie)) : 0), $referer);
		$params['filename'] = urlencode($FileName);
		if (!empty($force_name)) $params['force_name'] = urlencode($force_name);
		$params['host'] = urlencode($url['host']);
		if (!empty($url['port'])) $params['port'] = urlencode($url['port']);
		$params['path'] = urlencode($url['path'] . (!empty($url['query']) ? '?' . $url['query'] : ''));
		if (!empty($post)) $params['post'] = urlencode(encrypt(serialize($post)));
		if (!empty($auth)) $params['auth'] = ($auth == '1' ? '1' : urlencode(encrypt(base64_encode($auth))));
		if (!empty($addon)) {
			if (!is_array($addon)) html_error('Plugin problem! Please report, error: "The parameter passed must be an array"'); // Some problems with the plugin, quit it
			foreach ($addon as $name => $value) $params[$name] = (is_array($value) ? urlencode(serialize($value)) : urlencode($value));
		}
		insert_location($params);
	}

	/*
	 * Use this function to move your multiples links array to auto downloader
	 * @param array $link_array -> Normal array containing all download links
	 */

	public function moveToAutoDownloader($link_array) {
		global $PHP_SELF, $options;
		if (empty($link_array) || !is_array($link_array) || count($link_array) == 0) html_error('Error getting links from folder.');

		if (!is_file('audl.php') || !empty($options['auto_download_disable'])) html_error('audl.php not found or you have disable auto download feature!');

		$pos = strrpos($PHP_SELF, '/');
		$audlpath = ($pos !== false) ? substr($PHP_SELF, 0, $pos + 1).'audl.php?GO=GO' : 'audl.php?GO=GO';
		$inputs = GetDefaultParams();
		$inputs['links'] = implode("\r\n", $link_array);

		$key_array = array('premium_acc', 'premium_user', 'premium_pass', 'cookieuse', 'cookie');
		foreach ($key_array as $v) if (!empty($_GET[$v])) $inputs[$v] = urlencode($_GET[$v]);
		insert_location($inputs, $audlpath);
		exit();
	}

	public function CountDown($countDown) {
		if ($countDown <= 0) return;
		insert_timer($countDown, "Waiting link timelock");
	}

	/*
	 * Use this function to create Captcha display form
	 * @param string $captchaImg -> The link of the captcha image or downloaded captcha image on server
	 * @param array $inputs -> Key Value pairs for html form input elements ( these elements will be hidden form elements )
	 * @param string $captchaSize -> The size of captcha text box
	 * @param string $sname -> The text of submit button
	 * @param string $sname -> The name of captcha text field
	 */

	public function EnterCaptcha($captchaImg, $inputs, $captchaSize = '5', $sname = 'Enter Captcha', $iname = 'captcha') {
		echo "\n<form name='captcha' action='{$GLOBALS['PHP_SELF']}' method='POST'>\n";
		foreach ($inputs as $name => $input) echo "\t<input type='hidden' name='$name' id='$name' value='$input' />\n";
		echo "\t<h4>" . lang(301) . " <img alt='CAPTCHA Image' src='$captchaImg' /> " . lang(302) . ": <input id='captcha' type='text' name='$iname' size='$captchaSize' />&nbsp;&nbsp;\n\t\t<input type='submit' onclick='return check();' value='$sname' />\n\t</h4>\n\t<script type='text/javascript'>/* <![CDATA[ */\n\t\tfunction check() {\n\t\t\tvar captcha=document.getElementById('captcha').value;\n\t\t\tif (captcha == '') {\n\t\t\t\twindow.alert('You didn\'t enter the image verification code');\n\t\t\t\treturn false;\n\t\t\t} else return true;\n\t\t}\n\t/* ]]> */</script>\n</form>\n";
		include(TEMPLATE_DIR.'footer.php');
		exit();
	}

	/*
	 * This function will return an array with the Default Key Value pairs including proxy, method, email, etc.
	 * @param string $link -> Adds the link value to the array url encoded if you need it.
	 * @param string $cookie -> Adds the cookie value to the array url encoded if you need it.
	 * @param string $referer -> Adds the referer value to the array url encoded if you need it. If isn't set, it will load $Referer value. (Set as 0 or false for not add it in the array.)
	 */

	public function DefaultParamArr($link = 0, $cookie = 0, $referer = 1, $encrypt = 0) {
		if ($referer === 1 || $referer === true) {
			global $Referer;
			$referer = $Referer;
		}
		if (!empty($cookie)) {
			if (is_array($cookie)) $cookie = CookiesToStr($cookie);
			if ($encrypt) $cookie = encrypt($cookie);
		}

		$DParam = GetDefaultParams();
		if (!empty($link)) $DParam['link'] = urlencode($link);
		if (!empty($cookie)) $DParam['cookie'] = urlencode($cookie);
		if (!empty($referer)) $DParam['referer'] = urlencode($referer);
		return $DParam;
	}

	/* Use this function for filehost longer timelock
	 * Param int $secs -> The number of seconds to count down
	 * Param array $post -> Variable array to include as POST so you dont need to start over the process
	 * Param $string $text -> Default text you want to display when counting down
	 */

	public function JSCountdown($secs, $post = 0, $text='Waiting link timelock', $stop = 1) {
		echo "<p><center><span id='dl' class='htmlerror'><b>ERROR: Please enable JavaScript. (Countdown)</b></span><br /><span id='dl2'>Please wait</span></center></p>\n";
		echo "<form action='{$GLOBALS['PHP_SELF']}' name='cdwait' method='POST'>\n";
		if (!empty($post) && is_array($post)) foreach ($post as $name => $input) echo "<input type='hidden' name='$name' id='C_$name' value='$input' />\n";
		?><script type="text/javascript">/* <![CDATA[ */
		var c = <?php echo $secs; ?>;var text = "<?php echo $text; ?>";var c2 = 0;var dl = document.getElementById("dl");var a2 = document.getElementById("dl2");fc();fc2();
		function fc() {
			if (c > 0) {
				if (c > 120) {
					dl.innerHTML = text+". Please wait <b>"+ Math.round(c/60) +"</b> minutes...";
				} else {
					dl.innerHTML = text+". Please wait <b>"+c+"</b> seconds...";
				}
				c = c - 1;
				setTimeout("fc()", 1000);
			} else {
				dl.style.display="none";
				void(<?php if (!empty($post)) echo 'document.forms.cdwait.submit()';else echo 'location.reload()'; ?>);
			}
		}
		function fc2(){if(c>120){if(c2<=20){a2.innerHTML=a2.innerHTML+".";c2=c2+1}else{c2=10;a2.innerHTML=""}setTimeout("fc2()",100)}else{dl2.style.display="none"}}<?php
		echo "/* ]]> */</script></form><br />";
		if ($stop) {
			include(TEMPLATE_DIR.'footer.php');
			exit();
		}
	}

	public function changeMesg($mesg, $add=false) {
		echo("\n<script type='text/javascript'>document.getElementById('mesg').innerHTML = " . ($add ? "document.getElementById('mesg').innerHTML + " : '') . "unescape('" . rawurlencode($mesg) . "');</script>");
	}

	public function reCAPTCHA($publicKey, $inputs, $sname = 'Download File') {
		if (empty($publicKey) || preg_match('/[^\w\.\-]/', $publicKey)) html_error('Invalid reCAPTCHA public key.');
		if (!is_array($inputs)) html_error('Error parsing captcha post data.');
		// Check for a global recaptcha key
		$page = $this->GetPage('http://www.google.com/recaptcha/api/challenge?k=' . $publicKey, 0, 0, 'http://fakedomain.tld/fakepath');
		if (substr($page, 9, 3) != '200') html_error('Invalid or deleted reCAPTCHA public key.');

		if (strpos($page, 'Invalid referer') === false) {
			// Embed captcha
			echo "<script language='JavaScript'>var RecaptchaOptions = {theme:'red', lang:'en'};</script>\n\n<center><form name='recaptcha' action='{$GLOBALS['PHP_SELF']}' method='POST'><br />\n";
			foreach ($inputs as $name => $input) echo "<input type='hidden' name='$name' id='C_$name' value='$input' />\n";
			echo "<script type='text/javascript' src='http://www.google.com/recaptcha/api/challenge?k=$publicKey'></script><noscript><iframe src='http://www.google.com/recaptcha/api/noscript?k=$publicKey' height='300' width='500' frameborder='0'></iframe><br /><textarea name='recaptcha_challenge_field' rows='3' cols='40'></textarea><input type='hidden' name='recaptcha_response_field' value='manual_challenge' /></noscript><br /><input type='submit' name='submit' onclick='javascript:return checkc();' value='$sname' />\n<script type='text/javascript'>/*<![CDATA[*/\nfunction checkc(){\nvar capt=document.getElementById('recaptcha_response_field');\nif (capt.value == '') { window.alert('You didn\'t enter the image verification code.'); return false; }\nelse { return true; }\n}\n/*]]>*/</script>\n</form></center>\n";
			include(TEMPLATE_DIR.'footer.php');
		} else {
			// Download captcha
			$page = $this->GetPage('http://www.google.com/recaptcha/api/challenge?k=' . $publicKey);
			if (!preg_match('@[\{,\s]challenge\s*:\s*[\'\"]([\w\-]+)[\'\"]@', $page, $challenge)) html_error('Error getting reCAPTCHA challenge.');
			$inputs['recaptcha_challenge_field'] = $challenge = $challenge[1];

			list($headers, $imgBody) = explode("\r\n\r\n", $this->GetPage('http://www.google.com/recaptcha/api/image?c=' . $challenge), 2);
			if (substr($headers, 9, 3) != '200') html_error('Error downloading captcha img.');
			$mimetype = (preg_match('@image/[\w+]+@', $headers, $mimetype) ? $mimetype[0] : 'image/jpeg');

			$this->EnterCaptcha("data:$mimetype;base64,".base64_encode($imgBody), $inputs, 20, $sname, 'recaptcha_response_field');
		}
		exit;
	}

	public function captchaSolveMedia($skey, $data, $referer = 0, $sname = 'Download File') {
		if (!is_array($data)) html_error('Post needs to be sended in a array.');
		if (empty($skey) || preg_match('@[^\w\-\.]@', $skey)) html_error('Invalid value for $skey');
		$page = $this->GetPage("http://api.solvemedia.com/papi/challenge.noscript?k=$skey", 0, 0, $referer);
		if (!preg_match('@<img [^/<>]*src\s?=\s?\"((https?://[^/\"<>]+)?/papi/media[^\"<>]+)\"@i', $page, $imgurl)) html_error('[SM] CAPTCHA img not found.');
		$imgurl = (empty($imgurl[2])) ? 'http://api.solvemedia.com'.$imgurl[1] : $imgurl[1];

		if (!preg_match_all('@<input [^/|<|>]*type\s?=\s?\"?hidden\"?[^/<>]*\s?name\s?=\s?\"(\w+)\"[^/<>]*\s?value\s?=\s?\"([^\"<>]+)\"[^/<>]*/?\s*>@i', $page, $forms)) html_error('[SM] CAPTCHA data not found.');
		$forms = array_combine($forms[1], $forms[2]);
		foreach ($forms as $n => $v) $data["_smc[$n]"] = urlencode($v);

		//Download captcha img.
		list($headers, $imgBody) = explode("\r\n\r\n", $this->GetPage($imgurl), 2);
		if (substr($headers, 9, 3) != '200') html_error('[SM] Error downloading CAPTCHA img.');
		$mimetype = (preg_match('@image/[\w+]+@', $headers, $mimetype) ? $mimetype[0] : 'image/gif');

		$this->EnterCaptcha("data:$mimetype;base64,".base64_encode($imgBody), $data, 20, $sname, 'adcopy_response');
		exit;
	}

	public function verifySolveMedia($referer = 0) {
		if (empty($_POST['adcopy_response'])) html_error('[SM] You didn\'t enter the image verification code.');
		if (empty($_POST['_smc']) || !is_array($_POST['_smc'])) html_error('[SM] CAPTCHA data invalid.');
		$post = array();
		foreach ($_POST['_smc'] as $n => $v) $post[urlencode($n)] = $v;
		$post['adcopy_response'] = urlencode($_POST['adcopy_response']);

		$url = 'http://api.solvemedia.com/papi/verify.noscript';
		$page = $this->GetPage($url, 0, $post, $referer);

		if (!preg_match('@(https?://[^/\'\"<>\r\n]+)?/papi/verify\.pass\.noscript\?[^/\'\"<>\r\n]+@i', $page, $resp)) {
			is_present($page, '/papi/challenge.noscript', '[SM] Wrong CAPTCHA entered.');
			html_error('Error sending CAPTCHA.');
		}
		$resp = (empty($resp[1])) ? 'http://api.solvemedia.com'.$resp[0] : $resp[0];

		$page = $this->GetPage($resp, 0, 0, $url);
		if (!preg_match('@>[\s\t\r\n]*([^<>\r\n]+)[\s\t\r\n]*</textarea>@i', $page, $gibberish)) html_error('[SM] CAPTCHA response not found.');

		return array('adcopy_challenge' => urlencode($gibberish[1]), 'adcopy_response' => 'manual_challenge');
	}

}

/**********************************************************
  Added support of force_name in RedirectDownload function by Raj Malhotra on 02 May 2010
  Fixed  EnterCaptcha function ( Re-Write )  by Raj Malhotra on 16 May 2010
  Added auto-encryption system (szal) 14 June 2010
  Added GetPage support function for https connection by Th3-822 21 April 2011
  Added GetPage support function for xml request by vdhdevil 9 July 2011
  Tweaked DefaultParamArr code by Th3-822 22 July 2011
  Moved JSCountdown function for future use by Th3-822
  Add CheckBack function to test correctly download link by vdhdevil
  Remove declaration of checkback function, it automatically signed in the plugin itself
  Add new limitation options by Ruud v.Tony
 **********************************************************/
?>