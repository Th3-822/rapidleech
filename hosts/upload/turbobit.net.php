<?php
######## Account Info ########
$upload_acc['turbobit_net']['user'] = ''; //Set your login
$upload_acc['turbobit_net']['pass'] = ''; //Set your password
########################

$_GET['proxy'] = isset($_GET['proxy']) ? $_GET['proxy'] : '';
$not_done = true;
$continue_up = false;

if (!empty($upload_acc['turbobit_net']['user']) && !empty($upload_acc['turbobit_net']['pass'])) {
	$default_acc = true;
	$_REQUEST['up_login'] = $upload_acc['turbobit_net']['user'];
	$_REQUEST['up_pass'] = $upload_acc['turbobit_net']['pass'];
	$_REQUEST['action'] = 'FORM';
	echo "<b><center>Using Default Login.</center></b>\n";
} else $default_acc = false;

if (!empty($_REQUEST['action']) && $_REQUEST['action'] == 'FORM') $continue_up = true;
else {
	echo "<table border='0' style='width:270px;' cellspacing='0' align='center'>
	<form method='POST'>
	<input type='hidden' name='action' value='FORM' />
	<tr><td style='white-space:nowrap;'>&nbsp;Email*</td><td>&nbsp;<input type='text' name='up_login' value='' style='width:160px;' /></td></tr>
	<tr><td style='white-space:nowrap;'>&nbsp;Password*</td><td>&nbsp;<input type='password' name='up_pass' value='' style='width:160px;' /></td></tr>\n";
	echo "<tr><td colspan='2' align='center'><br /><input type='submit' value='Upload' /></td></tr>\n";
	echo "<tr><td colspan='2' align='center'><small>*You can set it as default in <b>".basename(__FILE__)."</b></small></td></tr>\n";
	echo "</table>\n</form>\n";
}

