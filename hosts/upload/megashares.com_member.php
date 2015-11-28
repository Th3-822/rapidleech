<?php
######## Account Info ########
$upload_acc['megashares_com']['user'] = ''; //Set your username
$upload_acc['megashares_com']['pass'] = ''; //Set your password
########################

$_GET['proxy'] = isset($_GET['proxy']) ? $_GET['proxy'] : '';
$not_done = true;
$ftypes = array('doc'=>'Document', 'video'=>'Video / Movie', 'application'=>'Application', 'music'=>'Music', 'image'=>'Image / Photo');

// Check https support for requests.
$use_curl = $options['use_curl'] && extension_loaded('curl') && function_exists('curl_init') && function_exists('curl_exec') ? true : false;
$chttps = false;
$use_https = true;
if ($use_curl) {
	$cV = curl_version();
	if (in_array('https', $cV['protocols'], true)) $chttps = true;
}
if (!extension_loaded('openssl') && !$chttps) $use_https = false;
else if (!$chttps) $use_curl = false;

if ($upload_acc['megashares_com']['user'] && $upload_acc['megashares_com']['pass']) {
	$default_acc = true;
	$_REQUEST['up_login'] = $upload_acc['megashares_com']['user'];
	$_REQUEST['up_pass'] = $upload_acc['megashares_com']['pass'];
	$_REQUEST['action'] = 'FORM';
	// Change settings here: (With default login enabled)
	$_REQUEST['up_description'] = 'Uploaded with Rapidleech.'; // File Description.
	$_REQUEST['up_category'] = 'doc'; // File Category. You can use 'doc', 'video', 'application', 'music' or 'image'
	$_REQUEST['up_lpassword'] = ''; // Link password.
	$_REQUEST['up_searchable'] = 'yes'; // Make link searchable.
	echo "<b><center>Using Default Login.</center></b>\n";
} else $default_acc = false;

