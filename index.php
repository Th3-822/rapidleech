<?php

require_once('rl_init.php');
ignore_user_abort(true);
login_check();

// If configs/files.lst is not writable, give a warning
if (!is__writable(CONFIG_DIR . 'files.lst')) html_error(lang(304));

// If the download path is not writable, show error
if (!is__writable(DOWNLOAD_DIR)) html_error(DOWNLOAD_DIR . lang(305));

purge_files($options['delete_delay']);
register_shutdown_function('pause_download');

$_REQUEST = $_GET = array_merge($_GET, $_POST);
$_REQUEST['premium_acc'] = $_POST['premium_acc'] = $_GET['premium_acc'] = isset($_REQUEST['premium_acc']) && $_REQUEST['premium_acc'] == 'on' ? 'on' : false;
$_REQUEST['cookieuse'] = $_POST['cookieuse'] = $_GET['cookieuse'] = isset($_REQUEST['cookieuse']) && $_REQUEST['cookieuse'] == 'on' ? 'on' : false;

require_once(CLASS_DIR . 'cookie.php');

if (!@file_exists(HOST_DIR . 'download/hosts.php')) html_error(lang(127));

// require "hosts.php";
require_once(HOST_DIR . 'download/hosts.php');

if (!empty($_GET['image'])) {
	require_once(CLASS_DIR . 'http.php');
	require_once(CLASS_DIR . 'image.php');
	exit();
}

$_GET['proxy'] = !empty($_GET['proxy']) ? trim(urldecode($_GET['proxy'])) : '';
if (!empty($_GET['useproxy']) && (empty($_GET['proxy']) || strpos($_GET['proxy'], ':') === false)) html_error(lang(324));
$pauth = (!empty($_GET['proxyuser']) && !empty($_GET['proxypass'])) ? base64_encode($_GET['proxyuser'] . ':' . $_GET['proxypass']) : (!empty($_GET['pauth']) ? decrypt(urldecode(trim($_GET['pauth']))) : '');

if (empty($_GET['path']) || $options['download_dir_is_changeable'] == false) {
	if (empty($_GET['host'])) $_GET['path'] = (substr($options['download_dir'], 0, 6) != 'ftp://') ? realpath(DOWNLOAD_DIR) : $options['download_dir'];
	else $_GET['saveto'] = (substr($options['download_dir'], 0, 6) != 'ftp://') ? realpath(DOWNLOAD_DIR) : $options['download_dir'];
}

