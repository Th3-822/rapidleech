<?php

####### Free Account Info. ###########
$badongo_login = ""; //Set you username
$badongo_pass = ""; //Set your password
##############################

$not_done=true;
$continue_up=false;
if ($badongo_login & $badongo_pass){
	$_REQUEST['my_login'] = $badongo_login;
	$_REQUEST['my_pass'] = $badongo_pass;
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
<tr><td colspan=2 align=center><small>*You can set it as default in <b><?php echo $page_upload["badongo.com_FreeUser"]; ?></b></small></tr>
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
<div id=login width=100% align=center>Login to Badongo</div>
<?php
			$page = geturl("www.badongo.com", 80, "/login", 0, 0, 0, 0, "");
			is_page($page);
			
			preg_match('/cap_secret.*value="(.*)"/i', $page, $cap);
	        $post['username'] = $_REQUEST['my_login'];
            $post['password'] = $_REQUEST['my_pass'];
            $post['do'] = 'login';
			$post['cou'] = 'en';
			$post['cap_secret'] = $cap[1];
			
            $page = geturl("www.badongo.com", 80, "/", 0, 0, $post);			
			is_page($page);
			$cookie = GetCookies($page);
?>
<script>document.getElementById('login').style.display='none';</script>
<div id=info width=100% align=center>Retrive upload ID</div>
<?php
			$login_url = "http://upload.badongo.com/upload_single/f/?cou=en&k=member";
			$Url = parse_url($login_url);
			$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, $cookie, 0, 0, $_GET["proxy"],$pauth);
			is_page($page);
			preg_match('/UPLOAD_IDENTIFIER.*value="(.*)"/i', $page, $id);
			preg_match('/toc.*value="(.*)"/i', $page, $toc);
			$url_action = "http://upload.badongo.com/index.php?page=upload_s&s=&cou=en";
			$post['UPLOAD_IDENTIFIER'] = $id[1];
			$post['affiliate'] = $cap[1];
			$post['sub'] = 'Upload';
			$post['toc'] = $toc[1];
?>
<script>document.getElementById('info').style.display='none';</script>
<?php			
			$url = parse_url($url_action);
			$upagent = "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.0.1) Gecko/2008070208 Firefox/3.0.1";
			$upfiles = upfile($url["host"],$url["port"] ? $url["port"] : 80, $url["path"].($url["query"] ? "?".$url["query"] : ""),$login_url, $cookie, $post, $lfile, $lname, "filename");
?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?php		
			is_page($upfiles);
			//is_notpresent($upfiles,'batch_id','File not upload');
			preg_match('/&url=(.*?)&url_kill=(.*?)&/i', $upfiles, $flink);
			$download_link = urldecode($flink[1]);
			$delete_link = urldecode($flink[2]);
	}
?>