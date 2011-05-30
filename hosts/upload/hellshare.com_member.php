<?
####### Free Account Info. ###########
$hellshare_login = "";
$hellshare_pass = "";
##############################

$not_done=true;
$continue_up=false;
if ($hellshare_login & $hellshare_pass){
	$_REQUEST['login'] = $hellshare_login;
	$_REQUEST['password'] = $hellshare_pass;
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
<tr><td nowrap>&nbsp;Description<td>&nbsp;<textarea name="description_0" style="width:160px;"></textarea>&nbsp;</tr>
<tr><td nowrap>&nbsp;Dealer ID<td>&nbsp;<input type=updealer_id name=updealer_id value='' style="width:160px;" />&nbsp;</tr>
<tr><td nowrap>&nbsp;Private File<td>&nbsp;<input id="input_initinfo_private_file_0" name="private_file_0" type="checkbox" value="1" />
</table>
<center><input type=submit value='Upload' /></center></tr>
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
<div id=login width=100% align=center>Login to hellshare.com</div>
<?php

			$ref = 'http://www.en.hellshare.com/';

			$post['lgnb17_lg'] = $_REQUEST['login'];
			$post['lgnb17_psw'] = $_REQUEST['password'];
			$post['lgnb17_psw_holder'] ="";
			$post['lgnb17_sbm'] = "Log In";
			$post['DownloadRedirect'] = $_REQUEST['password'];
			
			$Url=parse_url($ref);
			$page = geturl($Url["host"], defport($Url), $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $ref, 0, $post, 0, $_GET["proxy"],$pauth);
			is_page($page);
			is_notpresent($page, 'profile', 'Error logging in - are your logins correct?');

			preg_match_all('/Set-Cookie: (.*);/U',$page,$temp);
			$cookie = $temp[1];
			$cookies = implode(';',$cookie);
			
			$Url=parse_url('http://www.en.hellshare.com/profile?lgnb17_m=1');
			$page = geturl($Url["host"], defport($Url), $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $ref, $cookies, 0, 0, $_GET["proxy"],$pauth);
			is_page($page);
			is_notpresent($page, '<h2>'.$_REQUEST['login'].'</h2>', 'Error logging in - are your logins correct? Second');
?>
<script>document.getElementById('login').style.display='none';</script>
<div id=info width=100% align=center>Retrive upload ID</div>
<?php
	$Url=parse_url($ref);
	$page = geturl($Url["host"], defport($Url), $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $ref, $cookies, 0, 0, $_GET["proxy"],$pauth);
	is_page($page);
	
	$tfn = cut_str($page,'this_file_num" value="','"');
	$eur = cut_str($page,'embedded_upload_results" value="','"');
	$rau = cut_str($page,'rau" value="','"');
	$upfrm = cut_str($page,'file_upload_action_path = "','"');
	$refup = "http://".cut_str($upfrm,'http://','/');
	
	preg_match_all('/Set-Cookie: (.*);/U',$page,$temp);
	$cookie = $temp[1];
	$cookies = implode(';',$cookie);
	
	$post['this_file_num']=$tfn;
	$post['embedded_upload_results']=$eur;
	$post['upload_file_folder_0']= "0";
	$post['updealer_id_0']= $_REQUEST['updealer_id'];
	$post['rau']= $rau;
	$post['description_0']=$lname;
	$post['private_file_0']= $_REQUEST['private_file_0'];
	$post['submit']=' Upload! ';
	
	$url=parse_url($upfrm);
	$ref='http://www.en.hellshare.com/';
?>
<script>document.getElementById('info').style.display='none';</script>
<?

	$upfiles=upfile($url["host"],defport($url), $url["path"].($url["query"] ? "?".$url["query"] : ""),$refup, $cookies, $post, $lfile, $lname, "upfile_0");

?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?
	is_page($upfiles);
	$locat=trim(cut_str($upfiles,'Location:',"\n"));
	unset($post);
	$Url=parse_url($locat);
	$page = geturl($Url["host"], defport($Url), $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $ref, $cookies, 0, 0, $_GET["proxy"],$pauth);
	is_page($page);

	$up2 = cut_str($page,"self.location='hs_upload_process_pro.php?tmp_sid=","'");
	$Url = parse_url("http://www.en.hellshare.com/hs_upload_process_pro.php?tmp_sid=".$up2);
	$page = geturl($Url["host"], defport($Url), $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $ref, $cookies, 0, 0, $_GET["proxy"],$pauth);
	is_page($page);

	$fileid = cut_str($page,"getElementById('fileinfo-fileid-0').value=\"",'"');
	$ddl= 'http://download.en.hellshare.com/'.cut_str($page,'<a href="http://download.en.hellshare.com/','"');
	$del= 'http://www.en.hellshare.com/'.cut_str($page,'/maintenance/',"'");

	$download_link = $ddl;
	$delete_link = $del;
	
	Echo "To edit your file informations go <a href=\"$del\">HERE</a>";
}
// Made by Baking 12/11/2009 21:27
?>