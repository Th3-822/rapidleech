<?php

if (!defined('RAPIDLEECH')) exit;
$_T8 = array('v' => 9); // Version of this config file. (Do Not Edit)

/* # Plugin's Settings # */
$_T8['domain'] = 'uptobox.com'; // May require the www. (Check first if the site adds the www.).
$_T8['anonUploadDisable'] = false; // Disallow non-registered users upload. (XFS Pro)
$_T8['anonUploadLimit'] = 1024; // File-size limit for non-registered users (MB) | 0 = Plugin's limit | (XFS Pro)

// Advanced Settings (Don't edit it unless you know what are you doing)
	$_T8['port'] = 80; // Server's port, default: 80 | 443 = https.
	$_T8['xfsFree'] = false; // Change to true if the host is using XFS free.
	$_T8['path'] = '/'; // URL path to XFS script, default: '/'
	$_T8['sslLogin'] = false; // Force https on login.
	$_T8['opUploadName'] = 'upload'; // Custom ?op=value for checking upload page, default: 'upload'
	$_T8['flashUpload'] = false; // Forces the use of flash upload method... Also filename for .cgi if it's a non empty string. (XFS Pro)
	$_T8['fw_sendLogin'] = 'SendLogin'; // Callable function

$acc_key_name = str_ireplace(array('www.', '.'), array('', '_'), $_T8['domain']); // (Do Not Edit)

/* # Account Info # */
$upload_acc[$acc_key_name]['user'] = ''; //Set your login
$upload_acc[$acc_key_name]['pass'] = ''; //Set your password

function SendLogin($post) {
	global $_T8, $cookie, $pauth;
	$page = geturl('login.uptobox.com', 443, '/logarithme', 'https://login.uptobox.com/', $cookie, $post, 0, 0, 0, 0, 'https'); // geturl doesn't support https proxy
	is_page($page);
	is_present($page, 'You are trying to log in from a different country', 'Login Failed: Login Blocked By IP, Check Account Email And Follow The Steps To Add IP to Whitelist.');
	return $page;
}

if (!file_exists(HOST_DIR . 'upload/GenericXFSHost.inc.php')) html_error('Cannot load "'.htmlentities(HOST_DIR).'upload/GenericXFSHost.inc.php" (File doesn\'t exists), please install lastest version from: http://rapidleech.com/forum/viewtopic.php?f=17&t=80 or http://pastebin.com/E0z7qMU1 ');
require(HOST_DIR . 'upload/GenericXFSHost.inc.php');

// Written by Th3-822

?>