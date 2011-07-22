<?php
####### Account Info. ###########
$datei_login = "";
$datei_pass = "";
##############################

$not_done=true;
$continue_up=false;
if ($datei_login & $datei_pass){
	$_REQUEST['bin_login'] = $datei_login;
	$_REQUEST['bin_pass'] = $datei_pass;
	$_REQUEST['action'] = "FORM";
	echo "<b><center>Let it empty for free user</center></b>\n";
}
if ($_REQUEST['action'] == "FORM")
    $continue_up=true;
else{
?>
<table border=1 style="width:270px;" cellspacing=0 align=center>
<form method=post>
<input type=hidden name=action value='FORM'><input type=hidden value=uploaded value'<?php $_REQUEST[uploaded]?>'>
<input type=hidden name=filename value='<?php echo base64_encode($_REQUEST[filename]); ?>'>
<tr><td nowrap>&nbsp;Login<td>&nbsp;<input name=bin_login value='' style="width:160px;">&nbsp;</tr>
<tr><td nowrap>&nbsp;Password<td>&nbsp;<input name=bin_pass value='' style="width:160px;">&nbsp;</tr>
<tr><td colspan=2 align=center>Let it empty for free user</tr>
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
<div id=login width=100% align=center></div> 
<?php
			$page = geturl("datei.to", 80, "/", 0, 0, 0, 0);
			$cookie=GetCookies($page);
			$cookies = "".$cookie."; Username=".$_REQUEST['bin_login']."; Password=".md5(trim($_REQUEST['bin_pass']))."";
			$Url=parse_url('http://datei.to/ajax/account.php');
			if ($_REQUEST['action'] == "FORM")
			{
				$post["Menue"]=0;
				$post["Seite"]="";
			
			$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"] . ($Url["query"] ? "?" . $Url["query"] : ""), "http://datei.to/account", $cookies, $post, 0, $_GET["proxy"], $pauth);
			
			$Url=parse_url('http://datei.to/upload');
			$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"] . ($Url["query"] ? "?" . $Url["query"] : ""), 0, $cookies, 0, 0, $_GET["proxy"], $pauth);
			is_present($page, ' Login', 'Your login details are wrong!');
			$UserID = cut_str($page,'name="UserID" value="','">');
			$Server = cut_str($page,'Datei.upload_server_name = "','";');
			}	
?>
<script>document.getElementById('info').style.display='none';</script>
<div id=info width=100% align=center>Retrive upload ID</div> 
<?php
			$Url=parse_url('http://datei.to/ajax/upload_start.php');
            $post["UserID"] = $UserID;
            $post["upload_file[]"] = $lname;
            $post["Server"] = $Server;
			
			$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"] . ($Url["query"] ? "?" . $Url["query"] : ""), "http://datei.to/upload", $cookies, $post, 0, $_GET["proxy"], $pauth);
			$upload_id = cut_str($page,'Datei.startUpload("','"');
			
			$cookies2 = "Username=".$_REQUEST['bin_login']."; Password=".md5(trim($_REQUEST['bin_pass']))."";
			$url=parse_url('http://'.$Server.'.datei.to/cgi-bin/Datei.pl?upload_id='.$upload_id.'');
			$fpost["UserID"] = $UserID;
			$upfiles = upfile($url["host"], $url["port"] ? $url["port"] : 80, $url["path"] . ($url["query"] ? "?" . $url["query"] : ""), 0, $cookies2, $fpost, $lfile, $lname, "upfile_1");
			
			$Url=parse_url('http://datei.to/ajax/upload_finish.php?upload_id='.$upload_id.'&Server='.$Server.'');
			$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"] . ($Url["query"] ? "?" . $Url["query"] : ""), "http://datei.to/upload", $cookies, 0, 0, $_GET["proxy"], $pauth);
?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?php
			$ddl = cut_str($page,'id="DateiLink" value="','"');
			$del = cut_str($page,'id="RemoveLink" value="','"');
			if (!empty($ddl))
			$download_link = $ddl;
			else
			html_error ('Didn\'t find downloadlink!');
			if (!empty($del))
			$delete_link= $del;
			else
			html_error ('Didn\'t find deletelink!');
}       
?>