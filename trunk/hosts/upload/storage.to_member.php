<?
####### Free Account Info. ###########
$sto_login = "";
$sto_pass = "";
##############################

$not_done=true;
$continue_up=false;
if ($sto_login & $sto_pass){
	$_REQUEST['email'] = $sto_login;
	$_REQUEST['password'] = $sto_pass;
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
<div id=login width=100% align=center>Login to storage.to</div>
<?php
			$post['email'] = urlencode($_REQUEST['email']);
			$post['password'] = urlencode($_REQUEST['password']);
			$post[''] = "Login" ;
			$page = geturl("storage.to", 80, "/login", 0, 0, $post, 0, $_GET["proxy"], $pauth);
			is_page($page);
			is_notpresent($page, 'HTTP/1.1 200 OK', 'Error logging in - are your logins correct? First');
			preg_match_all('/Set-Cookie: (.*);/U',$page,$temp);
			$cookie = $temp[1];
			$cookies = implode(';',$cookie);
			$page = geturl("storage.to", 80, "/account", "http://www.storage.to/login", $cookies, 0, 0, "");
			is_page($page);
			is_notpresent($page, 'HTTP/1.1 200 OK', 'Error logging in - are your logins correct?Second');
?>
<script>document.getElementById('login').style.display='none';</script>
<div id=info width=100% align=center>Retrive upload ID</div>
<?php
	$ref='http://www.storage.to/';
	$Url=parse_url($ref);
	$page = geturl($Url["host"], defport($Url), $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, 0, 0, 0, $_GET["proxy"],$pauth);
	is_page($page);
	$upfrm = cut_str($page,'form-data" action="/upload/','"');
	$upfrm = 'http://www.storage.to/upload/'.$upfrm;
	$maxfz = cut_str($page,'MAX_FILE_SIZE" value="','"');
	$post['MAX_FILE_SIZE']=$maxfz;
	$url=parse_url($upfrm);
?>
<script>document.getElementById('info').style.display='none';</script>
<?

	$upfiles=upfile($url["host"],defport($url), $url["path"].($url["query"] ? "?".$url["query"] : ""),$ref, $cookies, $post, $lfile, $lname, "files[]");

?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?
	is_page($upfiles);
	$locat='http://www.storage.to/uploadresult';
		
	$Url=parse_url($locat);
	$page = geturl($Url["host"], defport($Url), $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $ref, $cookies, 0, 0, $_GET["proxy"],$pauth);
	is_page($page);
	$ddl=cut_str($page,'name="" value="http://www.storage.to/get/','"');
	$del=cut_str($page,'name="" value="http://www.storage.to/delete/','"');
	$download_link= 'http://www.storage.to/get/'.$ddl;
	$delete_link= 'http://www.storage.to/delete/'.$del;
	
	}
// Made by Baking 30/06/2009 01:37
// Member upload plugin Made by Baking 30/06/2009 06:39
// FIXED 01/07/2009 12:37
?>