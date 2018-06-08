<?php
######## Account Info ########
$upload_acc['mediafire_com']['user'] = ''; //Set your login
$upload_acc['mediafire_com']['pass'] = ''; //Set your password
########################

// Uncomment the next line to force default upload settings: Instant Upload: ON and Default Action on Duplicate Name: Skip Upload
// define('MF_forceDefaultUploadOptions');

$_GET['proxy'] = isset($_GET['proxy']) ? $_GET['proxy'] : '';
$not_done = true;

$cURL = $options['use_curl'] && extension_loaded('curl') && function_exists('curl_init') && function_exists('curl_exec') ? true : false;
$chttps = false;
if ($cURL) {
	$cV = curl_version();
	if (in_array('https', $cV['protocols'], true)) $chttps = true;
}
if (!extension_loaded('openssl') && !$chttps) html_error('You need to install/enable PHP\'s OpenSSL extension to support HTTPS connections.');
elseif (!$chttps) $cURL = false;

/* Not Final */
if (!function_exists('curl_upfile')) {
	function curl_upfile($host, $port, $url, $referer, $cookie, $post, $file, $filename, $fieldname, $field2name = '', $proxy = 0, $pauth = 0, $upagent = 0, $scheme = 'http') {
		static $ch;
		if (empty($upagent)) $upagent = rl_UserAgent;
		$scheme = strtolower("$scheme://");
		$fileSize = filesize($file);

		if (!is_readable($file)) return html_error(sprintf(lang(65), $file));
		if ($scheme == 'https://' && ($port == 0 || $port == 80)) $port = 443;
		elseif ($port == 0) $port = 80;

		if (!extension_loaded('curl') || !function_exists('curl_init') || !function_exists('curl_exec')) return html_error('cURL isn\'t enabled or cURL\'s functions are disabled');
		$arr = explode("\r\n", $referer);
		$header = array();
		if (count($arr) > 1) {
			$referer = $arr[0];
			unset($arr[0]);
			$header = array_filter(array_map('trim', $arr));
		}
		$opt = array(CURLOPT_HEADER => 1, CURLOPT_SSL_VERIFYPEER => 0,
			CURLOPT_SSL_VERIFYHOST => 0, CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_FOLLOWLOCATION => 0, CURLOPT_FAILONERROR => 1,
			CURLOPT_FORBID_REUSE => 0, CURLOPT_FRESH_CONNECT => 0,
			CURLINFO_HEADER_OUT => 1, CURLOPT_URL => $scheme . $host . ($port != 80 && $port != 443 ? ":" . $port : "") . str_replace(array(' ', "\r", "\n"), array('%20'), $url),
			CURLOPT_USERAGENT => $upagent);

		$opt[CURLOPT_REFERER] = !empty($referer) ? $referer : false;
		$opt[CURLOPT_COOKIE] = !empty($cookie) ? (is_array($cookie) ? CookiesToStr($cookie) : trim($cookie)) : false;

		if (!empty($_GET['useproxy']) && !empty($_GET['proxy'])) {
			$opt[CURLOPT_HTTPPROXYTUNNEL] = ($scheme == 'https://') ? true : false;
			// $opt[CURLOPT_HTTPPROXYTUNNEL] = false; // Uncomment this line for disable https proxy over curl.
			$opt[CURLOPT_PROXY] = $_GET['proxy'];
			$opt[CURLOPT_PROXYUSERPWD] = (!empty($pauth) ? base64_decode($pauth) : false);
		} else $opt[CURLOPT_PROXY] = false;

		// Send more headers...
		$headers = array('Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8', 'Accept-Language: en-US;q=0.7,en;q=0.3', 'Pragma: no-cache', 'Cache-Control: no-cache', 'Connection: Close');
		if (empty($opt[CURLOPT_REFERER])) $headers[] = 'Referer:';
		if (empty($opt[CURLOPT_COOKIE])) $headers[] = 'Cookie:';
		if (!empty($opt[CURLOPT_PROXY]) && empty($opt[CURLOPT_PROXYUSERPWD])) $headers[] = 'Proxy-Authorization:';
		if (count($header) > 0) $headers = array_merge($headers, $header);
		$opt[CURLOPT_HTTPHEADER] = $headers;

		$opt[CURLOPT_HTTPAUTH] = false;

		if (empty($post)) $post = array();
		$opt[CURLOPT_POST] = 1;
		if (!is_array($post)) return html_error('Error: $post must be a array!');
		if (function_exists('curl_file_create')) {
			$post[$fieldname] = curl_file_create($file, '', $filename);
		} else {
			$post = array_map(function($val) {
				return ltrim($val, '@');
			}, $post);
			$post[$fieldname] = sprintf('@%s;filename=%s', realpath($file), $filename);
			if (defined('CURLOPT_SAFE_UPLOAD')) $opt['CURLOPT_SAFE_UPLOAD'] = false;
		}
		$opt[CURLOPT_POSTFIELDS] = $post;

		// Progress bar
		$opt[CURLOPT_NOPROGRESS] = false;
		$opt[CURLOPT_BUFFERSIZE] = GetChunkSize($fileSize);
		$opt[CURLOPT_PROGRESSFUNCTION] = function($ch, $download_size = 0, $downloaded = 0, $upload_size = 0, $uploaded = 0) {
			if (version_compare(PHP_VERSION, '5.5.0') < 0) {
				$uploaded = $upload_size;
				$upload_size = $downloaded;
				$downloaded = $download_size;
				$download_size = $ch;
			}
			static $lastUploaded = 0;
			static $time = 0;
			static $lastChunkTime;
			if (!isset($lastChunkTime)) $lastChunkTime = microtime(true);

			if ($uploaded != $lastUploaded && (time() > $time || $uploaded == $upload_size)) {
				$mtime = microtime(true);
				$ctime = $mtime - $lastChunkTime;
				$ctime = ($ctime > 0) ? $ctime : 1;
				$lastChunkTime = $mtime;
				$speed = round(($uploaded - $lastUploaded) / 1024 / $ctime, 2);
				$progress = min(round(($uploaded / $upload_size) * 100, 2), 100);
				echo "<script type='text/javascript'>pr('$progress', '" . bytesToKbOrMbOrGb($uploaded) . "', '$speed');</script>\n";
				flush();
				$lastUploaded = $uploaded;
				$time = time();
			}
		};

		$opt[CURLOPT_CONNECTTIMEOUT] = $opt[CURLOPT_TIMEOUT] = 120;

		// Creating curl resource
		if (!isset($ch)) $ch = curl_init();

		foreach ($opt as $O => $V) curl_setopt($ch, $O, $V); // Using this instead of 'curl_setopt_array'

		if ($proxy) echo '<p>' . sprintf(lang(89), $proxyHost, $proxyPort) . '<br />UPLOAD: <b>' . htmlspecialchars($url) . "</b>...<br />\n";
		else echo '<p>'.sprintf(lang(90), $host, $port).'</p>';

		echo(lang(104) . ' <b>' . htmlspecialchars($filename) . '</b>, ' . lang(56) . ' <b>' . bytesToKbOrMbOrGb($fileSize) . '</b>...<br />');
		$GLOBALS['id'] = md5(time() * rand(0, 10));
		require (TEMPLATE_DIR . '/uploadui.php');
		flush();

		$page = curl_exec($ch);
		$info = curl_getinfo($ch);
		$errz = curl_errno($ch);
		$errz2 = curl_error($ch);
		curl_close($ch);

		if ($errz != 0) return html_error("[cURL-Upload:$errz] $errz2");

		if (substr($page, 9, 3) == '100' || !empty($opt[CURLOPT_HTTPPROXYTUNNEL])) $page = preg_replace("@^HTTP/1\.[01] \d{3}(?:\s[^\r\n]+)?\r\n\r\n(HTTP/1\.[01] \d+ [^\r\n]+)@i", "$1", $page, 1); // The "100 Continue" or "200 Connection established" can break some functions in plugins, lets remove it...
		return $page;
	}
}

