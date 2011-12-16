<?php

####### Account Info. ###########
$upload_acc['ifile_it']['apikey'] = ""; //Set your ifile.it apikey here.
##############################

$not_done = true;
$continue_up = false;

// Check https support for login.
$usecurl = $cantlogin = false;
if (!extension_loaded('openssl')) {
	if (extension_loaded('curl')) {
		$cV = curl_version();
		if (in_array('https', $cV['protocols'], true)) $usecurl = true;
		else $cantlogin = true;
	} else $cantlogin = true;
}

if ($upload_acc['ifile_it']['apikey']) {
	$_REQUEST['up_apikey'] = $upload_acc['ifile_it']['apikey'];
	$_REQUEST['action'] = "FORM";
	echo "<p style='text-align:center;font-weight:bold;'>Using Default Apikey.</p>\n";
}

if ($_REQUEST['action'] == "FORM") $continue_up=true;
else {
	echo "<form method='POST'>
	<input type='hidden' name='action' value='FORM' />";
	echo "<div id='apik' style='text-align:center;'>ifile.it Apikey*<br /><input type='text' id='up_apikey' name='up_apikey' value='' style='width:270px;' />";
	if (!$cantlogin) echo "<br /><br /><input type='checkbox' id='up_uselogin' name='up_uselogin' value='yes' onclick='javascript:showlogin();' />&nbsp;Use login?";
	echo "</div>\n\t<table border='0' style='width:270px;' cellspacing='0' align='center'>";
	if (!$cantlogin) echo "<tr id='tr_user' style='display:none;' align='center'><td>Username</td><td><input type='text' name='up_login' value='' style='width:160px;' /></td></tr>
	<tr id='tr_pass' style='display:none;' align='center'><td>Password</td><td><input type='password' name='up_pass' value='' style='width:160px;' /></td></tr>
	<tr id='tr_note' style='display:none;'><td colspan='2' align='center'>Remember to copy and save your Apikey after upload.<br /><small>Login needs https support.</small></td></tr>\n";
	echo "\t<tr><td colspan='2' align='center'><br /><input type='submit' value='Upload' /></td></tr>
	<tr><td colspan='2' align='center'><small>*You can set it as default in <b>{$page_upload["ifile.it_member"]}</b></small></td></tr>\n</table>\n</form>\n";
	echo "<script type='text/javascript'>/* <![CDATA[ */
	self.resizeTo(700,400);";
	if (!$cantlogin) echo "\n\tfunction showlogin() {
		if ($('#up_uselogin').is(':checked')) {
			$('#tr_user,#tr_pass,#tr_note').show();
			$('#up_apikey').attr('disabled', 'disabled');
			$('#up_apikey').val('');
		} else {
			$('#tr_user,#tr_pass,#tr_note').hide();
			$('#up_apikey').removeAttr('disabled');
		}
	}\n\t";
	echo "\n/* ]]> */</script>\n";
}

