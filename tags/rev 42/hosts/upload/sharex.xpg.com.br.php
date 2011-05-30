<?php

//Input your sharex.xpg.com.br username and password
$site_login = '';
$site_pass = '';



/////////////////////////////////////////////////
$not_done=true;
$continue_up=false;
if ($site_login & $site_pass)
{
	$_REQUEST['my_login'] = $site_login;
	$_REQUEST['my_pass'] = $site_pass;
	$_REQUEST['action'] = "FORM";
	echo "<b><center>Use Default login/pass.</center></b>\n";
}

if ($_REQUEST['action'] == "FORM")
{
	$continue_up=true;
}
else
{
?>
<table border=0 style="width:350px;" cellspacing=0 align=center>
<form method=post>
<input type=hidden name=action value='FORM' />
<tr><td nowrap>&nbsp;Username*<td>&nbsp;<input type=text name=my_login value='' style="width:160px;" />&nbsp;</tr>
<tr><td nowrap>&nbsp;Password*<td>&nbsp;<input type=password name=my_pass value='' style="width:160px;" />&nbsp;</tr>
<tr><td colspan=2 align=center><input type=submit value='Upload'></tr>
</table>
</form>
<?php
}
if ($continue_up)
{
	$not_done = false;
?>
<table width=600 align=center>
</td></tr>
<tr><td align=center>
<div id=login width=100% align=center>Login to Site</div>
<?php
if ( empty($_REQUEST['my_login']) || empty($_REQUEST['my_pass']) ) html_error('No user and pass given', 0);
$Url = parse_url('http://sharex.xpg.com.br/login.php');
$post = array();
$post['email'] = urlencode(trim($_REQUEST['my_login']));
$post['passwd'] = trim($_REQUEST['my_pass']);
$post['btenter'] = 'entrar';
$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, 0, $post, 0, $_GET["proxy"], $pauth);
is_page($page);
is_present($page, 'Para entrar, preencha seu e-mail e senha de acesso', 'Error logging in - please check your login information is correct');
$cookies = GetCookies($page);
$Url = parse_url('http://sharex.xpg.com.br/');
$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 'http://sharex.xpg.com.br/login.php', $cookies, 0, 0, $_GET["proxy"], $pauth);
is_page($page);
?>
<!-- now get rid of the login div -->
<script>document.getElementById('login').style.display='none';</script>
<div id=info width=100% align=center>Retrieve upload ID</div>
<?php
if (!preg_match('%javascript:beginUpload\(\'(.{32})\'\);return%', $page, $sid)) html_error('Upload ID not found');
if (!preg_match('%<input type="hidden" name="control" value="(.+)">%', $page, $control)) html_error('Upload control-ID not found');
$url = parse_url('http://sharex.xpg.com.br/cgi-bin/upload.cgi?sid=' . $sid[1] . '&maxsize=314572800');
?>
<script>document.getElementById('info').style.display='none';</script>
<?php
$post = array();
$post['control'] = $control[1];
$upfiles = upfile($url["host"],$url["port"] ? $url["port"] : 80, $url["path"].($url["query"] ? "?".$url["query"] : ""), 0, $cookies, $post, $lfile, $lname, "file");
?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?php
is_page($upfiles);
if (!$dpage = cut_str($upfiles, "<script>window.top.location = '", "';</script>")) html_error('Couldn\'t find the upload links');
$Url = parse_url($dpage);
$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, $cookies, 0, 0, $_GET["proxy"], $pauth);
is_page($page);
preg_match('%(http://sharex\.xpg\.com\.br/files/\d+/.+\..+)"%U', $page, $glink);
preg_match('%(http://sharex\.xpg\.com\.br/files/\d+/\d+[^\.])"%U', $page, $dlink);
$download_link = $glink[1];
$delete_link = $dlink[1];
}
//szal 04jul2009
?>