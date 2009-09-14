<?
####### Free Account Info. ###########
$mup_login = "";
$mup_pass = "";
##############################

$not_done=true;
$continue_up=false;
if ($mup_login & $mup_pass){
	$_REQUEST['login'] = $mup_login;
	$_REQUEST['password'] = $mup_pass;
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
<div id=login width=100% align=center>Login to multiup.org</div>
<?php
			$post['login'] = $_REQUEST['login'];
			$post['password'] = $_REQUEST['password'];
			$page = geturl("multiup.org", 80, "/connection.php", 0, 0, $post, 0, $_GET["proxy"], $pauth);
			is_page($page);
			is_notpresent($page, 'HTTP/1.1 200 OK', 'Error logging in - are your logins correct? First');
			preg_match_all('/Set-Cookie: (.*);/U',$page,$temp);
			$cookie = $temp[1];
			$cookies = implode('; ',$cookie);
			$page = geturl("multiup.org", 80, "/index.php?compte", "http://multiup.org/", $cookies, 0, 0, "");
			is_page($page);
			is_notpresent($page, 'HTTP/1.1 200 OK', 'Error logging in - are your logins correct?Second');
?>
<script>document.getElementById('login').style.display='none';</script>
<div id=info width=100% align=center>Retrive upload ID</div>
<?php
			$ref='http://multiup.org/';
			$page = geturl("multiup.org", 80, "/", "", $cookies, 0, 0, "");
			
?>
<script>document.getElementById('info').style.display='none';</script>
<table width=600 align=center>
</td></tr>
<tr><td align=center>
<?php

			$post["Filename"]="$lname";
			$post["Upload"]="Submit Query";
			$u1='http://91.121.82.68/script.php'.'?login='.$_REQUEST['login'];
			$url = parse_url($u1);
			$upagent = "Shockwave Flash";
			$upfiles = upfile($url["host"],$url["port"] ? $url["port"] : 80, $url["path"].($url["query"] ? "?".$url["query"] : ""), 0, 0, $post, $lfile, $lname, "photoupload",0,$upagent);
?>
<script>document.getElementById('progressblock').style.display='none';</script>
<div id=final width=100% align=center>Get final code</div>
<?php		
			is_page($upfiles);
			$form=cut_str($upfiles,'?lien=','>http');
			$download_link='http://multiup.org/?lien='.$form;
			

	}
// Made by Baking 21/06/2009 21:37
// Member upload plugin 12/07/2009 15:49
?>