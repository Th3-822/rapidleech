<?php
define('RAPIDLEECH', 'yes');
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
define('IMAGE_DIR', TEMPLATE_DIR . 'images/');

login_check();

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

if (isset ( $_REQUEST ["useuproxy"] ) && (! $_REQUEST ["uproxy"] || ! strstr ( $_REQUEST ["uproxy"], ":" )))
{
	html_error ( lang(324) );
}
else
{
	$proxy = $_REQUEST ["uproxy"];
}

if ($_REQUEST ["upauth"])
{
	$pauth = $_REQUEST ["upauth"];
}
else
{
	$pauth = ($_REQUEST ["uproxyuser"] && $_REQUEST ["uproxypass"]) ? base64_encode ( $_REQUEST ["uproxyuser"] . ":" . $_REQUEST ["uproxypass"] ) : "";
}

$fsize = getSize($lfile);

echo '<script type="text/javascript">var orlink="' . basename($_REQUEST['filename']) . ' to ' . $_REQUEST['uploaded'] . '";</script>';

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
			echo ($download_link ? '<tr><td width="100" nowrap="nowrap" align="right"><b>'.lang(68).':</b><td width="80%"><input value="'.$download_link.'" class="upstyles-dllink" readonly="readonly" /></tr>' : '');
			echo ($delete_link ? '<tr><td width="100" nowrap="nowrap" align="right">'.lang(69).':<td width="80%"><input value="'.$delete_link.'" class="upstyles-dellink" readonly="readonly" /></tr>' : '');
			echo ($stat_link ? '<tr><td width="100" nowrap="nowrap" align="right">'.lang(70).':<td width="80%"><input value="'.$stat_link.'" class="upstyles-statlink" readonly="readonly" /></tr>' : '');
			echo ($adm_link ? '<tr><td width="100" nowrap="nowrap" align="right">'.lang(71).':<td width="80%"><input value="'.$adm_link.'" class="upstyles-admlink" readonly="readonly" /></tr>': '');
			echo ($user_id ? '<tr><td width="100" nowrap="nowrap" align="right">'.lang(72).':<td width="80%"><input value="'.$user_id.'" class="upstyles-userid" readonly="readonly" /></tr>': '');
			echo ($ftp_uplink ? '<tr><td width="100" nowrap="nowrap" align="right">'.lang(73).':<td width="80%"><input value="'.$ftp_uplink.'" class="upstyles-ftpuplink" readonly="readonly" /></tr>': '');
			echo ($access_pass ? '<tr><td width="100" nowrap="nowrap" align="right">'.lang(74).':<td width="80%"><input value="'.$access_pass.'" class="upstyles-accesspass" readonly="readonly" /></tr>': '');
			/*echo ($protect ? '<tr><td width="100" nowrap="nowrap" align="right">Protect link:<td width="80%"><input value="'.$protect.'" style="width:470px; border: 1px solid #55AAFF; background-color: #FFFFFF; padding:3px" readonly /></tr>': '');*/
			echo "</table>\n";
			
			if(!file_exists(trim($lfile).".upload.html") && !isset($_GET['auul']) && !$options['upload_html_disable'])
			  {
				$html_header = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
								<html xmlns="http://www.w3.org/1999/xhtml">
								<head>
								<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
								<title>'.lang(75).'</title>
								<style type="text/css">
body {
	font-family: tahoma, arial, "times New Roman", georgia, verdana, sans-serif;
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
	font-family: tahoma, arial, "times New Roman", georgia, verdana, sans-serif;
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
	font-family: Georgia, "Times New Roman", Times, serif;
}
								</style>
								</head>
								<body>
								';
			write_file(trim($lfile).".upload.html", $html_header.sprintf(lang(76),$lname,bytesToKbOrMb($fsize)), 0);
			if (!$options['upload_html_disable']) {
				$html_content = '<div class="host"><div class="title"><strong>'.$_REQUEST['uploaded'].'</strong> - <span class="bluefont">'.date("Y-m-d H:i:s").'</span></div>
				<div class="links">'.
				($download_link ? '<strong>'.lang(68).': <a href="'.$download_link.'" target="_blank">'.$download_link.' </a></strong>' : '').
				($delete_link ? '<br />'.lang(69).': <a href="'.$delete_link.'" target="_blank">'.$delete_link.' </a>' : '').
				($stat_link ? '<br />'.lang(70).': <a href="'.$stat_link.'" target="_blank">'.$stat_link.' </a>' : '').
				($adm_link ? '<br />'.lang(71).': <a href="'.$adm_link.'" target="_blank">'.$adm_link.' </a>' : '').
				($user_id ? '<br />'.lang(72).': <a href="'.$user_id.'" target="_blank">'.$user_id.' </a>' : '').
				($access_pass ? '<br />'.lang(74).': <a href="'.$access_pass.'" target="_blank">'.$access_pass.' </a>' : '').
				($ftp_uplink ? '<br />'.lang(73).': <a href="'.$ftp_uplink.'" target="_blank">'.$ftp_uplink.' </a>' : '').
				'</div></div>';
				write_file(trim($lfile).".upload.html", $html_content, 0);
			}
		}
	}
echo $not_done ? "" : '<p><center><b><a href="javascript:window.close();">'.lang(77).'</a></b></center>';
?>
</body>
</html>
<?php
if (isset($_GET['auul'])) {
?><script type="text/javascript">parent.nextlink<?php echo $_GET['auul']; ?>();</script><?php
	// Write links to a file
	$file = $options['download_dir']."myuploads.txt";	// Obviously it was a mistake not making it a variable earlier
	if (!$options['myuploads_disable']) {
		if (!$_GET['save_style'] && $_GET['save_style'] !== lang(51)) {
			$dash = "";
			for ($i=0;$i<=80;$i++) $dash.="=";
			write_file($file, $lname."\r\n".$dash."\r\n".$download_link."\r\n\r\n", 0);
		} else {
			$save_style = base64_decode($_GET['save_style']);
			$save_style = str_replace('{link}',$download_link,$save_style);
			$save_style = str_replace('{name}',$lname,$save_style);
			write_file($file, $save_style."\r\n", 0);
		}
	}
}
?>