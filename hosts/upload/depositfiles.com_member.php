<?php
######## Account Info ########
$upload_acc['depositfiles_com']['user'] = ''; //Set your login
$upload_acc['depositfiles_com']['pass'] = ''; //Set your password
########################

$_GET['proxy'] = isset($_GET['proxy']) ? $_GET['proxy'] : '';
$not_done = true;
$continue_up = false;

if ($upload_acc['depositfiles_com']['user'] && $upload_acc['depositfiles_com']['pass']) {
	$default_acc = true;
	$_REQUEST['up_login'] = $upload_acc['depositfiles_com']['user'];
	$_REQUEST['up_pass'] = $upload_acc['depositfiles_com']['pass'];
	$_REQUEST['action'] = 'FORM';
	echo "<b><center>Using Default Login.</center></b>\n";
} else $default_acc = false;

if (!empty($_REQUEST['action']) && $_REQUEST['action'] == 'FORM') $continue_up = true;
else {
	echo "<table border='0' style='width:270px;' cellspacing='0' align='center'>
	<form method='POST'>
	<input type='hidden' name='action' value='FORM' />
	<tr><td style='white-space:nowrap;'>&nbsp;Login*</td><td>&nbsp;<input type='text' name='up_login' value='' style='width:160px;' /></td></tr>
	<tr><td style='white-space:nowrap;'>&nbsp;Password*</td><td>&nbsp;<input type='password' name='up_pass' value='' style='width:160px;' /></td></tr>\n";
	echo "<tr><td colspan='2' align='center'><br /><input type='submit' value='Upload' /></td></tr>\n";
	echo "<tr><td colspan='2' align='center'><small>*You can set it as default in <b>".basename(__FILE__)."</b></small></td></tr>\n";
	echo "</table>\n</form>\n";
}

if ($continue_up) {
	$login = $not_done = false;
	$cookie = array('lang_current' => 'en');
	$domain = CheckDomain('depositfiles.com');
	$referer = "https://$domain/";

	// Login
	echo "<table style='width:600px;margin:auto;'>\n<tr><td align='center'>\n<div id='login' width='100%' align='center'>Login to $domain</div>\n";

	if (!empty($_REQUEST['up_login']) && !empty($_REQUEST['up_pass'])) {
		if (!$default_acc && !empty($_POST['up_encrypted']) && $_POST['up_encrypted'] == 'true') {
			$_REQUEST['up_login'] = decrypt(urldecode($_REQUEST['up_login']));
			$_REQUEST['up_pass'] = decrypt(urldecode($_REQUEST['up_pass']));
		}
		SkipLoginC(strtolower($_REQUEST['up_login']), $_REQUEST['up_pass']);
		$login = true;
	} else echo "<b><center>Login not found or empty, using non member upload.</center></b>\n";

	// Retrive upload ID
	echo "<script type='text/javascript'>document.getElementById('login').style.display='none';</script>\n<div id='info' width='100%' align='center'>Retrive upload ID</div>\n";

	$page = cURL($referer, $cookie, 0, $referer);

	if (!preg_match('@https?://fileshare\d+\.(?:depositfiles|dfiles)\.[^/:\r\n\t\"\'<>]+(?:\:\d+)?/[\w\-]+/[^\'"\r\n\<>;\s\t]*@i', $page, $up)) html_error('Error: Cannot find upload server.');

	$post = array();
	$post['format'] = 'html5';
	$post['member_passkey'] = cut_str($page, 'sharedkey="', '"');
	$post['fm'] = '_root';
	$post['fmh'] = '';

	if (empty($post['member_passkey'])) html_error('Upload sharedkey Not Found.');

	$up_url = $up[0];

	// Uploading
	echo "<script type='text/javascript'>document.getElementById('info').style.display='none';</script>\n";

	$url = parse_url($up_url);
	$url['scheme'] = 'http'; // Force HTTP on upload.
	$upfiles = upfile($url['host'], defport($url), $url['path'].(!empty($url['query']) ? '?'.$url['query'] : ''), $referer, 0, $post, $lfile, $lname, 'files', '', $_GET['proxy'], $pauth, 0, $url['scheme']);

	// Upload Finished
	echo "<script type='text/javascript'>document.getElementById('progressblock').style.display='none';</script>\n";

	is_page($upfiles);

	$json = json2array($upfiles, 'Upload Error');

	if (!empty($json['download_url'])) {
		$download_link = $json['download_url'];
		if (!empty($json['delete_url'])) $delete_link = $json['delete_url'];

		// Optional?
		cURL($referer.'api/upload/add_info?url='.urlencode($json['download_url']).'&size='.$fsize, $cookie, 0, $referer);
	} else {
		if (!$default_acc) textarea($upfiles);
		html_error('Download link not found.');
	}
}

