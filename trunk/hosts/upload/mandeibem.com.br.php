<?php

//Input your mandeibem.com.br username and password
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
if ( !isset($_REQUEST['my_login']) || !isset($_REQUEST['my_pass']) ) html_error('No user and pass given', 0);

$Url = parse_url('http://www.mandeibem.com.br/usr/login.asp');

$post = array();
$post['idusuario'] = trim($_REQUEST['my_login']);
$post['password'] = trim($_REQUEST['my_pass']);
$post['acessar.x'] = rand(0,10);
$post['acessar.y'] = rand(0, 10);

$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, 0, $post, 0, $_GET["proxy"], $pauth);
is_page($page);
$cookies = GetCookies($page);
$Url = parse_url('http://www.mandeibem.com.br/usr/pasta.asp?cod=');
$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 'http://www.mandeibem.com.br/usr/home.asp', $cookies, 0, 0, $_GET["proxy"], $pauth);
is_page($page);
?>

<!-- now get rid of the login div -->
<script>document.getElementById('login').style.display='none';</script>
<div id=info width=100% align=center>Retrive upload ID</div>
<?php
$ID = time() % 1000000000;
$url = parse_url('http://www.mandeibem.com.br/usr/upload.asp?ID=' . $ID);
?>
<script>document.getElementById('info').style.display='none';</script>
<?php

$post = array();
$post['ProgressID'] = '';
$post['categoria'] = 'email';
$upfiles = upfile($url["host"],$url["port"] ? $url["port"] : 80, $url["path"].($url["query"] ? "?".$url["query"] : ""), 0, $cookies, $post, $lfile, $lname, "arquivo");
?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?php
is_page($upfiles);
if (preg_match('%<title>Object moved</title>%i', $upfiles)) html_error('File uploaded successfully, please check your account area.');
else html_error('An unknown error occured');
}
//szal 04jul2009
?>