if ($upload_acc['mediafire_com']['user'] && $upload_acc['mediafire_com']['pass']) {
	$default_acc = true;
	$_REQUEST['up_login'] = $upload_acc['mediafire_com']['user'];
	$_REQUEST['up_pass'] = $upload_acc['mediafire_com']['pass'];
	$_REQUEST['action'] = 'FORM';
	echo "<b><center>Using Default Login.</center></b>\n";
} else $default_acc = false;

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
	$domain = 'www.mediafire.com';
	$referer = "https://$domain/";
	$app = array('id' => '44595', 'api_version' => '1.5'); // Application ID for MediaFire's API @ https://www.mediafire.com/#settings/applications

	// Login
	echo "<table style='width:600px;margin:auto;'>\n<tr><td align='center'>\n<div id='info' width='100%' align='center'>Login to $domain</div>\n";

	$cookie = array();
	if (!empty($_REQUEST['up_login']) && !empty($_REQUEST['up_pass'])) {
		if (!empty($_REQUEST['A_encrypted'])) {
			$_REQUEST['up_login'] = decrypt(urldecode($_REQUEST['up_login']));
			$_REQUEST['up_pass'] = decrypt(urldecode($_REQUEST['up_pass']));
			unset($_REQUEST['A_encrypted']);
		}
		Login($_REQUEST['up_login'], $_REQUEST['up_pass']);
		$login = true;
	} else html_error('Login failed: User/Password empty.');

	// Hashing File
	echo "<script type='text/javascript'>document.getElementById('info').innerHTML = 'Hashing File';</script>\n";

	$fileHash = (in_array('sha256', hash_algos()) ? hash_file('sha256', $lfile, false) : ''); // sha256 is needed for Instant upload and extra checks.

	// Preparing Upload
	echo "<script type='text/javascript'>document.getElementById('info').innerHTML = 'Preparing Upload';</script>\n";

	$uploadCheck = array_map('strtolower_nr', mf_apireq('upload/check', array('size' => $fsize, 'hash' => $fileHash, 'filename' => $lname)));
	mf_checkErrors($uploadCheck, 'Pre-Upload Check Error');

	if ($uploadCheck['storage_limit_exceeded'] == 'yes') html_error('User storage limit exceeded.');
	if ($fsize > $uploadCheck['available_space']) html_error('User does not have enough space for this file.'); // $fsize > ($uploadCheck['storage_limit'] - $uploadCheck['used_storage_size'])

	if (!empty($fileHash) && $uploadCheck['file_exists'] == 'yes' && !empty($uploadCheck['different_hash']) && $uploadCheck['different_hash'] == 'no') html_error('A file with the same name already exists in your root directory and it\'s contents are identical.');

	if (!defined('MF_forceDefaultUploadOptions')) {
		$uploadPrefs = array_map('strtolower', mf_apireq('upload/get_options'));
		mf_checkErrors($uploadPrefs, 'Cannot Get Upload Preferences');
	} else $uploadPrefs = array('disable_instant' => 'no', 'action_on_duplicate' => 'skip');

	if ($uploadCheck['file_exists'] == 'yes' && $uploadPrefs['action_on_duplicate'] == 'skip') html_error('A file with the same name already exists in your root directory.');

	if ($uploadCheck['hash_exists'] == 'yes' && $uploadPrefs['disable_instant'] == 'no') {
		// Instant Upload
		echo "<script type='text/javascript'>document.getElementById('info').innerHTML = 'Instant Upload Mode';</script>\n";

		$instantUpload = mf_apireq('upload/instant', array('size' => $fsize, 'hash' => $fileHash, 'filename' => $lname, 'action_on_duplicate' => $uploadPrefs['action_on_duplicate']));
		mf_checkErrors($instantUpload, 'Instant Upload Error');

		if (empty($instantUpload['quickkey'])) html_error('Instant Upload: quickkey not found.');
		$download_link = "$referer?" . $instantUpload['quickkey'];
		return; // Stop this include
	}

	// Uploading
	echo "<script type='text/javascript'>document.getElementById('info').style.display = 'none';</script>\n";

	// action_token
	$getAToken = mf_apireq('user/get_action_token', array('type' => 'upload', 'lifespan' => 1440)); //24 Hours Lifespan
	mf_checkErrors($getAToken, 'Error Getting Action Token');

	$up_url = $referer . "api/{$app['api_version']}/upload/simple.php?response_format=json&session_token={$getAToken['action_token']}&action_on_duplicate=" . $uploadPrefs['action_on_duplicate'];

	$url = parse_url($up_url);
	$upfiles = curl_upfile($url['host'], defport($url), $url['path'].(!empty($url['query']) ? '?'.$url['query'] : ''), $referer.(!empty($fileHash) ? "\r\nX-Filehash: $fileHash" : ''), 0, 0, $lfile, $lname, 'Filedata', '', $_GET['proxy'], $pauth, 0, $url['scheme']);

	// Upload Finished
	echo "<script type='text/javascript'>document.getElementById('progressblock').style.display='none';</script>\n";

	is_page($upfiles);

	$status = intval(substr($upfiles, 9, 3));
	if ($status >= 500) $ulResult = array('result' => 'Error', 'error' => $status, 'message' => "Upload: HTTP Error $status.");
	else {
		$ulResult = Get_Reply($upfiles);
		if (count($ulResult) == 1 && !empty($ulResult['response'])) $ulResult = $ulResult['response'];
	}
	mf_checkErrors($ulResult, $prefix = 'Upload error');

	if (empty($ulResult['doupload']['key'])) html_error('Upload error: Key not found.');

	// Kill Action Token and test the Session Token with it too
	$test = mf_apireq('user/destroy_action_token', array('action_token' => $getAToken['action_token']));
	if (strtolower($test['result']) == 'error' && $test['error'] == '105') {
		// Re-Login
		Login($_REQUEST['up_login'], $_REQUEST['up_pass']);
		mf_apireq('user/destroy_action_token', array('action_token' => $getAToken['action_token']));
	}

	// Pool Upload
	echo "<div id='T8_div' width='100%' align='center'>Checking Finished Upload : Try <span id='T8_try'>0</span><br /><span id='T8_status'></span></div>\n";

	$x = 1;
	do {
		echo "<script type='text/javascript'>document.getElementById('T8_try').innerHTML = '$x';</script>\n";

		sleep($x + 5);
		$poll_upload = mf_apireq('upload/poll_upload', array('key' => $ulResult['doupload']['key']));
		mf_checkErrors($poll_upload, 'Error Saving File');

		echo "<script type='text/javascript'>document.getElementById('T8_status').innerHTML += '" . htmlspecialchars($poll_upload['doupload']['description'], ENT_QUOTES) . "<br />';</script>\n";

		if (!empty($poll_upload['doupload']['fileerror'])) {
			$err = "Uploaded File Error: [{$poll_upload['doupload']['error']}]";
			switch ($poll_upload['doupload']['error']) {
				default: $err .= ($poll_upload['doupload']['description'] ? $poll_upload['doupload']['description'] : '*No description for this error*');break;
				case 1: $err .= 'File is larger than the maximum filesize allowed';break;
				case 2: $err .= 'File size cannot be 0';break;
				case 3: case 4:
				case 9: $err .= 'Found a bad RAR file';break;
				case 5: $err .= 'Virus found';break;
				case 6: case 8:
				case 10: $err .= 'Unknown internal error';break;
				case 7: $err .= 'File hash or size mismatch';break;
				case 12: $err .= 'Failed to insert data into database';break;
				case 13: $err .= 'File name already exists in the same parent folder, skipping';break;
				case 14: $err .= 'Destination folder does not exist';break;
				case 15: $err .= 'Account storage limit is reached';break;
				case 16: $err .= 'There was a file update revision conflict';break;
				case 17: $err .= 'Error patching delta file';break;
				case 18: $err .= 'Account is blocked';break;
				case 19: $err .= 'Failure to create path';break;
			}
			html_error("$err.");
		}
	} while ($x++ < 20 && $poll_upload['doupload']['status'] != '99');

	if (empty($poll_upload['doupload']['quickkey'])) html_error('Upload: quickkey not found.');
	$download_link = "$referer?" . $poll_upload['doupload']['quickkey'];
}

