<?php

####### Account Info. ###########
$gigasize_login = ""; //Set you username
$gigasize_pass = ""; //Set your password
##############################

$not_done=true;
$continue_up=false;
if ($gigasize_login & $gigasize_pass){
	$_REQUEST['my_login'] = $gigasize_login;
	$_REQUEST['my_pass'] = $gigasize_pass;
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
<tr><td nowrap>&nbsp;Login*<td>&nbsp;<input type=text name=my_login value='' style="width:160px;" />&nbsp;</tr>
<tr><td nowrap>&nbsp;Password*<td>&nbsp;<input type=password name=my_pass value='' style="width:160px;" />&nbsp;</tr>
<tr><td colspan=2 align=center><input type=submit value='Upload' /></tr>
<tr><td colspan=2 align=center><small>*You can set it as default in <b><?php echo $page_upload["gigasize.com_premium"]; ?></b></small></tr>
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
<div id=login width=100% align=center>Login to Gigasize</div>
<?php 
	        $post['uname'] = $_REQUEST['my_login'];
            $post['passwd'] = $_REQUEST['my_pass'];
            $post['d'] = 'Login';
			$post['login'] = '1';
			
            $page = geturl("www.gigasize.com", 80, "/login.php", 0, 0, $post);			
			is_page($page);
			$cookie = GetCookies($page);
?>
<script>document.getElementById('login').style.display='none';</script>
<div id=info width=100% align=center>Retrive upload ID</div>
<?php 
			$login_url = "http://www.gigasize.com/myfiles.php";
			$Url = parse_url($login_url);
			$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, $cookie, 0, 0, $_GET["proxy"],$pauth);
			is_page($page);
			preg_match('/UPLOAD_IDENTIFIER.*value="(.*)"/i', $page, $id);
			preg_match('%(/up\.php.*?)"%i', $page, $act);
			$url_action = "http://www.gigasize.com".$act[1];
			$fpost['UPLOAD_IDENTIFIER'] = $id[1];
			$fpost['titlel'] = '';
			$fpost['description'] = '';
			$fpost['accept'] = '1';
?>
<script>document.getElementById('info').style.display='none';</script>
<?php 		
			$url = parse_url($url_action);
			$upagent = "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.0.1) Gecko/2008070208 Firefox/3.0.1";
			$upfiles = upfile($url["host"],$url["port"] ? $url["port"] : 80, $url["path"].($url["query"] ? "?".$url["query"] : ""),$login_url, $cookie, $fpost, $lfile, $lname, "file_0", "file_1");
?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?php 	
			is_page($upfiles);
			preg_match('/sid=(.*)&/i', $act[1], $sid);
			$post = array();
			$post['sid'] = $sid[1];
			$page = geturl("www.gigasize.com", 80, "/upload_progress.php", 0, 0, $post);
			preg_match('/done (.*)-- *(.*)--/i', $page, $query);
			$dl_page = 'http://www.gigasize.com/index.php?f='.$query[1].'--&pass='.$query[2].'--';
			
			$Url = parse_url($dl_page);
			$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, $cookie, 0, 0, $_GET["proxy"],$pauth);
			preg_match('%<a.*(http://.*get\.php\?.*?)"%i', $page, $flink);
			$download_link = $flink[1];
	}
?>