<?php

// Create a Application at http://vk.com/editapp?act=create
// Select "Web Site" and add your rapidleech url and the base domain.
// After creating the app, copy the Application ID and the Secret key. and add them at $app.

######## Plugin's Info ########
$app = array();
$app['id'] = ''; //Application ID
$app['secret'] = ''; //Application SecretKey
########################
if (empty($app['id']) || empty($app['secret'])) html_error('Application ID or SecretKey Empty. Please create a VK app and add them at '.HOST_DIR.'upload/'.basename(__FILE__));

$not_done = true;
$_GET['proxy'] = isset($proxy) ? $proxy : (isset($_GET['proxy']) ? $_GET['proxy'] : '');
$PHP_SELF = $_SERVER ['SCRIPT_NAME'];
if (!defined('ROOT_DIR')) define('ROOT_DIR', realpath('./'));
$return_url = link_for_file(realpath(basename($PHP_SELF)), true).'?uploaded='.urlencode($_REQUEST['uploaded']).'&filename='.urlencode(base64_encode($lname));
if (!empty($_GET['proxy'])) $return_url .= '&useuproxy=on&uproxy='.urlencode($_GET['proxy']);
if (!empty($_REQUEST['pauth'])) $return_url .= '&upauth='.urlencode($pauth);
if (!empty($_GET['save_style'])) $return_url .= '&save_style='.urlencode($_GET['save_style']);
if (isset($_GET['auul'])) $return_url .= '&auul='.urlencode($_GET['auul']);

$videxts = array('.avi', '.mp4', '.3gp', '.mpg', '.mpeg', '.mov', '.flv', '.wmv');
$video = true;
$upload_mp3_as_video = true; // Upload Mp3 files to "My Videos"... I don't know why "saveAudio" always gives error 121... So you should let this in true. (But, if you change it to false and it works, send me a msg.)
if (strtolower(strrchr($lname, '.')) == '.mp3') {
	if ($fsize > 20*1024*1024) html_error('You only can upload mp3 files up to 20 MB.');
	$video = $upload_mp3_as_video;
} elseif (!in_array(strtolower(strrchr($lname, '.')), $videxts)) html_error('This file ext isn\'t allowed by VK.');

// Check https support for login.
$usecurl = $cantuse = false;
if (!extension_loaded('openssl')) {
	if (extension_loaded('curl')) {
		$cV = curl_version();
		if (in_array('https', $cV['protocols'], true)) $usecurl = true;
		else $cantuse = true;
	} else $cantuse = true;
	if ($cantuse) html_error('Need OpenSSL enabled or cURL (with SSL) to use this plugin.');
}

$auth_url = 'https://oauth.vk.com/authorize?client_id='.urlencode($app['id']).'&display=popup&scope=audio,video,offline&response_type=code&redirect_uri='.rawurlencode($return_url);

