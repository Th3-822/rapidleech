<?php
######## Account Info ########
$upload_acc['1fichier_com']['user'] = ''; //Set your login
$upload_acc['1fichier_com']['pass'] = ''; //Set your password
########################

$_GET['proxy'] = isset($_GET['proxy']) ? $_GET['proxy'] : '';
$not_done = true;

if ($upload_acc['1fichier_com']['user'] && $upload_acc['1fichier_com']['pass']) {
	$default_acc = true;
	$_REQUEST['up_login'] = $upload_acc['1fichier_com']['user'];
	$_REQUEST['up_pass'] = $upload_acc['1fichier_com']['pass'];
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
	$domain = '1fichier.com';
	$referer = "https://$domain/";

	// Login
	echo "<table style='width:600px;margin:auto;'>\n<tr><td align='center'>\n<div id='login' width='100%' align='center'>Login to $domain</div>\n";

	$cookie = array('LG' => 'en');
	if (!empty($_REQUEST['up_login']) && !empty($_REQUEST['up_pass'])) {
		if (!empty($_REQUEST['A_encrypted'])) {
			$_REQUEST['up_login'] = decrypt(urldecode($_REQUEST['up_login']));
			$_REQUEST['up_pass'] = decrypt(urldecode($_REQUEST['up_pass']));
			unset($_REQUEST['A_encrypted']);
		}

		$post = array();
		$post['mail'] = urlencode($_REQUEST['up_login']);
		$post['pass'] = urlencode($_REQUEST['up_pass']);
		$post['lt'] = 'on';
		$post['restrict'] = 'on';
		$post['valider'] = 'Send';

		$page = geturl($domain, 443, '/login.pl', $referer, $cookie, $post, 0, 0, 0, 0, 'https');is_page($page);
		$cookie = GetCookiesArr($page, $cookie);

		is_present($page, 'Invalid email address');
		is_present($page, 'Invalid username or password', 'Login Failed: Email/Password incorrect.');

		$page = geturl($domain, 443, '/', $referer.'login.pl', $cookie, 0, 0, 0, 0, 0, 'https');is_page($page);
		is_notpresent($page, 'logout.pl">Logout', 'Login Error.');

		$login = true;
	} else {
		echo "<b><center>Login not found or empty, using non member upload.</center></b>\n";
		$login = false;
	}

	// Retrive upload ID
	echo "<script type='text/javascript'>document.getElementById('login').style.display='none';</script>\n<div id='info' width='100%' align='center'>Retrieving upload ID</div>\n";

	if (!$login) {
		$page = geturl($domain, 443, '/', $referer, $cookie, 0, 0, 0, 0, 0, 'https');is_page($page);
		$cookie = GetCookiesArr($page, $cookie);
	}

	if (!preg_match('@action="((https?://(?:\w+\.)*1fichier\.com)?(/)?upload\.cgi\?id=(?:[^\"\'\s<>]+))"@i', $page, $up)) html_error('Error: Upload url not found.');

	$post = array();
	$post['domain'] = '0';
	$post['message'] = $post['mails'] = $post['user'] = $post['dpass'] = $post['mail'] = '';
	if ($login) $post['did'] = '0';
	$post['submit'] = 'Send';

	$up_url = (empty($up[2]) ? "https://$domain".(empty($up[3]) ? '/' : '').$up[1] : $up[1]);

	// Uploading
	echo "<script type='text/javascript'>document.getElementById('info').style.display='none';</script>\n";

	$url = parse_url($up_url);
	$upfiles = upfile($url['host'], defport($url), $url['path'].(!empty($url['query']) ? '?'.$url['query'] : ''), $referer, $cookie, $post, $lfile, $lname, 'file[]', '', 0, 0, 0, $url['scheme']);

	// Upload Finished
	echo "<script type='text/javascript'>document.getElementById('progressblock').style.display='none';</script>\n";

	is_page($upfiles);

	if (!preg_match('@\nLocation: ((https?://(?:\w+\.)*1fichier\.com)?/end\.pl\?\w?id=(?:[^\"\'\s<>]+))@i', $upfiles, $end_url)) html_error('Error: Post Upload url not found.');

	$end_url = parse_url(empty($end_url[2]) ? (empty($up[2]) ? "https://$domain" : $up[2]) . $end_url[1] : $end_url[1]);

	$page = geturl($end_url['host'], defport($end_url), $end_url['path'].(!empty($end_url['query']) ? '?'.$end_url['query'] : ''), $up_url, $cookie, 0, 0, 0, 0, 0, $end_url['scheme']);is_page($page);

	if (!preg_match('@https?://(?:\w+\.)*1fichier\.com/remove/([\w\-]+)/[^\s<>\"\']+@i', $page, $lnk)) html_error('Download link not found.');
	$download_link = $referer . '?' . $lnk[1];
	$delete_link = $lnk[0];
}

//[18-3-2015]  Written by Th3-822.

?>
