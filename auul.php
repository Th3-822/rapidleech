<?php

require_once('rl_init.php');

if ($options['auto_upload_disable']) {
	require_once('deny.php');
	exit();
}
error_reporting(0);
ignore_user_abort(true);

login_check();

$id = 1;
require_once(HOST_DIR.'download/hosts.php');
require_once(CLASS_DIR.'http.php');
include(TEMPLATE_DIR.'header.php');
?>
<br />
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
			echo lang(46);
			exit;
		}
		$save_style = "";
		if (!empty($_POST['save_style']) && $_POST['save_style'] != lang(51)) {
			$save_style = '&save_style='.urlencode(base64_encode($_POST['save_style']));
		}
		$start_link = "upload.php";
		$i = 0;
		foreach ($uploads as $upload) {
			$getlinks[$i][] = "?uploaded=".$upload['host']."&filename=".urlencode(base64_encode($upload['file'])).$save_style;
			$i++;
			if ($i>$openwin) $i = 0;
		}
?>
<script type="text/javascript">
/* <![CDATA[ */
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
/* ]]> */
</script>
<?php
	for ($i=0;$i<=$openwin;$i++) {
		if (( $i+1 )% 2) echo "<br />";
?>
<iframe width="49%" height="300" src="" name="idownload<?php echo $i; ?>" id="idownload<?php echo $i; ?>" style="float:left; border:1px solid;"><?php echo lang(30); ?></iframe>
<?php
	}
?>
<script type="text/javascript">startauto();</script><br />
<a href="files/myuploads.txt">myuploads.txt</a>
<?php

	} else {
?>
<?php 
$options['show_all'] = true;
$_COOKIE["showAll"] = 1;
_create_list();
require_once("classes/options.php");
unset($Path);
?>
<form name="flist" method="post" action="auul.php?action=upload">
<p><b><?php echo lang(47); ?></b></p>
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
		echo '<span class="warning"><b>'.lang(48).'</b></span>';
	} else {
		sort($upload_services); reset($upload_services);
		$cc=0;
		foreach($upload_services as $upl)
		{
?>
	<tr>
		<td><input type="checkbox" name="hosts[]" value="<?php echo $upl; ?>" /></td>
		<td><?php echo str_replace("_"," ",$upl)." (".($max_file_size[$upl]==false ? "Unlim" : $max_file_size[$upl]."Mb").")"; ?></td>
	</tr>
<?php
		}
	}
?>
</table>
</div><br />
<hr /><br />
<input type="submit" name="submit" value="Upload" /> <?php echo lang(49); ?>: <input type="text" size="2" name="windows" value="4" /><br />
<?php echo lang(50); ?>: <input type="text" size="50" name="save_style" placeholder="{name}: {link} or {link}" /><br />
<a href="javascript:setCheckboxes(1);" class="chkmenu"><?php echo lang(52); ?></a> |
<a href="javascript:setCheckboxes(0);" class="chkmenu"><?php echo lang(53); ?></a> |
<a href="javascript:setCheckboxes(2);" class="chkmenu"><?php echo lang(54); ?></a> |
<a href="files/myuploads.txt" class="chkmenu">myuploads.txt</a>
<div style="overflow:auto; height:400px; width: 700px;">
<table cellpadding="3" cellspacing="1" width="100%" class="filelist" id="table_filelist_au">
	<tr class="flisttblhdr" valign="bottom">
		<th class="sorttable_checkbox">&nbsp;</th>
		<th class="sorttable_alpha"><?php echo lang(55); ?></th>
		<th><?php echo lang(56); ?></th>
	</tr>
<?php
if (!$list) {
?>
	<center><?php echo lang(57); ?></center>
<?php
} else {
?>
<?php
	foreach($list as $key => $file) {
		if(file_exists($file["name"])) {
?>
	<tr>
		<td><input type="checkbox" name="files[]" value="<?php echo base64_encode(basename($file["name"])); ?>" /></td>
		<td><?php echo basename($file["name"]); ?></td>
		<td><?php echo $file["size"]; ?></td>
	</tr>
<?php
		}
	}
?>
</table>
<?php
	if ($options['flist_sort']) {
		echo '<script type="text/javascript">sorttable.makeSortable(document.getElementById("table_filelist_au"));</script>';
	}
?>
<br />
<?php echo lang(58); ?><br />
<ol>
	<li>{link} : <?php echo lang(59); ?></li>
	<li>{name} : <?php echo lang(60); ?></li>
	<li><?php echo lang(51); ?> : <?php echo lang(61); ?></li>
</ol><br />
<?php echo lang(62); ?>
</div>
</form>
<?php
}
}

?>
</center>
<?php include(TEMPLATE_DIR.'footer.php'); ?>