<?php

//Input your <site> username and password
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
if ( !isset($_REQUEST['my_login']) || !isset($_REQUEST['my_pass']) ) html_error('No user and pass given');

$Url = parse_url('http://rghost.ru/');
$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, 0, 0, 0, $_GET["proxy"], $pauth);
is_page($page);
$cookies = GetCookies($page);
preg_match('%name="authenticity_token" type="hidden" value="(.+)" />%U', $page, $auth_token);
$authenticity_token = $auth_token[1];
$post = array();
$post['email'] = urlencode(trim($_REQUEST['my_login']));
$post['password'] = trim($_REQUEST['my_pass']);
$post['authenticity_token'] = $authenticity_token;
$post['remember_me'] = '0';
$post['return_to'] = urlencode('http://rghost.ru/');
$post['commit'] = 'Sign+in';
$Url = parse_url('http://rghost.ru/profile/login');
$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, $cookies, $post, 0, $_GET["proxy"], $pauth);
is_page($page);
$cookies = GetCookies($page);
$Url = parse_url('http://rghost.ru/');
$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, $cookies, 0, 0, $_GET["proxy"], $pauth);
is_page($page);
if (!preg_match('%var seed=\'(\d+)\';%U', $page, $pid)) html_error('Progress-ID not found');
?>

<!-- now get rid of the login div -->
<script>document.getElementById('login').style.display='none';</script>
<div id=info width=100% align=center>Retrive upload ID</div>
<?php

$uploadpage = 'http://phonon.rghost.ru/files?X-Progress-Id=' . $pid[1];
$url = parse_url($uploadpage);
?>
<script>document.getElementById('info').style.display='none';</script>
<?php

$post = array();
$post['authenticity_token'] = $authenticity_token;
$post['commit'] = 'Upload';

$upfiles = upfile($url["host"],$url["port"] ? $url["port"] : 80, $url["path"].($url["query"] ? "?".$url["query"] : ""), 'http://rghost.ru/', $cookies, $post, $lfile, $lname, "file");
?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?php
is_page($upfiles);
if (!preg_match('%(http://rghost.ru/\d+)\'%U', $upfiles, $infos)) html_error('Download Link not found - please check your account.');
$download_link = $infos[1];
}
//szal 03jul2009
?>
