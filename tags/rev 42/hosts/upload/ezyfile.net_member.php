<?
####### Free Account Info. ###########
$ezy_login = "";
$ezy_pass = "";
##############################

$not_done=true;
$continue_up=false;
if ($ezy_login & $ezy_pass){
	$_REQUEST['username'] = $ezy_login;
	$_REQUEST['password'] = $ezy_pass;
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
<div id=login width=100% align=center>Login to ezyfile.net</div>
<?php
			$post['op'] = "login" ;
			$post['redirect'] = "" ;
			$post['login'] = $_REQUEST['login'];
			$post['password'] = $_REQUEST['password'];
			$post['x'] = "0" ;
			$post['y'] = "0" ;
			$page = geturl("ezyfile.net", 80, "/login.html", 0, 0, $post, 0, $_GET["proxy"], $pauth);
			is_page($page);
			is_notpresent($page, 'HTTP/1.1 302 Moved', 'Error logging in - are your logins correct? First');
			preg_match_all('/Set-Cookie: (.*);/U',$page,$temp);
			$cookie = $temp[1];
			$cookies = implode(';',$cookie);
			$xfss=cut_str($cookies,'xfss=',' ');
			$page = geturl("ezyfile.net", 80, "/?op=my_files", "http://ezyfile.net/login.html", $cookies, 0, 0, "");
			is_page($page);
			is_notpresent($page, 'HTTP/1.1 200 OK', 'Error logging in - are your logins correct?Second');
?>
<script>document.getElementById('login').style.display='none';</script>
<div id=info width=100% align=center>Retrive upload ID</div>
<?php
	$ref='http://ezyfile.net/';
	$Url=parse_url($ref);
	$page = geturl($Url["host"], defport($Url), $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, 0, 0, 0, $_GET["proxy"],$pauth);
	is_page($page);
	$upfrm = cut_str($page,'form-data" action="','up');
	$uid = $i=0; while($i<12){ $i++;}
	$uid += floor(rand() * 10);
	$sessid = $xfss;
	if (!$sessid) html_error ('Error get sessid');
	$servid = cut_str($page,'.ezyfile.net/upload/','"');
	$post['srv_id']=$servid;
	$post['sess_id']=$sessid;
	$post['file_0_descr']=$_REQUEST['descript'];
	$post['file_0_public']='1';
	$post['file_0_keyword']='';
	$post['link_rcpt']='';
	$post['link_pass']='';
	$post['tos']='1';
	$post['submit']=' Upload! ';
	$uurl=$upfrm.'/upload/'.$servid.'/?X-Progress-ID='.$uid;
	$url=parse_url($upfrm.'/upload/'.$servid.'/?X-Progress-ID='.$uid);
?>
<script>document.getElementById('info').style.display='none';</script>
<?

	$upfiles=upfile($url["host"],defport($url), $url["path"].($url["query"] ? "?".$url["query"] : ""),$ref, $cookies, $post, $lfile, $lname, "file_0");

?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?
	is_page($upfiles);
	$locat=cut_str($upfiles,"name='fn' value='","'");
	unset($post);
	$gpost['op'] = "upload_result" ;
	$gpost['fn'] = "$locat" ;
	$gpost['st'] = "OK" ;
	$Url=parse_url($ref);
	$page = geturl($Url["host"], defport($Url), $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $uurl, $cookies, $gpost, 0, $_GET["proxy"],$pauth);
	is_page($page);
	$ddl=cut_str($page,'Direct Link:</b></td><td><input type="text" onFocus="copy(this);" value="','"');
	$del=cut_str($page,'Delete Link:</b></td><td><input type="text" onFocus="copy(this);" value="','"');
	$download_link=$ddl;
	$delete_link=$del;
	
	}
// Made by Baking 27/06/2009 19:49
?>