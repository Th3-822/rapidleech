<?php
####### Account Info. ###########
$upload_acc['lumfile_com']['user'] = ""; //Set your user
$upload_acc['lumfile_com']['pass'] = ""; //Set your password
##############################

$not_done=true;
$continue_up=false;
if ($upload_acc['lumfile_com']['user'] && $upload_acc['lumfile_com']['pass']) {
	$_REQUEST['login'] = $upload_acc['lumfile_com']['user'];
	$_REQUEST['password'] = $upload_acc['lumfile_com']['pass'];
	$_REQUEST['action'] = "FORM";
	echo "<b><center>Use Default login/pass.</center></b>\n";
}
if ($_REQUEST['action'] == "FORM")
	$continue_up=true;
else{
?>
<table border="0" style="width:270px;margin:auto;" cellspacing="0">
<form method="POST">
<input type="hidden" name="action" value="FORM" />
<tr><td style="white-space:nowrap;">&nbsp;Username*<td>&nbsp;<input type="text" name="login" value="" style="width:160px;" />&nbsp;</tr>
<tr><td style="white-space:nowrap;">&nbsp;Password*<td>&nbsp;<input type="password" name="password" value="" style="width:160px;" />&nbsp;</tr>
<tr><td colspan="2" align="center"><input type="submit" value="Upload" /></tr>
<tr><td colspan="2" align="center"><small>*You can set it as default in <b><?php echo basename(__FILE__); ?></b></small></tr>
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
<div id="login" style="width:100%;text-align:center;">Login to lumfile.com</div>
<?php
	$cookie = 'lang=english';
	if (!empty($_REQUEST['login']) && !empty($_REQUEST['password'])) {
		$post = array();
		$post['op'] = "login";
		$post['redirect'] = "";
		$post['login'] = $_REQUEST['login'];
		$post['password'] = $_REQUEST['password'];

		$page = geturl("lumfile.com", 80, "/", 'http://lumfile.com/', $cookie, $post, 0, $_GET["proxy"], $pauth);is_page($page);
		is_present($page, "Incorrect Username or Password", "Login failed: User/Password incorrect.");
		is_notpresent($page, 'Set-Cookie: xfss=', 'Error: Cannot find session cookie.');
		$cookie = "$cookie;" . GetCookies($page);
		$login = true;
	} else {
		echo "<b><center>Login not found or empty, using non member upload.</center></b>\n";
		//if (getSize($lfile) > 1024*1024*1024) html_error("File is too big for anon upload");
		$login = false;
	}
?>
<script type="text/javascript">document.getElementById('login').style.display='none';</script>
<div id="info" style="width:100%;text-align:center;">Retrive upload ID</div>
<?php
	$page = geturl("lumfile.com", 80, "/", 'http://lumfile.com/', $cookie, 0, 0, $_GET["proxy"], $pauth);is_page($page);
	if (!preg_match('@action="(https?://[^/|"]+/[^\?|"]+)\?@i',$page, $up)) html_error('Error: Cannot find upload server.', 0);

	$uid = '';$i = 0;
	while($i < 12) {
		$uid .= rand(0,9);
		$i++;
	}

	$post = array();
	$post['upload_type'] = "file";
	$post['sess_id'] = cut_str($page, 'name="sess_id" value="', '"');
	$post['srv_tmp_url'] = urlencode(cut_str($page, 'name="srv_tmp_url" value="', '"'));
	$post['link_rcpt'] = "";
	$post['link_pass'] = "";
	$post['tos'] = 1;
	$post['submit_btn'] = " Upload! ";

	$up_url = $up[1]."?upload_id=$uid&js_on=1&utype=".cut_str($page, "var utype='", "'")."&upload_type=file";
?>
<script type="text/javascript">document.getElementById('info').style.display='none';</script>
<?php

	$url=parse_url($up_url);
	$upfiles=upfile($url["host"], 80, $url["path"].($url["query"] ? "?".$url["query"] : ""), 'http://lumfile.com/', $cookie, $post, $lfile, $lname, "file_0");

?>
<script type="text/javascript">document.getElementById('progressblock').style.display='none';</script>
<?php
	is_page($upfiles);

	$post = array();
	$post['op'] = "upload_result";
	$post['fn'] = cut_str($upfiles,"'fn'>","<");
	$post['st'] = "OK";

	$page = geturl("lumfile.com", 80, "/", $up_url, $cookie, $post, 0, $_GET["proxy"], $pauth);is_page($page);

	if (preg_match('@(https?://(?:www\.)?lumfile\.com/\w+(?:/[^\?|/|<|>|\"|\'|\r|\n]+)?(?:\.html)?)\?killcode=\w+@i', $page, $lnk)) {
		$download_link = $lnk[1];
		$delete_link = $lnk[0];
	} else html_error("Download link not found.", 0);
}

//[01-6-2012] Written by Th3-822

?>