<?php

// Youtube Developer Key. NEEDED TO WORK...
$YT_Developer_Key = "";
// Get your Developer Key @ http://code.google.com/apis/youtube/dashboard/gwt/index.html

####### Account Info. ###########
$upload_acc['youtube_com']['user'] = ""; //Set your username/email
$upload_acc['youtube_com']['pass'] = ""; //Set your password
##############################

if (empty($YT_Developer_Key)) html_error("Developer Key is empty, please set yours @ {$page_upload["youtube.com"]}.", 0);
if (!preg_match("@\.(mp4|flv|mpe?g|mkv|wmv|mov|3gp|avi)$@i", $lname, $fext)) echo "<p style='color:red;text-align:center;font-weight:bold;'>This file format doesn't looks like a video file allowed by youtube.</p>\n";
if (!extension_loaded('openssl')) html_error("Need OpenSSL enabled to use this plugin.");
$not_done = true;
$continue_up = $login = false;
$categories = array('People' => 'People & Blogs', 'Film' => 'Film & Animation', 'Autos' => 'Autos & Vehicles', 'Music' => 'Music', 'Animals' => 'Pets & Animals', 'Sports' => 'Sports', 'Travel' => 'Travel & Events', 'Games' => 'Gaming', 'Comedy' => 'Comedy', 'News' => 'News & Politics', 'Entertainment' => 'Entertainment', 'Education' => 'Education', 'Howto' => 'Howto & Style', 'Nonprofit' => 'Nonprofits & Activism', 'Tech' => 'Science & Technology');

if ($upload_acc['youtube_com']['user'] && $upload_acc['youtube_com']['pass']) {
	$_REQUEST['up_login'] = $upload_acc['youtube_com']['user'];
	$_REQUEST['up_pass'] = $upload_acc['youtube_com']['pass'];
	//$_REQUEST['action'] = "FORM";
	$login = true;
	echo "<p style='text-align:center;font-weight:bold;'>Using Default Login and Pass.</p>\n";
}

if ($_REQUEST['action'] == "FORM") $continue_up=true;
else {
	echo "<table border='0' style='width:270px;' cellspacing='0' align='center'>
	<form method='POST'>
	<input type='hidden' name='action' value='FORM' />";
	if (!$login) echo "<tr><td style='white-space:nowrap;'>&nbsp;Login*</td><td>&nbsp;<input type='text' name='up_login' value='' style='width:160px;' /></td></tr>
	<tr><td style='white-space:nowrap;'>&nbsp;Password*</td><td>&nbsp;<input type='password' name='up_pass' value='' style='width:160px;' /></td></tr>\n";
	echo "<tr><td colspan='2' align='center'><br />Video options<br /><br /></td></tr>
	<tr><td style='white-space:nowrap;'>Title:</td><td>&nbsp;<input type='text' name='up_title' value='$lname' style='width:160px;' /></td></tr>
	<tr><td style='white-space:nowrap;'>Category:</td><td>&nbsp;<select name='up_category' style='width:160px;height:20px;'>\n";
	foreach($categories as $n => $v) echo "\t<option value='$n'>$v</option>\n";
	echo "</select></td></tr>\n";
	echo "<tr><td colspan='2' align='center'><br /><input type='submit' value='Upload' /></td></tr>\n";
	if (!$login) echo "<tr><td colspan='2' align='center'><small>*You can set it as default in <b>{$page_upload["youtube.com"]}</b></small></td></tr>\n";
	echo "</table>\n</form>\n";
	echo "<script type='text/javascript'>self.resizeTo(700,420);</script>\n"; //Resize upload window
}

