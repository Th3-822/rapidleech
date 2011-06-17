<?php

####### Default Settings. ###########
$Adult = false; // Adult Content?
$Thumbsize = 180; // Thumbnail Size (120, 180 or 300) px
$force = false; // Force this settings for upload. (For auul use)
##############################

$not_done=true;
$continue_up=false;
$tsizes = array(120, 180, 300);
if ($force){
	$_REQUEST['up_rB'] = $Adult ? 'NSFW' : 'SFW';
	$_REQUEST['up_thumbsize'] = $Thumbsize;
	$_REQUEST['action'] = "FORM";
	echo "<b><center>Using Default Settings.</center></b>\n";
}
if ($_REQUEST['action'] == "FORM")
	$continue_up=true;
else{
?>
<table border='0' style="width:270px;" cellspacing='0' align='center'>
<form method='POST'>
<input type='hidden' name='action' value='FORM' />
<tr><td colspan='2' align='center'>Upload options</td></tr>
<tr><td style="white-space:nowrap;" align='center'><br />Content type:&nbsp;&nbsp;<input type='radio' name='up_rB' value='SFW' checked='checked'>&nbsp;Family safe&nbsp;&nbsp;<input type='radio' name='up_rB' value='NSFW'>&nbsp;<span style="color:red;"><b>Adult content</b></span></td></tr>
<tr><td colspan='2' align='center'><br />Thumbnail size:&nbsp;&nbsp;<select name="up_thumbsize" style="height:20px;width:60px">
<?php foreach($tsizes as $v) echo "<option value='$v'>$v px</option>"; ?>
</select></td></tr>
<tr><td colspan='2' align='center'><br /><input type='submit' value='Upload' /></td></tr>
<tr><td colspan='2' align='center'><small>*You can set it as default in <b><?php echo $page_upload["imagehaven.net"]; ?></b></small></td></tr>
</table>
</form>
<?php
}

if ($continue_up) {
	$not_done=false;
?><table style="width:600px;margin:auto;">
<tr><td align="center">
<div id='info' style="width:100%;text-align:center;">Retrive upload ID</div>
<?php 
	$page = geturl("imagehaven.net", 80, "/");is_page($page);
	$cookie = GetCookies($page);
	$post = array();
	$post['Filename'] = $lname;
	$post['PHPSESSID'] = cut_str($page, '"PHPSESSID" : "', '"');
	if (in_array($_REQUEST['up_thumbsize'], $tsizes)) $post['thumbsize'] = $_REQUEST['up_thumbsize'];
	else $post['thumbsize'] = 180;
	$post['Upload'] = 'Submit Query';

	if ($_REQUEST['up_rB'] == 'NSFW') $up_loc = 'uploadadult.php';
	else $up_loc = 'upload.php';
	$up_loc = "http://imagehaven.net/$up_loc";
?><script type='text/javascript'>document.getElementById('info').style.display='none';</script>
<?php
	$url = parse_url($up_loc);
	$upfiles = upfile($url["host"],$url["port"] ? $url["port"] : 80, $url["path"].($url["query"] ? "?".$url["query"] : ""), 0, $cookie, $post, $lfile, $lname, "Filedata", '', 0, 0, "Shockwave Flash");
?><script type='text/javascript'>document.getElementById('progressblock').style.display='none';</script>
<?php
	is_page($upfiles);

	if(!preg_match('/((?:img|a)\d+\.imagehaven\.net)\|([^\r|\n]+)/i', $upfiles, $ul)) html_error("Download link not found.", 0);
	echo "\n<table width='100%' border='0'>\n<tr><td width='100' nowrap='nowrap' align='right'>Thumbnail Link:<td width='80%'><input value='http://{$ul[1]}/img/thumbs/{$ul[2]}' class='upstyles-dllink' readonly='readonly' /></tr>\n</table>\n";
	$download_link = "http://{$ul[1]}/img.php?id={$ul[2]}";
}

//[15-6-2011]  Written by Th3-822.

?>