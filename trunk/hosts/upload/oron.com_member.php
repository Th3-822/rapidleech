<?
####### Free Account Info. ###########
$oron_login = "";
$oron_pass = "";
##############################

$not_done=true;
$continue_up=false;
if ($oron_login & $oron_pass){
	$_REQUEST['login'] = $oron_login;
	$_REQUEST['password'] = $oron_pass;
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
<div id=login width=100% align=center>Login to oron.com</div>
<?php
			$post['login'] = $_REQUEST['login'];
			$post['password'] = $_REQUEST['password'];
			$post['op'] = "login";
			$post['redirect'] = "";
			$post['rand'] = "";
			$page = geturl("oron.com", 80, "/", 0, 0, $post, 0, $_GET["proxy"], $pauth);
			is_page($page);
			preg_match_all('/Set-Cookie: (.*);/U', $page, $temp);
			$cookie = $temp[1];
			$cookies = implode('; ', $cookie);
?>
<script>document.getElementById('login').style.display='none';</script>
<div id=info width=100% align=center>Retrive upload ID</div>
<?
	$ref='http://oron.com/';
	$Url=parse_url($ref);
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, $cookies, 0, 0, $_GET["proxy"],$pauth);
	is_page($page);
	preg_match('/(http:\/\/.*?\.oron\.com\/upload\/.*?)"/i', $page, $action);
	$actionurl = $action[1];
	echo $actionurl;
	$uid = $i = 0; while($i<12){ $i++;}
		$uid += floor(rand() * 10);
	$post['upload_type']="file";
	preg_match('/name="srv_id" value="(.*)"/i', $page, $srv_id);
	$post['srv_id'] = $srv_id[1];
	preg_match('/name="sess_id" value="(.*)"/i', $page, $sess_id);
	$post['sess_id'] = $sess_id[1];
	preg_match('/name="srv_tmp_url" value="(.*)"/i', $page, $srv_tmp_url);
	$post['srv_tmp_url'] = $srv_tmp_url[1];
	$post['ut'] = "file";
	$post['link_rcpt'] = "";
	$post['link_pass'] = '';
	$post['tos'] = '1';
	$post['submit_btn'] = ' Загрузить ';

	$url=parse_url($actionurl . '/?X-Progress-ID=' . $uid);
?>
<script>document.getElementById('info').style.display='none';</script>
<?
	$upfiles=upfile($url["host"],defport($url), $url["path"].($url["query"] ? "?".$url["query"] : ""),$ref, 0, $post, $lfile, $lname, "file_0");

?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?
	is_page($upfiles);
	$locat=cut_str($upfiles,"name='fn' value='","'");
	unset($post);
	$gpost['fn'] = "$locat" ;
	$gpost['st'] = "OK" ;
	$gpost['op'] = "upload_result" ;
	$Url=parse_url($ref);
	$page = geturl($Url["host"], defport($Url), $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $ref, 0, $gpost, 0, $_GET["proxy"],$pauth);
	is_page($page);
	$ddl=cut_str($page, '" class="btitle">', '</a></td>');
	$del=cut_str($page, $lname. '.html?killcode=','"');
	$download_link=$ddl;
	$delete_link= $ddl.'?killcode='.$del;	
	}
// Made by Luft-on 14/03/2011 11:15
?>