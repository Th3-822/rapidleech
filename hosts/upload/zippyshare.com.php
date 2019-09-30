<?php
######## Account Info ########
$upload_acc['zippyshare_com']['user'] = ''; //Set your userid/alias
$upload_acc['zippyshare_com']['pass'] = ''; //Set your password
########################

$_GET['proxy'] = isset($_GET['proxy']) ? $_GET['proxy'] : '';
$not_done = true;
$continue_up = false;

if ($upload_acc['zippyshare_com']['user'] && $upload_acc['zippyshare_com']['pass']) {
	$_REQUEST['up_login'] = $upload_acc['zippyshare_com']['user'];
	$_REQUEST['up_pass'] = $upload_acc['zippyshare_com']['pass'];
	$_REQUEST['up_private'] = false;
	$_REQUEST['action'] = 'FORM';
	echo "<b><center>Using Default Login.</center></b>\n";
}

if (!empty($_REQUEST['action']) && $_REQUEST['action'] == 'FORM') $continue_up = true;
else {
	echo "<table border='0' style='width:270px;' cellspacing='0' align='center'>
	<form method='POST'>
	<input type='hidden' name='action' value='FORM' />
	<tr><td style='white-space:nowrap;'>&nbsp;User*</td><td>&nbsp;<input type='text' name='up_login' value='' style='width:160px;' /></td></tr>
	<tr><td style='white-space:nowrap;'>&nbsp;Password*</td><td>&nbsp;<input type='password' name='up_pass' value='' style='width:160px;' /></td></tr>\n";
	echo "<tr><td colspan='2' align='center'><br />Upload options *<br /></td></tr>\r\n<tr><td colspan='2' align='center'><input type='checkbox' name='up_private' value='true' />&nbsp; Set as Private Upload</td></tr>";
	echo "<tr><td colspan='2' align='center'><br /><input type='submit' value='Upload' /></td></tr>\n";
	echo "<tr><td colspan='2' align='center'><small>*You can set it as default in <b>".basename(__FILE__)."</b></small></td></tr>\n";
	echo "</form>\n</table>\n";
}

if ($continue_up) {
	$not_done = false;
	$domain = 'zippyshare.com';
	$referer = "https://$domain/";

	// Login
	echo "<table style='width:600px;margin:auto;'>\n<tr><td align='center'>\n<div id='login' width='100%' align='center'>Login to zippyshare.com</div>\n";

	$cookie = array('ziplocale' => 'en');
	if (!empty($_REQUEST['up_login']) && !empty($_REQUEST['up_pass'])) {
		$post = array();
		$post['login'] = $_REQUEST['up_login'];
		$post['pass'] = $_REQUEST['up_pass'];
		$post['remember'] = 'on';

		$page = geturl($domain, 443, '/services/login', $referer, $cookie, $post, 0, $_GET['proxy'], $pauth, 0, 'https');is_page($page);
		is_present($page, '?invalid=1', 'Login failed: User/Password incorrect.');
		$cookie = GetCookiesArr($page, $cookie);
		if (empty($cookie['zipname']) && empty($cookie['ziphash'])) html_error('Error: Login cookies not found.');
		$login = true;
	} else {
		echo "<b><center>Login not found or empty, using non member upload.</center></b>\n";
		$login = false;
	}

	// Retrieve upload ID
	echo "<script type='text/javascript'>document.getElementById('login').style.display='none';</script>\n<div id='info' width='100%' align='center'>Retrive upload ID</div>\n";

	$page = geturl($domain, 443, '/', $referer, $cookie, 0, 0, $_GET['proxy'], $pauth, 0, 'https');is_page($page);

	if (!preg_match('@(?<=//|\')www\d+(?=\.zippyshare\.com/upload|\';)@i', $page, $up)) html_error('Error: Cannot find upload server.');

	$post = array();
	$post['name'] = $lname;
	if ($login) {
		$post['zipname'] = $cookie['zipname'];
		$post['ziphash'] = $cookie['ziphash'];
	}
	$post['embPlayerValues'] = (!empty($cookie['embed-player-values']) ? $cookie['embed-player-values'] : 'false');
	if (!empty($_REQUEST['up_private'])) $post['private'] = 'true';
	else $post['notprivate'] = 'true';

	$up_url = sprintf('https://%s.zippyshare.com/upload', $up[0]);

	// Uploading
	echo "<script type='text/javascript'>document.getElementById('info').style.display='none';</script>\n";

	$url = parse_url($up_url);
	$upfiles = upfile($url['host'], defport($url), $url['path'].(!empty($url['query']) ? '?'.$url['query'] : ''), $referer, $cookie, $post, $lfile, $lname, 'Filedata', '', $_GET['proxy'], $pauth, 0, $url['scheme']);

	// Upload Finished
	echo "<script type='text/javascript'>document.getElementById('progressblock').style.display='none';</script>\n";

	is_page($upfiles);

	if (preg_match('@https?://www\d*\.zippyshare\.com/v/\w+/file\.html@i', $upfiles, $link)) $download_link = $link[0];
	else html_error('Download link not found.');

}

//[17-5-2013] Written by Th3-822.
//[28-9-2014] Added private upload option. - Th3-822
//[11-1-2015] Fixed upload & Link regexp. (Happy new year) - Th3-822
//[24-6-2018] Switched to https & small changes. - Th3-822