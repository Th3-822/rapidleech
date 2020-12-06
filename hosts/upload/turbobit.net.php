<?php
######## Account Info ########
$upload_acc['turbobit_net']['apikey'] = ''; //Set your apikey
########################

$_GET['proxy'] = isset($_GET['proxy']) ? $_GET['proxy'] : '';
$not_done = true;
$continue_up = false;

if (!empty($upload_acc['turbobit_net']['apikey'])) {
	$default_acc = true;
	$_REQUEST['up_apikey'] = $upload_acc['turbobit_net']['apikey'];
	$_REQUEST['action'] = 'FORM';
	echo "<b><center>Using Default ApiKey.</center></b>\n";
} else $default_acc = false;

if (!empty($_REQUEST['action']) && $_REQUEST['action'] == 'FORM') $continue_up = true;
else {
	echo "<table border='0' cellspacing='0' align='center'>
	<form method='POST'>
	<input type='hidden' name='action' value='FORM' />
	<tr><td style='white-space:nowrap;'>&nbsp;API Key*</td><td>&nbsp;<input type='text' name='up_apikey' placeholder='Get it @ https://turbobit.net/user/settings' value='' style='width:250px;' /></td></tr>\n";
	echo "<tr><td colspan='2' align='center'><br /><input type='submit' value='Upload' /></td></tr>\n";
	echo "<tr><td colspan='2' align='center'><small>*You can set it as default in <b>".basename(__FILE__)."</b></small></td></tr>\n";
	echo "</table>\n</form>\n";
}

if ($continue_up) {
	$login = $not_done = false;
	$domain = 'turbobit.net';
	$referer = "https://$domain/";

	// Login
	echo "<table style='width:600px;margin:auto;'>\n<tr><td align='center'>\n<div id='login' width='100%' align='center'>Login to $domain</div>\n";

	$cookie = array('user_lang' => 'en');
	if (!empty($_REQUEST['up_apikey'])) {
		$page = geturl($domain, 443, '/v001/upload/http/server', 0, 0, array('api_key' => urlencode(trim($_REQUEST['up_apikey']))), 0, $_GET['proxy'], $pauth, 0, 'https');is_page($page);
		$json = json2array($page, 'Login Error');

		if (empty($json['result'])) {
			if (!empty($json['message'])) html_error('[Login Error] ' . htmlspecialchars($json['message']));
			html_error('[Login Error] Unexpected Reply.');
		}

		$login = true;
	} else {
		echo "<b><center>Login not found or empty, using non member upload.</center></b>\n";
		if ($fsize > 209715200) html_error('File is too big for anon upload (> 200 MiB)'); // 200 MiB
	}

	// Retrive upload ID
	echo "<script type='text/javascript'>document.getElementById('login').style.display='none';</script>\n<div id='info' width='100%' align='center'>Retrive upload ID</div>\n";

	if ($login) {
		if (empty($json['url'])) html_error('Error: API Upload URL not found.');
		if (empty($json['params']) || !is_array($json['params'])) html_error('Error: API Upload params not found.');

		$up_url = $json['url'];
		$post = $json['params'];
	} else {
		$page = geturl($domain, 443, '/', $referer, $cookie, 0, 0, $_GET['proxy'], $pauth, 0, 'https');is_page($page);

		if (!preg_match('@https?://s\d+\.turbobit\.net/uploadfile@i', $page, $up_url)) html_error('Error: Upload URL not found.');

		$post = array();
		$post['Filename'] = $lname;
		$post['apptype'] = cut_str($page, 'name="apptype" value="', '"');
		$up_url = $up_url[0];
	}

	// Uploading
	echo "<script type='text/javascript'>document.getElementById('info').style.display='none';</script>\n";

	$url = parse_url($up_url);
	$upfiles = upfile($url['host'], defport($url), $url['path'].(!empty($url['query']) ? '?'.$url['query'] : ''), 0, 0, $post, $lfile, $lname, 'Filedata', '', $_GET['proxy'], $pauth, 0, $url['scheme']);

	// Upload Finished
	echo "<script type='text/javascript'>document.getElementById('progressblock').style.display='none';</script>\n";

	is_page($upfiles);

	$json = json2array($upfiles);
	if ((empty($json['result']) || $json['result'] != 'true') || empty($json['id'])) html_error('Upload error: "'.htmlspecialchars($json['message']).'"');
	$id = is_array($json['id']) ? $json['id']['fid'] : $json['id'];

	if (!$login) {
		$page = geturl($domain, 443, '/newfile/gridFile/'.$id, $referer."newfile/edit/\r\nX-Requested-With: XMLHttpRequest", $cookie, 0, 0, $_GET['proxy'], $pauth, 0, 'https');is_page($page);
		$json = json2array($page);
		$info = reset($json['rows']);
		if (!empty($info['cell'][7])) $delete_link = sprintf('%sdelete/file/%s/%s', $referer, $id, $info['cell'][7]);
	}

	$download_link = "$referer$id.html";
}

function json2array($content, $errorPrefix = 'Error') {
	if (!function_exists('json_decode')) html_error('Error: Please enable JSON in php.');
	if (empty($content)) html_error("[$errorPrefix]: No content.");
	$content = ltrim($content);
	if (($pos = strpos($content, "\r\n\r\n")) > 0) $content = trim(substr($content, $pos + 4));
	$cb_pos = strpos($content, '{');
	$sb_pos = strpos($content, '[');
	if ($cb_pos === false && $sb_pos === false) html_error("[$errorPrefix]: JSON start braces not found.");
	$sb = ($cb_pos === false || $sb_pos < $cb_pos) ? true : false;
	$content = substr($content, strpos($content, ($sb ? '[' : '{')));$content = substr($content, 0, strrpos($content, ($sb ? ']' : '}')) + 1);
	if (empty($content)) html_error("[$errorPrefix]: No JSON content.");
	$rply = json_decode($content, true);
	if ($rply === NULL) html_error("[$errorPrefix]: Error reading JSON.");
	return $rply;
}

//[11-1-2013] Written by Th3-822.
//[07-6-2013] Added login recaptcha support. - Th3-822
//[22-8-2013] Fixed for changes at upload page. - Th3-822
//[06-9-2013] Fixed get fileid. - Th3-822
//[12-2-2017] Removed CAPTCHA (Unsupported ATM) and Fixed Login (There were Huges Typos on It). - Th3-822
//[06-12-2020] Switched to API Upload instead of login. - Th3-822