<?php
$id=1;
// A work of Chaza and TheOnly92!
// Presents auto-upload script!
/*	function callback($buffer)
			{
				$rndvar = rand(1, 10000);
  				// replace all the apples with oranges
  				$rep1 = str_replace("received", "received".$rndvar, $buffer);
  				$rep2 = str_replace("percent", "percent".$rndvar, $rep1);
  				$rep3 = str_replace("speed", "speed".$rndvar, $rep2);
  				return (str_replace("progress", "progress".$rndvar, $rep3));

			}*/
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
require_once("classes/other.php");
require_once(HOST_DIR."hosts.php");
require_once(CLASS_DIR."http.php");

// If you set password for your rapidleech site, this asks for the password
if ($login === true && (!isset($_SERVER['PHP_AUTH_USER']) || ($loggeduser = logged_user($users)) === false))
	{
		header("WWW-Authenticate: Basic realm=\"RAPIDLEECH PLUGMOD\"");
		header("HTTP/1.0 401 Unauthorized");
		exit("<html>$nn<head>$nn<title>RAPIDLEECH PLUGMOD</title>$nn<meta http-equiv=\"Content-Type\" content=\"text/html; charset=windows-1251\">$nn</head>$nn<body>$nn<h1>$nn<center>$nn<a href=http://www.rapidleech.com>RapidLeech</a>: Access Denied - Wrong Username or Password$nn</center>$nn</h1>$nn</body>$nn</html>");
	}
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<script type="text/javascript" src="classes/js.php"></script>
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
			switch ($i) {
				case 0:
					$getlinks0[] = "?uploaded=".$upload['host']."&filename=".base64_encode($upload['file']);
					break;
				case 1:
					$getlinks1[] = "?uploaded=".$upload['host']."&filename=".base64_encode($upload['file']);
					break;
				case 2:
					$getlinks2[] = "?uploaded=".$upload['host']."&filename=".base64_encode($upload['file']);
					break;
				case 3:
					$getlinks3[] = "?uploaded=".$upload['host']."&filename=".base64_encode($upload['file']);
					break;
			}
			$i++;
			if ($i>3) $i = 0;
		}
		/*$winup0 = ceil(count($uploads) / 4);
		$winup1 = floor(count($uploads) / 4);
		$winup2 = floor(count($uploads) / 4);
		$winup3 = floor(count($uploads) / 4);
		for ($i=0;$i<=$winup0;$i++) {
			$getlinks0[] = "?uploaded=".$uploads[$i]['host']."&filename=".base64_encode($uploads[$i]['file']);
		}
		for ($i=$winup0+1;$i<=$winup0+$winup1;$i++) {
			$getlinks1[] = "?uploaded=".$uploads[$i]['host']."&filename=".base64_encode($uploads[$i]['file']);
		}
		for ($i=$winup0+$winup1+1;$i<=$winup0+$winup1+$winup2;$i++) {
			$getlinks2[] = "?uploaded=".$uploads[$i]['host']."&filename=".base64_encode($uploads[$i]['file']);
		}
		for ($i=$winup0+$winup1+$winup2+1;$i<=$winup0+$winup1+$winup2+$winup3;$i++) {
			$getlinks3[] = "?uploaded=".$uploads[$i]['host']."&filename=".base64_encode($uploads[$i]['file']);
		}
		foreach ($uploads as $file=>$host) {
			$getlinks[] = "?uploaded=".$host."&filename=".base64_encode($file);
		}*/
