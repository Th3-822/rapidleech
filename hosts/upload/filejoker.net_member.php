<?php
######## Account Info ########
$upload_acc['filejoker_net']['user'] = ''; //Set your login
$upload_acc['filejoker_net']['pass'] = ''; //Set your password
########################

$_GET['proxy'] = isset($_GET['proxy']) ? $_GET['proxy'] : '';
$not_done = true;

if ($upload_acc['filejoker_net']['user'] && $upload_acc['filejoker_net']['pass']) {
	$default_acc = true;
	$_REQUEST['up_login'] = $upload_acc['filejoker_net']['user'];
	$_REQUEST['up_pass'] = $upload_acc['filejoker_net']['pass'];
	$_REQUEST['action'] = 'FORM';
	echo "<b><center>Using Default Login.</center></b>\n";
} else $default_acc = false;

if (empty($_REQUEST['action']) || $_REQUEST['action'] != 'FORM') {
	echo "<table border='0' style='width:270px;' cellspacing='0' align='center'>
	<form method='POST'>
	<input type='hidden' name='action' value='FORM' />
	<tr><td style='white-space:nowrap;'>&nbsp;EMail*</td><td>&nbsp;<input type='text' name='up_login' value='' style='width:160px;' /></td></tr>
	<tr><td style='white-space:nowrap;'>&nbsp;Password*</td><td>&nbsp;<input type='password' name='up_pass' value='' style='width:160px;' /></td></tr>\n";
	echo "<tr><td colspan='2' align='center'><br /><input type='submit' value='Upload' /></td></tr>\n";
	echo "<tr><td colspan='2' align='center'><small>*You can set it as default in <b>".basename(__FILE__)."</b></small></td></tr>\n";
	echo "</table>\n</form>\n";
} else {
	$login = $not_done = false;
	$scheme = 'https';
	$domain = 'filejoker.net';
	$referer = "$scheme://$domain/";

	// Login
	echo "<table style='width:600px;margin:auto;'>\n<tr><td align='center'>\n<div id='login' width='100%' align='center'>Login to $domain</div>\n";

	$cookie = array('lang' => 'english');
	if (!empty($_REQUEST['up_login']) && !empty($_REQUEST['up_pass'])) {
		if (!empty($_REQUEST['A_encrypted'])) {
			$_REQUEST['up_login'] = decrypt(urldecode($_REQUEST['up_login']));
			$_REQUEST['up_pass'] = decrypt(urldecode($_REQUEST['up_pass']));
			unset($_REQUEST['A_encrypted']);
		}
		CookieLogin($_REQUEST['up_login'], $_REQUEST['up_pass']);
		$login = true;
	} else html_error('Login failed: Email/Password empty.');

	// Retrive upload ID
	echo "<script type='text/javascript'>document.getElementById('login').style.display='none';</script>\n<div id='info' width='100%' align='center'>Retrieving upload ID</div>\n";

	$page = geturl($domain, 443, '/', $referer, $cookie, '', 0, $_GET['proxy'], $pauth, 0, $scheme);is_page($page);
	if (!preg_match('@action=["\']((https?://(?:[\w\-]+\.)+[\w\-]+(?:\:\d+)?)?/upload/\d+)["\']@i', $page, $up_url)) html_error('Error: Cannot find upload server.');
	$up_url = (empty($up_url[2])) ? $scheme.'://'.$_T8['domain'].$up_url[1].'/' : $up_url[1].'/';

	$post = array();
	$post['upload_type'] = 'file';
	$post['sess_id'] = !empty($cookie['xfss']) ? $cookie['xfss'] : cut_str($page, 'name="sess_id" value="', '"');
	foreach (array('srv_tmp_url', 'utype', 'srv_id', 'disk_id') as $tmpName) {
		if (stripos($page, "name=\"$tmpName\" value=\"") !== false && ($tmp = cut_str($page, "name=\"$tmpName\" value=\"", '"'))) $post["$tmpName"] = $tmp;
	}
	$post['link_pass'] = $post['link_rcpt'] = '';
	$post['file_descr'] = 'Uploaded with Rapidleech.';
	$post['file_public'] = '1';
	$post['tos'] = '1';
	$post['submit_btn'] = ' Upload! ';

	// Uploading
	echo "<script type='text/javascript'>document.getElementById('info').style.display='none';</script>\n";
	$url = parse_url($up_url);
	$upfiles = upfile($url['host'], defport($url), $url['path'].(!empty($url['query']) ? '?'.$url['query'] : ''), $referer, $cookie, $post, $lfile, $lname, 'file_1', '', $_GET['proxy'], $pauth, 0, $url['scheme']);

	// Upload Finished
	echo "<script type='text/javascript'>document.getElementById('progressblock').style.display='none';</script>\n";

	is_page($upfiles);

	if (!$login && stripos($page, 'Uploads not enabled for this type of users') !== false) html_error('Please set '.($_T8['xfsFree'] ? '$_T8[\'xfsFree\'] to false and ' : '').'$_T8[\'anonUploadDisable\'] to true.');

	$statuscode = intval(substr($upfiles, 9, 3));
	if ($statuscode >= 400 || preg_match('@<body><b>([^<>]+)</b></body></html>@i', $upfiles, $err)) html_error("Upload server isn't working or has failed (HTTP $statuscode)".(!empty($err[1]) ? ', response: ' . htmlspecialchars($err[1]) : '.'));

	if ($page = cut_str($upfiles, '<Form name=\'F1\'', '</Form>')) {
		// Normal Upload (F1 Form)
		if (!preg_match_all('@<input\s+[^<>]*name=\'([^\']+)\'\s+value=\'([^\']+)\'@i', $page, $inputs)) html_error('Error: upload_result data not found.');
		$post = array_map('urlencode', array_map('html_entity_decode', array_combine(array_map('trim', $inputs[1]), array_map('trim', $inputs[2]))));
		if (empty($post['op']) || strtolower(urldecode($post['op'])) != 'upload_result') html_error('Error: "upload_result" value not found.');
		if (empty($post['fn'])) html_error('Error: "fn" input not found.');
		if (strtolower($post['st']) != 'ok') html_error('Upload failed, response: '.htmlspecialchars(urldecode($post['st'])));
	} else if (preg_match('@"file_status"\s*:\s*"([^\"\'\]\}]+)"@', $upfiles, $reply) && (strtolower($reply[1]) != 'ok' || preg_match('@"file_code"\s*:\s*"(\w{12})"@', $upfiles, $fileid))) {
		// New JSON response.
		if (empty($fileid)) html_error('Upload failed, json response: '.htmlspecialchars($reply[1]));
		$post = array('op' => 'upload_result', 'fn' => urlencode($fileid[1]), 'st' => urlencode($reply[1]));
	} else html_error('Error: upload_result form/json not found.');

	$page = geturl($domain, 443, '/', $up_url, $cookie, $post, 0, $_GET['proxy'], $pauth, 0, $scheme);is_page($page);

	$host_rexexp = 'https?://(?:www\.)?'.preg_quote(str_ireplace('www.', '', $domain), '@').'/';
	if (preg_match('@('.$host_rexexp.'\w{12}(?:/[^\?/<>\"\'\r\n]+)?(?:\.html?)?)\?killcode=\w+@i', $page, $lnk)) {
		$download_link = $lnk[1];
		$delete_link = $lnk[0];
	} else if (preg_match('@'.$host_rexexp.'del-(\w{12})-\w+/([^<>\"\'\r\n]+)@i', $page, $lnk)) {
		$download_link = substr($lnk[0], 0, (stripos($lnk[0], '/del-') + 1)) . $lnk[2] . '/' . $lnk[3];
		$delete_link = $lnk[0];
	} else if (preg_match('@'.$host_rexexp.'\w{12}(?:/[^\?/<>\"\'\r\n]+)?(?:\.html?)?(?=[\r\n\t\s\'\"<>])@i', $page, $lnk)) $download_link = $lnk[0];
	else html_error('Download link not found.');
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
	global $cookie, $scheme, $domain, $referer, $pauth, $default_acc;

	$is_step = (!empty($_POST['step']) && $_POST['step'] == '1');
	if ($is_step) {
		$_POST['step'] = false;
		if (empty($_POST['recaptcha_response_field'])) html_error('You didn\'t enter the image verification code.');
	}

	$post = array();
	$post['op'] = 'login';
	$post['redirect'] = '';
	$post['email'] = urlencode($_REQUEST['up_login']);
	$post['password'] = urlencode($_REQUEST['up_pass']);
	$post['recaptcha_response_field'] = !empty($_POST['recaptcha_response_field']) ? urlencode($_POST['recaptcha_response_field']) : '';
	$post['recaptcha_challenge_field'] = !empty($_POST['recaptcha_challenge_field']) ? urlencode($_POST['recaptcha_challenge_field']) : '';
	$post['rand'] = '';

	$page = geturl($domain, 443, '/login', "$referer\r\nX-Requested-With: XMLHttpRequest", $cookie, $post, 0, $_GET['proxy'], $pauth, 0, $scheme);is_page($page);

	if (stripos($page, 'data-captcha="yes"') !== false) {
		if ($is_step) html_error('Wrong Captcha Entered.');
		$data = array();
		$data['step'] = '1';
		$data['cookie'] = urlencode(encrypt(CookiesToStr($cookie)));
		$data['action'] = 'FORM';
		if (!$default_acc) {
			$data['A_encrypted'] = 'true';
			$data['up_login'] = urlencode(encrypt($user)); // encrypt() will keep this safe.
			$data['up_pass'] = urlencode(encrypt($pass)); // And this too.
		}
		reCAPTCHA('6LetAu0SAAAAACCJkqZLvjNS4L7eSL8fGxr-Jzy2', $data, 'Login'); // Hardcoded reCAPTCHA id.
		exit();
	}
	is_present($page, 'Incorrect Login or Password', 'Login failed: Email/Password incorrect.');
	is_present($page, 'op=resend_activation', 'Login failed: Your account isn\'t confirmed yet.');
	is_present($page, 'Please%20enter%20your%20e-mail', "Login failed: Missing account's email, login at site and set the email.");
	$cookie = GetCookiesArr($page, $cookie);
	if (empty($cookie['xfss']) && empty($cookie['email'])) html_error('Error: Login cookies not found.');
	$cookie['lang'] = 'english';

	$page = geturl($domain, 443, '/profile', $referer.'login', $cookie, 0, 0, $_GET['proxy'], $pauth, 0, $scheme);is_page($page);
	is_notpresent($page, '>Logout</a>', 'Login Error');

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
	global $scheme, $domain, $referer, $secretkey, $pauth;
	if (empty($user) || empty($pass)) html_error('Login Failed: User or Password is empty.');
	$user = strtolower($user);

	$filename = 'filejoker_ul.php';
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

		$page = geturl($domain, 443, '/profile', $referer.'login', $testCookie, 0, 0, $_GET['proxy'], $pauth, 0, $scheme);is_page($page);
		if (stripos($page, '>Logout</a>' === false)) return Login($user, $pass);

		$GLOBALS['cookie'] = $testCookie; // Update cookies
		SaveCookies($user, $pass); // Update cookies file
		return true;
	}
	return Login($user, $pass);
}

function SaveCookies($user, $pass) {
	global $secretkey;
	$maxdays = 31; // Max days to keep cookies saved

	$filename = 'filejoker_ul.php';
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

//[05-6-2016]  Written by Th3-822.

?>