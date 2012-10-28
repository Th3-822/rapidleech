<?php
####### Account Info. ###########
$upload_acc['netkups_com']['user'] = ""; //Set your user
$upload_acc['netkups_com']['pass'] = ""; //Set your password
##############################

$_GET["proxy"] = isset($_GET["proxy"]) ? $_GET["proxy"] : '';
$not_done=true;
$continue_up=false;

if ($upload_acc['netkups_com']['user'] && $upload_acc['netkups_com']['pass']) {
	$_REQUEST['login'] = $upload_acc['netkups_com']['user'];
	$_REQUEST['password'] = $upload_acc['netkups_com']['pass'];
	$_REQUEST['action'] = "FORM";
	echo "<b><center>Use Default login/pass.</center></b>\n";
}
if (!empty($_REQUEST['action']) && $_REQUEST['action'] == "FORM")
	$continue_up=true;
else{
?>
<table border="0" style="width:270px;margin:auto;" cellspacing="0">
<form method="POST">
<input type="hidden" name="action" value="FORM" />
<tr><td style="white-space:nowrap;">&nbsp;Username*</td><td>&nbsp;<input type="text" name="login" value="" style="width:160px;" />&nbsp;</td></tr>
<tr><td style="white-space:nowrap;">&nbsp;Password*</td><td>&nbsp;<input type="password" name="password" value="" style="width:160px;" />&nbsp;</td></tr>
<tr><td colspan="2" align="center">(Let the login empty for anon upload)</td></tr>
<tr><td colspan="2" align="center"><br /><input type="submit" value="Upload" /></td></tr>
<tr><td colspan="2" align="center"><small>*You can set it as default in <b><?php echo basename(__FILE__); ?></b></small></td></tr>
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
<div id="login" style="width:100%;text-align:center;">Login to netkups.com</div>
<?php
	if (!empty($_REQUEST['login']) && !empty($_REQUEST['password'])) {
		$post = array();
		$post['username'] = urlencode($_REQUEST['login']);
		$post['password'] = urlencode($_REQUEST['password']);

		$page = geturl("netkups.com", 80, "/?page=login", 'http://netkups.com/', 0, $post, 0, $_GET["proxy"], $pauth);is_page($page);
		is_present($page, "/?page=login&err=user", "Login failed: Username incorrect.");
		is_present($page, "/?page=login&err=password", "Login failed: Password incorrect.");
		is_present($page, "/?page=login&err=", "Login failed.");
		$cookie = GetCookiesArr($page);
		if (empty($cookie['session'])) html_error("Error: Cannot find 'session' cookie.");
		if (empty($cookie['user'])) html_error("Error: Cannot find 'user' cookie.");
	} else {
		echo "<b><center>Login not found or empty, using non member upload.</center></b>\n";
		$cookie = array();
	}
?>
<script type="text/javascript">document.getElementById('login').style.display='none';</script>
<div id="info" style="width:100%;text-align:center;">Retrive upload ID</div>
<?php
	$page = geturl("netkups.com", 80, "/ajax.php?action=upload", 'http://netkups.com/', $cookie, 0, 0, $_GET["proxy"], $pauth);is_page($page);
	if (stripos($page, "ERROR")) html_error('Error: '. htmlentities(substr($page, strpos($page, "\r\n\r\n") + 4)), 0);
	$rply = Get_Reply($page);

	$post = array();
	$post['Filename'] = $lname;
	$post['name'] = $lname;
	$post['Upload'] = "Submit Query";

	$up_url = "http://u{$rply['server']}.netkups.com/upload?id={$rply['id']}&key={$rply['key']}";
?>
<script type="text/javascript">document.getElementById('info').style.display='none';</script>
<?php

	$url=parse_url($up_url);
	$upfiles=upfile($url["host"], 80, $url["path"].($url["query"] ? "?".$url["query"] : ""), 'http://netkups.com/', $cookie, $post, $lfile, $lname, "file");

?>
<script type="text/javascript">document.getElementById('progressblock').style.display='none';</script>
<?php
	is_page($upfiles);

	$page = geturl("netkups.com", 80, "/?finish={$rply['key']}&process={$rply['process']}", 'http://netkups.com/', $cookie, 0, 0, $_GET["proxy"], $pauth);is_page($page);

	if (!preg_match('@(https?%3A%2F%2F(?:[^\%]+\.)?netkups\.com%2F%3Fd%3D[^\"|\'|\&]+)@i', $page, $lnk)) html_error("Download link not found.", 0);
	$download_link = urldecode($lnk[1]);
}

function Get_Reply($page) {
	if (!function_exists('json_decode')) html_error("Error: Please enable JSON in php.");
	$json = substr($page, strpos($page,"\r\n\r\n") + 4);
	$json = substr($json, strpos($json, "{"));$json = substr($json, 0, strrpos($json, "}") + 1);
	$rply = json_decode($json, true);
	if (!$rply || (is_array($rply) && count($rply) == 0)) html_error("Error getting json data.");
	return $rply;
}

//[11-3-2012] Written by Th3-822

?>