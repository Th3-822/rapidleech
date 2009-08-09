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
</table>
</form>
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
	$post = array();
	$post['username'] = trim($_REQUEST['my_login']);
	$post['password'] = trim($_REQUEST['my_pass']);
	


	//////////////////////////	EDIT FROM HERE DOWN	///////////////////////////////////////
	$Url = parse_url("http://www.youtube.com/login?next=/") ;
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"] . ($Url["query"] ? "?" . $Url["query"] : ""), "http://www.youtube.com/", $cookie, 0, 0, $_GET["proxy"], $pauth);
	is_page($page);
	$cookies = GetCookies($page);

	if (preg_match('%ocation: (.+)\r\n%', $page, $redir))
	{
		$geturl = rtrim($redir["1"]);
	}
	
	$contents = sslcurl("get", $geturl, 0, $cook, 0);
	$cookie_GALX = GetCookies($contents);

	$post_url = "https://www.google.com/accounts/ServiceLoginAuth?service=youtube";
	$post = array();
	$post['ltmpl'] = 'sso';
	$post['continue'] = 'http://www.youtube.com/signup?hl=en_US&warned=&nomobiletemp=1&next=/index';
	$post['service'] = 'youtube';
	$post['uilel'] = '3';
	$post['ltmpl'] = 'sso';
	$post['hl'] = 'en_US' ;
	$post['ltmpl'] = 'sso';
	$post['GALX'] = substr($cookie_GALX, 5);
	$post['Email'] = $_REQUEST['my_login'];
	$post['Passwd'] = $_REQUEST['my_pass'];
	$post['PersistentCookie'] = 'yes';
	$post['rmShown'] = '1';
	$post['signIn'] = 'Sign in';
	$post['asts'] = '';
	$cookie = 'GoogleAccountsLocale_session=en; ' . $cookie_GALX;
	$contents = sslcurl("post", $post_url, $post, $cookie, $geturl);

	if (preg_match('%ocation: (.+)\r\n%', $contents, $redir))
	{
		$redirect = rtrim($redir["1"]);
		$Url = parse_url($redirect);
	}

	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"] . ($Url["query"] ? "?" . $Url["query"] : ""), 0, $cookies, 0, 0, $_GET["proxy"], $pauth);
	is_page($page);
	
	$cookie_LOGIN_INFO = GetCookies($page);
	$cookies = str_replace('PREF=f1=40000000; ', '', $cookies);
	$utube_login_cookie = $cookies . '; ' . $cookie_LOGIN_INFO . '&uvdm=1';
	
	$url = 'http://' . $Url['host'] . '/my_videos_upload';
	$Url = parse_url($url);
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"] . ($Url["query"] ? "?" . $Url["query"] : ""), $url, $utube_login_cookie, 0, 0, $_GET["proxy"], $pauth);
	is_page($page);
	is_notpresent($page, $_REQUEST['my_login'], 'Error logging in.');
	
	$action_url = 'http://upload.youtube.com/my_videos_post';
	$Url = parse_url($action_url);
	$dkv_cookie = 'dkv=' . cut_str($page, 'Set-Cookie: dkv=', ';');
	$upload_cookie = $utube_login_cookie . '; ' . $dkv_cookie . '; ' . $cookie_GALX;
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
//updated by szalinski 09-Aug-2009
?>