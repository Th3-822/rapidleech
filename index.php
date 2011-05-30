<?php
error_reporting (0);
// ini_set('display_errors', 0);
set_time_limit (0);
ini_alter ("memory_limit", "1024M");
ob_end_clean ();
ob_implicit_flush (true);
ignore_user_abort (true);
clearstatcache ();
error_reporting (6135);
$nn = "\r\n";
$fromaddr = "RapidLeech";
$dev_name = 'Final';
$rev_num = '42';
$plusrar_v = '4.1';
$PHP_SELF = ! $PHP_SELF ? $_SERVER ["PHP_SELF"] : $PHP_SELF;
define ('RAPIDLEECH', 'yes');
define ('ROOT_DIR', realpath ("./"));
define ('PATH_SPLITTER', (strstr (ROOT_DIR, "\\") ? "\\" : "/"));
define ('HOST_DIR', 'hosts/');
define ('IMAGE_DIR', 'images/');
define ('CLASS_DIR', 'classes/');
define ('CONFIG_DIR', 'configs/');
define ('BUILD', '08Jun2010');
define ('CREDITS', '<a href="http://www.rapidleech.com/" class="rl-link"><b>RapidLeech</b></a>&nbsp;<b class="rev-dev">PlugMod (eqbal) rev. ' . $rev_num . '</b> <span class="rev-dev">' . $dev_name . '</span><br><small class="small-credits">Credits to Pramode &amp; Checkmate &amp; Kloon</small><br /><p class="rapidleechhost"><a href="http://www.rapidleechhost.com/aff.php?aff=001" target="_blank">RapidleechHost Offical Hosting</a></p>');

require_once(CONFIG_DIR . 'setup.php');
// $options['download_dir'] should always end with a '/'
if (substr ($options['download_dir'], - 1) != '/')
	$options['download_dir'] .= '/';

define ('DOWNLOAD_DIR', (substr ($options['download_dir'], 0, 6) == "ftp://" ? '' : $options['download_dir']));

define ('TEMPLATE_DIR', 'templates/' . $options['template_used'] . '/');

if ($options['no_cache'])
{
	header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header ("Last-Modified: " . gmdate ("D, d M Y H:i:s") . "GMT");
	header ("Cache-Control: no-cache, must-revalidate");
	header ("Pragma: no-cache");
}
if (! defined ('CRLF'))
	define ('CRLF', "\r\n");
if (! defined ("FTP_AUTOASCII"))
	define ("FTP_AUTOASCII", - 1);
if (! defined ("FTP_BINARY"))
	define ("FTP_BINARY", 1);
if (! defined ("FTP_ASCII"))
	define ("FTP_ASCII", 0);
if (! defined ('FTP_FORCE'))
	define ('FTP_FORCE', true);
define ('FTP_OS_Unix', 'u');
define ('FTP_OS_Windows', 'w');
define ('FTP_OS_Mac', 'm');

require_once (CLASS_DIR . "other.php");

require_once (TEMPLATE_DIR . 'functions.php');
// If configs/files.lst is not writable, give a warning
if (! is__writable (CONFIG_DIR . 'files.lst'))
{
	html_error (lang (304));
}
// If the download path is not writable, show error
if (! is__writable (DOWNLOAD_DIR))
{
	html_error (DOWNLOAD_DIR . lang (305));
}

purge_files ($options['delete_delay']);

register_shutdown_function ("pause_download");

login_check();

foreach ($_POST as $key => $value)
{
	$_GET [$key] = $value;
}

if (! $_COOKIE)
{
	if (strstr ($_SERVER ["HTTP_COOKIE"], ";"))
	{
		foreach (explode ("; ", $_SERVER ["HTTP_COOKIE"]) as $key => $value)
		{
			list ($var, $val) = explode ("=", $value);
			$_COOKIE [$var] = $val;
		}
	}
	else
	{
		list ($var, $val) = @explode ("=", $_SERVER ["HTTP_COOKIE"]);
		$_COOKIE [$var] = $val;
	}
}

require_once (CLASS_DIR . "cookie.php");

if (! @file_exists (HOST_DIR . "download/hosts.php"))
{
	create_hosts_file ("download/hosts.php");
}

if (! empty ($_GET ["image"]))
{
	require_once (CLASS_DIR . "http.php");
	require_once (CLASS_DIR . "image.php");
	exit ();
}

if (isset ($_GET ["useproxy"]) && (! $_GET ["proxy"] || ! strstr ($_GET ["proxy"], ":")))
{
	html_error (lang(324));
}
else
{
	if ($_GET ["pauth"])
	{
		$pauth = $_GET ["pauth"];
	}
	else
	{
		$pauth = ($_GET ["proxyuser"] && $_GET ["proxypass"]) ? base64_encode ($_GET ["proxyuser"] . ":" . $_GET ["proxypass"]) : "";
	}
}

