<?php

if (!defined('RAPIDLEECH')) exit;
$_T8 = array('v' => 9); // Version of this config file. (Do Not Edit)

/* # Plugin's Settings # */
$_T8['domain'] = 'upvast.com'; // May require the www. (Check first if the site adds the www.).
$_T8['anonUploadDisable'] = false; // Disallow non-registered users upload. (XFS Pro)
$_T8['anonUploadLimit'] = 200; // File-size limit for non-registered users (MB) | 0 = Plugin's limit | (XFS Pro)

// Advanced Settings (Don't edit it unless you know what are you doing)
	$_T8['port'] = 80; // Server's port, default: 80 | 443 = https.
	$_T8['xfsFree'] = false; // Change to true if the host is using XFS free.
	$_T8['path'] = '/'; // URL path to XFS script, default: '/'
	$_T8['sslLogin'] = false; // Force https on login.
	$_T8['opUploadName'] = 'upload'; // Custom ?op=value for checking upload page, default: 'upload'
	$_T8['flashUpload'] = false; // Forces the use of flash upload method... Also filename for .cgi if it's a non empty string. (XFS Pro)

$acc_key_name = str_ireplace(array('www.', '.'), array('', '_'), $_T8['domain']); // (Do Not Edit)

/* # Account Info # */
$upload_acc[$acc_key_name]['user'] = ''; //Set your login
$upload_acc[$acc_key_name]['pass'] = ''; //Set your password

if (!file_exists(HOST_DIR . 'upload/GenericXFSHost.inc.php')) html_error('Cannot load "'.htmlentities(HOST_DIR).'upload/GenericXFSHost.inc.php" (File doesn\'t exists), please install lastest version from: http://rapidleech.com/forum/viewtopic.php?f=17&t=80 or http://pastebin.com/E0z7qMU1 ');
require(HOST_DIR . 'upload/GenericXFSHost.inc.php');

// Written by Th3-822 - Last Update: [29-10-2015]

?>