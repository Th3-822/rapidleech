<?php
######## Account Info ########
$upload_acc['filehosting_org']['user'] = ''; //Set your Email
########################

$_GET['proxy'] = isset($_GET['proxy']) ? $_GET['proxy'] : '';
$not_done = true;

if ($upload_acc['filehosting_org']['user']) {
	$default_acc = true;
	$_REQUEST['up_email'] = $upload_acc['filehosting_org']['user'];
	$_REQUEST['action'] = 'FORM';
	echo "<b><center>Using Default Email.</center></b>\n";
} else $default_acc = false;

if (empty($_REQUEST['action']) || $_REQUEST['action'] != 'FORM') {
	echo "<table border='0' style='width:270px;' cellspacing='0' align='center'>
	<form method='POST'>
	<input type='hidden' name='action' value='FORM' />
	<tr><td style='white-space:nowrap;'>&nbsp;Email*</td><td>&nbsp;<input type='text' name='up_email' value='' style='width:160px;' /></td></tr>\n";
	echo "<tr><td colspan='2' align='center'><br /><input type='submit' value='Upload' /></td></tr>\n";
	echo "<tr><td colspan='2' align='center'><small>*You can set it as default in <b>".basename(__FILE__)."</b></small></td></tr>\n";
	echo "</table>\n</form>\n";
} else {
	$login = $not_done = false;
	$domain = 'www.filehosting.org';
	$referer = "http://$domain/";

	// Login
	echo "<table style='width:600px;margin:auto;'>\n<tr><td align='center'>\n<div id='login' width='100%' align='center'>Login to $domain</div>\n";

	$cookie = array('language' => 'en');
	if (!empty($_REQUEST['up_email'])) {
		$uploader_email = trim($_REQUEST['up_email']);

		if (!preg_match('/^[\w\.\-]+@([a-zA-Z\d\-]+\.){1,3}[a-zA-Z\d]{2,4}$/', $uploader_email)) html_error('Invalid email address.');

		$login = true;
	} else html_error('Login Failed: Email or Password are empty. Please check login data.');

	// Retrive upload ID
	echo "<script type='text/javascript'>document.getElementById('login').style.display='none';</script>\n<div id='info' width='100%' align='center'>Retrieving upload ID</div>\n";

	$page = geturl($domain, 80, '/', $referer, $cookie, 0, 0, $_GET['proxy'], $pauth);is_page($page);
	$cookie = GetCookiesArr($page, $cookie);

	if (!preg_match('@<form\s+(?:[^<>]+\s+)?action="(https?://(?:[\w\-]+\.)*filehosting\.org(/[^\"\s<>]*)?)"@i', $page, $up_url)) {
		textarea($page);
		html_error('Upload Server Not Found.');
	} else $up_url = $up_url[1] . (empty($up_url[2]) ? '/' : '');

	$post = array();
	$post['uploader_email'] = $uploader_email;
	$post['accept_tos'] = '1';
	$post['submit'] = 'Upload now';

	// Uploading
	echo "<script type='text/javascript'>document.getElementById('info').style.display='none';</script>\n";
	$url = parse_url($up_url);
	$upfiles = upfile($url['host'], defport($url), $url['path'].(!empty($url['query']) ? '?'.$url['query'] : ''), $referer, $cookie, $post, $lfile, $lname, 'upload_file', '', $_GET['proxy'], $pauth, 0, $url['scheme']);

	// Upload Finished
	echo "<script type='text/javascript'>document.getElementById('progressblock').style.display='none';</script>\n";
	is_page($upfiles);

	if (!preg_match('@(?><form(?:\s[^\>]*)?\>)(?>.*?</form>)@is', $upfiles, $form) || !preg_match('@^<form\s+(?:[^<>]+\s+)?action=\'(https?://(?:[\w\-]+\.)*filehosting\.org(/[^\"\'\s<>]*)?)\'@i', $form[0], $form_url)) {
		textarea($upfiles);
		html_error('Post-Upload Form Not Found');
	}

	$form = $form[0];
	$form_url = $form_url[1] . (empty($form_url[2]) ? '/' : '');

	preg_match_all("@<input\s*[^>]*\stype='hidden'[^>]*\sname='(\w+)'[^>]*\svalue='([^']*)'@i", $form, $inputs);
	$post = array_map('urlencode', array_map('html_entity_decode', array_combine($inputs[1], $inputs[2])));

	$cookie = GetCookiesArr($upfiles, $cookie);
	$url = parse_url($form_url);
	$page = geturl($url['host'], defport($url), $url['path'].(!empty($url['query']) ? '?'.$url['query'] : ''), $up_url, $cookie, $post, 0, $_GET['proxy'], $pauth, 0, $url['scheme']);is_page($page);
	$cookie = GetCookiesArr($page, $cookie);

	if (preg_match('@\nLocation: (https?://(?:[\w\-]+\.)*filehosting\.org)?(/[^\s]*)@i', $page, $redir)) {
		$redir = (empty($redir[1]) ? $url['scheme'] . '://' . $url['host'] : $redir[1]) . $redir[2];
		$url = parse_url($redir);
		$page = geturl($url['host'], defport($url), $url['path'].(!empty($url['query']) ? '?'.$url['query'] : ''), $form_url, $cookie, 0, 0, $_GET['proxy'], $pauth, 0, $url['scheme']);is_page($page);
	}

	if (stripos($page, 'A link to the file and a download link were sent to your email address') === false) {
		textarea($page);
		html_error('Unknown Post-Upload Error.');
	}

	$download_link = 'Check Your Email: "' . $uploader_email . '"';
}

//[15-12-2015]  Written by Th3-822.

?>