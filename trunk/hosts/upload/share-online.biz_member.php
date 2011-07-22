<?php
####### Account Info. ###########
$so_login = "";
$so_pass = "";
##############################

$not_done=true;
$continue_up=false;
if ($so_login & $so_pass){
	$_REQUEST['bin_login'] = $so_login;
	$_REQUEST['bin_pass'] = $so_pass;
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
			$Url=parse_url('http://www.share-online.biz/upv3_session.php');
			if ($_REQUEST['action'] == "FORM")
			{
				$post["username"]=trim($_REQUEST['bin_login']);
				$post["password"]=trim($_REQUEST['bin_pass']);
			
			$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"] . ($Url["query"] ? "?" . $Url["query"] : ""), 0, 0, $post, 0, $_GET["proxy"], $pauth);
			is_present($page, 'EXCEPTION username or password invalid', 'Your login details are wrong!');
			preg_match('#([a-zA-Z0-9]+);(dlw([0-9]+)-[0-9]\.share-online\.biz\/upv3\.php)#', $page,$session);
			}	
?>
<script>document.getElementById('info').style.display='none';</script>
<div id=info width=100% align=center>Retrive upload ID</div> 
<?php
			$url=parse_url("http://".$session[2]."");
            $post["username"] = trim($_REQUEST['bin_login']);
            $post["password"] = trim($_REQUEST['bin_pass']);
            $post["upload_session"] = $session[1]; 
            $post["chunk_no"]= 1; 
			$post["chunk_number"]= 1; 
			$post["filesize"]= filesize($lfile);
			$post["finalize"]= 1;
			
			$upfiles = upfile($url["host"], $url["port"] ? $url["port"] : 80, $url["path"] . ($url["query"] ? "?" . $url["query"] : ""), 0, 0, $post, $lfile, $lname, "fn");
			preg_match('#(http:\/\/www\.share-online\.biz\/dl\/.+);.+;#', $upfiles,$dllink);
			if (!empty($dllink[1]))
			$download_link = $dllink[1];
			else
			html_error ('Didn\'t find downloadlink!');
?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?php
}       
?>