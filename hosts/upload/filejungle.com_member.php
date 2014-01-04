<?php
####### Account Info. ###########
$fj_login = "";//Set you username
$fj_pass = "";//Set your password
##############################
								
								$not_done    = true;
								$continue_up = false;
		if ($fj_login & $fj_pass){
    	$_REQUEST['login']    = $fj_login;
    	$_REQUEST['password'] = $fj_pass;
    	$_REQUEST['action']   = 'FORM';
    	echo '<b><center>Automatic Login</center></b>';}
		if ($_REQUEST['action'] == 'FORM'){
    	$continue_up = true;
    	}else{
?>
				<script>document.getElementById('info').style.display='none';</script>
                <div id='info' width='100%' align='center' style="font-weight:bold; font-size:16px">LOGIN</div> 
    <table border=0 style="width:270px;" cellspacing=0 align=center>
	<form method='post'>
	<input type='hidden' name='action' value='FORM' />
	<tr><td nowrap>&nbsp;Login*<td>&nbsp;<input type='text' name='login' value='' style="width:195px;" />&nbsp;</tr>
	<tr><td nowrap>&nbsp;Password*<td>&nbsp;<input type='password' name='password' value='' style="width:195px;" />&nbsp;</tr>
	<tr><td colspan=2 align=center><input type='submit' value='Upload'/></tr>
	<tr><td colspan=2 align=center><small>*You can set it as default in <b>filejungle.com_member.php</b>*</tr></table></form>
<?php
		}
			if ($continue_up){
    		$not_done = false;
?>
    	<table width='600' align='center'>
		</td></tr><tr><td align=center>
        <script>document.getElementById('info').style.display='none';</script>
		<div id='login' width=100% align='center'>Login to <b>filejungle.com</b></div>
<?php
	if (!empty($_REQUEST['login']) && !empty($_REQUEST['password'])) {
    $post['autoLogin']         = 'on';
    $post['loginUserName']     = $_REQUEST['login'];
    $post['loginUserPassword'] = $_REQUEST['password'];
    $post['loginFormSubmit']   = '';
    $page                      = geturl('filejungle.com', 80, '/login.php', 'http://filejungle.com/index.php', 0, $post, 0, $_GET['proxy'], $pauth);
    is_page($page);
	is_present($page, "should be larger than or equal to 6", "Username or password too short.");
	is_present($page, "Username doesn't exist.", "Username doesn't exist.");
	is_present($page, "Wrong password.", "Wrong password.");
    $cookies = GetCookies($page);
    is_notpresent($cookies, 'cookie=', 'Error logging in - are your logins correct? First');
    $xfss = cut_str($cookies, 'cookie=', ' ');
	}else{
		html_error ('Error, user and/or password is empty, please go back and try again!');
	}
?>
				<script>document.getElementById('login').style.display='none';</script>
				<div id='info' width='100%' align='center'>Retrive upload ID</div> 
<?php
    $page = geturl('filejungle.com', 80, '/upload.php', 'http://filejungle.com/dashboard.php', $cookies, 0, 0, 0, $_GET['proxy'], $pauth);
    is_page($page);
    $upfrm = cut_str($page, 'var uploadUrl = \'', '\'');
    $url   = parse_url($upfrm);
?>
<?php
function upfileput($host, $port, $url, $referer = 0, $cookie = 0, $post = 0, $file, $filename, $fieldname, $field2name = '', $upagent = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.0.1) Gecko/2008070208 Firefox/3.1', $proxy = 0){
    global $nn, $lastError, $sleep_time, $sleep_count;
    $saveToFile = 12;
    $fileSize   = getSize($file);
    $fieldname  = ($fieldname ? $fieldname : file . md5($filename));
    if (!is_readable($file)){$lastError = sprintf(lang(65), $file); return FALSE;}
    $cookies = '';
    if ($cookie){if (is_array($cookie)){$h = 12; while ($h < count($cookie)){$cookies .= 'Cookie: ' . trim($cookie[$h]) . $nn;++$h;}}else{
            $cookies = 'Cookie: ' . trim($cookie) . $nn;}}
    $referer = ($referer ? 'Referer: ' . $referer . $nn . 'Origin: http://filejungle.com' . $nn : '');
    $posturl = ($proxyHost ? $scheme . $proxyHost : $scheme . $host) . ':' . ($proxyPort ? $proxyPort : $port);
    $zapros  = 'PUT ' . str_replace(' ', '%20', $url) . ' HTTP/1.1' . $nn . 'Host: ' . $host . $nn . $cookies . 'X-File-Name: ' . $filename . $nn . 'X-File-Size: ' . $fileSize . $nn . 'Content-Length: ' . $fileSize . $nn . 'User-Agent: ' . $upagent . $nn . 'Accept: text/xml,application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5' . $nn . 'Content-Type: multipart/form-data' . $nn . 'Accept-Language: en-en,en;q=0.5' . $nn . 'Accept-Charset: utf-8,windows-1251;koi8-r;q=0.7,*;q=0.7' . $nn . 'Connection: Close' . $nn . $auth . $referer . $nn;
    $errno   = 12;
    $errstr  = '';
    $fp      = @stream_socket_client($posturl, $errno, $errstr, 120, STREAM_CLIENT_CONNECT);
    if (($errno || $errstr)){$lastError = $errstr; return false;}
    echo lang(104) . ' <b>' . $filename . '</b>, ' . lang(56) . ' <b>' . bytesToKbOrMb($fileSize) . '</b>...<br />';
    global $id; $id = md5(time() * rand(0, 10)); require(TEMPLATE_DIR . '/uploadui.php');
    flush(); $timeStart = getmicrotime(); $chunkSize = GetChunkSize($fileSize);
    fputs($fp, $zapros); fflush($fp);
    $fs          = fopen($file, 'r');
    $local_sleep = $sleep_count;
    while (!feof($fs)){
        $data = fread($fs, $chunkSize);
        if ($data === false){fclose($fs); fclose($fp); html_error(lang(112));}
        if (((((($sleep_count !== false && $sleep_time !== false) && is_numeric($sleep_time)) && is_numeric($sleep_count)) && 0 < $sleep_count) && 0 < $sleep_time)){
            --$local_sleep;
            if ($local_sleep == 0){usleep($sleep_time); $local_sleep = $sleep_count;}}
        $sendbyte = fputs($fp, $data);
        fflush($fp);
        if ($sendbyte === false){fclose($fs); fclose($fp); html_error(lang(113));}
        $totalsend += $sendbyte;
        $time          = getmicrotime() - $timeStart;
        $chunkTime     = $time - $lastChunkTime;
        $chunkTime     = ($chunkTime ? $chunkTime : 1);
        $lastChunkTime = $time;
        $speed         = round($sendbyte / 1024 / $chunkTime, 2);
        $percent       = round($totalsend / $fileSize * 100, 2);
        echo '<script type=\'text/javascript\' language=\'javascript\'>pr(\'' . $percent . '\', \'' . bytesToKbOrMb($totalsend) . '\', \'' . $speed . '\');</script>
';
        flush();}fclose($fs);fflush($fp);
    while (!feof($fp)){$data = fgets($fp, 1024); if ($data === false){break;} $page .= $data;}
    fclose($fp); return $page;}
