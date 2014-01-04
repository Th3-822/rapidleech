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
	$referer = "http://$domain/";

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
		$page = geturl($domain, 80, '/', $referer, $cookie, 0, 0, $_GET['proxy'], $pauth);is_page($page);
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
	$upfiles = upfile($url['host'], defport($url), $url['path'].(!empty($url['query']) ? '?'.$url['query'] : ''), 0, 0, $post, $lfile, $lname, 'Filedata', '', $_GET['proxy'], $pauth);

	// Upload Finished
	echo "<script type='text/javascript'>document.getElementById('progressblock').style.display='none';</script>\n";

	is_page($upfiles);

	$json = Get_Reply($upfiles);
	if ((empty($json['result']) || $json['result'] != 'true') || empty($json['id'])) html_error('Upload error: "'.htmlentities($json['message']).'"');
	$id = is_array($json['id']) ? $json['id']['fid'] : $json['id'];

	//geturl($domain, 80, '/newfile/edit/'.$id, $referer, $cookie, 0, 0, $_GET['proxy'], $pauth);is_page($page);
	$page = geturl($domain, 80, '/newfile/gridFile/'.$id, $referer."newfile/edit/\r\nX-Requested-With: XMLHttpRequest", $cookie, 0, 0, $_GET['proxy'], $pauth);is_page($page);
	$json = Get_Reply($page);
	$info = reset($json['rows']);

	$download_link = "$referer$id.html";
	if (!empty($info['cell'][7])) $delete_link = $referer."delete/file/$id/".$info['cell'][7];
}

function Get_Reply($page) {
	if (!function_exists('json_decode')) html_error('Error: Please enable JSON in php.');
	$json = substr($page, strpos($page, "\r\n\r\n") + 4);
	$json = substr($json, strpos($json, '{'));$json = substr($json, 0, strrpos($json, '}') + 1);
	$rply = json_decode($json, true);
	if (!$rply || count($rply) == 0) html_error('Error reading json.');
	return $rply;
}

// Edited For upload.php usage.
function EnterCaptcha($captchaImg, $inputs, $captchaSize = '5') {
	echo "\n<form name='captcha' method='POST'>\n";
	foreach ($inputs as $name => $input) echo "\t<input type='hidden' name='$name' id='$name' value='$input' />\n";
	echo "\t<h4>" . lang(301) . " <img alt='CAPTCHA Image' src='$captchaImg' /> " . lang(302) . ": <input type='text' name='captcha' size='$captchaSize' />&nbsp;&nbsp;\n\t\t<input type='submit' onclick='return check();' value='Enter Captcha' />\n\t</h4>\n\t<script type='text/javascript'>/* <![CDATA[ */\n\t\tfunction check() {\n\t\t\tvar captcha=document.dl.captcha.value;\n\t\t\tif (captcha == '') {\n\t\t\t\twindow.alert('You didn\'t enter the image verification code');\n\t\t\t\treturn false;\n\t\t\t} else return true;\n\t\t}\n\t/* ]]> */</script>\n</form>\n</body>\n</html>";
}

// Edited For upload.php usage.
function Show_reCaptcha($pid, $inputs, $sname = 'Upload File') {
	if (!is_array($inputs)) html_error('Error parsing captcha data.');

	// Themes: 'red', 'white', 'blackglass', 'clean'
	echo "<script language='JavaScript'>var RecaptchaOptions = {theme:'red', lang:'en'};</script>\n\n<center><form name='recaptcha' method='POST'><br />\n";
	foreach ($inputs as $name => $input) echo "<input type='hidden' name='$name' id='$name' value='$input' />\n";
	echo "<script type='text/javascript' src='http://www.google.com/recaptcha/api/challenge?k=$pid'></script><noscript><iframe src='http://www.google.com/recaptcha/api/noscript?k=$pid' height='300' width='500' frameborder='0'></iframe><br /><textarea name='recaptcha_challenge_field' rows='3' cols='40'></textarea><input type='hidden' name='recaptcha_response_field' value='manual_challenge' /></noscript><br /><input type='submit' name='submit' onclick='javascript:return checkc();' value='$sname' />\n<script type='text/javascript'>/*<![CDATA[*/\nfunction checkc(){\nvar capt=document.getElementById('recaptcha_response_field');\nif (capt.value == '') { window.alert('You didn\'t enter the image verification code.'); return false; }\nelse { return true; }\n}\n/*]]>*/</script>\n</form></center>\n</body>\n</html>";
	exit;
}

