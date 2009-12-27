<?php
####### Free Account Info. ###########
$extabit_login = "";
$extabit_pass = "";
##############################

$not_done=true;
$continue_up=false;
if ($extabit_login & $extabit_pass){
	$_REQUEST['email'] = $extabit_login;
	$_REQUEST['password'] = $extabit_pass;
	$_REQUEST['action'] = "FORM";
	echo "<b><center>Use Default email/pass.</center></b>\n";
}
if ($_REQUEST['action'] == "FORM")
    $continue_up=true;
else{
?>
<table border=0 style="width:270px;" cellspacing=0 align=center>
<form method=post>
<input type=hidden name=action value='FORM' />
<tr><td nowrap>&nbsp;email*<td>&nbsp;<input type=text name=email value='' style="width:160px;" />&nbsp;</tr>
<tr><td nowrap>&nbsp;Password*<td>&nbsp;<input type=password name=password value='' style="width:160px;"/>&nbsp;</tr>
<tr><td nowrap>&nbsp;Private<td>&nbsp;<input type="checkbox" name="private" value="1"/></tr>
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
<div id=login width=100% align=center>Login to extabit</div>
<?php
			$ref = "http://extabit.com/";
			
			$post['email'] =  $_REQUEST['email'];
			$post['pass'] = $_REQUEST['password'];
			$post['remember'] = $_REQUEST['password'];
			$post['auth_submit_login.x'] = mt_rand(1, 30);
			$post['auth_submit_login.y'] = mt_rand(1, 30);;
			$post['auth_submit_login'] = 'Enter';

			$page = geturl("extabit.com", 80, "/login.jsp", 0, 0, $post, 0, $_GET["proxy"], $pauth);
			is_page($page);
			is_notpresent($page, 'auth_hash', 'Error logging in - are your logins correct? First');

			
			preg_match_all('/Set-Cookie: (.*);/U',$page,$temp);
			$cookie1 = $temp[1];
			$cookies = implode('; ',$cookie1);


			$page = geturl("extabit.com", 80, "/profile.jsp", $ref, $cookies, 0, 0, $_GET["proxy"], $pauth);
			is_page($page);
			is_notpresent($page, 'logout.jsp', 'Error logging in - are your logins correct? Second');
?>
<script>document.getElementById('login').style.display='none';</script>
<div id=info width=100% align=center>Retrive upload ID</div>
<?php
		
	$page = geturl("extabit.com", 80, "/", $ref, $cookies, 0, 0, $_GET["proxy"], $pauth);
	is_page($page);
	is_notpresent($page, 'logout.jsp', 'Error logging in - are your logins correct? Second');
			
	$upfrom = cut_str($page,' block;" action="' ,'"');
	$APC = cut_str($page,'name="APC_UPLOAD_PROGRESS" value="' ,'"');
	$UPK = cut_str($page,'name="upload_key" value="' ,'"');
	$DIR = cut_str($page,'name="folder" value="' ,'"');
	$UID = cut_str($page,'name="uid" value="' ,'"');
	$MAX = cut_str($page,'name="MAX_FILE_SIZE" value="' ,'"');
	
	$upost['APC_UPLOAD_PROGRESS'] = $APC;
	$upost['upload_key'] = $UPK;
	$upost['folder'] = $DIR;
	$upost['uid'] = $UID;
	$upost['MAX_FILE_SIZE'] = $MAX;
	$upost['checkbox_terms'] = 'on';
	if(!$_REQUEST['private']) {$_REQUEST['private'] = 0;}
		else{$_REQUEST['private'] = 1;}
	$upost['private'] = $_REQUEST['private'];
	
	$url=parse_url($upfrom);
?>
<script>document.getElementById('info').style.display='none';</script>
<?php

	$upfiles = upfile($url["host"], defport($url), $url["path"].($url["query"] ? "?".$url["query"] : ""), 0, 0, $upost, $lfile, $lname, "my_file");
?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?php

	$download_link= trim(cut_str($upfiles,'Location:',"\n"));
	
	}

// Made by Baking 27/12/2009 18:08
?>