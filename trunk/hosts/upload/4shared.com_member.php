<?php
######## Account Info ########
$upload_acc['4shared_com']['user'] = ''; //Set your login
$upload_acc['4shared_com']['pass'] = ''; //Set your password
########################

if (!class_exists('SoapClient')) html_error('This plugins needs SOAP module enabled.');
$_GET['proxy'] = isset($_GET['proxy']) ? $_GET['proxy'] : '';
$not_done = true;

if (!empty($upload_acc['4shared_com']['user']) && !empty($upload_acc['4shared_com']['pass'])) {
	$_REQUEST['up_login'] = $upload_acc['4shared_com']['user'];
	$_REQUEST['up_pass'] = $upload_acc['4shared_com']['pass'];
	$_REQUEST['action'] = 'FORM';
	echo "<b><center>Using Default Login.</center></b>\n";
}

if (empty($_REQUEST['action']) || $_REQUEST['action'] != 'FORM') {
	echo "<table border='0' style='width:270px;' cellspacing='0' align='center'>
	<form method='POST'>
	<input type='hidden' name='action' value='FORM' />
	<tr><td style='white-space:nowrap;'>&nbsp;Email*</td><td>&nbsp;<input type='text' name='up_login' value='' style='width:160px;' /></td></tr>
	<tr><td style='white-space:nowrap;'>&nbsp;Password*</td><td>&nbsp;<input type='password' name='up_pass' value='' style='width:160px;' /></td></tr>\n";
	echo "<tr><td colspan='2' align='center'><br /><input type='submit' value='Upload' /></td></tr>\n";
	echo "<tr><td colspan='2' align='center'><small>*You can set it as default in <b>".basename(__FILE__)."</b></small></td></tr>\n";
	echo "</form>\n</table>\n";
} else {
	$not_done = false;

	// Login
	echo "<table style='width:600px;margin:auto;'>\n<tr><td align='center'>\n<div id='login' width='100%' align='center'>Login to 4shared.com</div>\n";

	if (!empty($_REQUEST['up_login']) && !empty($_REQUEST['up_pass'])) {
		$user = $_REQUEST['up_login'];
		$pass = $_REQUEST['up_pass'];
		$soap_options = array('connection_timeout' => 120, 'cache_wsdl' => WSDL_CACHE_DISK, 'exceptions' => false);
		if (!empty($_GET['proxy'])) {
			list($soap_options['proxy_host'], $soap_options['proxy_port']) = explode(':', $_GET['proxy'], 2);
			if (!empty($pauth)) list($soap_options['proxy_login'], $soap_options['proxy_password']) = array_map('rawurldecode', explode(':', base64_decode($pauth), 2));
		}
		$client = new SoapClient('http://api.4shared.com/jax3/DesktopApp?wsdl', $soap_options);
		if (!is_object($client)) html_error('Cannot get 4shared\'s wsdl.');

		if (($Chk = $client->hasRightUpload()) !== true) {
			if (is_soap_fault($Chk)) html_error('[' . $Chk->faultcode . '] ' . htmlentities($Chk->faultstring));
			else html_error('Uploading is temporarily disabled.');
		}

		$LoginChk = $client->isExistsLoginPassword($user, $pass);
		if (is_soap_fault($LoginChk)) html_error('[' . $LoginChk->faultcode . '] ' . htmlentities($LoginChk->faultstring));
		elseif ($LoginChk !== true) html_error('Login failed: Email/Password incorrect.');

		if ($client->isAccountBanned($user, $pass) === true) html_error('Login failed: Account is banned.');

		if ($fsize > $client->getMaxFileSize($user, $pass)) html_error('Error: Your file is too big.');
		elseif ($fsize > $client->getFreeSpace($user, $pass)) html_error('Error: Not enough space in your account.');
	} else html_error('Login failed: User/Password empty.');

	// Retrive upload ID
	echo "<script type='text/javascript'>document.getElementById('login').style.display='none';</script>\n<div id='info' width='100%' align='center'>Retrive upload ID</div>\n";

	$session = $client->createUploadSessionKey($user, $pass, -1);
	if (!$session) html_error('Error: Cannot get upload session.');

	$dc = $client->getNewFileDataCenter($user, $pass, -1);
	if ($dc <= 0) html_error('Error: Cannot get upload server.');

	$up_url = $client->getUploadFormUrl($dc, $session);
	if (!$up_url) html_error('Error: Cannot get upload url.');

	$fid = $client->uploadStartFile($user, $pass, -1, $lname, $fsize);
	if (!$fid) html_error('Error: Cannot get upload id.');

	$post = array();
	$post['resumableFileId'] = $fid;
	$post['resumableFirstByte'] = 0;

	// Uploading
	echo "<script type='text/javascript'>document.getElementById('info').style.display='none';</script>\n";

	$url = parse_url($up_url);
	$upfiles = upfile($url['host'], defport($url), $url['path'].(!empty($url['query']) ? '?'.$url['query'] : ''), 0, 0, $post, $lfile, $lname, 'FilePart', '', $_GET['proxy'], $pauth);

	// Upload Finished
	echo "<script type='text/javascript'>document.getElementById('progressblock').style.display='none';</script>\n";

	is_page($upfiles);

	$finish = $client->uploadFinishFile($user, $pass, $fid, md5_file($lfile));
	if (is_soap_fault($finish)) html_error('[' . $finish->faultcode . '] ' . htmlentities($finish->faultstring));
	elseif ($finish != '') html_error('Upload error: ' . htmlentities($finish));

	$fileinfo = $client->getFileInfo($user, $pass, $fid);
	$download_link = $fileinfo->downloadLink;
}

//[09-4-2013] Written by Th3-822

?>