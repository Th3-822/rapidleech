<?
####### Free Account Info. ###########
$sb_login = "";
$sb_pass = "";
##############################

$not_done=true;
$continue_up=false;
if ($sb_login & $sb_pass){
	$_REQUEST['email'] = $sb_login;
	$_REQUEST['password'] = $sb_pass;
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
<div id=login width=100% align=center>Login to sharebase.to</div>
<?php
			$post['lmail'] = $_REQUEST['email'];
			$post['lpass'] = $_REQUEST['password'];
			$post['104'] = 'Login Now !' ;
			$page = geturl("sharebase.to", 80, "/members/", 0, 0, $post, 0, $_GET["proxy"], $pauth);
			is_page($page);
			is_notpresent($page, 'HTTP/1.1 200 OK', 'Error logging in - are your logins correct? First');
			//preg_match_all('/Set-Cookie: (.*);/U',$page,$temp);
			//$cookie = $temp[1];
			//$cookies = implode('; ',$cookie);
			$cok1 = 'memm='.cut_str($page,'memm=',';').'; ';
			$cok2 = 'memp='.cut_str($page,'memp=',';').';';
			$cookies ="$cok1$cok2";
					
			//$xfss=cut_str($cookies,'xfss=',' ');
			//$page = geturl("uploadspace.eu", 80, "/?op=my_files", "http://ezyfile.net/login.html", $cookies, 0, 0, "");
			//is_page($page);
			//is_notpresent($page, 'HTTP/1.1 200 OK', 'Error logging in - are your logins correct?Second');
?>
<script>document.getElementById('login').style.display='none';</script>
<div id=info width=100% align=center>Retrive upload ID</div>
<?php

	$ref='http://sharebase.to/upload/';
	$Url=parse_url($ref);
	$page = geturl($Url["host"], defport($Url), $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, $cookies, 0, 0, $_GET["proxy"],$pauth);
	is_page($page);
	
	$upsrv = cut_str($page,'name="usrv" type="hidden" value="','"');
	$umid = cut_str($page,'name="umid" type="hidden" value="','"');
	$tmp01 = cut_str($page,'<input name="umid" type="hidden" value="">','</div>');
	$upbtn = cut_str($tmp01,'class="fform" size="45"> <input name="','"');
	
	
	$post['usrv']= $upsrv;
	$post['umid']= $umid;
	$post['101']= 'Upload Now !';
	
	$refup = 'http://'.$upsrv.'.sharebase.to/';
	$url=parse_url($refup.'upload/');
	

?>
<script>document.getElementById('info').style.display='none';</script>
<?

	$upfiles=upfile($url["host"],defport($url), $url["path"].($url["query"] ? "?".$url["query"] : ""),$refup, $cookies, $post, $lfile, $lname, "ufile");

?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?
	is_page($upfiles);
	$ddl=cut_str($upfiles,'downlink" size="65" value="','"');
	$del=cut_str($upfiles,'deletelink" size="65" value="','"');
	$download_link=$ddl;
	$delete_link= $del;
	}
	
// Made by Baking 15/07/2009 07:27
// Member upload plugin 16/07/2009 15:37
// Thx to "TheOnly92" for his help
?>