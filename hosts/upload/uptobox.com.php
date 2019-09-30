<?php

if (!defined('RAPIDLEECH')) exit;
$_T8 = array('v' => 9); // Version of this config file. (Do Not Edit)

/* # Plugin's Settings # */
$_T8['domain'] = 'uptobox.com'; // May require the www. (Check first if the site adds the www.).
$_T8['anonUploadDisable'] = false; // Disallow non-registered users upload. (XFS Pro)
$_T8['anonUploadLimit'] = 1024; // File-size limit for non-registered users (MB) | 0 = Plugin's limit | (XFS Pro)

// Advanced Settings (Don't edit it unless you know what are you doing)
	$_T8['port'] = 443; // Server's port, default: 80 | 443 = https.
	$_T8['xfsFree'] = false; // Change to true if the host is using XFS free.
	$_T8['path'] = '/'; // URL path to XFS script, default: '/'
	$_T8['sslLogin'] = false; // Force https on login.
	$_T8['opUploadName'] = 'upload'; // Custom ?op=value for checking upload page, default: 'upload'
	$_T8['flashUpload'] = false; // Forces the use of flash upload method... Also filename for .cgi if it's a non empty string. (XFS Pro)
	$_T8['fw_sendLogin'] = 'SendLogin'; // Callable function

$acc_key_name = str_ireplace(array('www.', '.'), array('', '_'), $_T8['domain']); // (Do Not Edit)

/* # Account Info # */
$upload_acc[$acc_key_name]['user'] = ''; //Set your login
$upload_acc[$acc_key_name]['pass'] = ''; //Set your password

function SendLogin($post) {
	global $_T8, $cookie, $pauth;
	$page = geturl('uptobox.com', 443, '/?op=login&referer=homepage', 'https://uptobox.com/?op=login&referer=homepage', $cookie, $post, 0, 0, 0, 0, 'https'); // geturl doesn't support https proxy
	is_page($page);
	is_present($page, 'You are trying to log in from a different country', 'Login Failed: Login Blocked By IP, Check Account Email And Follow The Steps To Add IP to Whitelist.');
	return $page;
}

//if (!file_exists(HOST_DIR . 'upload/GenericXFSHost.inc.php')) html_error('Cannot load "'.htmlentities(HOST_DIR).'upload/GenericXFSHost.inc.php" (File doesn\'t exists), please install lastest version from: http://rapidleech.com/forum/viewtopic.php?f=17&t=80 or http://pastebin.com/E0z7qMU1 ');
//require(HOST_DIR . 'upload/GenericXFSHost.inc.php');

// Quick and dirty modified GenericXFSHost for quick upload fix:
/* # Default Settings # */
$default = array();
$default['port'] = 80; // Server's port, default: 80 | 443 = https.
$default['path'] = '/'; // URL path to XFS script, default: '/'
$default['xfsFree'] = false; // Change to true if the host is using XFS free.
$default['sslLogin'] = false; // Force https on login.
$default['opUploadName'] = 'upload'; // Custom ?op=value for checking upload page, default: 'upload'
$default['anonUploadDisable'] = false; // Disallow non registered users upload. (XFS Pro)
$default['anonUploadLimit'] = 0; // File-size limit for non registered users (MB) - 0 = Plugin's limit | (XFS Pro)
$default['flashUpload'] = false; // Forces the use of flash upload method... Also filename for .cgi if it's a non empty string. (XFS Pro)

$_T8 = array_merge($default, array_filter($_T8)); // Merge default settings with loader's settings

$_GET['proxy'] = isset($_GET['proxy']) ? $_GET['proxy'] : '';
$pauth = !empty($pauth) ? $pauth : false;
$not_done = true;

if (!$_T8['xfsFree'] && !empty($upload_acc[$acc_key_name]['user']) && !empty($upload_acc[$acc_key_name]['pass'])) {
	$_REQUEST['up_login'] = $upload_acc[$acc_key_name]['user'];
	$_REQUEST['up_pass'] = $upload_acc[$acc_key_name]['pass'];
	$_REQUEST['action'] = 'FORM';
	echo "<b><center>Using Default Login.</center></b>\n";
}

