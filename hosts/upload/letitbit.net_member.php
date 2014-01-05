<?php
####### Account Info. ###########
$upload_acc['letitbit_net']['user'] = ""; //Set your email
$upload_acc['letitbit_net']['pass'] = ""; //Set your password
##############################

$not_done=true;
$continue_up=false;
if ($upload_acc['letitbit_net']['user'] && $upload_acc['letitbit_net']['pass']){
	$_REQUEST['login'] = $upload_acc['letitbit_net']['user'];
	$_REQUEST['password'] = $upload_acc['letitbit_net']['pass'];
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
<tr><td style="white-space:nowrap;">&nbsp;Email*<td>&nbsp;<input type="text" name="login" value="" style="width:160px;" />&nbsp;</tr>
<tr><td style="white-space:nowrap;">&nbsp;Password*<td>&nbsp;<input type="password" name="password" value="" style="width:160px;" />&nbsp;</tr>
<tr><td colspan="2" align="center"><input type="submit" value="Upload" /></tr>
<tr><td colspan="2" align="center"><small>*You can set it as default in <b><?php echo $page_upload["letitbit.net_member"]; ?></b></small></tr>
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
<div id="login" style="width:100%;text-align:center;">Login to letitbit.net</div>
<?php
	if (empty($_REQUEST['login']) || empty($_REQUEST['password'])) html_error("Login failed: User/Password empty.", 0);
	$page = SkipLoginC(strtolower($_REQUEST['login']), $_REQUEST['password']);
?>
<script type="text/javascript">document.getElementById('login').style.display='none';</script>
<div id="info" style="width:100%;text-align:center;">Retrive upload ID</div>
<?php
	if (!preg_match("@var\s+ACUPL_UPLOAD_SERVER\s*=\s*'([^\']+)'\s*;@i",$page, $up)) html_error('Error: Cannot find upload server.', 0);

	function rndStr($lg, $num = false){
		if ($num) $str = "0123456789";
		else {
			$str = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
			$str .= strtolower($str)."0123456789";
		}
		$str = str_split($str);
		$ret = '';
		for ($i = 1; $i <= $lg; $i++) $ret .= $str[array_rand($str)];
		return $ret;
	}

	$UID = strtoupper(base_convert(time().rndStr(3, true),10,16)).'_'.rndStr(40);

	$post = array();
	$post['MAX_FILE_SIZE'] = cut_str($page, 'name="MAX_FILE_SIZE" value="', '"');
	$post['owner'] = cut_str($page, 'name="owner" type="hidden" value="', '"');
	$post['pin'] = cut_str($page, 'name="pin" type="hidden" value="', '"');
	$post['base'] = cut_str($page, 'name="base" type="hidden" value="', '"');
	$post['host'] = cut_str($page, 'name="host" type="hidden" value="', '"');

	$up_url = "http://{$up[1]}/marker=$UID";
?>
<script type="text/javascript">document.getElementById('info').style.display='none';</script>
<?php

	$url = parse_url($up_url);
	$upfiles = upfile($url["host"], $url["port"] ? $url["port"] : 80, $url["path"].($url["query"] ? "?".$url["query"] : ""), 'http://letitbit.net/', $cookie, $post, $lfile, $lname, "file0");

?>
<script type="text/javascript">document.getElementById('progressblock').style.display='none';</script>
<?php
	is_page($upfiles);

	$page = geturl("letitbit.net", 80, "/acupl_proxy.php?srv={$up[1]}&uid=$UID", 'http://letitbit.net/', $cookie, 0, 0, $_GET["proxy"], $pauth);is_page($page);
	if (!preg_match('@"post_result":\s*"(http://[^\"]+)"@i', $page, $rd)) html_error("Error: Redirect not found.", 0);

	$url = parse_url($rd[1]);
	$page = geturl($url["host"], $url["port"] ? $url["port"] : 80, $url["path"].($url["query"] ? "?".$url["query"] : ""), 'http://letitbit.net/', $cookie, 0, 0, $_GET["proxy"], $pauth);is_page($page);

	if (preg_match('@https?://[^/|\"|\'|<|\r|\n]+/download/[^\"|\'|<|\r|\n]+@i', $page, $lnk)) {
		$download_link = $lnk[0];
		if (preg_match('@https?://[^/|\"|\'|<|\r|\n]+/download/delete[^\"|\'|<|\r|\n]+@i', $page, $dlnk)) $delete_link = $dlnk[0];
	} else {
		html_error("Error: Download link not found.", 0);
	}
}

