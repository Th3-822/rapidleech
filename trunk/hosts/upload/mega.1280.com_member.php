<?php

//Input your mega.1280.com username and password
$site_login = '';
$site_maildom = 'yahoo.com';
$site_pass = '';
$site_desc = 'Upload by RL';



 /////////////////////////////////////////////////
$not_done=true;
$continue_up=false;
if ($site_login && $site_pass && $site_maildom && $site_desc)
{
	$_REQUEST['my_login'] = $site_login;
	$_REQUEST['my_pass'] = $site_pass;
	$_REQUEST['my_maildom'] = $site_maildom;
	$_REQUEST['my_desc'] = $site_desc;
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
		<tr><td nowrap>&nbsp;Mail-Domain*</td>
				<td>&nbsp;<select type=password name=my_maildom value='' style="width:180px;">
										<option value='yahoo.com'>@yahoo.com</option>
										<option value='yahoo.com.vn'>@yahoo.com.vn</option>
										<option value='gmail.com'>@gmail.com</option>
										<option value='hotmail.com'>@hotmail.com</option>
									</select>
				</td>
		</tr>
		<tr><td nowrap>&nbsp;Description*</td><td>&nbsp;<input type=text name=my_desc value='Upload by RL' style="width:250px;" />&nbsp;</td></tr>
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
	echo "<script>document.getElementById('login').style.display='none';</script>";
?>
<table width=1000 align=center>
</td></tr>
<tr><td align=center>
	
<div id=info width=100% align=center>Retrive upload ID</div>
<?php
			$post['user_email'] = trim($_REQUEST['my_login']);
            $post['user_password'] = trim($_REQUEST['my_pass']);

			$Url=parse_url('http://mega.1280.com/login.php');
			$page = geturl($Url["host"], $url["port"] ? $url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, 0, 0, 0, $_GET["proxy"], $pauth);
			is_page($page);
			$cookie = GetCookies($page);
			
			$post = array(
					'user_email' => $_REQUEST['my_login'],
					'lstdomain_mail' => urlencode('@' . $_REQUEST['my_maildom']),
					'user_password' => $_REQUEST['my_pass'],
					'btnLogin' => '+',
					'user_previous' => 'http%3A%2F%2Fmega.1280.com%2Findex.php');

			$Url = parse_url('http://psp.1280.com/login.php');
			$page = geturl($Url["host"], $url["port"] ? $url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 'http://psp.1280.com/index.php?url_back=http://mega.1280.com/index.php', $cookie, $post, 0, $_GET["proxy"], $pauth);
			is_page($page);
			$cookies = $cookie . '; ' . GetCookies($page);
			
			$cookies = substr($cookie, 0, 34) . preg_replace('%.*=-1*%', '', $cookies);
			if (!preg_match('%ocation: (.+)\r\n%', $page, $ref)) html_error('Error getting return url');
		               
			$Url=parse_url($ref[1]);
			$page = geturl($Url["host"], $url["port"] ? $url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, $cookies, 0, 0, $_GET["proxy"], $pauth);
			is_page($page);

			$post["APC_UPLOAD_PROGRESS"] = cut_str($page,'fileKey" value="','"');
			$post["uploadfile"] = 'uploadfile';
			$post["folder"] = -1;
			$post["public"] = 1;
			$post["txt_rmail"] = '';
			$post["txt_rmail_many"] = '';
			$post["txt_ymail"] = '';
			$post["link_file_pwd"] = '';
			$post["check_accept2"] = 'check_accept';
			$post["txt_fdes"] = $_REQUEST['my_desc'];
			$post["check_accept2"] = 'check_accept';
			
			$url = parse_url('http://mega.1280.com/upload.php');
?>
<script>document.getElementById('info').style.display='none';</script>
<table width=600 align=center>
</td></tr>
<tr><td align=center>
<?
			$upfiles=upfile($url["host"],$url["port"] ? $url["port"] : 80, $url["path"].($url["query"] ? "?".$url["query"] : ""), $Url, $cookies, $post, $lfile, $lname, "fileupload");
			//file_put_contents('mega1280.log', $upfiles);
?>
<script>document.getElementById('progressblock').style.display='none';</script>
<div id=final width=100% align=center>Get final code</div>
<?php		
			is_page($upfiles);

			if(!preg_match("#http://mega.1280.com/file/[^'\"<]+/#", $upfiles, $preg)) html_error("Error get direct link");
			$download_link=$preg[0];
?>
<script>document.getElementById('final').style.display='none';</script>
<?php
      }
// written by szalinski 02-Sep-09
?>