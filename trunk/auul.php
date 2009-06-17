<?php
$id=1;
// A work of Chaza and TheOnly92!
// Presents auto-upload script!
// We define some constants here, essential for some parts in rapidleech
define('RAPIDLEECH', 'yes');
define('HOST_DIR', 'hosts/');
define('IMAGE_DIR', 'images/');
define('CLASS_DIR', 'classes/');
define('CONFIG_DIR', 'configs/');
// Some configuration
error_reporting(0);	// This sets error reporting to none, which means no errors will be reported
//ini_set('display_errors', 1);	// This sets error reporting to all, all errors will be reported
set_time_limit(0);	// Removes the time limit, so it can upload as many as possible
ini_alter("memory_limit", "1024M");	// Set memory limit, in case it runs out when processing large files
ob_end_clean();	// Cleans any previous outputs
ob_implicit_flush(TRUE);	// Sets so that we can update the page without refreshing
ignore_user_abort(1);	// Continue executing the script even if the page was stopped or closed
clearstatcache();	// Clear caches created by PHP
require_once("configs/config.php");	// Reads the configuration file, so we can pick up any accounts needed to use
define('DOWNLOAD_DIR', (substr($download_dir, 0, 6) == "ftp://" ? '' : $download_dir));	// Set the download directory constant
// Include other useful functions
require_once('classes/other.php');
require_once(HOST_DIR.'download/hosts.php');
require_once(CLASS_DIR.'http.php');

// If you set password for your rapidleech site, this asks for the password
if ($login === true && (!isset($_SERVER['PHP_AUTH_USER']) || ($loggeduser = logged_user($users)) === false)) {
	header("WWW-Authenticate: Basic realm=\"RAPIDLEECH PLUGMOD\"");
	header("HTTP/1.0 401 Unauthorized");
	exit("<html>$nn<head>$nn<title>RAPIDLEECH PLUGMOD</title>$nn<meta http-equiv=\"Content-Type\" content=\"text/html; charset=windows-1251\">$nn</head>$nn<body>$nn<h1>$nn<center>$nn<a href=http://www.rapidleech.com>RapidLeech</a>: Access Denied - Wrong Username or Password$nn</center>$nn</h1>$nn</body>$nn</html>");
}
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>RAPIDLEECH PLUGMOD - Auto Upload</title>
<style type="text/css">
<!--
@import url("images/rl_style_pm.css");
-->
.container td {
	background-color:#001825;
	padding:2px;
}
</style>
	<style type="text/css">
<!--
@import url("images/rl_style_pm.css");
-->
</style>
<script language="JavaScript">
function setCheckboxes(act)
  {
  elts =  document.forms["flist"].elements["files[]"];
  var elts_cnt  = (typeof(elts.length) != 'undefined') ? elts.length : 0;
  if (elts_cnt)
    {
    for (var i = 0; i < elts_cnt; i++)
      {
      elts[i].checked = (act == 1 || act == 0) ? act : elts[i].checked ? 0 : 1;
      }
    }
  }
</script>

