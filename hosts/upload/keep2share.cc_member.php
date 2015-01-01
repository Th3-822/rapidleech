<?php
######## Account Info ########
$upload_acc['keep2share_cc']['user'] = ''; //Set your login
$upload_acc['keep2share_cc']['pass'] = ''; //Set your password
########################

$_GET['proxy'] = isset($_GET['proxy']) ? $_GET['proxy'] : '';
$not_done = true;

if ($upload_acc['keep2share_cc']['user'] && $upload_acc['keep2share_cc']['pass']) {
	$default_acc = true;
	$_REQUEST['up_login'] = $upload_acc['keep2share_cc']['user'];
	$_REQUEST['up_pass'] = $upload_acc['keep2share_cc']['pass'];
	$_REQUEST['action'] = 'FORM';
	echo "<b><center>Using Default Login.</center></b>\n";
} else $default_acc = false;

if (empty($_REQUEST['action']) || $_REQUEST['action'] != 'FORM') {
	echo "<table border='0' style='width:270px;' cellspacing='0' align='center'>
	<form method='POST'>
	<input type='hidden' name='action' value='FORM' />
	<tr><td style='white-space:nowrap;'>&nbsp;Login*</td><td>&nbsp;<input type='text' name='up_login' value='' style='width:160px;' /></td></tr>
	<tr><td style='white-space:nowrap;'>&nbsp;Password*</td><td>&nbsp;<input type='password' name='up_pass' value='' style='width:160px;' /></td></tr>\n";
	echo "<tr><td colspan='2' align='center'><br /><input type='submit' value='Upload' /></td></tr>\n";
	echo "<tr><td colspan='2' align='center'><small>*You can set it as default in <b>".basename(__FILE__)."</b></small></td></tr>\n";
	echo "</table>\n</form>\n";
} else {
	$login = $not_done = false;
	$domain = 'keep2share.cc';
	$referer = "http://$domain/";

	// Login
	echo "<table style='width:600px;margin:auto;'>\n<tr><td align='center'>\n<div id='login' width='100%' align='center'>Login to $domain</div>\n";

	$cookie = array();
	if (!empty($_REQUEST['up_login']) && !empty($_REQUEST['up_pass'])) {
		if (!empty($_REQUEST['A_encrypted'])) {
			$_REQUEST['up_login'] = decrypt(urldecode($_REQUEST['up_login']));
			$_REQUEST['up_pass'] = decrypt(urldecode($_REQUEST['up_pass']));
			unset($_REQUEST['A_encrypted']);
		}
		CookieLogin($_REQUEST['up_login'], $_REQUEST['up_pass']);
		$login = true;
	} else html_error('Login failed: User/Password empty.');

	// Retrive upload ID
	echo "<script type='text/javascript'>document.getElementById('login').style.display='none';</script>\n<div id='info' width='100%' align='center'>Retriving upload ID</div>\n";

	$uploadData = k2s_apireq('getUploadFormData');
	k2s_checkErrors($uploadData, 'Pre-Upload Error');

	// Uploading
	echo "<script type='text/javascript'>document.getElementById('info').style.display='none';</script>\n";

	$url = parse_url($uploadData['form_action']);
	$upfiles = upfile($url['host'], defport($url), $url['path'].(!empty($url['query']) ? '?'.$url['query'] : ''), $referer, 0, $uploadData['form_data'], $lfile, $lname, $uploadData['file_field'], '', 0, 0, 0, $url['scheme']);

	// Upload Finished
	echo "<script type='text/javascript'>document.getElementById('progressblock').style.display='none';</script>\n";

	is_page($upfiles);

	$status = (int)substr($upfiles, 9, 3);
	$ulResult = ($status >= 500) ? array('status' => 'fail', 'code' => $status, 'message' => "HTTP Error $status.") : Get_Reply($upfiles);
	k2s_checkErrors($ulResult, $prefix = 'Upload error');

	if (empty($ulResult['user_file_id'])) html_error('Download link not found.', 0);
	$download_link = 'http://k2s.cc/file/'.$ulResult['user_file_id'];
}

// Edited For upload.php usage.
function EnterCaptcha($captchaImg, $inputs, $captchaSize = '5', $sname = 'Enter Captcha', $iname = 'captcha') {
	echo "\n<form name='captcha' method='POST'>\n";
	foreach ($inputs as $name => $input) echo "\t<input type='hidden' name='$name' id='$name' value='$input' />\n";
	echo "\t<h4>" . lang(301) . " <img alt='CAPTCHA Image' src='$captchaImg' /> " . lang(302) . ": <input type='text' id='captcha' name='$iname' size='$captchaSize' />&nbsp;&nbsp;\n\t\t<input type='submit' onclick='return check();' value='$sname' />\n\t</h4>\n\t<script type='text/javascript'>/* <![CDATA[ */\n\t\tfunction check() {\n\t\t\tvar captcha=document.getElementById('captcha').value;\n\t\t\tif (captcha == '') {\n\t\t\t\twindow.alert('You didn\'t enter the image verification code');\n\t\t\t\treturn false;\n\t\t\t} else return true;\n\t\t}\n\t/* ]]> */</script>\n</form>\n</body>\n</html>";
}

