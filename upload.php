<?php

require_once('rl_init.php');
ignore_user_abort(true);

login_check();

include(CLASS_DIR . 'http.php');
if (!defined('CRLF')) define('CRLF', "\r\n");

if (!empty($_REQUEST['filename'])) $_REQUEST['filename'] = trim($_REQUEST['filename']);
if (!empty($_REQUEST['uploaded'])) $_REQUEST['uploaded'] = $uphost = basename(trim($_REQUEST['uploaded']));
if (empty($_REQUEST['uploaded']) || empty($_REQUEST['filename'])) html_error(lang(46));

$lname = basename(base64_decode($_REQUEST['filename']));
$lfile = DOWNLOAD_DIR . $lname;

$page_title = sprintf(lang(63), htmlspecialchars($lname), htmlspecialchars($uphost));
require_once(TEMPLATE_DIR . '/header.php');

if (!file_exists($lfile)) html_error(sprintf(lang(64), htmlspecialchars($lname)));
if (!is_readable($lfile)) html_error(sprintf(lang(65), htmlspecialchars($lname)));

$fsize = filesize($lfile);
// We want to check if the selected upload service is a valid ones
if (isset($_REQUEST['useuproxy']) && (empty($_REQUEST['uproxy']) || !strstr($_REQUEST['uproxy'], ':'))) {
	html_error(lang(324));
} else {
	$_GET['proxy'] = $_REQUEST['uproxy'];
}

$proxy = $_GET['proxy'] = isset($_GET['proxy']) ? $_GET['proxy'] : '';

if (!empty($_GET['proxy']) && strpos($_GET['proxy'], ':') < 1) {
	html_error(lang(324));
}

if (!empty($_REQUEST['upauth'])) {
	$pauth = $_REQUEST['upauth'];
} else {
	$pauth = (!empty($_REQUEST['uproxyuser']) && !empty($_REQUEST['uproxypass'])) ? base64_encode($_REQUEST['uproxyuser'] . ':' . $_REQUEST['uproxypass']) : '';
}

// We want to check if the selected upload service is a valid ones
$upload_services = $max_file_size = $page_upload = array();
if (file_exists(HOST_DIR . "upload/$uphost.index.php") && file_exists(HOST_DIR . "upload/$uphost.php")) {
	require_once(HOST_DIR . "upload/$uphost.index.php");
	if (!in_array("$uphost", $upload_services, true)) html_error(lang(48));
	if (!empty($max_file_size["$uphost"]) && $fsize > ($max_file_size["$uphost"] * 1048576)) html_error(lang(66));
	require_once(HOST_DIR . "upload/$uphost.php");
} else html_error(lang(67));

echo '<script type="text/javascript">var orlink="' . htmlspecialchars($lname, ENT_QUOTES) . ' to ' . htmlspecialchars($uphost, ENT_QUOTES) . '";</script>';

