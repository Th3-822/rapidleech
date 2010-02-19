<?php
define('RAPIDLEECH', 'yes');
define('IMAGE_DIR', 'images/');
define('CONFIG_DIR', 'configs/');
define('HOST_DIR', 'hosts/');
error_reporting(0);
set_time_limit(0);
@ini_alter("memory_limit", "1024M");
@ob_end_clean();
ob_implicit_flush(TRUE);
ignore_user_abort(1);
clearstatcache();
error_reporting(6135);
$nn = "\r\n";
require_once(CONFIG_DIR.'setup.php');
require_once("classes/other.php");
define ( 'TEMPLATE_DIR', 'templates/'.$options['template_used'].'/' );

if ($options['login'] === true && (!isset($_SERVER['PHP_AUTH_USER']) || ($loggeduser = logged_user($options['users'])) === false))
	{
		header("WWW-Authenticate: Basic realm=\"RAPIDLEECH PLUGMOD\"");
		header("HTTP/1.0 401 Unauthorized");
		include('deny.php');
		exit;
	}
include("classes/http.php");

if(!defined('CRLF')) define('CRLF',"\r\n");
$_REQUEST['filename']=base64_decode($_REQUEST['filename']);

// Check if requested upload file is within our $options['download_dir']
// We put basename() because we are quite sure that no one is able to upload things besides the download directory normally
// htmlentities() prevents XSS attacks
$_REQUEST['filename'] = htmlentities($options['download_dir'].basename($_REQUEST['filename']));
$_REQUEST['uploaded'] = htmlentities($_REQUEST['uploaded']);
// We want to check if the selected upload service is a valid ones
$d = opendir ( HOST_DIR . "upload/" );
while ( false !== ($modules = readdir ( $d )) ) {
	if ($modules != "." && $modules != "..") {
		if (is_file ( HOST_DIR . "upload/" . $modules )) {
			if (strpos ( $modules, ".index.php" ))
				include_once (HOST_DIR . "upload/" . $modules);
		}
	}
}
if (!in_array($_REQUEST['uploaded'],$upload_services) || !$_REQUEST['uploaded'] || !$_REQUEST['filename']) {
	html_error(lang(46));
}

$page_title = sprintf(lang(63),basename($_REQUEST['filename']),$_REQUEST['uploaded']);
require(TEMPLATE_DIR.'/header.php');
?>
<?php
if (!file_exists($_REQUEST['filename']))
	{
		html_error(sprintf(lang(64),$filename));
	}
				
if (is_readable($_REQUEST['filename']))
	{
		$lfile=$_REQUEST['filename'];
		$lname=basename($lfile);
	}
		else
	{
		html_error(sprintf(lang(65),$filename));
	}

$fsize = getSize($lfile);

echo '<script type="text/javascript" language="javascript">var orlink="' . basename($_REQUEST['filename']) . ' to ' . $_REQUEST['uploaded'] . '";</script>';

if (file_exists("hosts/upload/".$_REQUEST['uploaded'].".php")){    
    include_once("hosts/upload/".$_REQUEST['uploaded'].".index.php");
    if ($max_file_size[$_REQUEST['uploaded']]!=false)
        if ($fsize > $max_file_size[$_REQUEST['uploaded']]*1024*1024)       
            html_error(lang(66));
    include_once("hosts/upload/".$page_upload[$_REQUEST['uploaded']]);
}
else html_error(lang(67));

