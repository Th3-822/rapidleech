<?
####### Free Account Info. ###########
$fsvr_login = "";
$fsvr_pass = "";
##############################

$not_done=true;
$continue_up=false;
if ($fsvr_login & $fsvr_pass){
	$_REQUEST['login'] = $fsvr_login;
	$_REQUEST['password'] = $fsvr_pass;
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
<div id=login width=100% align=center>Login to Filesavr.com</div>
<?php

			$post['username'] = $_REQUEST['login'];
			$post['password'] = $_REQUEST['password'];
			$page = geturl("www.filesavr.com", 80, "/login.php", 0, 0, $post, 0, $_GET["proxy"], $pauth);
			$cookies = cut_str($page,'Set-Cookie: ',';');
			is_page($page);
			is_notpresent($page, 'HTTP/1.1 302 Found', 'Error logging in - are your logins correct?');
			preg_match_all('/Set-Cookie: (.*);/U',$page,$temp);
			$cookie = $temp[1];
			$cookies = implode('; ',$cookie);
			$uid = "userid=".cut_str($cookies,'userid=',';')."; ";
			$unm = "username=".cut_str($cookies,'username=',';')."; ";
			$u2d = "uniqueid=".cut_str($cookies,'uniqueid=',';')."; ";
			$cookies = "$uid$unm$u2d";
			$page = geturl("www.filesavr.com", 80, "/premium.php", "http://www.filesavr.com/", $cookies, 0, 0, "");
			is_page($page);
			is_notpresent($page, 'HTTP/1.1 200 OK', 'Error logging in - are your logins correct?');
?>
<script>document.getElementById('info').style.display='none';</script>
<table width=600 align=center>
</td></tr>
<tr><td align=center>
<?php
			$post["Filename"]="$lname";
			$post["Upload"]="Submit Query";
			$u1='http://www.filesavr.com/index.php?xml=true';
			$url = parse_url($u1);
			$upagent = "Shockwave Flash";
			$upfiles = upfile($url["host"],$url["port"] ? $url["port"] : 80, $url["path"].($url["query"] ? "?".$url["query"] : ""), 0, 0, $post, $lfile, $lname, "file",0,$upagent);
?>
<script>document.getElementById('progressblock').style.display='none';</script>
<div id=final width=100% align=center>Get final code</div>
<?php		
			is_page($upfiles);
			$ID= "PHPSESSID=".cut_str($upfiles,'PHPSESSID=',";").'; ';
			$form=cut_str($upfiles,'/index.php',"\n");
			$page = geturl("www.filesavr.com", 80, "/index.php$form", "http://www.filesavr.com/", $ID.$cookies, 0, 0, $_GET["proxy"], $pauth);
			$ddl=cut_str($page,'Location: /',"\n");
			$download_link='http://www.filesavr.com/'.$ddl;
			

}
// Made by Baking 11/09/2009 17:17
// Member plugin by Baking 11/09/2009 22:21
?>