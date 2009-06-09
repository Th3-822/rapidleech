<?php
(!extension_loaded('curl')) ? html_error("Error: cURL is not enabled", 0) : '';

####### Account Info. ###########
$adrive_com_login = ""; //Set you email
$adrive_com_pass = ""; //Set your password
##############################

function get_ssl_page($url, $post='', $cookie=''){
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_COOKIE, $cookie);
	//curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HEADER, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
	$page = curl_exec($ch);
	curl_close($ch);
	return $page;
}

$not_done=true;
$continue_up=false;
if ($adrive_com_login & $adrive_com_pass){
	$_REQUEST['my_login'] = $adrive_com_login;
	$_REQUEST['my_pass'] = $adrive_com_pass;
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
<tr><td nowrap>&nbsp;Email*<td>&nbsp;<input type=text name=my_login value='' style="width:160px;" />&nbsp;</tr>
<tr><td nowrap>&nbsp;Password*<td>&nbsp;<input type=password name=my_pass value='' style="width:160px;" />&nbsp;</tr>
<tr><td colspan=2 align=center><input type=submit value='Upload' /></tr>
<tr><td colspan=2 align=center><small>*You can set it as default in <b><?php echo $page_upload["adrive.com"]; ?></b></small></tr>
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
<div id=login width=100% align=center>Login to ADrive.com</div>
<?php 
			$page = get_ssl_page("https://www.adrive.com/login/login", "email=".$_REQUEST['my_login']."&passwrd=".$_REQUEST['my_pass']."&forcelogout=1");
			$cookies = GetCookies($page);
			preg_match('/symfony=(.*?);/i', $cookies, $cook);
			$cookie = 'symfony='.$cook[1].'; '.'orig_ref=https://www.adrive.com/login';
?>
<script>document.getElementById('login').style.display='none';</script>
<div id=info width=100% align=center>Retrive upload ID</div>
<?php 
			$Url = parse_url("http://www.adrive.com/home/httpuploadfile?dir=%3E");
			$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, $cookie, 0, 0, $_GET["proxy"],$pauth);
			preg_match('/action="(.*?)"/i', $page, $up_url);
			$url_action = 'http://www.adrive.com'.$up_url[1];
			$fpost['directory'] = '/';
			$fpost['commit'] = 'Upload';
?>
<script>document.getElementById('info').style.display='none';</script>
<?php 		
			$url = parse_url($url_action);
			$upfiles = upfile($url["host"],$url["port"] ? $url["port"] : 80, $url["path"].($url["query"] ? "?".$url["query"] : ""),"http://www.adrive.com/home/httpuploadfile?dir=%3E", $cookie, $fpost, $lfile, $lname, "userfile[]");
?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?php 	
			is_page($upfiles);
			$Url = parse_url("http://www.adrive.com/login/logout");
			$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, $cookie, 0, 0, $_GET["proxy"],$pauth);
			html_error("Finished, Go to your account to see Download-URL.", 0);
	}
?>