<?
####### Free Account Info. ###########
$uma_login = "";
$uma_pass = "";
##############################

$not_done=true;
$continue_up=false;
if ($uma_login & $uma_pass){
	$_REQUEST['login'] = $uma_login;
	$_REQUEST['password'] = $uma_pass;
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
<div id=login width=100% align=center>Login to uploadmachine.com</div>
<?php
			$post['act'] = "login" ;
			$post['user'] = $_REQUEST['login'];
			$post['pass'] = $_REQUEST['password'];
			$post['login'] = "Connexion" ;
			$post['autologin'] = "1" ;
			$page = geturl("www.uploadmachine.com", 80, "/login.php", 0, 0, $post, 0, $_GET["proxy"], $pauth);
			is_page($page);
			is_notpresent($page, 'HTTP/1.1 302 Found', 'Error logging in - are your logins correct? First');
						
			$cookie1 = "PHPSESSID=".cut_str($page,'Cookie: PHPSESSID=',";")."; ";
			$cookie2 = "yab_passhash=".cut_str($page,'Cookie: yab_passhash=',";")."; ";
			$cookie3 = "yab_sess_id=".cut_str($page,'Cookie: yab_sess_id=',";")."; ";
			$cookie4 = "yab_last_click=".cut_str($page,'Cookie: yab_last_click=',";")."; ";
			$cookie5 = "yab_uid=".cut_str(cut_str($page,'yab_uid',true),'Cookie: yab_uid=',';')."; ";
			$cookie6 = "yab_autologin=1"."; ";
			$cookie7 = "yab_mylang=en"."; ";
			$cookie8 = "yab_logined=1"."; ";
			$cookies = "$cookie3$cookie7$cookie8$cookie5$cookie4$cookie1$cookie6$cookie2";
			
			$page = geturl("www.uploadmachine.com", 80, "/members.php", "http://www.uploadmachine.com/", $cookies, 0, 0, "");
			is_page($page);
			is_notpresent($page, 'HTTP/1.1 200 OK', 'Error logging in - are your logins correct?Second');
			
?>
<script>document.getElementById('login').style.display='none';</script>
<div id=info width=100% align=center>Retrive upload ID</div>
<?php
	$ref='http://www.uploadmachine.com/';
	$Url=parse_url($ref);
	$page = geturl($Url["host"], defport($Url), $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, $cookies, 0, 0, $_GET["proxy"],$pauth);
	is_page($page);
	
	$upfrm = cut_str($page,'name=uploadform action="','"');
	$sessionid = cut_str($page,'name="sessionid" value="','"');
	$aceskey = cut_str($page,'name="AccessKey" value="','"');
	$maxsize = cut_str($page,'name="maxfilesize" value="','"');
	$phpscript = cut_str($page,'name="phpuploadscript" value="','"');
	$returnurl = cut_str($page,'name="returnurl" value="','"');
	
	$post['sessionid']=$sessionid;
	$post['UploadSession']= $sessionid;
	$post['AccessKey']= $aceskey;
	$post['maxfilesize']= $maxsize;
	$post['phpuploadscript']= $phpscript;
	$post['returnurl']= $returnurl;
	$post['uploadmode']= "1";
	$post['file_descr[0]']="";
	$post['file_password[0]']="";
	$post['flash_descr']= "";
	$post['flash_password']= "";
	$post['uploadurl[0]']= "";
	$post['url_descr[0]']= "";
	$post['url_password[0]']= "";
		
	$url=parse_url($upfrm);
?>
<script>document.getElementById('info').style.display='none';</script>
<?

	$upfiles=upfile($url["host"],defport($url), $url["path"].($url["query"] ? "?".$url["query"] : ""),'http://www.upfordown.com/', $cookies, $post, $lfile, $lname, "uploadfile_0");
	$locat=trim(cut_str($upfiles,'Location:',"\n"));
?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?
	is_page($upfiles);
	$locat=trim(cut_str($upfiles,'Location:',"\n"));
		
	$Url=parse_url($locat);
	$page = geturl($Url["host"], defport($Url), $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 'http://server1.uploadmachine.com/', $cookies, 0, 0, $_GET["proxy"],$pauth);
	is_page($page);
	
	$Url=parse_url('http://www.uploadmachine.com/members.php?showlinks=1');
	$page = geturl($Url["host"], defport($Url), $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 'http://www.uploadmachine.com/', $cookies, 0, 0, $_GET["proxy"],$pauth);
	is_page($page);
	
	$temp=cut_str($page,'this text">'.$lname,' target=blank>http://www.uploadmachine.com/delete.php?');
	$ddl=cut_str($page,'http://www.uploadmachine.com/file/','"');
	$del=cut_str($page,'http://www.uploadmachine.com/delete.php?id=','"');
	
	$download_link= 'http://www.uploadmachine.com/file/'.$ddl;
	$delete_link= 'http://www.uploadmachine.com/delete.php?id='.$del;
	
	}
// Made by Baking 13/07/2009 15:22

?>