if (empty($_REQUEST['code'])) {
	echo "\n<script type='text/javascript'>document.location = '$auth_url';</script>\n<h1 style='text-align:center'>You are being redirected to VK's auth dialog...</h1><div style='text-align:center'>It doesn't redirect?, Click <a href='$auth_url'>here</a>.</div>\n";
	exit("</body>\n</html>");
} else {
	$not_done = false;

	// Auth
	echo "<table style='width:600px;margin:auto;'>\n<tr><td align='center'>\n<div id='login' width='100%' align='center'>Authenticating</div>\n";

	if ($usecurl) $page = cURL ('https://oauth.vk.com/access_token?client_id='.urlencode($app['id']).'&client_secret='.urlencode($app['secret']).'&code='.urlencode($_REQUEST['code']).'&redirect_uri='.rawurlencode($return_url));
	else {
		$page = geturl('oauth.vk.com', 0, '/access_token?client_id='.urlencode($app['id']).'&client_secret='.urlencode($app['secret']).'&code='.urlencode($_REQUEST['code']).'&redirect_uri='.urlencode($return_url), 0, 0, 0, 0, $_GET['proxy'], $pauth, 0, 'https'); // Port is overridden to 443
		is_page($page);
	}
	$json = Get_Reply($page);
	if (!empty($json['error']) && stripos($json['error'], 'REDIRECT_URI') === false) html_error("Auth Error: [{$json['error']}] ".(!empty($json['error_description'])?$json['error_description']:''));

	// Retrive upload ID
	echo "<script type='text/javascript'>document.getElementById('login').style.display='none';</script>\n<div id='info' width='100%' align='center'>Preparing Upload</div>\n";

	sleep(1); // They have a limit of 3 Request/Second... Let's wait a second :D

	if ($video) $data = array('api_id'=>$app['id'], 'method'=>'video.save', 'name'=>urlencode($lname), 'description'=>'Uploaded+With+Rapidleech');
	else $data = array('api_id'=>$app['id'], 'method'=>'audio.getUploadServer');
	$req = SigAndReq($data, '/method/'.$data['method'].'?');

	if ($usecurl) $page = cURL ('https://api.vk.com'.$req);
	else {
		$page = geturl('api.vk.com', 0, $req, 0, 0, 0, 0, $_GET['proxy'], $pauth, 0, 'https'); // Port is overridden to 443
		is_page($page);
	}

	$upsrv = Get_Reply($page);
	if (!empty($upsrv['error'])) {
		if ($upsrv['error'] == '204') html_error('Your account is banned for uploading.');
		html_error("Error: [{$upsrv['error']['error_code']}] {$upsrv['error']['error_msg']}");
	}

	$post = array();
	// if ($video) $post['is_private'] = '1'; // Uncomment for upload videos as private
	$up_url = $upsrv['response']['upload_url'];// .'&api_id='.$app['id'].'&access_token='.$json['access_token'];

	// Uploading
	echo "<script type='text/javascript'>document.getElementById('info').style.display='none';</script>\n";

	$url = parse_url($up_url);
	$upfiles = upfile($url['host'], 80, $url['path'].($url['query'] ? '?'.$url['query'] : ''), '', '', $post, $lfile, $lname, ($video ? 'video_file' : 'file'), '', $_GET['proxy'], $pauth);

	// Upload Finished
	echo "<script type='text/javascript'>document.getElementById('progressblock').style.display='none';</script>\n";

	is_page($upfiles);
	$upres = Get_Reply($upfiles);
	if (!empty($upres['error'])) html_error("Upload Error: [{$upres['error']['error_code']}] {$upres['error']['error_msg']}");

	if (!$video) {
		sleep(1); // Let's wait another second :D
		$data = array('api_id'=>$app['id'], 'method'=>'audio.save', 'audio'=>$upres['audio'], 'hash'=>$upres['hash'], 'server'=>$upres['server']);
		$req = SigAndReq($data, '/method/'.$data['method'].'?');
		if ($usecurl) $page = cURL ('https://api.vk.com'.$req);
		else {
			$page = geturl('api.vk.com', 0, $req, 0, 0, array('hash'=>$upres['hash']), 0, $_GET['proxy'], $pauth, 0, 'https'); // Port is overridden to 443
			is_page($page);
		}
		$rply = Get_Reply($page);
	}

	if (!empty($rply['error'])) {
		if (is_array($rply['error'])) html_error("audio.save Error: [{$rply['error']['error_code']}] {$rply['error']['error_msg']}");
		else html_error('Save Error: '. $rply['error']);
	}

	if ($video && !empty($upsrv['response']['vid'])) $download_link = 'http://vk.com/video' . $json['user_id'] . '_' . $upsrv['response']['vid'];
	elseif ($video) html_error('Your video will appear in your VK account after a while.', 0);
	elseif (!$video && !empty($rply['response']['url'])) $download_link = $rply['response']['url'];
	else html_error('Check your VK account for see the new uploaded file.', 0);
}

function SigAndReq($data, $req) {
	$data['format'] = 'json';
	$data['v'] = '3.0';
	$sig = '';
	ksort($data);
	foreach ($data as $k => $v) {
		$sig .= "$k=$v";
		if ($k != 'method') $req .= "$k=$v&";
	}
	$sig = md5($sig.$GLOBALS['app']['secret']);
	$req .= "sig=$sig&access_token=".$GLOBALS['json']['access_token'];
	return $req;
}

function Get_Reply($page) {
	if (!function_exists('json_decode')) html_error('Error: Please enable JSON in php.');
	$json = substr($page, strpos($page, "\r\n\r\n") + 4);
	$json = substr($json, strpos($json, '{'));$json = substr($json, 0, strrpos($json, '}') + 1);
	$rply = json_decode($json, true);
	if (!$rply || (is_array($rply) && count($rply) == 0)) html_error('Error getting json data.');
	return $rply;
}

//[30-8-2012] Written by Th3-822.
//[28-10-2012] Small fixes that i don't remember. - Th3-822
//[24-11-2012] Now it shows video url & Fixed auul for this plugin. - Th3-822

?>