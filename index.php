<?php
// ini_set('display_errors', 0);
@set_time_limit(0);
ini_alter('memory_limit', '1024M');
if (ob_get_level()) ob_end_clean();
ob_implicit_flush(true);
ignore_user_abort(true);
clearstatcache();
error_reporting(6135);
$nn = "\r\n";
$fromaddr = 'RapidLeech';
$dev_name = 'Development Stage';
$rev_num = '43';
$plusrar_v = '4.1';
$PHP_SELF = $_SERVER['SCRIPT_NAME'];
define('RAPIDLEECH', 'yes');
define('ROOT_DIR', realpath('./'));
define('PATH_SPLITTER', ((strpos(ROOT_DIR, '\\') !== false) ? '\\' : '/'));
define('HOST_DIR', 'hosts/');
define('CLASS_DIR', 'classes/');
define('CONFIG_DIR', 'configs/');
define('BUILD', '30May2011');
define('CREDITS', '<a href="http://www.rapidleech.com/" class="rl-link"><b>RapidLeech</b></a>&nbsp;<b class="rev-dev">PlugMod (eqbal) rev. ' . $rev_num . '</b> <span class="rev-dev">' . $dev_name . '</span><br><small class="small-credits">Credits to Pramode &amp; Checkmate &amp; Kloon</small><br /><p class="rapidleechhost"><a href="http://www.rapidleechhost.com/aff.php?aff=001" target="_blank">RapidleechHost Offical Hosting</a></p>');

require_once(CONFIG_DIR . 'setup.php');
// $options['download_dir'] should always end with a '/'
if (substr($options['download_dir'], - 1) != '/') $options['download_dir'] .= '/';
define('DOWNLOAD_DIR', (substr($options['download_dir'], 0, 6) == 'ftp://' ? '' : $options['download_dir']));

define('TEMPLATE_DIR', 'templates/' . $options['template_used'] . '/');
define('IMAGE_DIR', TEMPLATE_DIR . 'images/');

if ($options['no_cache']) {
	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	header('Last-Modified: ' . gmdate ("D, d M Y H:i:s") . 'GMT');
	header('Cache-Control: no-cache, must-revalidate');
	header('Pragma: no-cache');
}

require_once(CLASS_DIR . 'other.php');

require_once(TEMPLATE_DIR . 'functions.php');
// If configs/files.lst is not writable, give a warning
if (!is__writable(CONFIG_DIR . 'files.lst')) html_error(lang(304));

// If the download path is not writable, show error
if (!is__writable(DOWNLOAD_DIR)) html_error(DOWNLOAD_DIR . lang(305));

purge_files($options['delete_delay']);

register_shutdown_function('pause_download');

login_check();

$_REQUEST['premium_acc'] = $_POST['premium_acc'] = isset($_REQUEST['premium_acc']) && $_REQUEST['premium_acc'] == 'on' ? 'on' : false;
$_REQUEST['cookieuse'] = $_POST['cookieuse'] = isset($_REQUEST['cookieuse']) && $_REQUEST['cookieuse'] == 'on' ? 'on' : false;

foreach($_POST as $key => $value) $_GET[$key] = $value;

if (!$_COOKIE) {
	if (isset($_SERVER['HTTP_COOKIE']) && strpos ($_SERVER['HTTP_COOKIE'], ';') !== false) {
		foreach(explode('; ', $_SERVER['HTTP_COOKIE']) as $key => $value) {
			list ($var, $val) = explode('=', $value);
			$_COOKIE[$var] = $val;
		}
	} elseif (!empty($_SERVER['HTTP_COOKIE'])) {
		list($var, $val) = @explode('=', $_SERVER['HTTP_COOKIE']);
		$_COOKIE[$var] = $val;
	}
}

require_once(CLASS_DIR . 'cookie.php');

if (!@file_exists(HOST_DIR . 'download/hosts.php')) html_error(lang(127));

// require "hosts.php";
require_once(HOST_DIR . 'download/hosts.php');

if (!empty ($_GET['image'])) {
	require_once(CLASS_DIR . 'http.php');
	require_once(CLASS_DIR . 'image.php');
	exit();
}

if (isset($_GET['useproxy']) && (empty($_GET['proxy']) || strpos($_GET['proxy'], ':') === false)) html_error(lang(324));
if (!empty($_GET['pauth'])) $pauth = decrypt(urldecode(trim($_GET['pauth'])));
else $pauth = (!empty($_GET['proxyuser']) && !empty($_GET['proxypass'])) ? base64_encode($_GET['proxyuser'] . ':' . $_GET['proxypass']) : '';

