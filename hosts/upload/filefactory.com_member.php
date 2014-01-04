<?php

####### Account Info. ###########
$upload_acc['filefactory_com']['user'] = ""; //Set your email
$upload_acc['filefactory_com']['pass'] = ""; //Set your password
##############################

$not_done = true;

if (!empty($upload_acc['filefactory_com']['user']) && !empty($upload_acc['filefactory_com']['pass'])) {
	$_REQUEST['up_login'] = $upload_acc['filefactory_com']['user'];
	$_REQUEST['up_pass'] = $upload_acc['filefactory_com']['pass'];
	$_REQUEST['action'] = '_T8_';
	echo "<b><center>Using Default Login.</center></b>\n";
}

if (empty($_REQUEST['action']) || $_REQUEST['action'] != '_T8_') {
	echo "<table border='0' style='width:270px;' cellspacing='0' align='center'>\n<form method='POST'>\n\t<input type='hidden' name='action' value='_T8_' />\n\t<tr><td style='white-space:nowrap;'>&nbsp;Email*</td><td>&nbsp;<input type='text' name='up_login' value='' style='width:160px;' /></td></tr>\n\t<tr><td style='white-space:nowrap;'>&nbsp;Password*</td><td>&nbsp;<input type='password' name='up_pass' value='' style='width:160px;' /></td></tr>\n";
	echo "\t<tr><td colspan='2' align='center'><br /><input type='submit' value='Upload' /></td></tr>\n";
	echo "\t<tr><td colspan='2' align='center'><small>*You can set it as default in <b>".basename(__FILE__)."</b></small></td></tr>\n";
	echo "</form>\n</table>\n";
} else {
	$not_done = $login = false;
	$domain = 'www.filefactory.com';
	$referer = "http://$domain";

	// Login
	echo "<table style='width:600px;margin:auto;'>\n<tr><td align='center'>\n<div id='login' width='100%' align='center'>Login to FileFactory</div>\n";

	$cookie = array('ff_locale'=>'en_US.utf8');
	if (!empty($_REQUEST['up_login']) && !empty($_REQUEST['up_pass'])) {
		$post = array();
		$post['loginEmail'] = urlencode($_REQUEST['up_login']);
		$post['loginPassword'] = urlencode($_REQUEST['up_pass']);
		$post['Submit'] = 'Sign+In';
		$page = geturl($domain, 80, '/member/signin.php', "$referer/member/signin.php", $cookie, $post);is_page($page);

		is_present($page, 'The Email Address submitted was invalid', 'Login Failed: Invalid email address.');
		is_present($page, 'The email or password wre invalid', 'Login Failed: The Email/Password you have entered is incorrect.');
		is_present($page, 'The email or password were invalid', 'Login Failed: The Email/Password you have entered is incorrect.');
		is_present($page, "\nLocation: /member/setpwd.php", 'Your password has expired, please change it.');

		$cookie = GetCookiesArr($page, $cookie);
		if (empty($cookie['auth'])) html_error('Login Failed, auth cookie not found.');
		$login = true;
	} else html_error('Login failed: User/Password empty.');

	// Retrive upload ID
	echo "<script type='text/javascript'>document.getElementById('login').style.display='none';</script>\n<div id='info' width='100%' align='center'>Retrive upload ID</div>\n";

	$post = array();
	$post['cookie'] = rawurldecode($cookie['auth']);

	$up_loc = 'http://upload.filefactory.com/upload-beta.php';

	// Uploading
	echo "<script type='text/javascript'>document.getElementById('info').style.display='none';</script>\n";

	$url = parse_url($up_loc);
	$upfiles = upfile('upload.filefactory.com', 80, '/upload-beta.php', "$referer/upload/", 0, $post, $lfile, $lname, 'Filedata');

	// Upload Finished
	echo "<script type='text/javascript'>document.getElementById('progressblock').style.display='none';</script>";

	is_page($upfiles);

	if(!preg_match('@\s(\w+)\s*$@i', $upfiles, $uid)) html_error('Upload ID not found.');
	$page = geturl($domain, 80, '/upload/results.php?files='.$uid[1], "$referer/upload/", $cookie);is_page($page);

	if(!preg_match('@/file/\w+(/[^\r\n\"\'<>\s\t]+)?@i', $page, $dl)) html_error("Download link not found. (ID: {$uid[1]})");
	$download_link = $referer.$dl[0];
}

//[17-6-2011]  Written by Th3-822.
//[15-9-2013]  Rewritten and fixed for new FF site & Removed anon user support. - Th3-822
//[24-10-2013] Added a error at login. - Th3-822

?>