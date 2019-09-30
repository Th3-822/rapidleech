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
		if (!$referer && !empty($GLOBALS['Referer'])) {
			$referer = $GLOBALS['Referer'];
		}
		$cURL = $GLOBALS['options']['use_curl'] && extension_loaded('curl') && function_exists('curl_init') && function_exists('curl_exec') ? true : false;
		$Url = parse_url(trim($link));
		if (strtolower($Url['scheme']) == 'https') {
			$chttps = false;
			if ($cURL) {
				$cV = curl_version();
				if (in_array('https', $cV['protocols'], true)) $chttps = true;
			}
			if (!extension_loaded('openssl') && !$chttps) html_error('You need to install/enable PHP\'s OpenSSL extension to support HTTPS connections.');
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

	public function RedirectDownload($link, $FileName = 0, $cookie = 0, $post = 0, $referer = 0, $force_name = 0, $auth = 0, $addon = array()) {
		if (!$referer && !empty($GLOBALS['Referer'])) {
			$referer = $GLOBALS['Referer'];
		}
		$url = parse_url($link);
		$params = $this->DefaultParamArr($link, $cookie, $referer, true);
		unset($params['premium_acc']);
		$params['filename'] = urlencode((!empty($FileName) ? basename($FileName) : urldecode(basename(parse_url($link, PHP_URL_PATH)))));
		if (!empty($force_name)) $params['force_name'] = urlencode(basename($force_name));
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
		if (empty($link_array) || !is_array($link_array) || count($link_array) == 0) html_error('Error getting links from folder.');

		if (!is_file('audl.php') || !empty($GLOBALS['options']['auto_download_disable'])) html_error('audl.php not found or you have disable auto download feature!');

		$pos = strrpos($_SERVER['SCRIPT_NAME'], '/');
		$audlpath = ($pos !== false) ? substr($_SERVER['SCRIPT_NAME'], 0, $pos + 1).'audl.php?GO=GO' : 'audl.php?GO=GO';
		$inputs = GetDefaultParams();
		$inputs['links'] = implode("\r\n", $link_array);

		$key_array = array('premium_acc', 'premium_user', 'premium_pass', 'cookieuse', 'cookie');
		foreach ($key_array as $v) if (!empty($_GET[$v])) $inputs[$v] = urlencode($_GET[$v]);
		insert_location($inputs, $audlpath);
		exit();
	}

	public function CountDown($countDown) {
		if ($countDown <= 0) return;
		insert_timer($countDown, 'Waiting link timelock.', '', true);
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
		echo "\n<form name='captcha' action='{$_SERVER['SCRIPT_NAME']}' method='POST'>\n";
		foreach ($inputs as $name => $input) echo "\t<input type='hidden' name='$name' id='$name' value='" . htmlspecialchars($input, ENT_QUOTES) . "' />\n";
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
		if (!empty($link)) $DParam['link'] = $link;
		if (!empty($cookie)) {
			$DParam['cookie'] = urlencode($cookie);
			if ($encrypt) $DParam['cookie_encrypted'] = 1;
		}
		if (!empty($referer)) $DParam['referer'] = $referer;
		return $DParam;
	}

	/* Use this function for filehost longer timelock
	 * Param int $secs -> The number of seconds to count down
	 * Param array $post -> Variable array to include as POST so you dont need to start over the process
	 * Param $string $text -> Default text you want to display when counting down
	 */

	public function JSCountdown($secs, $post = 0, $text = '', $stop = 1, $onFinish = '') {
		if (empty($text)) $text = 'Waiting link timelock';
		if (empty($onFinish)) $onFinish = (empty($post) ? 'location.reload();' : 'document.forms.js_timer.submit();');
		echo "<p><center><span id='jsTimer_1' class='htmlerror'><b>ERROR: Please enable JavaScript. (Countdown)</b></span><br /><span id='jsTimer_2'>Please wait</span></center></p>\n";
		echo "<form action='{$_SERVER['SCRIPT_NAME']}' name='js_timer' method='POST'>\n";
		if (!empty($post) && is_array($post)) foreach ($post as $name => $input) echo "<input type='hidden' name='$name' id='C_$name' value='" . htmlspecialchars($input, ENT_QUOTES) . "' />\n";
		echo "<script type='text/javascript'>/* <![CDATA[ */\nvar jsTimer=".intval($secs).";var text='".htmlspecialchars($text, ENT_QUOTES)."';var c2=0;var dl=document.getElementById('jsTimer_1');var a2=document.getElementById('jsTimer_2');fc();fc2();function fc(){if(jsTimer>0){if(jsTimer>120)dl.innerHTML=text+'. Please wait <b>'+Math.round(jsTimer/60)+'</b> minutes...';else dl.innerHTML=text+'. Please wait <b>'+jsTimer+'</b> seconds...';jsTimer--;setTimeout('fc()',1000)}else{dl.style.display='none';$onFinish}}function fc2(){if(jsTimer>120){if(c2<=20){a2.innerHTML=a2.innerHTML+'.';c2++}else{c2=10;a2.innerHTML=''}setTimeout('fc2()',100)}else{dl2.style.display='none'}}\n/* ]]> */</script></form><br />";
		if ($stop) {
			include(TEMPLATE_DIR.'footer.php');
			exit();
		}
	}

	public function changeMesg($mesg, $add=false) {
		echo("\n<script type='text/javascript'>document.getElementById('mesg').innerHTML = " . ($add ? "document.getElementById('mesg').innerHTML + " : '') . "unescape('" . rawurlencode($mesg) . "');</script>");
	}

	public function json2array($content, $errorPrefix = 'Error') {
		if (!function_exists('json_decode')) html_error('Error: Please enable JSON in php.');
		if (empty($content)) html_error("[$errorPrefix]: No content.");
		$content = ltrim($content);
		if (($pos = strpos($content, "\r\n\r\n")) > 0) $content = trim(substr($content, $pos + 4));
		$cb_pos = strpos($content, '{');
		$sb_pos = strpos($content, '[');
		if ($cb_pos === false && $sb_pos === false) html_error("[$errorPrefix]: JSON start braces not found.");
		$sb = ($cb_pos === false || $sb_pos < $cb_pos) ? true : false;
		$content = substr($content, strpos($content, ($sb ? '[' : '{')));$content = substr($content, 0, strrpos($content, ($sb ? ']' : '}')) + 1);
		if (empty($content)) html_error("[$errorPrefix]: No JSON content.");
		$rply = json_decode($content, true);
		if ($rply === NULL) html_error("[$errorPrefix]: Error reading JSON.");
		return $rply;
	}

	public function reCAPTCHA($publicKey, $inputs, $referer = 0, $sname = 'Download File') {
		if (empty($publicKey) || preg_match('/[^\w\.\-]/', $publicKey)) html_error('Invalid reCAPTCHA PublicKey Format.');
		if (!is_array($inputs)) html_error('Error parsing captcha post data.');

		$cookie = array('PREF' => 'LD=en');
		// Check for a global recaptcha key
		$page = $this->GetPage('http://www.google.com/recaptcha/api/challenge?k=' . $publicKey, $cookie, 0, 'http://fakedomain.tld/fakepath');
		if (substr($page, 9, 3) != '200') html_error('Invalid or Deleted reCAPTCHA PublicKey.');
		$inputs['recaptcha_public_key'] = $publicKey; // This may be needed later.

		if (strpos($page, 'Invalid referer') === false && strpos($page, 'An internal error occurred') === false) {
			// Embed captcha
			echo "<script language='JavaScript'>var RecaptchaOptions = {theme:'red', lang:'en'};</script>\n\n<center><form name='recaptcha' action='{$_SERVER['SCRIPT_NAME']}' method='POST'><br />\n";
			foreach ($inputs as $name => $input) echo "<input type='hidden' name='$name' id='C_$name' value='" . htmlspecialchars($input, ENT_QUOTES) . "' />\n";
			echo "<script type='text/javascript' src='//www.google.com/recaptcha/api/challenge?k=$publicKey'></script><noscript><iframe src='//www.google.com/recaptcha/api/noscript?k=$publicKey' height='300' width='500' frameborder='0'></iframe><br /><textarea name='recaptcha_challenge_field' rows='3' cols='40'></textarea><input type='hidden' name='recaptcha_response_field' value='manual_challenge' /></noscript><br /><input type='submit' name='submit' onclick='javascript:return checkc();' value='$sname' />\n<script type='text/javascript'>/*<![CDATA[*/\nfunction checkc(){\nvar capt=document.getElementById('recaptcha_response_field');\nif (capt.value == '') { window.alert('You didn\'t enter the image verification code.'); return false; }\nelse { return true; }\n}\n/*]]>*/</script>\n</form></center>\n";
			include(TEMPLATE_DIR.'footer.php');
		} else {
			// Download captcha
			$page = $this->GetPage('http://www.google.com/recaptcha/api/challenge?k=' . $publicKey, $cookie, 0, $referer);
			if (strpos($page, 'Invalid referer') !== false) html_error('Error getting reCAPTCHA challenge: Bad referer');
			if (strpos($page, 'An internal error occurred') !== false) html_error('Error getting reCAPTCHA challenge: Possible reCAPTCHA v2 or bad referer');

			if (!preg_match('@[\{,\s]challenge\s*:\s*[\'\"]([\w\.\-]+)[\'\"]@', $page, $challenge)) html_error('Error getting reCAPTCHA challenge.');
			$inputs['recaptcha_challenge_field'] = $challenge = $challenge[1];

			list($headers, $imgBody) = explode("\r\n\r\n", $this->GetPage('http://www.google.com/recaptcha/api/image?c=' . $challenge, 0, 0, $referer), 2);
			if (substr($headers, 9, 3) != '200') html_error('Error downloading captcha img.');
			$mimetype = (preg_match('@image/[\w+]+@', $headers, $mimetype) ? $mimetype[0] : 'image/jpeg');

			$this->EnterCaptcha("data:$mimetype;base64,".base64_encode($imgBody), $inputs, 20, $sname, 'recaptcha_response_field');
		}
		exit;
	}

	public function SolveMedia($publicKey, $data, $referer = 0, $sname = 'Download File') {
		if (!is_array($data)) html_error('Post needs to be sended in a array.');
		if (empty($publicKey) || preg_match('@[^\w\-\.]@', $publicKey)) html_error('[SM] Invalid value for $publicKey');
		$page = $this->GetPage("http://api.solvemedia.com/papi/challenge.noscript?k=$publicKey", 0, 0, $referer);
		is_present($page, 'domain / ckey mismatch', '[SM] Error getting CAPTCHA challenge: Bad referer.');
		if (!preg_match('@<img [^/<>]*src\s?=\s?\"((https?://[^/\"<>]+)?/papi/media[^\"<>]+)\"@i', $page, $imgurl)) html_error('[SM] CAPTCHA img not found.');
		$imgurl = (empty($imgurl[2])) ? 'http://api.solvemedia.com'.$imgurl[1] : $imgurl[1];

		if (!preg_match_all('@<input [^/|<|>]*type\s?=\s?\"?hidden\"?[^/<>]*\s?name\s?=\s?\"(\w+)\"[^/<>]*\s?value\s?=\s?\"([^\"<>]+)\"[^/<>]*/?\s*>@i', $page, $forms)) html_error('[SM] CAPTCHA data not found.');
		$forms = array_combine($forms[1], $forms[2]);
		foreach ($forms as $n => $v) $data["_smc[$n]"] = urlencode($v);
		$data['sm_public_key'] = $publicKey; // Required for verifySolveMedia()

		//Download captcha img.
		list($headers, $imgBody) = explode("\r\n\r\n", $this->GetPage($imgurl), 2);
		if (substr($headers, 9, 3) != '200') html_error('[SM] Error downloading CAPTCHA img.');
		$mimetype = (preg_match('@image/[\w+]+@', $headers, $mimetype) ? $mimetype[0] : 'image/gif');

		$this->EnterCaptcha("data:$mimetype;base64,".base64_encode($imgBody), $data, 20, $sname, 'adcopy_response');
		exit;
	}

	public function verifySolveMedia($directOutput = false, $retryMethod = 'retrySolveMedia') {
		if (empty($_POST['adcopy_response'])) html_error('[SM] You didn\'t enter the image verification code.');
		if (empty($_POST['_smc']) || !is_array($_POST['_smc'])) html_error('[SM] CAPTCHA data invalid.');
		$post = array();
		foreach ($_POST['_smc'] as $n => $v) $post[urlencode($n)] = $v;
		$post['adcopy_response'] = urlencode($_POST['adcopy_response']);
		$publicKey = $_POST['sm_public_key'];

		$link = 'http://api.solvemedia.com/papi/verify.noscript';
		$page = $this->GetPage($link, 0, $post, 'http://api.solvemedia.com/papi/challenge.noscript?k=' . urlencode($publicKey));

		if (!preg_match('@(https?://[^/\'\"<>\r\n]+)?/papi/verify\.pass\.noscript\?[^/\'\"<>\r\n]+@i', $page, $resp)) {
			if (stripos($page, '/papi/challenge.noscript') !== false) {
				$retryCallback = array($this, $retryMethod);
				if (is_callable($retryCallback)) {
					echo '<span class="htmlerror"><b>[SM] Wrong CAPTCHA entered.</b></span><br /><br />';
					return call_user_func($retryCallback);
				} else html_error('[SM] Wrong CAPTCHA entered.');
			}
			html_error('Error sending CAPTCHA.');
		}
		$resp = (empty($resp[1])) ? 'http://api.solvemedia.com'.$resp[0] : $resp[0];

		$page = $this->GetPage($resp, 0, 0, $link);
		if (!preg_match('@>\s*([^<>\s]+)\s*</textarea>@i', $page, $gibberish)) html_error('[SM] CAPTCHA response not found.');

		if ($directOutput) return urlencode($gibberish[1]);
		else return array('adcopy_challenge' => urlencode($gibberish[1]), 'adcopy_response' => 'manual_challenge');
	}

	public function reCAPTCHAv2($publicKey, $inputs, $referer = 0, $sname = 'Download File') {
		// RC2 no longer works due to security enhancements
		html_error('reCAPTCHA2 is no longer supported');
	}

	public function verifyReCaptchav2($directOutput = false, $retryMethod = 'retryReCaptchav2') {
		// RC2 no longer works due to security enhancements
		html_error('reCAPTCHA2 is no longer supported');
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
