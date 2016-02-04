<?php

// ** VK (ВКонтакте) Upload Plugin by Th3-822 **
// Create a Application at http://vk.com/editapp?act=create
// Select "Web Site" and add your rapidleech url and the base domain.
// After creating the app, copy the Application ID and the Secret key. and add them at $app.

######## Plugin's Info ########
$app = array();
$app['id'] = ''; //Application ID
$app['secret'] = ''; //Application SecretKey
$upload_audio_as_video = false; // Upload Audio files to "My Videos"...
$upload_video_as_doc = false; // Upload original Video file as Doc (Limit 200 MB)...
########################
if (empty($app['id']) || empty($app['secret'])) html_error('Application ID or SecretKey Empty. Please create a VK\'s app and add them at '.HOST_DIR.'upload/'.basename(__FILE__));

$not_done = true;
$_GET['proxy'] = isset($proxy) ? $proxy : (isset($_GET['proxy']) ? $_GET['proxy'] : '');

$return_url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'] . '?uploaded='.urlencode($_REQUEST['uploaded']).'&filename='.urlencode(base64_encode($lname));
if (!empty($_GET['proxy'])) $return_url .= '&useuproxy=on&uproxy='.urlencode($_GET['proxy']);
if (!empty($_REQUEST['pauth'])) $return_url .= '&upauth='.urlencode($pauth);
if (!empty($_GET['save_style'])) $return_url .= '&save_style='.urlencode($_GET['save_style']);
if (isset($_GET['auul'])) $return_url .= '&auul='.urlencode($_GET['auul']);

$fileExts = array(
	'video' => array('avi', 'mp4', '3gp', 'mpg', 'mpeg', 'mov', 'flv', 'wmv'),
	'audio' => array('mp3'),
	'block' => array('exe', 'scr', 'msi', 'com', 'cmd', 'bat', 'reg', 'pif', 'vbs', 'js', 'hta') /* Block non allowed fileExts (List May Be Incomplete) */
);
// File Ext Check
$fExt = (strpos($lname, '.') !== false) ? strtolower(substr(strrchr($lname, '.'), 1)) : '';
$type = 'doc';
foreach ($fileExts as $t => $exts) {
	if (in_array($fExt, $exts)) {
		$type = $t;
		break;
	}
}

// Convert Upload Types
if ($type == 'audio' && $upload_audio_as_video) $type = 'video';
elseif ($type == 'video' && $upload_video_as_doc) $type = 'doc';

// Check Type Limits
switch ($type) {
	case 'block': html_error("This file ext. ($fExt) isn't allowed by VK.");
	case 'audio':
	case 'doc':
		if ($fsize > 200*1024*1024) html_error("You only can upload '$type' files up to 200 MB.");
}

// Check https support for login.
$usecurl = $options['use_curl'] && extension_loaded('curl') && function_exists('curl_init') && function_exists('curl_exec') ? true : false;
$chttps = false;
if ($usecurl) {
	$cV = curl_version();
	if (in_array('https', $cV['protocols'], true)) $chttps = true;
}
if (!extension_loaded('openssl') && !$chttps) html_error('You need to install/enable PHP\'s OpenSSL extension to support HTTPS connections.');
elseif (!$chttps) $usecurl = false;

$auth_url = 'https://oauth.vk.com/authorize?client_id='.urlencode($app['id']).'&display=popup&scope=audio,video,docs,offline&response_type=code&redirect_uri='.urlencode($return_url);

