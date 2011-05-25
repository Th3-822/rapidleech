<?
####### Free Account Info. ###########
$freakshare_login = "";
$freakshare_pass = "";
##############################

$not_done=true;
$continue_up=false;
if ($freakshare_login & $freakshare_pass){
	$_REQUEST['login'] = $freakshare_login;
	$_REQUEST['password'] = $freakshare_pass;
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
<tr><td nowrap>&nbsp;Login*<td>&nbsp;<input type=text name=login value='' style="width:160px;" />&nbsp;</tr>
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
<div id=login width=100% align=center>Login to freakshare.com</div>
<?php
			$post['user'] = $_REQUEST['login'];
			$post['pass'] = $_REQUEST['password'];
			$post['submit'] = "Login" ;
			$page = geturl("freakshare.com", 80, "/login.html", 'http://freakshare.com/', 0, $post, 0, $_GET["proxy"], $pauth);
			is_page($page);
			is_notpresent($page, 'Set-Cookie: login=', 'Error logging in - are your logins correct? First');
			preg_match_all('/Set-Cookie: (.*);/U',$page,$temp);
			$cookie = $temp[1];
			$cookies = implode('; ',$cookie);
			$page = geturl("freakshare.com", 80, "/", "http://freakshare.com/", $cookies, 0, 0, "");
			is_page($page);
			is_notpresent($page, "<td>".$_REQUEST['login']."</td>", 'Error logging in - are your logins correct?Second');
?>
<script>document.getElementById('login').style.display='none';</script>
<div id=info width=100% align=center>Retrive upload ID</div>
<?php

	$ref='http://freakshare.com/';
	$Url=parse_url($ref);
	$page = geturl($Url["host"], defport($Url), $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $cookies, 0, 0, 0, $_GET["proxy"],$pauth);
	is_page($page);
	
	$upfrm0 = cut_str($page,'<div id="tabcontent_file">','<fieldset>');
	$upfrm = cut_str($upfrm0,'<form action="','"');
	$refup = cut_str($upfrm0,'<form action="','/upload.php');
	
	$AUP = cut_str($page,'id="progress_key"  value="','"');
	$AUG = cut_str($page,'id="usergroup_key"  value="','"');
	$UID = cut_str($page,'name="UPLOAD_IDENTIFIER" value="','"');
		
	$post['APC_UPLOAD_PROGRESS']= $AUP;
	$post['APC_UPLOAD_USERGROUP']= $AUG;
	$post['UPLOAD_IDENTIFIER']= $UID;
	$url=parse_url($upfrm);

?>
<script>document.getElementById('info').style.display='none';</script>
<?
	$upfiles=upfile($url["host"],defport($url), $url["path"].($url["query"] ? "?".$url["query"] : ""),$refup, 0, $post, $lfile, $lname, "file[]", "file[]");
	is_page($upfiles);
	$rand = mt_rand();
	$id = time().'+'.(rand() * 1000000);
	$json = cut_str($page,'$.getJSON("' ,'"').$id;
	$Url=parse_url($json);
	$page = geturl($Url["host"], defport($Url), $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 'http://freakshare.com/', $cookies, 0, 0, $_GET["proxy"],$pauth);

?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?

	$locat=trim(cut_str($upfiles,'Location: ',"\n"));
	$Url=parse_url($locat);
	$page = geturl($Url["host"],  80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 'http://freakshare.com/', $cookies, 0, 0, $_GET["proxy"],$pauth);
	is_page($page);		
	
	$ddl=cut_str($page,'http://freakshare.com/files/','"');
	$del=cut_str($page,'http://freakshare.com/delete/','"');
	$download_link='http://freakshare.com/files/'.$ddl;
	$delete_link= 'http://freakshare.com/delete/'.$del;
}	

// Made by Baking 17/09/2009 14:16
// Big thanks to Szalinski :)
// Fixed by Darkra 23/01/2011

?>