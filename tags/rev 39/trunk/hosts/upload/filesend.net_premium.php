<?php

####### Account Info. ###########
$filesend_net_login = ""; //Set you username
$filesend_net_pass = ""; //Set your password
##############################

$not_done=true;
$continue_up=false;
if ($filesend_net_login & $filesend_net_pass){
	$_REQUEST['my_login'] = $filesend_net_login;
	$_REQUEST['my_pass'] = $filesend_net_pass;
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
<tr><td colspan=2 align=center><small>*You can set it as default in <b><?php echo $page_upload["filesend.net_premium"]; ?></b></small></tr>
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
<div id=login width=100% align=center>Login to Filesend.net</div>
<?php 
	        $post['username'] = $_REQUEST['my_login'];
            $post['password'] = $_REQUEST['my_pass'];
            $post['page'] = 'index.php';
			$post['login'] = '+++';
			
            $page = geturl("www.filesend.net", 80, "/handlelogin.php", 0, 0, $post);			
			is_page($page);
			
			$cookies = GetCookies($page);
			preg_match('/premium.*?;/i', $cookies, $cookie);
			$cookie = $cookie[0];
?>
<script>document.getElementById('login').style.display='none';</script>
<div id=info width=100% align=center>Retrive upload ID</div>
<?php 
			$url_id = "http://dl3.filesend.net/ubr_link_upload.php?config_file=ubr_default_configc.php&rnd_id=".rand(1000000000000, 9999999999999);
			$Url = parse_url($url_id);
			$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, $cookie, 0, 0, $_GET["proxy"],$pauth);
			is_page($page);
			preg_match('/startUpload\("(.*?)"/i', $page, $id);
			
			$url_action = "http://dl3.filesend.net/cgi-bin/uploadc.cgi?upload_id=".$id[1];
			$fpost['confirm'] = 'on';
			
?>
<script>document.getElementById('info').style.display='none';</script>
<?php 		
			$url = parse_url($url_action);
			$upagent = "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.0.1) Gecko/2008070208 Firefox/3.0.1";
			$upfiles = upfile($url["host"],$url["port"] ? $url["port"] : 80, $url["path"].($url["query"] ? "?".$url["query"] : ""),$login_url, $cookie, $fpost, $lfile, $lname, "upfile_0");
?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?php 	
			is_page($upfiles);

			$redir_url = "http://dl3.filesend.net/ubr_finished.php?upload_id=".$id[1];
			$Url = parse_url($redir_url);
			$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, $cookie, 0, 0, $_GET["proxy"],$pauth);
			is_page($page);
			preg_match('/location=\'(.*)\';/i', $page, $multi);
			
			$Url = parse_url($multi[1]);
			$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, $cookie, 0, 0, $_GET["proxy"],$pauth);
			is_page($page);

			preg_match('%Download Link:.*(http://.*?)"%i', $page, $flink);
			preg_match('%Delete Link:.*(http://.*?)"%i', $page, $dlink);
			$download_link = $flink[1];
			$delete_link = $dlink[1];
	}
?>