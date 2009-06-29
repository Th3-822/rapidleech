<?php
if (!defined('RAPIDLEECH')) {
	require_once("index.html");
	exit;
}

/**
 * Pauses for countdown timer in file hosts
 * @param int The number of seconds to count down
 * @param string The text you want to display when counting down
 * @param string The text you want to display when count down is complete
 * @param bool
 * @return bool
 */
function insert_timer($countd, $caption ="", $timeouttext = "", $hide = false) {
	global $disable_timer;

	if ($disable_timer === true) {return true;}
	if (!$countd || !is_numeric($countd)) {return false;}

	$timerid=rand(1000,time());
?>
<center><span id="global<?php echo $timerid;?>"><br><span style="FONT-FAMILY: Tahoma; FONT-SIZE: 11px;"><?php echo $caption ?></span>&nbsp;&nbsp;<span id='timerlabel<?php echo $timerid; ?>' style="FONT-FAMILY: Tahoma; FONT-SIZE: 11px;"></span></span></center>
<script type="text/javascript">
var count<?php echo $timerid; ?>=<?php echo $countd; ?>;
function timer<?php echo $timerid; ?>() {
	if(count<?php echo $timerid; ?> > 0) {
		document.getElementById('timerlabel<?php echo $timerid; ?>').innerHTML = "Please wait " + count<?php echo $timerid; ?> + ' sec...';
		count<?php echo $timerid; ?>=count<?php echo $timerid; ?> - 1;
		setTimeout("timer<?php echo $timerid; ?>()", 1000)
	}
}
timer<?php echo $timerid; ?>();
</script>
<!-- <?php
	flush();
	for ($nnn=0; $nnn<$countd; $nnn++) {
		sleep(1);
	}
?>
-->
<?php
	if ($hide === true) {
?>
<script language=javascript>
	document.getElementById('global<?php echo $timerid; ?>').style.display='none';
</script>
<?php
		flush();
		return true;
	}

	if ($timeouttext) {
?>
<script language=javascript>
	document.getElementById('global<?php echo $timerid; ?>').innerHTML = '<?php echo $timeouttext; ?>';
</script>
<?php
		flush();
		return true;
	}
	return true;
}

/**
 * Counter for those filehosts that displays mirror after countdown
 * @param int The number of seconds to count down
 * @param string Text you want to display above the counter
 * @param string The text you want to display when counting down
 * @param string The text you want to display when count down is complete
 */
function insert_new_timer($countd, $displaytext, $caption = "", $text = "") {
	if (!is_numeric($countd)) {
    	html_error("Text passed as counter is string!");
    }
	?>
<p><div id="code"></div></p>
<p><center><div id="dl"><h4>ERROR: Please enable JavaScript.</h4></div></center></p>
<script language="JavaScript">
var c = <?php echo $countd; ?>;
fc();
function fc() {
	if(c>0) {
		document.getElementById("dl").innerHTML = "<?php echo $caption; ?> Please wait <b>" + c.toFixed(1) + "</b> seconds...";
		c = c - .5;
		setTimeout("fc()", 500);
	} else {
		document.getElementById("dl").style.display="none";
		document.getElementById("code").innerHTML = unescape("<?php echo $displaytext; ?>");
	}
}
</script>
<?php
	if (!empty($text)) {
		print $text;
	}
?>
</body>
</html>
<?php
}

/**
 * Function to check if geturl function has completed successfully
 */
function is_page($lpage) {
	global $lastError;
	if (!$lpage) {
		html_error("Error retriving the link<br>$lastError", 0);
	}
}

