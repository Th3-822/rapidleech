<?php
######## Account Info ########
$upload_acc['depositfiles_com']['user'] = ''; //Set your login
$upload_acc['depositfiles_com']['pass'] = ''; //Set your password
########################

$_GET['proxy'] = isset($_GET['proxy']) ? $_GET['proxy'] : '';
$not_done = true;
$continue_up = false;

if ($upload_acc['depositfiles_com']['user'] && $upload_acc['depositfiles_com']['pass']) {
	$_REQUEST['up_login'] = $upload_acc['depositfiles_com']['user'];
	$_REQUEST['up_pass'] = $upload_acc['depositfiles_com']['pass'];
	$_REQUEST['action'] = 'FORM';
	echo "<b><center>Using Default Login.</center></b>\n";
}

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
	$domain = 'depositfiles.com';
	$referer = "http://$domain/";

	// Login
	echo "<table style='width:600px;margin:auto;'>\n<tr><td align='center'>\n<div id='login' width='100%' align='center'>Login to $domain</div>\n";

	$cookie = array('lang_current' => 'en');
	if (!empty($_REQUEST['up_login']) && !empty($_REQUEST['up_pass'])) {
		$page = SkipLoginC(strtolower($_REQUEST['up_login']), $_REQUEST['up_pass']);
		$login = true;
	} else echo "<b><center>Login not found or empty, using non member upload.</center></b>\n";

	// Retrive upload ID
	echo "<script type='text/javascript'>document.getElementById('login').style.display='none';</script>\n<div id='info' width='100%' align='center'>Retrive upload ID</div>\n";

	if (!$login) {
		$page = geturl($domain, 80, '/', $referer, $cookie, 0, 0, $_GET['proxy'], $pauth);is_page($page);
		$cookie = GetCookiesArr($page, $cookie);
	}

	if (!preg_match('@https?://fileshare\d+\.depositfiles\.com/[\w\-]+/[^\?\'"\r\n\<>;\s\t]*@i', $page, $up)) html_error('Error: Cannot find upload server.', 0);

	$post = array();
	$post['MAX_FILE_SIZE'] = cut_str($page, 'name="MAX_FILE_SIZE" value="', '"');
	$post['UPLOAD_IDENTIFIER'] = generate_upload_id();
	$post['go'] = cut_str($page, 'name="go" value="', '"');
	$post['agree'] = '1';

	$up_url = $up[0].'?X-Progress-ID='.$post['UPLOAD_IDENTIFIER'];

	// Uploading
	echo "<script type='text/javascript'>document.getElementById('info').style.display='none';</script>\n";

	$url = parse_url($up_url);
	$upfiles = upfile($url['host'], 80, $url['path'].($url['query'] ? '?'.$url['query'] : ''), $referer, $cookie, $post, $lfile, $lname, 'files', '', $_GET['proxy'], $pauth);

	// Upload Finished
	echo "<script type='text/javascript'>document.getElementById('progressblock').style.display='none';</script>\n";

	is_page($upfiles);

	if (preg_match('@http://(?:[^/]\.)?depositfiles\.com/files/[^\'"\r\n\s\t<>;]+@i', $upfiles, $dl)) {
		$download_link = $dl[0];
		if (preg_match('@http://(?:[^/]\.)?depositfiles\.com/rmv/[^\'"\r\n\s\t<>;]+@i', $upfiles, $del)) $delete_link = $del[0];
	} else html_error('Download link not found.', 0);
}

function generate_upload_id() {
	$chars = str_split('1234567890qwertyuiopasdfghjklzxcvbnm');
	$uid = time();
	for ($i=0;$i<32;$i++) $uid .= $chars[array_rand($chars)];
	return $uid;
}

