<?php
####### Account Info. ###########
$upload_acc['2shared_com']['user'] = ""; //Set your user
$upload_acc['2shared_com']['pass'] = ""; //Set your password
##########################

$_GET["proxy"] = isset($_GET["proxy"]) ? $_GET["proxy"] : '';
$not_done = true;
$continue_up = false;

if ($upload_acc['2shared_com']['user'] && $upload_acc['2shared_com']['pass']) {
	$_REQUEST['up_login'] = $upload_acc['2shared_com']['user'];
	$_REQUEST['up_pass'] = $upload_acc['2shared_com']['pass'];
	$_REQUEST['action'] = "FORM";
	echo "<b><center>Using Default Login.</center></b>\n";
}

if (!empty($_REQUEST['action']) && $_REQUEST['action'] == "FORM") $continue_up = true;
else {
	echo "<table border='0' style='width:270px;' cellspacing='0' align='center'>
	<form method='POST'>
	<input type='hidden' name='action' value='FORM' />
	<tr><td style='white-space:nowrap;'>&nbsp;Email*</td><td>&nbsp;<input type='text' name='up_login' value='' style='width:160px;' /></td></tr>
	<tr><td style='white-space:nowrap;'>&nbsp;Password*</td><td>&nbsp;<input type='password' name='up_pass' value='' style='width:160px;' /></td></tr>\n";
	echo "<tr><td colspan='2' align='center'><br /><input type='submit' value='Upload' /></td></tr>\n";
	echo "<tr><td colspan='2' align='center'><small>*You can set it as default in <b>".basename(__FILE__)."</b></small></td></tr>\n";
	echo "</table>\n</form>\n";
}

if ($continue_up) {
	$not_done = false;
	$referer = "http://www.2shared.com/";
	// Login
	echo "<table style='width:600px;margin:auto;'>\n<tr><td align='center'>\n<div id='login' width='100%' align='center'>Login to 2shared.com</div>\n";

	$cookie = array();
	if (!empty($_REQUEST['up_login']) && !empty($_REQUEST['up_pass'])) {
		$post = array();
		$post['login'] = $_REQUEST['up_login'];
		$post['password'] = $_REQUEST['up_pass'];

		$page = geturl("www.2shared.com", 80, "/login", $referer, $cookie, $post, 0, $_GET["proxy"], $pauth);is_page($page);
		is_present($page, "Invalid e-mail address or password", "Login Failed: Invalid Email or Password.");
		if (stripos($page, '"ok":false') !== false) {
			if ($err=cut_str($page, '"rejectReason":"', '"')) html_error("Login Failed: 2S says: '$err'.");
			else html_error("Login Failed.");
		}
		$cookie = GetCookiesArr($page);
		$login = true;
	} else html_error("Login Failed: Email or Password are empty. Please check login data.");

	// Retrive upload ID
	echo "<script type='text/javascript'>document.getElementById('login').style.display='none';</script>\n<div id='info' width='100%' align='center'>Retrive upload ID</div>\n";

	$page = geturl("www.2shared.com", 80, "/", $referer, $cookie, 0, 0, $_GET["proxy"], $pauth);is_page($page);
	if (!preg_match('@action="(https?://[^/|\"|\<|\>]+/[^\"|\<|\>]+)"@i', $page, $up)) html_error('Error: Cannot find upload server.');

	$post = array('mainDC' => cut_str($page, 'name="mainDC" value="', '"'));

	$up_url = $up[1];

	// Uploading
	echo "<script type='text/javascript'>document.getElementById('info').style.display='none';</script>\n";

	$url = parse_url($up_url);
	$upfiles = upfile($url["host"], 80, $url["path"].($url["query"] ? "?".$url["query"] : ""), $referer, $cookie, $post, $lfile, $lname, 'fff', '', $_GET["proxy"], $pauth);

	// Upload Finished
	echo "<script type='text/javascript'>document.getElementById('progressblock').style.display='none';</script>";

	is_page($upfiles);

	if (stripos($upfiles, 'Your upload has successfully completed') === false) html_error('Error at upload');
	$page = geturl("www.2shared.com", 80, "/uploadComplete.jsp?".$url["query"], $referer, $cookie, 0, 0, $_GET["proxy"], $pauth);is_page($page);

	if (preg_match('@action="(https?://[^/|\"|\<|\>]+/[^\"|\<|\>]+)"[^\<|\>]*name="downloadForm@i', $page, $lnk)) {
		$download_link = $lnk[1];
		if (preg_match('@action="(https?://[^/|\"|\<|\>]+/[^\"|\<|\>]+)"[^\<|\>]*name="adminForm@i', $page, $admlnk)) $adm_link = $admlnk[1];
	} else html_error("Download link not found.");
}

//[17-6-2012] Rewritten (for adding login support) by Th3-822.

?>