function Login($user, $pass) {
	$post = array();
	$post['email'] = $user;
	$post['password'] = $pass;
	$post['application_id'] = $GLOBALS['app']['id'];
	$post['signature'] = sha1($user . $pass . $GLOBALS['app']['id']);
	$post['token_version'] = 1;

	$login = mf_apireq('user/get_session_token', $post);
	mf_checkErrors($login, 'Login Error');
	if (empty($login['session_token'])) html_error('Session Token not Found.');
	$GLOBALS['app']['session_token'] = $login['session_token'];

	if (!empty($login['current_api_version']) && $login['current_api_version'] != $GLOBALS['app']['api_version']) $GLOBALS['app']['new_api_version'] = $login['current_api_version'];

	return true;
}

function Get_Reply($content) {
	if (!function_exists('json_decode')) html_error('Error: Please enable JSON in PHP.');
	$content = ltrim($content);
	if (($pos = strpos($content, "\r\n\r\n")) > 0) $content = trim(substr($content, $pos + 4));
	$cb_pos = strpos($content, '{');
	$sb_pos = strpos($content, '[');
	if ($cb_pos === false && $sb_pos === false) html_error('JSON start braces not found.');
	$sb = ($cb_pos === false || $sb_pos < $cb_pos) ? true : false;
	$content = substr($content, strpos($content, ($sb ? '[' : '{')));$content = substr($content, 0, strrpos($content, ($sb ? ']' : '}')) + 1);
	if (empty($content)) html_error('No JSON content.');
	$rply = json_decode($content, true);
	if ($rply === null) html_error('Error reading JSON.');
	return $rply;
}