if ($download_link || $delete_link || $stat_link || $adm_link)
	{
			//Protect down link with http://lix.in/
			/*
			if ($_REQUEST['protect']==1){
				unset($post);
				$post['url'] =$download_link;
				$post['button'] = 'Protect+Link';
				$post['op'] = 'crypt_single';
				$post['reset']='Clear';
				$page = geturl("lix.in",80,"/index.php","http://lix.in/",0,$post);
				$tmp = cut_str($page,"http://lix.in/","'");
				if (!empty($tmp)) $protect = "http://lix.in/".$tmp;
			}
			*/			
			
			echo "\n<table width=100% border=0>";
			echo ($download_link ? "<tr><td width=100 nowrap align=right><b>".lang(68).":</b><td width=80%><input value='$download_link' class='upstyles-dllink' readonly></tr>" : "");
			echo ($delete_link ? "<tr><td width=100 nowrap align=right>".lang(69).":<td width=80%><input value='$delete_link' class='upstyles-dellink' readonly></tr>" : "");
			echo ($stat_link ? "<tr><td width=100 nowrap align=right>".lang(70).":<td width=80%><input value='$stat_link' class='upstyles-statlink' readonly></tr>" : "");
			echo ($adm_link ? "<tr><td width=100 nowrap align=right>".lang(71).":<td width=80%><input value='$adm_link' class='upstyles-admlink' readonly></tr>": "");
			echo ($user_id ? "<tr><td width=100 nowrap align=right>".lang(72).":<td width=80%><input value='$user_id' class='upstyles-userid' readonly></tr>": "");
			echo ($ftp_uplink ? "<tr><td width=100 nowrap align=right>".lang(73).":<td width=80%><input value='$ftp_uplink' class='upstyles-ftpuplink' readonly></tr>": "");
			echo ($access_pass ? "<tr><td width=100 nowrap align=right>".lang(74).":<td width=80%><input value='$access_pass' class='upstyles-accesspass' readonly></tr>": "");
			/*echo ($protect ? "<tr><td width=100 nowrap align=right>Protect link:<td width=80%><input value='$protect' style=\"width: 470px; border: 1px solid #55AAFF; background-color: #FFFFFF; padding:3px\" readonly></tr>": "");*/
			echo "</table>\n";
			
			if(!file_exists(trim($lfile).".upload.html") && !isset($_GET['auul']))
			  {
				$html_header = "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">
								<html xmlns=\"http://www.w3.org/1999/xhtml\">
								<head>
								<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\" />
								<title>".lang(75)."</title>
								<style type=\"text/css\">
body {
	font-family: tahoma, arial, \"times New Roman\", georgia, verdana, sans-serif;
	font-size: 11px;
	color: #333333;
	background-color: #EFF0F4;
	margin: 0px;
	padding: 0px;
}
.linktitle {
	width: 576px;
	background-color: #C291F9;
	text-align: center;
	padding:3px;
	margin-top: 25px;
	margin-right: auto;
	margin-bottom: 0;
	margin-left: auto;
	border-top-width: 1px;
	border-right-width: 1px;
	border-bottom-width: 0px;
	border-left-width: 1px;
	border-top-style: solid;
	border-right-style: solid;
	border-bottom-style: solid;
	border-left-style: solid;
	border-top-color: #C7C4FB;
	border-right-color: #C7C4FB;
	border-bottom-color: #C7C4FB;
	border-left-color: #C7C4FB;
}
.bluefont {
	color: #0E078F;
	font-family: tahoma, arial, \"times New Roman\", georgia, verdana, sans-serif;
	font-size: 11px;
}
hr {
	border-top-width: 0px;
	border-right-width: 0px;
	border-bottom-width: 0px;
	border-left-width: 0px;
	border-top-style: solid;
	height: 1px;
	background-color: #046FC6;
	color: #046FC6;
	border-right-style: solid;
	border-bottom-style: solid;
	border-left-style: solid;
	width: 90%;
}
.host .links {
	width: 95%;
	margin:0 auto;
	text-align:left;
	padding:3px 0 3px 10px;
	border: 1px dashed #666666;
	background-color: #F2F1FE;
}
.host {
	width: 600px;
	margin: 10px auto 10px;
}
.host .links a {
	text-decoration:none;
	color: #666666;
	font-size: 11px;
}
.host .links a:hover {
	text-decoration:none;
	color:#E8740B
}
.host .title {
	width: 95%;
	margin:0 auto;
	text-align:left;
	padding:3px 0 3px 10px;
	background-color: #C7C4FB;
	color: #000000;
	border-top-width: 1px;
	border-right-width: 1px;
	border-bottom-width: 0px;
	border-left-width: 1px;
	border-top-style: dashed;
	border-right-style: dashed;
	border-bottom-style: dashed;
	border-left-style: dashed;
	border-top-color: #333333;
	border-right-color: #333333;
	border-bottom-color: #333333;
	border-left-color: #333333;
	font-size: 12px;
	font-family: Georgia, \"Times New Roman\", Times, serif;
}
								</style>
								</head>
								<body>
								";
				$fp = fopen(trim($lfile).".upload.html",'w');
				fwrite($fp, $html_header);
				fwrite($fp, sprintf(lang(76),$lname,bytesToKbOrMb($fsize)));
				fclose($fp);
			  }
			$fr = fopen(trim($lfile).".upload.html",'a+');
			if ($fr)
				{
					/*fwrite($fr,date("Y-m-d H:i:s")."\n");
					fwrite($fr,$lname."  ".bytesToKbOrMb($fsize)."\n");
					if ($download_link) { fwrite($fr,"download link: $download_link\r\n");}
					if ($delete_link) { fwrite($fr,"delete link: $delete_link\r\n");}
					if ($stat_link) { fwrite($fr,"stat link: $stat_link\r\n");}
					if ($adm_link) { fwrite($fr,"ADM link: $adm_link\r\n");}
					if ($user_id) {fwrite($fr,"USER ID: $user_id\r\n");}
					if ($access_pass) {fwrite($fr,"PASSWD: $access_pass\r\n");}
					if ($ftp_uplink) {fwrite($fr,"ftp upload: $ftp_uplink\r\n");}
					//if ($protect) {fwrite($fr,"protect link: $protect\r\n");}
					fwrite($fr,"\n");*/
					fwrite($fr, "<div class=\"host\"><div class=\"title\"><strong>".$_REQUEST['uploaded']."</strong> - <span class=\"bluefont\">".date("Y-m-d H:i:s")."</span></div>");
					fwrite($fr, "<div class=\"links\">");
					if ($download_link) fwrite($fr, "<strong>".lang(68).": <a href=\"".$download_link."\" target=\"_blank\">".$download_link." </a></strong>");
					if ($delete_link) fwrite($fr, "<br />".lang(69).": <a href=\"".$delete_link."\" target=\"_blank\">".$delete_link." </a>");
					if ($stat_link) fwrite($fr, "<br />".lang(70).": <a href=\"".$stat_link."\" target=\"_blank\">".$stat_link." </a>");
					if ($adm_link) fwrite($fr, "<br />".lang(71).": <a href=\"".$adm_link."\" target=\"_blank\">".$adm_link." </a>");
					if ($user_id) fwrite($fr, "<br />".lang(72).": <a href=\"".$user_id."\" target=\"_blank\">".$user_id." </a>");
					if ($access_pass) fwrite($fr, "<br />".lang(74).": <a href=\"".$access_pass."\" target=\"_blank\">".$access_pass." </a>");
					if ($ftp_uplink) fwrite($fr, "<br />".lang(73).": <a href=\"".$ftp_uplink."\" target=\"_blank\">".$ftp_uplink." </a>");
					fwrite($fr, " </div></div>");
					fclose($fr);
				}
	}
echo $not_done ? "" : '<p><center><b><a href="javascript:window.close();">'.lang(77).'</a></b></center>';
?>
</body>
</html>
<?php
if (isset($_GET['auul'])) {
?><script type='text/javascript' language='javascript'>parent.nextlink<?php echo $_GET['auul']; ?>();</script><?php
	// Write links to a file
	$file = $options['download_dir']."myuploads.txt";	// Obviously it was a mistake not making it a variable earlier
	$fh = fopen($file, 'a');
	if (!$_GET['save_style'] && $_GET['save_style'] !== lang(51)) {
		$dash = "";
		for ($i=0;$i<=80;$i++) $dash.="=";
		fwrite($fh,$lname."\r\n".$dash."\r\n".$download_link."\r\n\r\n");
	} else {
		$save_style = base64_decode($_GET['save_style']);
		$save_style = str_replace('{link}',$download_link,$save_style);
		$save_style = str_replace('{name}',$lname,$save_style);
		fwrite($fh,$save_style."\r\n");
	}
	fclose($fh);
}
?>