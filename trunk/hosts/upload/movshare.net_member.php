<?
####### Free/default Account Info. ###########
$movsh_login = "";
$movsh_pass = "";
##############################

$not_done=true;
$continue_up=false;
if ($movsh_login & $movsh_pass){
	$_REQUEST['user'] = $movsh_login;
	$_REQUEST['password'] = $movsh_pass;
	$_REQUEST['title']= $lname;
	$_REQUEST['action'] = "FORM";
	echo "<b><center>Use Default login/pass and file name as title.</center></b>\n";
}
if ($_REQUEST['action'] == "FORM")
    $continue_up=true;
else{
?>
<table border=0 style="width:270px;" cellspacing=0 align=center>
<form method=post>
<input type=hidden name=action value='FORM' />
<tr><td nowrap>&nbsp;Login*<td>&nbsp;<input type=text name=user value='' style="width:160px;" />&nbsp;</tr>
<tr><td nowrap>&nbsp;Password*<td>&nbsp;<input type=password name=password value='' style="width:160px;" />&nbsp;</tr>
<tr><td nowrap>&nbsp;<td>&nbsp;&nbsp;</tr>
<tr><td nowrap>&nbsp;Title*<td>&nbsp;<input type=text name=title value='Enter Title here' style="width:160px;" />&nbsp;</tr>
<tr><td nowrap>&nbsp;Description*<td>&nbsp;<textarea name=description style="width: 160px;">Enter Description here</textarea>&nbsp;</tr>
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
<div id=login width=100% align=center>Login to www.movshare.net</div>
<?php
			
			$post['user'] = $_REQUEST['user'];
			$post['pass'] = $_REQUEST['password'];
			$post['Submit.x'] = "0";
			$post['Submit.y'] = "0";
			$post['Submit'] = "Submit";
			$page = geturl("movshare.net", 80, "/login.php", 0, 0, $post, 0, $_GET["proxy"], $pauth);
			is_page($page);
			is_notpresent($page, 'HTTP/1.1 302 Found', 'Error logging in - are your logins correct? First');
			preg_match_all('/Set-Cookie: (.*);/U',$page,$temp);
			$cookie = $temp[1];
			$cookies = implode('; ',$cookie);
			
			
			$page = geturl("movshare.net", 80, "/panel.php", "http://movshare.net/login.php", $cookies, 0, 0, "");
			is_page($page);
			is_notpresent($page, 'HTTP/1.1 200 OK', 'Error logging in - are your logins correct? Second');

?>
<script>document.getElementById('login').style.display='none';</script>
<div id=info width=100% align=center>Retrive upload ID</div>
<?php
	$ref='http://www.movshare.net/';
	$Url=parse_url($ref.'panel.php?q=3');
	$page = geturl($Url["host"], defport($Url), $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, $cookies, 0, 0, $_GET["proxy"],$pauth);
	is_page($page);
	$userid = cut_str($page,'upload/ubr_file_upload.php?u=','" frameborder="0"');
	if (!$userid) html_error ('Error get userID');

	$rand = mt_rand(1000000000000, 1999999999999);
	
	$refup='http://95.211.84.49/upload/ubr_link_upload.php?rnd_id='.$rand;
	$Url=parse_url($refup);
	
	$page = geturl($Url["host"], defport($Url), $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, "user=$userid", 0, 0, $_GET["proxy"],$pauth);
	is_page($page);
	$upid = cut_str($page,'startUpload("','"');
	
	$post['title']=$_REQUEST['title'];
	$post['desc']=$_REQUEST['description'];
	
	$url=parse_url('http://95.211.84.49/'.'/cgi-bin/ubr_upload.pl?upload_id='.$upid);
	$refup2='http://95.211.84.49/';
?>
<script>document.getElementById('info').style.display='none';</script>
<?

	$upfiles=upfile($url["host"],defport($url), $url["path"].($url["query"] ? "?".$url["query"] : ""), $refup2, 0, $post, $lfile, $lname, "upfile_0");

?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?
	is_page($upfiles);
	$locat=cut_str($upfiles,'The document has moved <a href="','"');
	$Url=parse_url($locat);
		
	$page = geturl($Url["host"], defport($Url), $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $refup2, "user=$userid", 0, 0, $_GET["proxy"],$pauth);
	is_page($page);
		
	$end_url=cut_str($page,'Location: ',"\n");
	$Url=parse_url('http://95.211.84.49/upload/'.$end_url);
	
	$page = geturl($Url["host"], defport($Url), $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $refup2, "user=$userid", 0, 0, $_GET["proxy"],$pauth);
	is_page($page);
		
	$ddl=cut_str($page,'value="http://www.movshare.net/video/','"');
	$del=cut_str($page,'value="http://www.movshare.net/delete/','"');
	$del = substr($del,0,strlen($del)-1);

	$download_link= 'http://www.movshare.net/video/'.$ddl;
	$delete_link= 'http://www.movshare.net/delete/'.$del;
	
	}
// Made by Baking 29/06/2009 20:20
?>