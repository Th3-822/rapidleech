<?php
######## Account Info ########
$upload_acc['ge_tt']['user'] = ''; //Set your login
$upload_acc['ge_tt']['pass'] = ''; //Set your password
########################

$_GET['proxy'] = isset($_GET['proxy']) ? $_GET['proxy'] : '';
$not_done = true;

if ($upload_acc['ge_tt']['user'] && $upload_acc['ge_tt']['pass']) {
	$default_acc = true;
	$_REQUEST['up_login'] = $upload_acc['ge_tt']['user'];
	$_REQUEST['up_pass'] = $upload_acc['ge_tt']['pass'];
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
	$domain = 'ge.tt';
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

		$post = array();
		$post['email'] = $_REQUEST['up_login'];
		$post['password'] = $_REQUEST['up_pass'];
		$post['autologin'] = false;

		if (!function_exists('json_encode')) html_error('Error: Please enable JSON in php.');

		$page = geturl($domain, 80, '/u/login?t='.jstime(), $referer, $cookie, json_encode($post), 0, $_GET['proxy'], $pauth);is_page($page);
		//$cookie = GetCookiesArr($page, $cookie);

		is_present($page, 'User not found', 'Invalid email address or user doesn\'t exists.');
		is_present($page, 'Wrong email or password', 'Login Failed: Email/Password incorrect.');

		$userInfo = json2array($page, 'Login Error');
		if ($fsize > $userInfo['storage']['free']) html_error('You don\'t have the enough free space in your account for upload this file.');

		$login = true;
	} else {
		echo "<b><center>Login not found or empty, using non member upload.</center></b>\n";
		$login = false;

		// Anon Login
		$page = geturl('ge.tt', 80, '/', $referer, $cookie, 0, 0, $_GET['proxy'], $pauth);is_page($page);
		$anonCookie = GetCookiesArr($page, $cookie);
		if (empty($anonCookie['session']) || !preg_match('@"accesstoken"\s*:\s*"([^\"]+)"@i', urldecode($anonCookie['session']), $anonToken)) html_error('Anonymous Session not Found.');
		$anonToken = $anonToken[1];

		// Get Anon User Info
		$page = geturl('open.ge.tt', 80, '/1/users/me?accesstoken='.urlencode($anonToken).'&t='.jstime(), $referer, $cookie, 0, 0, $_GET['proxy'], $pauth);is_page($page);
		$userInfo = json2array($page, 'Anon Login Error');
		$userInfo['accesstoken'] = $anonToken;
		if ($fsize > $userInfo['storage']['free']) html_error('You don\'t have the enough free space in your anon account for upload this file.');
	}

	// Retrive upload ID
	echo "<script type='text/javascript'>document.getElementById('login').style.display='none';</script>\n<div id='info' width='100%' align='center'>Retrieving upload ID</div>\n";

	// Create Share
	$page = geturl('open.ge.tt', 80, '/1/shares/create?accesstoken='.urlencode($userInfo['accesstoken']).'&t='.jstime(), $referer, $cookie, '', 0, $_GET['proxy'], $pauth);is_page($page);
	$share = json2array($page, 'Create Share Error');
	if (empty($share['sharename'])) html_error('Cannot get Share ID');

	// Create File
	$post = array('filename' => $lname);
	$page = geturl('open.ge.tt', 80, '/1/files/'.urlencode($share['sharename']).'/create?accesstoken='.urlencode($userInfo['accesstoken']).'&t='.jstime(), $referer, $cookie, json_encode($post), 0, $_GET['proxy'], $pauth);is_page($page);
	$file = json2array($page, 'Create File Error');
	if (empty($file['getturl'])) html_error('Download link not found.');
	if (empty($file['upload']['posturl'])) html_error('Upload URL not Found.'); // posturl		puturl

	// Uploading
	echo "<script type='text/javascript'>document.getElementById('info').style.display='none';</script>\n";
	$url = parse_url($file['upload']['posturl']); // .'&nounce='.exports_uuid()
	$upfiles = upfile($url['host'], defport($url), $url['path'].(!empty($url['query']) ? '?'.$url['query'] : ''), $file['getturl'], $cookie, array(), $lfile, $lname, 'blob', '', $_GET['proxy'], $pauth, 0, $url['scheme']);
	//$upfiles = putfile($url['host'], defport($url), $url['path'].(!empty($url['query']) ? '?'.$url['query'] : ''), $file['getturl'], $cookie, $lfile, $lname, $_GET['proxy'], $pauth, 0, $url['scheme']);

	// Upload Finished
	echo "<script type='text/javascript'>document.getElementById('progressblock').style.display='none';</script>\n";

	is_page($upfiles);

	if (stripos($upfiles, 'computer says yes') === false) {
		textarea($upfiles);
		html_error('Unknown Upload Error.');
	}

	$download_link = $file['getturl'];
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

function exports_uuid() {
	$ALPHA = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
	$uuid = '';
	for ($i = 0; $i < 36; $i++) {
		$uuid .= $ALPHA[mt_rand(0, 61)];
	}
	return $uuid . '-' . $ALPHA[mt_rand(0, 61)];
}

//[16-8-2015]  Written by Th3-822.

?>