function mf_checkErrors($reply, $prefix = 'Error') {
	if (strtolower($reply['result']) != 'error' || (!empty($reply['doupload']['result']) && is_numeric($reply['doupload']['result']) && $reply['doupload']['result'] >= 0)) return;
	if (!empty($reply['error'])) {
		$err = "$prefix: [{$reply['error']}]: ";
		switch ($reply['error']) {
			default: $err .= ($reply['message'] ? $reply['message'] : '*No message for this error*');break;
			case 101:
			case 900: $err .= 'API temporarily not available, please try again later';break;
			case 107: $err .= 'Email/Password incorrect';break;
			case 108: $err .= 'Invalid User/Email';break;
			// HTTP Errors
			case 500: $err .= 'Server error while processing your request, please try again later';break;
			case 503: $err .= 'API temporarily not available, please try again later';break;
		}
	} else {
		$err = "$prefix: [{$reply['doupload']['error']}]: ";
		switch ($reply['doupload']['error']) {
			default: $err .= ($reply['doupload']['description'] ? $reply['doupload']['description'] : '*No description for this error*');break;
			case -20: $err .= 'Invalid Upload Key';break;
			case -80: $err .= 'Upload Key not Found';break;
			case -701:
			case -881: $err .= 'Maximum file size for free users exceeded';break;
			case -700:
			case -882: $err .= 'Maximum file size exceeded';break;
		}
	}
	if (!empty($GLOBALS['app']['new_api_version'])) echo "\n<span>Current API Used: " . htmlspecialchars($GLOBALS['app']['api_version']) . "<br />Lastest API Available: " . htmlspecialchars($GLOBALS['app']['new_api_version']) . "</span>\n";
	html_error("$err.");
}

