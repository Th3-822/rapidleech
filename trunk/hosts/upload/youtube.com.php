<?php 
// #Youtube Login Details## Add your youtube logins here
$site_login = '';
$site_pass = '';



/////////////////////////////////////////////////
$not_done = true;
$continue_up = false;
if ($site_login && $site_pass)
{
	$_REQUEST['my_login'] = $site_login;
	$_REQUEST['my_pass'] = $site_pass;
	$_REQUEST['action'] = "FORM";
	echo "<b><center>Use Default login/pass.</center></b>\n";
}
if ($_REQUEST['action'] == "FORM")
{
	$continue_up = true;
}
else
{
	echo <<<EOF
<table border=0 style="width:350px;" cellspacing=0 align=center>
	<form method=post>
		<input type=hidden name=action value='FORM' />
		<tr><td nowrap>&nbsp;Username*</td><td>&nbsp;<input type=text name=my_login value='' style="width:160px;" />&nbsp;</td></tr>
		<tr><td nowrap>&nbsp;Password*</td><td>&nbsp;<input type=password name=my_pass value='' style="width:160px;" />&nbsp;</td></tr>
		<tr><td colspan=2 align=center><input type=submit value='Upload'></td></tr>
	</form>
</table>
EOF;
}

if ($continue_up)
{
	$not_done = false;

	echo <<<EOF
	<table width=600 align=center></td></tr><tr><td align=center>
	<div id=login width=100% align=center>Login to Site</div>
EOF;
	
	if (empty($_REQUEST['my_login']) || empty($_REQUEST['my_pass'])) html_error('No user and pass given', 0);
	


	//////////////////////////	EDIT FROM HERE DOWN	///////////////////////////////////////
	$Url = parse_url("http://www.youtube.com/login?next=/");
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"] . ($Url["query"] ? "?" . $Url["query"] : ""), "http://www.youtube.com/", $cookie, 0, 0, $_GET["proxy"], $pauth);
	is_page($page);
	$cookies = GetCookies($page);

	if (preg_match('%ocation: (.+)\r\n%', $page, $redir))
	{
		$geturl = rtrim($redir["1"]);
	}

	$contents = sslcurl("get", $geturl, 0, $cookies, 0);
	$cookie_GALX = GetCookies($contents);
	
	$post_url = "https://www.google.com/accounts/ServiceLoginAuth?service=youtube";
	$post = array();
	$post['ltmpl'] = 'sso';
	$post['continue'] = urldecode('http%3A%2F%2Fwww.youtube.com%2Fsignin%3Faction_handle_signin%3Dtrue%26nomobiletemp%3D1%26hl%3Den_US%26next%3D%252F');
	$post['service'] = 'youtube';
	$post['uilel'] = '3';
	$post['ltmpl'] = 'sso';
	$post['hl'] = 'en_US' ;
	$post['ltmpl'] = 'sso';
	$post['GALX'] = substr($cookie_GALX, 38);
	$post['Email'] = trim($_REQUEST['my_login']);
	$post['Passwd'] = trim($_REQUEST['my_pass']);
	$post['PersistentCookie'] = 'yes';
	$post['rmShown'] = '1';
	$post['signIn'] = 'Sign+in';
	$post['asts'] = '';
	$contents = sslcurl("post", $post_url, $post, $cookie_GALX, $geturl);
	if (!preg_match('%ocation: (.+)\r\n%', $contents, $redir)) html_error('Error - logins incorrect');
	$redirect = urldecode(rtrim($redir["1"]));
	
	if (preg_match('%^https://www.google.com/accounts/CheckCookie%', $redirect)) $google = true; else $google = false;
	
	if ($google === true)
	{
		$gcookies = preg_replace('%LSID=EXPIRED; %U', '', GetCookies($contents));
		$Url = parse_url($redirect);
		$page = sslcurl('get', $redirect, 0, $gcookies, urldecode($geturl));
		$lsid = preg_replace('%LSID=EXPIRED; %U', '', GetCookies($page));
		$gredir = html_entity_decode(cut_str($page, '<meta http-equiv="refresh" content="0; url=&#39;', '&#39;">'));
		$Url = parse_url($gredir);
		$page = sslcurl('get', $gredir, 0, $lsid, 0);
		preg_match('%ocation: (.+)\r\n%', $page, $redir3);
		$Url = parse_url($redir3[1]);
		$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"] . ($Url["query"] ? "?" . $Url["query"] : ""), 0, $gcookies, 0, 0, $_GET["proxy"], $pauth);
		is_page($page);
		$lcookies = GetCookies($page);
		preg_match('%ocation: (.+)\r\n%', $page, $redir4);
		$Url = parse_url($redir4[1]);
		$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"] . ($Url["query"] ? "?" . $Url["query"] : ""), 0, $lcookies, 0, 0, $_GET["proxy"], $pauth);
		is_page($page);
		$ytcookies = GetCookies($page);
		$utube_login_cookie = $lcookies . '; ' . $ytcookies;
	}
	else
	{
		$Url = parse_url($redirect);
		$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"] . ($Url["query"] ? "?" . $Url["query"] : ""), 0, $cookies, 0, 0, $_GET["proxy"], $pauth);
		is_page($page);
		$cookie_LOGIN_INFO = GetCookies($page);
		$utube_login_cookie = $cookies . '; ' . $cookie_LOGIN_INFO;
	}
		
	$url = 'http://www.youtube.com/my_videos_upload';
	$Url = parse_url($url);
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"] . ($Url["query"] ? "?" . $Url["query"] : ""), $url, $utube_login_cookie, 0, 0, $_GET["proxy"], $pauth);
	is_page($page);
	is_notpresent($page, $_REQUEST['my_login'], 'Error logging in.');
	
	$action_url = 'http://upload.youtube.com/my_videos_post';
	$Url = parse_url($action_url);
	$upload_cookie = $utube_login_cookie . '; ' . $cookie_GALX;
	$return_address = cut_str($page, '<input type="hidden" name="return_address" value="', '">');
	$upload_key = cut_str($page, '<input type="hidden" name="upload_key" value="', '">');
	$session_token = cut_str($page, "\t\tgXSRF_token = '", "';");
	$post = array();
	$post['uploader_type'] = 'Web_HTML';
	$post['return_address'] = $return_address;
	$post['upload_key'] = $upload_key;
	$post['action_postvideo'] = '1';
	$post['session_token'] = $session_token;

	echo <<<EOF
	<script>document.getElementById('login').style.display='none';</script>
	<table width=600 align=center>
	</td></tr>
	<tr><td align=center>
EOF;
	
	$upfiles = upfile($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"] . ($Url["query"] ? "?" . $Url["query"] : ""), 'http://www.youtube.com/my_videos_upload?nobeta', $upload_cookie, $post, $lfile, $lname, "field_uploadfile");
	is_page($upfiles);
	if (!preg_match("%ocation: .+&video_id=(.+)\r\n%", $upfiles, $video_id)) html_error('Couldn\'t find the video ID - perhaps the upload failed?');
	$download_link = 'http://www.youtube.com/watch?v=' . $video_id[1];
	echo "<script>document.getElementById('progressblock').style.display='none';</script>";
}

/////////// DO NOT TOUCH ///////////
function sslcurl ($method, $link, $post, $cookie, $refer)
{
	if ($method == "post")
	{
		$mm = 1;
		$postdata = formpostdata($post);
	}
	elseif ($method == "get")
	{
		$mm = 0;
	}

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $link);
	curl_setopt($ch, CURLOPT_HEADER, 1);
	curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U;Windows NT 5.1; de;rv:1.8.0.1)\r\nGecko/20060111\r\nFirefox/1.5.0.1');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POST, $mm);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_REFERER, $refer);
	curl_setopt($ch, CURLOPT_COOKIE, $cookie) ;
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0); 
	// curl_setopt ( $ch , CURLOPT_TIMEOUT, 15);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
	$contents .= curl_exec($ch); 
	// $info = curl_getinfo($ch);
	// $stat = $info['http_code'];
	curl_close($ch);
	return $contents;
}
// written by kaox 26/05/09
//updated by szalinski 15-Aug-2009
?>