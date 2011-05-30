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
	if (!preg_match('%ocation: (.+)\r\n%', $page, $redir)) html_error ('No login location found.');
	$geturl = rtrim($redir["1"]);

	$contents = sslcurl($geturl, 0, $cookies);
	$cookie_GALX = GetCookies($contents);
	
	$post_url = "https://www.google.com/accounts/ServiceLoginAuth";
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

	$contents = sslcurl($post_url, $post, $cookie_GALX, $geturl);	
	if (!preg_match('%ocation: (.+)\r\n%', $contents, $redir) and !preg_match('%url=&#39;(.+)&#39;%', $contents, $redir)) html_error('Error - logins incorrect');
	$redirect = html_entity_decode($redir[1]);
	
	if (preg_match('%^https://www.google.com/accounts/CheckCookie%', $redirect)) $google = true; else $google = false;
	
	if ($google === true)
	{
		$gcookies = preg_replace('%LSID=EXPIRED; %U', '', GetCookies($contents));
		$Url = parse_url($redirect);
		$page = sslcurl($redirect, 0, $gcookies, urldecode($geturl));
		$lsid = preg_replace('%LSID=EXPIRED; %U', '', GetCookies($page));
		$gredir = html_entity_decode(cut_str($page, '<meta http-equiv="refresh" content="0; url=&#39;', '&#39;">'));
		$Url = parse_url($gredir);
		if (stripos($gredir, 'accounts/SetSID?'))
		{
			$page = sslcurl($gredir, 0, $lsid);
			preg_match('%ocation: (.+)\r\n%', $page, $redir3);
			$lcookies = GetCookies($page);
			$Url = parse_url($redir3[1]);
		}
		$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"] . ($Url["query"] ? "?" . $Url["query"] : ""), 0, $gcookies, 0, 0, $_GET["proxy"], $pauth);
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
		$page = geturl('www.youtube.com', 80, '/', $redirect, $utube_login_cookie, 0, 0, $_GET["proxy"], $pauth);
		is_page($page);
	}
	
	$Url = 'http://upload.youtube.com/my_videos_upload';
	$Url = parse_url($Url);
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"] . ($Url["query"] ? "?" . $Url["query"] : ""), 0, $utube_login_cookie, 0, 0, $_GET["proxy"], $pauth);
	is_page($page);
	is_notpresent($page, $_REQUEST['my_login'], 'Error logging in.');
	
	$uploadkey = cut_str($page, "'uploadKey': '", "',");

	$Urlpost = Array
	(
		'protocolVersion' => "0.8",
		'createSessionRequest' => Array
			(
				'fields' => Array
					(
						Array
							(
								'external' => Array
									(
										'name' => 'file',
										'filename' => $lname,
										'formPost' => new stdClass(),
										'size' => filesize($lfile)
									)
							),

						Array
							(
								'inlined' => Array
									(
										'name' => 'return_address',
										'content' => 'upload.youtube.com',
										'contentType' => 'text/plain'
									)
							),

					   Array
							(
								'inlined' => Array
									(
										'name' => 'upload_key',
										'content' => $uploadkey,
										'contentType' => 'text/plain'
									)
							),

					   Array
							(
								'inlined' => Array
									(
										'name' => 'action_postvideo',
										'content' => "1",
										'contentType' => 'text/plain'
									)
							),

						Array
							(
								'inlined' => Array
									(
										'name' => 'live_thumbnail_id',
										'content' => 'uexBkOjf326Y1286907488.27.0',
										'contentType' => 'text/plain'
									)
							),

						Array
							(
								'inlined' => Array
									(
										'name' => 'parent_video_id',
										'content' => '',
										'contentType' => 'text/plain'
									)
							),

						Array
							(
								'inlined' => Array
									(
									   'name' => 'allow_offweb',
									   'content' => "True",
										'contentType' => 'text/plain'
									)
							),

						Array
							(
								'inlined' => Array
									(
										'name' => 'uploader_type',
										'content' => 'Web_XHR',
										'contentType' => 'text/plain'
									)
							),
					),
			),
		'clientId' => 'scotty xhr non-resumable'
	);

	$Urlpost_json = json_encode($Urlpost);
	$Urlpost_json = str_replace('\\', '', $Urlpost_json);
	
	$Url = 'http://upload.youtube.com/upload/rupio';	
	$ch = curl_init($Url);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U;Windows NT 5.1; de;rv:1.8.0.1)\r\nGecko/20060111\r\nFirefox/1.5.0.1');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $Urlpost_json);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-GUploader-Client-Info: clientId:scotty xhr non-resumable', 'Content-Type: application/x-www-form-urlencoded;charset=utf-8'));
	curl_setopt($ch, CURLOPT_REFERER, 'http://upload.youtube.com/my_videos_upload');
	curl_setopt($ch, CURLOPT_COOKIE, $utube_login_cookie) ;
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
	$page = curl_exec($ch);
	curl_close($ch);

	$page = json_decode($page, true);
	$action_url = $page['sessionStatus']['externalFieldTransfers'][0]['formPostInfo']['url'];
	if (!$action_url) html_error('Upload URL not found, halted.');
	$Url = parse_url($action_url);
	$upload_cookie = $utube_login_cookie . '; ' . 'enabledapps.uploader=0';
	$post = array();
	$post['Filename'] = $lfile;
	$post['Upload'] = 'Submit Query';

	echo <<<EOF
	<script>document.getElementById('login').style.display='none';</script>
	<table width=600 align=center>
	</td></tr>
	<tr><td align=center>
EOF;
	
	$upfiles = upfile($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"] . ($Url["query"] ? "?" . $Url["query"] : ""), 'http://upload.youtube.com/my_videos_upload', $upload_cookie, $post, $lfile, $lname, "Filedata");
	is_page($upfiles);
	$video_id = cut_str($upfiles, '{"video_id":"', '",') or html_error('Couldn\'t find the video ID - perhaps the upload failed?');
	$download_link = 'http://www.youtube.com/watch?v=' . $video_id;
	echo "<script>document.getElementById('progressblock').style.display='none';</script>";
}

//sslcurl function moved to http.php
// written by kaox 26/05/09
//updated by szalinski 12-Oct-2010
?>