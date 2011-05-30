<?
####### Free Account Info. ###########
$ufd_login = "";
$ufd_pass = "";
##############################

$not_done=true;
$continue_up=false;
if ($ufd_login & $ufd_pass){
	$_REQUEST['login'] = $ufd_login;
	$_REQUEST['password'] = $ufd_pass;
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
<center><br>Please make sure that filenames do not contain<br> invalid characters and start with a letter or number.</center>
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
<div id=login width=100% align=center>Login to upfordown.com</div>
<?php
			$post['action'] = "login" ;
			$post['task'] = "login" ;
			$post['username'] = $_REQUEST['login'];
			$post['password'] = $_REQUEST['password'];
			$page = geturl("www.upfordown.com", 80, "/login", 0, 0, $post, 0, $_GET["proxy"], $pauth);
			is_page($page);
			is_notpresent($page, 'HTTP/1.1 302 Found', 'Error logging in - are your logins correct? First');
			preg_match_all('/Set-Cookie: (.*);/U',$page,$temp);
			$cookie = $temp[1];
			$cookies = implode('; ',$cookie);
			$page = geturl("www.upfordown.com", 80, "/myfiles", "http://www.upfordown.com/", $cookies, 0, 0, "");
			is_page($page);
			is_notpresent($page, 'HTTP/1.1 200 OK', 'Error logging in - are your logins correct?Second');
?>
<script>document.getElementById('login').style.display='none';</script>
<div id=info width=100% align=center>Retrive upload ID</div>
<?php
	$ref='http://www.upfordown.com/upload';
	$Url=parse_url($ref);
	$page = geturl($Url["host"], defport($Url), $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, 0, 0, 0, $_GET["proxy"],$pauth);
	is_page($page);
	$upAPC = cut_str($page,'name="APC_UPLOAD_PROGRESS" value="','"');
		
	$post['APC_UPLOAD_PROGRESS']=$upAPC;
	$post['action']= "upload";
	$post['upload_to']= "";
	$post['create_img_tags']= "1";
	$post['thumbnail_size']= "small";
	$post['upload_to']= "";
	$url=parse_url('http://www.upfordown.com/upload.php');
?>
<script>document.getElementById('info').style.display='none';</script>
<?

	$upfiles=upfile($url["host"],defport($url), $url["path"].($url["query"] ? "?".$url["query"] : ""),'http://www.upfordown.com/', $cookies, $post, $lfile, $lname, "file0");

?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?
	is_page($upfiles);
	
	$ddl=cut_str($upfiles,'http://www.upfordown.com/files/download/',"',");
	$download_link= 'http://www.upfordown.com/files/download/'.$ddl;

	
	}
// Made by Baking 09/07/2009 18:07

?>