function Login($user, $pass) {
	global $default_acc, $cookie, $domain, $referer, $pauth;
	$errors = array('CaptchaInvalid' => 'Wrong CAPTCHA entered.', 'InvalidLogIn' => 'Invalid Login/Pass.', 'CaptchaRequired' => 'Captcha Required.');
	if (!empty($_POST['step']) && ($_POST['step'] == '1' || $_POST['step'] == '2')) {
		$cookie = StrToCookies(decrypt(urldecode($_POST['cookie'])));

		$post = array();
		$post['user%5Blogin%5D'] = urlencode($user);
		$post['user%5Bpass%5D'] = urlencode($pass);
		if ($_POST['step'] == '1') {
			if (empty($_POST['captcha'])) html_error('You didn\'t enter the image verification code.');
			$post['user%5Bcaptcha_response%5D'] = urlencode($_POST['captcha']);
		} else {
			if (empty($_POST['recaptcha_response_field'])) html_error('You didn\'t enter the image verification code.');
			$post['recaptcha_challenge_field'] = $_POST['recaptcha_challenge_field'];
			$post['recaptcha_response_field'] = urlencode($_POST['recaptcha_response_field']);
		}
		$post['user%5Bcaptcha_type%5D'] = urlencode($_POST['c_type']);
		$post['user%5Bcaptcha_subtype%5D'] = (!empty($_POST['c_subtype']) ? urlencode($_POST['c_subtype']) : '');
		$post['user%5Bmemory%5D'] = 'on';
		$post['user%5Bsubmit%5D'] = 'Login';

		$page = geturl($domain, 80, '/user/login', $referer.'login', $cookie, $post, 0, $_GET['proxy'], $pauth);is_page($page);
		$cookie = GetCookiesArr($page, $cookie);

		$x = 0;
		while ($x < 3 && stripos($page, "\nLocation: ") !== false && preg_match('@\nLocation: ((https?://[^/\r\n]+)?/[^\r\n]*)@i', $page, $redir)) {
			$redir = (empty($redir[2])) ? 'http://turbobit.net'.$redir[1] : $redir[1];
			$url = parse_url($redir);
			$page = geturl($url['host'], defport($url), $url['path'].(!empty($url['query']) ? '?'.$url['query'] : ''), $referer, $cookie, 0, 0, $_GET['proxy'], $pauth);is_page($page);
			$cookie = GetCookiesArr($page, $cookie);
			$x++;
		}

		is_present($page, 'Incorrect login or password', 'Login Failed: Login/Password incorrect.');
		is_present($page, 'E-Mail address appears to be invalid.', 'Login Failed: Invalid E-Mail.');
		is_present($page, 'Incorrect verification code', 'Login Failed: Wrong CAPTCHA entered.');
		is_present($page, 'Incorrect captcha code', 'Login Failed: Wrong Recaptcha entered.');
		//is_present($page, 'Limit of login attempts exceeded for your account. It has been temporarily locked.', 'Login Failed: Account Temporally Locked.');

		if (empty($redir) || $redir != $referer) {
			$page = geturl($domain, 80, '/', $referer, $cookie, 0, 0, $_GET['proxy'], $pauth);is_page($page);
		}
		is_notpresent($page, '/user/logout">Logout<', 'Login Failed.');

		SaveCookies($user, $pass); // Update cookies file
		return $page;
	} else {
		$post = array();
		$post['user%5Blogin%5D'] = urlencode($user);
		$post['user%5Bpass%5D'] = urlencode($pass);
		$post['user%5Bmemory%5D'] = 'on';
		$post['user%5Bsubmit%5D'] = 'Login';

		$page = geturl($domain, 80, '/user/login', $referer.'login', $cookie, $post, 0, $_GET['proxy'], $pauth);is_page($page);
		$cookie = GetCookiesArr($page, $cookie);

		if (!empty($cookie['user_isloggedin']) && $cookie['user_isloggedin'] == '1') {
			$page = geturl($domain, 80, '/', $referer, $cookie, 0, 0, $_GET['proxy'], $pauth);is_page($page);
			SaveCookies($user, $pass); // Update cookies file
			return $page;
		}

		$x = 0;
		while ($x < 3 && stripos($page, "\nLocation: ") !== false && preg_match('@\nLocation: ((https?://[^/\r\n]+)?/[^\r\n]*)@i', $page, $redir)) {
			$redir = (empty($redir[2])) ? 'http://turbobit.net'.$redir[1] : $redir[1];
			$url = parse_url($redir);
			$page = geturl($url['host'], defport($url), $url['path'].(!empty($url['query']) ? '?'.$url['query'] : ''), $referer, $cookie, 0, 0, $_GET['proxy'], $pauth);is_page($page);
			$cookie = GetCookiesArr($page, $cookie);
			$x++;
		}
		if ($x < 1) html_error('Login redirect not found');

		is_present($page, 'Incorrect login or password', 'Login Failed: Login/Password incorrect');
		is_present($page, 'E-Mail address appears to be invalid.', 'Login Failed: Invalid E-Mail');
		// is_present($page, 'Limit of login attempts exceeded for your account. It has been temporarily locked.', 'Login Failed: Account Temporally Locked');

		if (preg_match('@(https?://[^/\r\n\t\s\'\"<>]+)?/captcha/[^\r\n\t\s\'\"<>]+@i', $page, $imgurl)) {
				$imgurl = (empty($imgurl[1])) ? 'http://turbobit.net'.$imgurl[0] : $imgurl[0];
				$imgurl = html_entity_decode($imgurl);

				if (!preg_match('@\Wvalue\s*=\s*[\'\"]([^\'\"\r\n<>]+)[\'\"]\s+name\s*=\s*[\'\"]user\[captcha_type\][\'\"]@i', $page, $c_type) || !preg_match('@\Wvalue\s*=\s*[\'\"]([^\'\"\r\n<>]*)[\'\"]\s+name\s*=\s*[\'\"]user\[captcha_subtype\][\'\"]@i', $page, $c_subtype)) html_error('CAPTCHA data not found.');

				//Download captcha img.
				$url = parse_url($imgurl);
				$page = geturl($url['host'], defport($url), $url['path'].(!empty($url['query']) ? '?'.$url['query'] : ''), $referer, $cookie, 0, 0, $_GET['proxy'], $pauth);is_page($page);
				$capt_img = substr($page, strpos($page, "\r\n\r\n") + 4);
				$imgfile = DOWNLOAD_DIR . 'turbobit_captcha.png';

				if (file_exists($imgfile)) unlink($imgfile);
				if (!write_file($imgfile, $capt_img)) html_error('Error getting CAPTCHA image.');

				$data = array();
				$data['action'] = 'FORM';
				$data['cookie'] = urlencode(encrypt(CookiesToStr($cookie)));
				$data['step'] = '1';
				$data['c_type'] = urlencode($c_type[1]);
				$data['c_subtype'] = urlencode($c_subtype[1]);
				if (!$default_acc) {
					$data['up_encrypted'] = 'true';
					$data['up_login'] = urlencode(encrypt($user));
					$data['up_pass'] = urlencode(encrypt($pass));
				}
				EnterCaptcha($imgfile.'?'.time(), $data);
				exit;
		} elseif (preg_match('@https?://(?:[^/]+\.)?(?:(?:google\.com/recaptcha/api)|(?:recaptcha\.net))/(?:(?:challenge)|(?:noscript))\?k=([\w|\-]+)@i', $page, $pid)) {
			$data = array();
			$data['action'] = 'FORM';
			$data['cookie'] = urlencode(encrypt(CookiesToStr($cookie)));
			$data['step'] = '2';
			$data['c_type'] = 'recaptcha';
			if (!$default_acc) {
				$data['up_encrypted'] = 'true';
				$data['up_login'] = urlencode(encrypt($user));
				$data['up_pass'] = urlencode(encrypt($pass));
			}
			Show_reCaptcha($pid[1], $data, 'Login');
			exit;
		} elseif (stripos($page, '/user/logout">Logout<') !== false) {
			$this->SaveCookies($user, $pass); // Update cookies file
			is_present($page, '<u>Turbo Access</u> denied', 'Login Failed: Account isn\'t premium');
			return $this->PremiumDL();
		} else html_error('CAPTCHA not found.');
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

		$page = geturl($domain, 80, '/', $referer, $cookie, 0, 0, $_GET['proxy'], $pauth);is_page($page);
		if (stripos($page, '/user/logout">Logout<') === false) return Login($user, $pass);
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

?>