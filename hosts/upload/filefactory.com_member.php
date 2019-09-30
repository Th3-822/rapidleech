<?php
######## Account Info ########
$upload_acc['filefactory_com']['user'] = ''; //Set your email
$upload_acc['filefactory_com']['pass'] = ''; //Set your password
########################

$_GET['proxy'] = isset($_GET['proxy']) ? $_GET['proxy'] : '';
$not_done = true;

if (!empty($upload_acc['filefactory_com']['user']) && !empty($upload_acc['filefactory_com']['pass'])) {
	$default_acc = true;
	$_REQUEST['up_login'] = $upload_acc['filefactory_com']['user'];
	$_REQUEST['up_pass'] = $upload_acc['filefactory_com']['pass'];
	$_REQUEST['action'] = '_T8_';
	echo "<b><center>Using Default Login.</center></b>\n";
} else $default_acc = false;

if (empty($_REQUEST['action']) || $_REQUEST['action'] != '_T8_') {
	echo "<table border='0' style='width:270px;' cellspacing='0' align='center'>
	<form method='POST'>
	<input type='hidden' name='action' value='_T8_' />
	<tr><td style='white-space:nowrap;'>&nbsp;EMail*</td><td>&nbsp;<input type='text' name='up_login' value='' style='width:160px;' /></td></tr>
	<tr><td style='white-space:nowrap;'>&nbsp;Password*</td><td>&nbsp;<input type='password' name='up_pass' value='' style='width:160px;' /></td></tr>\n";
	echo "<tr><td colspan='2' align='center'><br /><input type='submit' value='Upload' /></td></tr>\n";
	echo "<tr><td colspan='2' align='center'><small>*You can set it as default in <b>".basename(__FILE__)."</b></small></td></tr>\n";
	echo "</form>\n</table>\n";
} else {
	$login = $not_done = false;
	$domain = 'www.filefactory.com';
	$referer = "http://$domain/";

	// Login
	echo "<table style='width:600px;margin:auto;'>\n<tr><td align='center'>\n<div id='login' width='100%' align='center'>Login to $domain</div>\n";

	$cookie = array('ff_locale' => 'en_US.utf8');
	if (!empty($_REQUEST['up_login']) && !empty($_REQUEST['up_pass'])) {
		if (!empty($_REQUEST['A_encrypted'])) {
			$_REQUEST['up_login'] = decrypt(urldecode($_REQUEST['up_login']));
			$_REQUEST['up_pass'] = decrypt(urldecode($_REQUEST['up_pass']));
			unset($_REQUEST['A_encrypted']);
		}

		$post = array();
		$post['loginEmail'] = urlencode($_REQUEST['up_login']);
		$post['loginPassword'] = urlencode($_REQUEST['up_pass']);
		$post['Submit'] = 'Sign+In';
		$page = geturl($domain, 80, '/member/signin.php', "$referer/member/signin.php", $cookie, $post, 0, $_GET['proxy'], $pauth);is_page($page);

		is_present($page, 'The Email Address submitted was invalid', 'Login Failed: Invalid email address.');
		is_present($page, 'The email address or password you have entered is incorrect.', 'Login Failed: The Email/Password you have entered is incorrect.');

		$cookie = GetCookiesArr($page, $cookie);
		if (empty($cookie['auth'])) html_error('Login Failed, auth cookie not found.');

		// Check account messages
		$page = geturl($domain, 80, '/account/', "$referer/member/signin.php", $cookie, 0, 0, $_GET['proxy'], $pauth);is_page($page);
		is_present($page, "\nLocation: /member/settos.php", 'TOS have changed and need to be approved at the site.');
		is_present($page, "\nLocation: /member/setpwd.php", 'Your password has expired, please change it.');

		$login = true;
	} else html_error('Login failed: User/Password empty.');

	// Retrieve upload ID
	echo "<script type='text/javascript'>document.getElementById('login').style.display='none';</script>\n<div id='info' width='100%' align='center'>Retrieving upload ID</div>\n";

	$post = array();
	$post['cookie'] = rawurldecode($cookie['auth']);

	$up_loc = 'http://upload.filefactory.com/upload';
	$up_loc .= '-beta.php'; // Old Url

	// Uploading
	echo "<script type='text/javascript'>document.getElementById('info').style.display='none';</script>\n";

	$url = parse_url($up_loc);
	$upfiles = upfile($url['host'], defport($url), $url['path'] . (!empty($url['query']) ? '?' . $url['query'] : ''), "$referer/upload/", 0, $post, $lfile, $lname, 'Filedata', '', $_GET['proxy'], $pauth, 0, $url['scheme']);

	// Upload Finished
	echo "<script type='text/javascript'>document.getElementById('progressblock').style.display='none';</script>";

	is_page($upfiles);

	if (!preg_match('@\s(\w+)\s*$@i', $upfiles, $uid)) html_error('Upload ID not found.');
	$page = geturl($domain, 80, '/upload/results.php?files='.$uid[1], "$referer/upload/", $cookie, 0, 0, $_GET['proxy'], $pauth);is_page($page);

	if (preg_match('@(?<=/)file/\w+(/[^\r\n\"\'<>\s\t]+)?@i', $page, $dl)) {
		$download_link = $referer.$dl[0];
	} else html_error("Download link not found. (ID: {$uid[1]})");
}

//[17-6-2011]  Written by Th3-822.
//[15-9-2013]  Rewritten and fixed for new FF site & Removed anon user support. - Th3-822
//[24-10-2013] Added a error at login. - Th3-822
//[02-12-2016] Fixed login error msgs & revised plugin. - Th3-822

?>