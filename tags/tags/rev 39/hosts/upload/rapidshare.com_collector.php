<?php

####### Premium Account Info. ###########
$rapidshare_clogin = ""; //Set your RS collector account username
$rapidshare_cpass = ""; //Set your RS collector account password
##############################

$not_done=true;
$continue_up=false;
if ($rapidshare_clogin & $rapidshare_cpass){
	$_REQUEST['my_login'] = $rapidshare_clogin;
	$_REQUEST['my_pass'] = $rapidshare_cpass;
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
<tr><td colspan=2 align=center><small>*You can set it as default in <b><?php echo $page_upload["rapidshare.com_collector"]; ?></b></small></tr>
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
<div id=login width=100% align=center>Login to Rapidshare Premium</div>
<?php 

?>
<script>document.getElementById('login').style.display='none';</script>
<div id=info width=100% align=center>Retrive upload ID</div>
<?php
			$nextsrv = 'http://rapidshare.com/cgi-bin/rsapi.cgi?sub=nextuploadserver_v1';
			if (!$rsrv = @file_get_contents($nextsrv))
			{
				$ch = curl_init();
				curl_setopt ($ch, CURLOPT_URL, $nextsrv);
				curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 15);
				curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
				$rsrv = curl_exec($ch);
				curl_close($ch);
			}
			$url_action = "http://rs{$rsrv}.rapidshare.com/cgi-bin/upload.cgi";
			$post['freeaccountid'] = $_REQUEST['my_login'];
			$post['password'] = $_REQUEST['my_pass'];
			$post['rsapi_v1'] = '1';
?>
<script>document.getElementById('info').style.display='none';</script>
<?php 		
			$url = parse_url($url_action);
			$upagent = "RAPIDSHARE MANAGER Application Version: NOT INSTALLED VERSION STARTED";
			$upfiles = upfile($url["host"],$url["port"] ? $url["port"] : 80, $url["path"].($url["query"] ? "?".$url["query"] : ""),0, 0, $post, $lfile, $lname, "filecontent");
?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?php 	
			is_page($upfiles);
			preg_match('%http://rapidshare\.com/((?!killcode).)+html%i', $upfiles, $flink);
			preg_match('%http://rapidshare\.com/.*killcode.*%i', $upfiles, $dlink);
			$download_link = trim($flink[0]);
			$delete_link = trim($dlink[0]);
	}
?>