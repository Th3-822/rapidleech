<?php
######## Account Info ########
$upload_acc['putlocker_com']['user'] = ''; //Set your login
$upload_acc['putlocker_com']['pass'] = ''; //Set your password
########################

$_GET['proxy'] = isset($_GET['proxy']) ? $_GET['proxy'] : '';
$not_done = true;

if ($upload_acc['putlocker_com']['user'] && $upload_acc['putlocker_com']['pass']) {
	$_REQUEST['up_login'] = $upload_acc['putlocker_com']['user'];
	$_REQUEST['up_pass'] = $upload_acc['putlocker_com']['pass'];
	$_REQUEST['up_convert'] = 'no';
	$_REQUEST['action'] = 'FORM';
	echo "<b><center>Using Default Login.</center></b>\n";
}

if (empty($_REQUEST['action']) || $_REQUEST['action'] != 'FORM') {
	echo "<table border='0' style='width:270px;' cellspacing='0' align='center'>
	<form method='POST'>
	<input type='hidden' name='action' value='FORM' />
	<tr><td style='white-space:nowrap;'>&nbsp;Login*</td><td>&nbsp;<input type='text' name='up_login' value='' style='width:160px;' /></td></tr>
	<tr><td style='white-space:nowrap;'>&nbsp;Password*</td><td>&nbsp;<input type='password' name='up_pass' value='' style='width:160px;' /></td></tr>\n";
	echo "<tr><td colspan='2' align='center'><br />Upload options *<br /></td></tr>\r\n<tr><td colspan='2' align='center'><input type='checkbox' name='up_convert' value='yes' />&nbsp; Convert for Streaming</td></tr>";
	echo "<tr><td colspan='2' align='center'><br /><input type='submit' value='Upload' /></td></tr>\n";
	echo "<tr><td colspan='2' align='center'><small>*You can set it as default in <b>".basename(__FILE__)."</b></small></td></tr>\n";
	echo "</table>\n</form>\n";
} else {
	$login = $not_done = false;
	$domain = 'www.putlocker.com';
	$referer = "http://$domain/";

	// Login
	echo "<table style='width:600px;margin:auto;'>\n<tr><td align='center'>\n<div id='login' width='100%' align='center'>Login to $domain</div>\n";

	$cookie = array();
	if (!empty($_REQUEST['up_login']) && !empty($_REQUEST['up_pass'])) {
		CookieLogin(strtolower($_REQUEST['up_login']), $_REQUEST['up_pass']);
		$login = true;
	} else echo "<b><center>Login not found or empty, using non member upload.</center></b>\n";

	// Retrive upload ID
	echo "<script type='text/javascript'>document.getElementById('login').style.display='none';</script>\n<div id='info' width='100%' align='center'>Retriving upload ID</div>\n";

	if (!$login) {
		$page = geturl($domain, 80, '/', $referer, $cookie, 0, 0, $_GET['proxy'], $pauth);is_page($page);
		$cookie = GetCookiesArr($page, $cookie);
	}

	$page = geturl($domain, 80, '/upload_form.php', $referer, $cookie, 0, 0, $_GET['proxy'], $pauth);is_page($page);
	$cookie = GetCookiesArr($page, $cookie);

	if (!preg_match('@http://upload\d+\.putlocker\.com/[^\r\n\t\'\"<>]+@i', $page, $up)) html_error('Error: Cannot find upload server.');
	if (!preg_match('@\'auth_hash\'[\s\t]*:[\s\t]*\'([^\']+)\'@i', $page, $ah)) html_error('Error: Upload hash not found.');

	$post = array();
	$post['Filename'] = $lname;
	$post['folder'] = '/';
	$post['session'] = $cookie['PHPSESSID'];
	$post['auth_hash'] = $ah[1];
	$post['fileext'] = '*';
	if (!empty($_REQUEST['up_convert']) && $_REQUEST['up_convert'] == 'yes') $post['do_convert'] = '1';
	$post['Upload'] = 'Submit Query';

	$up_url = $up[0];

	// Uploading
	echo "<script type='text/javascript'>document.getElementById('info').style.display='none';</script>\n";

	$url = parse_url($up_url);
	$upfiles = upfile($url['host'], defport($url), $url['path'].(!empty($url['query']) ? '?'.$url['query'] : ''), 0, 0, $post, $lfile, $lname, 'Filedata', '', $_GET['proxy'], $pauth, 'Shockwave Flash');

	// Upload Finished
	echo "<script type='text/javascript'>document.getElementById('progressblock').style.display='none';</script>\n";

	is_page($upfiles);

	$page = geturl($domain, 80, '/upload_form.php?done='.$cookie['upload_hash'], $referer.'upload_form.php', $cookie, 0, 0, $_GET['proxy'], $pauth);is_page($page);
	$cookie = GetCookiesArr($page, $cookie);

	$page = geturl($domain, 80, '/cp.php?uploaded='.$cookie['upload_hash'], $referer.'upload_form.php?done='.$cookie['upload_hash'], $cookie, 0, 0, $_GET['proxy'], $pauth);is_page($page);
	$cookie = GetCookiesArr($page, $cookie);

	if (!preg_match('@https?://(?:www\.)?putlocker\.com/file/[^<>\"\'\r\n\t]+@i', $page, $lnk)) html_error('Download link not found.', 0);
	$download_link = $lnk[0];
}


