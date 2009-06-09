<?php

####### Account Info. ###########
$depositfiles_com_login = ""; //Set you username
$depositfiles_com_pass = ""; //Set your password
##############################

$not_done=true;
$continue_up=false;
if ($depositfiles_com_login & $depositfiles_com_pass){
	$_REQUEST['my_login'] = $depositfiles_com_login;
	$_REQUEST['my_pass'] = $depositfiles_com_pass;
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
<tr><td colspan=2 align=center><small>*You can set it as default in <b><?php echo $page_upload["depositfiles.com_premium"]; ?></b></small></tr>
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
<div id=login width=100% align=center>Login to Depositfiles.com</div>
<?php 
	        $post['login'] = $_REQUEST['my_login'];
            $post['password'] = $_REQUEST['my_pass'];
			$post['go'] = 1;
			
            $page = geturl("depositfiles.com", 80, "/en/login.php?return=/en/", 0, 0, $post);			
			is_page($page);
			
			$cookies = GetCookies($page);
			preg_match('/autologin=(.*?);/i', $cookies, $cookie);
			$cookie = 'autologin='.$cookie[1];
?>
<script>document.getElementById('login').style.display='none';</script>
<div id=info width=100% align=center>Retrive upload ID</div>
<?php 
			$url_id = 'http://depositfiles.com/en/';
			$Url = parse_url($url_id);
			$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, $cookie, 0, 0, $_GET["proxy"],$pauth);
			is_page($page);
			
			preg_match('/target="uploadframe".*action="(.*?)"/i', $page, $upurl);
			preg_match('/MAX_FILE_SIZE.*value="(.*)"/i', $page, $max);
			preg_match('/name="UPLOAD_IDENTIFIER".*value="(.*)"/i', $page, $iden);
			
			$url_action = $upurl[1];
			$fpost['MAX_FILE_SIZE'] = $max[1];
			$fpost['UPLOAD_IDENTIFIER'] = $iden[1];
			$fpost['go'] = 1;
			$fpost['agree'] = 1;
			
			
?>
<script>document.getElementById('info').style.display='none';</script>
<?php 		
			$url = parse_url($url_action);
			$upagent = "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.0.1) Gecko/2008070208 Firefox/3.0.1";
			$upfiles = upfile($url["host"],$url["port"] ? $url["port"] : 80, $url["path"].($url["query"] ? "?".$url["query"] : ""),0, $cookie, $fpost, $lfile, $lname, "files");
?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?php 	
			is_page($upfiles);

			preg_match('/ud_download_url\s*=.*\'(.*)\'/i', $upfiles, $flink);
			preg_match('/ud_delete_url\s*=.*\'(.*)\'/i', $upfiles, $dlink);
			$download_link = $flink[1];
			$delete_link = $dlink[1];
	}
?>