// Edited For upload.php usage.
function reCAPTCHA($publicKey, $inputs, $sname = 'Upload File') {
	global $cookie, $domain, $referer, $pauth;
	if (empty($publicKey) || preg_match('/[^\w\.\-]/', $publicKey)) html_error('Invalid reCAPTCHA public key.');
	if (!is_array($inputs)) html_error('Error parsing captcha post data.');
	// Check for a global recaptcha key
	$page = geturl('www.google.com', 0, '/recaptcha/api/challenge?k=' . $publicKey, 'http://fakedomain.tld/fakepath', 0, 0, 0, $_GET['proxy'], $pauth);is_page($page);
	if (substr($page, 9, 3) != '200') html_error('Invalid or deleted reCAPTCHA public key.');

	if (strpos($page, 'Invalid referer') === false) {
		// Embed captcha
		echo "<script language='JavaScript'>var RecaptchaOptions = {theme:'red', lang:'en'};</script>\n\n<center><form name='recaptcha' method='POST'><br />\n";
		foreach ($inputs as $name => $input) echo "<input type='hidden' name='$name' id='C_$name' value='$input' />\n";
		echo "<script type='text/javascript' src='http://www.google.com/recaptcha/api/challenge?k=$publicKey'></script><noscript><iframe src='http://www.google.com/recaptcha/api/noscript?k=$publicKey' height='300' width='500' frameborder='0'></iframe><br /><textarea name='recaptcha_challenge_field' rows='3' cols='40'></textarea><input type='hidden' name='recaptcha_response_field' value='manual_challenge' /></noscript><br /><input type='submit' name='submit' onclick='javascript:return checkc();' value='$sname' />\n<script type='text/javascript'>/*<![CDATA[*/\nfunction checkc(){\nvar capt=document.getElementById('recaptcha_response_field');\nif (capt.value == '') { window.alert('You didn\'t enter the image verification code.'); return false; }\nelse { return true; }\n}\n/*]]>*/</script>\n</form></center>\n</body>\n</html>";
	} else {
		// Download captcha
		$page = geturl('www.google.com', 0, '/recaptcha/api/challenge?k=' . $publicKey, $referer, 0, 0, 0, $_GET['proxy'], $pauth);is_page($page);
		if (!preg_match('@[\{,\s]challenge\s*:\s*[\'\"]([\w\-]+)[\'\"]@', $page, $challenge)) html_error('Error getting reCAPTCHA challenge.');
		$inputs['recaptcha_challenge_field'] = $challenge = $challenge[1];

		$imgReq = geturl('www.google.com', 0, '/recaptcha/api/image?c=' . $challenge, $referer, 0, 0, 0, $_GET['proxy'], $pauth);is_page($imgReq);
		list($headers, $imgBody) = explode("\r\n\r\n", $imgReq, 2);
		unset($imgReq);
		if (substr($headers, 9, 3) != '200') html_error('Error downloading captcha img.');
		$mimetype = (preg_match('@image/[\w+]+@', $headers, $mimetype) ? $mimetype[0] : 'image/jpeg');

		EnterCaptcha("data:$mimetype;base64,".base64_encode($imgBody), $inputs, 20, $sname, 'recaptcha_response_field');
	}
	exit;
}

