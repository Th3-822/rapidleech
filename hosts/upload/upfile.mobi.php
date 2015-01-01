<?php
######## Account Info ########
$upload_acc['upfile_mobi']['user'] = ''; //Set your username
$upload_acc['upfile_mobi']['pass'] = ''; //Set your password
########################

$_GET['proxy'] = isset($_GET['proxy']) ? $_GET['proxy'] : '';
$not_done = true;
$continue_up = false;

if ($upload_acc['upfile_mobi']['user'] && $upload_acc['upfile_mobi']['pass']) {
	$default_acc = true;
	$_REQUEST['up_login'] = $upload_acc['upfile_mobi']['user'];
	$_REQUEST['up_pass'] = $upload_acc['upfile_mobi']['pass'];
	$_REQUEST['action'] = 'FORM';
	echo "<b><center>Using Default Login.</center></b>\n";
	// Upload settings
	$_REQUEST['T8']['passw'] = '';
	$_REQUEST['T8']['descr'] = '';
} else $default_acc = false;

if (!empty($_REQUEST['action']) && $_REQUEST['action'] == 'FORM') $continue_up = true;
else {
	echo "<table border='0' style='width:270px;' cellspacing='0' align='center'>
	<form method='POST'>
	<input type='hidden' name='action' value='FORM' />
	<tr><td style='white-space:nowrap;'>&nbsp;Username*</td><td>&nbsp;<input type='text' name='up_login' value='' style='width:160px;' /></td></tr>
	<tr><td style='white-space:nowrap;'>&nbsp;Password*</td><td>&nbsp;<input type='password' name='up_pass' value='' style='width:160px;' /></td></tr>\n";
	echo "<tr><td colspan='2' align='center'><br />Upload options<br /><br /></td></tr>
	<tr><td style='white-space:nowrap;'>File Password:</td><td>&nbsp;<input type='text' name='T8[passw]' value='' style='width:160px;' /></td></tr>
	<tr><td style='white-space:nowrap;'>Description:</td><td>&nbsp;<input type='text' name='T8[descr]' value='Uploaded with Rapidleech.' style='width:160px;' /></td></tr>\n";
	echo "<tr><td colspan='2' align='center'><br /><input type='submit' value='Upload' /></td></tr>\n";
	echo "<tr><td colspan='2' align='center'><small>*You can set it as default in <b>".basename(__FILE__)."</b></small></td></tr>\n";
	echo "</table>\n</form>\n";
}

if ($continue_up) {
	$not_done = false;
	$domain = 'upfile.mobi';
	$referer = "http://$domain/";

	// Login
	echo "<table style='width:600px;margin:auto;'>\n<tr><td align='center'>\n<div id='login' width='100%' align='center'>Login to $domain</div>\n";

	$cookie = array('lang' => 'en');
	if (!empty($_REQUEST['up_login']) && !empty($_REQUEST['up_pass'])) {
		$post = array();
		$post['u'] = urlencode($_REQUEST['up_login']);
		$post['p'] = urlencode($_REQUEST['up_pass']);

		$page = geturl($domain, 80, '/index.php?page=login&start', $referer, $cookie, $post, 0, $_GET['proxy'], $pauth);is_page($page);
		is_present($page, 'username or password is incorrect', 'Login Failed: User/Password incorrect.');
		$cookie = GetCookiesArr($page);
		if (empty($cookie['secret_code'])) html_error('Login Failed: Session cookie not found.');
		$cookie['lang'] = 'en';
		$login = true;
	} else {
		echo "<b><center>Login not found or empty, using non member upload.</center></b>\n";
		$login = false;
	}

	// Retrive upload ID
	echo "<script type='text/javascript'>document.getElementById('login').style.display='none';</script>\n<div id='info' width='100%' align='center'>Retrive upload ID</div>\n";

	$_REQUEST['T8'] = array_map('trim', $_REQUEST['T8']);
	$post = array();
	$post['folder_id'] = 0;
	if (!empty($_REQUEST['T8']['passw'])) $post['pass'] = $_REQUEST['T8']['passw'];
	$post['info'] = !empty($_REQUEST['T8']['descr']) ? $_REQUEST['T8']['descr'] : 'Uploaded with Rapidleech.';
	$post['agree'] = 'yes';

	$up_url = "http://$domain/index.php?page=upload";

	// Uploading
	echo "<script type='text/javascript'>document.getElementById('info').style.display='none';</script>\n";

	$url = parse_url($up_url);
	$upfiles = upfile($url['host'], defport($url), $url['path'].(!empty($url['query']) ? '?'.$url['query'] : ''), $referer, $cookie, $post, $lfile, $lname, 'upfile', '', $_GET['proxy'], $pauth);

	// Upload Finished
	echo "<script type='text/javascript'>document.getElementById('progressblock').style.display='none';</script>\n";

	is_page($upfiles);

	if (!preg_match('@[\?&]id=(\d+)@i', $upfiles, $fid)) html_error('Download link not Found.');
	$download_link = $referer.$fid[1];
	if (preg_match('@[\?&]password=(\w+)@i', $upfiles, $fpsw)) $access_pass = $download_link.'.'.$fpsw[1]; // Testing this var.
	if (preg_match('@[\?&]code=(\w+)@i', $upfiles, $fdel)) $delete_link = $referer."?page=delete&f={$fid[1]}&s=".$fdel[1];
}

//[05-7-2013] Written by Th3-822.
//[10-5-2014] Fixed. - Th3-822

?>