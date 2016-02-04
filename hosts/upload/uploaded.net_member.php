<?php
######## Account Info ########
$upload_acc['uploaded_net']['user'] = ''; //Set your userid/alias
$upload_acc['uploaded_net']['pass'] = ''; //Set your password
########################

$_GET['proxy'] = isset($_GET['proxy']) ? $_GET['proxy'] : '';
$not_done = true;
$continue_up = false;

if ($upload_acc['uploaded_net']['user'] && $upload_acc['uploaded_net']['pass']) {
	$_REQUEST['up_login'] = $upload_acc['uploaded_net']['user'];
	$_REQUEST['up_pass'] = $upload_acc['uploaded_net']['pass'];
	$_REQUEST['action'] = 'FORM';
	echo "<b><center>Using Default Login.</center></b>\n";
}

if (!empty($_REQUEST['action']) && $_REQUEST['action'] == 'FORM') $continue_up = true;
else {
	echo "<table border='0' style='width:270px;' cellspacing='0' align='center'>
	<form method='POST'>
	<input type='hidden' name='action' value='FORM' />
	<tr><td style='white-space:nowrap;'>&nbsp;User*</td><td>&nbsp;<input type='text' name='up_login' value='' style='width:160px;' /></td></tr>
	<tr><td style='white-space:nowrap;'>&nbsp;Password*</td><td>&nbsp;<input type='password' name='up_pass' value='' style='width:160px;' /></td></tr>\n";
	echo "<tr><td colspan='2' align='center'><br /><input type='submit' value='Upload' /></td></tr>\n";
	echo "<tr><td colspan='2' align='center'><small>*You can set it as default in <b>".basename(__FILE__)."</b></small></td></tr>\n";
	echo "</table>\n</form>\n";
}

if ($continue_up) {
	$not_done = false;
	$referer = 'http://uploaded.net/';

	// Login
	echo "<table style='width:600px;margin:auto;'>\n<tr><td align='center'>\n<div id='login' width='100%' align='center'>Login to uploaded.net</div>\n";

	$cookie = array();
	if (!empty($_REQUEST['up_login']) && !empty($_REQUEST['up_pass'])) CookieLogin(urlencode($_REQUEST['up_login']), urlencode($_REQUEST['up_pass']));
	else html_error('Login Failed: Email or Password are empty. Please check login data.');

	// Retrive upload ID
	echo "<script type='text/javascript'>document.getElementById('login').style.display='none';</script>\n<div id='info' width='100%' align='center'>Retrive upload ID</div>\n";

	$page = geturl('uploaded.net', 80, '/', $referer, $cookie, 0, 0, $_GET['proxy'], $pauth);is_page($page);
	$js = geturl('uploaded.net', 80, '/js/script.js', $referer, $cookie, 0, 0, $_GET['proxy'], $pauth);is_page($js);

	if (!preg_match('@uploadServer = [\'|\"](https?://([^\|\'|\"|\r|\n|\s|\t]+\.)uploaded\.net/)[\'|\"]@i', $js, $up)) {
		is_present($js, "uploadServer = '';" ,'Due to high load our capacities are currently in full usage. Please try again in a few minutes.');
		html_error('Error: Cannot find upload server.');
	}

	if (!preg_match('@id="user_id" value="(\d+)"@i', $page, $uid)) html_error('Error: UserID not found.');
	if (!preg_match('@id="user_pw" value="(\w+)"@i', $page, $spass)) html_error('Error: Password hash not found.'); // $spass = array(1 => sha1($_REQUEST['up_pass']));
	$adm_link = generate();

	$post = array();
	$post['Filename'] = $lname;
	$post['Upload'] = 'Submit Query';

	$up_url = $up[1]."upload?admincode=$adm_link&id={$uid[1]}&pw={$spass[1]}&folder=0";

	// Uploading
	echo "<script type='text/javascript'>document.getElementById('info').style.display='none';</script>\n";

	$url = parse_url($up_url);
	$upfiles = upfile($url['host'], 80, $url['path'].($url['query'] ? '?'.$url['query'] : ''), 0, 0, $post, $lfile, $lname, 'Filedata', '', $_GET['proxy'], $pauth, 'Shockwave Flash');

	// Upload Finished
	echo "<script type='text/javascript'>document.getElementById('progressblock').style.display='none';</script>\n";

	is_page($upfiles);

	$content = substr($upfiles, strpos($upfiles, "\r\n\r\n") + 4);

	if (stripos($content, 'forbidden') === 0) html_error('File Forbidden (Blacklisted)');

	if (preg_match('@^(\w+)\,\d@i', $content, $fid)) {
		$download_link = 'http://uploaded.net/file/'.$fid[1]; // $download_link = 'http://ul.to/'.$fid[1];
	} else html_error('Download link not found.');

}

function generate($len = 6) {
	$pwd = '';
	$con = array('b','c','d','f','g','h','j','k','l','m','n','p','r','s','t','v','w','x','y','z');
	$voc = array('a','e','i','o','u');

	for($i = 0; $i < $len/2; $i++) {
		$c = mt_rand(0, 1000) % 20;
		$v = mt_rand(0, 1000) % 5;
		$pwd .= $con[$c] . $voc[$v];
	}

	return $pwd;
}

