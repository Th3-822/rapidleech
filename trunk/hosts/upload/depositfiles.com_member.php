<?php
####### Account Info. ###########
$depositfiles_login = "";
$depositfiles_pass = "";
##############################

$not_done=true;
$continue_up=false;
if ($depositfiles_login & $depositfiles_pass){
	$_REQUEST['bin_login'] = $depositfiles_login;
	$_REQUEST['bin_pass'] = $depositfiles_pass;
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
			$Url=parse_url('http://depositfiles.com/de/login.php?return=/de/signup.php');
			if ($_REQUEST['action'] == "FORM")
			{
				$post["go"]=1;
				$post["login"]=$_REQUEST['bin_login'];
				$post["password"]=$_REQUEST['bin_pass'];
			
			$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"] . ($Url["query"] ? "?" . $Url["query"] : ""), "http://oron.com/login", 0, $post, 0, $_GET["proxy"], $pauth);
			if (!preg_match('#Set-Cookie: autologin=([a-zA-Z0-9]+);#', $page))
				html_error ('Not logged in. Check your login details!');
			$cookies = GetCookies($page);
			}	
?>
<script>document.getElementById('info').style.display='none';</script>
<div id=info width=100% align=center>Retrive upload ID</div> 
<?php
			$Url=parse_url('http://depositfiles.com/de/');
			$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"] . ($Url["query"] ? "?" . $Url["query"] : ""), "http://oron.com/login", $cookies, 0, 0, $_GET["proxy"], $pauth);
			
			$upload_form = cut_str($page,'id="upload_form" method="post" enctype="multipart/form-data" action="','"');
			$mfilesize = cut_str($page,'name="MAX_FILE_SIZE" value="','"');
			$uidentifier = cut_str($page,'name="UPLOAD_IDENTIFIER" value="','"');
			
			$url = parse_url($upload_form);
			$fpost["MAX_FILE_SIZE"]=$upload_form;
			$fpost["UPLOAD_IDENTIFIER"]=$uidentifier;
			$fpost["go"]=1;
			$fpost["agree"]=1;
			$fpost["padding"]="";
			$upfiles = upfile($url["host"], $url["port"] ? $url["port"] : 80, $url["path"] . ($url["query"] ? "?" . $url["query"] : ""), "http://depositfiles.com/de/", $cookies, $fpost, $lfile, $lname, "files");
?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?php
			$ddl = cut_str($upfiles,'parent.ud_download_url = \'','\';');
			$del = cut_str($upfiles,'parent.ud_delete_url = \'','\';');
			if (!empty($ddl))
			$download_link = $ddl;
			else
			html_error ('Didn\'t find downloadlink!');
			if (!empty($del))
			$delete_link= $del;
			else
			html_error ('Didn\'t find deletelink!');
}
/**
written by defport 22/05/2011
**/   
?>