// Edited For upload.php usage.
function Show_reCaptcha($pid, $inputs, $default_login) { 
	if (!is_array($inputs)) html_error('Error parsing captcha data.');

	// Themes: 'red', 'white', 'blackglass', 'clean'
	echo "<script language='JavaScript'>var RecaptchaOptions={theme:'white', lang:'en'};</script>\n";
	echo "\n<center><form name='dl' method='post' ><br />\n";
	foreach ($inputs as $name => $input) echo "<input type='hidden' name='$name' id='$name' value='$input' />\n";
	if (!$default_login) echo "\t<table border='0' style='width:270px;' cellspacing='0' align='center'>\n\t\t<tr><td style='white-space:nowrap;'>&nbsp;Login</td><td>&nbsp;<input type='text' id='up_login' name='up_login' value='".htmlentities($_REQUEST['up_login'])."' style='width:160px;' /></td></tr>\n\t\t<tr><td style='white-space:nowrap;'>&nbsp;Password</td><td>&nbsp;<input type='password' id='up_password' name='up_pass' value='' style='width:160px;' /></td></tr>\n\t</table><br /><br />";
	echo "<script type='text/javascript' src='http://www.google.com/recaptcha/api/challenge?k=$pid'></script>";
	echo "<noscript><iframe src='http://www.google.com/recaptcha/api/noscript?k=$pid' height='300' width='500' frameborder='0'></iframe><br />";
	echo "<textarea name='recaptcha_challenge_field' rows='3' cols='40'></textarea><input type='hidden' name='recaptcha_response_field' value='manual_challenge' /></noscript><br />";
	echo "<input type='submit' name='submit' onclick='javascript:return checks();' value='Enter Captcha' />\n";
	echo "<script type='text/javascript'>/*<![CDATA[*/\n\tfunction checks() {\n\t\tif ($('#recaptcha_response_field').val() == '') {\n\t\t\twindow.alert('You didn\'t enter the image verification code.');\n\t\t\treturn false;\n\t\t} " . ($default_login ? '' : "else if ($('#up_login').val() == '' || $('#up_password').val() == '') {\n\t\t\twindow.alert('Please fill login fields.');\n\t\t\treturn false;\n\t\t} ") . "else return true;\n\t}\n/*]]>*/</script>\n";
	echo "</form></center>\n</body>\n</html>";
	exit;
}

function Login($user, $pass) {
	global $upload_acc, $cookie, $domain, $referer, $pauth;
	$captcha = (isset($_POST['step']) && $_POST['step'] == 'captcha') ? true : false;
	if ($captcha && empty($_POST['recaptcha_response_field'])) html_error('You didn\'t enter the image verification code.');

	$post = array();
	$post['go'] = '1';
	$post['login'] = $user;
	$post['password'] = $pass;
	if ($captcha) {
		$post['recaptcha_challenge_field'] = $_POST['recaptcha_challenge_field'];
		$post['recaptcha_response_field'] = $_POST['recaptcha_response_field'];
	}

	$page = geturl($domain, 80, '/login.php?return=%2F', $referer, $cookie, $post, 0, $_GET['proxy'], $pauth);is_page($page);
	if (stripos($page, 'Enter security code:') !== false) {
		if (!preg_match('@https?://(?:[^/]+\.)?(?:(?:google\.com/recaptcha/api)|(?:recaptcha\.net))/(?:(?:challenge)|(?:noscript))\?k=([\w|\-]+)@i', $page, $pid)) html_error('Error: reCAPTCHA not found.');
		if (stripos($page, 'Your password or login is incorrect') !== false) echo "<p style='color:red;text-align:center;font-weight:bold;'>Your password or login is incorrect.</p>\n";
		$data = array('action' => 'FORM', 'step' => 'captcha');
		Show_reCaptcha($pid[1], $data, ($upload_acc['depositfiles_com']['user'] && $upload_acc['depositfiles_com']['pass']));
		exit;
	}
	is_present($page, 'Your password or login is incorrect', 'Login failed: User/Password incorrect.');
	is_notpresent($page, 'Set-Cookie: autologin=', 'Login failed: Cannot find "autologin" cookie.');
	$cookie = GetCookiesArr($page, $cookie);

	SaveCookies($user, $pass);

	$page = geturl($domain, 80, '/', $referer, $cookie, 0, 0, $_GET['proxy'], $pauth);is_page($page);
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

function SkipLoginC($user, $pass) {
	global $cookie, $domain, $referer, $hash, $maxdays, $secretkey, $pauth;
	$filename = 'depositfiles_ul.php';
	$maxdays = 3; // Max days to keep cookies saved
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
		if (stripos($page, 'logout.php">logout<') === false) return Login($user, $pass);
		SaveCookies($user, $pass); // Update cookies file
		return $page;
	}
	return Login($user, $pass);
}

function SaveCookies($user, $pass) {
	global $cookie, $maxdays, $secretkey;
	$filename = 'depositfiles_ul.php';
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

//[06-9-2012] Written by Th3-822.

?>