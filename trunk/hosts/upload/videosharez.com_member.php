<?php

####### Account Info. ###########
$upload_acc['videosharez_com']['user'] = ""; //Set your user
$upload_acc['videosharez_com']['pass'] = ""; //Set your password
##############################

$not_done = true;
$continue_up = $login = false;

if (!preg_match("@\.(mp4|flv|mpe?g|wmv|mov|3gp|avi)$@i", $lname, $fext)) echo "<p style='color:red;text-align:center;font-weight:bold;'>This file ext. doesn't looks like a video file.</p>\n";

if ($upload_acc['videosharez_com']['user'] && $upload_acc['videosharez_com']['pass']) {
	$_REQUEST['up_login'] = $upload_acc['videosharez_com']['user'];
	$_REQUEST['up_pass'] = $upload_acc['videosharez_com']['pass'];
	$login = true;
	echo "<p style='text-align:center;font-weight:bold;'>Using Default Login and Pass.</p>\n";
}

$chans = array(7 => 'Music', 8 => 'News', 9 => 'Movies', 10 => 'Sports', 11 => 'Fun', 12 => 'Funny');
if ($_REQUEST['action'] == "FORM") $continue_up = true;
else {
	echo "<table border='0' style='width:270px;' cellspacing='0' align='center'>
	<form method='POST'>
	<input type='hidden' name='action' value='FORM' />";
	if (!$login) echo "<tr><td style='white-space:nowrap;'>&nbsp;Login*</td><td>&nbsp;<input type='text' name='up_login' value='' style='width:160px;' /></td></tr>
	<tr><td style='white-space:nowrap;'>&nbsp;Password*</td><td>&nbsp;<input type='password' name='up_pass' value='' style='width:160px;' /></td></tr>
	<tr><td colspan='2' align='center'><small>*You can set it as default in <b>{$page_upload["videosharez.com_member"]}</b></small></td></tr>";
	echo "\t<tr><td colspan='2' align='center'><br />Upload options<br /><br /></td></tr>
	<tr><td style='white-space:nowrap;'>Title:</td><td>&nbsp;<input type='text' name='up_title' maxlength='60' value='$lname' style='width:160px;' /></td></tr>
	<tr><td style='white-space:nowrap;'>Description:</td><td>&nbsp;<textarea rows='4' style='width:160px;' name='up_description'>Uploaded with Rapidleech.</textarea></td></tr>
	<tr><td style='white-space:nowrap;'>Tags:&nbsp;<span title='Click for help' onclick='javascript:alert(\"Enter one or more tags, separated by spaces.\");'>[?]</span></td><td>&nbsp;<input type='text' name='up_tags' maxlength='120' value='' style='width:160px;' /></td></tr>
	<tr><td style='white-space:nowrap;'>Video Channels:&nbsp;<span title='Click for help' onclick='javascript:alert(\"Select between one to three channels that best describe your video.\")'>[?]</span></td><td>\n";
	foreach($chans as $v => $n) echo "\t<input type='checkbox' name='up_chan[]' value='$v' /> $n\n";
	echo "\t</td></tr>
	<tr><td colspan='2' align='center'><br /><input type='submit' value='Upload' /></td></tr>\n</table>\n</form>\n";
	echo "<script type='text/javascript'>self.resizeTo(700,450);</script>\n"; //Resize upload window
}

if ($continue_up) {
	$not_done = false;
	// Checking
	$_REQUEST['up_title'] = trim($_REQUEST['up_title']);
	$_REQUEST['up_description'] = trim($_REQUEST['up_description']);
	$_REQUEST['up_tags'] = trim($_REQUEST['up_tags']);
	if (empty($_REQUEST['up_tags'])) html_error("Please provide tag(s).", 0);
	if (!is_array($_REQUEST['up_chan'])) html_error("Please check (1 to 3) channel(s). [?]", 0);
	else {
		$vchans = array();
		$count = 0;
		foreach ($_REQUEST['up_chan'] as $n) {
			if (array_key_exists($n, $chans)) {
				$vchans[] = $n;
				$count++;
			}
			if ($count > 3) html_error("Please check (1 to 3) channel(s). [>3]", 0);
		}
		if($count == 0) html_error("Please check (1 to 3) channel(s). [0]", 0);
	}

	// Login
	echo "<table style='width:600px;margin:auto;'>\n<tr><td align='center'>\n<div id='login' width='100%' align='center'>Login to Videosharez</div>\n";

	if (empty($_REQUEST['up_login']) || empty($_REQUEST['up_pass'])) html_error("Login failed: User/Password empty.", 0);
	$post = array();
	$post['username'] = $_REQUEST['up_login'];
	$post['password'] = $_REQUEST['up_pass'];
	$post['login_remember'] = 'on';
	$post['action_login'] = "Log In";

	$page = geturl("www.videosharez.com", 80, "/login", 'http://www.videosharez.com/login', 0, $post, 0, $_GET["proxy"], $pauth);is_page($page);
	is_present($page, "Invalid Username/Password", "Login failed: User/Password incorrect.");
	is_notpresent($page, 'Set-Cookie: remember=', 'Error: Login cookie not found.');
	$cookie = GetCookies($page);

	// Retrive upload ID
	echo "<script type='text/javascript'>document.getElementById('login').style.display='none';</script>\n<div id='info' width='100%' align='center'>Retrive upload ID</div>\n";

	$page = geturl("www.videosharez.com", 80, "/ubr_link_upload.php?config_file=ubr_default_config.php&rnd_id=".time().rand(100,999), 0, $cookie);is_page($page);
	if (!preg_match('@startUpload\("([^"]+)"@i', $page, $uid)) html_error("Upload ID not found.", 0);

	$post = array();
	$post['MAX_FILE_SIZE'] = 104857600;
	$post['upload_range'] = 1;
	$post['adult'] = '';
	$post['field_myvideo_keywords'] = trim($_REQUEST['up_tags']);
	$post['field_myvideo_title'] = empty($_REQUEST['up_title']) ? $fname : $_REQUEST['up_title'];
	$post['field_myvideo_descr'] = empty($_REQUEST['up_description']) ? 'Uploaded with Rapidleech.' : $_REQUEST['up_description'];
	$post['listch'] = implode('|', $vchans);
	$post['field_privacy'] = 'public';

	$up_loc = "http://www.videosharez.com/cgi-bin/ubr_upload.pl?upload_id=".$uid[1];

	// Uploading
	echo "<script type='text/javascript'>document.getElementById('info').style.display='none';</script>\n";

	$url = parse_url($up_loc);
	$upfiles = upfile($url["host"],$url["port"] ? $url["port"] : 80, $url["path"].($url["query"] ? "?".$url["query"] : ""), "http://www.videosharez.com/upload", $cookie, $post, $lfile, $lname, "upfile_0");

	// Upload Finished
	echo "<script type='text/javascript'>document.getElementById('progressblock').style.display='none';</script>";
	is_page($upfiles);

	$page = geturl("www.videosharez.com", 80, "/upload?upload_id=".$uid[1], 0, $cookie);is_page($page);
	if (!preg_match('@Location: http://(www\.)?videosharez\.com(/uploadsuccess/[^\r|\n]+)@i', $page, $rd)) html_error("Redirect not found.", 0);
	$page = geturl("www.videosharez.com", 80, $rd[2], 0, $cookie);is_page($page);
	if (!preg_match('@value="(http://(www\.)?videosharez\.com/video/\w+/[^"]+)" name="video_link"@i', $page, $dl)) html_error("Download link not found.", 0);
	$download_link = $dl[1];
}

//[02-8-2011]  Written by Th3-822.

?>