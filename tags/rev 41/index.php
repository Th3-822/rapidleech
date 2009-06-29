<?php
error_reporting ( 0 );
//ini_set('display_errors', 0);
set_time_limit ( 0 );
ini_alter ( "memory_limit", "1024M" );
ob_end_clean ();
ob_implicit_flush ( TRUE );
ignore_user_abort ( 1 );
clearstatcache ();
error_reporting ( 6135 );

$nn = "\r\n";
$fromaddr = "RapidLeech";
$dev_name = 'eqbal, updated by szalinski';
$rev_num = '41';
$PHP_SELF = ! $PHP_SELF ? $_SERVER ["PHP_SELF"] : $PHP_SELF;
define ( 'RAPIDLEECH', 'yes' );
define ( 'ROOT_DIR', realpath ( "./" ) );
define ( 'PATH_SPLITTER', (strstr ( ROOT_DIR, "\\" ) ? "\\" : "/") );
define ( 'HOST_DIR', 'hosts/' );
define ( 'IMAGE_DIR', 'images/' );
define ( 'CLASS_DIR', 'classes/' );
define ( 'CONFIG_DIR', 'configs/' );
define ( 'BUILD', '06282009' );
define ( 'CREDITS', '<a href="http://www.rapidleech.com/" style="text-decoration:none"><b>RapidLeech</b></a>&nbsp;<b style="color:#F09D19">PlugMod rev. ' . $rev_num . '</b> <span style="color:#F09D19">by ' . $dev_name . '</span><br><small style="color:#239FD9">Credits to Pramode &amp; Checkmate &amp; Kloon</small>' );

require_once (CONFIG_DIR . "config.php");
// $download_dir should always end with a '/'
if (substr ( $download_dir, - 1 ) != '/')
	$download_dir .= '/';

define ( 'DOWNLOAD_DIR', (substr ( $download_dir, 0, 6 ) == "ftp://" ? '' : $download_dir) );

if ($no_cache) {
	header ( "Expires: Mon, 26 Jul 1997 05:00:00 GMT" );
	header ( "Last-Modified: " . gmdate ( "D, d M Y H:i:s" ) . "GMT" );
	header ( "Cache-Control: no-cache, must-revalidate" );
	header ( "Pragma: no-cache" );
}
if (! defined ( 'CRLF' ))
	define ( 'CRLF', "\r\n" );
if (! defined ( "FTP_AUTOASCII" ))
	define ( "FTP_AUTOASCII", - 1 );
if (! defined ( "FTP_BINARY" ))
	define ( "FTP_BINARY", 1 );
if (! defined ( "FTP_ASCII" ))
	define ( "FTP_ASCII", 0 );
if (! defined ( 'FTP_FORCE' ))
	define ( 'FTP_FORCE', TRUE );
define ( 'FTP_OS_Unix', 'u' );
define ( 'FTP_OS_Windows', 'w' );
define ( 'FTP_OS_Mac', 'm' );

require_once (CLASS_DIR . "other.php");

// If configs/files.lst is not writable, give a warning
if (! is__writable ( CONFIG_DIR . 'files.lst' )) {
	html_error ( "configs/files.lst is not writable, please make sure it is chmod to 777" );
}

// If the download path is not writable, show error
if (! is__writable ( DOWNLOAD_DIR )) {
	html_error ( DOWNLOAD_DIR . " is selected as your download path and it is not writable. Please chmod it to 777" );
}

purge_files ( $delete_delay );

register_shutdown_function ( "pause_download" );

if ($login === true && (! isset ( $_SERVER ['PHP_AUTH_USER'] ) || ($loggeduser = logged_user ( $users )) === false)) {
	header ( "WWW-Authenticate: Basic realm=\"RAPIDLEECH PLUGMOD\"" );
	header ( "HTTP/1.0 401 Unauthorized" );
	exit ( "<html>$nn<head>$nn<title>RAPIDLEECH PLUGMOD</title>$nn<meta http-equiv=\"Content-Type\" content=\"text/html; charset=windows-1251\">$nn</head>$nn<body>$nn<h1>$nn<center>$nn<a href=http://www.rapidleech.com>RapidLeech</a>: Access Denied - Wrong Username or Password$nn</center>$nn</h1>$nn</body>$nn</html>" );
}