function Login($user, $pass) {
	global $cookie;
	$post = array();
	$post['act'] = "login";
	$post['login'] = $user;
	$post['password'] = $pass;

	$page = geturl('letitbit.net', 80, '/ajax/auth.php', 'http://letitbit.net/', 'lang=en', $post, 0, $_GET["proxy"], $pauth);is_page($page);
	is_present($page, "Authorization data is invalid", "Login failed: User/Password incorrect.");
	is_notpresent($page, 'Set-Cookie: log=', 'Login failed: Cannot find login cookie.');
	is_notpresent($page, 'Set-Cookie: pas=', 'Login failed: Cannot find paswword cookie.');
	$cookie = GetCookiesArr($page);
	$cookie['lang'] = 'en';

	SaveCookies($user, $pass);

	$page = geturl("letitbit.net", 80, "/", 'http://letitbit.net/', $cookie, 0, 0, $_GET["proxy"], $pauth);is_page($page);
	return $page;
}

function IWillNameItLater($cookie, $decrypt=true) {
	if (!is_array($cookie)) {
		if (!empty($cookie)) return $decrypt ? decrypt(urldecode($cookie)) : urlencode(encrypt($cookie));
		return '';
	}
	if (count($cookie) < 1) return $cookie;
	$keys = array_keys($cookie);
	$values = array_values($cookie);
	$keys = $decrypt ? array_map('decrypt', array_map('urldecode', $keys)) : array_map('urlencode', array_map('encrypt', $keys));
	$values = $decrypt ? array_map('decrypt', array_map('urldecode', $values)) : array_map('urlencode', array_map('encrypt', $values));
	return array_combine($keys, $values);
}

function SkipLoginC($user, $pass) {
	global $cookie, $hash, $maxdays, $secretkey;
	$filename = 'letitbit_ul.php';
	$maxdays = 3; // Max days to keep cookies saved
	if (!defined('DOWNLOAD_DIR')) {
		global $options;
		if (substr ($options['download_dir'], - 1) != '/') $options['download_dir'] .= '/';
		define ('DOWNLOAD_DIR', (substr ($options['download_dir'], 0, 6) == "ftp://" ? '' : $options['download_dir']));
	}

	$filename = DOWNLOAD_DIR.basename($filename);
	if (!file_exists($filename)) return Login($user, $pass);

	$file = file($filename);
	$savedcookies = unserialize($file[1]);
	unset($file);

	$hash = hash('crc32b', $user.':'.$pass);
	if (array_key_exists($hash, $savedcookies)) {
		if (time() - $savedcookies[$hash]['time'] >= ($maxdays * 24 * 60 * 60)) return Login($user, $pass); // Ignore old cookies
		$_secretkey = $secretkey;
		$secretkey = sha1($user.':'.$pass);
		$cookie = (decrypt(urldecode($savedcookies[$hash]['enc'])) == 'OK') ? IWillNameItLater($savedcookies[$hash]['cookie']) : '';
		$secretkey = $_secretkey;
		if ((is_array($cookie) && count($cookie) < 1) || empty($cookie)) return Login($user, $pass);

		$page = geturl("letitbit.net", 80, "/", 'http://letitbit.net/', $cookie, 0, 0, $_GET["proxy"], $pauth);is_page($page);
		if (stripos($page, 'title="Logout">Logout</a>') === false) return Login($user, $pass);
		SaveCookies($user, $pass); // Update cookies file
		return $page;
	}
	return Login($user, $pass);
}

function SaveCookies($user, $pass) {
	global $cookie, $maxdays, $secretkey;
	$filename = 'letitbit_ul.php';
	$filename = DOWNLOAD_DIR.basename($filename);
	if (file_exists($filename)) {
		$file = file($filename);
		$savedcookies = unserialize($file[1]);
		unset($file);

		// Remove old cookies
		foreach ($savedcookies as $k => $v) if (time() - $v['time'] >= ($maxdays * 24 * 60 * 60)) unset($savedcookies[$k]);
	} else $savedcookies = array();
	$hash = hash('crc32b', $user.':'.$pass);
	$_secretkey = $secretkey;
	$secretkey = sha1($user.':'.$pass);
	$savedcookies[$hash] = array('time' => time(), 'enc' => urlencode(encrypt('OK')), 'cookie' => IWillNameItLater($cookie, false));
	$secretkey = $_secretkey;

	write_file($filename, "<?php exit(); ?>\r\n" . serialize($savedcookies));
}

//[01-1-2012] Written by Th3-822. // Happy New Year!
//[16-2-2012] Added functions for save encrypted cookies in a file (Works with more than 1 login) and for keep the cookies saved for 3 days since the last login (can be changed). - Th3-822

?>