function CheckDomain($domain) {
	global $cookie, $pauth;
	$domain = strtolower($domain);
	$url = "https://$domain/";
	$page = cURL($url, $cookie, 0, $url);
	if (($hpos = strpos($page, "\r\n\r\n")) > 0) $page = substr($page, 0, $hpos);
	if (stripos($page, "\nLocation: ") !== false && preg_match('@\nLocation: (?:https?:)?//(?:[^/\r\n]+\.)?((?:depositfiles|dfiles)\.[^/:\r\n\t\"\'<>]+)(?:\:\d+)?/@i', $page, $redir_domain)) {
		$redir_domain = strtolower($redir_domain[1]);
		if ($domain != $redir_domain) $domain = $redir_domain;
	}
	return $domain;
}

function json2array($content, $errorPrefix = 'Error') {
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

function Login($user, $pass) {
	global $default_acc, $cookie, $domain, $referer, $pauth;
	$errors = array('CaptchaInvalid' => 'Wrong CAPTCHA entered.', 'InvalidLogIn' => 'Invalid Login/Pass.', 'CaptchaRequired' => 'Captcha Required.');
	if (!empty($_POST['step']) && $_POST['step'] == '1') {
		html_error('reCAPTCHA2 Not Supported ATM.');
		/*
		if (empty($_POST['recaptcha_response_field'])) html_error('You didn\'t enter the image verification code.');
		$post = array('recaptcha_challenge_field' => $_POST['recaptcha_challenge_field'], 'recaptcha_response_field' => $_POST['recaptcha_response_field']);
		$post['login'] = urlencode($user);
		$post['password'] = urlencode($pass);

		$page = cURL($referer . 'api/user/login', $cookie, $post, $referer.'login.php?return=%2F');
		$json = json2array($page, 'Login Error P');
		if (!empty($json['error'])) html_error('Login Error'. (!empty($errors[$json['error']]) ? ': ' . $errors[$json['error']] : '..'));
		elseif ($json['status'] != 'OK') html_error('Login Failed');

		$cookie = GetCookiesArr($page, $cookie);
		if (empty($cookie['autologin'])) html_error('Login Error: Cannot find "autologin" cookie');

		SaveCookies($user, $pass); // Update cookies file
		return true;
		*/
	} else {
		$post = array();
		$post['login'] = urlencode($user);
		$post['password'] = urlencode($pass);

		$page = cURL($referer . 'api/user/login', $cookie, $post, $referer.'login.php?return=%2F');
		$json = json2array($page, 'Login Error');
		if (!empty($json['error']) && $json['error'] != 'CaptchaRequired') html_error('Login Error'. (!empty($errors[$json['error']]) ? ': ' . $errors[$json['error']] : '.'));
		elseif ($json['status'] == 'OK') {
			$cookie = GetCookiesArr($page, $cookie);
			if (empty($cookie['autologin'])) html_error('Login Error: Cannot find "autologin" cookie.');
			SaveCookies($user, $pass); // Update cookies file
			return true;
		} elseif (empty($json['error']) || $json['error'] != 'CaptchaRequired') html_error('Login Failed.');

		// Captcha Required
		$page = cURL($referer.'login.php?return=%2F', $cookie, 0, $referer);
		$cookie = GetCookiesArr($page, $cookie);

		if (!preg_match('@(https?://([^/\r\n\t\s\'\"<>]+\.)?(?:depositfiles|dfiles)\.[^/:\r\n\t\"\'<>]+(?:\:\d+)?)/js/base2\.js@i', $page, $jsurl)) html_error('Cannot find captcha.');
		$jsurl = (empty($jsurl[1])) ? 'https://' . $domain . $jsurl[0] : $jsurl[0];
		$page = cURL($jsurl, $cookie, 0, $referer.'login.php?return=%2F');

		if (!preg_match('@recaptcha2PublicKey\s*=\s*[\'\"]([\w\.\-]+)@i', $page, $cpid)) html_error('reCAPTCHA2 Not Found.');

		$post = array('action' => 'FORM');
		$post['step'] = '1';
		if (!$default_acc) {
			$post['up_encrypted'] = 'true';
			$post['up_login'] = urlencode(encrypt($user));
			$post['up_pass'] = urlencode(encrypt($pass));
		}
		html_error('reCAPTCHA2 Not Supported ATM.');
	}
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

function SkipLoginC($user, $pass) {
	global $cookie, $domain, $referer, $secretkey, $pauth;
	if (!defined('DOWNLOAD_DIR')) {
		global $options;
		if (substr($options['download_dir'], -1) != '/') $options['download_dir'] .= '/';
		define('DOWNLOAD_DIR', (substr($options['download_dir'], 0, 6) == 'ftp://' ? '' : $options['download_dir']));
	}

	$filename = DOWNLOAD_DIR.basename('depositfiles_ul.php');
	if (!file_exists($filename)) return Login($user, $pass);

	$file = file($filename);
	$savedcookies = unserialize($file[1]);
	unset($file);

	$hash = hash('crc32b', $user.':'.$pass);
	if (array_key_exists($hash, $savedcookies)) {
		$_secretkey = $secretkey;
		$secretkey = sha1($user.':'.$pass);
		$testCookie = (decrypt(urldecode($savedcookies[$hash]['enc'])) == 'OK') ? IWillNameItLater($savedcookies[$hash]['cookie']) : '';
		$secretkey = $_secretkey;
		if ((is_array($testCookie) && count($testCookie) < 1) || empty($testCookie)) return Login($user, $pass);

		$page = cURL($referer, $testCookie, 0, $referer);
		if (stripos($page, 'style="display: none;" data-type="guest"') === false) return Login($user, $pass);
		$cookie = $testCookie; // Update cookies
		SaveCookies($user, $pass); // Update cookies file
		return true;
	}
	return Login($user, $pass);
}

function SaveCookies($user, $pass) {
	global $cookie, $secretkey;
	$maxdays = 31; // Max days to keep cookies saved
	$filename = DOWNLOAD_DIR.basename('depositfiles_ul.php');
	if (file_exists($filename)) {
		$file = file($filename);
		$savedcookies = unserialize($file[1]);
		unset($file);

		// Remove old cookies
		foreach ($savedcookies as $k => $v) if (time() - $v['time'] >= ($maxdays * 24 * 60 * 60)) unset($savedcookies[$k]);
	} else $savedcookies = array();
	$hash = hash('crc32b', $user.':'.$pass);
	$_secretkey = $secretkey;
	$secretkey = sha1($user.':'.$pass);
	$savedcookies[$hash] = array('time' => time(), 'enc' => urlencode(encrypt('OK')), 'cookie' => IWillNameItLater($cookie, false));
	$secretkey = $_secretkey;

	write_file($filename, "<?php exit(); ?>\r\n" . serialize($savedcookies));
}

//[06-9-2012] Written by Th3-822.
//[01-1-2013] Fixed login. - Th3-822 (Happy New Year)
//[20-1-2013] Updated for df's new domains. - Th3-822
//[25-3-2017] Switched to cURL/https, fixed domain checker & fixed login & upload. - Th3-822