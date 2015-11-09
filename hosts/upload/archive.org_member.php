<?php
######## Account Info ########
$upload_acc['archive_org']['accessKey'] = ''; //Set your login
$upload_acc['archive_org']['secretKey'] = ''; //Set your password
# Get your Api Keys @ https://archive.org/account/s3.php
########################

$_GET['proxy'] = isset($_GET['proxy']) ? $_GET['proxy'] : '';
$not_done = true;

// Check https support for requests.
$use_curl = $options['use_curl'] && extension_loaded('curl') && function_exists('curl_init') && function_exists('curl_exec') ? true : false;
$chttps = false;
$use_https = true;
if ($use_curl) {
	$cV = curl_version();
	if (in_array('https', $cV['protocols'], true)) $chttps = true;
}
if (!extension_loaded('openssl') && !$chttps) $use_https = false;
else if (!$chttps) $use_curl = false;

$media_types = array('data', 'audio', 'image', 'movies', 'software', 'texts');

if ($upload_acc['archive_org']['accessKey'] && $upload_acc['archive_org']['secretKey']) {
	$default_acc = true;
	$_REQUEST['up_login'] = $upload_acc['archive_org']['accessKey'];
	$_REQUEST['up_pass'] = $upload_acc['archive_org']['secretKey'];
	$_REQUEST['action'] = 'FORM';
	// Edit data here for AuUL usage:
	$_REQUEST['bucket'] = ''; // Must not be empty. - Use only unaccented letters, numbers, dashes, underscores or periods. - If empty: Filtered filename.
	$_REQUEST['up_mediatype'] = 'data'; // 'data', 'audio', 'image', 'movies', 'software', 'texts' (Case sensitive) - If Invalid: 'data'.
	// $_REQUEST['is_test_item'] = '1'; // Uncomment to create bucket as a test item
	echo "<b><center>Using Default Login.</center></b>\n";
} else $default_acc = false;

