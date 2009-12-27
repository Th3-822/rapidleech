<?php
####### Free Account Info. ###########
$x7_login = "";
$x7_pass = "";
##############################

// Baking Addon
function generate($len){
	$conso = Array("b","c","d","f","g","h","j","k","l","m","n","p","r","s","t","v","w","x","y","z");
	$vocal = Array("a","e","i","o","u");
	$password = '';
	
	for($i=0; $i < $len/2; $i++)
	{
		$c = ceil(rand()) % 20;
		$v = ceil(rand()) % 5;
		$password .= $conso[$c].$vocal[$v];
	}

	return $password;
}
//End Addon

$not_done=true;
$continue_up=false;
if ($x7_login & $x7_pass){
	$_REQUEST['login'] = $x7_login;
	$_REQUEST['password'] = $x7_pass;
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
<div id=login width=100% align=center>Login to x7.to</div>
<?php
			$ref = "http://x7.to/";
			
			$post['id'] =  $_REQUEST['login'];
			$post['pw'] = $_REQUEST['password'];
			
			$page = geturl("x7.to", 80, "/james/login", $ref, 0, $post, 0, $_GET["proxy"], $pauth);
			is_page($page);
			is_notpresent($page, 'succ:true', 'Error logging in - are your logins correct? First');

			
			$cok1 = "PHPSESSID=".cut_str($page,"PHPSESSID=" ,";").";";
			$cok2 = cut_str($page,"x7.to" ,";").";";
			$cok3 = "login=".cut_str($cok2,"login=" ,";");
			$cookies = $cok1.$cok3;


			$page = geturl("x7.to", 80, "/my", $ref, $cookies, 0, 0, $_GET["proxy"], $pauth);
			is_page($page);
			is_notpresent($page, 'Logout', 'Error logging in - are your logins correct? Second');
			
			$id = cut_str($page,'hidden" id="id" value="','"');
			$pw = cut_str($page,'hidden" id="pw" value="','"');
			
?>
<script>document.getElementById('login').style.display='none';</script>
<div id=info width=100% align=center>Retrive upload ID</div>
<?php

		
	$ref='http://x7.to/';
	$Url=parse_url($ref);
	$page = geturl($Url["host"], defport($Url), $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, 0, 0, 0, $_GET["proxy"],$pauth);
	
	$upserv = cut_str($page,"var uploadServer = '" ,"'");
	
	$upost['Filename'] = $lname;
	$upost['Upload'] = 'Submit Query';
	
	$code = generate(7);
	
	$url=parse_url($upserv."upload?admincode=".$code."&id=$id&pw=$pw");
?>
<script>document.getElementById('info').style.display='none';</script>
<?
	$upagent = "Shockwave Flash";
	$upfiles = upfile($url["host"], defport($url), $url["path"].($url["query"] ? "?".$url["query"] : ""), 0, 0, $upost, $lfile, $lname, "Filedata",0,$upagent);
?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?
	$ddl=cut_str($upfiles,'close' ,',');
	echo strlen($ddl);
	if (strlen($ddl)<4){html_error("Error - Unable to retrive the download link, please try again later.");}
	$access_pass = $code;
	$download_link= "http://x7.to/".ltrim($ddl);
	
	}

// Made by Baking 20/11/2009 13:03
// Upgraded by Baking 25/12/2009 12:30
// Member by Baking 25/12/2009 13:06
?>