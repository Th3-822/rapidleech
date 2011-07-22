<?php
####### Account Info. ###########
$hs_login = "";
$hs_pass = "";
##############################

$not_done=true;
$continue_up=false;
if ($hs_login & $hs_pass){
	$_REQUEST['bin_login'] = $hs_login;
	$_REQUEST['bin_pass'] = $hs_pass;
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
			$Url=parse_url('http://hulkshare.com/');
			if ($_REQUEST['action'] == "FORM")
			{
				$post["op"]="login";
				$post["redirect"]="";
				$post["login"]=trim($_REQUEST['bin_login']);
				$post["password"]=trim($_REQUEST['bin_pass']);
			
			$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"] . ($Url["query"] ? "?" . $Url["query"] : ""), 0, 0, $post, 0, $_GET["proxy"], $pauth);
			is_present($page, 'Incorrect Login or Password', 'Your login details are wrong!');
			$cookies = GetCookies($page);
			}	
?>
<script>document.getElementById('info').style.display='none';</script>
<div id=info width=100% align=center>Retrive upload ID</div> 
<?php
			$Url=parse_url('http://hulkshare.com/');
			$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"] . ($Url["query"] ? "?" . $Url["query"] : ""), 0, $cookies, 0, 0, $_GET["proxy"], $pauth);
			for ($i=0;$i<12;$i++)
			{
				$uid+=''+floor(rand() * 10);
			}
			preg_match('#<form name="file" enctype="multipart\/form-data" action="(http:\/\/upload([0-9]+)\.hulkshare\.com\/cgi-bin\/upload\.cgi\?upload_id=)"#', $page,$urlup);
			$url2 = (''.$urlup[1].''.$uid.'&js_on=1&utype=reg&upload_type=file');
			$url = parse_url($url2);
			$post["upload_type"]= "file"; 
			$post["sess_id"] = cut_str($page,'name="sess_id" value="','"');
			$post["srv_tmp_url"] = cut_str($page,'name="srv_tmp_url" value="','"'); 
			$post["file_1"]= "Content-Type: application/octet-stream";
			$post["file_0_descr"]= "";
			$post["link_rcpt"]= "";
			$post["link_pass"]= "";
			$post["tos"]= "1";
			$post["upload_btn"]= "";
			
			$cookiesn = "".$cookies.";";
			$upfiles = upfile($url["host"], $url["port"] ? $url["port"] : 80, $url["path"] . ($url["query"] ? "?" . $url["query"] : ""), "http://hulkshare.com/", $cookies, $post, $lfile, $lname, "file_0");
			preg_match('#<textarea name=\'fn\'>([a-z0-9]+)<#', $upfiles,$link);
?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?php
			if (empty($link[1]))
			html_error("Download link not found, try looking in your account.", 0);
			else
			$download_link = "http://hulkshare.com/".$link[1]."/".$lname."";
}       
?>