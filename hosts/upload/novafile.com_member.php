<?php
######## Account Info ########
$upload_acc['novafile_com']['user'] = ''; //Set your login
$upload_acc['novafile_com']['pass'] = ''; //Set your password
########################

$_GET['proxy'] = isset($_GET['proxy']) ? $_GET['proxy'] : '';
$not_done = true;

if (!empty($upload_acc['novafile_com']['user']) && !empty($upload_acc['novafile_com']['pass'])) {
	$_REQUEST['up_login'] = $upload_acc['novafile_com']['user'];
	$_REQUEST['up_pass'] = $upload_acc['novafile_com']['pass'];
	$_REQUEST['action'] = 'FORM';
	echo "<b><center>Using Default Login.</center></b>\n";
}

if (empty($_REQUEST['action']) || $_REQUEST['action'] != 'FORM') {
	echo "<table border='0' style='width:270px;' cellspacing='0' align='center'>
	<form method='POST'>
	<input type='hidden' name='action' value='FORM' />
	<tr><td style='white-space:nowrap;'>&nbsp;Username*</td><td>&nbsp;<input type='text' name='up_login' value='' style='width:160px;' /></td></tr>
	<tr><td style='white-space:nowrap;'>&nbsp;Password*</td><td>&nbsp;<input type='password' name='up_pass' value='' style='width:160px;' /></td></tr>\n";
	echo "<tr><td colspan='2' align='center'><br /><input type='submit' value='Upload' /></td></tr>\n";
	echo "<tr><td colspan='2' align='center'><small>*You can set it as default in <b>".basename(__FILE__)."</b></small></td></tr>\n";
	echo "</table>\n</form>\n";
} else {
	$not_done = false;
	$domain = 'novafile.com';
	$referer = "http://$domain/";

	// Login
	echo "<table style='width:600px;margin:auto;'>\n<tr><td align='center'>\n<div id='login' width='100%' align='center'>Login to ".str_replace('www.', '', $domain)."</div>\n";

	$cookie = array('lang' => 'english');
	if (!empty($_REQUEST['up_login']) && !empty($_REQUEST['up_pass'])) {
		$post = array();
		$post['op'] = 'login';
		$post['redirect'] = '';
		$post['login'] = urlencode($_REQUEST['up_login']);
		$post['password'] = urlencode($_REQUEST['up_pass']);

		$page = geturl($domain, 80, '/login', $referer, $cookie, $post, 0, $_GET['proxy'], $pauth);is_page($page);
		$header = substr($page, 0, strpos($page, "\r\n\r\n"));
		if (preg_match('@Incorrect ((Username)|(Login)) or Password@i', $page)) html_error('Login failed: User/Password incorrect.');
		is_present($page, 'op=resend_activation', 'Login failed: Your account isn\'t confirmed yet.');
		is_notpresent($header, 'Set-Cookie: xfss=', 'Error: Cannot find session cookie.');
		$cookie = GetCookiesArr($header);
		$cookie['lang'] = 'english';
		$login = true;
	} else html_error('Login failed: User/Password empty.');

	// Retrive upload ID
	echo "<script type='text/javascript'>document.getElementById('login').style.display='none';</script>\n<div id='info' width='100%' align='center'>Retrive upload ID</div>\n";

	$page = geturl($domain, 80, '/?op=upload', $referer, $cookie, 0, 0, $_GET['proxy'], $pauth);is_page($page);

	if (!preg_match('@action="((https?://[^/\"]+)?/upload/\d+)/?\"@i',$page, $up)) html_error('Error: Cannot find upload server.');
	$up[1] = (empty($up[2])) ? "http://$domain".$up[1] : $up[1];

	$uid = '';for ($i = 0; $i < 13; $i++) $uid .= rand(0,9);

	$post = array();
	$post['upload_type'] = 'file';
	$post['srv_id'] = cut_str($page, 'name="srv_id" value="', '"');
	$post['sess_id'] = cut_str($page, 'name="sess_id" value="', '"');
	$post['utype'] = cut_str($page, 'name="utype" value="', '"');
	$post['srv_tmp_url'] = cut_str($page, 'name="srv_tmp_url" value="', '"');
	$post['file_0_descr'] = 'Uploaded by Rapidleech.';
	$post['tos'] = 1;
	$post['submit_btn'] = 'Upload';

	$up_url = $up[1]."/?X-Progress-ID=$uid";

	// Uploading
	echo "<script type='text/javascript'>document.getElementById('info').style.display='none';</script>\n";

	$url = parse_url($up_url);
	$upfiles = upfile($url['host'], defport($url), $url['path'].(!empty($url['query']) ? '?'.$url['query'] : ''), $referer, $cookie, $post, $lfile, $lname, 'file_0', '', $_GET['proxy'], $pauth);

	// Upload Finished
	echo "<script type='text/javascript'>document.getElementById('progressblock').style.display='none';</script>\n";

	is_page($upfiles);

	$post = array();
	$post['op'] = 'upload_result';
	if (!preg_match('@name=[\'\"]fn[\'\"](?:(?:[\s\t]*>)|(?:[\s\t]*value=[\'\"]))([^\'\"<>]+)@i', $upfiles, $fn)) html_error('Error: fn value not found.');
	$post['fn'] = trim($fn[1]);
	$post['st'] = 'OK';

	$page = geturl($domain, 80, '/', $up_url, $cookie, $post, 0, $_GET['proxy'], $pauth);is_page($page);

	if (preg_match('@(https?://(?:www\.)?'.preg_quote($domain, '@').'/\w{12}(?:/[^\?/<>\"\'\r\n]+)?(?:\.html?)?)\?killcode=\w+@i', $page, $lnk)) {
		$download_link = $lnk[1];
		$delete_link = $lnk[0];
	} else html_error('Download link not found.');
}

//[17-11-2012] Written by Th3-822.

?>