<?php

//Input your FileFactory username (email) and password
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
	echo "<center><b>Use Default login/pass...</b></center>\n";
}
if ($_REQUEST['action'] == "FORM")
{
	$continue_up=true;
}
else
{
	echo <<<EOF
<div id=login width=100% align=center>Login to Site</div>
<table border=0 style="width:350px;" cellspacing=0 align=center>
	<form method=post>
		<input type=hidden name=action value='FORM' />
		<tr><td nowrap>&nbsp;Username*</td><td>&nbsp;<input type=text name=my_login value='' style="width:160px;" />&nbsp;</td></tr>
		<tr><td nowrap>&nbsp;Password*</td><td>&nbsp;<input type=password name=my_pass value='' style="width:160px;" />&nbsp;</td></tr>
		<tr><td colspan=2 align=center><input type=submit value='Upload'></td></tr>
	</form>
</table>
EOF;
}

if ($continue_up)
{
	$not_done = false;

	if ( empty($_REQUEST['my_login']) || empty($_REQUEST['my_pass']) ) html_error('No user and pass given', 0);
?>
	<center>
<?
	$post = array();
	$post['email'] = trim($_REQUEST['my_login']);
	$post['password'] = trim($_REQUEST['my_pass']);
	$post['redirect'] = '/';
	$page = geturl("www.filefactory.com", 80, "/", 0, 0, $post, 0, $_GET["proxy"], $pauth);
	is_page($page);
	$cookie = GetCookies($page);
	if (!preg_match('%ff_membership=%', $cookie)) html_error('Error getting login-cookie - are your logins correct?');
	$page = geturl("www.filefactory.com", 80, "/?login=1", 0, $cookie, 0, 0, $_GET["proxy"], $pauth);
	is_page($page);
	is_notpresent($page, 'You have been signed in as', 'Error logging in - are your logins correct?');
	?>
	<script type="text/javascript" language="javascript">document.getElementById('login').style.display='none';</script>
	<div id=info width="100%" align=center>Retrive upload ID</div>
	<?php
	$upload_form = cut_str($page, '<form accept-charset="UTF-8" id="uploader" action="', '"');
	if (!$url = parse_url($upload_form)) html_error('Error getting upload url');
	?>
	<script type="text/javascript" language="javascript">document.getElementById('info').style.display='none';</script>
	<?php
	$fpost = array();
	$fpost['Filename'] = $lname;
	$fpost['cookie'] = urldecode(str_replace('ff_membership=', '', $cookie));
	$fpost['folderViewhash'] = '0';
	$fpost['Upload'] = 'Submit Query';
	$upfiles = upfile($url["host"],$url["port"] ? $url["port"] : 80, $url["path"].($url["query"] ? "?".$url["query"] : ""), $upload_form, 0, $fpost, $lfile, $lname, "Filedata");
	?>
	<script type="text/javascript" language="javascript">document.getElementById('progressblock').style.display='none';</script>
	<?php
	is_page($upfiles);
	if (!preg_match('%\r\n\r\n([a-z0-9]{7})$%', $upfiles, $curi)) html_error('Couldn\'t get the download link, but the file might have been uploaded to your account ok');
	$completeurl = 'http://www.filefactory.com/file/complete.php/' . $curi[1] . '/';
	$Url = parse_url($completeurl);
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, $lcook[1], 0, 0, $_GET["proxy"], $pauth);
	is_page($page);
	is_notpresent($page, 'Upload Complete', 'Error getting download link - The upload probably failed');
	$download_link = trim(cut_str($page, '<div class="metadata">', '</div>'));
}
	?>
	</center>
<?php
//Updated by szalinski 12-Aug-2009
?>