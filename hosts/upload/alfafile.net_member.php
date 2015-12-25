<?php
######## Account Info ########
$upload_acc['alfafile_net']['user'] = ''; //Set your login
$upload_acc['alfafile_net']['pass'] = ''; //Set your password
########################

$_GET['proxy'] = isset($_GET['proxy']) ? $_GET['proxy'] : '';
$not_done = true;

// Check https support for requests.
$use_curl = $options['use_curl'] && extension_loaded('curl') && function_exists('curl_init') && function_exists('curl_exec') ? true : false;
$chttps = false;
$use_https = true;
if ($use_curl) {
	$cV = curl_version();
	if (in_array('https', $cV['protocols'], true)) $chttps = true;
}
if (!extension_loaded('openssl') && !$chttps) $use_https = false;
else if (!$chttps) $use_curl = false;

if ($upload_acc['alfafile_net']['user'] && $upload_acc['alfafile_net']['pass']) {
	$default_acc = true;
	$_REQUEST['up_login'] = $upload_acc['alfafile_net']['user'];
	$_REQUEST['up_pass'] = $upload_acc['alfafile_net']['pass'];
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
	$domain = 'alfafile.net';
	$referer = "https://$domain/";

	// Login
	echo "<table style='width:600px;margin:auto;'>\n<tr><td align='center'>\n<div id='info' width='100%' align='center'>Login to $domain</div>\n";

	$cookie = array('lang' => 'en');
	if (!empty($_REQUEST['up_login']) && !empty($_REQUEST['up_pass'])) {
		if (!empty($_REQUEST['A_encrypted'])) {
			$_REQUEST['up_login'] = decrypt(urldecode($_REQUEST['up_login']));
			$_REQUEST['up_pass'] = decrypt(urldecode($_REQUEST['up_pass']));
			unset($_REQUEST['A_encrypted']);
		}

		$login_json = ApiReq('user/login', array('login' => $_REQUEST['up_login'], 'password' => $_REQUEST['up_pass']));
		if (empty($login_json['token'])) html_error('Login Token Not Found.');
		if (empty($login_json['user'])) html_error('User Data Not Found.');
		if (!empty($login_json['user']['upload']['max_file_size']) && $fsize > $login_json['user']['upload']['max_file_size']) html_error('User does not have enough space for this file.');

		$login = true;
	} else {
		html_error('Login Failed: Login/Password empty.');
	}

	// Hashing File
	echo "<script type='text/javascript'>document.getElementById('info').innerHTML = 'Hashing File...';</script>\n";

	$fileHash = md5_file($lfile, false);

	// Preparing Upload
	echo "<script type='text/javascript'>document.getElementById('info').innerHTML = 'Preparing Upload';</script>\n";

	$preUpload = ApiReq('file/upload', array('name' => $lname, 'size' => $fsize, 'hash' => $fileHash));
	if (empty($preUpload['upload'])) html_error('Empty PreUpload Response.');
	$preUpload = $preUpload['upload'];

	if (empty($preUpload['url'])) html_error('Upload URL not found.');
	if ($preUpload['state'] !== 0) {
		if ($preUpload['state_label'] == 'Done' && !empty($preUpload['file']['url'])) return $download_link = $preUpload['file']['url']; // Instantupload
		html_error(sprintf('Invalid PreUpload State: [%d] %s', $preUpload['state'], htmlspecialchars($preUpload['state_label'], ENT_QUOTES)));
	}

	// Uploading
	echo "<script type='text/javascript'>document.getElementById('info').style.display='none';</script>\n";
	$url = parse_url($preUpload['url']);
	$upfiles = upfile($url['host'], defport($url), $url['path'].(!empty($url['query']) ? '?'.$url['query'] : ''), $referer, $cookie, array(), $lfile, $lname, 'file', '', $_GET['proxy'], $pauth, 0, $url['scheme']);

	// Upload Finished
	echo "<script type='text/javascript'>document.getElementById('progressblock').style.display='none';</script>\n";
	is_page($upfiles);

	$postUpload = json2array($upfiles, 'Post-Upload Error');
	if (empty($postUpload['response']) && !empty($postUpload['details'])) html_error('[Post-Upload Error] ' . htmlspecialchars($postUpload['details'], ENT_QUOTES));
	$postUpload = $postUpload['response'];
	if (empty($postUpload['upload'])) html_error('Empty PostUpload Response.');
	$postUpload = $postUpload['upload'];

	if (empty($postUpload['state']) || $postUpload['state'] > 1) {
		if ($postUpload['state'] == 2 && $postUpload['state_label'] == 'Done' && !empty($postUpload['file']['url'])) return $download_link = $postUpload['file']['url'];
		html_error(sprintf('Invalid PostUpload State: [%d] %s', $postUpload['state'], htmlspecialchars($postUpload['state_label'], ENT_QUOTES)));
	}

	// Check Upload
	echo "<div id='T8_div' width='100%' align='center'>Checking Finished Upload : Try <span id='T8_try'>0</span><br /><span id='T8_status'></span></div>\n";

	$x = 1;
	do {
		echo "<script type='text/javascript'>document.getElementById('T8_try').innerHTML = '$x';</script>\n";

		sleep($x);
		$uploadInfo = ApiReq('file/upload_info', array('upload_id' => $preUpload['upload_id']));
		if (empty($uploadInfo['upload'])) html_error('Empty uploadInfo Response.');
		$uploadInfo = $uploadInfo['upload'];

		echo "<script type='text/javascript'>document.getElementById('T8_status').innerHTML += '" . htmlspecialchars($uploadInfo['state_label'], ENT_QUOTES) . "<br />';</script>\n";
	} while ($x++ < 10 && $uploadInfo['state'] == 1);

	if ($uploadInfo['state'] != 2 && $uploadInfo['state_label'] != 'Done') {
		html_error(sprintf('Invalid uploadInfo State: [%d] %s', $uploadInfo['state'], htmlspecialchars($uploadInfo['state_label'], ENT_QUOTES)));
	}

	if (empty($uploadInfo['file']['url'])) html_error('Error: File URL not found.');
	$download_link = $uploadInfo['file']['url'];
}

