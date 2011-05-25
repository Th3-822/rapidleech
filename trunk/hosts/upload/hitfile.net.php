<?php

####### Account Info. ###########
$hitfile_login = ""; //Set your hitfile user
$hitfile_pass = ""; //Set your hitfile password
##############################

$not_done=true;
$continue_up=false;
if ($hitfile_login && $hitfile_pass){
	$_REQUEST['my_login'] = $hitfile_login;
	$_REQUEST['my_pass'] = $hitfile_pass;
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
<tr><td colspan=2 align=center><small>*You can set it as default in <b><?php echo $page_upload["hitfile.net"]; ?></b></small></tr>
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
<div id=login width=100% align=center>Login to hitfile.net</div>
<?php 
			$page = geturl("hitfile.net", 80, "/", 0, 0, 0, 0, $_GET["proxy"], $pauth);
			is_page($page);			
			$cookie = GetCookies($page);
			
			$postlog = array();
			$postlog['user[login]'] = $_REQUEST['my_login'];
			$postlog['user[pass]'] = $_REQUEST['my_pass'];
			$postlog['user[memory]'] = 'on';
			$postlog['user[submit]'] = 'Login';
			
			$Url=parse_url('http://hitfile.net/user/login');
			$page = geturl($Url["host"], $url["port"] ? $url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 'http://hitfile.net/', $cookie, $postlog, 0, $_GET["proxy"], $pauth);
			is_page($page);		
			$cookie = GetCookies($page);
			
			is_notpresent($cookie, 'sid=', 'Error logging in - hitfile.net');
			
			$Url=parse_url('http://hitfile.net/');
			$page = geturl($Url["host"], $url["port"] ? $url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), '', $cookie, $postlog, 0, $_GET["proxy"], $pauth);
			is_page($page);
			preg_match('/flashvars="cancelLang=Cancel&browserLang=Add&downloadLang=Upload&maxSize=(.*?)&domain=main&urlSite=(.*?)&userId=(.*?)&apptype=(.*?)"/', $page, $hitup);
			
		
			
?>
<script>document.getElementById('login').style.display='none';</script>
<div id=info width=100% align=center>Retrive test</div>
<script>document.getElementById('info').style.display='none';</script>
<?php 		
			$upload_url = $hitup[2];
			$agent = 'Shockwave Flash';
			$data['Filename'] = $lname;
			$data['stype'] = 'null';
			$data['apptype'] = $hitup[4];
			$data['user_id'] = $hitup[3];
			$data['id'] = 'null';
			$url = parse_url($upload_url);
			$upfiles = upfile($url["host"],$url["port"] ? $url["port"] : 80, $url["path"].($url["query"] ? "?".$url["query"] : ""), '', $cookie, $data, $lfile, $lname, "Filedata", "", 0, 0, $agent);
?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?php 	
			is_page($upfiles);
			preg_match('/"result":true,"id":"(.*?)","message":"Everything is ok"/', $upfiles, $link);
			if(!empty($link[1]))
			{
				$download_link = 'http://hitfile.net/'.$link[1].'/'.$lname.'.html';	
			}
			else
			{
	    			html_error("Error - Unable to retrive the download link, please try again later.");
	 		}
	}

?>