function geturl($host, $port, $url, $referer = 0, $cookie = 0, $post = 0, $saveToFile = 0, $proxy = 0, $pauth = 0, $auth = 0, $scheme = "http", $resume_from = 0) {
global $nn, $lastError, $PHP_SELF, $AUTH, $IS_FTP, $FtpBytesTotal, $FtpBytesReceived, $FtpTimeStart, $FtpChunkSize, $Resume, $bytesReceived, $fs, $forbidden_filetypes, $rename_these_filetypes_to, $bw_save, $force_name, $rename_prefix, $rename_suffix;
//die('saveToFile:'.$saveToFile);
$scheme.= "://";

if (($post !== 0) && ($scheme == "http://"))
  {
    $method = "POST";
    $postdata = formpostdata($post);
    $length = strlen($postdata);
    $content_tl = "Content-Type: application/x-www-form-urlencoded".$nn."Content-Length: ".$length.$nn;
  }
else
  {
    $method = "GET";
    $postdata = "";
    $content_tl = "";
  }

if ($cookie)
	{
		if (is_array($cookie))
			{
				for( $i = 0; $i < count($cookie); $i++)
					{
						$cookies .= "Cookie: ".$cookie[$i].$nn;
					}
			}
		else
			{
				$cookies = "Cookie: ".$cookie.$nn;
			}
	}
$referer = $referer ? "Referer: ".$referer.$nn : "";

if ($scheme == "https://")
	{
	$scheme = "ssl://";
	$port = 443;
	}

if($proxy)
	{
    list($proxyHost, $proxyPort) = explode(":", $proxy);
    $url = $scheme.$host.":".$port.$url;
    $host = $host.":".$port;
	}

if ($scheme != "ssl://")
	{
	$scheme = "";
	}

$http_auth = ($auth) ? "Authorization: Basic ".$auth.$nn : "";
$proxyauth = ($pauth) ? "Proxy-Authorization: Basic ".$pauth.$nn : "";

$request=
$method." ".str_replace(" ", "%20", $url)." HTTP/1.1".$nn.
"Host: ".$host.$nn.
"User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.14) Gecko/20080404 Firefox/2.0.0.14".$nn.
"Accept: */*".$nn.
"Accept-Language: en-us;q=0.7,en;q=0.3".$nn.
"Accept-Charset: windows-1251,utf-8;q=0.7,*;q=0.7".$nn.
"Pragma: no-cache".$nn.
"Cache-Control: no-cache".$nn.
($Resume["use"] === TRUE ? "Range: bytes=".$Resume["from"]."-".$nn : "").
$http_auth.
$proxyauth.
$referer.
$cookies.
"Connection: Close".$nn.
$content_tl.$nn.$postdata;

//write_file(CONFIG_DIR."request.txt", $request);

$errno = 0; $errstr = "";
$host = ($proxyHost ? $scheme.$proxyHost : $scheme.$host).':'.($proxyPort ? $proxyPort : $port);
$fp = @stream_socket_client($host,$errno,$errstr);
//$fp = @fsockopen($proxyHost ? $scheme.$proxyHost : $scheme.$host, $proxyPort ? $proxyPort : $port, $errno, $errstr, 15);

if (!$fp)
	{
	html_error("Couldn't connect to ".($proxyHost ? $proxyHost : $host)." at port ".($proxyPort ? $proxyPort : $port), 0);
	}

socket_set_timeout($fp, 120);

if($errno || $errstr)
  {
  $lastError = $errstr;
  return false;
  }

if ($saveToFile)
	{
	if ($proxy)
		{
		echo "<p>Connected to proxy: <b>".$proxyHost."</b> at port <b>".$proxyPort."</b>...<br>\n";
		echo "GET: <b>".$host.$url."</b>...<br>\n";
		}
	else
		{
		echo "<p>Connected to: <b>".$host."</b> at port <b>".$port."</b>...<br>";
		}
	}

#########################################################################
fputs($fp,$request);
fflush($fp);
$timeStart = getmicrotime();

// Rewrote the get header function according to the proxy script
// Also made sure it goes faster and I think 8192 is the best value for retrieving headers
// Oops.. The previous function hooked up everything and now I'm returning it back to normal
do {
	$header .= fgets($fp,8192);
} while (strpos($header, $nn.$nn) === false);

#########################################################################

if (!$header)
  {
  $lastError = "No header received";
  return false;
  }

preg_match('/^HTTP\/1\.0|1 ([0-9]+) .*/',$header,$responsecode);
if (($responsecode[1] == 404 || $responsecode[1] == 403) && $saveToFile)
{
	// Do some checking, please, at least tell them what error it was
	if ($responsecode[1] == 403) {
		$lastError = 'The page was not found!';
	} elseif ($responsecode[1] == 404) {
		$lastError = 'You are forbidden to access the page!';
	} else {
		// Weird, it shouldn't come here...
		$lastError = 'The page was either forbidden or not found!';
	}
	return false;
}

//write_file(CONFIG_DIR."header.txt", $header);

if ($saveToFile)
  {
  $bytesTotal = intval(trim(cut_str($header, "Content-Length:", "\n")));
  if (stristr($host, "rapidshare") && $bytesTotal < 10000)
	{
		while(!feof($fp))
			{
				$page_src .= fread($fp,1024*8);
			}
		is_present($page_src,"is already in use with another ip", "This premium account is already in use with another ip.");
		//is_present($page_src, "Wrong access code");
		//is_present($page_src, "You have entered a wrong code too many times");
	}
	if (stristr($host, "imageshack") && $bytesTotal < 15000)
	{
		while(!feof($fp))
			{
				$page_src .= fread($fp,1024*8);
			}
		is_present($page_src,"To avoid creation of corrupted zip files, you cannot create a zip on this torrent until it is done downloading");
	}
  /*
  if($redirect = trim(cut_str($header, "Location:", "\n")))
	{
  $lastError = "Error! it is redirected to [".$redirect."]";
  return FALSE;
	}*/
	if(trim(preg_match('/[^\-]Location: *(.+)(\r|\n)+/i', $header, $redir)))
	{
		$redirect = $redir[1];
		$lastError = "Error! it is redirected to [".$redirect."]";
  return FALSE;
	}
if(in_array(cut_str($header, "WWW-Authenticate: ", " "), array("Basic", "Digest")))
	{
  $lastError = "This site requires authorization. For the indication of username and password of access it is necessary to use similar url:<br>http://<b>login:password@</b>www.site.com/file.exe";
  return FALSE;
  }
  $ContentType = trim(cut_str($header, "Content-Type:", "\n"));
if (stristr($host, "rapidshare") && stristr($ContentType, "text/html") && stristr($header, "404 Not Found"))
	{
	unset($saveToFile);
	$NoDownload = TRUE;
	}
elseif (stristr($host, "megaupload") && stristr($ContentType, "text/html"))
	{
	unset($saveToFile);
	$NoDownload = TRUE;
	}
if ($Resume["use"] === TRUE && !stristr($header, "Content-Range:"))
	{
	if (stristr($header, "503 Limit Exceeded"))
		{
		$lastError = "Resume limit exceeded";
		}
	else
		{
		$lastError = "This server doesn't support resume";
		}
	return FALSE;
	}
$ContentDisposition = trim(cut_str($header, "Content-Disposition:", "\n"))."\n";
if ($ContentDisposition && stristr($ContentDisposition, "filename="))
	{

	if($force_name)
	  {
		$FileName = $force_name;
		$saveToFile = dirname($saveToFile).PATH_SPLITTER.$FileName;
	  }
	else
	  {
		$FileName = trim(trim(trim(trim(trim(cut_str($ContentDisposition, "filename=", "\n")), "="), "?"), ";"), '"');
		$saveToFile = dirname($saveToFile).PATH_SPLITTER.$FileName;
	  }
	}

if(!empty($rename_prefix)){
	$File_Name = $rename_prefix.'_'.basename($saveToFile);
	$saveToFile = dirname($saveToFile).PATH_SPLITTER.$File_Name;
}
if(!empty($rename_suffix)){
	$ext = strrchr(basename($saveToFile), ".");
	$before_ext = explode($ext, basename($saveToFile));
	$File_Name = $before_ext[0].'_'.$rename_suffix.$ext;
	$saveToFile = dirname($saveToFile).PATH_SPLITTER.$File_Name;
}
$filetype = strrchr($saveToFile, ".");
if (is_array($forbidden_filetypes) && in_array(strtolower($filetype), $forbidden_filetypes))
	{
	if ($rename_these_filetypes_to !== false)
		{
		$saveToFile = str_replace($filetype, $rename_these_filetypes_to, $saveToFile);
		}
	else
		{
		$lastError = "The filetype $filetype is forbidden to be downloaded";
		return false;
		}
  }

if(@file_exists($saveToFile) && $bw_save)
	{
	html_error('Download: <a href="'.DOWNLOAD_DIR.basename($saveToFile).'">'.basename($saveToFile).'</a>', 0);
	}
if(@file_exists($saveToFile) && $Resume["use"] === TRUE)
	{
	$fs = @fopen($saveToFile, "ab");
	if(!$fs)
		{
		$lastError = "File ".basename($saveToFile)." cannot be saved in directory ".dirname($saveToFile)."<br>".
                 "Try to chmod the folder to 777.<br><a href=\"javascript:location.reload();\">Try again</a>";
    return FALSE;
		}
	}
else
	{
	$exist_file =false;
	if (@file_exists($saveToFile))
	    {
			$saveToFile = dirname($saveToFile).PATH_SPLITTER.time()."_".basename($saveToFile);
		}
			$fs = @fopen($saveToFile, "wb");
			if(!$fs)
				{
				$secondName = dirname($saveToFile).PATH_SPLITTER.str_replace(":", "", str_replace("?", "", basename($saveToFile)));
				$fs = @fopen($secondName, "wb");
				if(!$fs)
					{
					$lastError = "File ".basename($saveToFile)." cannot be saved in directory ".dirname($saveToFile)."<br>".
	                   "Try to chmod the folder to 777.<br><a href=\"javascript:location.reload();\">Try again</a>";
					return FALSE;
					}
				}

	}

	flock($fs, LOCK_EX);
	//$bytesTotal = intval(trim(cut_str($header, "Content-Length:", "\n")));
	if ($Resume["use"] === TRUE && stristr($header, "Content-Range:"))
		{
		list($temp, $Resume["range"]) = explode(" ", trim(cut_str($header, "Content-Range:", "\n")));
		list($Resume["range"], $fileSize) = explode("/", $Resume["range"]);
		$fileSize = bytesToKbOrMbOrGb($fileSize);
		}
	else
		{
		$fileSize = bytesToKbOrMbOrGb($bytesTotal);
		}
	$chunkSize = GetChunkSize($bytesTotal);
	print "File <b>".$saveToFile."</b>, Size <b>".$fileSize."</b>...<br>";

?>
<br>
<table cellspacing="0" cellpadding="0" style="FONT-FAMILY: Tahoma; FONT-SIZE: 11px;">
<tr>
<td></td>
<td>
<div style='border:1px solid royalblue; width:300px; height:10px;'>
<div id="progress" style='background-color:#FFFFFF; width:0%; height:10px;'>
</div>
</div>
</td>
<td></td>
<tr>
<tr>
<td align="left" id="received">0 KB</td>
<td align="center" id="percent">0%</td>
<td align="right" id="speed">0 KB/s</td>
</tr>
</table>
<br>
<div id="resume" align="center"></div>
<script language="javascript">
function pr(percent, received, speed){
	document.getElementById("received").innerHTML = '<b>' + received + '</b>';
	document.getElementById("percent").innerHTML = '<b>' + percent + '%</b>';
	document.getElementById("progress").style.width = percent + '%';
	document.getElementById("speed").innerHTML = '<b>' + speed + ' KB/s</b>';
	document.title = percent + '% Downloaded';
	return true;
	}

function mail(str, field) {
	document.getElementById("mailPart." + field).innerHTML = str;
	return true;
	}
</script>
<br>
<?php
if ($Resume["use"] === TRUE)
	{
	$received = bytesToKbOrMbOrGb(filesize($saveToFile));
	$percent = round($Resume["from"] / ($bytesTotal + $Resume["from"]) * 100, 2);
	echo "<script>pr('".$percent."', '".$received."', '0')</script>\r\n";
	flush();
	}
  }
else
  {
  $page = $header;
  }

	while(!feof($fp))
		{
	 	$data = @fgets($fp, 8192);
		if ($data == '') break;
	 	if($saveToFile)
	    {
	    $bytesSaved = fwrite($fs, $data);
	    if($bytesSaved > -1)
	      {
	      $bytesReceived += $bytesSaved;
	      }
	    else
	      {
	      $lastError = "It is not possible to carry out a record in the file ".$saveToFile;
	      return false;
	      }
	    if($bytesReceived >= $bytesTotal)
	      {
	      $percent = 100;
	      }
	    else
	      {
	      $percent = @round(($bytesReceived + $Resume["from"]) / ($bytesTotal + $Resume["from"]) * 100, 2);
	      }
	    if($bytesReceived > $last + $chunkSize)
	      {
	      $received = bytesToKbOrMbOrGb($bytesReceived + $Resume["from"]);
	      $time = getmicrotime() - $timeStart;
	      $chunkTime = $time - $lastChunkTime;
	      $chunkTime = $chunkTime ? $chunkTime : 1;
	      $lastChunkTime = $time;
	      $speed = @round($chunkSize / 1024 / $chunkTime, 2);
	      echo "<script>pr('".$percent."', '".$received."', '".$speed."')</script>\r\n";
	      $last = $bytesReceived;
	      }
	    }
	  else
	    {
	    $page.= $data;
	    }
	  }


	if($saveToFile)
	  {
	  flock($fs, LOCK_UN);
	  fclose($fs);
	  if($bytesReceived <= 0)
			{
	    $lastError = "Invalid URL or unknown error occured";
	    fclose($fp);
	    return FALSE;
			}
	  }
	fclose($fp);
	if($saveToFile)
	  {
	  return array("time"              => sec2time(round($time)),
	               "speed"             => @round($bytesTotal / 1024 / (getmicrotime() - $timeStart), 2),
	               "received"          => true,
	               "size"              => $fileSize,
	               "bytesReceived"     => ($bytesReceived + $Resume["from"]),
	               "bytesTotal"        => ($bytesTotal + $Resume["from"]),
	               "file"              => $saveToFile);
	  }
	else
	  {
	  if ($NoDownload)
	    {
	    if (stristr($host, "rapidshare"))
	      {
			is_present($page, "You have reached the limit for Free users", "You have reached the limit for Free users.", 0);
			is_present($page, "The download session has expired", "The download session has expired", 0);
			is_present($page, "Wrong access code.", "Wrong access code.", 0);
			is_present($page, "You have entered a wrong code too many times", "You have entered a wrong code too many times", 0);
	      print $page;
	      }
	    elseif (stristr($host, "megaupload"))
	      {
	      is_present($page, "Download limit exceeded", "Download limit exceeded", 0);
	      print $page;
	      }
	    }
	  else
	    {
	    return $page;
	    }
	  }
}