if ($continue_up) {
	$not_done = false;
	// Login
	echo "<table style='width:600px;margin:auto;'>\n<tr><td align='center'>\n<div id='login' width='100%' align='center'>Validating login</div>\n";

	if (empty($_REQUEST['up_login']) || empty($_REQUEST['up_pass'])) html_error("Login or pass empty.", 0);

	$post = array();
	$post["Email"] = urlencode($_REQUEST['up_login']);
	$post["Passwd"] = urlencode($_REQUEST['up_pass']);
	$post["service"] = 'youtube';

	$page = geturl ("www.google.com", 80, '/accounts/ClientLogin', "https://www.google.com/accounts/ClientLogin", 0, $post, 0, 0, 0, 0, 'https');is_page($page);
	is_present($page, "Error=BadAuthentication", "Login Failed: The login/password entered are incorrect.");
	is_present($page, "Error=NotVerified", "Login Failed: The account has not been verified.");
	is_present($page, "Error=TermsNotAgreed", "Login Failed: The account has not agreed to terms.");
	is_present($page, "Error=CaptchaRequired", "Login Failed: Need CAPTCHA. (Not supported yet)... Or check you login and try again.");
	is_present($page, "Error=Unknown", "Login Failed.");
	is_present($page, "Error=AccountDeleted", "Login Failed: The user account has been deleted.");
	is_present($page, "Error=AccountDisabled", "Login Failed: The user account has been disabled.");
	is_present($page, "Error=ServiceDisabled", "Login Failed: The user's access to the specified service has been disabled.");
	is_present($page, "Error=ServiceUnavailable", "Login Failed: Service is not available; try again later.");

	if (!preg_match('@Auth=([^\r|\n]+)@i', $page, $auth)) html_error("Login Failed: Auth token not found.", 0);

	// Preparing upload
	echo "<script type='text/javascript'>document.getElementById('login').style.display='none';</script>\n<div id='info' width='100%' align='center'>Preparing upload</div>\n";

	$vtitle = $_REQUEST['up_title'] ? trim($_REQUEST['up_title']) : $lname;
	if (array_key_exists($_REQUEST['up_category'], $categories)) $vcategory = $_REQUEST['up_category'];
	else $vcategory = "People";

	$xml = "<?xml version='1.0'?>\r\n<entry xmlns='http://www.w3.org/2005/Atom' xmlns:media='http://search.yahoo.com/mrss/' xmlns:yt='http://gdata.youtube.com/schemas/2007'>\r\n";
	$xml .= "  <media:group>\r\n";
	$xml .= "    <yt:incomplete/>\r\n";
	$xml .= "    <media:title type='plain'>$vtitle</media:title>";
	$xml .= "    <media:description type='plain'>Uploaded with rapidleech.</media:description>";
	// @<atom:category term='([^']+)' label='([^']+)'[^>]+><yt:assignable/>@i
	$xml .= "    <media:category scheme='http://gdata.youtube.com/schemas/2007/categories.cat'>$vcategory</media:category>\r\n";
	$xml .= "  </media:group>\r\n";
	$xml .= "</entry>";

	// Uploading
	echo "<script type='text/javascript'>document.getElementById('info').style.display='none';</script>\n";

	// UploadToYoutube($host, $port, $url, $dkey, $uauth, $XMLReq, $file, $filename)
	$upfiles = UploadToYoutube("uploads.gdata.youtube.com", 80, "/feeds/api/users/default/uploads", $YT_Developer_Key, $auth[1], $xml, $lfile, $lname);

	// Upload Finished
	echo "<script type='text/javascript'>document.getElementById('progressblock').style.display='none';</script>";

	is_page($upfiles);

	if (preg_match('@<error>(.*)</error>@i', $upfiles, $err)) html_error("Error: (".htmlenties($err[1], ENT_QUOTES).") .", 0);
	if (!preg_match('@<yt:videoid>([^<]+)</yt:videoid>@i', $upfiles, $vid)) html_error("Error: Video ID not found.", 0);

	echo "<p style='text-align:center;font-weight:bold;'>Please check your video in your <a href='http://www.youtube.com/my_videos'>youtube page</a> for details/errors.<br />The '".lang(71)."' link is for go to 'edit video' page.</p>\n";
	$download_link = "http://www.youtube.com/watch?v=".$vid[1];
	$adm_link = "http://www.youtube.com/my_videos_edit?ns=1&video_id={$vid[1]}&next=%2Fmy_videos";
}