if (empty($_REQUEST['action']) || $_REQUEST['action'] != 'FORM') {
	echo "<table border='0' style='width:270px;' cellspacing='0' align='center'>
	<form method='POST'>
	<input type='hidden' name='action' value='FORM' />
	<tr><td style='white-space:nowrap;'>Access Key*</td><td>&nbsp;<input type='text' name='up_login' value='' required='required' style='width:160px;' /></td></tr>
	<tr><td style='white-space:nowrap;'>Secret Key*</td><td>&nbsp;<input type='password' name='up_pass' value='' required='required' style='width:160px;' /></td></tr>\n";
	echo "<tr><td colspan='2' align='center'><br />Upload options *<br /><br /></td></tr>
	<tr><td style='white-space:nowrap;'>Identifier (Bucket):</td><td>&nbsp;<input type='text' name='bucket' pattern='[0-9a-zA-Z_\.\-]{3,255}' title='Please use only unaccented letters, numbers, dashes, underscores or periods.' style='width:160px;' /></td></tr>
	<tr><td style='white-space:nowrap;'>Media Type:</td><td>&nbsp;<select name='up_mediatype' style='width:160px;'>\n";
	foreach($media_types as $type) echo "\t<option value='$type'>".ucfirst($type)."</option>\n";
	echo "</select></td></tr>\n";
	echo "<tr><td style='white-space:nowrap;'>This is a test item?</td><td>&nbsp;<input type='checkbox' name='is_test_item' value='yes' /> <i>(remove after 30 days)</i></td></tr>";
	echo "<tr><td colspan='2' align='center'><br /><input type='submit' value='Upload' /></td></tr>\n";
	echo "<tr><td colspan='2' align='center'><i>*You can set it as default in <b>".basename(__FILE__)."</b></i></td></tr>\n";
	echo "</table>\n</form>\n";
} else {
	$login = $not_done = false;
	$domain = 'archive.org';
	$referer = "https://$domain/";

	// Clean and Validate Filename
	$lname = preg_replace('@^\.|\.\.|\.$|[^0-9a-zA-Z_\.\-]@', '', str_replace(' ', '_', $lname));
	if (empty($lname)) html_error('Filename not allowed: "Please use only unaccented letters, numbers, dashes, underscores or periods".');

	// Validate bucket name.
	if (isset($_REQUEST['bucket'])) $_REQUEST['bucket'] = trim($_REQUEST['bucket']);
	if (!empty($_REQUEST['bucket'])) {
		$len = strlen($_REQUEST['bucket']);
		if ($len < 3 || $len > 255 || preg_match('@^\.|\.\.|\.$|[^0-9a-zA-Z_\.\-]@', $_REQUEST['bucket'])) html_error('Error: Invalid Bucket Name.');
	} else {
		//$_REQUEST['bucket'] = preg_replace('/\.\w{2,4}$/', '', $lname);
		$_REQUEST['bucket'] = pathinfo($lname, PATHINFO_FILENAME);
		if (strlen($_REQUEST['bucket']) < 3) html_error('Error: Empty Bucket Name.');
	}

	// Login
	echo "<table style='width:600px;margin:auto;'>\n<tr><td align='center'>\n<div id='login' width='100%' align='center'>Login to $domain</div>\n";

	$cookie = array();
	if (!empty($_REQUEST['up_login']) && !empty($_REQUEST['up_pass'])) {
		if (!empty($_REQUEST['A_encrypted'])) {
			$_REQUEST['up_login'] = decrypt(urldecode($_REQUEST['up_login']));
			$_REQUEST['up_pass'] = decrypt(urldecode($_REQUEST['up_pass']));
			unset($_REQUEST['A_encrypted']);
		}

		$iaS3AccessKey = trim($_REQUEST['up_login']);
		$iaS3SecretKey = trim($_REQUEST['up_pass']);

		if (preg_match('@[^A-Za-z\d+/=]@', $iaS3AccessKey.$iaS3SecretKey)) {
			// Simple check for invalid chars at API keys.
			html_error('Error: Invalid Character Found at API Key.');
		}

		// Test API Keys:
		$page = ias3Request();
		if (intval(substr($page, 9, 3)) != 200) html_error('[Login Error]: HTTP ' . substr($page, 9, 3));
		$dom = page2xmldom($page, 'Login Error');
		if (strtolower(trim($dom->getElementsByTagName('DisplayName')->item(0)->nodeValue)) == 'readable id goes here' && !$dom->getElementsByTagName('Buckets')->item(0)->hasChildNodes()) {
			// When user's account is new, the api doesn't return neither a name or any buckets (including the profile itself).
			// So i can't check if the AccessKey is valid without uploading something.
			// Regenerating the API keys seems to fix that issue.
			html_error('Login Error: Invalid API Keys?, upload something via the new uploader or "Regenerate your API Keys".');
		}
		unset($dom);

		$login = true;
	} else {
		html_error('Error: Empty Access Key or Secret Key.');
	}

	// Preparing Upload
	echo "<script type='text/javascript'>document.getElementById('login').style.display='none';</script>\n<div id='info' width='100%' align='center'>Preparing Upload</div>\n";

	$uploadHeaders = array();

	// Test Bucket
	$page = ias3Request($_REQUEST['bucket']);
	$status = intval(substr($page, 9, 3));
	if ($status == 200) {
		if (!empty($_REQUEST['is_test_item'])) html_error('Bucket must not exist for upload a test item.');
		$bucketExists = true;
		$bucket = page2xmldom($page, 'Error Loading Bucket');
		if (ias3FileExists($bucket, $lname)) {
			html_error('Error: A file already exists with the same name.');
		}
	} else if ($status == 404) {
		$bucketExists = $bucket = false;
		$uploadHeaders['x-archive-auto-make-bucket'] = '1';
		$uploadHeaders['x-archive-meta-submitter'] = 'Rapidleech';
		if (!empty($_REQUEST['is_test_item'])) {
			$uploadHeaders['x-archive-meta-description'] = $uploadHeaders['x-archive-meta-subject'] = 'test item';
			$uploadHeaders['x-archive-meta-collection'] = 'test_collection';
		} else {
			$uploadHeaders['x-archive-meta-description'] = 'Uploaded with Rapidleech';
			$uploadHeaders['x-archive-meta-subject'] = 'Rapidleech';
		}
		if (!empty($_REQUEST['up_mediatype']) && in_array($_REQUEST['up_mediatype'], $media_types)) {
			$uploadHeaders['x-archive-meta-mediatype'] = $_REQUEST['up_mediatype'];
		}
	} else {
		html_error('[Error Pre Checking Bucket]: HTTP ' . $status);
	}

	// Pre-Upload test
	$page = ul_GetPage(($GLOBALS['use_https'] ? 'https' : 'http').'://s3.us.archive.org/?check_limit=1&accesskey='.urlencode($iaS3AccessKey).'&bucket='.urlencode($_REQUEST['bucket']));
	$json = json2array($page, 'Pre-Upload Error');
	if ($json['over_limit'] != 0) {
		textarea($json);
		html_error('The server is overloaded, please try to upload later.');
	}

	if (!empty($uploadHeaders)) $uploadHeaders = array_map('iaS3HeaderEncode', $uploadHeaders);
	$uploadHeaders['Authorization'] = 'LOW ' . $iaS3AccessKey . ':' . $iaS3SecretKey;
	$uploadReferer = $referer;
	foreach ($uploadHeaders as $tok => $val) {
		$uploadReferer .= "\r\n$tok: $val";
	}

	// Uploading
	echo "<script type='text/javascript'>document.getElementById('info').style.display='none';</script>\n";
	$url = parse_url('http://s3.us.archive.org/' . urlencode($_REQUEST['bucket']) . '/' . urlencode($lname));
	$upfiles = putfile($url['host'], defport($url), $url['path'].(!empty($url['query']) ? '?'.$url['query'] : ''), $uploadReferer, $cookie, $lfile, $lname, $_GET['proxy'], $pauth, 0, $url['scheme']);

	// Upload Finished
	echo "<script type='text/javascript'>document.getElementById('progressblock').style.display='none';</script>\n";

	is_page($upfiles);

	if (intval(substr($upfiles, 9, 3)) != 200) {
		if (preg_match('@<Error><Code>(\w+)</Code><Message>(?>(.*?)</Message>)@i', $upfiles, $err)) {
			switch ($err[1]) {
				case 'AccessDenied': html_error('Upload Error: Access to bucket denied, make sure that this bucket is yours.');
				case 'InvalidAccessKeyId': html_error('Upload Error: Invalid AccessKey.');
				case 'SignatureDoesNotMatch': html_error('Upload Error: Invalid or Incorrect SecretKey.');
				case 'SlowDown': case 'ServiceUnavailable': html_error('The server is overloaded and discarted this upload, please try to upload later.');
				default: html_error("Upload Error [{$err[1]}]: " . htmlspecialchars($err[2]));
			}
		}
		textarea($upfiles);
		html_error('Unknown Upload Error');
	}

	$download_link = $referer . 'download/' . urlencode($_REQUEST['bucket']) . '/' . urlencode($lname);
	$stat_link = $referer . 'catalog.php?history=1&identifier=' . urlencode($_REQUEST['bucket']);
}