// This new function requires less line and actually reduces filesize :P
// Besides, using less globals means more variables available for us to use
function formpostdata($post) {
	$postdata = "";
	foreach ($post as $k => $v) {
		$postdata .= "$k=$v&";
	}
	// Remove the last '&'
	$postdata = substr($postdata,0,-1);
	return $postdata;
}

function GetCookies($content)
{
	// The U option will make sure that it matches the first character
	// So that it won't grab other information about cookie such as expire, domain and etc
	preg_match_all('/Set-Cookie: (.*);/U',$content,$temp);
	$cookie = $temp[1];
	$cook = implode('; ',$cookie);
	return $cook;
}

function GetChunkSize($fsize)
{
	if ($fsize <= 1024*1024) { return 4096; }
	if ($fsize <= 1024*1024*10) { return 4096*10; }
	if ($fsize <= 1024*1024*40) { return 4096*30; }
	if ($fsize <= 1024*1024*80) { return 4096*47; }
	if ($fsize <= 1024*1024*120) { return 4096*65; }
	if ($fsize <= 1024*1024*150) { return 4096*70; }
	if ($fsize <= 1024*1024*200) { return 4096*85; }
	if ($fsize <= 1024*1024*250) { return 4096*100; }
	if ($fsize <= 1024*1024*300) { return 4096*115; }
	if ($fsize <= 1024*1024*400) { return 4096*135; }
	if ($fsize <= 1024*1024*500) { return 4096*170; }
	if ($fsize <= 1024*1024*1000) { return 4096*200; }
	return 4096*210;
}

