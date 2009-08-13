<?php

//Input your Zshare username and pass
$zshare_premium_user = '';
$zshare_premium_pass = '';



/////////////////////////////////////////////////
$not_done=true;
$continue_up=false;
if ($zshare_premium_user & $zshare_premium_pass)
{
	$_REQUEST['my_login'] = $zshare_premium_user;
	$_REQUEST['my_pass'] = $zshare_premium_pass;
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
<!--<tr><td nowrap>&nbsp;Description*<td>&nbsp;<textarea name="description" style="width:160px;"></textarea>&nbsp;</tr>-->
<tr><td colspan=2 align=center><input type=submit value='Upload'></tr>
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
<div id=login width=100% align=center>Login to ZShare.Net</div>
<?php
			if (!isset($_REQUEST['my_login']) || !isset($_REQUEST['my_pass'])) html_error('No user and pass given', 0);
			$post = array();
			$post['username'] = $_REQUEST['my_login'];
			$post['password'] = $_REQUEST['my_pass'];
			$post['submit.x'] = rand(7, 123);
			$post['submit.y'] = rand(4, 22);
			$post['submit'] = 'submit';

			$login_url = 'http://www.zshare.net/myzshare/process.php?loc=http://www.zshare.net/myzshare/login.php';
			$Url = parse_url($login_url);
			$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, 0, $post, 0, $_GET["proxy"], $pauth);
			is_page($page);
			is_notpresent($page, '302 Found', 'Error logging into the website');
			$cookies = GetCookies($page);
			//preg_match('/PHPSESSID=([a-z0-9]{32}); /i', $cookies, $cook);
			preg_match('/sid=([a-z0-9]{32}); /i', $cookies, $cook);
			$sess_cookie = $cook[0];
			$mysess_cookie = 'mysession='.$cook[1];
			$upload_cookie = $sess_cookie.$mysess_cookie;
?>
<script>document.getElementById('login').style.display='none';</script>
<div id=info width=100% align=center>Retrive upload ID</div>
<?php
			$uploadpage = 'http://zshare.net/';
			$Url = parse_url($uploadpage);
			$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, $upload_cookie, 0, 0, $_GET["proxy"], $pauth);
			
			preg_match('/var path_to_link_script = \"(.*)\";/i', $page, $link_script_url);
			preg_match('/var path_to_upload_script = \"(.*)\";/i', $page, $upload_script_url);
			
			$upload_id_url = $link_script_url[1];
			$upload_url = $upload_script_url[1];
			$upload_url_parse = parse_url($upload_url);
			$main_upload_url = 'http://'.$upload_url_parse['host'].':'.$upload_url_parse['port'].'/';

			$Url = parse_url($upload_id_url);
			$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, $upload_cookie, 0, 0, $_GET["proxy"], $pauth);
			preg_match('/startUpload\(\"([a-z0-9]{32})\"/i', $page, $upload_ids);
			$upload_id = $upload_ids[1];
?>
<script>document.getElementById('info').style.display='none';</script>
<?php
			$action_url = $upload_url.'?upload_id='.$upload_id;
			$url = parse_url($action_url);
			
			$post = array();
			$post['desc'] = '';
			$post['TOS'] = '1';

			$upfiles = upfile($url["host"],$url["port"] ? $url["port"] : 80, $url["path"].($url["query"] ? "?".$url["query"] : ""), $uploadpage, $upload_cookie, $post, $lfile, $lname, "file");
?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?php 	
			is_page($upfiles);
			is_notpresent($upfiles, '302 Found', 'Unknown Error - maybe upload failed?');
			$info_url = $main_upload_url.'/index2.php?upload_id='.$upload_id.'&f_id='.$lname.'&descr=';
			$Url = parse_url($info_url);
			$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, $upload_cookie, 0, 0, $_GET["proxy"], $pauth);
			preg_match('/Location: (.*)\r\n/i', $page, $infos);
			$info_page = $infos[1];
			$Url = parse_url($info_page);
			$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, $upload_cookie, 0, 0, $_GET["proxy"], $pauth);
			preg_match('/value=\"(http:\/\/www.zshare.net\/(download|video)\/.*\/)\" size=/i', $page, $glink);
			preg_match('/value=\"(http:\/\/www.zshare.net\/delete.html\?.*)\" size=/i', $page, $dlink);
			$download_link = $glink[1];
			$delete_link = $dlink[1];
	}
	//updated by szalinski 12-Aug-2009
?>