<?
####### Free Account Info. ###########
$sb_login = "";
$sb_pass = "";
##############################

$not_done=true;
$continue_up=false;
if ($sb_login & $sb_pass){
	$_REQUEST['email'] = $sb_login;
	$_REQUEST['password'] = $sb_pass;
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
<tr><td nowrap>&nbsp;email*<td>&nbsp;<input type=text name=email value='' style="width:160px;" />&nbsp;</tr>
<tr><td nowrap>&nbsp;Password*<td>&nbsp;<input type=password name=password value='' style="width:160px;" />&nbsp;</tr>
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
<div id=login width=100% align=center>Login to sharebase.to</div>
<?php
			$post['lg_mail'] = $_REQUEST['email'];
			$post['lg_pass'] = $_REQUEST['password'];
			$post['m_login'] = 'Login' ;
			$page = geturl("sharebase.to", 80, "/mlogin", "http://sharebase.to/", 0, $post, 0, $_GET["proxy"], $pauth);

			is_page($page);
			is_notpresent($page, 'maccount', 'Error logging in - are your logins correct? First');
			
			$cok0 = 'PHPSESSID='.cut_str($page,'PHPSESSID=',';').'; ';
			$cok1 = 'memm='.cut_str($page,'memm=',';').'; ';
			$cok2 = 'memp='.cut_str($page,'memp=',';');
			$cookies ="$cok1$cok2";

			$Url=parse_url("http://sharebase.to/maccount");
			$page = geturl($Url["host"], defport($Url), $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, $cookies, 0, 0, $_GET["proxy"],$pauth);

			is_page($page);
			is_notpresent($page, 'Save Now !', 'Error logging in - are your logins correct? Second');
			
			
?>
<script>document.getElementById('login').style.display='none';</script>
<div id=info width=100% align=center>Retrive upload ID</div>
<?php

	$ref='http://sharebase.to/upload';
	$Url=parse_url($ref);
	$page = geturl($Url["host"], defport($Url), $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), "http://sharebase.to/maccount", $cookies, 0, 0, $_GET["proxy"],$pauth);

	is_page($page);
	is_notpresent($page, 'mlogout', 'Error logging in - are your logins correct? Third');

	$upsrv = cut_str($page,'rt/form-data" action="','upload"');
	$umid = cut_str($page,'name="umid" type="hidden" value="','"');
	
	$post['umid']= $umid;
	$post['uptyp']= '1';
	$post['upload']= 'Upload Your Files';
	
	$refup = $upsrv;
	$url=parse_url($refup.'upload');
?>
<script>document.getElementById('info').style.display='none';</script>
<?

	$upfiles=upfile($url["host"],defport($url), $url["path"].($url["query"] ? "?".$url["query"] : ""),$refup, $cookies, $post, $lfile, $lname, "ufile[]");

?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?
	is_page($upfiles);

	$ddl=cut_str($upfiles,'downlink" size="65" value="','"');
	$del=cut_str($upfiles,'deletelink" size="65" value="','"');
	$download_link=$ddl;
	$delete_link= $del;
	}
	
// Made by Baking 15/07/2009 07:27
// Member upload plugin 16/07/2009 15:37
// Thx to "TheOnly92" for his help
// Fixed By Baking 12/12/2009 16:14
?>