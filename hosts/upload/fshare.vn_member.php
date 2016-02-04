<?php
######## Account Info ########
$upload_acc['fshare_vn']['user'] = ''; //Set your login
$upload_acc['fshare_vn']['pass'] = ''; //Set your password
########################

$_GET['proxy'] = isset($_GET['proxy']) ? $_GET['proxy'] : '';
$not_done = true;

if ($upload_acc['fshare_vn']['user'] && $upload_acc['fshare_vn']['pass']) {
	$default_acc = true;
	$_REQUEST['up_login'] = $upload_acc['fshare_vn']['user'];
	$_REQUEST['up_pass'] = $upload_acc['fshare_vn']['pass'];
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
	$domain = 'www.fshare.vn';
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
	echo "<script type='text/javascript'>document.getElementById('login').style.display='none';</script>\n<div id='info' width='100%' align='center'>Retrieving upload ID</div>\n";

	$page = geturl('up.fshare.vn', 80, '/index.php?keepThis=false&', $referer, $cookie, 0, 0, $_GET['proxy'], $pauth);is_page($page);
	$cookie = GetCookiesArr($page, $cookie);

	if (!preg_match('@upload_url\s*:\s*"((https?://(?:\w+\.)*fshare\.vn)?(/)?[^\s\'\"<>]+)",@i', $page, $up)) html_error('Error: Upload url not found.');
	if (!preg_match('@\{"SESSID"\s*:\s*"(\w+)"@i', $page, $sid)) html_error('Error: Upload session not found.');

	$post = array();
	$post['Filename'] = $lname;
	$post['folder_id'] = '-1';
	$post['direct_link'] = $post['secure'] = 'null';
	$post['desc'] = 'Uploaded with Rapidleech.';
	$post['SESSID'] = $sid[1];
	$post['Upload'] = 'Submit Query';

	$up_url = (empty($up[2]) ? 'http://up.fshare.vn'.(empty($up[3]) ? '/' : '').$up[1] : $up[1]);

	// Uploading
	echo "<script type='text/javascript'>document.getElementById('info').style.display='none';</script>\n";

	$url = parse_url($up_url);
	$upfiles = upfile($url['host'], defport($url), $url['path'].(!empty($url['query']) ? '?'.$url['query'] : ''), 0, 0, $post, $lfile, $lname, 'fileupload', '', 0, 0, 'Shockwave Flash', $url['scheme']);

	// Upload Finished
	echo "<script type='text/javascript'>document.getElementById('progressblock').style.display='none';</script>\n";

	is_page($upfiles);

	if (!preg_match('@https?://(?:www\.)?fshare\.vn/file/\w+/?@i', $upfiles, $lnk)) html_error('Download link not found.');
	$download_link = $lnk[0];
}

// Edited For upload.php usage.
function EnterCaptcha($captchaImg, $inputs, $captchaSize = '5') {
	echo "\n<form name='captcha' method='POST'>\n";
	foreach ($inputs as $name => $input) echo "\t<input type='hidden' name='$name' id='$name' value='$input' />\n";
	echo "\t<h4>" . lang(301) . " <img alt='CAPTCHA Image' src='$captchaImg' /> " . lang(302) . ": <input type='text' name='captcha' size='$captchaSize' />&nbsp;&nbsp;\n\t\t<input type='submit' onclick='return check();' value='Enter Captcha' />\n\t</h4>\n\t<script type='text/javascript'>/* <![CDATA[ */\n\t\tfunction check() {\n\t\t\tvar captcha=document.dl.captcha.value;\n\t\t\tif (captcha == '') {\n\t\t\t\twindow.alert('You didn\'t enter the image verification code');\n\t\t\t\treturn false;\n\t\t\t} else return true;\n\t\t}\n\t/* ]]> */</script>\n</form>\n</body>\n</html>";
}

