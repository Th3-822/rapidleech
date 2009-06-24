<?php

$site_login = '';  // input your username
$site_pass = '';   // input your password

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
$post = array();
$post['username'] = $_REQUEST['my_login'];
$post['password'] = $_REQUEST['my_pass'];
$post['action_login'] = 'Log+in';
$login_url = 'http://www.supernovatube.com/login.php';
$Url = parse_url($login_url);
$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, 0,$post, 0, $_GET["proxy"], $pauth);
is_page($page);
is_notpresent($page, '302 Found', 'Invalid username or password');
$cookies = GetCookies($page);
/*
preg_match('/PHPSESSID=([a-z0-9]{26}); /i', $cookies, $cook);
$phpsessionid_cookie = $cook[0];
$mysessionid_cookie = 'PHPSESSID=';
$cookie_string = $mysessionid_cookie.$phpsessionid_cookie;
*/
?>

<script>document.getElementById('login').style.display='none';</script>
<div id=info width=100% align=center>Retrive upload ID</div>

<?php

$uploadpage = 'http://www.supernovatube.com/flash_upload_fox.php';
$Url = parse_url($uploadpage);
$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, $cookies, 0, 0, $_GET["proxy"], $pauth);

preg_match('/UID=(.*)/i', $page, $upload_identifier_array);
$upid=rtrim($upload_identifier_array[1]) ;
$upload_identifier = 'http://fox.supernovatube.com/ubr_link_upload.php?rnd_id=1245664158997';
//$upload_url_parse = parse_url($upload_identifier);
//$main_upload_url = 'http://'.$upload_url_parse;
$Url = parse_url($upload_identifier);
$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, $cookies, 0, 0, $_GET["proxy"], $pauth);
$upload_id=cut_str($page,'startUpload("','"')
//preg_match('/upload_id=([a-z0-9]{32})/', $page, $upload_ids);
//$upload_id = $upload_ids[1];
?>
<script>document.getElementById('info').style.display='none';</script>
<?php

$action_url = 'http://fox.supernovatube.com/cgi-bin/ubr_upload.pl?upload_id='.$upload_id;
$url = parse_url($action_url);

$post = array();
$post['field_myvideo_title'] = $lname;
$post['UID'] = $upid;
$post['field_myvideo_descr'] = 'mydescription';
$post['field_myvideo_keywords'] = 'my keywords';
$post['chlist[]'] = '1';
$post['field_privacy'] = 'public';

$upfiles = upfile($url["host"],$url["port"] ? $url["port"] : 80, $url["path"].($url["query"] ? "?".$url["query"] : ""), $uploadpage, $cookie, $post, $lfile, $lname, "upfile_0");
?>


<script>document.getElementById('progressblock').style.display='none';</script>
<?php
is_page($upfiles);

$info_url = 'http://fox.supernovatube.com/upload_finished.php?upload_id='.$upload_id;
$Url = parse_url($info_url);
$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, $cookie_string, 0, 0, $_GET["proxy"], $pauth);
preg_match('/Location: (.*)\r\n/', $page, $infos);
$info_page = $infos[1];
$Url = parse_url($info_page);
$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, $cookie_string, 0, 0, $_GET["proxy"], $pauth);
preg_match('/http:\/\/.+/', $page, $glink);
$download_link = $glink[0];
}
// written by castledracula and kaox 24/06/2009
?>