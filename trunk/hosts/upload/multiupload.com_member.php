<?php
$upload_acc = $UlTo = $cookie = array();

####### Account Info. ###########
$upload_acc['multiupload_com']['user'] = ""; //Set your username
$upload_acc['multiupload_com']['pass'] = ""; //Set your password
$AUUL = false; // Change it to true for Autoupload usage.

# Upload Sites/Accounts: (Note: It'll upload/ask for login to sites non listed here)
$UlTo['megaupload']/*As*/ = array('user' => '', 'pass' => '', 'upload' => true);
$UlTo['uploadking']/*As*/ = array('user' => '', 'pass' => '', 'upload' => true);
$UlTo['depositfiles']/*As*/ = array('user' => '', 'pass' => '', 'upload' => true);
$UlTo['hotfile']/*As*/ = array('user' => '', 'pass' => '', 'upload' => true);
$UlTo['uploadhere']/*As*/ = array('user' => '', 'pass' => '', 'upload' => true);
$UlTo['zshare']/*As*/ = array('user' => '', 'pass' => '', 'upload' => true);
$UlTo['filesonic']/*As*/ = array('user' => '', 'pass' => '', 'upload' => true);
$UlTo['wupload']/*As*/ = array('user' => '', 'pass' => '', 'upload' => true);
// Multiupload have added a new site and wanna set a default pass?: Copy the name showed in Upload to these hosts* and add it in a new line (Lowercase name).
###########################

// Using a function for get the sites supported - Warning: Don't Edit This Function
function GetMUSites($page='') {
	global $cookie;
	if (empty($page)) {
		$page = geturl("www.multiupload.com", 80, "/", 0, $cookie);is_page($page);
		$cookie = GetCookiesArr($page);
	}
	if (!preg_match_all("@showdetails\('(\w+)'\)@i", $page, $hosts)) html_error('Cannot Check Supported Sites [1]');
	$sites = array();
	foreach ($hosts[1] as $host) {
		$popup = cut_str($page, 'id="details_'.$host.'">', '</div></div>');
		if (empty($popup) || (!preg_match('@"logos/(\d+)\.[^"]+"@i', $popup, $hid) || !preg_match('@Upload files directly into my ([^\"|\'|\<|\:]+) account\:@i', $popup, $name))) html_error("Cannot Check Supported Sites [2 ($host)]");
		$name[1] = trim($name[1]);
		if ($hid[1] != 14) $sites[] = array('hname'=>$name[1], 'id' => $hid[1], 'sname' => $host);
		unset($popup,$hid,$name);
	}
	return $sites;
}

$not_done = true;
$continue_up = $login = false;

$sites = GetMUSites();

if ($upload_acc['multiupload_com']['user'] && $upload_acc['multiupload_com']['pass']) {
	$_REQUEST['up_login'] = $upload_acc['multiupload_com']['user'];
	$_REQUEST['up_pass'] = $upload_acc['multiupload_com']['pass'];

	if ($AUUL) {
		$_REQUEST['up_description'] = 'Uploaded with Rapidleech.';
		$_REQUEST['action'] = "FORM";
	}
	$login = true;
	echo "<p style='text-align:center;font-weight:bold;'>Using Default Account.</p>\n";
}
if ($_REQUEST['action'] == "FORM") $continue_up=true;
else {
	echo "<table border='0' style='width:270px;' cellspacing='0' align='center'>
	<form method='POST'>
	<input type='hidden' name='action' value='FORM' />";
	if (!$login) echo "<tr><td style='white-space:nowrap;'>&nbsp;Multiupload Acc*</td><td>&nbsp;<input type='text' name='up_login' value='' style='width:160px;' /></td></tr>
	<tr><td style='white-space:nowrap;'>&nbsp;Password*</td><td>&nbsp;<input type='password' name='up_pass' value='' style='width:160px;' /></td></tr>\n";
	echo "\t<tr><td colspan='2' align='center'><br />Upload options*<br /><br /></td></tr>
	<tr><td style='white-space:nowrap;'>File Description:</td><td>&nbsp;<input type='text' name='up_description' value='Uploaded with Rapidleech.' style='width:160px;' /></td></tr>
	<tr><td colspan='2' align='center'><br />Upload to these hosts*<br /><br /></td></tr>\n";
	$pre_tr = true;
	foreach ($sites as $site) {
		echo "\t<tr><td style='white-space:nowrap;'><input type='checkbox' name='enable_{$site['sname']}' value='yes'".((@$UlTo[strtolower($site['hname'])]['upload'] === false)?"":" checked='checked'")." />&nbsp;".htmlentities($site['hname'])."&nbsp;&nbsp;</td>";
		if (empty($UlTo[strtolower($site['hname'])]['user']) || empty($UlTo[strtolower($site['hname'])]['pass'])) echo"<td style='text-align:right;'>&nbsp;".lang(37).":&nbsp;<input type='text' name='{$site['sname']}_login' value='' style='width:120px;' /><br />&nbsp;".lang(38).":&nbsp;<input type='password' name='{$site['sname']}_pass' value='' style='width:120px;' /></td></tr>\n";
		else echo "<td style='text-align:right;font-weight:bold;'>&nbsp;[Default Account Loaded]</td></tr>\n";
		echo "\t<tr><td><br /></td></tr>\n";
	}
	echo "\t<tr><td colspan='2' align='center'><br /><input type='submit' value='Upload' /></td></tr>
	<tr><td colspan='2' align='center'><small>*You can set it as default in <b>{$page_upload["multiupload.com_member"]}</b></small></td></tr>\n</table>\n</form>\n";
	echo "<script type='text/javascript'>self.resizeTo(700,600);</script>\n"; //Resize upload window
}

