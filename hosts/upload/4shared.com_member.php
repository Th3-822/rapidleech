<?php
######## Account Info ########
$upload_acc['4shared_com']['user'] = ''; //Set your login
$upload_acc['4shared_com']['pass'] = ''; //Set your password
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

if ($upload_acc['4shared_com']['user'] && $upload_acc['4shared_com']['pass']) {
	$default_acc = true;
	$_REQUEST['up_login'] = $upload_acc['4shared_com']['user'];
	$_REQUEST['up_pass'] = $upload_acc['4shared_com']['pass'];
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
	$domain = 'www.4shared.com';
	$referer = "https://$domain/";

	$home_url = $referer . 'account/home.jsp';

	// Login
	echo "<table style='width:600px;margin:auto;'>\n<tr><td align='center'>\n<div id='login' width='100%' align='center'>Login to $domain</div>\n";

	$cookie = array('4langcookie' => 'en');
	if (!empty($_REQUEST['up_login']) && !empty($_REQUEST['up_pass'])) {
		if (!empty($_REQUEST['A_encrypted'])) {
			$_REQUEST['up_login'] = decrypt(urldecode($_REQUEST['up_login']));
			$_REQUEST['up_pass'] = decrypt(urldecode($_REQUEST['up_pass']));
			unset($_REQUEST['A_encrypted']);
		}

		$post = array();
		$post['login'] = urlencode($_REQUEST['up_login']);
		$post['password'] = urlencode($_REQUEST['up_pass']);
		$post['returnto'] = urlencode($home_url);
		$post['_remember'] = $post['remember'] = 'on';

		$page = ul_GetPage($referer . 'web/login', $cookie, $post, $referer);
		is_present($page, 'Invalid e-mail address or password', 'Login Failed: Email/Password incorrect.');

		$cookie = GetCookiesArr($page, $cookie);
		if (empty($cookie['Login']) || empty($cookie['Password'])) html_error('Login Error.');

		$login = true;
	} else {
		html_error('Login Failed: Login/Password empty.');
	}

	// Retrive upload ID
	echo "<script type='text/javascript'>document.getElementById('login').style.display='none';</script>\n<div id='info' width='100%' align='center'>Retrieving upload ID</div>\n";

	$page = ul_GetPage($referer . 'account/home.jsp', $cookie, 0, $referer);
	$cookie = GetCookiesArr($page, $cookie);

	if (!preg_match('@dc_path\s*:\s*[\'\"](https?://dc\d+\.4shared\.com)[\'\"]@i', $page, $up_server)) html_error('Upload Server Not Found.');
	$up_server = $up_server[1];
	if (!preg_match('@(?:id="jsRootFolderIdForCurrentUser"|class="jsRootId") value="([\w\-\.]+)"@i', $page, $folder_id)) html_error('Root Folder ID Not Found.');
	$folder_id = urlencode($folder_id[1]);

	$up_url = "$up_server/main/upload.jsp?x-upload-dir=$folder_id";

	// Uploading
	echo "<script type='text/javascript'>document.getElementById('info').style.display='none';</script>\n";
	$url = parse_url($up_url);
	$upfiles = upfile($url['host'], defport($url), $url['path'].(!empty($url['query']) ? '?'.$url['query'] : ''), $referer . 'account/home.jsp', $cookie, array(), $lfile, $lname, 'tfff0', '', $_GET['proxy'], $pauth, 0, $url['scheme']);

	// Upload Finished
	echo "<script type='text/javascript'>document.getElementById('progressblock').style.display='none';</script>\n";

	is_page($upfiles);

	if (!preg_match('@https?://(?:www\.)?4shared\.com/\w+/[\w\-\.]+/[^\r\n\"\'<>]+\.html?@i', $upfiles, $download_link)) {
		if (preg_match('@id="alert"\s+value=\"([^\"\r\n<>]+)\"@i', $upfiles, $err)) html_error('Upload Error: ' . htmlspecialchars($err[1]));
		is_notpresent($upfiles, 'Your upload has successfully completed!', 'Upload Failed.');
		html_error('Download-Link not Found.');
	}

	$download_link = $download_link[0];
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

//[11-11-2015] ReWritten by Th3-822.
//[13-11-2015] Fixed Link Regexp. - Th3-822

?>