if ($continue_up) {
	$login = $not_done = false;
	$domain = 'turbobit.net';
	$referer = "https://$domain/";

	// Login
	echo "<table style='width:600px;margin:auto;'>\n<tr><td align='center'>\n<div id='login' width='100%' align='center'>Login to $domain</div>\n";

	$cookie = array('user_lang' => 'en');
	if (!empty($_REQUEST['up_login']) && !empty($_REQUEST['up_pass'])) {
		if (!$default_acc && !empty($_POST['up_encrypted']) && $_POST['up_encrypted'] == 'true') {
			$_REQUEST['up_login'] = decrypt(urldecode($_REQUEST['up_login']));
			$_REQUEST['up_pass'] = decrypt(urldecode($_REQUEST['up_pass']));
		}
		$page = CookieLogin(strtolower($_REQUEST['up_login']), $_REQUEST['up_pass']);
		$login = true;
	} else {
		echo "<b><center>Login not found or empty, using non member upload.</center></b>\n";
		if ($fsize > 209715200) html_error('File is too big for anon upload'); // 200 Mib
	}

	// Retrive upload ID
	echo "<script type='text/javascript'>document.getElementById('login').style.display='none';</script>\n<div id='info' width='100%' align='center'>Retrive upload ID</div>\n";

	if (!$login) {
		$page = geturl($domain, 443, '/', $referer, $cookie, 0, 0, $_GET['proxy'], $pauth, 0, 'https');is_page($page);
	}

	if (!preg_match('@https?://s\d+\.turbobit\.net/uploadfile@i', $page, $up_url)) html_error('Error: Upload URL not found.');

	$post = array();
	$post['Filename'] = $lname;
	if ($login) {
		$post['user_id'] = cut_str($page, 'name="user_id" value="', '"');
		if (empty($post['user_id'])) html_error('Error: UserID not found.');
	}
	$post['apptype'] = cut_str($page, 'name="apptype" value="', '"');

	$up_url = $up_url[0];

	// Uploading
	echo "<script type='text/javascript'>document.getElementById('info').style.display='none';</script>\n";

	$url = parse_url($up_url);
	$upfiles = upfile($url['host'], defport($url), $url['path'].(!empty($url['query']) ? '?'.$url['query'] : ''), 0, 0, $post, $lfile, $lname, 'Filedata', '', $_GET['proxy'], $pauth, 0, $url['scheme']);

	// Upload Finished
	echo "<script type='text/javascript'>document.getElementById('progressblock').style.display='none';</script>\n";

	is_page($upfiles);

	$json = json2array($upfiles);
	if ((empty($json['result']) || $json['result'] != 'true') || empty($json['id'])) html_error('Upload error: "'.htmlentities($json['message']).'"');
	$id = is_array($json['id']) ? $json['id']['fid'] : $json['id'];

	$page = geturl($domain, 443, '/newfile/gridFile/'.$id, $referer."newfile/edit/\r\nX-Requested-With: XMLHttpRequest", $cookie, 0, 0, $_GET['proxy'], $pauth, 0, 'https');is_page($page);
	$json = json2array($page);
	$info = reset($json['rows']);

	$download_link = "$referer$id.html";
	if (!empty($info['cell'][7])) $delete_link = $referer."delete/file/$id/".$info['cell'][7];
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
	global $cookie, $domain, $referer, $pauth;
	$post = array();
	$post['user%5Blogin%5D'] = urlencode($user);
	$post['user%5Bpass%5D'] = urlencode($pass);
	$post['user%5Bmemory%5D'] = 'on';
	$post['user%5Bsubmit%5D'] = 'Login';

	$page = geturl($domain, 443, '/user/login', $referer.'login', $cookie, $post, 0, $_GET['proxy'], $pauth, 0, 'https');is_page($page);
	$cookie = GetCookiesArr($page, $cookie);

	$x = 0;
	while ($x < 3 && stripos($page, "\nLocation: ") !== false && preg_match('@\nLocation: ((https?://[^/\r\n]+)?/[^\r\n]*)@i', $page, $redir)) {
		$redir = (empty($redir[2])) ? 'https://turbobit.net'.$redir[1] : $redir[1];
		$url = parse_url($redir);
		$page = geturl($url['host'], defport($url), $url['path'].(!empty($url['query']) ? '?'.$url['query'] : ''), $referer, $cookie, 0, 0, $_GET['proxy'], $pauth, 0, $url['scheme']);is_page($page);
		$cookie = GetCookiesArr($page, $cookie);
		$x++;
	}
	if ($x < 1) html_error('Login Redirect not Found');

	is_present($page, 'Incorrect login or password', 'Login Failed: Login/Password incorrect');
	is_present($page, 'E-Mail address appears to be invalid.', 'Login Failed: Invalid E-Mail');
	// is_present($page, 'Limit of login attempts exceeded for your account. It has been temporarily locked.', 'Login Failed: Account Temporally Locked');

	is_present($page, 'Please enter the captcha code.', 'CAPTCHA not supported.');
	if (empty($cookie['user_isloggedin'])) html_error('Login Error: Cookie "user_isloggedin" not found or empty.');
	is_notpresent($page, '/user/logout', 'Login Failed.');

	SaveCookies($user, $pass); // Update cookies file
	return $page;
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
	if (!defined('DOWNLOAD_DIR')) {
		global $options;
		if (substr($options['download_dir'], -1) != '/') $options['download_dir'] .= '/';
		define('DOWNLOAD_DIR', (substr($options['download_dir'], 0, 6) == 'ftp://' ? '' : $options['download_dir']));
	}

	$filename = DOWNLOAD_DIR.basename('turbobit_ul.php');
	if (!file_exists($filename)) return Login($user, $pass);

	$file = file($filename);
	$savedcookies = unserialize($file[1]);
	unset($file);

	$hash = hash('crc32b', $user.':'.$pass);
	if (array_key_exists($hash, $savedcookies)) {
		$_secretkey = $secretkey;
		$secretkey = sha1($user.':'.$pass);
		$cookie = (decrypt(urldecode($savedcookies[$hash]['enc'])) == 'OK') ? IWillNameItLater($savedcookies[$hash]['cookie']) : '';
		$secretkey = $_secretkey;
		if ((is_array($cookie) && count($cookie) < 1) || empty($cookie)) return Login($user, $pass);

		$page = geturl($domain, 443, '/', $referer, $cookie, 0, 0, $_GET['proxy'], $pauth, 0, 'https');is_page($page);
		if (stripos($page, '/user/logout') === false) return Login($user, $pass);
		SaveCookies($user, $pass); // Update cookies file
		return $page;
	}
	return Login($user, $pass);
}

function SaveCookies($user, $pass) {
	global $cookie, $secretkey;
	$maxdays = 31; // Max days to keep cookies saved
	$filename = DOWNLOAD_DIR.basename('turbobit_ul.php');
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

//[11-1-2013] Written by Th3-822.
//[07-6-2013] Added login recaptcha support. - Th3-822
//[22-8-2013] Fixed for changes at upload page. - Th3-822
//[06-9-2013] Fixed get fileid. - Th3-822
//[12-2-2017] Removed CAPTCHA (Unsupported ATM) and Fixed Login (There were Huges Typos on It). - Th3-822