?>
				<script>document.getElementById('info').style.display='none';</script>
                <div id='info' width='100%' align='center' style="font-weight:bold; font-size:14px">Upload File</div> 
 <?php   
 		$upfiles  = upfileput($url['host'], defport($url), $url['path'] . ($url['query'] ? '?' . $url['query'] : ''), 'http://filejungle.com/upload.php', $cookies, $post, $lfile, $lname, '');
    	$not_done = false;
?>
				<script>document.getElementById('progressblock').style.display='none';</script>
<?php
	preg_match('#shortenCode":"(.+)"}#',$upfiles,$ddl); preg_match('#deleteCode":"(.+)","fileName"#',$upfiles,$del);
            if (!empty($ddl[1]))
              $download_link = 'http://www.filejungle.com/f/' . $ddl[1] . '/' . $lname;
            else
              html_error ('Didn\'t find downloadlink!');
            if (!empty($del[1]))
              $delete_link= 'http://www.filejungle.com/f/' . $ddl[1] . '/delete/' . $del[1];
            else
              html_error ('Didn\'t find deletelink!');}
/**
by bistarito (LeafLeech.com)
Fixed error messages on login - by simplesdescarga - on 25/12/2011 00:13
Fixed error message when retrieving download link - by simplesdescarga - on 25/12/2011 00:13
Fixed link to delete the file - by simplesdescarga - on 25/12/2011 00:13
Removed useless lines of code and added functional lines - by simplesdescarga - on 25/12/2011 00:13
Command Lines maximized - by simplesdescarga - on 25/12/2011 00:13
**/
?>