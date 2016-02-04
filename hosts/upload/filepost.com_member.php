<?php

####### Account Info. ###########
$upload_acc['filepost_com']['user'] = ""; //Set your email
$upload_acc['filepost_com']['pass'] = ""; //Set your password
##############################

$not_done = true;
$continue_up = false;

if ($upload_acc['filepost_com']['user'] && $upload_acc['filepost_com']['pass']) {
	$_REQUEST['up_login'] = $upload_acc['filepost_com']['user'];
	$_REQUEST['up_pass'] = $upload_acc['filepost_com']['pass'];
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
	<tr><td colspan='2' align='center'><small>*You can set it as default in <b>{$page_upload["filepost.com_member"]}</b></small></td></tr>\n</table>\n</form>\n";
}

if ($continue_up) {
	$not_done = $login = false;
	$cookie = array();

	// Login
	echo "<table style='width:600px;margin:auto;'>\n<tr><td align='center'>\n<div id='login' width='100%' align='center'>Login to Filepost</div>\n";

	if (!empty($_REQUEST['up_login']) && !empty($_REQUEST['up_pass'])) {
		$post = array();
		$post["email"] = urlencode($_REQUEST['up_login']);
		$post["password"] = urlencode($_REQUEST['up_pass']);

		$page = geturl ('filepost.com', 80, '/general/login_form/?JsHttpRequest='.time().'-xml', 'http://filepost.com/', 0, $post);
		is_page($page);

		is_present($page, 'Incorrect e-mail\/password combination', 'Login Failed: Invalid username and/or password.');
		$cookie = GetCookiesArr($page);
		if (empty($cookie['SID'])) html_error('Login Failed: Session cookie not found');
		$login = true;
	} else echo "<b><center>Login not found or empty, using non member upload.</center></b>\n";

	// Retrive upload ID
	echo "<script type='text/javascript'>document.getElementById('login').style.display='none';</script>\n<div id='info' width='100%' align='center'>Retrive upload ID</div>\n";

	// Start loop here
	$try = 0;
	$max_trys = 4;
	do {
		$try++;
		$page = geturl("filepost.com", 80, "/", 0, $cookie);is_page($page);
		if ($try > $max_trys) {
			$try = $try-1;
			if (stristr($page, 'Upload will be available a bit later')) html_error("Error@$try: Upload will be available a bit later.");
			else html_error("Error: No useable upload server found in $try trys.");
		}
		$cookie = array_merge($cookie, GetCookiesArr($page));

		if (stristr($page, 'Upload will be available a bit later')) {
			sleep(1); // Let's wait...
			continue;
		}

		if (!preg_match("@upload_url:\s+'(http://fs\d+\.filepost\.com/)([^\']+)'@i", $page, $loc)) html_error("Upload url not found.");

		//Testing server.
		$test = @file_get_contents($loc[1].'crossdomain.xml');
		if ($try+1 == $max_trys && !$test) { // To be removed later...
			//Do code at last try
			// With my acc logged in, selected server doesn't connect... But without it  work...
			$loc[1] = 'http://fs470.filepost.com/'; //I will use one of the working servers
			$test = file_get_contents($loc[1].'crossdomain.xml');
		}
	} while (!$test);
	// Now we have a useable upload server...

	$post = array();
	$post['Filename'] = $lname;
	$post['SID'] = cut_str($page, "SID: '", "'");
	$post['Upload'] = 'Submit Query';

	$up_loc = $loc[1].$loc[2];

	// Uploading
	echo "<script type='text/javascript'>document.getElementById('info').style.display='none';</script>\n";

	$url = parse_url($up_loc);
	$upfiles = upfile($url["host"],$url["port"] ? $url["port"] : 80, $url["path"].($url["query"] ? "?".$url["query"] : ""), '', 0, $post, $lfile, $lname, "file", '', 0, 0, 'Shockwave Flash');

	// Upload Finished
	echo "<script type='text/javascript'>document.getElementById('progressblock').style.display='none';</script>";

	is_page($upfiles);
	if (!preg_match('@\{"answer":"(\w+)"\}@i', $upfiles, $udid)) html_error("File-Adm id not found.");
	$page = geturl("filepost.com", 80, "/files/done/".$udid[1], 'http://filepost.com/', $cookie);is_page($page);

	if (preg_match('@id="down_link"[^<|>]+value="(http://[^\"]+)"@i', $page, $dl)) {
		$download_link = $dl[1];
		if (preg_match('@id="edit_link"[^<|>]+value="(http://[^\"]+)"@i', $page, $adml)) $adm_link = $adml[1];
	} else html_error("Download link not found.");
}

//[07-12-2011]  Written by Th3-822.

?>