if (empty($_GET['filename']) || empty($_GET['host']) || empty($_GET['path'])) {
	$LINK = !empty($_GET['link']) ? trim($_GET['link']) : false;
	if (!$LINK) {
		require_once(CLASS_DIR . 'main.php');
		exit();
	}

	if ($options['ref_check']) check_referer();

	// Detect if it doesn't have a protocol assigned
	if (stripos($LINK, '://') === false || (strtolower(substr($LINK, 0, 7)) != 'http://' && strtolower(substr($LINK, 0, 6)) != 'ftp://' && strtolower(substr($LINK, 0, 6)) != 'ssl://' && strtolower(substr($LINK, 0, 8)) != 'https://')) {
		// Automatically assign http://
		$LINK = 'http://' . $LINK;
	}

	if (!empty($_GET['saveto']) && empty($_GET['path'])) html_error(lang(6));

	if (!empty($_GET['domail']) && !checkmail($_GET['email'])) {
		html_error(lang(3));
		if (!empty($_GET['split']) && !is_numeric($_GET['partSize'])) html_error(lang(4)); // T-8: Check this.
	}

	$Url = parse_url($LINK);
	$Url['scheme'] = strtolower($Url['scheme']);
	$Url['host'] = strtolower($Url['host']);

	$Url['path'] = (empty($Url['path'])) ? '/' : str_replace('%7C', '|', $Url['path']);
	$LINK = rebuild_url($Url);

	if (empty($_GET['referer'])) {
		$Referer = $Url;
		$Referer['scheme'] = strtolower($Referer['scheme']);
		// Remove login from Referer
		unset($Referer['user'], $Referer['pass']);
		$Referer = rebuild_url($Referer);
	} else $Referer = trim(rawurldecode($_GET['referer']));

	if (!in_array($Url['scheme'], array('http', 'https', 'ftp'))) html_error(lang(5));

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

	include(TEMPLATE_DIR . '/header.php');

	if (empty($_GET['dis_plug']) || $_GET['dis_plug'] != 'on') {
		// check Domain-Host
		foreach ($host as $site => $file) {
			if (host_matches($site, $Url['host'])) {
				error_reporting(E_ERROR | E_PARSE | error_reporting()); // Make sure to show any critical error while loading the plugin.
				require_once(CLASS_DIR . 'http.php');
				require_once(HOST_DIR . 'DownloadClass.php');
				$class = substr($file, 0, -4);
				if (empty($auth) && !empty($_GET['premium_acc'])) {
					if (!empty($premium_acc["$class"]['proxy'])) {
						if ($premium_acc["$class"]['proxy'] != -1) {
							// Load Server-Side Proxy for this host.
							$_GET['useproxy'] = 'on';
							$_GET['proxy'] = $premium_acc["$class"]['proxy'];
							$pauth = (!empty($premium_acc["$class"]['pauth']) ? base64_encode($premium_acc["$class"]['pauth']) : false);
						}
					} else if (!empty($premium_acc["$class"])) {
						// Disable User-Proxy Support for Server-Side accounts.
						$_GET['useproxy'] = $_GET['proxy'] = $pauth = false;
					}
				}
				require_once(HOST_DIR . "download/$file");
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

	$mydomain = strtolower(($pos = strpos($_SERVER['HTTP_HOST'], ':')) !== false ? substr($_SERVER['HTTP_HOST'], 0, $pos) : $_SERVER['HTTP_HOST']);
	if ($options['bw_save'] && ($Url['host'] == $_SERVER['SERVER_ADDR'] || host_matches($mydomain, $Url['host']))) html_error(sprintf(lang(7), $mydomain, $_SERVER['SERVER_ADDR']));
	if (empty($auth) && !empty($_GET['premium_acc'])) {
		if (!empty($premium_acc[$Url['host']]['proxy'])) {
			$proxy = $premium_acc[$Url['host']]['proxy'];
			if ($proxy != -1) {
				$_GET['useproxy'] = ($proxy != -1 ? 'on' : false);
				$_GET['proxy'] = ($proxy != -1 ? $proxy : false);
				$pauth = (!empty($premium_acc[$Url['host']]['pauth']) ? base64_encode($premium_acc[$Url['host']]['pauth']) : false);
			}
		} else $proxy = false;

		if (!empty($premium_acc[$Url['host']]['user']) && !empty($premium_acc[$Url['host']]['pass'])) $auth = '2';
		else if (empty($_GET['cookie']) && !empty($premium_acc[$Url['host']]['cookie'])) $auth = '3';
		else if (!filter_var($Url['host'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6)) {
			foreach (array_keys($premium_acc) as $site) if (host_matches($site, $Url['host'])) {
				if (empty($proxy) && !empty($premium_acc["$site"]['proxy'])) {
					$proxy = $premium_acc["$site"]['proxy'];
					if ($proxy != -1) {
						// Load Server-Side Proxy for this host.
						$_GET['useproxy'] = 'on';
						$_GET['proxy'] = $proxy;
						$pauth = (!empty($premium_acc["$site"]['pauth']) ? base64_encode($premium_acc["$site"]['pauth']) : false);
					}
				} else if (!empty($premium_acc["$site"])) {
					// Disable User-Proxy Support for Server-Side accounts.
					$_GET['useproxy'] = $_GET['proxy'] = $pauth = false;
				}
				if (!empty($premium_acc["$site"]['user']) && !empty($premium_acc["$site"]['pass'])) {
					$auth = '2';
					break;
				} else if (empty($_GET['cookie']) && !empty($premium_acc["$site"]['cookie'])) {
					$auth = '3';
					break;
				}
			}
		}
	}

	$redir = GetDefaultParams();
	$redir['dis_plug'] = 'on';
	$redir['filename'] = urlencode((isset($Url['path']) && basename($Url['path'])) ? urldecode(basename($Url['path'])) : 'index.html');
	$redir['host'] = urlencode($Url['host']);
	if (!empty($Url['port'])) $redir['port'] = urlencode($Url['port']);
	$redir['path'] = urlencode($Url['path'] . (!empty($Url['query']) ? '?' . $Url['query'] : ''));
	if (!empty($Referer)) $redir['referer'] = urlencode($Referer);
	$redir['link'] = urlencode($LINK);
	if (!empty($_GET['cookie'])) $redir['cookie'] = urlencode(encrypt($_GET['cookie']));
	if (!empty($auth)) $redir['auth'] = $auth;

	insert_location($redir);
} else {
	require_once(TEMPLATE_DIR . 'functions.php');
	include(TEMPLATE_DIR . '/header.php');
	if ($options['ref_check']) check_referer();
	echo('<div align="center">');

	$_GET['saveto'] = urldecode(trim($_GET['saveto']));
	do {
		$_GET['filename'] = urldecode(trim($_GET['filename']));
		if (strpos($_GET['filename'], '?') !== false) $_GET['filename'] = substr($_GET['filename'], 0, strpos($_GET['filename'], '?'));
		$_GET['host'] = strtolower(urldecode(trim($_GET['host'])));
		$_GET['path'] = urldecode(trim($_GET['path']));
		$_GET['port'] = !empty($_GET['port']) ? urldecode(trim($_GET['port'])) : 0;
		$_GET['referer'] = !empty($_GET['referer']) ? urldecode(trim($_GET['referer'])) : 0;
		$_GET['link'] = urldecode(trim($_GET['link']));

		$_GET['post'] = !empty($_GET['post']) ? unserialize(decrypt(urldecode(trim($_GET['post'])))) : 0;
		$_GET['cookie'] = !empty($_GET['cookie']) ? decrypt(urldecode(trim($_GET['cookie']))) : '';
		// $resume_from = $_GET["resume"] ? intval(urldecode(trim($_GET["resume"]))) : 0;
		// if ($_GET["resume"]) {unset($_GET["resume"]);}
		$redirectto = '';

		if ($options['bw_save']) {
			$mydomain = ($pos = strpos($_SERVER['HTTP_HOST'], ':')) !== false ? substr($_SERVER['HTTP_HOST'], 0, $pos) : $_SERVER['HTTP_HOST'];
			if (($_GET['host'] == $_SERVER['SERVER_ADDR'] || host_matches($mydomain, $_GET['host']))) html_error(sprintf(lang(7), $mydomain, $_SERVER['SERVER_ADDR']));
			unset($mydomain);
		}

		$AUTH = array();
		$_GET['auth'] = !empty($_GET['auth']) ? trim($_GET['auth']) : '';
		if ($_GET['auth'] == '1') {
			if (!preg_match('|^(?:.+\.)?(.+\..+)$|i', $_GET['host'], $hostmatch)) html_error('No valid hostname found for authorisation!');
			$hostmatch = str_replace('.', '_', $hostmatch[1]);
			if (isset($premium_acc["$hostmatch"]) && is_array($premium_acc["$hostmatch"]) && !empty($premium_acc["$hostmatch"]['user']) && !empty($premium_acc["$hostmatch"]['pass'])) {
				$AUTH['user'] = $premium_acc["$hostmatch"]['user'];
				$AUTH['pass'] = $premium_acc["$hostmatch"]['pass'];
				$auth = base64_encode($AUTH['user'] . ':' . $AUTH['pass']);
			} else html_error('No usable premium account found for this download - please set one in accounts.php');
		} elseif ($_GET['auth'] == '2') {
			if (!empty($premium_acc[$_GET['host']]['user']) && !empty($premium_acc[$_GET['host']]['pass'])) {
				$AUTH['user'] = $premium_acc[$_GET['host']]['user'];
				$AUTH['pass'] = $premium_acc[$_GET['host']]['pass'];
				$auth = base64_encode($AUTH['user'] . ':' . $AUTH['pass']);
			} else if (!filter_var($_GET['host'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6)) {
				foreach (array_keys($premium_acc) as $site) {
					if (host_matches($site, $_GET['host']) && !empty($premium_acc["$site"]['user']) && !empty($premium_acc["$site"]['pass'])) {
						$AUTH['user'] = $premium_acc["$site"]['user'];
						$AUTH['pass'] = $premium_acc["$site"]['pass'];
						$auth = base64_encode($AUTH['user'] . ':' . $AUTH['pass']);
						break;
					}
				}
			}
		} elseif ($_GET['auth'] == '3') {
			$auth = false;
			if (!empty($premium_acc[$_GET['host']]['cookie'])) {
				$_GET['cookie'] = trim($premium_acc[$_GET['host']]['cookie']);
			} else if (!filter_var($_GET['host'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6)) {
				foreach (array_keys($premium_acc) as $site) {
					if (host_matches($site, $_GET['host']) && !empty($premium_acc["$site"]['cookie'])) {
						$_GET['cookie'] = trim($premium_acc["$site"]['cookie']);
						break;
					}
				}
			}
		} elseif (!empty($_GET['auth'])) {
			$auth = decrypt(urldecode($_GET['auth']));
			list($AUTH['user'], $AUTH['pass']) = array_map('rawurldecode', explode(':', base64_decode($auth), 2));
		} else $auth = false;

		$pathWithName = $_GET['saveto'] . PATH_SPLITTER . basename($_GET['filename']);
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
			$redirectto = substr($lastError, strpos(lang(95), '%1$s'));
			$redirectto = trim(substr($redirectto, 0, strrpos($redirectto, ']')));
			print lang(8) . ' <b>' . htmlspecialchars($redirectto) . "</b> ... <br />$nn";
			$_GET['referer'] = urlencode($_GET['link']);
			if (!preg_match('@^(?:https?|ftp)\://@i', $redirectto)) { // If redirect doesn't have the host
				$ref = parse_url($_GET['link']);
				unset($ref['user'], $ref['pass'], $ref['query'], $ref['fragment']);
				if (substr($redirectto, 0, 1) != '/') $redirectto = "/$redirectto";
				$purl = array_merge($ref, parse_url($redirectto));
			} else $purl = parse_url($redirectto);
			$_GET['link'] = urlencode(rebuild_url($purl));
			if (basename($purl['path'])) $_GET['filename'] = basename($purl['path']);
			else $_GET['filename'] = 'index.html';
			$_GET['host'] = urlencode($purl['host']);
			$_GET['path'] = urlencode($purl['path'] . (!empty($purl['query']) ? '?' . $purl['query'] : ''));
			$_GET['port'] = !empty($purl['port']) ? $purl['port'] : 0;
			$_GET['cookie'] = !empty($_GET['cookie']) ? urlencode(encrypt($_GET['cookie'])) : '';
			if (is_array($_GET['post'])) $_GET['post'] = false;//$_GET['post'] = urlencode(encrypt(serialize($_GET['post'])));
			$lastError = $_GET['auth'] = '';
			unset($ref, $purl);
		}
	} while ($redirectto && !$lastError);

	if ($lastError) html_error(htmlspecialchars($lastError));
	elseif ($file['bytesReceived'] == $file['bytesTotal'] || $file['size'] == 'Unknown') {
		echo '<script type="text/javascript">' . "pr(100, '" . $file['size'] . "', '" . $file['speed'] . "')</script>\r\n";
		echo sprintf(lang(10), link_for_file($file['file']), $file['size'], $file['time'], $file['speed']);
		$file['date'] = time();

		if (!write_file(CONFIG_DIR . 'files.lst', serialize(array('name' => $file['file'], 'size' => $file['size'], 'date' => $file['date'], 'link' => $_GET['link'], 'comment' => (!empty($_GET['comment']) ? str_replace(array("\r", "\n"), array('\r', '\n'), $_GET['comment']) : ''))) . "\r\n", 0)) echo lang(9) . '<br />';

		if (!empty($_GET['email']) && !$options['disable_email']) {
			require_once(CLASS_DIR . 'mail.php');
			$_GET['partSize'] = (isset($_GET['partSize']) && is_numeric($_GET['partSize']) ? $_GET['partSize'] * 1024 * 1024 : false);
			if (xmail($fromaddr, $_GET['email'], 'File ' . $file['name'], 'File: ' . $file['name'] . "\r\n" . 'Link: ' . $_GET['link'] . (!empty($_GET['comment']) ? "\r\n" . 'Comments: ' . str_replace (array('\r', '\n'), array("\r", "\n"), $_GET['comment']) : ''), $pathWithName, $_GET['partSize'], ($_GET['partSize'] && !empty($_GET['method']) ? $_GET['method'] : ''))) {
				printf(lang(11), $_GET['email'], $file['name']);
			} else echo lang(12) . '<br />';
		}
		echo "\n<form method='POST' name='flist' action='{$_SERVER['SCRIPT_NAME']}'>\n";
		echo "\t<input type='hidden' name='files[]' value='{$file['date']}' /><br />\n";
		echo "\t<div style='text-align:center;'>\n";
		echo renderActions();
		echo "\t</div>\n";
		echo "</form>\n";
		if ($options['new_window']) echo '<br /><a href="javascript:window.close();">' . lang(378) . '</a>';
		else echo "<br /><a href='{$_SERVER['SCRIPT_NAME']}'>" . lang(13) . "</a>";

		if (!empty($_GET['audl'])) echo $nn . '<script type="text/javascript">parent.nextlink();</script>';
	} else {
		unlink($pathWithName);
		print lang(14) . '<br /><a href="javascript:location.reload();">' . lang(15) . '</a>';
		if (!empty($_GET['audl'])) {
			echo $nn . '<script type="text/javascript">parent.nextlink();</script>';
		}
		//echo '<script type="text/javascript">location.reload();</script>';
	}
	echo "\n</div>\n</body>\n</html>";
}