if (empty($_GET['path']) || $options['download_dir_is_changeable'] == false) {
	if (empty($_GET['host'])) $_GET['path'] = (substr($options['download_dir'], 0, 6) != 'ftp://') ? realpath(DOWNLOAD_DIR) : $options['download_dir'];
	else $_GET['saveto'] = (substr($options['download_dir'], 0, 6) != 'ftp://') ? realpath(DOWNLOAD_DIR) : $options['download_dir'];
}

if (empty($_GET['filename']) || empty($_GET['host']) || empty($_GET['path'])) {
	$LINK = !empty($_GET['link']) ? trim(rawurldecode($_GET['link'])) : false;
	if (!$LINK) {
		require_once(CLASS_DIR . 'main.php');
		exit();
	}

	check_referer();

	// Detect if it doesn't have a protocol assigned
	if (stripos($LINK, '://') === false || (substr($LINK, 0, 7) != 'http://' && substr($LINK, 0, 6) != 'ftp://' && substr($LINK, 0, 6) != 'ssl://' && substr($LINK, 0, 8) != 'https://')) {
		// Automatically assign http://
		$LINK = 'http://' . $LINK;
	}

	if (!empty($_GET['saveto']) && empty($_GET['path'])) html_error(lang(6));

	if (empty($_GET['useproxy'])) $_GET['proxy'] = '';

	if (!empty($_GET['domail']) && !checkmail($_GET['email'])) {
		html_error(lang(3));
		if (!empty($_GET['split']) && !is_numeric($_GET['partSize'])) html_error(lang(4)); // T-8: Check this.
	}

	$Url = parse_url($LINK);

	$Url['path'] = (empty($Url['path'])) ? '/' :str_replace('%2F', '/', rawurlencode(rawurldecode($Url['path'])));
	$LINK = rebuild_url($Url);

	if (empty($_GET['referer'])) {
		$Referer = $Url;
		// Remove login from Referer
		unset($Referer['user'], $Referer['pass']);
		$Referer = rebuild_url($Referer);
	} else $Referer = trim(rawurldecode($_GET['referer']));

	if ($Url['scheme'] != 'http' && $Url['scheme'] != 'https' && $Url['scheme'] != 'ftp') html_error(lang(5));

	if (empty($Url['user']) xor empty($Url['pass'])) {
		unset($Url['user'], $Url['pass']);
		$LINK = rebuild_url($Url);
	}

	if (isset($_GET['user_pass']) && $_GET['user_pass'] == 'on' && !empty($_GET['iuser']) && !empty($_GET['ipass'])) {
		$Url['user'] = $_GET['iuser'];
		$Url['pass'] = $_GET['ipass'];
		// Rebuild url
		$LINK = rebuild_url($Url);
	}

	// If Url has user & pass, use them as premium login for plugins and set $auth for direct download.
	if (!empty($Url['user']) && !empty($Url['pass'])) {
		if (empty($_REQUEST['premium_acc'])) $_GET['premium_acc'] = $_POST['premium_acc'] = $_REQUEST['premium_acc'] = 'on';
		$_GET['premium_user'] = $_POST['premium_user'] = $_REQUEST['premium_user'] = $Url['user'];
		$_GET['premium_pass'] = $_POST['premium_pass'] = $_REQUEST['premium_pass'] = $Url['pass'];

		$auth = urlencode(encrypt(base64_encode(rawurlencode($Url['user']) . ':' . rawurlencode($Url['pass']))));

		// Lets delete User and Pass from link because isn't needed now.
		unset($Url['user'], $Url['pass']);
		$LINK = rebuild_url($Url);
	} else $auth = '';

	if (empty($_GET['dis_plug']) || $_GET ['dis_plug'] != 'on') {
		// check Domain-Host
		foreach ($host as $site => $file) {
			if (host_matches($site, $Url['host'])) {
				include(TEMPLATE_DIR . '/header.php');
				require_once(CLASS_DIR . 'http.php');
				require_once(HOST_DIR . 'DownloadClass.php');
				require_once(HOST_DIR . 'download/' . $file);
				$class = substr($file, 0, -4);
				$firstchar = substr($file, 0, 1);
				if ($firstchar > 0) $class = "d$class";
				if (class_exists($class)) {
					$hostClass = new $class();
					$hostClass->Download($LINK);
				}
				exit();
			}
		}
	}
	// print "<html>$nn<head>$nn<title>Downloading $LINK</title>$nn<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\" />$nn</head>$nn<body>$nn";
	include(TEMPLATE_DIR . '/header.php');

	$FileName = isset($Url['path']) ? basename($Url['path']) : '';
	$mydomain = ($pos = strpos($_SERVER['HTTP_HOST'], ':')) !== false ? substr($_SERVER['HTTP_HOST'], 0, $pos) : $_SERVER['HTTP_HOST'];
	if ($options['bw_save'] && ($Url['host'] == $_SERVER['SERVER_ADDR'] || host_matches($mydomain, $Url['host']))) html_error(sprintf(lang(7), $mydomain, $_SERVER['SERVER_ADDR']));

	$redir = GetDefaultParams();
	$redir['dis_plug'] = 'on';
	$redir['filename'] = urlencode($FileName);
	$redir['host'] = urlencode($Url['host']);
	if (!empty($Url['port'])) $redir['port'] = urlencode($Url['port']);
	$redir['path'] = urlencode($Url['path'] . (!empty($Url['query']) ? '?' . $Url['query'] : ''));
	if (!empty($Referer)) $redir['referer'] = urlencode($Referer);
	$redir['link'] = urlencode($LINK);
	if (!empty($_GET['cookie'])) $redir['cookie'] = urlencode(encrypt($_GET['cookie']));
	if (!empty($auth)) $redir['auth'] = $auth;

	insert_location($redir);
} else {
	include(TEMPLATE_DIR . '/header.php');
	check_referer();
	echo('<div align="center">');

	do {
		$_GET['filename'] = urldecode(trim($_GET['filename']));
		if (strpos($_GET['filename'], '?') !== false) $_GET['filename'] = substr($_GET['filename'], 0, strpos($_GET['filename'], '?'));
		$_GET['saveto'] = urldecode(trim($_GET['saveto']));
		$_GET['host'] = urldecode(trim($_GET['host']));
		$_GET['path'] = urldecode(trim($_GET['path']));
		$_GET['port'] = !empty($_GET['port']) ? urldecode(trim($_GET['port'])) : 0;
		$_GET['referer'] = !empty($_GET['referer']) ? urldecode(trim($_GET['referer'])) : 0;
		$_GET['link'] = urldecode(trim($_GET['link']));

		$_GET['post'] = !empty($_GET['post']) ? unserialize(decrypt(urldecode(trim($_GET['post'])))) : 0;
		$_GET['cookie'] = !empty($_GET['cookie']) ? decrypt(urldecode(trim($_GET['cookie']))) : '';
		$_GET['proxy'] = !empty($_GET['proxy']) ? trim(urldecode($_GET['proxy'])) : '';
		// $resume_from = $_GET["resume"] ? intval(urldecode(trim($_GET["resume"]))) : 0;
		// if ($_GET["resume"]) {unset($_GET["resume"]);}
		$redirectto = '';

		$pauth = !empty($_GET['pauth']) ? decrypt(urldecode(trim($_GET['pauth']))) : '';

		$AUTH = array();
		$_GET['auth'] = !empty($_GET['auth']) ? trim($_GET['auth']) : '';
		if ($_GET['auth'] == '1') {
			if (!preg_match('|^(?:.+\.)?(.+\..+)$|i', $_GET['host'], $hostmatch)) html_error('No valid hostname found for authorisation!');
			$hostmatch = str_replace('.', '_', $hostmatch[1]);
			if (isset($premium_acc["$hostmatch"]) && is_array($premium_acc["$hostmatch"]) && !empty($premium_acc["$hostmatch"]['user']) && !empty($premium_acc["$hostmatch"]['pass'])) {
				$auth = base64_encode($premium_acc["$hostmatch"]['user'] . ':' . $premium_acc["$hostmatch"]['pass']);
			} else html_error('No usable premium account found for this download - please set one in accounts.php');
		} elseif (!empty($_GET['auth'])) {
			$auth = decrypt(urldecode($_GET['auth']));
			list($AUTH['user'], $AUTH['pass']) = array_map('rawurldecode', explode(':', base64_decode($auth), 2));
		} else $auth = false;

		$pathWithName = $_GET['saveto'] . PATH_SPLITTER . basename(urldecode($_GET['filename']));
		while (strpos($pathWithName, "\\\\") !== false) $pathWithName = str_replace("\\\\", "\\", $pathWithName);
		if (strpos($pathWithName, '?') !== false) $pathWithName = substr($pathWithName, 0, strpos($pathWithName, '?'));

		$url = parse_url($_GET['link']);
		if (empty($url['port'])) $url['port'] = $_GET['port'];
		if (isset($url['scheme']) && $url['scheme'] == 'ftp' && empty($_GET['proxy'])) {
			require_once(CLASS_DIR . 'ftp.php');
			$file = getftpurl($_GET['host'], defport($url), urldecode($_GET['path']), $pathWithName);
		} else {
			require_once(CLASS_DIR . 'http.php');
			!empty($_GET['force_name']) ? $force_name = urldecode($_GET['force_name']) : '';
			$file = geturl($_GET['host'], defport($url), $_GET['path'], $_GET['referer'], $_GET['cookie'], $_GET['post'], $pathWithName, $_GET['proxy'], $pauth, $auth, $url['scheme']);
		}

		if ($options['redir'] && $lastError && strpos($lastError, substr(lang(95), 0, strpos(lang(95), '%1$s'))) !== false) {
			$redirectto = trim(cut_str($lastError, substr(lang(95), 0, strpos(lang(95), '%1$s')), ']'));
			print lang(8) . " <b>$redirectto</b> ... <br />$nn";
			$_GET['referer'] = urlencode($_GET['link']);
			if (strpos($redirectto, '://') === false) { // If redirect doesn't have the host
				$ref = parse_url(urldecode($_GET['referer']));
				unset($ref['user'], $ref['pass'], $ref['query'], $ref['fragment']);
				if (substr($redirectto, 0, 1) != '/') $redirectto = "/$redirectto";
				$purl = array_merge($ref, parse_url($redirectto));
			} else $purl = parse_url($redirectto);
			$_GET['link'] = urlencode(rebuild_url($purl));
			$_GET['filename'] = urlencode(basename($purl['path']));
			$_GET['host'] = urlencode($purl['host']);
			$_GET['path'] = urlencode($purl['path'] . (!empty($purl['query']) ? '?' . $purl['query'] : ''));
			$_GET['port'] = !empty($purl['port']) ? $purl['port'] : 80;
			$_GET['cookie'] = !empty($_GET['cookie']) ? urlencode(encrypt($_GET['cookie'])) : '';
			if (is_array($_GET['post'])) $_GET['post'] = urlencode(encrypt(serialize($_GET['post'])));
			if (!empty($_GET['proxy'])) {
				$_GET['proxy'] = urlencode($_GET['proxy']);
				if (!empty($pauth)) $_GET['pauth'] = urlencode(encrypt($pauth));
			}
			$lastError = $_GET['auth'] = ''; // With $_GET['auth'] empty it will still using the $auth
			unset($ref, $purl);
		}
	} while ($redirectto && !$lastError);

	if ($lastError) html_error($lastError, 0);
	elseif ($file['bytesReceived'] == $file['bytesTotal'] || $file['size'] == 'Unknown') {
		echo '<script type="text/javascript">' . "pr(100, '" . $file['size'] . "', '" . $file['speed'] . "')</script>\r\n";
		echo sprintf(lang(10), link_for_file(dirname($pathWithName) . '/' . basename($file['file'])), $file['size'], $file['time'], $file['speed']);
		$file['date'] = time();

		if (!write_file(CONFIG_DIR . 'files.lst', serialize(array('name' => $file['file'], 'size' => $file['size'], 'date' => $file['date'], 'link' => $_GET['link'], 'comment' => (!empty($_GET['comment']) ? str_replace(array("\r", "\n"), array('\r', '\n'), $_GET['comment']) : ''))) . "\r\n", 0)) echo lang(9) . '<br />';

		if (!empty($_GET['email']) && !$options['disable_email']) {
			require_once(CLASS_DIR . 'mail.php');
			$_GET['partSize'] = (isset($_GET['partSize']) && is_numeric($_GET['partSize']) ? $_GET['partSize'] * 1024 * 1024 : false);
			if (xmail($fromaddr, $_GET['email'], 'File ' . basename($file['file']), 'File: ' . basename($file['file']) . "\r\n" . 'Link: ' . $_GET['link'] . (!empty($_GET['comment']) ? "\r\n" . 'Comments: ' . str_replace (array('\r', '\n'), array("\r", "\n"), $_GET['comment']) : ''), $pathWithName, $_GET['partSize'], ($_GET['partSize'] && !empty($_GET['method']) ? $_GET['method'] : ''))) {
				printf(lang(11), $_GET['email'], basename($file['file']));
			} else echo lang(12) . '<br />';
		}
		echo "\n<form method='POST' name='flist' action='$PHP_SELF'>\n";
		echo "\t<input type='hidden' name='files[]' value='{$file['date']}' /><br />\n";
		echo "\t<div style='text-align:center;'>\n";
		echo renderActions();
		echo "\t</div>\n";
		echo "</form>\n";
		if ($options['new_window']) echo '<br /><a href="javascript:window.close();">' . lang(378) . '</a>';
		else echo "<br /><a href='$PHP_SELF'>" . lang(13) . "</a>";

		if (!empty($_GET['audl'])) echo $nn . '<script type="text/javascript">parent.nextlink();</script>';
	} else {
		unlink($pathWithName);
		print lang(14) . '<br /><a href="javascript:location.reload();">' . lang(15) . '</a>';
		if (!empty($_GET['audl'])) {
			echo $nn . '<script type="text/javascript">parent.nextlink();</script>';
		}
		echo '<script type="text/javascript">location.reload();</script>';
	}
	echo "\n</div>\n</body>\n</html>";
}

?>