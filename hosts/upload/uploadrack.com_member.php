<?
####### Free Account Info. ###########
$upr_login = "";
$upr_pass = "";
##############################

$not_done=true;
$continue_up=false;
if ($upr_login & $upr_pass){
	$_REQUEST['login'] = $upr_login;
	$_REQUEST['password'] = $upr_pass;
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
<div id=login width=100% align=center>Login to uploadrack.com</div>
<?php
			$post['op'] = "login" ;
			$post['redirect'] = "" ;
			$post['login'] = $_REQUEST['login'];
			$post['password'] = $_REQUEST['password'];
			$post['x'] = "0" ;
			$post['y'] = "0" ;
			$page = geturl("www.uploadrack.com", 80, "/", 'http://www.uploadrack.com/', 0, $post, 0, $_GET["proxy"], $pauth);
			is_page($page);
			is_notpresent($page, 'HTTP/1.1 302 Moved', 'Error logging in - are your logins correct? First');
			preg_match_all('/Set-Cookie: (.*);/U',$page,$temp);
			$cookie = $temp[1];
			$cookies = implode('; ',$cookie);
			$xfss=cut_str($cookies,'xfss=',' ');
			$page = geturl("www.uploadrack.com", 80, "/?op=my_files", "http://www.uploadrack.com/", $cookies, 0, 0, "");
			is_page($page);
			is_notpresent($page, 'HTTP/1.1 200 OK', 'Error logging in - are your logins correct?Second');
?>
<script>document.getElementById('login').style.display='none';</script>
<div id=info width=100% align=center>Retrive upload ID</div>
<?php
	$ref='http://212.117.170.117/';
	$upfrm = 'http://212.117.170.117/cgi-bin/upload.cgi?upload_id=';
	$uid = $i=0; while($i<12){ $i++;}
	$uid += floor(rand() * 10);
	$sessid = $xfss;
	if (!$sessid) html_error ('Error get sessid');
	//$servid = cut_str($page,'.ezyfile.net/upload/','"');
	$post['upload_type']= 'file';
	$post['sess_id']=$sessid;
	$post['file_0_descr']='';
	$post['link_rcpt']='';
	$post['link_pass']='';
	$post['tos']='1';
	$post['submit']=' Upload! ';
	$uurl=$upfrm.$uid.'&js_on=1&utype=reg&upload_type=file';
	$url=parse_url($uurl);
?>
<script>document.getElementById('info').style.display='none';</script>
<?

	$upfiles=upfile($url["host"],defport($url), $url["path"].($url["query"] ? "?".$url["query"] : ""),$ref, $cookies, $post, $lfile, $lname, "file_0");

?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?
	$ref2 = 'http://www.uploadrack.com/';
	is_page($upfiles);
	$locat=cut_str($upfiles,"textarea name='fn'>","<");
	unset($post);
	$gpost['op'] = "upload_result" ;
	$gpost['fn'] = "$locat" ;
	$gpost['st'] = "OK" ;
	$Url=parse_url('http://www.uploadrack.com/?&fn='.$locat.'&st=OK&op=upload_result');
	//$Url=parse_url($ref);
	$page = geturl($Url["host"], defport($Url), $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $ref2, $cookies, 0, 0, $_GET["proxy"],$pauth);
	is_page($page);
	//echo $page;
	$ddl=cut_str($page,'>Download Link:</b></td><td colspan=2><a href="','"');
	$del=cut_str($page, $ddl.'?killcode=','"');
	$download_link=$ddl;
	$delete_link= $ddl.'?killcode='.$del;
	
	}
// Made by Baking 04/07/2009 10:13
?>