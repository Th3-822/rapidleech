<?php

####### Account Info. ###########
$upload_acc['filedude.com']['user'] = ""; //Set your email
$upload_acc['filedude_com']['pass'] = ""; //Set your password
##############################

$not_done = true;
$continue_up = false;

if ($upload_acc['filedude_com']['user'] && $upload_acc['filedude_com']['pass']) {
	$_REQUEST['up_login'] = $upload_acc['filedude_com']['user'];
	$_REQUEST['up_pass'] = $upload_acc['filedude_com']['pass'];
	$_REQUEST['action'] = "FORM";
	echo "<p style='text-align:center;font-weight:bold;'>Using Default Login and Pass.</p>\n";
}

if ($_REQUEST['action'] == "FORM") $continue_up=true;
else {
	echo "<table border='0' style='width:270px;' cellspacing='0' align='center'>
	<form method='POST'>
	<input type='hidden' name='action' value='FORM' />
	<tr><td style='white-space:nowrap;'>&nbsp;Email*</td><td>&nbsp;<input type='text' name='up_login' value='' style='width:160px;' /></td></tr>
	<tr><td style='white-space:nowrap;'>&nbsp;Password*</td><td>&nbsp;<input type='password' name='up_pass' value='' style='width:160px;' /></td></tr>\n";
	echo "\t<tr><td colspan='2' align='center'><br /><input type='submit' value='Upload' /></td></tr>
	<tr><td colspan='2' align='center'><small>*You can set it as default in <b>{$page_upload["filedude.com_member"]}</b></small></td></tr>\n</table>\n</form>\n";
}

if ($continue_up) {
	$not_done = $login = false;
	// Login
	echo "<table style='width:600px;margin:auto;'>\n<tr><td align='center'>\n<div id='login' width='100%' align='center'>Login to FileDude</div>\n";

	$cookie = 0;
	if (!empty($_REQUEST['up_login']) && !empty($_REQUEST['up_pass'])) {
		$post = array();
		$post["email"] = $_REQUEST['up_login'];
		$post["pass"] = $_REQUEST['up_pass'];

		$page = geturl("www.filedude.com", 80, "/login", 0, $cookie, $post);is_page($page);

		is_present($page, "Please check to make sure you entered", "Login Failed: The email/password entered are incorrect.");
		$cookie = GetCookies($page);
		is_notpresent($cookie, "uploader_email=", "Login Failed: Email cookie not found.");
		is_notpresent($cookie, "uploader_pass=", "Login Failed: Auth cookie not found");
		$login = true;
	} else {
		echo "<b><center>Login not found or empty, using non member upload.</center></b>\n";
	}

	// Retrive upload ID
	echo "<script type='text/javascript'>document.getElementById('login').style.display='none';</script>\n<div id='info' width='100%' align='center'>Retrive upload ID</div>\n";

	$page = geturl("www.filedude.com", 80, "/", 0, $cookie);is_page($page);

	if ($login) {
		$post = array();
		$post['uploader_email'] = cut_str("$cookie;", 'uploader_email=', ';');
		$post['uploader_pass'] = cut_str("$cookie;", 'uploader_pass=', ';');
	} else $post = 0;

	if (!preg_match("@http://(\w+\.)?\w+\.filedude\.com/upload@i", $page, $up_loc)) html_error("Upload server not found.", 0);
	$up_loc = $up_loc[0];

	// Uploading
	echo "<script type='text/javascript'>document.getElementById('info').style.display='none';</script>\n";

	$url = parse_url($up_loc);
	$upfiles = upfile($url["host"],$url["port"] ? $url["port"] : 80, $url["path"].($url["query"] ? "?".$url["query"] : ""), "http://www.filedude.com/", $cookie, $post, $lfile, $lname, "Filedata");

	// Upload Finished
	echo "<script type='text/javascript'>document.getElementById('progressblock').style.display='none';</script>";

	is_page($upfiles);
	if (!preg_match('@http://(www\.)?filedude\.com/download/\w+@i', $upfiles, $dl)) html_error("Download link not found.", 0);
	$download_link = $dl[0];
}

//[08-7-2011]  Written by Th3-822.

?>