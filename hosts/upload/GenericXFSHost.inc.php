<?php
/*
@h@	/hosts/upload/GenericXFSHost.inc.php
*/
if (!defined('RAPIDLEECH')) exit;

// Check include
if (!isset($_T8) || !is_array($_T8) || empty($_T8['domain']) || $_T8['domain'] == 'domain.tld' || empty($_T8['v'])) {
	if (strtolower(basename(__FILE__)) == strtolower($page_upload[$_REQUEST['uploaded']])) html_error('This plugin can\'t be called directly.');
	html_error('Error: Called from non configured plugin "' . htmlentities($page_upload[$_REQUEST['uploaded']]) . '".');
}
if ($_T8['v'] > 2) html_error('Error: '.basename(__FILE__).' is outdated, please install last version from: http://www.rapidleech.com/index.php/topic/14014-upload-plugin-for-sites-with-xfs-pro/ or http://pastebin.com/E0z7qMU1 ');

/* # Default Settings # */
$default = array();
$default['path'] = '/'; // URL path to XFS script, default: '/'
$default['xfsFree'] = false; // Change to true if the host is using XFS free.
$default['opUploadName'] = 'upload'; // Custom ?op=value for checking upload page, default: 'upload'
$default['anonUploadDisable'] = false; // Disallow non registered users upload. (XFS Pro)
$default['anonUploadLimit'] = 0; // File-size limit for non registered users (MB) - 0 = Plugin's limit | (XFS Pro)
$default['flashUpload'] = false; // Forces the use of flash upload method... Also filename for .cgi if it's a non empty string. (XFS Pro)

$_T8 = array_merge($default, array_filter($_T8)); // Merge default settings with loader's settings

$_GET['proxy'] = isset($_GET['proxy']) ? $_GET['proxy'] : '';
$not_done = true;

if (!$_T8['xfsFree'] && !empty($upload_acc[$acc_key_name]['user']) && !empty($upload_acc[$acc_key_name]['pass'])) {
	$_REQUEST['up_login'] = $upload_acc[$acc_key_name]['user'];
	$_REQUEST['up_pass'] = $upload_acc[$acc_key_name]['pass'];
	$_REQUEST['action'] = 'FORM';
	echo "<b><center>Using Default Login.</center></b>\n";
}