function Login($user, $pass) {
	global $cookie, $domain, $referer, $pauth, $default_acc;

	$post = array();
	$post['LoginForm%5Busername%5D'] = urlencode($user);
	$post['LoginForm%5Bpassword%5D'] = urlencode($pass);
	$post['LoginForm%5BrememberMe%5D'] = 1;
	if (empty($_POST['step']) || !in_array($_POST['step'], array('1', '2'))) {
		$page = geturl($domain, 80, '/login.html', $referer, 0, $post, 0, $_GET['proxy'], $pauth);is_page($page);
		$cookie = GetCookiesArr($page);

		if (stripos($page, 'The verification code is incorrect.') !== false) {
			$data = array();
			$data['cookie'] = urlencode(encrypt(CookiesToStr($cookie)));
			$data['action'] = 'FORM';
			if (!$default_acc) {
				$data['A_encrypted'] = 'true';
				$data['up_login'] = urlencode(encrypt($user)); // encrypt() will keep this safe.
				$data['up_pass'] = urlencode(encrypt($pass)); // And this too.
			}
			if (preg_match('@https?://(?:[^/]+\.)?(?:(?:google\.com/recaptcha/api)|(?:recaptcha\.net))/(?:(?:challenge)|(?:noscript))\?k=([\w\.\-]+)@i', $page, $cpid)) {
				$data['step'] = '1';
				reCAPTCHA($pid[1], $data, 'Login');
			} elseif (preg_match('@\W(auth/captcha\.html\?v=\w+)@i', $page, $cpid)) {
				$data['step'] = '2';

				$imgReq = geturl($domain, 80, '/' . $cpid[1], $referer, $cookie, 0, 0, $_GET['proxy'], $pauth);is_page($imgReq);
				list($headers, $imgBody) = explode("\r\n\r\n", $imgReq, 2);
				unset($imgReq);
				if (substr($headers, 9, 3) != '200') html_error('Error downloading captcha img.');
				$mimetype = (preg_match('@image/[\w+]+@', $headers, $mimetype) ? $mimetype[0] : 'image/png');

				EnterCaptcha("data:$mimetype;base64,".base64_encode($imgBody), $data, 20, 'Login');
			} else html_error('Login CAPTCHA not found.');
			exit;
		}

		is_present($page, 'Incorrect username or password', 'Login Failed: Email/Password incorrect.');
		if (empty($cookie['c903aeaf0da94d1b365099298d28f38f'])) html_error('Login Cookie Not Found.');
		if (empty($cookie['sessid'])) html_error('Session Cookie Not Found.');

		$test = k2s_apireq('test');
		if ($test['code'] != 403) k2s_checkErrors($test, 'Login error');
		else {
			$page = geturl($domain, 80, '/', $referer.'login.html', $cookie, 0, 0, $_GET['proxy'], $pauth);is_page($page);
			is_notpresent($page, '/auth/logout.html">Logout', 'Login Error.');
		}

		SaveCookies($user, $pass); // Update cookies file
		return true;
	}

	if ($_POST['step'] == '1') {
		if (empty($_POST['recaptcha_response_field'])) html_error('You didn\'t enter the image verification code.');
		$post['LoginForm%5BverifyCode%5D'] = '';
		$post['recaptcha_challenge_field'] = urlencode($_POST['recaptcha_challenge_field']);
		$post['recaptcha_response_field'] = urlencode($_POST['recaptcha_response_field']);
	} else {
		if (empty($_POST['captcha'])) html_error('You didn\'t enter the image verification code.');
		$post['LoginForm%5BverifyCode%5D'] = urlencode($_POST['captcha']);
	}

	$_POST['step'] = false;
	$cookie = StrToCookies(decrypt(urldecode($_POST['cookie'])));

	$page = geturl($domain, 80, '/login.html', $referer, $cookie, $post, 0, $_GET['proxy'], $pauth);is_page($page);
	$cookie = GetCookiesArr($page, $cookie);

	is_present($page, 'The verification code is incorrect.');
	is_present($page, 'Incorrect username or password', 'Login Failed: Email/Password incorrect');
	if (empty($cookie['c903aeaf0da94d1b365099298d28f38f'])) html_error('Login Cookie Not Found');
	if (empty($cookie['sessid'])) html_error('Session Cookie Not Found');

	$test = k2s_apireq('test');
	if ($test['code'] != 403) k2s_checkErrors($test, 'Login Error');
	else {
		$page = geturl($domain, 80, '/', $referer.'login.html', $cookie, 0, 0, $_GET['proxy'], $pauth);is_page($page);
		is_notpresent($page, '/auth/logout.html">Logout', 'Login Error');
	}

	SaveCookies($user, $pass); // Update cookies file
	return true;
}