if (! $_GET ["path"] || $options['download_dir_is_changeable'] == false)
{
	if (! $_GET ["host"])
	{
		$_GET ["path"] = (substr ($options['download_dir'], 0, 6) != "ftp://") ? realpath (DOWNLOAD_DIR) : $options['download_dir'];
	}
	else
	{
		$_GET ["saveto"] = (substr ($options['download_dir'], 0, 6) != "ftp://") ? realpath (DOWNLOAD_DIR) : $options['download_dir'];
	}
}
if (! $_GET ["filename"] || ! $_GET ["host"] || ! $_GET ["path"])
{
	// require "host.php";
	require_once (HOST_DIR . "download/hosts.php");

	$LINK = trim (urldecode ($_GET ["link"]));
	if (! $LINK)
	{
		require_once (CLASS_DIR . "main.php");
		exit ();
	} 
	// Detect if it doesn't have a protocol assigned
	if (substr($LINK, 0, 7) != "http://" && substr($LINK, 0, 6) != "ftp://" && substr($LINK, 0, 6) != "ssl://" && substr($LINK, 0, 8) != "https://" && !stristr($LINK, '://'))
	{ 
		// Automatically assign http://
		$LINK = "http://" . $LINK;
	}

	if (! empty ($_GET ["saveto"]) && ! $_GET ["path"])
	{
		html_error (lang(6));
	}

	if (empty ($_GET ["useproxy"]))
	{
		$_GET ["proxy"] = "";
	}

	if (! empty ($_GET ["domail"]) && ! checkmail ($_GET ["email"]))
	{
		html_error (lang(3));
		if ($_GET ["split"] && ! is_numeric ($_GET ["partSize"]))
		{
			html_error (lang(4));
		}
	}

	$Referer = ($_GET ["referer"] ? trim (urldecode ($_GET ["referer"])) : $LINK);
	$Url = parse_url ($LINK);
	if ($Url ['scheme'] != 'http' && $Url ['scheme'] != 'https' && $Url ['scheme'] != 'ftp')
	{
		html_error (lang(5));
	}

	if ($_GET['user_pass'] == "on")
	{
		$Url['user'] = $_GET['iuser'];
		$Url['pass'] = $_GET['ipass']; 
		// Rebuild url
		$query = "";
		if ($Url['query'])
		{
			$query = '?' . $Url['query'];
		}
		$LINK = $Url['scheme'] . "://" . $Url['user'] . ":" . $Url['pass'] . "@" . $Url['host'] . $Url['path'] . $query;
	}

	if ($_GET ["dis_plug"] != "on")
	{ 
		// check Domain-Host
		if (isset ($_GET ["vBulletin_plug"]))
		{ 
			// print "<html>$nn<head>$nn<title>Downloading $LINK</title>$nn<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\" />$nn";
			include(TEMPLATE_DIR . '/header.php'); 
			// print "<style type=\"text/css\">$nn<!--$nn@import url(\"" . IMAGE_DIR . "rl_style_pm.css\");$nn-->$nn</style>$nn</head>$nn<body>$nn<center><img src=\"" . IMAGE_DIR . "logo_pm.gif\" alt=\"RAPIDLEECH PLUGMOD\" /></center><br /><br />$nn";
			require_once (CLASS_DIR . "http.php");
			require_once (HOST_DIR . "vBulletin_plug.php");
			exit ();
		}
		else
		{
			foreach ($host as $site => $file)
			{ 
				// if ($Url["host"] == $site)
				if (preg_match ("/^(.+\.)?" . $site . "$/i", $Url ["host"]))
				{
					// print "<html>$nn<head>$nn<title>Downloading $LINK</title>$nn<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\" />$nn";
					// print "<style type=\"text/css\">$nn<!--$nn@import url(\"" . IMAGE_DIR . "rl_style_pm.css\");$nn-->$nn</style>$nn</head>$nn<body>$nn<center><img src=\"" . IMAGE_DIR . "logo_pm.gif\" alt=\"RAPIDLEECH PLUGMOD\" /></center><br /><br />$nn";
					include(TEMPLATE_DIR . '/header.php');
					require_once (CLASS_DIR . "http.php");
					require_once (HOST_DIR . "DownloadClass.php");
					require_once (HOST_DIR . 'download/' . $file);
					$class = substr($file, 0, -4);
					$firstchar = substr($file, 0, 1);
					if ($firstchar > 0)
					{
						$class = "d" . $class;
					}
					if (class_exists($class))
					{
						$hostClass = new $class();
						$hostClass->Download($LINK);
					}
					exit ();
				}
			}
		}
	} 
	// print "<html>$nn<head>$nn<title>Downloading $LINK</title>$nn<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\" />$nn</head>$nn<body>$nn";
	include(TEMPLATE_DIR . '/header.php');

	$Url = parse_url ($LINK);
	$FileName = basename ($Url ["path"]);
	$mydomain = $_SERVER['SERVER_NAME'];
	$myip = $_SERVER['SERVER_ADDR'];
	if (!$options['bw_save'] && preg_match("/($mydomain|$myip)/i", $Url["host"]))
	{
		html_error(sprintf(lang(7), $mydomain, $myip));
	}

	$auth = ($Url ["user"] && $Url ["pass"]) ? "&auth=" . urlencode (encrypt (base64_encode ($Url ["user"] . ":" . $Url ["pass"]))) : ""; 

	if (isset ($_GET ['cookieuse']))
	{
		if (strlen ($_GET ['cookie'] > 0))
		{
			$_GET ['cookie'] .= ';' . $_POST ['cookie'];
		}
		else
		{
			$_GET ['cookie'] = $_POST ['cookie'];
		}
	}

	insert_location ("$PHP_SELF?filename=" . urlencode ($FileName) . "&host=" . $Url ["host"] . "&port=" . $Url ["port"] . "&path=" . urlencode ($Url ["path"] . ($Url ["query"] ? "?" . $Url ["query"] : "")) . "&referer=" . urlencode ($Referer) . "&email=" . ($_GET ["domail"] ? $_GET ["email"] : "") . "&partSize=" . ($_GET ["split"] ? $_GET ["partSize"] : "") . "&method=" . $_GET ["method"] . "&proxy=" . ($_GET ["useproxy"] ? $_GET ["proxy"] : "") . "&saveto=" . $_GET ["path"] . "&link=" . urlencode ($LINK) . ($_GET ["add_comment"] == "on" ? "&comment=" . urlencode ($_GET ["comment"]) : "") . $auth . ($pauth ? "&pauth=$pauth" : "") . (isset ($_GET ["audl"]) ? "&audl=doum" : "") . "&cookie=" . urlencode (encrypt ($_GET ['cookie'])));
}
else
{
	include(TEMPLATE_DIR . '/header.php');
	echo('<div align="center">');

	do
	{
		list ($_GET ["filename"], $tmp) = explode ('?', urldecode (trim ($_GET ["filename"])));
		$_GET ["saveto"] = urldecode (trim ($_GET ["saveto"]));
		$_GET ["host"] = urldecode (trim ($_GET ["host"]));
		$_GET ["path"] = urldecode (trim ($_GET ["path"]));
		$_GET ["port"] = $_GET ["port"] ? urldecode (trim ($_GET ["port"])) : 80;
		$_GET ["referer"] = $_GET ["referer"] ? urldecode (trim ($_GET ["referer"])) : 0;
		$_GET ["link"] = urldecode (trim ($_GET ["link"]));

		$_GET ["post"] = $_GET ["post"] ? unserialize (stripslashes (urldecode (trim ($_GET ["post"])))) : 0;
		$_GET ["cookie"] = $_GET ["cookie"] ? decrypt(urldecode(trim($_GET["cookie"]))) : "";
		// $resume_from = $_GET["resume"] ? intval(urldecode(trim($_GET["resume"]))) : 0;
		// if ($_GET["resume"]) {unset($_GET["resume"]);}
		$redirectto = "";

		$pauth = urldecode (trim ($_GET ["pauth"]));

		if ($_GET['auth'] == 1)
		{
			if (!preg_match("|^(?:.+\.)?(.+\..+)$|i", $_GET ["host"], $hostmatch)) html_error('No valid hostname found for authorisation!');
			$hostmatch = str_replace('.', '_', $hostmatch[1]);
			if ($premium_acc ["$hostmatch"] && $premium_acc ["$hostmatch"] ["user"] && $premium_acc ["$hostmatch"] ["pass"])
			{
				$auth = base64_encode ( $premium_acc ["$hostmatch"] ["user"] . ":" . $premium_acc ["$hostmatch"] ["pass"] );
			}
			else html_error('No useable premium account found for this download - please set one in accounts.php');
		}
		else
		{
			$auth = decrypt(urldecode(trim($_GET['auth'])));
		}

		if ($_GET ["auth"])
		{
			$AUTH ["use"] = true;
			$AUTH ["str"] = $_GET ["auth"];
		}
		else
		{
			unset ($AUTH);
		}

		$ftp = parse_url ($_GET ["link"]);

		$IS_FTP = $ftp ["scheme"] == "ftp" ? true : false;
		$AUTH ["ftp"] = array ("login" => $ftp ["user"] ? $ftp ["user"] : "anonymous", "password" => $ftp ["pass"] ? $ftp ["pass"] : "anonymous@leechget.com");

		$pathWithName = $_GET ["saveto"] . PATH_SPLITTER . $_GET ["filename"];
		while (stristr ($pathWithName, "\\\\"))
		{
			$pathWithName = str_replace ("\\\\", "\\", $pathWithName);
		}

		list ($pathWithName, $tmp) = explode ('?', $pathWithName);

		if ($ftp ["scheme"] == "ftp" && ! $_GET ["proxy"])
		{
			require_once (CLASS_DIR . "ftp.php");
			$file = getftpurl ($_GET ["host"], $ftp ["port"] ? $ftp ["port"] : 21, $_GET ["path"], $pathWithName);
		}
		else
		{
			require_once (CLASS_DIR . "http.php");
			$_GET ["force_name"] ? $force_name = urldecode ($_GET ["force_name"]) : '';
			$file = geturl ($_GET ["host"], $_GET ["port"], $_GET ["path"], $_GET ["referer"], $_GET ["cookie"], $_GET ["post"], $pathWithName, $_GET ["proxy"], $pauth, $auth, $ftp ["scheme"]);
		}

		if ($options['redir'] && $lastError && stristr ($lastError, "Error! it is redirected to ["))
		{
			$redirectto = trim (cut_str ($lastError, "Error! it is redirected to [", "]"));
			print lang(8) . " <b>$redirectto</b> ... <br />$nn";
			$_GET ["referer"] = $_GET ["link"];
			$_GET ["link"] = $redirectto;
			$purl = parse_url ($redirectto);
			list ($_GET ["filename"], $tmp) = explode ('?', basename ($redirectto)); 
			// In case the redirect didn't include the host
			$_GET ["host"] = ($purl ["host"]) ? $purl ["host"] : $_GET ["host"];
			$_GET ["path"] = $purl ["path"] . ($purl ["query"] ? "?" . $purl ["query"] : "");
			$_GET ['port'] = $purl ['port'] ? $purl ['port'] : 80;
			$lastError = "";
		}
	}
	while ($redirectto && ! $lastError);

	if ($lastError)
	{
		html_error ($lastError, 0);
	}
	elseif ($file ["bytesReceived"] == $file ["bytesTotal"] || $file ["size"] == "Unknown")
	{
		echo '<script type="text/javascript">' . "pr(100, '" . $file ["size"] . "', '" . $file ["speed"] . "')</script>\r\n";
		echo sprintf(lang(10), link_for_file(dirname($pathWithName) . '/' . basename($file["file"])), $file ["size"], $file ["time"], $file ["speed"]);
		$file ['date'] = time ();
		if (! write_file (CONFIG_DIR . "files.lst", serialize (array ("name" => $file ["file"], "size" => $file ["size"], "date" => $file ['date'], "link" => $_GET ["link"], "comment" => str_replace ("\n", "\\n", str_replace ("\r", "\\r", $_GET ["comment"])))) . "\r\n", 0))
		{
			echo lang(9) . '<br />';
		}
		if ($_GET ["email"])
		{
			require_once (CLASS_DIR . "mail.php");
			$_GET ["partSize"] = (isset ($_GET ["partSize"]) ? $_GET ["partSize"] * 1024 * 1024 : false);
			if (xmail ($fromaddr, $_GET ["email"], "File " . basename ($file ["file"]), "File: " . basename ($file ["file"]) . "\r\n" . "Link: " . $_GET ["link"] . ($_GET ["comment"] ? "\r\n" . "Comments: " . str_replace ("\\r\\n", "\r\n", $_GET ["comment"]) : ""), $pathWithName, $_GET ["partSize"], $_GET ["method"]))
			{
				printf(lang(11), $_GET['email'], basename($file['file']));
			}
			else
			{
				echo lang(12) . "<br />";
			}
		}
		echo ('<form method="post" name="flist" action="' . $PHP_SELF . '">');
		echo ('<input type="hidden" name="files[]" value="' . $file ['date'] . '" /><br />');
		echo ('<div align="center">');
		echo renderActions();
		echo ('</div>');
		echo ('</form>');
		if ($options['new_window'])
		{
			echo '<br /><a href="javascript:window.close();">' . lang(378) . '</a>';
		}
		else
		{
			echo '<br /><a href="' . $PHP_SELF . '">' . lang(13) . '</a>';
		}
		if (isset ($_GET ["audl"]))
		{
			echo $nn . '<script type="text/javascript">parent.nextlink();</script>';
		}
	}
	else
	{
		unlink ($pathWithName);
		print lang(14) . '<br /><a href="javascript:location.reload();">' . lang(15) . '</a>';
		if (isset ($_GET ["audl"]))
		{
			echo $nn . '<script type="text/javascript">parent.nextlink();</script>';
		}
		echo '<script type="text/javascript">location.reload();</script>';
	}
	echo ('</div>');
	echo ('</body>');
	echo ('</html>');
}

?>
