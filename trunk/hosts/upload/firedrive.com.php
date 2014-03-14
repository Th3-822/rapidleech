<?php
######## Account Info ########
$upload_acc['firedrive_com']['user'] = ''; //Set your login
$upload_acc['firedrive_com']['pass'] = ''; //Set your password
########################

$_GET['proxy'] = isset($_GET['proxy']) ? $_GET['proxy'] : '';
$not_done = true;

if ($upload_acc['firedrive_com']['user'] && $upload_acc['firedrive_com']['pass']) {
	$_REQUEST['up_login'] = $upload_acc['firedrive_com']['user'];
	$_REQUEST['up_pass'] = $upload_acc['firedrive_com']['pass'];
	$_REQUEST['action'] = 'FORM';
	echo "<b><center>Using Default Login.</center></b>\n";
}

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
	$domain = 'www.firedrive.com';
	$referer = "http://$domain/";

	// Login
	echo "<table style='width:600px;margin:auto;'>\n<tr><td align='center'>\n<div id='login' width='100%' align='center'>Login to $domain</div>\n";

	$cookie = array();
	if (!empty($_REQUEST['up_login']) && !empty($_REQUEST['up_pass'])) {
		CookieLogin($_REQUEST['up_login'], $_REQUEST['up_pass']);
		$login = true;
	} else echo "<b><center>Login not found or empty, using non member upload.</center></b>\n";

	// Retrive upload ID
	echo "<script type='text/javascript'>document.getElementById('login').style.display='none';</script>\n<div id='info' width='100%' align='center'>Retriving upload ID</div>\n";

	if (!$login) {
		$page = geturl($domain, 80, '/', $referer, $cookie, 0, 0, $_GET['proxy'], $pauth);is_page($page);
		$cookie = GetCookiesArr($page, $cookie);
	}

	$page = geturl($domain, 80, '/upload?_='.jstime(), $referer, $cookie, 0, 0, $_GET['proxy'], $pauth);is_page($page);
	$cookie = GetCookiesArr($page, $cookie);
	if (stripos($page, "\nLocation: https://www.firedrive.com/") !== false) {
		$cookie = GetCookiesArr($page, $cookie);
		$page = geturl($domain, 0, '/upload?_='.jstime(), $referer, $cookie, 0, 0, 0, 0, 0, 'https');is_page($page);
	}

	if (!preg_match('@https?://upload\d*\.firedrive\.com/[^\r\n\t\'\"<>]+@i', $page, $up)) html_error('Error: Cannot find upload server.');
	if (!preg_match('@function\s+getUploadVars\s*\(\s*\)\s*{\s*return\s+\'([^\']+)\'@i', $page, $ah)) html_error('Error: Upload hash not found.');

	$post = array();
	$post['Filename'] = $lname;
	$post['vars'] = $ah[1];
	$post['target_folder'] = '0';
	$post['target_group'] = '0';

	$up_url = $up[0];

	// Uploading
	echo "<script type='text/javascript'>document.getElementById('info').style.display='none';</script>\n";

	$url = parse_url($up_url);
	$upfiles = upfile($url['host'], defport($url), $url['path'].(!empty($url['query']) ? '?'.$url['query'] : ''), 0, 0, $post, $lfile, $lname, 'file', '', 0, 0, 0, $url['scheme']);

	// Upload Finished
	echo "<script type='text/javascript'>document.getElementById('progressblock').style.display='none';</script>\n";

	is_page($upfiles);

	if (!preg_match('@"id"\s*:\s*"(\w+)"@i', $upfiles, $lnk)) html_error('Download link not found.', 0);
	$download_link = 'http://www.firedrive.com/file/'.$lnk[1];
}

function Login($user, $pass) {
	global $upload_acc, $cookie, $domain, $referer, $pauth;

	$post = array();
	$post['user'] = urlencode($user);
	$post['pass'] = urlencode($pass);
	$post['json'] = $post['remember'] = 1;

	$page = geturl('auth.firedrive.com', 0, '/', $referer.'authenticate.php?login', $cookie, $post, 0, 0, 0, 0, 'https');is_page($page);
	$cookie = GetCookiesArr($page, $cookie);

	is_present($page, '"status":0', 'Login Failed: Email/Password incorrect.');
	if (empty($cookie['auth'])) html_error('Login Error: Cannot find "auth" cookie.');

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
	global $cookie, $domain, $referer, $secretkey, $pauth;
	if (empty($user) || empty($pass)) html_error('Login Failed: User or Password is empty.');
	$user = strtolower($user);

	$filename = 'firedrive_ul.php';
	if (!defined('DOWNLOAD_DIR')) {
		global $options;
		if (substr($options['download_dir'], -1) != '/') $options['download_dir'] .= '/';
		define('DOWNLOAD_DIR', (substr($options['download_dir'], 0, 6) == 'ftp://' ? '' : $options['download_dir']));
	}
	$filename = DOWNLOAD_DIR.basename($filename);
	if (!file_exists($filename)) return Login($user, $pass);

	$file = file($filename);
	$savedcookies = unserialize($file[1]);
	unset($file);

	$hash = hash('crc32b', $user.':'.$pass);
	if (array_key_exists($hash, $savedcookies)) {
		$_secretkey = $secretkey;
		$secretkey = hash('crc32b', $pass).sha1($user.':'.$pass).hash('crc32b', $user); // A 56 char key should be safer. :D
		$cookie = (decrypt(urldecode($savedcookies[$hash]['enc'])) == 'OK') ? IWillNameItLater($savedcookies[$hash]['cookie']) : '';
		$secretkey = $_secretkey;
		if ((is_array($cookie) && count($cookie) < 1) || empty($cookie)) return Login($user, $pass);

		$page = geturl($domain, 80, '/myfiles', $referer, $cookie, 0, 0, $_GET['proxy'], $pauth);is_page($page);
		if (stripos($page, "\nLocation: https://www.firedrive.com/") !== false) {
			$cookie = GetCookiesArr($page, $cookie);
			$page = geturl($domain, 0, '/myfiles', $referer, $cookie, 0, 0, 0, 0, 0, 'https');is_page($page);
		}
		if (stripos($page, '>Sign Out</a>') === false) return Login($user, $pass);
		$cookie = GetCookiesArr($page, $cookie); // Update cookies
		SaveCookies($user, $pass); // Update cookies file
		return true;
	}
	return Login($user, $pass);
}

function SaveCookies($user, $pass) {
	global $cookie, $secretkey;
	$maxdays = 31; // Max days to keep cookies saved

	$filename = 'firedrive_ul.php';
	$filename = DOWNLOAD_DIR.basename($filename);
	if (file_exists($filename)) {
		$file = file($filename);
		$savedcookies = unserialize($file[1]);
		unset($file);

		// Remove old cookies
		foreach ($savedcookies as $k => $v) if (time() - $v['time'] >= ($maxdays * 24 * 60 * 60)) unset($savedcookies[$k]);
	} else $savedcookies = array();
	$hash = hash('crc32b', $user.':'.$pass);
	$_secretkey = $secretkey;
	$secretkey = hash('crc32b', $pass).sha1($user.':'.$pass).hash('crc32b', $user); // A 56 char key should be safer. :D
	$savedcookies[$hash] = array('time' => time(), 'enc' => urlencode(encrypt('OK')), 'cookie' => IWillNameItLater($cookie, false));
	$secretkey = $_secretkey;

	write_file($filename, "<?php exit(); ?>\r\n" . serialize($savedcookies));
}

//[14-2-2014]  Re-Written by Th3-822.
//[20-2-2014]  Fixed Link Regexp. - Th3-822

?>