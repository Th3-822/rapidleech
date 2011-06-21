<?php

####### Account Info. ###########
$upload_acc['filefactory_com']['user'] = ""; //Set your email
$upload_acc['filefactory_com']['pass'] = ""; //Set your password
##############################

$not_done = true;
$continue_up = false;

if ($upload_acc['filefactory_com']['user'] && $upload_acc['filefactory_com']['pass']) {
	$_REQUEST['up_login'] = $upload_acc['filefactory_com']['user'];
	$_REQUEST['up_pass'] = $upload_acc['filefactory_com']['pass'];
	$_REQUEST['action'] = "FORM";
	echo "<b style='text-align: center;'>Using Default Login and Pass.</b>\n";
}

if ($_REQUEST['action'] == "FORM") $continue_up=true;
else {
	echo "<table border='0' style='width:270px;' cellspacing='0' align='center'>
	<form method='POST'>
	<input type='hidden' name='action' value='FORM' />
	<tr><td style='white-space:nowrap;'>&nbsp;Email*</td><td>&nbsp;<input type='text' name='up_login' value='' style='width:160px;' />&nbsp;</td></tr>
	<tr><td style='white-space:nowrap;'>&nbsp;Password*</td><td>&nbsp;<input type='password' name='up_pass' value='' style='width:160px;' />&nbsp;</td></tr>
	<tr><td colspan='2' align='center'><input type='submit' value='Upload' /></td></tr>
	<tr><td colspan='2' align='center'><small>*You can set it as default in <b>{$page_upload["filefactory.com_member"]}</b></small></td></tr>\n</table>\n</form>";
}

if ($continue_up) {
	$not_done = $login = false;
	// Login
	echo "<table style='width:600px;margin:auto;'>\n<tr><td align='center'>\n<div id='login' width='100%' align='center'>Login to FileFactory</div>\n";

	$cookie = "rPopHome=1";
	if (!empty($_REQUEST['up_login']) && !empty($_REQUEST['up_pass'])) {
		$post = array();
		$post["redirect"] = "/";
		$post["email"] = $_REQUEST['up_login'];
		$post["password"] = $_REQUEST['up_pass'];

		$page = geturl("www.filefactory.com", 80, "/member/login.php", 0, $cookie, $post);is_page($page);

		is_present($page, "?err=", "Login Failed: The email or password you have entered is incorrect.");
		$cookie = "$cookie; " . GetCookies($page);
		is_notpresent($cookie, "ff_membership=", "Login Failed.");
		$login = true;
	} else {
		echo "<b><center>Login not found or empty, using non member upload.</center></b>\n";
	}

	// Retrive upload ID
	echo "<script type='text/javascript'>document.getElementById('login').style.display='none';</script>\n<div id='info' width='100%' align='center'>Retrive upload ID</div>\n";

	$post = array();
	$post['Filename'] = $lname;
	if ($login) {
		$post['cookie'] = urldecode(cut_str("$cookie;", 'ff_membership=', ';'));
		$post['folderViewhash'] = 0;
	}
	$post['Upload'] = 'Submit Query';

	$up_loc = "http://upload.filefactory.com/upload.php";

	// Uploading
	echo "<script type='text/javascript'>document.getElementById('info').style.display='none';</script>\n";

	$url = parse_url($up_loc);
	$upfiles = upfile($url["host"],$url["port"] ? $url["port"] : 80, $url["path"].($url["query"] ? "?".$url["query"] : ""), 0, 0, $post, $lfile, $lname, "Filedata", '', 0, 0, "Shockwave Flash");

	// Upload Finished
	echo "<script type='text/javascript'>document.getElementById('progressblock').style.display='none';</script>";

	is_page($upfiles);

	if(!preg_match('@(\w+)$@i', $upfiles, $uid)) html_error("Upload ID not found.", 0);
	$page = geturl("www.filefactory.com", 80, "/file/complete.if.php/{$uid[1]}/", 'http://www.filefactory.com/upload/upload.if.php', $cookie);is_page($page);

	if(!preg_match('@/file/\w+/n/[^\'|"|<]+@i', $page, $dl)) html_error("Download link not found. (ID: {$uid[1]})", 0);
	$download_link = "http://www.filefactory.com{$dl[0]}";
}

//[17-6-2011]  Written by Th3-822.

?>