foreach ( $_POST as $key => $value ) {
	$_GET [$key] = $value;
}

if (! $_COOKIE) {
	if (strstr ( $_SERVER ["HTTP_COOKIE"], ";" )) {
		foreach ( explode ( "; ", $_SERVER ["HTTP_COOKIE"] ) as $key => $value ) {
			list ( $var, $val ) = explode ( "=", $value );
			$_COOKIE [$var] = $val;
		}
	} else {
		list ( $var, $val ) = @explode ( "=", $_SERVER ["HTTP_COOKIE"] );
		$_COOKIE [$var] = $val;
	}
}

require_once (CLASS_DIR . "cookie.php");

if (! @file_exists ( HOST_DIR . "download/hosts.php" )) {
	create_hosts_file ( "download/hosts.php" );
}

if (! empty ( $_GET ["image"] )) {
	require_once (CLASS_DIR . "http.php");
	require_once (CLASS_DIR . "image.php");
	exit ();
}

if (isset ( $_GET ["useproxy"] ) && (! $_GET ["proxy"] || ! strstr ( $_GET ["proxy"], ":" ))) {
	html_error ( "Wrong proxy address entered" );
} else {
	if ($_GET ["pauth"]) {
		$pauth = $_GET ["pauth"];
	} else {
		$pauth = ($_GET ["proxyuser"] && $_GET ["proxypass"]) ? base64_encode ( $_GET ["proxyuser"] . ":" . $_GET ["proxypass"] ) : "";
	}
}

