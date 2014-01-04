<?php

######## Account Info ########
$upload_acc['uploadhero_co']['user'] = ''; //Set your login
$upload_acc['uploadhero_co']['pass'] = ''; //Set your password
########################

$GetUpUrlFromApi = true; // Leave this in true for get the upload url from api, false for get it from main site.
$_GET['proxy'] = isset($_GET['proxy']) ? $_GET['proxy'] : '';
$not_done = true;

if (!empty($upload_acc['uploadhero_co']['user']) && !empty($upload_acc['uploadhero_co']['pass'])) {
	$_REQUEST['up_login'] = $upload_acc['uploadhero_co']['user'];
	$_REQUEST['up_pass'] = $upload_acc['uploadhero_co']['pass'];
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
	$cookie = array();

	// Login
	echo "<table style='width:600px;margin:auto;'>\n<tr><td align='center'>\n<div id='login' width='100%' align='center'>Login to uploadhero.co</div>\n";

	if (!empty($_REQUEST['up_login']) && !empty($_REQUEST['up_pass'])) {
		$query = array();
		$query['u'] = $_REQUEST['up_login'];
		$query['p'] = $_REQUEST['up_pass'];

		$page = geturl('api.uploadhero.co', 80, '/upload.php?'.http_build_query($query), '', 0, 0, 0, $_GET['proxy'], $pauth);is_page($page);
		$body = substr($page, strpos($page, "\r\n\r\n") + 4);
		is_present($body, 'Invalid Username/password', 'Login failed: User/Password incorrect.');
		$login = true;
	} else {
		echo "<b><center>Login not found or empty, using non member upload.</center></b>\n";
		if ($GetUpUrlFromApi) {
			$page = geturl('api.uploadhero.co', 80, '/upload.php', '', 0, 0, 0, $_GET['proxy'], $pauth);
			is_page($page);
		}
		$login = false;
	}

	// Retrive upload ID
	echo "<script type='text/javascript'>document.getElementById('login').style.display='none';</script>\n<div id='info' width='100%' align='center'>Preparing upload</div>\n";

	if (!$GetUpUrlFromApi) {
		$page = geturl('uploadhero.co', 80, '/', '', 0, 0, 0, $_GET['proxy'], $pauth);
		is_page($page);
	}

	if (!preg_match('@https?://(?:[a-zA-Z\d\-]+\.)*uploadhero\.com?/upload/upload\.php[^\r\n\s\t<>\"\']*@i', $page, $up)) html_error('Error: Cannot find upload URL.', 0);

	if ($login) {
		if (!preg_match('@<params>[\r\n\s\t]*([^<>\r\n\s\t]+)[\r\n\s\t]*</params>@i', $body, $pars)) html_error('Upload params not found.');
		$post = FormToArr($pars[1].'&');
	} else $post = array();

	$up_url = $up[0];

	// Uploading
	echo "<script type='text/javascript'>document.getElementById('info').style.display='none';</script>\n";

	$url = parse_url($up_url);
	$upfiles = upfile($url['host'], defport($url), $url['path'].(!empty($url['query']) ? '?'.$url['query'] : ''), '', 0, $post, $lfile, $lname, 'Filedata', '', $_GET['proxy'], $pauth);

	// Upload Finished
	echo "<script type='text/javascript'>document.getElementById('progressblock').style.display='none';</script>\n";

	is_page($upfiles);

	is_present($upfiles, "\r\n\r\nupload_error", 'Your upload has failed.');
	$body = trim(substr($upfiles, strpos($upfiles, "\r\n\r\n") + 4));

	if (!$login) {
		$finds = array('%20', '%27', '%C3%A9', '%C3%A8', '%E2%82%AC', '%25', '%26');
		$replace = array('kkk', 'qspkq', 'ab12', 'ab21', 'eurobcd', 'pourcentbcd', 'uuuuuu');
		$name = str_replace($finds, $replace, rawurlencode($lname));
		$page = geturl('uploadhero.co', 80, "/fileinfo.php?folder=&name=$name&size=$fsize", '', 0, 0, 0, $_GET['proxy'], $pauth);is_page($page);
	}

	if (!$login && preg_match('@https?://(?:www\.)?uploadhero\.com?/dl/[^\r\n\s\t<>\"\']+@i', $page, $dlnk)) {
		$download_link = $dlnk[0];
		if (preg_match('@https?://(?:www\.)?uploadhero\.com?/delete/[^\r\n\s\t<>\"\']+@i', $page, $dellnk)) $delete_link = $dellnk[0];
	} elseif (strlen($body) == 8) $download_link = "http://uploadhero.co/dl/$body";
	else html_error('Download link not found.', 0);
}

function FormToArr($content, $v1 = '&', $v2 = '=') {
	$rply = array();
	if (empty($content) || strpos($content, $v1) === false || strpos($content, $v2) === false) return $rply;
	foreach (array_filter(array_map('trim', explode($v1, $content))) as $v) {
		$v = array_map('trim', explode($v2, $v, 2));
		if ($v[0] != '') $rply[$v[0]] = $v[1];
	}
	return $rply;
}

//[03-4-2013] Written by Th3-822.
//[24-11-2013] Fixed get download link as member. - Th3-822

?>