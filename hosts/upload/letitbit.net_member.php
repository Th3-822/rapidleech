<?php
####### Account Info. ###########
$upload_acc['letitbit_net']['user'] = ''; //Set your email
$upload_acc['letitbit_net']['pass'] = ''; //Set your password
##############################

$_GET['proxy'] = isset($_GET['proxy']) ? $_GET['proxy'] : '';
$not_done = true;

if ($upload_acc['letitbit_net']['user'] && $upload_acc['letitbit_net']['pass']) {
	$default_acc = true;
	$_REQUEST['up_login'] = $upload_acc['letitbit_net']['user'];
	$_REQUEST['up_pass'] = $upload_acc['letitbit_net']['pass'];
	$_REQUEST['action'] = 'FORM';
	echo "<b><center>Using Default Login.</center></b>\n";
} else $default_acc = false;

if (empty($_REQUEST['action']) || $_REQUEST['action'] != 'FORM') {
	echo "<table border='0' style='width:270px;' cellspacing='0' align='center'>
	<form method='POST'>
	<input type='hidden' name='action' value='FORM' />
	<tr><td style='white-space:nowrap;'>&nbsp;Email*</td><td>&nbsp;<input type='text' name='up_login' value='' style='width:160px;' /></td></tr>
	<tr><td style='white-space:nowrap;'>&nbsp;Password*</td><td>&nbsp;<input type='password' name='up_pass' value='' style='width:160px;' /></td></tr>\n";
	echo "<tr><td colspan='2' align='center'><br /><input type='submit' value='Upload' /></td></tr>\n";
	echo "<tr><td colspan='2' align='center'><small>*You can set it as default in <b>".basename(__FILE__)."</b></small></td></tr>\n";
	echo "</table>\n</form>\n";
} else {
	$login = $not_done = false;
	$domain = 'letitbit.net';
	$referer = "http://$domain/";

	// Login
	echo "<table style='width:600px;margin:auto;'>\n<tr><td align='center'>\n<div id='login' width='100%' align='center'>Login to $domain</div>\n";

	$cookie = array('lang' => 'en');
	if (!empty($_REQUEST['up_login']) && !empty($_REQUEST['up_pass'])) {
		if (!empty($_REQUEST['A_encrypted'])) {
			$_REQUEST['up_login'] = decrypt(urldecode($_REQUEST['up_login']));
			$_REQUEST['up_pass'] = decrypt(urldecode($_REQUEST['up_pass']));
			unset($_REQUEST['A_encrypted']);
		}
		CookieLogin(strtolower($_REQUEST['up_login']), $_REQUEST['up_pass']);
		$login = true;
	} else html_error('Login failed: User/Password empty.');

	// Retrive upload ID
	echo "<script type='text/javascript'>document.getElementById('login').style.display='none';</script>\n<div id='info' width='100%' align='center'>Preparing upload</div>\n";

	$page = geturl($domain, 80, '/', $referer, $cookie, 0, 0, $_GET['proxy'], $pauth);
	is_page($page);

	if (!preg_match("@\.server\s*=\s*'([^\']+)'\s*;@i",$page, $up)) html_error('Error: Cannot find upload server.');
	if (!($props = cut_str($page, '.props = {', '}'))) html_error('Error: Cannot find upload data.');

	function rndStr($lg, $num = false) {
		if ($num) $str = "0123456789";
		else {
			$str = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
			$str .= strtolower($str)."0123456789";
		}
		$str = str_split($str);
		$ret = '';
		for ($i = 1; $i <= $lg; $i++) $ret .= $str[array_rand($str)];
		return $ret;
	}

	$UID = strtoupper(base_convert(time().rndStr(3, true),10,16)).'_'.rndStr(40);

	$post = array();
	//$post['MAX_FILE_SIZE'] = cut_str($page, 'name="MAX_FILE_SIZE" value="', '"');
	$post['owner'] = cut_str($props, "username:'", "'");
	$post['pin'] = cut_str($props, "pin:'", "'");
	$post['base'] = cut_str($props, "base:'", "'");
	$post['host'] = cut_str($props, "host:'", "'");
	$post['source'] = cut_str($props, "source:'", "'");

	$up_url = "http://{$up[1]}/marker=$UID";
	// Uploading
	echo "<script type='text/javascript'>document.getElementById('info').style.display='none';</script>\n";

	$url = parse_url($up_url);
	$upfiles = upfile($url['host'], defport($url), $url['path'].(!empty($url['query']) ? '?'.$url['query'] : ''), 0, 0, $post, $lfile, $lname, 'file0', '', $_GET['proxy'], $pauth);

	// Upload Finished
	echo "<script type='text/javascript'>document.getElementById('progressblock').style.display='none';</script>\n";

	is_page($upfiles);

	if (preg_match('@"uids"\s*:\s*\["([\w\.\-]+)"\]@i', $upfiles, $uid)) {
		$download_link = 'http://letitbit.net/download/'.$uid[1].'/'.str_replace(array(' ', '?'), '_', $lname).'.html';
	} else {
		html_error("Error: Download link not found.");
	}
}

function Login($user, $pass) {
	global $cookie, $domain, $referer, $pauth, $default_acc;
	$post = array();
	$post['act'] = 'login';
	$post['login'] = urlencode($user);
	$post['password'] = urlencode($pass);

	$page = geturl($domain, 80, '/ajax/auth.php', $referer, 'lang=en', $post, 0, $_GET['proxy'], $pauth);is_page($page);
	is_present($page, 'Authorization data is invalid', 'Login failed: User/Password incorrect.');
	is_notpresent($page, 'Set-Cookie: log=', 'Login failed: Cannot find login cookie.');
	is_notpresent($page, 'Set-Cookie: pas=', 'Login failed: Cannot find paswword cookie.');
	$cookie = GetCookiesArr($page);
	$cookie['lang'] = 'en';

	SaveCookies($user, $pass);
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

	$filename = 'letitbit_ul.php';
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

		$page = geturl($domain, 80, '/', $referer, $testCookie, 0, 0, $_GET['proxy'], $pauth);is_page($page);
		if (stripos($page, 'title="Logout">Logout</a>') === false) return Login($user, $pass);
		$GLOBALS['cookie'] = GetCookiesArr($page, $testCookie); // Update cookies
		SaveCookies($user, $pass); // Update cookies file
		return true;
	}
	return Login($user, $pass);
}

function SaveCookies($user, $pass) {
	global $secretkey;
	$maxdays = 31; // Max days to keep cookies saved

	$filename = 'letitbit_ul.php';
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

//[01-1-2012] Written by Th3-822. // Happy New Year!
//[16-2-2012] Added functions for save encrypted cookies in a file (Works with more than 1 login) and for keep the cookies saved for 3 days since the last login (can be changed). - Th3-822
//[09-10-2014] Updated & Fixed upload. - Th3-822

?>