function ias3FileExists($bucket, $filename) {
	$files = $bucket->getElementsByTagName('Contents');
	if ($files->length > 0 && !empty($filename)) {
		$filename = strtolower($filename);
		foreach ($files as $file) {
			if (strtolower($file->getElementsByTagName('Key')->item(0)->nodeValue) == $filename) return $file;
		}
	}
	return false;
}

function ias3Request($path = '', $header = array()) {
	if (!is_array($header)) $header = array();
	if (!empty($header)) $header = array_map('iaS3HeaderEncode', $header);
	$header['Authorization'] = 'LOW ' . $GLOBALS['iaS3AccessKey'] . ':' . $GLOBALS['iaS3SecretKey'];

	$headers = '';
	foreach ($header as $tok => $val) {
		$headers .= "\r\n$tok: $val";
	}

	$count = 0;
	$host = 's3.us.archive.org';
	do {
		$page = ul_GetPage(($GLOBALS['use_https'] ? 'https' : 'http')."://$host/$path", 0, 0, $GLOBALS['referer'].$headers);
		$status = intval(substr($page, 9, 3));
		if ($status == 307) {
			if ($count >= 2) html_error('Redirect Loop Detected.');
			if (!preg_match('@(?:\nLocation: https?://|<Endpoint>)((?:[\w\-]+\.)*archive.org)@i', $page, $host)) html_error('Redirect endpoint not found.');
			$host = $host[1];
		}
	} while ($count++ < 2 && $status == 307);

	return $page;
}

function iaS3HeaderEncode($v) {
	return (preg_match('/[^\x20-\x7E]/', $v) ? 'uri('.rawurlencode($v).')' : $v);
}

function page2xmldom($page, $errorPrefix = 'Error') {
	if (!class_exists('DOMDocument')) html_error('Error: Please install/enable the DOM module in php.');
	// Remove Headers
	if (($pos = strpos($page, "\r\n\r\n")) > 0) $body = trim(substr($page, $pos + 4));

	$dom = new DOMDocument();
	if (!$dom->loadXML($body)) html_error("[$errorPrefix]: Error reading XML.");

	return $dom;
}

function json2array($content, $errorPrefix = 'Error') {
	if (!function_exists('json_decode')) html_error('Error: Please enable JSON in php.');
	if (empty($content)) return NULL;
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

function ul_GetPage($link, $cookie = 0, $post = 0, $referer = 0, $auth = 0, $XMLRequest = 0) {
	if (!$referer && !empty($GLOBALS['Referer'])) {
		$referer = $GLOBALS['Referer'];
	}

	if ($GLOBALS['use_curl']) {
		if ($XMLRequest) $referer .= "\r\nX-Requested-With: XMLHttpRequest";
		$page = cURL($link, $cookie, $post, $referer, $auth);
	} else {
		global $pauth;
		$Url = parse_url($link);
		$page = geturl($Url['host'], defport($Url), $Url['path'] . (!empty($Url['query']) ? '?' . $Url['query'] : ''), $referer, $cookie, $post, 0, !empty($_GET['proxy']) ? $_GET['proxy'] : '', $pauth, $auth, $Url['scheme'], 0, $XMLRequest);
		is_page($page);
	}
	return $page;
}

//[28-9-2015]  Written by Th3-822.

?>