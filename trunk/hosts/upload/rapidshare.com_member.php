<?php

####### Premium Account Info. ###########
$rapidshare_login = ""; //Set your username
$rapidshare_pass = ""; //Set your password
##############################

$not_done=true;
$continue_up=false;
if ($rapidshare_login && $rapidshare_pass) {
	$_REQUEST['up_login'] = $rapidshare_login;
	$_REQUEST['up_pass'] = $rapidshare_pass;
	$_REQUEST['action'] = "FORM";
	echo "<b><center>Using Default login/pass.</center></b>\n";
}
if ($_REQUEST['action'] == "FORM")
	$continue_up=true;
else {
?>
<table border="0" style="width:270px;margin:auto;" cellspacing="0">
<form method="POST">
<input type="hidden" name="action" value="FORM" />
<tr><td style="white-space:nowrap;">&nbsp;Login*<td>&nbsp;<input type="text" name="up_login" value="" style="width:160px;" />&nbsp;</tr>
<tr><td style="white-space:nowrap;">&nbsp;Password*<td>&nbsp;<input type="password" name="up_pass" value="" style="width:160px;" />&nbsp;</tr>
<tr><td colspan="2" align="center"><input type="submit" value="Upload" /></tr>
<tr><td colspan="2" align="center"><small>*You can set it as default in <b><?php echo $page_upload["rapidshare.com_member"]; ?></b></small></tr>
</form>
</table>

<?php
	}

if ($continue_up)
	{
		$not_done=false;
?>
<table style="width:600px;margin:auto;">
</td></tr>
<tr><td align="center">
<div id="login" style="width:100%;text-align:center;">Login to Rapidshare</div>
<?php
	if (!empty($_REQUEST['up_login']) && !empty($_REQUEST['up_pass'])) {
		$post['sub'] = 'getaccountdetails';
		$post['login'] = urlencode($_REQUEST['up_login']);
		$post['password'] = urlencode($_REQUEST['up_pass']);

		$page = geturl("api.rapidshare.com", 80, "/cgi-bin/rsapi.cgi", 0, 0, $post);is_page($page);

		is_present($page, "ERROR: IP blocked.", "Rapidshare has locked your IP. (Too many failed logins sended)");
		is_present($page, "ERROR: Login failed. Login data invalid.", "Invalid Login.");
		is_present($page, "ERROR: Login failed. Password incorrect or account not found.", "Login failed. User/Password incorrect or could not be found.");
		is_present($page, "ERROR: Login failed. Account not validated.", "Login failed. Account not validated.");
		is_present($page, "ERROR: Login failed. Account locked.", "Login failed. Account locked.");
		is_present($page, "ERROR: Login failed.", "Login failed. Invalid Login?");
	} else {
		html_error ("Login not found or empty", 0);
	}
?>
<script type="text/javascript">document.getElementById('login').style.display='none';</script>
<div id="info" style="width:100%;text-align:center;">Retrive upload Server</div>
<?php
		$nextsrv = 'http://api.rapidshare.com/cgi-bin/rsapi.cgi?sub=nextuploadserver';
		if (!$rsrv = @file_get_contents($nextsrv)) {
			$ch = curl_init();
			curl_setopt ($ch, CURLOPT_URL, $nextsrv);
			curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 15);
			curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
			$rsrv = curl_exec($ch);
			curl_close($ch);
		}
		$url_action = "http://rs{$rsrv}.rapidshare.com/cgi-bin/rsapi.cgi";
		$post = array();
		$post['sub'] = 'upload';
		$post['login'] = $_REQUEST['up_login'];
		$post['password'] = $_REQUEST['up_pass'];
?>
<script type="text/javascript">document.getElementById('info').style.display='none';</script>
<?php
		$url = parse_url($url_action);
		$upfiles = upfile($url["host"],$url["port"] ? $url["port"] : 80, $url["path"].($url["query"] ? "?".$url["query"] : ""),0, 0, $post, $lfile, $lname, "filecontent", "", $proxy, $pauth);
?>
<script type="text/javascript">document.getElementById('progressblock').style.display='none';</script>
<?php
		is_page($upfiles);

		if (!preg_match('@COMPLETE\n(\d+),([^,]+)@i', $upfiles, $link)) html_error ("Download link not found.", 0);
		$download_link = "https://rapidshare.com/files/{$link[1]}/{$link[2]}";
	}

//[25-03-2011]  Regex for get links fixed by Th3-822.
//[10-6-2011]  Some fixes in plugin (added a '&', editing html and removed '_v1' from nextsrv). && Added login check function. - Th3-822
//[03-9-2011]  Removed non member upload, added sufix '_member' & Fixed upload link and regexp for dlink, deletion link removed. - Th3-822

?>