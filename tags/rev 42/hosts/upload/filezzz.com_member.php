<?
####### Free Account Info. ###########
$filez_login = "";
$filez_pass = "";
##############################

$not_done=true;
$continue_up=false;
if ($filez_login & $filez_pass){
	$_REQUEST['login'] = $filez_login;
	$_REQUEST['password'] = $filez_pass;
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
<div id=login width=100% align=center>Login to filezzz.com</div>
<?php
			
			
			$page = geturl("filezzz.com", 80, "/auth.html", 0, 0, 0, 0, $_GET["proxy"], $pauth);
			
			preg_match_all('/Set-Cookie: (.*);/U',$page,$temp);
			$cookie = $temp[1];
			$cookies = implode(';',$cookie);
			
			$post['back'] = '' ;
			$post['login'] = $_REQUEST['login'];
			$post['password'] = $_REQUEST['password'];
			$post['submit'] = "login" ;
			$page = geturl("filezzz.com", 80, "/login.html", 'http://filezzz.com/', $cookies, $post, 0, $_GET["proxy"], $pauth);
			is_page($page);
			is_notpresent($page, 'HTTP/1.1 302 Found', 'Error logging in - are your logins correct? First'.$page);
			$page = geturl("filezzz.com", 80, "/files.html", "http://filezzz.com/auth.html", $cookies, 0, 0, "");
			is_page($page);
			is_notpresent($page, 'HTTP/1.1 200 OK', 'Error logging in - are your logins correct?Second');
?>
<script>document.getElementById('login').style.display='none';</script>
<div id=info width=100% align=center>Retrive upload ID</div>
<?php
			$ref = 'http://www.filezzz.com/';
			$Url=parse_url($ref);
			$page = geturl($Url["host"], defport($Url), $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, 0, 0, 0, $_GET["proxy"],$pauth);
			is_page($page);
			$form=cut_str($page,'<form','</form>');
			$upurl=cut_str($form,'action="','"');
			if (!$upurl) html_error ('Error get upload url');
			$url=parse_url($upurl);
?>
<script>document.getElementById('info').style.display='none';</script>
<?
			$upfiles=@upfile($url["host"],defport($url), $url["path"].($url["query"] ? "?".$url["query"] : ""),$ref, $cookies, $post, $lfile, $lname, "userfile");
?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?
			is_page($upfiles);
			$finish_url=trim(cut_str($upfiles,'Location:',"\n"));
			if (!$finish_url) html_error ('Error get location 1');
			$Url=parse_url($finish_url);
			$page = geturl($Url["host"], defport($Url), $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $ref, $cookies, 0, 0, $_GET["proxy"],$pauth);
			is_page($page);
			$finish_url=trim(cut_str($page,'Location: /',"\n"));
			if (!$finish_url) html_error ('Error get location 2');
			$Url=parse_url($ref.$finish_url);
			$page = geturl($Url["host"], defport($Url), $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $ref, $cookies, 0, 0, $_GET["proxy"],$pauth);
			is_page($page);
			$tmp=cut_str($page,'Download link',true);
			$tmp=cut_str($tmp,'href="','"');
			if (!$tmp) html_error ('Error get download url <br>'.$page);
			$download_link=$tmp;
			$tmp=cut_str($page,'Delete link',true);
			$tmp=cut_str($tmp,'href="','"');
			$delete_link=$tmp;
	}
// sert 27.12.2008
// Member upload plugins by Baking 03/07/2009 22:11
?>