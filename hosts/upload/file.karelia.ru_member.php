<?php
######## Account Info ########
$upload_acc['karelia_ru']['user'] = ''; //Set your login
$upload_acc['karelia_ru']['pass'] = ''; //Set your password
########################

$_GET['proxy'] = isset($_GET['proxy']) ? $_GET['proxy'] : '';
$not_done = true;

if ($upload_acc['karelia_ru']['user'] && $upload_acc['karelia_ru']['pass']) {
	$default_acc = true;
	$_REQUEST['up_login'] = $upload_acc['karelia_ru']['user'];
	$_REQUEST['up_pass'] = $upload_acc['karelia_ru']['pass'];
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
	$domain = 'file.karelia.ru';
	$referer = "http://$domain/";

	// Login
	echo "<table style='width:600px;margin:auto;'>\n<tr><td align='center'>\n<div id='login' width='100%' align='center'>Login to $domain</div>\n";

	$cookie = array('cookieon' => '1');
	if (!empty($_REQUEST['up_login']) && !empty($_REQUEST['up_pass'])) {
		if (!empty($_REQUEST['A_encrypted'])) {
			$_REQUEST['up_login'] = decrypt(urldecode($_REQUEST['up_login']));
			$_REQUEST['up_pass'] = decrypt(urldecode($_REQUEST['up_pass']));
			unset($_REQUEST['A_encrypted']);
		}

		$page = geturl($domain, 80, '/login/img', $referer, $cookie);is_page($page);
		$cookie = GetCookiesArr($page, $cookie);

		$x = 0;
		while ($x < 2 && preg_match('@\nLocation: (https?://([\w\-]+\.)*(?:karelia|sampo)\.ru/[^\r\n]+)@i', $page, $redir)) {
			$redir = parse_url($redir[1]);
			$page = geturl($redir['host'], defport($redir), $redir['path'].(!empty($redir['query']) ? '?'.$redir['query'] : ''), $referer.'login/img', $cookie, 0, 0, 0, 0, 0, $redir['scheme']);is_page($page);
			$cookie = GetCookiesArr($page, $cookie);
			$x++;
		}

		$post = array();
		$post['nigol'] = urlencode($_REQUEST['up_login']);
		$post['drowssap'] = urlencode($_REQUEST['up_pass']);

		$page = geturl($domain, 80, '/login', "$referer\r\nX-Requested-With: XMLHttpRequest", $cookie, $post);is_page($page);
		$cookie = GetCookiesArr($page, $cookie);

		is_present($page, '\u041b\u043e\u0433\u0438\u043d \u0438\u043b\u0438 \u043f\u0430\u0440\u043e\u043b\u044c \u043d\u0435\u0432\u0435\u0440\u043d\u044b\u0439.', 'Login Failed: Email/Password incorrect.');
		is_notpresent($page, '"success":true', 'Login Error.');
		$login = true;
	} else html_error('Login failed: User/Password empty.');

	// Retrive upload ID
	echo "<script type='text/javascript'>document.getElementById('login').style.display='none';</script>\n<div id='info' width='100%' align='center'>Retriving upload ID</div>\n";

	$page = geturl($domain, 80, '/', $referer, $cookie);is_page($page);
	$cookie = GetCookiesArr($page, $cookie);

	$x = 0;
	while ($x < 3 && preg_match('@\nLocation: (https?://([\w\-]+\.)*(?:karelia|sampo)\.ru/[^\r\n]*)@i', $page, $redir)) {
		$redir = parse_url($redir[1]);
		$page = geturl($redir['host'], defport($redir), $redir['path'].(!empty($redir['query']) ? '?'.$redir['query'] : ''), $referer.'login/img', $cookie, 0, 0, 0, 0, 0, $redir['scheme']);is_page($page);
		$cookie = GetCookiesArr($page, $cookie);
		$x++;
	}

	if (!preg_match('@https?://(?:[\w\-]+\.)+file\.(?:karelia|sampo)\.ru/upload[^\s\'\"<>]*@i', $page, $up)) html_error('Error: Upload URL not found.');
	if (!preg_match('@uploadStart\s*\(\s*\'?(\d+)\'?\s*,\s*\'(\w+)\'\s*\)@i', $page, $id_hash)) html_error('Error: Upload ID not found.');
	if (!preg_match('@"login"\s*:\s*"([^\"]+)"\s*,\s*"login_hash"\s*:\s*"(\w+)"@i', $page, $login_lhash)) html_error('Error: Userdata not found.');

	$post = array();
	$post['Filename'] = $lname;
	$post['login'] = $login_lhash[1];
	$post['login_hash'] = $login_lhash[2];
	$post['uploader'] = 'swf';
	$post['uploadId'] = $id_hash[1];
	$post['uploadId_hash'] = $id_hash[2];
	$post['Upload'] = 'Submit Query';

	$up_url = $up[0];

	// Uploading
	echo "<script type='text/javascript'>document.getElementById('info').style.display='none';</script>\n";

	$url = parse_url($up_url);
	$upfiles = upfile($url['host'], defport($url), $url['path'].(!empty($url['query']) ? '?'.$url['query'] : ''), 0, 0, $post, $lfile, $lname, 'Filedata[]', '', 0, 0, 'Shockwave Flash', $url['scheme']);

	// Upload Finished
	echo "<script type='text/javascript'>document.getElementById('progressblock').style.display='none';</script>\n";

	is_page($upfiles);

	$json = Get_Reply($upfiles);
	if (empty($json['result']) || $json['result'] != 'success' || empty($json['url'])) html_error('Download link not found.', 0);
	$download_link = $json['url'];
}

function Get_Reply($content) {
	if (!function_exists('json_decode')) html_error('Error: Please enable JSON in php.');
	if (($pos = strpos($content, "\r\n\r\n")) > 0) $content = substr($content, $pos + 4);
	$cb_pos = strpos($content, '{');
	$sb_pos = strpos($content, '[');
	if ($cb_pos === false && $sb_pos === false) html_error('Json start braces not found.');
	$sb = ($cb_pos === false || $sb_pos < $cb_pos) ? true : false;
	$content = substr($content, strpos($content, ($sb ? '[' : '{')));$content = substr($content, 0, strrpos($content, ($sb ? ']' : '}')) + 1);
	if (empty($content)) html_error('No json content.');
	$rply = json_decode($content, true);
	if (!$rply || count($rply) == 0) html_error('Error reading json.');
	return $rply;
}

//[09-3-2014]  Written by Th3-822.

?>