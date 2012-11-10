<?php
######### Account Info #########
$upload_acc['uploadhero_com']['user'] = ''; //Set your user
$upload_acc['uploadhero_com']['pass'] = ''; //Set your password
##########################

$_GET['proxy'] = !empty($proxy) ? $proxy : (!empty($_GET['proxy']) ? $_GET['proxy'] : '');
$not_done = true;

if ($upload_acc['uploadhero_com']['user'] && $upload_acc['uploadhero_com']['pass']) {
	$_REQUEST['up_login'] = $upload_acc['uploadhero_com']['user'];
	$_REQUEST['up_pass'] = $upload_acc['uploadhero_com']['pass'];
	$_REQUEST['action'] = 'FORM';
	echo "<b><center>Using Default Login.</center></b>\n";
}

if (empty($_REQUEST['action']) || $_REQUEST['action'] != 'FORM') {
	echo "<table border='0' style='width:270px;' cellspacing='0' align='center'>
	<form method='POST'>
	<input type='hidden' name='action' value='FORM' />
	<tr><td style='white-space:nowrap;'>&nbsp;User*</td><td>&nbsp;<input type='text' name='up_login' value='' style='width:160px;' /></td></tr>
	<tr><td style='white-space:nowrap;'>&nbsp;Password*</td><td>&nbsp;<input type='password' name='up_pass' value='' style='width:160px;' /></td></tr>\n";
	echo "<tr><td colspan='2' align='center'><br /><input type='submit' value='Upload' /></td></tr>\n";
	echo "<tr><td colspan='2' align='center'><small>*You can set it as default in <b>".basename(__FILE__)."</b></small></td></tr>\n";
	echo "</table>\n</form>\n";
} else {
	$not_done = false;
	$domain = 'uploadhero.com';
	$referer = "http://$domain/";

	// Login
	echo "<table style='width:600px;margin:auto;'>\n<tr><td align='center'>\n<div id='login' width='100%' align='center'>Login to $domain</div>\n";

	$cookie = array('lang' => 'en');
	if (!empty($_REQUEST['up_login']) && !empty($_REQUEST['up_pass'])) {
		$post = array();
		$post['pseudo_login'] = urlencode($_REQUEST['up_login']);
		$post['password_login'] = urlencode($_REQUEST['up_pass']);

		$page = geturl($domain, 80, '/lib/connexion.php', $referer, $cookie, $post, 0, $_GET['proxy'], $pauth);is_page($page);
		$cookie = GetCookiesArr($page, $cookie);

		is_present($page, 'Username or password invalid.', 'Login Failed: Invalid Username or Password.');

		if (!preg_match('@<div id="cookietransitload"[^>]*>([^<>]+)</div>@i', $page, $uh)) html_error('Login Error: Cannot find \'uh\' cookie.'); // Why in many sites at the code the site is called as "transitfiles.com"?
		$cookie['uh'] = urlencode(html_entity_decode($uh[1]));
		$cookie['lang'] = 'en';
		$login = true;
	} else {
		echo "<b><center>Login not found or empty, using non member upload.</center></b>\n";
		$login = false;
	}

	// Retrive upload ID
	echo "<script type='text/javascript'>document.getElementById('login').style.display='none';</script>\n<div id='info' width='100%' align='center'>Retrive upload ID</div>\n";

	$page = geturl($domain, 80, '/', $referer, $cookie, 0, 0, $_GET['proxy'], $pauth);is_page($page);
	$cookie = GetCookiesArr($page, $cookie);
	if (!preg_match('@[\t\s]upload_url[\t\s]*:[\t\s]*"(https?://[^/\"]+/[^\"]+)"@i', $page, $upload_url)) html_error('Error: Cannot find upload url.', 0);

	$post = array();
	$post['Filename'] = $lname;
	if ($login) {
		if (!preg_match('@"ID"[\t\s]*:[\t\s]*"([^\"]+)"@i', $page, $uid)) html_error('Error: UserID not found.');
		$post['ID'] = $uid[1];
	}
	$post['PHPSESSID'] = $cookie['PHPSESSID'];
	$post['Upload'] = 'Submit Query';

	$up_url = $upload_url[1];

	// Uploading
	echo "<script type='text/javascript'>document.getElementById('info').style.display='none';</script>\n";

	$url = parse_url($up_url);
	$upfiles = upfile($url['host'], 80, $url['path'].(!empty($url['query']) ? '?'.$url['query'] : ''), '', 0, $post, $lfile, $lname, 'Filedata', '', $_GET['proxy'], $pauth, 'Shockwave Flash');

	// Upload Finished
	echo "<script type='text/javascript'>document.getElementById('progressblock').style.display='none';</script>";

	is_page($upfiles);

	$body = trim(substr($upfiles, strpos($upfiles, "\r\n\r\n") + 4));

	$finds = array('%20', '%27', '%C3%A9', '%C3%A8', '%E2%82%AC', '%25', '%26');
	$replace = array('kkk', 'qspkq', 'ab12', 'ab21', 'eurobcd', 'pourcentbcd', 'uuuuuu');
	$name = str_replace($finds, $replace, rawurlencode($lname));

	$page = geturl($domain, 80, "/fileinfo.php?folder=&name=$name&size=$fsize", $referer, $cookie, 0, 0, $_GET['proxy'], $pauth);is_page($page);

	if (preg_match('@https?://(?:www\.)?uploadhero\.com/dl/[^\r\n\s\t<>\"\']+@i', $page, $dlnk)) {
		$download_link = $dlnk[0];
		if (preg_match('@https?://(?:www\.)?uploadhero\.com/delete/[^\r\n\s\t<>\"\']+@i', $page, $dellnk)) $delete_link = $dellnk[0];
	} elseif (strlen($body) == 8) $download_link = "http://uploadhero.com/dl/$body";
	else html_error('Download link not found.', 0);
}

// [06-11-2012] Written by Th3-822.

?>