function upfile($host, $port, $url, $referer = 0, $cookie = 0, $post = 0, $file, $filename, $fieldname, $field2name = "", $upagent="Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.0.1) Gecko/2008070208 Firefox/3.1", $proxy = 0) {
global $nn, $lastError, $sleep_time, $sleep_count;

$bound="--------".substr(md5(time()),-8);
$saveToFile=0;

unset($postdata);
foreach ($post as $key => $value)
	{
		$postdata.="--".$bound.$nn;
		$postdata.="Content-Disposition: form-data; name=\"$key\"".$nn.$nn;
		$postdata.=$value.$nn;
	}

$fileSize = getSize($file);

$fieldname = $fieldname ? $fieldname : file.md5($filename);

if (!is_readable($file))
	{
		$lastError="Error read file $file";
		return FALSE;
	}
if($field2name != '')
  {
$postdata.="--".$bound.$nn;
$postdata.="Content-Disposition: form-data; name=\"$field2name\"; filename=\"\"".$nn;
$postdata.="Content-Type: application/octet-stream".$nn.$nn;
  }

$postdata.="--".$bound.$nn;
$postdata.="Content-Disposition: form-data; name=\"$fieldname\"; filename=\"$filename\"".$nn;
$postdata.="Content-Type: application/octet-stream".$nn.$nn;

$cookies="";

if ($cookie)
	{
		if (is_array($cookie))
			{
				for( $h=0; $h<count($cookie); $h++)
					{
						$cookies.="Cookie: ".trim($cookie[$h]).$nn;
					}
			}
				else
			{
				$cookies = "Cookie: ".trim($cookie).$nn;
			}
	}

$referer = $referer ? "Referer: ".$referer.$nn : "";
if($proxy)
  {
    list($proxyHost, $proxyPort) = explode(":", $proxy);
    $url = "http://".$host.":".$port.$url;
    $host = $host.":".$port;
  }

$zapros=
"POST ".str_replace(" ", "%20", $url)." HTTP/1.0".$nn.
"Host: ".$host.$nn.$cookies.
"Content-Type: multipart/form-data; boundary=".$bound.$nn."Content-Length: ".(strlen($postdata)+strlen($nn."--".$bound."--".$nn)+$fileSize).$nn.
"User-Agent: ".$upagent.$nn.
"Accept: text/xml,application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5".$nn.
"Accept-Language: en-en,en;q=0.5".$nn.
"Accept-Charset: windows-1251;koi8-r;q=0.7,*;q=0.7".$nn.
"Connection: Close".$nn.
$auth.
$referer.
$nn.
$postdata;
#print_r($zapros);
#write_file('debug',$zapros);

$fp = @fsockopen($host, $port, $errno, $errstr, 150);
stream_set_timeout($fp, 300);

if($errno || $errstr)
	{
  		$lastError = 'err'.$errstr;
		return false;
	}


echo "File <b>".$filename."</b>, size <b>".bytesToKbOrMb($fileSize)."</b>...<br>";
global $id;
$id = md5(time() * rand(0,10));
?>
<div id=<?php echo $id; ?>>
<table cellspacing=0 cellpadding=0 style="FONT-FAMILY: Tahoma; FONT-SIZE: 11px;" id=progressblock>
<tr>
	<td width=100>&nbsp;</td>
	<td width=300 nowrap>
		<div style='border:1px solid royalblue; width:300px; height:10px;'>
			<div id="progress" style='background-color:#FFFFFF; width:0%; height:10px;'>
    		</div>
		</div>
	</td>
<td width=100>&nbsp;</td>
<tr>
	<td align=right id=received width=100 nowrap>0 KB</td>
	<td align=center id=percent width=300>0%</td>
	<td align=left id=speed width=100 nowrap>0 KB/s</td>
</tr>
</table>
<script>
function pr(percent, received, speed)
{
	document.getElementById("received").innerHTML = '<b>' + received + '</b>';
	document.getElementById("percent").innerHTML = '<b>' + percent + '%</b>';
	document.title='Uploading ' + percent + '% ['+orlink+']';
	if (percent > 90) {percent=percent-1;}
	document.getElementById("progress").style.width = percent + '%';
	document.getElementById("speed").innerHTML = '<b>' + speed + ' KB/s</b>';
	return true;
}

function mail(str, field)
{
	document.getElementById("mailPart." + field).innerHTML = str;
	return true;
}
</script>
<br>
</div>
<?php

flush();

$timeStart=getmicrotime();
$len=strlen($zapros);

$chunkSize=GetChunkSize($fileSize);

fputs($fp,$zapros);
fflush($fp);

################################################################
								//echo '| '.$zapros.' |';

$pac=ceil($fileSize / $chunkSize);
$fs=fopen($file,'r');

$i=0;

$local_sleep=$sleep_count;
while (!feof($fs))
	{
		$data=fread($fs,$chunkSize);
		if ($data === false)
			{
				fclose($fs);
				fclose($fp);
				html_error('Error READ Data');
			}

	 	if (($sleep_count !== false) && ($sleep_time !== false) && is_numeric($sleep_time) && is_numeric($sleep_count) && ($sleep_count > 0) && ($sleep_time > 0))
	 		{
	 			$local_sleep--;
	 			if ($local_sleep == 0)
	 				{
	 					usleep($sleep_time);
	 					$local_sleep=$sleep_count;
	 				}
			}

		$sendbyte=fputs($fp,$data);
		fflush($fp);

		if ($sendbyte === false)
			{
				fclose($fs);
				fclose($fp);
				html_error('Error SEND Data');
			}

		$totalsend+=$sendbyte;

		$time = getmicrotime() - $timeStart;
		$chunkTime = $time - $lastChunkTime;
		$chunkTime = $chunkTime ? $chunkTime : 1;
		$lastChunkTime = $time;
		$speed = round($sendbyte / 1024 / $chunkTime, 2);
		$percent = round($totalsend / $fileSize * 100, 2);
		echo "<script>pr(".$percent.", '".bytesToKbOrMb($totalsend)."', ".$speed.")</script>\n";
		flush();
	}
fclose($fs);

fputs($fp,$nn."--".$bound."--".$nn);
fflush($fp);

while(!feof($fp))
	{
	  	$data=fgets($fp,1024);
  		if ($data === false) {break;}
		$page.=$data;
	};

fclose($fp);

return $page;
}
?>