if ($continue_up) {
	$not_done = $uselogin = false;
	$login = true;

	// Ping api
	$page = geturl ('ifile.it', 80, '/api-ping.api');is_page($page);
	is_notpresent($page, '"message":"pong"', "Error: ifile.it api is down?.");

	// Login
	if ($_REQUEST['up_uselogin'] == 'yes' && !empty($_REQUEST['up_login']) && !empty($_REQUEST['up_pass'])) {
		echo "\n<div id='login' width='100%' align='center'>Getting Apikey for ifile.it</div>\n";
		$post = array();
		$post["username"] = urlencode($_REQUEST['up_login']);
		$post["password"] = urlencode($_REQUEST['up_pass']);

		if (!$usecurl) {
		$page = geturl ('secure.ifile.it', 0, '/api-fetch_apikey.api', 'https://secure.ifile.it/api-fetch_apikey.api', 0, $post, 0, 0, 0, 0, 'https'); // Port is overridden to 443 .
		is_page($page);
		} else {
			$page = IF_cURL ('https://secure.ifile.it/api-fetch_apikey.api', $post);
		}

		is_present($page, '"status":"error"', 'Login Failed: "'.str_replace('\\','',cut_str($page, '"message":"','"')).'"');
		is_notpresent($page, '"akey":"', "Login Failed: Akey not found.");
		$_REQUEST['up_apikey'] = cut_str($page, '"akey":"', '"');
		$uselogin = true;
	} else if(empty($_REQUEST['up_apikey'])) {
		$login = false;
		echo "<b><center>Neither Apikey nor Login were found or are empty, using non member upload.</center></b>\n";
	}

	if ($login) { // Check Apikey...
		$_REQUEST['up_apikey'] = trim($_REQUEST['up_apikey']);
		echo "<script type='text/javascript'>document.getElementById('login').style.display='none';</script>\n<div id='apikey' width='100%' align='center'>Checking Apikey</div>\n";
		$page = geturl ('ifile.it', 80, '/api-fetch_account_info.api', '', 0, array('akey'=>$_REQUEST['up_apikey']));is_page($page);
		is_present($page, '"status":"error"', 'Error while checking Apikey: "'.str_replace('\\','',cut_str($page, '"message":"','"')).'"');
	}

	// Retrive upload ID
	echo "<table style='width:600px;margin:auto;'>\n<tr><td align='center'>\n<script type='text/javascript'>document.getElementById('apikey').style.display='none';</script>\n<div id='info' width='100%' align='center'>Retrive upload ID</div>\n";

	$page = geturl('ifile.it', 80, '/api-fetch_upload_url.api?response=text');is_page($page);
	is_present($page, 'status: error', 'Upload Failed: "'.htmlentities(cut_str($page, 'message: ',"\n")).'"');

	if (!preg_match("@upload_url: http://(i\d+\.ifile\.it)(/[^\r|\n]+)@i", $page, $loc)) html_error("Upload server not found.", 0);

	$post = array();
	if ($login) $post['akey'] = $_REQUEST['up_apikey'];

	$up_loc = "http://{$loc[1]}{$loc[2]}?response=text";

	// Uploading
	echo "<script type='text/javascript'>document.getElementById('info').style.display='none';</script>\n";

	$url = parse_url($up_loc);
	$upfiles = upfile($url["host"],$url["port"] ? $url["port"] : 80, $url["path"].($url["query"] ? "?".$url["query"] : ""), '', 0, $post, $lfile, $lname, "Filedata");

	// Upload Finished
	echo "<script type='text/javascript'>document.getElementById('progressblock').style.display='none';</script>";

	is_page($upfiles);
	is_present($page, 'status: error', 'Upload Error: "'.htmlentities(cut_str($page, 'message: ',"\n")).'"');

	if (preg_match('@ukey: (\w+)@i', $upfiles, $dl)) {
		$download_link = "http://ifile.it/".$dl[1];
		if(preg_match("@name: ([^\r|\n]+)@i", $upfiles, $fn)) $download_link .= "/" . $fn[1];
		if ($uselogin) echo "\n<table width='100%' border='0'>\n<tr><td width='100' nowrap='nowrap' align='right'>ifile.it Apikey:<td width='80%'><input value='{$_REQUEST['up_apikey']}' class='upstyles-dllink' readonly='readonly' /></tr>\n</table>\n";
	} else html_error("Download link not found.", 0);
}

// Small cURL function for login (If OpenSSL isn't loaded and cURL have SSL support)
function IF_cURL($link, $post) { // Yes, it's from the youtube plugin.
	$opt = array(CURLOPT_HEADER => 1, CURLOPT_REFERER => $link,
		CURLOPT_SSL_VERIFYPEER => 0, CURLOPT_SSL_VERIFYHOST => 0, CURLOPT_RETURNTRANSFER => 1,
		CURLOPT_USERAGENT => "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.7.6) Gecko/20050317 Firefox/1.0.2");
	$opt[CURLOPT_POST] = 1;
	$opt[CURLOPT_POSTFIELDS] = formpostdata($post);
	$ch = curl_init($link);
	foreach ($opt as $O => $V) { // Using this instead of 'curl_setopt_array'
		curl_setopt($ch, $O, $V);
	}
	$page = curl_exec($ch);
	$errz = curl_errno($ch);
	$errz2 = curl_error($ch);
	curl_close($ch);

	if ($errz != 0) html_error("IF:[cURL:$errz] $errz2");
	return $page;
}

//[25-6-2011]  Written by Th3-822.

?>