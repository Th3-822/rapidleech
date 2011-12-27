<?php
####### Account Info. ###########
$upload_acc['glumbouploads_com']['user'] = ""; //Set your user
$upload_acc['glumbouploads_com']['pass'] = ""; //Set your password
##############################

$not_done=true;
$continue_up=false;
if ($upload_acc['glumbouploads_com']['user'] && $upload_acc['glumbouploads_com']['pass']){
	$_REQUEST['login'] = $upload_acc['glumbouploads_com']['user'];
	$_REQUEST['password'] = $upload_acc['glumbouploads_com']['pass'];
	$_REQUEST['action'] = "FORM";
	// Change settings here: (With default login enabled)
	$_REQUEST['up_description'] = 'Uploaded with Rapidleech'; // File Description.
	$_REQUEST['up_public'] = 'yes'; // Make link searchable/public.
	$_REQUEST['up_convert'] = 'yes'; // Convert video to MP4.
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
<tr><td colspan='2' align='center'><br />Upload options*<br /><br /></td></tr>
<tr><td colspan='2' align='center'><input type='checkbox' name='up_convert' value='yes' />&nbsp; Convert video to MP4</td></tr>
<tr><td style='white-space:nowrap;'>File Description:</td><td>&nbsp;<input type='text' name='up_description' value='Uploaded with Rapidleech' style='width:160px;' /></td></tr>
<tr><td colspan='2' align='center'><input type='checkbox' name='up_public' value='yes' checked='checked' />&nbsp; Make link searchable/public</td></tr>
<tr><td colspan="2" align="center"><input type="submit" value="Upload" /></tr>
<tr><td colspan="2" align="center"><small>*You can set it as default in <b><?php echo $page_upload["glumbouploads.com_member"]; ?></b></small></tr>
</form>
</table>
<script type='text/javascript'>self.resizeTo(700,420);</script>
<?php
}

if ($continue_up)
	{
		$not_done=false;
?>
<table style="width:600px;margin:auto;">
</td></tr>
<tr><td align="center">
<div id="login" style="width:100%;text-align:center;">Login to glumbouploads.com</div>
<?php
	if (!empty($_REQUEST['login']) && !empty($_REQUEST['password'])) {
		$cookie = 'lang=english';
		$post = array();
		$post['op'] = "login";
		$post['redirect'] = "";
		$post['login'] = $_REQUEST['login'];
		$post['password'] = $_REQUEST['password'];

		$page = geturl("glumbouploads.com", 80, "/", 'http://glumbouploads.com/', $cookie, $post, 0, $_GET["proxy"], $pauth);is_page($page);
		is_present($page, "Incorrect Username or Password", "Login failed: User/Password incorrect.");
		is_notpresent($page, 'Set-Cookie: xfss=', 'Error: Cannot find session cookie.');
		$cookie = "$cookie;" . GetCookies($page);
		$login = true;
	} else {
		echo "<b><center>Login not found or empty, using non member upload.</center></b>\n";
		if (getSize($file) > 1024*1024*1024) html_error("File is too big for anon upload");
		$login = false;
	}
?>
<script type="text/javascript">document.getElementById('login').style.display='none';</script>
<div id="info" style="width:100%;text-align:center;">Retrive upload ID</div>
<?php
	$page = geturl("glumbouploads.com", 80, "/", 'http://glumbouploads.com/', $cookie, 0, 0, $_GET["proxy"], $pauth);is_page($page);
	if (!preg_match('@action="(http://[^/|"]+/[^\?|"]+)\?@i',$page, $up)) html_error('Error: Cannot find upload server.', 0);

	$uid = '';$i = 0;
	while($i < 12) {
		$uid .= rand(0,9);
		$i++;
	}

	$post = array();
	$post['upload_type'] = "file";
	$post['sess_id'] = cut_str($page, 'name="sess_id" value="', '"');
	$post['srv_tmp_url'] = urlencode(cut_str($page, 'name="srv_tmp_url" value="', '"'));
	$post['convert'] = ($_REQUEST['up_convert'] == 'yes') ? 'yes' : 'no';
	$post['file_0_descr'] = trim($_REQUEST['up_description']);
	if ($_REQUEST['up_public'] == 'yes') $post['file_0_public'] = 1;
	$post['link_rcpt'] = "";
	$post['link_pass'] = "";
	$post['tos'] = 1;
	$post['submit_btn'] = " Upload! ";

	$up_url = $up[1]."?upload_id=$uid&js_on=1&utype=".cut_str($page, "var utype='", "'")."&upload_type=file&convert=".$post['convert'];
?>
<script type="text/javascript">document.getElementById('info').style.display='none';</script>
<?php

	$url=parse_url($up_url);
	$upfiles=upfile($url["host"], 80, $url["path"].($url["query"] ? "?".$url["query"] : ""), 'http://glumbouploads.com/', $cookie, $post, $lfile, $lname, "file_0");

?>
<script type="text/javascript">document.getElementById('progressblock').style.display='none';</script>
<?php
	is_page($upfiles);

	$post = array();
	$post['op'] = "upload_result";
	$post['fn'] = cut_str($upfiles,"'fn'>","<");
	$post['st'] = "OK";

	$page = geturl("glumbouploads.com", 80, "/", $up_url, $cookie, $post, 0, $_GET["proxy"], $pauth);is_page($page);

	if (preg_match('@(https?://(?:www\.)?glumbouploads\.com/\w+)(\.html)?\?killcode=\w+@i', $page, $lnk)) {
		$download_link = $lnk[1];
		$delete_link = $lnk[0];
	} else {
		html_error("Download link not found.", 0);
	}
}

//[21-12-2011] Written by Th3-822

?>