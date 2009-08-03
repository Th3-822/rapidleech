<?
####### Free Account Info. ###########
$fh_email = "";

##############################

$not_done=true;
$continue_up=false;
if ($fh_email){
	$_REQUEST['email'] = $fh_email;
	$_REQUEST['action'] = "FORM";
	echo "<b><center>Use Default E-mail.</center></b>\n";
}
if ($_REQUEST['action'] == "FORM")
    $continue_up=true;
else{
?>
<table border=0 style="width:270px;" cellspacing=0 align=center>
<form method=post>
<input type=hidden name=action value='FORM' />
<tr><td nowrap>&nbsp;E-mail*<td>&nbsp;<input type=text name=email value='' style="width:160px;" />&nbsp;</tr>
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
			$page = geturl("www.filehosting.org", 80, "/", 0, 0, 0, 0, $_GET["proxy"], $pauth);
			is_page($page);
			preg_match_all('/Set-Cookie: (.*);/U',$page,$temp);
			$cookie = $temp[1];
			$cookies = implode('; ',$cookie);
												
?>
<script>document.getElementById('info').style.display='none';</script>
<table width=600 align=center>
</td></tr>
<tr><td align=center>
<?php

			$post["uploader_email"]= $_REQUEST['email'];
			$post["submit"]="upload now";
			$u1='http://www.filehosting.org/';
			$url = parse_url($u1); 
			$upfiles = upfile($url["host"],$url["port"] ? $url["port"] : 80, $url["path"].($url["query"] ? "?".$url["query"] : ""), $u1, $cookies, $post, $lfile, $lname, "upload_file");
?>
<script>document.getElementById('progressblock').style.display='none';</script>
<div id=final width=100% align=center>Get final code</div>
<?php		
			is_page($upfiles);
			
			$page = geturl("www.filehosting.org", 80, "/", $u1, $cookies, 0, 0, $_GET["proxy"], $pauth);
			is_page($page);
			if (stristr($page,"A link to the file and a download link where sent to your e-mail address")){
			$download_link='A link to the file and a download link where sent to your e-mail address';}
			else {html_error("Error the file was probably sent to ".$_REQUEST['email']);}
			
	}
// Made by Baking 01/08/2009 15:22
?>