if (empty($_REQUEST['code'])) {
	echo "\n<script type='text/javascript'>document.location = '$auth_url';</script>\n<h1 style='text-align:center'>You are being redirected to VK's oauth dialog...</h1><div style='text-align:center'>It doesn't redirect?, Click <a href='$auth_url'>here</a>.</div>\n";
	exit("</body>\n</html>");
} else {
	$not_done = false;

	// Auth
	echo "<table style='width:600px;margin:auto;'>\n<tr><td align='center'>\n<div id='login' width='100%' align='center'>Authenticating</div>\n";

	$page = vk_GetPage('https://oauth.vk.com/access_token?client_id='.urlencode($app['id']).'&client_secret='.urlencode($app['secret']).'&code='.urlencode($_REQUEST['code']).'&redirect_uri='.urlencode($return_url));
	$json = Get_Reply($page);
	if (!empty($json['error']) && stripos($json['error'], 'REDIRECT_URI') === false) html_error("Auth Error: [{$json['error']}] ".(!empty($json['error_description'])?$json['error_description']:''));

	// Retrive upload ID
	echo "<script type='text/javascript'>document.getElementById('login').style.display='none';</script>\n<div id='info' width='100%' align='center'>Preparing Upload</div>\n";

	sleep(1); // They have a limit of 3 Request/Second... Let's wait a second :D

	$data = array();
	switch ($type) {
		case 'video':
			$data['method'] = 'video.save';
			$data['name'] = $lname;
			$data['description'] = 'Uploaded with Rapidleech';
			// $data['is_private'] = '1'; // Uncomment for upload videos as private. (Not Sure Yet How It Does Work)
			// $data['group_id'] = 1234567890; // ID of the community. (Positive Number)
			break;
		case 'audio':
			$data['method'] = 'audio.getUploadServer';
			break;
		case 'doc':
			$data['method'] = 'docs.getUploadServer';
			// $data['group_id'] = 1234567890; // ID of the community. (Positive Number)
			break;
	}

	$upsrv = vk_doApiReq($data);
	if (!empty($upsrv['error'])) {
		if ($upsrv['error'] == '204') html_error('Your account is banned for uploading.');
		html_error("{$data['method']} Error: [{$upsrv['error']['error_code']}] {$upsrv['error']['error_msg']}");
	}

	$post = array();
	$up_url = $upsrv['response']['upload_url'];

	// Uploading
	echo "<script type='text/javascript'>document.getElementById('info').style.display='none';</script>\n";

	$url = parse_url($up_url);
	$upfiles = upfile($url['host'], defport($url), $url['path'].($url['query'] ? '?'.$url['query'] : ''), '', '', $post, $lfile, $lname, ($type == 'video' ? 'video_file' : 'file'), '', $_GET['proxy'], $pauth, 0, $url['scheme']);

	// Upload Finished
	echo "<script type='text/javascript'>document.getElementById('progressblock').style.display='none';</script>\n";

	is_page($upfiles);
	$upres = Get_Reply($upfiles);
	if (!empty($upres['error'])) {
		if (is_array($upres['error'])) html_error("Upload $type Error: [{$upres['error']['error_code']}] " . $upres['error']['error_msg']);
		else if ($type == 'doc' && $upres['error'] == 'unknown error' && in_array($fExt, array('zip', 'zipx', 'rar', 'rar5', 'tar', 'gz', '7z', 'cab'))) html_error("Upload $type Error: Possible Forbidden File Type Inside Compressed File.");
		else html_error("Upload $type Error: " . $upres['error']);
	}

	if ($type != 'video') {
		sleep(1); // Let's wait another second :D

		$data = array();
		switch ($type) {
			case 'audio':
				$data['method'] = 'audio.save';
				$data['audio'] = $upres['audio'];
				$data['server'] = $upres['server'];
				$data['hash'] = $upres['hash'];
				break;
			case 'doc':
				$data['method'] = 'docs.save';
				$data['file'] = $upres['file'];
				$data['tags'] = 'Rapidleech';
				break;
		}
		$rply = vk_doApiReq($data);

		if (!empty($rply['error'])) {
			if (is_array($rply['error'])) html_error($data['method'] . " Error: [{$rply['error']['error_code']}] " . $rply['error']['error_msg']);
			else html_error($data['method'] . ' Error: ' . $rply['error']);
		}
	}

	if ($type == 'video') {
		if (!empty($upsrv['response']['vid'])) $download_link = 'https://vk.com/video' . $json['user_id'] . '_' . $upsrv['response']['vid'];
		else html_error('Your video will appear in your VK account after a while.');
	}
	elseif ($type == 'doc' && !empty($rply['response'][0]['did'])) $download_link = 'https://vk.com/doc' . $rply['response'][0]['owner_id'] . '_' . $rply['response'][0]['did'];
	else html_error('Check your VK account for see your new uploaded file.');
}

function vk_doApiReq($data) {
	global $app;
	$data['api_id'] = $app['id'];
	$data['format'] = 'json';
	$data['v'] = '3.0';
	$sig = '';
	ksort($data);
	foreach ($data as $k => $v) $sig .= "$k=$v";
	$sig = md5($sig.$app['secret']);
	$baseLink = 'https://api.vk.com/method/'.$data['method'].'?';
	unset($k, $v, $data['method']);
	return Get_Reply(vk_GetPage($baseLink . http_build_query($data) . "&sig=$sig&access_token=".$GLOBALS['json']['access_token']));
}

function Get_Reply($page) {
	if (!function_exists('json_decode')) html_error('Error: Please enable JSON in php.');
	$json = substr($page, strpos($page, "\r\n\r\n") + 4);
	$json = substr($json, strpos($json, '{'));$json = substr($json, 0, strrpos($json, '}') + 1);
	$rply = json_decode($json, true);
	if (!$rply || (is_array($rply) && count($rply) == 0)) html_error('Error getting json data.');
	return $rply;
}

function vk_GetPage($link, $cookie = 0, $post = 0, $referer = 0, $auth = 0, $XMLRequest = 0) {
	if (!$referer && !empty($GLOBALS['Referer'])) {
		$referer = $GLOBALS['Referer'];
	}

	if ($GLOBALS['usecurl']) {
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

//[30-8-2012] Written by Th3-822.
//[28-10-2012] Small fixes that i don't remember. - Th3-822
//[24-11-2012] Now it shows video url & Fixed auul for this plugin. - Th3-822
//[22-9-2013] Fixed mp3 upload & Other issues. - Th3-822
//[15-12-2014] Fixed uploading (Now it requires support for HTTPS uploads). - Th3-822
//[23-8-2015] Added Docs upload support. - Th3-822

?>