function IWillNameItLater($cookie, $decrypt=true) {
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

function CookieLogin($user, $pass) {
	global $domain, $referer, $secretkey, $pauth;
	if (empty($user) || empty($pass)) html_error('Login Failed: User or Password is empty.');
	$user = strtolower($user);

	$filename = 'keep2share_ul.php';
	if (!defined('DOWNLOAD_DIR')) {
		if (substr($GLOBALS['options']['download_dir'], -1) != '/') $GLOBALS['options']['download_dir'] .= '/';
		define('DOWNLOAD_DIR', (substr($GLOBALS['options']['download_dir'], 0, 6) == 'ftp://' ? '' : $GLOBALS['options']['download_dir']));
	}
	$filename = DOWNLOAD_DIR.basename($filename);
	if (!file_exists($filename)) return Login($user, $pass);

	$file = file($filename);
	$savedcookies = unserialize($file[1]);
	unset($file);

	$hash = hash('crc32b', $user.':'.$pass);
	if (is_array($savedcookies) && array_key_exists($hash, $savedcookies)) {
		$_secretkey = $secretkey;
		$secretkey = hash('crc32b', $pass).sha1($user.':'.$pass).hash('crc32b', $user); // A 56 char key should be safer. :D
		$testCookie = (decrypt(urldecode($savedcookies[$hash]['enc'])) == 'OK') ? IWillNameItLater($savedcookies[$hash]['cookie']) : false;
		$secretkey = $_secretkey;
		if (empty($testCookie) || (is_array($testCookie) && count($testCookie) < 1)) return Login($user, $pass);

		$test = k2s_apireq('test', array('auth_token' => urldecode($testCookie['sessid'])));
		if ($test['code'] != 403) k2s_checkErrors($test, 'Login error');
		else {
			// If session is expired, try to get a updated one from the site with the cookies
			$page = geturl($domain, 80, '/', $referer.'login.html', $cookie, 0, 0, $_GET['proxy'], $pauth);is_page($page);
			$testCookie = GetCookiesArr($page, $testCookie);
			if (stripos($page, '/auth/logout.html">Logout') === false || empty($testCookie['sessid'])) return Login($user, $pass);
			// Test possibly updated session
			$test2 = k2s_apireq('test', array('auth_token' => urldecode($testCookie['sessid'])));
			if ($test2['code'] == 403) return Login($user, $pass);
			k2s_checkErrors($test2, 'Login error');
		}
		$GLOBALS['cookie'] = $testCookie; // Update cookies
		SaveCookies($user, $pass); // Update cookies file
		return true;
	}
	return Login($user, $pass);
}

function SaveCookies($user, $pass) {
	global $secretkey;
	$maxdays = 31; // Max days to keep cookies saved

	$filename = 'keep2share_ul.php';
	$filename = DOWNLOAD_DIR.basename($filename);
	if (file_exists($filename)) {
		$file = file($filename);
		$savedcookies = unserialize($file[1]);
		unset($file);

		// Remove old cookies
		foreach ($savedcookies as $k => $v) if (time() - $v['time'] >= ($maxdays * 86400)) unset($savedcookies[$k]);
	} else $savedcookies = array();
	$hash = hash('crc32b', $user.':'.$pass);
	$_secretkey = $secretkey;
	$secretkey = hash('crc32b', $pass).sha1($user.':'.$pass).hash('crc32b', $user); // A 56 char key should be safer. :D
	$savedcookies[$hash] = array('time' => time(), 'enc' => urlencode(encrypt('OK')), 'cookie' => IWillNameItLater($GLOBALS['cookie'], false));
	$secretkey = $_secretkey;

	write_file($filename, "<?php exit(); ?>\r\n" . serialize($savedcookies));
}

function Get_Reply($content) {
	if (!function_exists('json_decode')) html_error('Error: Please enable JSON in PHP.');
	$content = ltrim($content);
	if (($pos = strpos($content, "\r\n\r\n")) > 0) $content = trim(substr($content, $pos + 4));
	$cb_pos = strpos($content, '{');
	$sb_pos = strpos($content, '[');
	if ($cb_pos === false && $sb_pos === false) html_error('JSON start braces not found.');
	$sb = ($cb_pos === false || $sb_pos < $cb_pos) ? true : false;
	$content = substr($content, strpos($content, ($sb ? '[' : '{')));$content = substr($content, 0, strrpos($content, ($sb ? ']' : '}')) + 1);
	if (empty($content)) html_error('No JSON content.');
	$rply = json_decode($content, true);
	if ($rply === NULL) html_error('Error reading JSON.');
	return $rply;
}

function k2s_checkErrors($reply, $prefix = 'Error') {
	if (strtolower($reply['status']) != 'fail') return;
	switch ($reply['code']) {
		default: $msg = ($reply['message'] ? $reply['message'] : '*No message for this error*');break;
		case 429: $msg = 'Too many requests to the API, please try again later';break;
		case 500: $msg = 'Server error while processing your request, please try again later';break;
		case 503: $msg = 'API temporarily not available, please try again later';break;
	}
	html_error("$prefix: [$err] $msg.");
}

function k2s_apireq($actionPath, $post = array()) {
	if (!function_exists('json_encode')) html_error('Error: Please enable JSON in PHP.');
	if (!is_array($post)) html_error('k2s_apireq: Parameter 2 must be passed as an array.');
	$post['auth_token'] = (!empty($post['auth_token']) ? $post['auth_token'] : (!empty($GLOBALS['cookie']['sessid']) ? urldecode($GLOBALS['cookie']['sessid']) : false));

	$page = geturl($GLOBALS['domain'], 80, '/api/v1/'.$actionPath, $GLOBALS['referer'], 0, json_encode($post), 0, $_GET['proxy'], $GLOBALS['pauth']);
	is_page($page);

	$status = (int)substr($page, 9, 3);
	if ($status >= 500) return array('status' => 'fail', 'code' => $status, 'message' => "k2s_apireq: HTTP Error $status.");

	return Get_Reply($page);
}

//[31-7-2014]  Written by Th3-822.

?>