if (!empty($download_link) || !empty($delete_link) || !empty($stat_link) || !empty($adm_link)) {
	echo "\n<table width=100% border=0>";
	echo (!empty($download_link) ? '<tr><td width="100" nowrap="nowrap" align="right"><b>' . lang(68) . ':</b><td width="80%"><input value="' . htmlspecialchars($download_link, ENT_QUOTES) . '" class="upstyles-dllink" readonly="readonly" /></tr>' : '');
	echo (!empty($delete_link) ? '<tr><td width="100" nowrap="nowrap" align="right">' . lang(69) . ':<td width="80%"><input value="' . htmlspecialchars($delete_link, ENT_QUOTES) . '" class="upstyles-dellink" readonly="readonly" /></tr>' : '');
	echo (!empty($stat_link) ? '<tr><td width="100" nowrap="nowrap" align="right">' . lang(70) . ':<td width="80%"><input value="' . htmlspecialchars($stat_link, ENT_QUOTES) . '" class="upstyles-statlink" readonly="readonly" /></tr>' : '');
	echo (!empty($adm_link) ? '<tr><td width="100" nowrap="nowrap" align="right">' . lang(71) . ':<td width="80%"><input value="' . htmlspecialchars($adm_link, ENT_QUOTES) . '" class="upstyles-admlink" readonly="readonly" /></tr>': '');
	echo (!empty($user_id) ? '<tr><td width="100" nowrap="nowrap" align="right">' . lang(72) . ':<td width="80%"><input value="' . htmlspecialchars($user_id, ENT_QUOTES) . '" class="upstyles-userid" readonly="readonly" /></tr>': '');
	echo (!empty($ftp_uplink) ? '<tr><td width="100" nowrap="nowrap" align="right">' . lang(73) . ':<td width="80%"><input value="' . htmlspecialchars($ftp_uplink, ENT_QUOTES) . '" class="upstyles-ftpuplink" readonly="readonly" /></tr>': '');
	echo (!empty($access_pass) ? '<tr><td width="100" nowrap="nowrap" align="right">' . lang(74) . ':<td width="80%"><input value="' . htmlspecialchars($access_pass, ENT_QUOTES) . '" class="upstyles-accesspass" readonly="readonly" /></tr>': '');
	echo "</table>\n";

	if (!$options['upload_html_disable'] && !isset($_GET['auul']) && !file_exists("$lfile.upload.html")) {
		$upload_html = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /><title>' . lang(75) . '</title><style type="text/css">.bluefont,body{font-family:tahoma,arial,"times New Roman",georgia,verdana,sans-serif;font-size:11px}.linktitle,hr{border-style:solid}.bluefont,.host .links a,body{font-size:11px}body{color:#333;background-color:#EFF0F4;margin:0;padding:0}.linktitle{width:576px;background-color:#C291F9;text-align:center;padding:3px;margin:25px auto 0;border-width:1px 1px 0;border-color:#C7C4FB}.host .links,.host .title{text-align:left;padding:3px 0 3px 10px}.bluefont{color:#0E078F}hr{height:1px;background-color:#046FC6;color:#046FC6;width:90%;border-width:0}.host .links{width:95%;margin:0 auto;border:1px dashed #666;background-color:#F2F1FE}.host{width:600px;margin:10px auto}.host .links a{text-decoration:none;color:#666}.host .links a:hover{text-decoration:none;color:#E8740B}.host .title{width:95%;margin:0 auto;background-color:#C7C4FB;color:#000;font-size:12px;font-family:Georgia,"Times New Roman",Times,serif;border-width:1px 1px 0;border-style:dashed;border-color:#333}</style></head><body>' . sprintf(lang(76), htmlspecialchars($lname), bytesToKbOrMbOrGb($fsize)) . '<div class="host"><div class="title"><strong>' . htmlspecialchars($uphost) . '</strong> - <span class="bluefont">' . date("Y-m-d H:i:s") . '</span></div><div class="links">' . 
		(!empty($download_link) ? '<strong>'.lang(68).': <a href="'.htmlspecialchars($download_link).'" target="_blank">'.htmlspecialchars($download_link).' </a></strong>' : '').
		(!empty($delete_link) ? '<br />'.lang(69).': <a href="'.htmlspecialchars($delete_link).'" target="_blank">'.htmlspecialchars($delete_link).' </a>' : '').
		(!empty($stat_link) ? '<br />'.lang(70).': <a href="'.htmlspecialchars($stat_link).'" target="_blank">'.htmlspecialchars($stat_link).' </a>' : '').
		(!empty($adm_link) ? '<br />'.lang(71).': <a href="'.htmlspecialchars($adm_link).'" target="_blank">'.htmlspecialchars($adm_link).' </a>' : '').
		(!empty($user_id) ? '<br />'.lang(72).': <a href="'.htmlspecialchars($user_id).'" target="_blank">'.htmlspecialchars($user_id).' </a>' : '').
		(!empty($access_pass) ? '<br />'.lang(74).': <a href="'.htmlspecialchars($access_pass).'" target="_blank">'.htmlspecialchars($access_pass).' </a>' : '').
		(!empty($ftp_uplink) ? '<br />'.lang(73).': <a href="'.htmlspecialchars($ftp_uplink).'" target="_blank">'.htmlspecialchars($ftp_uplink).' </a>' : '').
		'</div></div></body></html>';
		file_put_contents("$lfile.upload.html", $upload_html);
	}
}


if (empty($not_done)) {
	echo '<p><center><b><a href="javascript:window.close();">' . lang(77) . '</a></b></center>';

if (isset($_GET['auul'])) {
		printf('<script type="text/javascript">parent.nextlink%d();</script>', $_GET['auul']);
	// Write links to a file
	if (!$options['myuploads_disable']) {
		if (empty($_GET['save_style']) || $_GET['save_style'] == lang(51)) $_GET['save_style'] = base64_encode('{name}\n' . str_repeat('=', 80) . '\n{link}\n');

		$save_style = str_ireplace(array('{link}', '{name}', '\n', '{size}', '{sizeb}'), array($download_link, $lname, "\r\n", bytesToKbOrMbOrGb($fsize), $fsize), base64_decode($_GET['save_style']));
			file_put_contents(DOWNLOAD_DIR . 'myuploads.txt', "$save_style\r\n", FILE_APPEND | LOCK_EX); // Obviously it was a mistake not making it a variable earlier
		}
	}
}

include(TEMPLATE_DIR . '/footer.php');