if (! $_GET ["path"] || $download_dir_is_changeable == false) {
	if (! $_GET ["host"]) {
		$_GET ["path"] = (substr ( $download_dir, 0, 6 ) != "ftp://") ? realpath ( DOWNLOAD_DIR ) : $download_dir;
	} else {
		$_GET ["saveto"] = (substr ( $download_dir, 0, 6 ) != "ftp://") ? realpath ( DOWNLOAD_DIR ) : $download_dir;
	}
}
if (! $_GET ["filename"] || ! $_GET ["host"] || ! $_GET ["path"]) {
	//require "host.php";
	require_once (HOST_DIR . "download/hosts.php");
	
	$LINK = trim ( urldecode ( $_GET ["link"] ) );
	if (! $LINK) {
		require_once (CLASS_DIR . "main.php");
		exit ();
	}
	
	if (! empty ( $_GET ["saveto"] ) && ! $_GET ["path"]) {
		html_error ( "Path is not specified for saving this file" );
	}
	
	if (empty ( $_GET ["useproxy"] )) {
		$_GET ["proxy"] = "";
	}
	
	if (! empty ( $_GET ["domail"] ) && ! checkmail ( $_GET ["email"] )) {
		html_error ( "You didn't enter a valid e-mail address" );
		if ($_GET ["split"] && ! is_numeric ( $_GET ["partSize"] )) {
			html_error ( "Untrue a size of the part is specified" );
		}
	}
	
	$Referer = ($_GET ["referer"] ? trim ( urldecode ( $_GET ["referer"] ) ) : $LINK);
	$Url = parse_url ( $LINK );
	if ($Url ['scheme'] != 'http' && $Url ['scheme'] != 'https' && $Url ['scheme'] != 'ftp') {
		html_error ( "Unknown URL Type, <span style=color:#000>Only Use <span style=color:#05F>http</span> or <span style=color:#05F>https</span> or <span style=color:#05F>ftp</span> Protocol</span>" );
	}
	
	if ($_GET ["dis_plug"] != "on") {
		//check Domain-Host
		if (isset ( $_GET ["vBulletin_plug"] )) {
			print "<html>$nn<head>$nn<title>Downloading $LINK</title>$nn<meta http-equiv=\"Content-Type\" content=\"text/html; charset=windows-1251\">$nn";
			print "<style type=\"text/css\">$nn<!--$nn@import url(\"" . IMAGE_DIR . "rl_style_pm.css\");$nn-->$nn</style>$nn</head>$nn<body>$nn<center><img src=\"" . IMAGE_DIR . "logo_pm.gif\" alt=\"RAPIDLEECH PLUGMOD\"></center><br><br>$nn";
			require_once (CLASS_DIR . "http.php");
			require_once (HOST_DIR . "vBulletin_plug.php");
			exit ();
		} else {
			foreach ( $host as $site => $file ) {
				//if ($Url["host"] == $site)
				if (preg_match ( "/^(.+\.)?" . $site . "$/i", $Url ["host"] )) {
					print "<html>$nn<head>$nn<title>Downloading $LINK</title>$nn<meta http-equiv=\"Content-Type\" content=\"text/html; charset=windows-1251\">$nn";
					print "<style type=\"text/css\">$nn<!--$nn@import url(\"" . IMAGE_DIR . "rl_style_pm.css\");$nn-->$nn</style>$nn</head>$nn<body>$nn<center><img src=\"" . IMAGE_DIR . "logo_pm.gif\" alt=\"RAPIDLEECH PLUGMOD\"></center><br><br>$nn";
					require_once (CLASS_DIR . "http.php");
					require_once (HOST_DIR . 'download/' . $file);
					exit ();
				}
			}
		}
	}
	
	print "<html>$nn<head>$nn<title>Downloading $LINK</title>$nn<meta http-equiv=\"Content-Type\" content=\"text/html; charset=windows-1251\">$nn</head>$nn<body>$nn";
	
	$Url = parse_url ( $LINK );
	$FileName = ! $FileName ? basename ( $Url ["path"] ) : $FileName;
	$mydomain = $_SERVER['SERVER_NAME'];
	$myip = $_SERVER['SERVER_ADDR'];
	if(!$bw_save && preg_match("/($mydomain|$myip)/i", $Url["host"])) {
		html_error("You are not allowed to leech from <font color=black>".$mydomain." (".$myip.")</font>");
	}

	$auth = ($Url ["user"] && $Url ["pass"]) ? "&auth=" . base64_encode ( $Url ["user"] . ":" . $Url ["pass"] ) : "";
	
	if (isset ( $_GET ['cookieuse'] )) {
		if (strlen ( $_GET ['cookie'] > 0 )) {
			$_GET ['cookie'] .= ';' . $_POST ['cookie'];
		} else {
			$_GET ['cookie'] = $_POST ['cookie'];
		}
	}
	
	insert_location ( "$PHP_SELF?filename=" . urlencode ( $FileName ) . "&host=" . $Url ["host"] . "&port=" . $Url ["port"] . "&path=" . urlencode ( $Url ["path"] . ($Url ["query"] ? "?" . $Url ["query"] : "") ) . "&referer=" . urlencode ( $Referer ) . "&email=" . ($_GET ["domail"] ? $_GET ["email"] : "") . "&partSize=" . ($_GET ["split"] ? $_GET ["partSize"] : "") . "&method=" . $_GET ["method"] . "&proxy=" . ($_GET ["useproxy"] ? $_GET ["proxy"] : "") . "&saveto=" . $_GET ["path"] . "&link=" . urlencode ( $LINK ) . ($_GET ["add_comment"] == "on" ? "&comment=" . urlencode ( $_GET ["comment"] ) : "") . $auth . ($pauth ? "&pauth=$pauth" : "") . (isset ( $_GET ["audl"] ) ? "&audl=doum" : "") . "&cookie=" . urlencode ( $_GET ['cookie'] ) );
} else {
	echo ('<html>');
	echo ('<head>');
	echo ('<meta http-equiv="Content-Type" content="text/html; charset=windows-1251">');
	echo ('<title>Downloading...</title>');
	echo ('<link type="text/css" rel="stylesheet" href="' . IMAGE_DIR . 'rl_style_pm.css" />');
	echo ('</head>');
	echo ('<body>');
	echo ('<div align="center">');
	echo ('<img src="images/logo_pm.gif" alt="RAPIDLEECH PLUGMOD" /><br /><br />');
	
	do {
		list ( $_GET ["filename"], $tmp ) = explode ( '?', urldecode ( trim ( $_GET ["filename"] ) ) );
		$_GET ["saveto"] = urldecode ( trim ( $_GET ["saveto"] ) );
		$_GET ["host"] = urldecode ( trim ( $_GET ["host"] ) );
		$_GET ["path"] = urldecode ( trim ( $_GET ["path"] ) );
		$_GET ["port"] = $_GET ["port"] ? urldecode ( trim ( $_GET ["port"] ) ) : 80;
		$_GET ["referer"] = $_GET ["referer"] ? urldecode ( trim ( $_GET ["referer"] ) ) : 0;
		$_GET ["link"] = urldecode ( trim ( $_GET ["link"] ) );
		
		$_GET ["post"] = $_GET ["post"] ? unserialize ( stripslashes ( urldecode ( trim ( $_GET ["post"] ) ) ) ) : 0;
		$_GET ["cookie"] = $_GET ["cookie"] ? urldecode ( trim ( $_GET ["cookie"] ) ) : 0;
		
		//$resume_from = $_GET["resume"] ? intval(urldecode(trim($_GET["resume"]))) : 0;
		//if ($_GET["resume"]) {unset($_GET["resume"]);}
		

		$redirectto = "";
		
		$pauth = urldecode ( trim ( $_GET ["pauth"] ) );
		$auth = urldecode ( trim ( $_GET ["auth"] ) );
		
		if ($_GET ["auth"]) {
			$AUTH ["use"] = TRUE;
			$AUTH ["str"] = $_GET ["auth"];
		} else {
			unset ( $AUTH );
		}
		
		$ftp = parse_url ( $_GET ["link"] );
		
		$IS_FTP = $ftp ["scheme"] == "ftp" ? TRUE : FALSE;
		$AUTH ["ftp"] = array ("login" => $ftp ["user"] ? $ftp ["user"] : "anonymous", "password" => $ftp ["pass"] ? $ftp ["pass"] : "anonymous@leechget.com" );
		
		$pathWithName = $_GET ["saveto"] . PATH_SPLITTER . $_GET ["filename"];
		while ( stristr ( $pathWithName, "\\\\" ) ) {
			$pathWithName = str_replace ( "\\\\", "\\", $pathWithName );
		}
		
		list ( $pathWithName, $tmp ) = explode ( '?', $pathWithName );
		
		if ($ftp ["scheme"] == "ftp" && ! $_GET ["proxy"]) {
			require_once (CLASS_DIR . "ftp.php");
			$file = getftpurl ( $_GET ["host"], $ftp ["port"] ? $ftp ["port"] : 21, $_GET ["path"], $pathWithName );
		} else {
			require_once (CLASS_DIR . "http.php");
			$_GET ["force_name"] ? $force_name = urldecode ( $_GET ["force_name"] ) : '';
			$file = geturl ( $_GET ["host"], $_GET ["port"], $_GET ["path"], $_GET ["referer"], $_GET ["cookie"], $_GET ["post"], $pathWithName, $_GET ["proxy"], $pauth, $auth, $ftp ["scheme"] );
		}
		
		if ($redir && $lastError && stristr ( $lastError, "Error! it is redirected to [" )) {
			$redirectto = trim ( cut_str ( $lastError, "Error! it is redirected to [", "]" ) );
			print "Redirecting to: <b>$redirectto</b> ... <br>$nn";
			$_GET ["referer"] = $_GET ["link"];
			$_GET ["link"] = $redirectto;
			$purl = parse_url ( $redirectto );
			list ( $_GET ["filename"], $tmp ) = explode ( '?', basename ( $redirectto ) );
			
			// In case the redirect didn't include the host
			$_GET ["host"] = ($purl ["host"]) ? $purl ["host"] : $_GET ["host"];
			$_GET ["path"] = $purl ["path"] . ($purl ["query"] ? "?" . $purl ["query"] : "");
			$_GET ['port'] = $purl ['port'] ? $purl ['port'] : 80;
			$lastError = "";
		}
	
	} while ( $redirectto && ! $lastError );
	
	if ($lastError) {
		html_error ( $lastError, 0 );
	} elseif ($file ["bytesReceived"] == $file ["bytesTotal"] || $file ["size"] == "Unknown") {
		$inCurrDir = stristr ( dirname ( $pathWithName ), ROOT_DIR ) ? TRUE : FALSE;
		if ($inCurrDir) {
			$Path = parse_url ( $PHP_SELF );
			$Path = substr ( $Path ["path"], 0, strlen ( $Path ["path"] ) - strlen ( strrchr ( $Path ["path"], "/" ) ) );
		}
		print "<script>pr(100, '" . $file ["size"] . "', '" . $file ["speed"] . "')</script>\r\n";
		print "File <b>" . ($inCurrDir ? "<a href=\"" . $Path . "/" . substr ( dirname ( $pathWithName ), strlen ( ROOT_DIR ) + 1 ) . "/" . basename ( $file ["file"] ) . "\">" : "") . basename ( $file ["file"] ) . ($inCurrDir ? "</a>" : "") . "</b> (<b>" . $file ["size"] . "</b>) Saved!<br>Time: <b>" . $file ["time"] . "</b><br>Average Speed: <b>" . $file ["speed"] . " KB/s</b><br>";
		$file ['date'] = time ();
		if (! write_file ( CONFIG_DIR . "files.lst", serialize ( array ("name" => $file ["file"], "size" => $file ["size"], "date" => $file ['date'], "link" => $_GET ["link"], "comment" => str_replace ( "\n", "\\n", str_replace ( "\r", "\\r", $_GET ["comment"] ) ) ) ) . "\r\n", 0 )) {
			print "Couldn't update the files list<br>";
		}
		if ($_GET ["email"]) {
			require_once (CLASS_DIR . "mail.php");
			$_GET ["partSize"] = (isset ( $_GET ["partSize"] ) ? $_GET ["partSize"] * 1024 * 1024 : FALSE);
			if (xmail ( $fromaddr, $_GET ["email"], "File " . basename ( $file ["file"] ), "File: " . basename ( $file ["file"] ) . "\r\n" . "Link: " . $_GET ["link"] . ($_GET ["comment"] ? "\r\n" . "Comments: " . str_replace ( "\\r\\n", "\r\n", $_GET ["comment"] ) : ""), $pathWithName, $_GET ["partSize"], $_GET ["method"] )) {
				print "<script>mail('File was sent to this address<b>" . $_GET ["email"] . "</b>.', '" . basename ( $file ["file"] ) . "');</script>\r\n";
			} else {
				print "Error sending file!<br>";
			}
		}
		echo ('<form method="post" name="flist">');
		echo ('<input type="hidden" name="files[]" value="' . $file ['date'] . '" /><br />');
		echo ('<div align="center">');
		echo ('<select name="act" onChange="javascript:void(document.flist.submit());"');
		if ($disable_action) {
			echo (' style="display:none;"');
		}
		echo ('>');
		echo ('<option selected="selected">Action</option>');
		echo ('<option value="upload">Upload</option>');
		echo ('<option value="ftp">FTP File</option>');
		echo ('<option value="mail">E-Mail</option>');
		echo ('<option value="boxes">Mass Submits</option>');
		echo ('<option value="split">Split Files</option>');
		echo ('<option value="merge">Merge Files</option>');
		echo ('<option value="md5">MD5 Hash</option>');
		if (file_exists ( CLASS_DIR . "pear.php" ) || file_exists ( CLASS_DIR . "tar.php" )) {
			echo ('<option value="pack">Pack Files</option>');
		}
		if (file_exists ( CLASS_DIR . "pclzip.php" )) {
			echo ('<option value="zip">ZIP Files</option>');
		}
		if (file_exists ( CLASS_DIR . "unzip.php" )) {
			echo ('<option value="unzip">Unzip Files (beta)</option>');
		}
		if (! $disable_deleting) {
			echo ('<option value="rename">Rename</option>');
			echo ('<option value="mrename">Mass Rename</option>');
			echo ('<option value="delete">Delete</option>');
		}
		echo ('</select>');
		echo ('</div>');
		echo ('</form>');
		print "<br><a href=\"" . $PHP_SELF . "\">Go back to main</a>";
		if (isset ( $_GET ["audl"] )) {
			echo "\r\n<script language=javascript>parent.nextlink();</script>";
		}
	} else {
		unlink ( $pathWithName );
		print "Connection lost, file deleted.<br><a href=\"javascript:location.reload();\">Reload</a>";
		if (isset ( $_GET ["audl"] )) {
			echo "\r\n<script language=javascript>parent.nextlink();</script>";
		}
		print "<script>location.reload();</script>";
	}
	echo ('</div>');
	echo ('</body>');
	echo ('</html>');
}
?>