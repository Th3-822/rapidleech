<?
####### Default Info. ###########
$novaup_desc = "";
$novaup_cat = ""; // 0 = Unknown *** 1 = Software *** 3 = Documents *** 4 = Videos *** 6 Games
##############################

$not_done=true;
$continue_up=false;
if ($novaup_desc & $novaup_cat){
	$_REQUEST['desc'] = $novaup_desc;
	$_REQUEST['cat'] = $novaup_cat;
	$_REQUEST['action'] = "FORM";
	echo "<b><center>Use Default login/pass.</center></b>\n";
}
if ($_REQUEST['action'] == "FORM")
    $continue_up=true;
else{
?>
<table border=0 style="width:270px;" cellspacing=0 align=center>
<form method=post>
<input type=hidden name=action value='FORM' />
<tr><td nowrap>&nbsp;Description<td>&nbsp;<input type=text name=desc value='' style="width:160px;" />&nbsp;</tr>
<tr><td nowrap>&nbsp;Categories<td>&nbsp;<select name="cat" id="cat">
  <option value="0">Unknown</option>
  <option value="1">Software</option>
  <option value="3">Documents</option>
  <option value="4">Videos</option>
  <option value="6">Games</option>
</select></tr>
<tr><td colspan=2 align=center><input type=submit value='Upload' /></tr>
</table>
</form>
<?php
}

if ($continue_up)
	{
		$not_done=false;
?>
<table width=600 align=center>
</td></tr>
<tr><td align=center>
<script>document.getElementById('login').style.display='none';</script>
<div id=info width=100% align=center>Retrive upload ID</div>
<?php

		$rand = mt_rand(1000000000000, 1999999999999);
		$page = geturl("www.novaup.com", 80, "/", "", 0, 0, 0, "");
		$page2 = geturl("u.novamov.com", 80, "/upload/ubr_link_upload.php?rnd_id=$rand", "", 0, 0, 0, "");
		$upid = cut_str($page2,'startUpload("','"');
		$cookies = "user=0";
?>
<script>document.getElementById('info').style.display='none';</script>
<table width=600 align=center>
</td></tr>
<tr><td align=center>
<?php

			$post["desc"]= $_REQUEST['desc'];
			$post["cat"]= $_REQUEST['cat'];
			
			$url = parse_url("http://u.novamov.com/cgi-bin/ubr_upload.pl?upload_id=$upid");
			$upfiles = upfile($url["host"],$url["port"] ? $url["port"] : 80, $url["path"].($url["query"] ? "?".$url["query"] : ""), 'http://u.novamov.com/', 0, $post, $lfile, $lname, "upfile_0");
?>
<script>document.getElementById('progressblock').style.display='none';</script>
<div id=final width=100% align=center>Get final code</div>
<?php		
			is_page($upfiles);
			$locat=trim(cut_str($upfiles,'Location: ',"\n"));
			$Url=parse_url($locat);
			$page = geturl($Url["host"],  80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 'http://u.novamov.com/', $cookies, 0, 0, $_GET["proxy"],$pauth);
			$ddl=cut_str($page,'finished.php?q=','&d=');
			$del=cut_str($page,'&d=',"\n");
			$download_link=$ddl;
			$delete_link=$del;
}


// Made by Baking 16/09/2009 13:59
?>