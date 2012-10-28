<?php
######## Account Info ########
$upload_acc['uploaded_net']['user'] = ''; //Set your userid/alias
$upload_acc['uploaded_net']['pass'] = ''; //Set your password
########################

$_GET['proxy'] = isset($_GET['proxy']) ? $_GET['proxy'] : '';
$not_done = true;
$continue_up = false;

if ($upload_acc['uploaded_net']['user'] && $upload_acc['uploaded_net']['pass']) {
	$_REQUEST['up_login'] = $upload_acc['uploaded_net']['user'];
	$_REQUEST['up_pass'] = $upload_acc['uploaded_net']['pass'];
	$_REQUEST['action'] = 'FORM';
	echo "<b><center>Using Default Login.</center></b>\n";
}

if (!empty($_REQUEST['action']) && $_REQUEST['action'] == 'FORM') $continue_up = true;
else {
	echo "<table border='0' style='width:270px;' cellspacing='0' align='center'>
	<form method='POST'>
	<input type='hidden' name='action' value='FORM' />
	<tr><td style='white-space:nowrap;'>&nbsp;User*</td><td>&nbsp;<input type='text' name='up_login' value='' style='width:160px;' /></td></tr>
	<tr><td style='white-space:nowrap;'>&nbsp;Password*</td><td>&nbsp;<input type='password' name='up_pass' value='' style='width:160px;' /></td></tr>\n";
	echo "<tr><td colspan='2' align='center'><br /><input type='submit' value='Upload' /></td></tr>\n";
	echo "<tr><td colspan='2' align='center'><small>*You can set it as default in <b>".basename(__FILE__)."</b></small></td></tr>\n";
	echo "</table>\n</form>\n";
}

if ($continue_up) {
	$not_done = false;
	$referer = 'http://uploaded.net/';

	// Login
	echo "<table style='width:600px;margin:auto;'>\n<tr><td align='center'>\n<div id='login' width='100%' align='center'>Login to uploaded.net</div>\n";

	$cookie = array();
	if (!empty($_REQUEST['up_login']) && !empty($_REQUEST['up_pass'])) {
		$post = array();
		$post['id'] = urlencode($_REQUEST['up_login']);
		$post['pw'] = urlencode($_REQUEST['up_pass']);

		$page = geturl('uploaded.net', 80, '/io/login', $referer."\r\nX-Requested-With: XMLHttpRequest", $cookie, $post, 0, $_GET['proxy'], $pauth);is_page($page);
		if (preg_match('@"err":"([^"]+)"@i', $page, $err)) html_error('Login Error: "'.htmlentities($err[1]).'".');
		$cookie = GetCookiesArr($page, $cookie);
		if (empty($cookie['login']) || empty($cookie['auth'])) html_error('Login Error: Login cookies not found.');
	} else html_error('Login Failed: Email or Password are empty. Please check login data.');

	// Retrive upload ID
	echo "<script type='text/javascript'>document.getElementById('login').style.display='none';</script>\n<div id='info' width='100%' align='center'>Retrive upload ID</div>\n";

	$page = geturl('uploaded.net', 80, '/', $referer, $cookie, 0, 0, $_GET['proxy'], $pauth);is_page($page);
	$js = geturl('uploaded.net', 80, '/js/script.js', $referer, $cookie, 0, 0, $_GET['proxy'], $pauth);is_page($js);

	if (!preg_match('@uploadServer = [\'|\"](https?://([^\|\'|\"|\r|\n|\s|\t]+\.)uploaded\.net/)[\'|\"]@i', $js, $up)) html_error('Error: Cannot find upload server.', 0);

	if (!preg_match('@id="user_id" value="(\d+)"@i', $page, $uid)) html_error('Error: UserID not found.');
	if (!preg_match('@id="user_pw" value="(\w+)"@i', $page, $spass)) html_error('Error: Password hash not found.'); // $spass = array(1 => sha1($_REQUEST['up_pass']));
	$adm_link = generate();

	$post = array();
	$post['Filename'] = $lname;
	$post['Upload'] = 'Submit Query';

	$up_url = $up[1]."upload?admincode=$adm_link&id={$uid[1]}&pw={$spass[1]}";

	// Uploading
	echo "<script type='text/javascript'>document.getElementById('info').style.display='none';</script>\n";

	$url = parse_url($up_url);
	$upfiles = upfile($url['host'], 80, $url['path'].($url['query'] ? '?'.$url['query'] : ''), $referer, $cookie, $post, $lfile, $lname, 'Filedata', '', $_GET['proxy'], $pauth, 'Shockwave Flash');

	// Upload Finished
	echo "<script type='text/javascript'>document.getElementById('progressblock').style.display='none';</script>\n";

	is_page($upfiles);

	$content = substr($upfiles, strpos($upfiles, "\r\n\r\n") + 4);

	if (preg_match('@^(\w+)\,\d@i', $content, $fid)) {
		$download_link = 'http://uploaded.net/file/'.$fid[1]; // $download_link = 'http://ul.to/'.$fid[1];
	} else html_error('Download link not found.', 0);

}

function generate($len = 6) {
	$pwd = '';
	$con = array('b','c','d','f','g','h','j','k','l','m','n','p','r','s','t','v','w','x','y','z');
	$voc = array('a','e','i','o','u');

	for($i = 0; $i < $len/2; $i++) {
		$c = mt_rand(0, 1000) % 20;
		$v = mt_rand(0, 1000) % 5;
		$pwd .= $con[$c] . $voc[$v];
	}

	return $pwd;
}

//[26-8-2012] Written by Th3-822.
//[02-10-2012] Fixed link regexp. - Th3-822

?>