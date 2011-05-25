<?php
####### Account Info. ###########
$fs_login = "";
$fs_pass = "";
##############################

$not_done=true;
$continue_up=false;
if ($fs_login & $fs_pass){
	$_REQUEST['bin_login'] = $fs_login;
	$_REQUEST['bin_pass'] = $fs_pass;
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
<?
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
			$Url=parse_url('http://freakshare.com/login.html');
			if ($_REQUEST['action'] == "FORM")
			{
				$post["user"]=$_REQUEST['bin_login'];
				$post["pass"]=$_REQUEST['bin_pass'];
				$post["submit"]="Login";
			
			$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"] . ($Url["query"] ? "?" . $Url["query"] : ""), "http://freakshare.com/login.html", 0, $post, 0, $_GET["proxy"], $pauth);
			$cookies=GetCookies($page);
			}	
?>
<script>document.getElementById('info').style.display='none';</script>
<div id=info width=100% align=center>Retrive upload ID</div> 
<?php
			$Url=parse_url('http://freakshare.com/');
			$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"] . ($Url["query"] ? "?" . $Url["query"] : ""), "http://freakshare.com/login.html", $cookies, $post, 0, $_GET["proxy"], $pauth);
			
			$serverurl = cut_str($page,'<form action="','" method="post" id="uploadform"');
			$uuid = "<script type=\"text/javascript\">
			var uuid;
			for (i = 0; i < 32; i++)
			{
				uuid += Math.floor(Math.random() * 16).toString(16);
			}

			document.write (uuid)</script>";
			
			$url2=''.$serverurl.'?X-Progress-ID='.$uuid.'';
			$url=parse_url($url2);
			$post["APC_UPLOAD_PROGRESS"] = cut_str($page,'name="APC_UPLOAD_PROGRESS" id="progress_key"  value="','"');
			$post["APC_UPLOAD_USERGROUP"] = cut_str($page,'name="APC_UPLOAD_USERGROUP" id="usergroup_key"  value="','"');
			$post["UPLOAD_IDENTIFIER"]= cut_str($page,'name="UPLOAD_IDENTIFIER" value="','"');
			$upfiles=upfile($url["host"],$url["port"] ? $url["port"] : 80, $url["path"].($url["query"] ? "?".$url["query"] : ""),"http://freakshare.com/", 0, $post, $lfile, $lname, "file[]", "file[]");
			$locat=trim(cut_str($upfiles,'Location: ',"\n"));
			$Url=parse_url($locat);
			$page = geturl($Url["host"],  80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 'http://freakshare.net/', $cookies, 0, 0, $_GET["proxy"],$pauth);
			is_page($page);
?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?php
			$ddl=cut_str($page,'<td><input type="text" value="','"');
			$del=cut_str($page,'value="http://freakshare.com/delete/','"');
			$download_link=$ddl;
			$delete_link= 'http://freakshare.net/delete/'.$del;
}
//created by nastrove
?>