function ul_GetPage($link, $cookie = 0, $post = 0, $referer = 0, $auth = 0, $XMLRequest = 0) {
	if (!$referer && !empty($GLOBALS['Referer'])) {
		$referer = $GLOBALS['Referer'];
	}

	if ($GLOBALS['use_curl']) {
		if ($XMLRequest) $referer .= "\r\nX-Requested-With: XMLHttpRequest";
		$page = cURL($link, $cookie, $post, $referer, $auth);
	} else {
		global $pauth;
		$Url = parse_url($link);
		$page = geturl($Url['host'], defport($Url), $Url['path'] . (!empty($Url['query']) ? '?' . $Url['query'] : ''), $referer, $cookie, $post, 0, !empty($_GET['proxy']) ? $_GET['proxy'] : '', $pauth, $auth, $Url['scheme'], 0, $XMLRequest);
		is_page($page);
	}
	return $page;
}

function ApiReq($path, $post = array()) {
	if (!is_array($post)) $post = array();
	if ($path != 'user/login' && !empty($GLOBALS['login_json']['token'])) $post['token'] = $GLOBALS['login_json']['token'];

	$page = ul_GetPage("https://alfafile.net/api/v1/$path", 0, array_map('urlencode', $post), 'https://alfafile.net/');
	$reply = json2array($page, "ApiReq($path) Error");

	if (empty($reply['response']) && !empty($reply['details'])) html_error("[ApiReq($path) Error] " . htmlspecialchars($reply['details'], ENT_QUOTES));

	return $reply['response'];
}

function json2array($content, $errorPrefix = 'Error') {
	if (!function_exists('json_decode')) html_error('Error: Please enable JSON in php.');
	if (empty($content)) return NULL;
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

//[24-12-2015]  Written by Th3-822.

?>