function mf_apireq($action, $post = array()) {
	if (!function_exists('json_encode')) html_error('Error: Please enable JSON in PHP.');
	if (!is_array($post)) html_error('mf_apireq: Parameter 2 must be passed as an array.');

	$post['response_format'] = 'json'; // Get API replies in json
	if (in_array($action, array('user/get_session_token', 'upload/poll_upload'))) unset($post['session_token']);
	else if (empty($post['session_token']) && !empty($GLOBALS['app']['session_token'])) $post['session_token'] = $GLOBALS['app']['session_token'];
	$post = array_map('urlencode', array_filter($post));

	$path = "api/{$GLOBALS['app']['api_version']}/$action.php";
	if ($GLOBALS['cURL']) $page = cURL($GLOBALS['referer'] . $path, 0, $post, $GLOBALS['referer']);
	else {
		$page = geturl($GLOBALS['domain'], 443, "/$path", $GLOBALS['referer'], 0, $post, 0, $_GET['proxy'], $GLOBALS['pauth'], 0, 'https');
		is_page($page);
	}

	$status = intval(substr($page, 9, 3));
	if ($status >= 500) return array('result' => 'Error', 'error' => $status, 'message' => "mf_apireq: HTTP Error $status.");

	$json = Get_Reply($page);
	if (count($json) == 1 && !empty($json['response'])) $json = $json['response'];
	return $json;
}

function strtolower_nr($str) {
	return (is_string($str) ? strtolower($str) : $str);
}

//[18-2-2015]  Written by Th3-822.
//[30-12-2017] Updated API to 1.5 & small fixes. - Th3-822
//[24-1-2018] Fixed upload token expiring issues & small fixes. - Th3-822
//[03-6-2018] Switched to www. domain & use cURL for upload (Requires PHP 5.4 for Progress Bar to Work, iirc) & spamming GH with issues won't make this fixed early. - Th3-822
//[08-6-2018] Fixed filenames on upload. - Th3-822