if (!$_T8['xfsFree'] && (empty($_REQUEST['action']) || $_REQUEST['action'] != 'FORM')) {
	echo "<table border='0' style='width:270px;' cellspacing='0' align='center'>\n<form method='POST'>\n\t<input type='hidden' name='action' value='FORM' />\n\t<tr><td style='white-space:nowrap;'>&nbsp;Username*</td><td>&nbsp;<input type='text' name='up_login' value='' style='width:160px;' /></td></tr>\n\t<tr><td style='white-space:nowrap;'>&nbsp;Password*</td><td>&nbsp;<input type='password' name='up_pass' value='' style='width:160px;' /></td></tr>\n";
	echo "\t<tr><td colspan='2' align='center'><br /><input type='submit' value='Upload' /></td></tr>\n";
	echo "\t<tr><td colspan='2' align='center'><small>*You can set it as default in <b>".$page_upload[$_REQUEST['uploaded']]."</b></small></td></tr>\n";
	echo "</form>\n</table>\n";
} else {
	$not_done = false;
	if (substr($_T8['path'], 0, 1) != '/') $_T8['path'] = '/'.$_T8['path'];
	if (substr($_T8['path'], -1) != '/') $_T8['path'] .= '/';
	$_T8['port'] = (!empty($_T8['port']) && $_T8['port'] > 0 && $_T8['port'] < 65536) ? (int)$_T8['port'] : 80;
	$scheme = ($_T8['port'] == 443) ? 'https' : 'http';
	$referer = $scheme.'://'.$_T8['domain'].$_T8['path'];

	// Login
	echo "<table style='width:600px;margin:auto;'>\n<tr><td align='center'>\n<div id='login' width='100%' align='center'>Login to ".str_ireplace('www.', '', $_T8['domain'])."</div>\n";

	$cookie = (!empty($cookie)) ? (is_array($cookie) ? $cookie : StrToCookies($cookie)) : array();
	$cookie['lang'] = 'english';
	if ($_T8['xfsFree']) $login = false;
	elseif (!empty($_REQUEST['up_login']) && !empty($_REQUEST['up_pass'])) {
		$post = array();
		$post['op'] = 'login';
		$post['redirect'] = '';
		$post['login'] = urlencode($_REQUEST['up_login']);
		$post['password'] = urlencode($_REQUEST['up_pass']);

		if (empty($_T8['fw_sendLogin']) || !is_callable($_T8['fw_sendLogin'])) {
			$page = geturl($_T8['domain'], $_T8['port'], $_T8['path'].'?op=login', $referer, $cookie, $post, 0, $_GET['proxy'], $pauth, 0, ($_T8['sslLogin'] ? 'https' : $scheme));is_page($page);
		} else $page = call_user_func($_T8['fw_sendLogin'], $post);
		$header = substr($page, 0, strpos($page, "\r\n\r\n"));
		if (stripos($header, "\nLocation: ") !== false && preg_match('@\nLocation: (https?://[^\r\n]+)@i', $header, $redir) && 'www.' . strtolower($_T8['domain']) == strtolower(parse_url($redir[1], PHP_URL_HOST))) html_error("Please set \$_T8['domain'] to 'www.{$_T8['domain']}'.");
		if (preg_match('@Incorrect ((Username)|(Login)) or Password@i', $page)) html_error('Login failed: User/Password incorrect.');
		is_present($page, 'op=resend_activation', 'Login failed: Your account isn\'t confirmed yet.');
		is_present($page, 'Please%20enter%20your%20e-mail', "Login failed: Missing account's email, login at site and set the email.");
		$cookie = GetCookiesArr($header, $cookie);
		if (empty($cookie['xfss']) && empty($cookie['login'])) html_error('Error: Login cookies not found.');
		$cookie['lang'] = 'english';
		$login = true;
	} else {
		if ($_T8['anonUploadDisable']) html_error('Login failed: User/Password empty.');
		echo "<b><center>Login not found or empty, using non member upload.</center></b>\n";
		if ($_T8['anonUploadLimit'] > 0 && $fsize > $_T8['anonUploadLimit']*1024*1024) html_error('File is too big for anon upload');
		$login = false;
	}

	// Retrive upload ID
	echo "<script type='text/javascript'>document.getElementById('login').style.display='none';</script>\n<div id='info' width='100%' align='center'>Retrive upload ID</div>\n";

	$page = geturl($_T8['domain'], $_T8['port'], $_T8['path'].'?op='.(empty($_T8['opUploadName']) ? 'upload' : $_T8['opUploadName']), $referer, $cookie, 0, 0, $_GET['proxy'], $pauth, 0, $scheme);is_page($page);
	if (substr($page, 9, 3) != '200') {
		$page = geturl($_T8['domain'], $_T8['port'], $_T8['path'], $referer, $cookie, 0, 0, $_GET['proxy'], $pauth, 0, $scheme);is_page($page);
	}
	$header = substr($page, 0, strpos($page, "\r\n\r\n"));
	if (!$login && stripos($header, "\nLocation: ") !== false && preg_match('@\nLocation: (https?://[^\r\n]+)@i', $header, $redir) && 'www.' . strtolower($_T8['domain']) == strtolower(parse_url($redir[1], PHP_URL_HOST))) html_error("Please set \$_T8['domain'] to 'www.{$_T8['domain']}'.");

	if (!preg_match('@action=["\'](https?://www\d+\.uptobox\.com/upload\?(?:\w+=\w+&)*sess_id=)@i', $page, $up_url)) html_error('Upload Server Not Found.');
	$up_url = $up_url[1];
	if ($login) $up_url .= $cookie['xfss'];

	// Uploading
	echo "<script type='text/javascript'>document.getElementById('info').style.display='none';</script>\n";

	$url = parse_url($up_url);
	$upfiles = upfile($url['host'], defport($url), $url['path'].(!empty($url['query']) ? '?'.$url['query'] : ''), 0, $cookie, array(), $lfile, $lname,
	'files[]', '', $_GET['proxy'], $pauth, 0, $url['scheme']);

	// Upload Finished
	echo "<script type='text/javascript'>document.getElementById('progressblock').style.display='none';</script>\n";

	is_page($upfiles);

	if (($pos = strpos($upfiles, "\r\n\r\n")) > 0) $reply = trim(substr($upfiles, $pos + 4));
	$reply = json_decode(trim($reply), true);

	if (empty($reply['files'][0]['url'])) html_error('Download Link Not Found.');
	$download_link = $reply['files'][0]['url'];
	if (!empty($reply['files'][0]['deleteUrl'])) $delete_link = $reply['files'][0]['deleteUrl'];
}

// Written by Th3-822