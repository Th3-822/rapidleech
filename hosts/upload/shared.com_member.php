<?php
######## Account Info ########
$upload_acc['shared_com']['user'] = ''; //Set your login
$upload_acc['shared_com']['pass'] = ''; //Set your password
########################

$_GET['proxy'] = isset($_GET['proxy']) ? $_GET['proxy'] : '';
$not_done = true;

if ($upload_acc['shared_com']['user'] && $upload_acc['shared_com']['pass']) {
	$default_acc = true;
	$_REQUEST['up_login'] = $upload_acc['shared_com']['user'];
	$_REQUEST['up_pass'] = $upload_acc['shared_com']['pass'];
	$_REQUEST['action'] = 'FORM';
	echo "<b><center>Using Default Login.</center></b>\n";
} else $default_acc = false;

if (empty($_REQUEST['action']) || $_REQUEST['action'] != 'FORM') {
	echo "<table border='0' style='width:270px;' cellspacing='0' align='center'>
	<form method='POST'>
	<input type='hidden' name='action' value='FORM' />
	<tr><td style='white-space:nowrap;'>&nbsp;Login*</td><td>&nbsp;<input type='text' name='up_login' value='' style='width:160px;' /></td></tr>
	<tr><td style='white-space:nowrap;'>&nbsp;Password*</td><td>&nbsp;<input type='password' name='up_pass' value='' style='width:160px;' /></td></tr>\n";
	echo "<tr><td colspan='2' align='center'><br /><input type='submit' value='Upload' /></td></tr>\n";
	echo "<tr><td colspan='2' align='center'><small>*You can set it as default in <b>".basename(__FILE__)."</b></small></td></tr>\n";
	echo "</table>\n</form>\n";
} else {
	$login = $not_done = false;
	$domain = 'shared.com';
	$referer = "https://$domain/";

	// Login
	echo "<table style='width:600px;margin:auto;'>\n<tr><td align='center'>\n<div id='login' width='100%' align='center'>Login to $domain</div>\n";

	$cookie = array();
	if (!empty($_REQUEST['up_login']) && !empty($_REQUEST['up_pass'])) {
		if (!empty($_REQUEST['A_encrypted'])) {
			$_REQUEST['up_login'] = decrypt(urldecode($_REQUEST['up_login']));
			$_REQUEST['up_pass'] = decrypt(urldecode($_REQUEST['up_pass']));
			unset($_REQUEST['A_encrypted']);
		}

		$page = geturl($domain, 80, '/login', $referer.'login', $cookie, 0, 0, $_GET['proxy'], $pauth);is_page($page);
		$cookie = GetCookiesArr($page, $cookie);

		$post = array();
		$post['utf8'] = urlencode(html_entity_decode(cut_str($page, 'name="utf8" type="hidden" value="', '"'), ENT_QUOTES, 'UTF-8'));
		$post['authenticity_token'] = cut_str($page, 'name="authenticity_token" type="hidden" value="', '"');
		if (empty($post['authenticity_token'])) html_error('CSRF token not found.');
		$post['user%5Bemail%5D'] = urlencode($_REQUEST['up_login']);
		$post['user%5Bpassword%5D'] = urlencode($_REQUEST['up_pass']);
		$post['submit'] = 'Log+In';

		$page = geturl($domain, 80, '/login', $referer.'login', $cookie, $post, 0, $_GET['proxy'], $pauth);is_page($page);
		$cookie = GetCookiesArr($page, $cookie);

		is_present($page, 'Login incorrect', 'Login Failed: Email/Password incorrect.');
		if (!preg_match('@\nLocation: (?:https?://shared.com)?/u/([^\r\n]+)@i', $page, $uname)) html_error('Login Error: Login redirect not found.');
		$login = true;
	} else html_error('Login failed: User/Password empty.');

	// Retrive upload ID
	echo "<script type='text/javascript'>document.getElementById('login').style.display='none';</script>\n<div id='info' width='100%' align='center'>Retrieving upload ID</div>\n";

	$page = geturl($domain, 80, '/u/'.$uname[1], $referer.'login', $cookie, 0, 0, $_GET['proxy'], $pauth);is_page($page);
	$cookie = GetCookiesArr($page, $cookie);

	if (!preg_match('@action="((https?://(?:\w+\.)*shared\.com)?(/)?[^\s\'\"<>]+)"\s+class="no-files-selected"@i', $page, $up)) html_error('Error: Upload url not found.');

	$csrf = trim(cut_str($page, 'name="authenticity_token" type="hidden" value="', '"'));
	if (empty($csrf)) html_error('Error: Upload CSRF token not found.');

	$post = array();
	$post['status'] = '1';

	$ref = $referer.'u/'.$uname[1]."\r\nX-CSRF-Token: $csrf";

	$up_url = (empty($up[2]) ? "http://$domain".(empty($up[3]) ? '/' : '').$up[1] : $up[1]);

	// Uploading
	echo "<script type='text/javascript'>document.getElementById('info').style.display='none';</script>\n";

	$url = parse_url($up_url);
	$upfiles = upfile($url['host'], defport($url), $url['path'].(!empty($url['query']) ? '?'.$url['query'] : ''), $ref, $cookie, $post, $lfile, $lname, 'files[]', '', 0, 0, 0, $url['scheme']);

	// Upload Finished
	echo "<script type='text/javascript'>document.getElementById('progressblock').style.display='none';</script>\n";

	is_page($upfiles);

	if (!preg_match('@"slug"\s*:\s*"([^"]+)",@i', $upfiles, $lnk)) html_error('Download link not found.');
	$download_link = 'http://shared.com/'.$lnk[1];
}

//[09-3-2014]  Written by Th3-822.
//[15-3-2014]  Fixed login redirect. - Th3-822
//[26-5-2014]  Removed https, site is forcing http connections now. - Th3-822

?>