function DL_reCaptcha($pid, $data) {
	global $pauth;
	$page = geturl('www.google.com', 80, "/recaptcha/api/challenge?k=$pid", 0, 0, 0, 0, $_GET['proxy'], $pauth);is_page($page);
	if (!preg_match('/challenge \: \'([^\']+)/i', $page, $ch)) html_error('Error getting CAPTCHA data.');
	$challenge = $ch[1];

	$data['challenge'] = $challenge;

	//Download captcha img.
	$page = geturl('www.google.com', 80, "/recaptcha/api/image?c=$challenge", 0, 0, 0, 0, $_GET['proxy'], $pauth);is_page($page);
	$capt_img = substr($page, strpos($page, "\r\n\r\n") + 4);
	$imgfile = DOWNLOAD_DIR . 'fsharevn_captcha.jpg';

	if (file_exists($imgfile)) unlink($imgfile);
	if (!write_file($imgfile, $capt_img)) html_error('Error getting CAPTCHA image.');

	EnterCaptcha($imgfile.'?'.time(), $data, 20);
	exit;
}

function Login($user, $pass) {
	global $cookie, $domain, $referer, $pauth, $default_acc;
	if (!empty($_POST['step']) && $_POST['step'] == '1') {
		$captcha = true;
		$_POST['step'] = false;
	} else $captcha = false;

	$post = array();
	$post['login_useremail'] = urlencode($user);
	$post['login_password'] = urlencode($pass);
	$post['url_refe'] = 'http%3A%2F%2Fwww.fshare.vn%2F';
	$post['auto_login'] = 1;
	if ($captcha) {
		if (empty($_POST['captcha'])) html_error('You didn\'t enter the image verification code.');
		$cookie = StrToCookies(decrypt(urldecode($_POST['cookie'])));
		$post['recaptcha_challenge_field'] = urlencode($_POST['challenge']);
		$post['recaptcha_response_field'] = urlencode($_POST['captcha']);
	}

	$page = geturl($domain, 0, '/login.php', $referer.'login.php', $cookie, $post, 0, 0, 0, 0, 'https');is_page($page);
	$cookie = GetCookiesArr($page, $cookie);
	if (preg_match('@\nLocation: ((https?://(\w+\.)*fshare.vn)?/active\.php\?code=[^\r\n]+)@i', $page, $redir)) {
		$redir = parse_url((empty($redir[2]) ? 'https://www.fshare.vn'.$redir[1] : $redir[1]));
		$page = geturl($redir['host'], defport($redir), $redir['path'], $referer.'login.php', $cookie, 0, 0, 0, 0, 0, $redir['scheme']);is_page($page);
		$cookie = GetCookiesArr($page, $cookie);
	}

	if ($captcha) is_present($page, 'Mã xác nhận không chính xác', 'Login Failed: Wrong CAPTCHA entered.');
	elseif (stripos($page, 'Mã xác nhận không chính xác') !== false) {
		if (!preg_match('@https?://(?:[^/]+\.)?(?:(?:google\.com/recaptcha/api)|(?:recaptcha\.net))/(?:(?:challenge)|(?:noscript))\?k=([\w|\-]+)@i', $page, $pid)) html_error('Login Error: Captcha not found.');
		$data = array();
		$data['step'] = '1';
		$data['cookie'] = urlencode(encrypt(CookiesToStr($cookie)));
		$data['action'] = 'FORM';
		if (!$default_acc) {
			$data['A_encrypted'] = 'true';
			$data['up_login'] = urlencode(encrypt($user)); // encrypt() will keep this safe.
			$data['up_pass'] = urlencode(encrypt($pass)); // And this too.
		}
		DL_reCaptcha($pid[1], $data);
		exit;
	}

	is_present($page, 'Mật khẩu phải có ít nhất 6 ký tự.', 'Login Failed: Password too short (< 6).');
	is_present($page, 'Bạn cần nhập chính xác email và mật khẩu.', 'Login Failed: Email/Password incorrect.');
	if (empty($cookie['fshare_userpass'])) html_error('Login Error: Cannot find "fshare_userpass" cookie.');

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

	$filename = 'fsharevn_ul.php';
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

		$page = geturl($domain, 80, '/', $referer, $cookie, 0, 0, $_GET['proxy'], $pauth);is_page($page);
		if (stripos($page, '>Thoát</a>') === false) return Login($user, $pass);
		$cookie = GetCookiesArr($page, $cookie); // Update cookies
		SaveCookies($user, $pass); // Update cookies file
		return true;
	}
	return Login($user, $pass);
}

function SaveCookies($user, $pass) {
	global $cookie, $secretkey;
	$maxdays = 31; // Max days to keep cookies saved

	$filename = 'fsharevn_ul.php';
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

//[21-2-2014]  Written by Th3-822.

?>