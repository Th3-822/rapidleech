<?php

####### Account Info. ###########
$upload_acc['megashares.com']['user'] = ""; //Set your username
$upload_acc['megashares.com']['pass'] = ""; //Set your password
##############################

$not_done = true;
$continue_up = false;
$ftypes = array("video"=>"Video / Movie", "doc"=>"Document", "application"=>"Application", "music"=>"Music", "image"=>"Image / Photo");

if ($upload_acc['megashares.com']['user'] && $upload_acc['megashares.com']['pass']) {
	$_REQUEST['up_login'] = $upload_acc['megashares.com']['user'];
	$_REQUEST['up_pass'] = $upload_acc['megashares.com']['pass'];
	$_REQUEST['action'] = "FORM";
	// Change settings here: (With default login enabled)
	$_REQUEST['up_description'] = 'Uploaded with Rapidleech.'; // File Description.
	$_REQUEST['up_category'] = 'video'; // File Category. You can use 'video', 'doc', 'application', 'music' or 'image'
	$_REQUEST['up_lpassword'] = ''; // Link password.
	$_REQUEST['up_searchable'] = 'yes'; // Make link searchable.
	echo "<p style='text-align:center;font-weight:bold;'>Using Default Login and Pass.</p>\n";
}

if ($_REQUEST['action'] == "FORM") $continue_up=true;
else {
	echo "<table border='0' style='width:270px;' cellspacing='0' align='center'>
	<form method='POST'>
	<input type='hidden' name='action' value='FORM' />
	<tr><td style='white-space:nowrap;'>&nbsp;Username*</td><td>&nbsp;<input type='text' name='up_login' value='' style='width:160px;' /></td></tr>
	<tr><td style='white-space:nowrap;'>&nbsp;Password*</td><td>&nbsp;<input type='password' name='up_pass' value='' style='width:160px;' /></td></tr>\n";
	echo "\t<tr><td colspan='2' align='center'><br />Upload options*<br /><br /></td></tr>
	<tr><td style='white-space:nowrap;'>File Description:</td><td>&nbsp;<input type='text' name='up_description' value='Uploaded with Rapidleech.' style='width:160px;' /></td></tr>
	<tr><td style='white-space:nowrap;'>Upload Category:</td><td>&nbsp;<select name='up_category' style='width:160px;height:20px;'>\n";
	foreach($ftypes as $n => $v) echo "\t<option value='$n'>$v</option>\n";
	echo "\t</select></td></tr>
	<tr><td style='white-space:nowrap;'>Link password:</td><td>&nbsp;<input type='text' name='up_lpassword' value='' style='width:160px;' /></td></tr>
	<tr><td colspan='2' align='center'><input type='checkbox' name='up_searchable' value='yes' checked='checked' />&nbsp; Make link searchable/public</td>";
	echo "\t<tr><td colspan='2' align='center'><br /><input type='submit' value='Upload' /></td></tr>
	<tr><td colspan='2' align='center'><small>*You can set it as default in <b>{$page_upload["megashares.com_member"]}</b></small></td></tr>\n</table>\n</form>\n";
	echo "<script type='text/javascript'>self.resizeTo(700,420);</script>\n"; //Resize upload window
}

if ($continue_up) {
	$not_done = $login = false;
	// Login
	echo "<table style='width:600px;margin:auto;'>\n<tr><td align='center'>\n<div id='login' width='100%' align='center'>Login to Megashares</div>\n";

	$cookie = "orgrfr=http%3A%2F%2Fmegashares.com%2F";
	if (!empty($_REQUEST['up_login']) && !empty($_REQUEST['up_pass'])) {
		$post = array();
		$post["httpref"] = "";
		$post["mymslogin_name"] = $_REQUEST['up_login'];
		$post["mymspassword"] = $_REQUEST['up_pass'];
		$post["myms_login"] = "Login";

		$page = geturl("d01.megashares.com", 80, "/myms_login.php", 0, $cookie, $post);is_page($page);

		is_present($page, "Error during login - Invalid Username.", "Login Failed: The username you have entered is incorrect.");
		is_present($page, "Password does not match Username.", "Login Failed: The password you have entered is incorrect.");
		is_present($page, "You have not verified your account yet.", "Login Failed: Account not verified.");
		$cookie = "$cookie; " . GetCookies($page);
		is_notpresent($cookie, "myms=", "Login Failed.");
		$login = true;
	} else {
		echo "<b><center>Login not found or empty, using non member upload.</center></b>\n";
	}

	// Retrive upload ID
	echo "<script type='text/javascript'>document.getElementById('login').style.display='none';</script>\n<div id='info' width='100%' align='center'>Retrive upload ID</div>\n";

	$page = geturl("www.megashares.com", 80, "/", 0, $cookie);is_page($page);
	if (!$login) $cookie = "$cookie; " . GetCookies($page);
	$post = array();
	$post['APC_UPLOAD_PROGRESS'] = cut_str($page, 'id="upload_id" value="', '"');
	$post['msup_id'] = cut_str($page, 'name="msup_id" value="', '"');
	$post['uploadFileDescription'] = $_REQUEST['up_description'];
	if (array_key_exists($_REQUEST['up_category'], $ftypes)) $post['uploadFileCategory'] = $_REQUEST['up_category'];
	else $post['uploadFileCategory'] = 'video';
	$post['passProtectUpload'] = (!empty($_REQUEST['up_lpassword'])) ? $_REQUEST['up_lpassword'] : "";
	$post['emailAddress'] = "";
	$post['checkTOS'] = "";
	$post['searchable'] = ($_REQUEST['up_searchable'] == "yes") ? "yes" : "no";
	$post['downloadProgressURL'] = urlencode(cut_str($page, 'name="downloadProgressURL" value="', '"'));

	if (!preg_match("@init\('[^']+','([^']+)','(\w+)','(\w+)','(\d+)','(\d+)'[^\)]+\)@i", $page, $ud)) html_error("Upload Query not found.", 0);
	$up_loc = "http://www.megashares.com/{$ud[1]}.php?tmp_sid={$ud[2]}&ups_sid={$ud[3]}&uld={$ud[4]}&uloc={$ud[5]}";

	// Uploading
	echo "<script type='text/javascript'>document.getElementById('info').style.display='none';</script>\n";

	$url = parse_url($up_loc);
	$upfiles = upfile($url["host"],$url["port"] ? $url["port"] : 80, $url["path"].($url["query"] ? "?".$url["query"] : ""), "http://www.megashares.com/", $cookie, $post, $lfile, $lname, "upfile_0");

	// Upload Finished
	echo "<script type='text/javascript'>document.getElementById('progressblock').style.display='none';</script>";

	is_page($upfiles);

	if (!preg_match('@\.location = "https?://([^\.|/]+\.)?megashares\.com(/upostproc\.php\?fid=\d+)"@i', $upfiles, $redir)) html_error("Redirect not found.", 0);
	$page = geturl($redir[1]."megashares.com", 80, $redir[2], 0, $cookie);is_page($page);

	if (!preg_match('@http://\w+.megashares.com/dl/\w+/[^<|"|\']+@i', $page, $dl)) html_error("Download link not found.", 0);
	$download_link = $dl[0];
	if (preg_match('@http://\w+.megashares.com/\?dl=\w+@i', $page, $dlt)) $delete_link = $dlt[0];
}

//[25-6-2011]  Written by Th3-822.
//[07-2-2012]  Regexp for $up_loc fixed, added redirect for getting download link. - Th3-822

?>