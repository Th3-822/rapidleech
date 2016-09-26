<?php
######## Account Info ########
$upload_acc['multiup_org']['user'] = ''; //Set your login
$upload_acc['multiup_org']['pass'] = ''; //Set your password
#######################

$DontUlTo = array();
#Plugin Settings#
	# Uncheck/Disable Upload Sites: (Note: It'll upload to sites non listed here if checked at login page).

		$DontUlTo['sitename.tld'] = true; // This will uncheck by default the checkbox for upload to 'sitename.tld' at login form and it won't upload to that host at auul.

	# MultiUP have added a new site and you want to uncheck/disable it from uploading?: Copy the name showed in Upload to these hosts* and add it in a new line (Lowercase name).
###########

// Don't edit from here unless you know what are you doing.
$not_done = true;
$domain = 'www.multiup.org';
$referer = "http://$domain/";

$page = geturl($domain, 80, '/api/get-list-hosts', $referer, 0, 0, 0, $_GET['proxy'], $pauth);is_page($page);
$page = Get_Reply($page);
if (empty($page['hosts'])) {
	if (empty($page['error']) || strtolower($page['error']) == 'success') html_error('Failed to get hosts list.');
	html_error('Failed to get hosts list: ' . htmlentities($page['error']) . '.');
}
$sites = array_combine(array_map('strtolower', array_keys($page['hosts'])), array_values($page['hosts']));
unset($page);

if (!empty($upload_acc['multiup_org']['user']) && !empty($upload_acc['multiup_org']['pass'])) {
	$default_acc = true;
	$_REQUEST['up_login'] = $upload_acc['multiup_org']['user'];
	$_REQUEST['up_pass'] = $upload_acc['multiup_org']['pass'];
	$_REQUEST['action'] = 'FORM';
	echo "<b><center>Using Default Login.</center></b>\n";
} else $default_acc = false;

if (empty($_REQUEST['action']) || $_REQUEST['action'] != 'FORM') {
	echo "<table border='0' style='width:270px;' cellspacing='0' align='center'>
	<form method='POST'>
	<input type='hidden' name='action' value='FORM' />
	<tr><td style='white-space:nowrap;'>&nbsp;Login*</td><td>&nbsp;<input type='text' name='up_login' value='' style='width:160px;' /></td></tr>
	<tr><td style='white-space:nowrap;'>&nbsp;Password*</td><td>&nbsp;<input type='password' name='up_pass' value='' style='width:160px;' /></td></tr>\n";
	echo "\t<tr><td colspan='2' align='center'><br />Upload to these hosts*<br /><br /></td></tr>\n";
	foreach ($sites as $site => $data) echo "\t<tr><td style='white-space:nowrap;' align='left'><input type='checkbox' name='UpT8[" . htmlentities($site, ENT_QUOTES) . "]' value='1'" . (($fsize > ($data['size'] * 1048576)) ? " title='This file is too heavy for the max size allowed for this hoster.'" : (!empty($DontUlTo[$site]) ? " title='Unchecked by default for \$DontUlTo setting.'" : " checked='checked'")) . " /></td><td style='white-space:nowrap;' align='right'>&nbsp;".htmlentities($site)."&nbsp; ({$data['size']} MB)</td>\n";
	echo "<tr><td colspan='2' align='center'><br /><input type='submit' value='Upload' onclick='javascript:return checkh();' /></td></tr>\n";
	echo "<tr><td colspan='2' align='center'><small>*You can set it as default in <b>" . basename(__FILE__) . "</b></small></td></tr>\n";
	echo "</form>\n</table>\n";
	echo "<script type='text/javascript'>/*<![CDATA[*/\nself.resizeTo(700,600);function checkh() {if ($(':checkbox').filter(':checked').length < 1) {alert('You mush select at least one filehoster for upload.'); return false;} return true;\n}\n/*]]>*/</script>\n"; //Resize upload window && Form Check
} else {
	$login = $not_done = false;

	// Login
	echo "<table style='width:600px;margin:auto;'>\n<tr><td align='center'>\n<div id='login' width='100%' align='center'>Login to $domain</div>\n";

	$cookie = array('user_lang' => 'en');
	if (!empty($_REQUEST['up_login']) && !empty($_REQUEST['up_pass'])) {
		$post = array('username' => urlencode($_REQUEST['up_login']), 'password' => urlencode($_REQUEST['up_pass']));

		$page = geturl($domain, 80, '/api/login', $referer, 0, $post, 0, $_GET['proxy'], $pauth);is_page($page);
		$json = Get_Reply($page);
		if (empty($json['user'])) {
			if (empty($json['error']) || strtolower($json['error']) == 'success') html_error('Login error: UserID value not found.');
			html_error('Login error: ' . htmlentities($json['error']) . '.');
		}
		$user = $json['user'];

		$login = true;
	} else echo "<b><center>Login not found or empty, using non member upload.</center></b>\n";

	// Retrive upload ID
	echo "<script type='text/javascript'>document.getElementById('login').style.display='none';</script>\n<div id='info' width='100%' align='center'>Retrive upload ID</div>\n";

	$page = geturl($domain, 80, '/api/get-fastest-server', $referer, 0, 0, 0, $_GET['proxy'], $pauth);is_page($page);
	$json = Get_Reply($page);
	if (empty($json['server'])) {
		if (empty($json['error']) || strtolower($json['error']) == 'success') html_error('Cannot get upload url.');
		html_error('Cannot get upload url: ' . htmlentities($json['error']) . '.');
	}

	$post = array();
	if ($login) $post['user'] = $user;
	$i = 1;
	foreach (array_keys($sites) as $site) if ((!empty($_POST['UpT8']) && !empty($_POST['UpT8'][$site])) || (empty($_POST['UpT8']) && empty($DontUlTo[$site]))) $post['host' . $i++] = $site;

	$up_url = $json['server'];

	// Uploading
	echo "<script type='text/javascript'>document.getElementById('info').style.display='none';</script>\n";

	$url = parse_url($up_url);
	$upfiles = upfile($url['host'], defport($url), $url['path'].(!empty($url['query']) ? '?'.$url['query'] : ''), $referer, 0, $post, $lfile, $lname, 'files[]', '', $_GET['proxy'], $pauth, 0, $url['scheme']);

	// Upload Finished
	echo "<script type='text/javascript'>document.getElementById('progressblock').style.display='none';</script>\n";

	is_page($upfiles);

	$json = Get_Reply($upfiles);
	if (empty($json['url'])) {
		if (empty($json['error']) || strtolower($json['error']) == 'success') html_error('Upload error.');
		html_error('Upload error: ' . htmlentities($json['error']) . '.');
	}

	$download_link = $json['url'];
}

function Get_Reply($page) {
	if (!function_exists('json_decode')) html_error('Error: Please enable JSON in php.');
	$body = substr($page, strpos($page, "\r\n\r\n") + 4);
	if (empty($body)) html_error('Error reading json (Empty page).');
	$body = substr($body, strpos($body, '{'));$body = substr($body, 0, strrpos($body, '}') + 1);
	$json = json_decode($body, true);
	if (empty($json)) html_error('Error reading json.');
	return $json;
}

//[25-1-2013] Written by Th3-822.
//[14-12-2015] Fixed. - Th3-822
//[19-7-2016] Added referer to requests to fix small bug. - Th3-822

?>