// Edited For upload.php usage.
function EnterCaptcha($captchaImg, $inputs, $default_login, $captchaSize = '5') {
	echo "\n<center><form name='dl' method='POST'>\n";
	foreach ($inputs as $name => $input) echo "\t<input type='hidden' name='$name' id='$name' value='$input' />\n";
	if (!$default_login) echo "\t<table border='0' style='width:270px;' cellspacing='0' align='center'>\n\t\t<tr><td style='white-space:nowrap;'>&nbsp;Login</td><td>&nbsp;<input type='text' id='up_login' name='up_login' value='".htmlentities($_REQUEST['up_login'])."' style='width:160px;' /></td></tr>\n\t\t<tr><td style='white-space:nowrap;'>&nbsp;Password</td><td>&nbsp;<input type='password' id='up_password' name='up_pass' value='' style='width:160px;' /></td></tr>\n\t</table><br /><br />";
	echo "\t<h4>" . lang(301) . " <img alt='CAPTCHA Image' src='$captchaImg' /> " . lang(302) . ": <input type='text' name='captcha' size='$captchaSize' />&nbsp;&nbsp;\n\t\t<input type='submit' onclick='return check();' value='Enter Captcha' />\n\t</h4>\n";
	echo "<script type='text/javascript'>\n\tfunction check() {\n\t\tvar captcha=document.dl.captcha.value;\n\t\tif (captcha == '') {\n\t\t\twindow.alert('You didn\'t enter the image verification code');\n\t\t\treturn false;\n\t\t} ".($default_login ? '' : "else if ($('#up_login').val() == '' || $('#up_password').val() == '') {\n\t\t\twindow.alert('Please fill login fields.');\n\t\t\treturn false;\n\t\t} ") . "else return true;\n\t}\n</script>";
	echo "</form></center>\n</body\n</html>";
}