if ($continue_up) {
	$not_done = false;
	// Login
	echo "<table style='width:600px;margin:auto;'>\n<tr><td align='center'>\n<div id='login' width='100%' align='center'>Login to Multiupload</div>\n";

	if (!empty($_REQUEST['up_login']) && !empty($_REQUEST['up_pass'])) {
		$post = array();
		$post["username"] = $_REQUEST['up_login'];
		$post["password"] = $_REQUEST['up_pass'];

		$page = geturl("www.multiupload.com", 80, "/login", 0, $cookie, $post);is_page($page);

		is_present($page, "Invalid username and/or password", "Login Failed: Invalid username and/or password.");
		$cookie = GetCookiesArr($page);
		if (empty($cookie['u'])) html_error("Login Failed.");
	} else {
		echo "<b><center>Login not found or empty, using non member upload.</center></b>\n";
	}

	// Retrive upload ID
	echo "<script type='text/javascript'>document.getElementById('login').style.display='none';</script>\n<div id='info' width='100%' align='center'>Retriving upload server</div>\n";

	$page = geturl("www.multiupload.com", 80, "/", 0, $cookie);is_page($page);

	$post = array();
	$post['UPLOAD_IDENTIFIER'] = cut_str($page, 'name="UPLOAD_IDENTIFIER" value="', '"');
	$post['u'] = cut_str($page, 'name="u" value="', '"');
	$post['description_0'] = $_REQUEST['up_description'];
	foreach ($sites as $site) {
		if ((@$UlTo[strtolower($site['hname'])]['upload'] === false && $AUUL) || ($_REQUEST['enable_'.$site['sname']] != 'yes' && !$AUUL)) continue;

		$post['service_'.$site['id']] = 1;
		if (!empty($UlTo[strtolower($site['hname'])]['user']) && !empty($UlTo[strtolower($site['hname'])]['pass'])) {
			$post['username_'.$site['id']] = $UlTo[strtolower($site['hname'])]['user'];
			$post['password_'.$site['id']] = $UlTo[strtolower($site['hname'])]['pass'];
		} elseif (!$AUUL && !empty($_REQUEST[$site['sname'].'_login']) && !empty($_REQUEST[$site['sname'].'_pass'])) {
			$post['username_'.$site['id']] = $_REQUEST[$site['sname'].'_login'];
			$post['password_'.$site['id']] = $_REQUEST[$site['sname'].'_pass'];
		}
		$post['remember_'.$site['id']] = 0;
	}

	if (!preg_match('@action="(http://www\d+\.multiupload\.com/upload/[^"]+)"@i', $page, $up_loc)) html_error("Upload URL not found.", 0);
	$up_loc = $up_loc[1];


	// Uploading
	echo "<script type='text/javascript'>document.getElementById('info').style.display='none';</script>\n";unset($post['service_14']);// Shhh

	$url = parse_url($up_loc);
	$upfiles = upfile($url["host"],$url["port"] ? $url["port"] : 80, $url["path"].($url["query"] ? "?".$url["query"] : ""), "http://www.multiupload.com/", $cookie, $post, $lfile, $lname, "file_0");

	// Upload Finished
	echo "<script type='text/javascript'>document.getElementById('progressblock').style.display='none';</script>";

	is_page($upfiles);
	if (!preg_match('@"downloadid":"([^"]+)"@i', $upfiles, $dl)) html_error("Download id not found.", 0);
	$download_link = "http://www.multiupload.com/".$dl[1];
}

//[21-11-2011]  Written by Th3-822.

?>