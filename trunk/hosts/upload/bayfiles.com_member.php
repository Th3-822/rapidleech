<?php
####### Account Info. ###########
$bayfiles_login = '';
$bayfiles_pass = '';
#################################

$not_done=true;
$continue_up=false;
if ($bayfiles_login && $bayfiles_pass){
	$_REQUEST['my_login'] = $bayfiles_login;
	$_REQUEST['my_pass'] = $bayfiles_pass;
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
<tr><td nowrap>&nbsp;User*<td>&nbsp;<input type=text name=my_login value='' style="width:160px;" />&nbsp;</tr>
<tr><td nowrap>&nbsp;Password*<td>&nbsp;<input type=password name=my_pass value='' style="width:160px;" />&nbsp;</tr>
<tr><td colspan=2 align=center><input type=submit value='Upload' /></tr>
<tr><td colspan=2 align=center><small>*You can set it as default in <b><?php echo $page_upload["bayfiles.com_member"]; ?></b></small></tr>
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
<div id=login width=100% align=center>Login to Bayfiles.com</div>
<?php 
	$loginUrl = parse_url("http://api.bayfiles.com/v1/account/login/".$_REQUEST['my_login']."/".$_REQUEST['my_pass']);
	$page = geturl($loginUrl['host'], defport($loginUrl), $loginUrl['path']);
	$json = json_decode(getBody($page), true);		
	if(!empty($json['error']))
		html_error($json['error']);
	
	$loginSession = $json['session'];
?>
<script>document.getElementById('login').style.display='none';</script>
<div id=info width=100% align=center>Retrieve upload ID</div>
<?php 

	$uploadUrl = parse_url("http://api.bayfiles.com/v1/file/uploadUrl?session=".$loginSession);
	$page = geturl($uploadUrl['host'], defport($loginUrl), $uploadUrl['path']."?".$uploadUrl['query']);
	$json = json_decode(getBody($page), true);
	if(!empty($json['error']))
		html_error($json['error']);
		
	$url = parse_url($json['uploadUrl']);
	
?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?php 		

	$upfiles = upfile($url["host"], defport($url), $url["path"].($url["query"] ? "?".$url["query"] : ""), 0, $cookie, $post, $lfile, $lname, "file");	
	is_page($upfiles);
	$page = json_decode(getBody($upfiles), true);
		
	echo "<h3><font color='green'>File successfully uploaded to your account</font></h3>";
	$download_link = $page['downloadUrl'];
}

function getBody($string) {
	list(, $body) = explode("\r\n\r\n", $string);
	return $body;
}

//Seeyabye 27/01/2012

?>