if (empty($_REQUEST['action']) || $_REQUEST['action'] != 'FORM') {
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
	echo "<tr><td colspan='2' align='center'><br /><input type='submit' value='Upload' /></td></tr>\n";
	echo "<tr><td colspan='2' align='center'><small>*You can set it as default in <b>".basename(__FILE__)."</b></small></td></tr>\n";
	echo "</table>\n</form>\n";
	echo "<script type='text/javascript'>self.resizeTo(700,420);</script>\n"; //Resize upload window
} else {
	$login = $not_done = false;
	$domain = 'www.megashares.com';
	$referer = "http://$domain/";

	// Login
	echo "<table style='width:600px;margin:auto;'>\n<tr><td align='center'>\n<div id='login' width='100%' align='center'>Login to $domain</div>\n";

	$cookie = array('orgrfr' => $referer);
	if (!empty($_REQUEST['up_login']) && !empty($_REQUEST['up_pass'])) {
		$post = array();
		$post['httpref'] = '';
		$post['mymslogin_name'] = urlencode($_REQUEST['up_login']);
		$post['mymspassword'] = urlencode($_REQUEST['up_pass']);
		$post['myms_login'] = 'Login';

		$loginUrl = 'https://d01.megashares.com/myms_login.php';
		$page = ul_GetPage($loginUrl, $cookie, $post, $referer);

		//is_present($page, "Error during login - Invalid Username.", "Login Failed: The username you have entered is incorrect.");
		//is_present($page, "Password does not match Username.", "Login Failed: The password you have entered is incorrect.");
		is_present($page, 'Error during login - Login failed for user', 'Login Failed: The username/password is incorrect.');
		is_present($page, 'You have not verified your account yet.', 'Login Failed: Account not verified.');
		$cookie = GetCookiesArr($page, $cookie);
		if (empty($cookie['myms'])) html_error("Login Failed.");

		$login = true;
	} else {
		html_error('Login Failed: Login/Password empty.');
	}

	// Retrive upload ID
	echo "<script type='text/javascript'>document.getElementById('login').style.display='none';</script>\n<div id='info' width='100%' align='center'>Retrive upload ID</div>\n";

	$page = ul_GetPage($referer, $cookie, 0, $loginUrl);

	$post = array();
	$post['Filename'] = $lname;

	//<Pl guid>
	$guid = base_convert(jstime(), 10, 32);
	for ($i = 0; $i < 5; $i++) $guid .= base_convert(rand(0, 65534), 10, 32);
	$guid = 'p'.$guid.base_convert(rand(0, 31), 10, 32);
	// <Pl guid/>
	$post['name'] = $guid.strrchr($lname, '.');

	$post['passProtectUpload'] = (!empty($_REQUEST['up_lpassword'])) ? $_REQUEST['up_lpassword'] : '';
	$post['searchable'] = (!empty($_REQUEST['up_searchable']) && $_REQUEST['up_searchable'] == 'yes') ? 'yes' : 'no';
	$post['uploadFileDescription'] = (!empty($_REQUEST['up_description']) ? $_REQUEST['up_description'] : 'Uploaded with Rapidleech.');
	$post['uploadFileCategory'] = (!empty($_REQUEST['up_category']) && array_key_exists($_REQUEST['up_category'], $ftypes) ? $_REQUEST['up_category'] : 'doc');

	//$post['checkTOS'] = '';
	//$post['Upload'] = 'Submit Query';

	// Pre-Upload Check
	$plData = array();
	$plData['uploading_files%5B0%5D%5Bid%5D'] = $guid;
	$plData['uploading_files%5B0%5D%5Bname%5D'] = urlencode($lname);
	$plData['uploading_files%5B0%5D%5Bsize%5D'] = $fsize;
	$plData['uploading_files%5B0%5D%5Bpercent%5D'] = $plData['uploading_files%5B0%5D%5Bloaded%5D'] = 0;
	$plData['uploading_files%5B0%5D%5Bstatus%5D'] = 1;
	$check = array_map('trim', explode("\r\n\r\n", ul_GetPage($referer . 'pre_upload.php', $cookie, $plData, $referer), 2));
	if (!empty($check[1]) && $check[1] != 'success') html_error('Pre-Upload Check Error: Page Says: ' . htmlspecialchars($check[1]));

	if (!preg_match('@https?://(?:[\w\-]+\.)*megashares\.com/[^\'\"<>\s]+\.php\?tmp_sid=\w+&ups_sid=\w+&uld=\d+&uloc=\d+@i', $page, $ud)) html_error('Upload URL not found.');
	$up_loc = $ud[0];

	// Uploading
	echo "<script type='text/javascript'>document.getElementById('info').style.display='none';</script>\n";
	$url = parse_url($up_loc);
	$upfiles = upfile($url['host'], defport($url), $url['path'].(!empty($url['query']) ? '?'.$url['query'] : ''), '', 0, $post, $lfile, $lname, 'file', '', $_GET['proxy'], $pauth, 'Shockwave Flash');

	// Upload Finished
	echo "<script type='text/javascript'>document.getElementById('progressblock').style.display='none';</script>\n";

	is_page($upfiles);
	is_present($upfiles, 'fupaup=1', 'The filename you have provided is against our AUP and may not be uploaded.');

	$body = trim(substr($upfiles, strpos($upfiles, "\r\n\r\n") + 4));

	if (!preg_match('@^\d+@i', $body, $fid)) html_error("Post-ul fileid not found.");
	$page = ul_GetPage($referer . 'upostproc.php?fid=' . $fid[0], $cookie, 0, $referer);

	if (!preg_match('@https?://(?:[\w\-]+\.)*megashares\.com/dl/\w+/[^\t\r\n\'\"<>]+@i', $page, $dl)) html_error('Download link not found.');
	$download_link = $dl[0];
	if (preg_match('@https?://(?:[\w\-]+\.)*megashares\.com/\?dl=\w+@i', $page, $dlt)) $delete_link = $dlt[0];
}

function ul_GetPage($link, $cookie = 0, $post = 0, $referer = 0, $auth = 0, $XMLRequest = 0) {
	if (!$referer && !empty($GLOBALS['Referer'])) {
		$referer = $GLOBALS['Referer'];
	}

	if ($GLOBALS['use_curl']) {
		if ($XMLRequest) $referer .= "\r\nX-Requested-With: XMLHttpRequest";
		$page = cURL($link, $cookie, $post, $referer, $auth);
	} else {
		global $pauth;
		$Url = parse_url($link);
		$page = geturl($Url['host'], defport($Url), $Url['path'] . (!empty($Url['query']) ? '?' . $Url['query'] : ''), $referer, $cookie, $post, 0, !empty($_GET['proxy']) ? $_GET['proxy'] : '', $pauth, $auth, $Url['scheme'], 0, $XMLRequest);
		is_page($page);
	}
	return $page;
}

//[25-6-2011]  Written by Th3-822.
//[07-2-2012]  Regexp for $up_loc fixed, added redirect for getting download link. - Th3-822
//[26-5-2012]  Fixed. - Th3-822
//[21-11-2015]  Updated. - Th3-822

?>