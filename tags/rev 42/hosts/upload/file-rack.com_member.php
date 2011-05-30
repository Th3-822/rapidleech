<?
####### Free Account Info. ###########
$filerack_login = "";
$filerack_pass = "";
##############################

$not_done=true;
$continue_up=false;
if ($filerack_login & $filerack_pass){
	$_REQUEST['login'] = $filerack_login;
	$_REQUEST['password'] = $filerack_pass;
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
<tr><td nowrap>&nbsp</td></tr>
<tr><td nowrap>&nbsp;File description<td>&nbsp;<input type=text name=descript value='' style="width:160px;" />&nbsp;</tr>
<tr><td colspan=2 align=center><input type=submit value='Upload' /></tr>
<tr><td colspan=2 align=center><small>*You can set it as default in <b><?php echo $page_upload["file-rack.com"]; ?></b></small></tr>
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
<div id=login width=100% align=center>Login to file-rack.com</div>
<?php
			$post['op'] = "login" ;
			$post['login'] = $_REQUEST['login'];
			$post['password'] = $_REQUEST['password'];
			$post['submit'] = "login" ;
			$post['formSubmitted'] = "true" ;
			$page = geturl("www.file-rack.com", 80, "/login.html", 0, 0, $post, 0, $_GET["proxy"], $pauth);
			$cookies = cut_str($page,'Set-Cookie: ',';');
			//preg_match ( '/Location: (.*)/', $upfiles, $loc);
			is_page($page);
			is_notpresent($page, 'HTTP/1.1 302 Moved', 'Error logging in - are your logins correct?');
			//$cookies=implode("; ", GetCookies($page, true));
			$page = geturl("www.file-rack.com", 80, "/my_index.html", 0, $cookies, 0, 0, "");
			is_page($page);
			is_notpresent($page, 'HTTP/1.1 200 OK', 'Error logging in - are your logins correct?');
?>
<script>document.getElementById('login').style.display='none';</script>
<div id=info width=100% align=center>Retrive upload ID</div>
<?php
	$ref='http://www.file-rack.com/';
	$Url=parse_url($ref.'upload.html');
	$page = geturl($Url["host"], defport($Url), $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, $cookies, 0, 0, $_GET["proxy"],$pauth);
	is_page($page);
	$uid=cut_str($page,'"sess_id" value="','"');
	if (!$uid) html_error ('Error get uid');
	$post['upload_type']='file';
	$post['sess_id']=$uid;
	$post['file_0_descr']=$_REQUEST['descript'];
	$post['file_0_public']='1';
	$post['file_0_keyword']='';
	$post['link_rcpt']='';
	$post['link_pass']='';
	$post['tos']='1';
	$post['submit']=' Upload! ';
	$url=parse_url($ref.'upload.php?upload_id=');
?>
<script>document.getElementById('info').style.display='none';</script>
<?

	$upfiles=upfile($url["host"],defport($url), $url["path"].($url["query"] ? "?".$url["query"] : ""),$ref, $cookies, $post, $lfile, $lname, "file_0");

?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?
	is_page($upfiles);
	unset($post);
	$locat=trim(cut_str($upfiles,'Location:',"\n"));
	if (!$locat || !strpos($locat,'file-rack.com')) html_error ('Error get location');
	
	$Url=parse_url($locat);
	$page = geturl($Url["host"], defport($Url), $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $ref, $cookies, 0, 0, $_GET["proxy"],$pauth);
	is_page($page);
	$tmp=cut_str($page,'[URL=',']');
	if ($tmp) $download_link=$tmp;
	else html_error ('Error retrive download url');
	
	}
// sert 13.05.2009
// Upload Member added by Baking 27/06/2009 16:04
?>