<script type="text/javascript" src="classes/js.php"></script>
</head>
<body>
<center><img src="images/logo_pm.gif" alt="RAPIDLEECH PLUGMOD"></center><br><br>
<center>
<?php
	// If the user submit to upload, go into upload page
	if ($_GET['action'] == 'upload') {
		// Define another constant
		if(!defined('CRLF')) define('CRLF',"\r\n");
		// The new line variable
		$nn = "\r\n";
		// Initialize some variables here
		$uploads = array();
		$total = 0;
		$hostss = array();
		// Get number of windows to be opened
		$openwin = (int) $_POST['windows'];
		if ($openwin <= 0) $openwin = 4;
		$openwin--;
		// Sort the upload hosts and files
		foreach ($_POST['files'] as $file) {
			foreach ($_POST['hosts'] as $host) {
				$hostss[] = $host;
				$uploads[] = array('host' => $host,
					'file' => DOWNLOAD_DIR.base64_decode($file));
				$total++;
			}
		}
		// Clear out duplicate hosts
		$hostss = array_unique($hostss);
		// If there aren't anything
		if (count($uploads) == 0) {
			echo "No files or hosts selected for upload";
			exit;
		}
		$start_link = "upload.php";
		$i = 0;
		foreach ($uploads as $upload) {
			$getlinks[$i][] = "?uploaded=".$upload['host']."&filename=".base64_encode($upload['file']);
			$i++;
			if ($i>$openwin) $i = 0;
		}
?>
<script language="javascript">

<?php
	for ($i=0;$i<=$openwin;$i++) {
?>
	var current_dlink<?php echo $i; ?>=-1;
	var links<?php echo $i; ?> = new Array();
<?php
	}
?>
	var start_link='<?php echo $start_link; ?>';
	var usingwin = 0;

	function startauto()
		{
			current_dlink0=-1;
			//document.getElementById('auto').style.display='none';
			nextlink0();
<?php
	for ($i=1;$i<=$openwin;$i++) {
?>
			if (links<?php echo $i; ?>.length > 0) {
				current_dlink<?php echo $i; ?>=-1;
				nextlink<?php echo $i; ?>();
			} else {
				document.getElementById('idownload<?php echo $i; ?>').style.display = 'none';
			}
<?php
	}
?>
		}

<?php
	for ($i=0;$i<=$openwin;$i++) {
?>
	function nextlink<?php echo $i; ?>() {
		current_dlink<?php echo $i; ?>++;
		if (current_dlink<?php echo $i; ?> < links<?php echo $i; ?>.length) {
			opennewwindow<?php echo $i; ?>(current_dlink<?php echo $i; ?>);
		} else {
			document.getElementById('idownload<?php echo $i; ?>').style.display = 'none';
		}
	}
	
	function opennewwindow<?php echo $i; ?>(id) {
		window.frames["idownload<?php echo $i; ?>"].location = start_link+links<?php echo $i; ?>[id]+'&auul=<?php echo $i; ?>';
	}
<?php
	}
		for ($j=0;$j<=$openwin;$j++) {
			foreach ($getlinks[$j] as $i=>$link) {
				echo "\tlinks{$j}[".$i."]='".$link."';\n";
			}
		}
?>
	
</script>
<?php
	for ($i=0;$i<=$openwin;$i++) {
		if (( $i+1 )% 2) echo "<br />";
?>
<iframe width="49%" height="300" src="" name="idownload<?php echo $i; ?>" id="idownload<?php echo $i; ?>" border="1" style="float: left;">Frames not supported, update your browser</iframe>
<?php
	}
?>
<script type="text/javascript">startauto();</script><br />
<a href="files/myuploads.txt">myuploads.txt</a>
<?php

	} else {
?>
<?php 
$show_all = true;
$_COOKIE["showAll"] = 1;
_create_list();
require_once("classes/options.php");
unset($Path);
?>
<form name="flist" method="post" action="auul.php?action=upload">
<p><b>Select Hosts to Upload</b></p>
<div style="overflow:auto; height:200px; width: 300px;">
<table>
<?php
	$d = opendir(HOST_DIR."upload/");
	while (false !== ($modules = readdir($d)))
		{
			if($modules!="." && $modules!="..")
				{
					if(is_file(HOST_DIR."upload/".$modules))
						{
							if (strpos($modules,".index.php")) include_once(HOST_DIR."upload/".$modules);
						}
				}
		}
	if (empty($upload_services)) 
	{
		echo "<span style='color:#FF6600'><b>No Supported Upload Services!</b></span>";
	} else {
		sort($upload_services); reset($upload_services);
		$cc=0;
		foreach($upload_services as $upl)
		{
?>
	<tr>
		<td><input type=checkbox name="hosts[]" value="<?php echo $upl; ?>"></td>
		<td><?php echo str_replace("_"," ",$upl)." (".($max_file_size[$upl]==false ? "Unlim" : $max_file_size[$upl]."Mb").")"; ?></td>
	</tr>
<?php
		}
	}
?>
</table>
</div><br />
<hr /><br />
<input type=submit name="submit" value="Upload" /> Upload windows: <input type="text" size="2" name="windows" value="4" /><br />
<a href="javascript:setCheckboxes(1);" style="color: #99C9E6;">Check All</a> |
<a href="javascript:setCheckboxes(0);" style="color: #99C9E6;">Un-Check All</a> |
<a href="javascript:setCheckboxes(2);" style="color: #99C9E6;">Invert Selection</a> |
<a href="files/myuploads.txt" style="color: #99C9E6">myuploads.txt</a>
<div style="overflow:auto; height:400px; width: 700px; border">
<table cellpadding="3" cellspacing="1" width="100%" class="filelist">
	<tr bgcolor="#4B433B" valign="bottom" align="center" style="color: white;">
		<th></th>
		<th>Name</th>
		<th>Size</th>
	</tr>
<?php
if (!$list) {
?>
	<center>No files found</center>
<?php
} else {
?>
<?php
	foreach($list as $key => $file) {
		if(file_exists($file["name"])) {
?>
	<tr>
		<td><input type=checkbox name="files[]" value="<?php echo base64_encode(basename($file["name"])); ?>"></td>
		<td><?php echo basename($file["name"]); ?></td>
		<td><?php echo $file["size"]; ?></td>
	</tr>
<?php
		}
	}
?>
</table>
</div>
</form>
<?php
}
}

?>
</center>
</body>
</html>