// upfile function edited for YT upload.
function UploadToYoutube($host, $port, $url, $dkey, $uauth, $XMLReq, $file, $filename) {
	global $nn, $lastError, $sleep_time, $sleep_count;

	if (!is_readable($file)) {
		$lastError = sprintf(lang(65),$file);
		return FALSE;
	}

	$fileSize = getSize($file);
	$bound = "--------" . md5(microtime());
	$saveToFile = 0;

	$postdata .= "--" . $bound . $nn;
	$postdata .= 'Content-Type: application/atom+xml; charset=UTF-8' . $nn . $nn;
	$postdata .= $XMLReq . $nn;
	$postdata .= "--" . $bound . $nn;
	$postdata .= "Content-Type: application/octet-stream" . $nn . $nn;

	$zapros = "POST " . str_replace ( " ", "%20", $url ) . " HTTP/1.1{$nn}Host: $host{$nn}Authorization: GoogleLogin auth=$uauth{$nn}GData-Version: 2{$nn}X-GData-Key: key=$dkey{$nn}Slug: $filename{$nn}Content-Type: multipart/related; boundary=$bound{$nn}Content-Length: " . (strlen($postdata) + strlen($nn . "--$bound--$nn") + $fileSize) . "{$nn}Connection: Close$nn$nn$postdata";
	$errno = 0; $errstr = "";
	$fp = @stream_socket_client("$host:$port", $errno, $errstr, 120, STREAM_CLIENT_CONNECT);

	if (!$fp) html_error(sprintf(lang(88),$host,$port));
	if ($errno || $errstr) {
		$lastError = $errstr;
		return false;
	}

	echo "<p>";
	printf(lang(90),$host,$port);
	echo "</p>";

	echo(lang(104).' <b>'.$filename.'</b>, '.lang(56).' <b>'.bytesToKbOrMb($fileSize).'</b>...<br />');
	global $id;
	$id = md5(time() * rand( 0, 10 ));
	require(TEMPLATE_DIR . '/uploadui.php');
	flush();

	$timeStart = getmicrotime();
	$chunkSize = GetChunkSize($fileSize);

	fputs($fp, $zapros);
	fflush($fp);

	$fs = fopen($file, 'r');

	$local_sleep = $sleep_count;
	while ( ! feof ( $fs ) ) {
		$data = fread ( $fs, $chunkSize );
		if ($data === false) {
			fclose($fs);
			fclose($fp);
			html_error (lang(112));
		}

		if (($sleep_count !== false) && ($sleep_time !== false) && is_numeric($sleep_time) && is_numeric($sleep_count) && ($sleep_count > 0) && ($sleep_time > 0)) {
			$local_sleep --;
			if ($local_sleep == 0) {
				usleep($sleep_time);
				$local_sleep = $sleep_count;
			}
		}

		$sendbyte = fputs($fp, $data);
		fflush($fp);

		if ($sendbyte === false) {
			fclose($fs);
			fclose($fp);
			html_error(lang(113));
		}

		$totalsend += $sendbyte;

		$time = getmicrotime() - $timeStart;
		$chunkTime = $time - $lastChunkTime;
		$chunkTime = $chunkTime ? $chunkTime : 1;
		$lastChunkTime = $time;
		$speed = round($sendbyte / 1024 / $chunkTime, 2);
		$percent = round($totalsend / $fileSize * 100, 2);
		echo '<script type="text/javascript">pr('."'"  . $percent . "', '" . bytesToKbOrMb ( $totalsend ) . "', '" . $speed . "');</script>\n";
		flush();
	}
	fclose ($fs);
	fputs ($fp, $nn . "--" . $bound . "--" . $nn);
	fflush ($fp);
	while (!feof($fp)) {
		$data = fgets($fp, 16384);
		if ($data === false) {
			break;
		}
		$page .= $data;
	}
	fclose ($fp);
	return $page;
}

//[15-7-2011]  Written by Th3-822.

?>