function Login($user, $pass) {
	global $upload_acc, $cookie, $domain, $referer, $pauth;
	if (!empty($_POST['step']) && $_POST['step'] == '1') {
		if (empty($_POST['captcha'])) html_error('You didn\'t enter the image verification code.');
		$cookie = StrToCookies(decrypt(urldecode($_POST['cookie'])));

		$post = array();
		$post['user'] = urlencode($user);
		$post['pass'] = urlencode($pass);
		$post['captcha_code'] = urlencode($_POST['captcha']);
		$post['remember'] = 1;
		$post['login_submit'] = 'Login';

		$page = geturl($domain, 80, '/authenticate.php?login', $referer.'authenticate.php?login', $cookie, $post, 0, $_GET['proxy'], $pauth);is_page($page);
		$cookie = GetCookiesArr($page, $cookie);

		if (stripos(substr($page, 0, strpos($page, "\r\n\r\n")), "\nLocation: ") !== false && preg_match('@\nLocation: ((https?://[^/\r\n]+)?/authenticate\.php[^\r\n]*)@i', substr($page, 0, strpos($page, "\r\n\r\n")), $redir)) {
			$url = parse_url((empty($redir[2]) ? 'http://www.putlocker.com'.$redir[1] : $redir[1]));
			$page = geturl($url['host'], defport($url), $url['path'].(!empty($url['query']) ? '?'.$url['query'] : ''), $referer.'authenticate.php?login', $cookie, 0, 0, $_GET['proxy'], $pauth);is_page($page);
			$cookie = GetCookiesArr($page, $cookie);
		}

		is_present($page, 'No such username or wrong password', 'Login Failed: Email/Password incorrect.');
		is_present($page, 'Please re-enter the captcha code', 'Login Failed: Wrong CAPTCHA entered.');

		if (empty($cookie['auth'])) html_error('Login Error: Cannot find "auth" cookie.');

		SaveCookies($user, $pass); // Update cookies file
		return true;
	} else {
		$page = geturl($domain, 80, '/authenticate.php?login', $referer.'authenticate.php?login', $cookie, 0, 0, $_GET['proxy'], $pauth);is_page($page);
		$cookie = GetCookiesArr($page, $cookie);

		if (!preg_match('@(https?://[^/\r\n\t\s\'\"<>]+)?/include/captcha\.php\?[^/\r\n\t\s\'\"<>]+@i', $page, $imgurl)) html_error('CAPTCHA not found.');
		$imgurl = (empty($imgurl[1])) ? 'http://www.putlocker.com'.$imgurl[0] : $imgurl[0];
		$imgurl = html_entity_decode($imgurl);

		//Download captcha img.
		$url = parse_url($imgurl);
		$page = geturl($url['host'], defport($url), $url['path'].(!empty($url['query']) ? '?'.$url['query'] : ''), $referer.'authenticate.php?login', $cookie, 0, 0, $_GET['proxy'], $pauth);is_page($page);
		$capt_img = substr($page, strpos($page, "\r\n\r\n") + 4);
		$imgfile = DOWNLOAD_DIR . 'putlocker_captcha.png';

		if (file_exists($imgfile)) unlink($imgfile);
		if (!write_file($imgfile, $capt_img)) html_error('Error getting CAPTCHA image.');

		$data = array();
		$data['action'] = 'FORM';
		$data['step'] = 1;
		$data['cookie'] = urlencode(encrypt(CookiesToStr($cookie)));
		$data['up_convert'] = ((!empty($_REQUEST['up_convert']) && $_REQUEST['up_convert'] == 'yes') ? 'yes' : 'no');
		EnterCaptcha($imgfile.'?'.time(), $data, ($upload_acc['putlocker_com']['user'] && $upload_acc['putlocker_com']['pass']));
		exit;
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

function CookieLogin($user, $pass) {
	global $cookie, $domain, $referer, $hash, $maxdays, $secretkey, $pauth;
	$filename = 'putlocker_ul.php';
	$maxdays = 7; // Max days to keep cookies saved
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
		if (time() - $savedcookies[$hash]['time'] >= ($maxdays * 24 * 60 * 60)) return Login($user, $pass); // Ignore old cookies
		$_secretkey = $secretkey;
		$secretkey = sha1($user.':'.$pass);
		$cookie = (decrypt(urldecode($savedcookies[$hash]['enc'])) == 'OK') ? IWillNameItLater($savedcookies[$hash]['cookie']) : '';
		$secretkey = $_secretkey;
		if ((is_array($cookie) && count($cookie) < 1) || empty($cookie)) return Login($user, $pass);

		$page = geturl($domain, 80, '/', $referer, $cookie, 0, 0, $_GET['proxy'], $pauth);is_page($page);
		if (stripos($page, '>Sign Out</a>') === false) return Login($user, $pass);
		$cookie = GetCookiesArr($page, $cookie); // Update cookies
		SaveCookies($user, $pass); // Update cookies file
		return true;
	}
	return Login($user, $pass);
}

function SaveCookies($user, $pass) {
	global $cookie, $maxdays, $secretkey;
	$filename = 'putlocker_ul.php';
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
	$secretkey = sha1($user.':'.$pass);
	$savedcookies[$hash] = array('time' => time(), 'enc' => urlencode(encrypt('OK')), 'cookie' => IWillNameItLater($cookie, false));
	$secretkey = $_secretkey;

	write_file($filename, "<?php exit(); ?>\r\n" . serialize($savedcookies));
}

//[16-11-2012] Written by Th3-822.
// This one doesn't use the Putlocker API and supports non member upload. - Th3-822

?>