function Login($user, $pass) {
	global $cookie, $referer, $pauth;
	$post = array_map('urlencode', array('id' => $user, 'pw' => $pass));

	$x = 0;
	do {
		$page = geturl('uploaded.net', 80, '/io/login', $referer.($x > 0 ? 'io/login' : '')."\r\nX-Requested-With: XMLHttpRequest", $cookie, $post, 0, $_GET['proxy'], $pauth);is_page($page);
		$cookie = GetCookiesArr($page, $cookie);
		$x++;
	} while ($x < 6 && substr($page, 9, 3) == '302' && stripos($page, "\nLocation: /io/login") !== false);

	$body = trim(substr($page, strpos($page, "\r\n\r\n") + 4));
	is_present($body, 'No connection to database', 'Login failed: "No connection to database".');
	if (preg_match('@\{\"err\":\"([^\"]+)\"@i', $body, $err)) html_error('Login Error: "'.html_entity_decode(stripslashes($err[1])).'".');
	if (empty($cookie['login'])) {
		if ($body == '') html_error('The host didn\'t replied the login request, wait 15-30 seconds and try again.');
		html_error('Login Error: Cannot find "login" cookie.');
	}

	SaveCookies($user, $pass); // Update cookies file
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
	global $secretkey, $cookie, $referer, $pauth;
	if (empty($user) || empty($pass)) html_error('Login Failed: User or Password is empty.');
	$user = strtolower($user);

	if (!defined('DOWNLOAD_DIR')) {
		global $options;
		if (substr($options['download_dir'], -1) != '/') $options['download_dir'] .= '/';
		define('DOWNLOAD_DIR', (substr($options['download_dir'], 0, 6) == 'ftp://' ? '' : $options['download_dir']));
	}
	$filename = DOWNLOAD_DIR . basename('uploaded_ul.php');
	if (!file_exists($filename) || filesize($filename) <= 6) return Login($user, $pass);

	$file = file($filename);
	$savedcookies = unserialize($file[1]);
	unset($file);

	$hash = hash('crc32b', $user.':'.$pass);
	if (is_array($savedcookies) && array_key_exists($hash, $savedcookies)) {
		$_secretkey = $secretkey;
		$secretkey = hash('crc32b', $pass).sha1($user.':'.$pass).hash('crc32b', $user); // A 56 char key should be safer. :D
		$testCookie = (decrypt(urldecode($savedcookies[$hash]['enc'])) == 'OK') ? IWillNameItLater($savedcookies[$hash]['cookie']) : '';
		$secretkey = $_secretkey;
		if (empty($testCookie) || (is_array($testCookie) && count($testCookie) < 1)) return Login($user, $pass);

		$x = 0;
		do {
			$page = geturl('uploaded.net', 80, '/me', $referer.($x > 0 ? 'me' : ''), $testCookie, 0, 0, $_GET['proxy'], $pauth);is_page($page);
			$testCookie = GetCookiesArr($page, $testCookie);
			$x++;
		} while ($x < 6 && substr($page, 9, 3) == '302' && stripos($page, "\nLocation: /me") !== false);

		if (substr($page, 9, 3) != '200') return Login($user, $pass);
		$cookie = $testCookie; // Update cookies
		SaveCookies($user, $pass); // Update cookies file
		return;
	}
	return Login($user, $pass);
}

function SaveCookies($user, $pass) {
	global $secretkey, $cookie;
	$maxdays = 30; // Max days to keep cookies for more than 1 user.
	$filename = DOWNLOAD_DIR . basename('uploaded_ul.php');
	if (file_exists($filename) && filesize($filename) > 6) {
		$file = file($filename);
		$savedcookies = unserialize($file[1]);
		unset($file);

		// Remove old cookies
		if (is_array($savedcookies)) {
			foreach ($savedcookies as $k => $v) if (time() - $v['time'] >= ($maxdays * 24 * 60 * 60)) unset($savedcookies[$k]);
		} else $savedcookies = array();
	} else $savedcookies = array();
	$hash = hash('crc32b', $user.':'.$pass);
	$_secretkey = $secretkey;
	$secretkey = hash('crc32b', $pass).sha1($user.':'.$pass).hash('crc32b', $user); // A 56 char key should be safer. :D
	$savedcookies[$hash] = array('time' => time(), 'enc' => urlencode(encrypt('OK')), 'cookie' => IWillNameItLater($cookie, false));
	$secretkey = $_secretkey;

	file_put_contents($filename, "<?php exit(); ?>\r\n" . serialize($savedcookies), LOCK_EX);
}

//[26-8-2012] Written by Th3-822.
//[02-10-2012] Fixed link regexp. - Th3-822
//[13-5-2013] Added "CookieLogin" for saving user cookies. (ul.to is blocking login requests after 5-6 logins) - Th3-822
//[06-12-2015] Added support for redirects blocking site access. #6DCambiemosVzla - Th3-822

?>