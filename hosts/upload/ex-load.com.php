<?php

if (!defined('RAPIDLEECH')) exit;
$_T8 = array('v' => 4); // Version of this config file. (Do Not Edit)

/* # Plugin's Settings # */
$_T8['domain'] = 'ex-load.com'; // May require the www. (Check first if the site adds the www.).
$_T8['anonUploadDisable'] = false; // Disallow non-registered users upload. (XFS Pro)
$_T8['anonUploadLimit'] = 100; // File-size limit for non-registered users (MB) | 0 = Plugin's limit | (XFS Pro)

// Advanced Settings (Don't edit it unless you know what are you doing)
	$_T8['port'] = 80; // Server's port, default: 80 | 443 = https.
	$_T8['xfsFree'] = false; // Change to true if the host is using XFS free.
	$_T8['path'] = '/'; // URL path to XFS script, default: '/'
	$_T8['sslLogin'] = false; // Force https on login.
	$_T8['opUploadName'] = 'page'; // Custom ?op=value for checking upload page, default: 'upload'
	$_T8['flashUpload'] = false; // Forces the use of flash upload method... Also filename for .cgi if it's a non empty string. (XFS Pro)
	$_T8['fw_sendLogin'] = 'SendLogin'; // Callable function

$acc_key_name = str_ireplace(array('www.', '.'), array('', '_'), $_T8['domain']); // (Do Not Edit)

/* # Account Info # */
$upload_acc[$acc_key_name]['user'] = ''; //Set your login
$upload_acc[$acc_key_name]['pass'] = ''; //Set your password

function SendLogin($post) {
	global $_T8, $referer, $cookie, $pauth;
	$page = geturl($_T8['domain'], $_T8['port'], $_T8['path'].'?op=login', $referer, $cookie, 0, 0, $_GET['proxy'], $pauth);is_page($page);

	if (!($form = cut_str($page, '<form', '</form>'))) html_error('Cannot find login form.');
	if (!($post['rand'] = cut_str($page, 'name="rand" value="', '"'))) html_error('Login form "rand" not found.');

	if (substr_count($form, "<span style='position:absolute;padding-left:") > 3 && preg_match_all("@<span style='[^\'>]*padding-left\s*:\s*(\d+)[^\'>]*'[^>]*>((?:&#\w+;)|(?:\d))</span>@i", $form, $txtCaptcha)) {
		// Text Captcha (decodeable)
		$txtCaptcha = array_combine($txtCaptcha[1], $txtCaptcha[2]);
		ksort($txtCaptcha, SORT_NUMERIC);
		$txtCaptcha = trim(html_entity_decode(implode($txtCaptcha), ENT_QUOTES, 'UTF-8'));
		$post['code'] = $txtCaptcha;
	} else html_error('Login captcha not found.');

	// Don't remove this sleep or you will only see "Error Decoding Captcha. [Login]"
	sleep(3); // 2 or 3 seconds.

	$page = geturl($_T8['domain'], $_T8['port'], $_T8['path'].'?op=login', $referer, $cookie, $post, 0, $_GET['proxy'], $pauth);is_page($page);
	is_present($page, '>Wrong captcha code<', 'Error: Error Decoding Captcha. [Login]');
	return $page;
}

if (!file_exists(HOST_DIR . 'upload/GenericXFSHost.inc.php')) html_error('Cannot load "'.htmlentities(HOST_DIR).'upload/GenericXFSHost.inc.php" (File doesn\'t exists), please install lastest version from: http://rapidleech.com/forum/viewtopic.php?f=17&t=80 or http://pastebin.com/E0z7qMU1 ');
require(HOST_DIR . 'upload/GenericXFSHost.inc.php');

// Written by Th3-822 - Last Update: [15-5-2015]

?>