?>
<script language="javascript">

	var current_dlink0=-1;
	var current_dlink1=-1;
	var current_dlink2=-1;
	var current_dlink3=-1;
	var links0 = new Array();
	var links1 = new Array();
	var links2 = new Array();
	var links3 = new Array();
	var start_link='<?php echo $start_link; ?>';
	var usingwin = 0;

	function startauto()
		{
			current_dlink0=-1;
			//document.getElementById('auto').style.display='none';
			nextlink0();
			if (links1.length > 0) {
				current_dlink1=-1;
				//document.getElementById('auto').style.display='none';
				nextlink1();
			} else {
					document.getElementById('idownload1').style.display = 'none';
				}
			if (links2.length > 0) {
				current_dlink2=-1;
				//document.getElementById('auto').style.display='none';
				nextlink2();
			} else {
					document.getElementById('idownload2').style.display = 'none';
				}
			if (links3.length > 0) {
				current_dlink3=-1;
				//document.getElementById('auto').style.display='none';
				nextlink3();
			} else {
					document.getElementById('idownload3').style.display = 'none';
				}
		}

	function nextlink0()
		{
			current_dlink0++;

			if (current_dlink0 < links0.length)
				{
					//document.getElementById('status'+current_dlink).innerHTML='Started';
					opennewwindow0(current_dlink0);
				} else {
					document.getElementById('idownload0').style.display = 'none';
				}
		}

	function nextlink1()
		{
			current_dlink1++;

			if (current_dlink1 < links1.length)
				{
					//document.getElementById('status'+current_dlink).innerHTML='Started';
					opennewwindow1(current_dlink1);
				} else {
					document.getElementById('idownload1').style.display = 'none';
				}
		}

	function nextlink2()
		{
			current_dlink2++;

			if (current_dlink2 < links2.length)
				{
					//document.getElementById('status'+current_dlink).innerHTML='Started';
					opennewwindow2(current_dlink2);
				} else {
					document.getElementById('idownload2').style.display = 'none';
				}
		}

	function nextlink3()
		{
			current_dlink3++;

			if (current_dlink3 < links3.length)
				{
					//document.getElementById('status'+current_dlink).innerHTML='Started';
					opennewwindow3(current_dlink3);
				} else {
					document.getElementById('idownload3').style.display = 'none';
				}
		}

	function opennewwindow0(id)
		{
			window.frames["idownload0"].location = start_link+links0[id]+'&auul=0';
		}

	function opennewwindow1(id)
		{
			window.frames["idownload1"].location = start_link+links1[id]+'&auul=1';
		}

	function opennewwindow2(id)
		{
			window.frames["idownload2"].location = start_link+links2[id]+'&auul=2';
		}
	function opennewwindow3(id)
		{
			window.frames["idownload3"].location = start_link+links3[id]+'&auul=3';
		}
<?php
		for ($i=0; $i<count($getlinks0); $i++) {
			echo "\tlinks0[".$i."]='".$getlinks0[$i]."';\n";
		}
		for ($i=0; $i<count($getlinks1); $i++) {
			echo "\tlinks1[".$i."]='".$getlinks1[$i]."';\n";
		}
		for ($i=0; $i<count($getlinks2); $i++) {
			echo "\tlinks2[".$i."]='".$getlinks2[$i]."';\n";
		}
		for ($i=0; $i<count($getlinks3); $i++) {
			echo "\tlinks3[".$i."]='".$getlinks3[$i]."';\n";
		}
?>
</script>
<iframe width="49%" height="300" src="" name="idownload0" id="idownload0" border="1" style="float: left;">Frames not supported, update your browser</iframe>
<iframe width="49%" height="300" src="" name="idownload1" id="idownload1" border="1" style="float: right;">Frames not supported, update your browser</iframe>
<br />
<iframe width="49%" height="300" src="" name="idownload2" id="idownload2" border="1" style="float: left;">Frames not supported, update your browser</iframe>
<iframe width="49%" height="300" src="" name="idownload3" id="idownload3" border="1" style="float: right;">Frames not supported, update your browser</iframe>
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
<input type=submit name="submit" value="Upload" /><br />
<a href="javascript:setCheckboxes(1);" style="color: #99C9E6;">Check All</a> |
<a href="javascript:setCheckboxes(0);" style="color: #99C9E6;">Un-Check All</a> |
<a href="javascript:setCheckboxes(2);" style="color: #99C9E6;">Invert Selection</a> |
<a href="files/myuploads.txt" style="color: #99C9E6">myuploads.txt</a>
<div style="overflow:auto; height:400px; width: 700px;">
<table cellpadding="3" cellspacing="1" width="100%" class="filelist">
	<tr bgcolor="#4B433B" valign="bottom" align="center" style="color: white;">
		<th></th>
		<th>Name</th>
		<th>Size</th>
	</tr>
<?php
if (!$list) {
?>
	<tr><td colspan="3"><center>No files found</center></td></tr>
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