if (!$_T8['xfsFree'] && (empty($_REQUEST['action']) || $_REQUEST['action'] != 'FORM')) {
	echo "<table border='0' style='width:270px;' cellspacing='0' align='center'>\n<form method='POST'>\n\t<input type='hidden' name='action' value='FORM' />\n\t<tr><td style='white-space:nowrap;'>&nbsp;Username*</td><td>&nbsp;<input type='text' name='up_login' value='' style='width:160px;' /></td></tr>\n\t<tr><td style='white-space:nowrap;'>&nbsp;Password*</td><td>&nbsp;<input type='password' name='up_pass' value='' style='width:160px;' /></td></tr>\n";
	echo "\t<tr><td colspan='2' align='center'><br /><input type='submit' value='Upload' /></td></tr>\n";
	echo "\t<tr><td colspan='2' align='center'><small>*You can set it as default in <b>".$page_upload[$_REQUEST['uploaded']]."</b></small></td></tr>\n";
	echo "</form>\n</table>\n";
} else {
	$not_done = false;
	if (substr($_T8['path'], 0, 1) != '/') $_T8['path'] = '/'.$_T8['path'];
	if (substr($_T8['path'], -1) != '/') $_T8['path'] .= '/';
	$referer = 'http://'.$_T8['domain'].$_T8['path'];

	// Login
	echo "<table style='width:600px;margin:auto;'>\n<tr><td align='center'>\n<div id='login' width='100%' align='center'>Login to ".str_ireplace('www.', '', $_T8['domain'])."</div>\n";

	$cookie = (!empty($cookie)) ? (is_array($cookie) ? $cookie : StrToCookies($cookie)) : array();
	$cookie['lang'] = 'english';
	if ($_T8['xfsFree']) $login = false;
	elseif (!empty($_REQUEST['up_login']) && !empty($_REQUEST['up_pass'])) {
		$post = array();
		$post['op'] = 'login';
		$post['redirect'] = '';
		$post['login'] = $_REQUEST['up_login'];
		$post['password'] = $_REQUEST['up_pass'];

		$page = geturl($_T8['domain'], 80, $_T8['path'].'?op=login', $referer, $cookie, array_map('urlencode', $post), 0, $_GET['proxy'], $pauth);is_page($page);
		$header = substr($page, 0, strpos($page, "\r\n\r\n"));
		if (stripos($header, "\nLocation: ") !== false && preg_match('@\nLocation: (https?://[^\r\n]+)@i', $header, $redir) && 'www.' . strtolower($_T8['domain']) == strtolower(parse_url($redir[1], PHP_URL_HOST))) html_error("Please set \$_T8['domain'] to 'www.{$_T8['domain']}'.");
		if (preg_match('@Incorrect ((Username)|(Login)) or Password@i', $page)) html_error('Login failed: User/Password incorrect.');
		is_present($page, 'op=resend_activation', 'Login failed: Your account isn\'t confirmed yet.');
		$cookie = GetCookiesArr($header, $cookie);
		if (empty($cookie['xfss']) && empty($cookie['login'])) html_error('Error: Login cookies not found.');
		$cookie['lang'] = 'english';
		$login = true;
	} else {
		if ($_T8['anonUploadDisable']) html_error('Login failed: User/Password empty.');
		echo "<b><center>Login not found or empty, using non member upload.</center></b>\n";
		if ($_T8['anonUploadLimit'] > 0 && $fsize > $_T8['anonUploadLimit']*1024*1024) html_error('File is too big for anon upload');
		$login = false;
	}

	// Retrive upload ID
	echo "<script type='text/javascript'>document.getElementById('login').style.display='none';</script>\n<div id='info' width='100%' align='center'>Retrive upload ID</div>\n";

	$page = geturl($_T8['domain'], 80, $_T8['path'].'?op='.(empty($_T8['opUploadName']) ? 'upload' : urlencode($_T8['opUploadName'])), $referer, $cookie, 0, 0, $_GET['proxy'], $pauth);is_page($page);
	if (substr($page, 9, 3) != '200') {
		$page = geturl($_T8['domain'], 80, $_T8['path'], $referer, $cookie, 0, 0, $_GET['proxy'], $pauth);is_page($page);
	}
	$header = substr($page, 0, strpos($page, "\r\n\r\n"));
	if (!$login && stripos($header, "\nLocation: ") !== false && preg_match('@\nLocation: (https?://[^\r\n]+)@i', $header, $redir) && 'www.' . strtolower($_T8['domain']) == strtolower(parse_url($redir[1], PHP_URL_HOST))) html_error("Please set \$_T8['domain'] to 'www.{$_T8['domain']}'.");

	if (preg_match('@var[\s\t]+max_upload_filesize[\s\t]*=[\s\t]*[\'\"]?(\d+)[\'\"]?[\s\t]*;@i', $page, $fzlimit) && $fzlimit[1] > 0 && $fsize > $fzlimit[1]*1024*1024) html_error('Error: '.lang(66)); // Max upload filesize test

	if (!preg_match('@action=["\']((https?://[^/"\']+)?/(?:[^\?"\'/]+/)*[\w\-]+\.cgi)\?upload_id=@i', $page, $up) && (empty($_T8['flashUpload']) || !preg_match('@[\'"]?uploader[\'"]?\s*:\s*[\'"]((https?://[^/"\']+)?/(?:[^\?"\'/]+/)*'.preg_quote((is_string($_T8['flashUpload']) ? $_T8['flashUpload'] :'up_flash.cgi'), '@').')[\'"]@i', $page, $up))) {
		is_present($page, 'We\'re sorry, there are no servers available for upload at the moment.', 'Site isn\'t accepting uploads.');
		if (!$login) {
			if (stripos($header, "\nLocation: ") !== false) is_present(cut_str($header, "\nLocation: ", "\n"), '?op=login', 'Please set '.($_T8['xfsFree'] ? '$_T8[\'xfsFree\'] to false and ' : '').'$_T8[\'anonUploadDisable\'] to true.');
			is_present($page, '>Register on site to be able to upload files<', 'Please set '.($_T8['xfsFree'] ? '$_T8[\'xfsFree\'] to false and ' : '').'$_T8[\'anonUploadDisable\'] to true.');
		}
		html_error('Error: Cannot find upload server.', 0);
	}
	$up_url = (empty($up[2])) ? 'http://'.$_T8['domain'].$up[1] : $up[1];

	// File-ext checks
	if (preg_match('@var[\s\t]+ext_allowed[\s\t]*=[\s\t]*[\'\"]\|?(\w+(?:\|\w+)*)\|?[\'\"][\s\t]*;@i', $page, $allowedExts) || preg_match('@var[\s\t]+ext_not_allowed[\s\t]*=[\s\t]*[\'\"]\|?(\w+(?:\|\w+)*)\|?[\'\"][\s\t]*;@i', $page, $notAllowedExts)) {
		$fExt = (strpos($lname, '.') !== false) ? strtolower(substr(strrchr($lname, '.'), 1)) : '';
		if (!empty($allowedExts[1])) {
			$allowedExts = array_map('strtolower', array_filter(explode('|', $allowedExts[1])));
			if (!in_array($fExt, $allowedExts)) html_error('Server doesn\'t allow upload of files with this ext: "'.htmlentities($fExt).'".');
		}
		if (!empty($notAllowedExts[1])) {
			$notAllowedExts = array_map('strtolower', array_filter(explode('|', $notAllowedExts[1])));
			if (in_array($fExt, $notAllowedExts)) html_error('Server doesn\'t allow upload of files with this ext: "'.htmlentities($fExt).'".');
		}
	}

	$uid = '';for ($i = 0; $i < 12; $i++) $uid .= mt_rand(0,9);

	$post = array();
	if (empty($_T8['flashUpload'])) {
		$post['upload_type'] = 'file';
		$post['sess_id'] = cut_str($page, 'name="sess_id" value="', '"');
		$post['srv_tmp_url'] = cut_str($page, 'name="srv_tmp_url" value="', '"');
		if (stripos($page, 'name="srv_id" value="') !== false && ($tmp = cut_str($page, 'name="srv_id" value="', '"'))) $post['srv_id'] = $tmp;
		if (stripos($page, 'name="disk_id" value="') !== false && ($tmp = cut_str($page, 'name="disk_id" value="', '"'))) $post['disk_id'] = $tmp;
		$post['link_rcpt'] = '';
		$post['link_pass'] = '';
		$post['file_descr'] = 'Uploaded with Rapidleech.';
		$post['file_public'] = '1';
		$post['tos'] = '1';
		$post['submit_btn'] = ' Upload! ';

		$up_url .= "?upload_id=$uid&js_on=1";
		if (!$_T8['xfsFree']) $up_url .= '&utype='.cut_str($page, "var utype='", "'").'&upload_type=file'.(!empty($post['disk_id']) ? '&disk_id=' . urlencode($post['disk_id']) : '');
	} else {
		$post['Filename'] = $lname;
		if ($login) if (!($post['sess_id'] = cut_str($page, 'name="sess_id" value="', '"'))) {
			if (!empty($cookie['xfss'])) $post['sess_id'] = $cookie['xfss'];
			elseif (preg_match('@["\']sess_id["\']\s*:\s*["\'](\w+)["\']@i', $page, $sid)) $post['sess_id'] = $sid[1];
			else html_error('Flash upload session key not found.');
		}
		$post['Upload'] = 'Submit Query';
	}

	// Uploading
	echo "<script type='text/javascript'>document.getElementById('info').style.display='none';</script>\n";

	$url = parse_url($up_url);
	if (!empty($_T8['flashUpload'])) $url['path'] = substr($url['path'], 0, strrpos($url['path'], '/') + 1).(is_string($_T8['flashUpload']) ? $_T8['flashUpload'] : 'up_flash.cgi');
	$upfiles = upfile($url['host'], defport($url), $url['path'].(!empty($url['query']) ? '?'.$url['query'] : ''), 0, $cookie, $post, $lfile, $lname, 
	(empty($_T8['flashUpload']) ? 'file' : 'Filedata'), '', $_GET['proxy'], $pauth);

	// Upload Finished
	echo "<script type='text/javascript'>document.getElementById('progressblock').style.display='none';</script>\n";

	is_page($upfiles);

	if (!$login && stripos($page, 'Uploads not enabled for this type of users') !== false) html_error('Please set '.($_T8['xfsFree'] ? '$_T8[\'xfsFree\'] to false and ' : '').'$_T8[\'anonUploadDisable\'] to true.');

	$statuscode = intval(substr($upfiles, 9, 3));
	if ($statuscode >= 400 || preg_match('@<body><b>([^<>]+)</b></body></html>@i', $upfiles, $err)) html_error('Upload server isn\'t working or has failed'.(!empty($err[1]) ? ', response: ' . htmlentities($err[1]) : '.'));

	if (!empty($_T8['flashUpload'])) {
		$body = trim(substr($upfiles, strpos($upfiles, "\r\n\r\n") + 4));
		if (strpos($body, ':') === false || !($reply = explode(':', $body, 6)) || strlen($reply[0]) != 12) html_error('Bad response from flash uploader, response: ' . htmlentities($body));
		$download_link = $referer.$reply[0];
		return;
	}

	$page = cut_str($upfiles, '<Form name=\'F1\'', '</Form>');
	if (empty($page)) html_error('Error: upload_result form not found.');
	if (!preg_match_all('@<textarea [^<>]*name=\'([^\']+)\'[^<>]*>([^>]*)</textarea>@i', $page, $textareas)) html_error('Error: upload_result data not found.');
	$post = array_map('urlencode', array_map('html_entity_decode', array_combine(array_map('trim', $textareas[1]), array_map('trim', $textareas[2]))));
	if (empty($post['op']) || strtolower(urldecode($post['op'])) != 'upload_result') html_error('Error: "upload_result" value not found.');
	if (empty($post['fn'])) html_error('Error: "fn" input not found.');
	if (strtolower($post['st']) != 'ok') html_error('Upload failed, response: '.htmlentities(urldecode($post['st'])));

	$page = geturl($_T8['domain'], 80, $_T8['path'], $up_url, $cookie, $post, 0, $_GET['proxy'], $pauth);is_page($page);
	$host_rexexp = 'https?://(?:www\.)?'.preg_quote(str_ireplace('www.', '', $_T8['domain']).$_T8['path'], '@');

	if (preg_match('@('.$host_rexexp.'\w{12}(?:/[^\?/<>\"\'\r\n]+)?(?:\.html?)?)\?killcode=\w+@i', $page, $lnk)) {
		$download_link = $lnk[1];
		$delete_link = $lnk[0];
	} elseif (preg_match('@'.$host_rexexp.'del-(\w{12})-\w+/([^<>\"\'\r\n]+)@i', $page, $lnk)) {
		$download_link = substr($lnk[0], 0, (stripos($lnk[0], '/del-') + 1)) . $lnk[2] . '/' . $lnk[3];
		$delete_link = $lnk[0];
	} elseif (preg_match('@'.$host_rexexp.'\w{12}(?:/[^\?/<>\"\'\r\n]+)?(?:\.html?)?(?=[\r\n\t\s\'\"<>])@i', $page, $lnk)) $download_link = $lnk[0];
	else html_error('Download link not found.', 0);
}

//[17-8-2012] Written by Th3-822
//[30-9-2012] Using ?op=upload for some sites that need it. - Th3-822
//[17-10-2012] Added "domain requires the www." check. - Th3-822
//[08-11-2012] Added XFS Free support. - Th3-822
//[16-3-2013] Some updates, it should support more sites now & Added more error msgs. - Th3-822
//[05-8-2013] Plugin rewritten for making it a include (for saving space) & Added file-ext check & Small edits. - Th3-822
//[21-9-2013] Fixed upload url regexp (Now it will work on hosts that change upload.cgi filename/path) & Edits for allow extra cookies & Added support for XFS's flash upload. - Th3-822

?>