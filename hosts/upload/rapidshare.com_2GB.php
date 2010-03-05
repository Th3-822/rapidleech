<?php

//If you don't submit form logins when uploading, and these values are set, these default values will be used. For auto-upload, you must set these values here.
//If you do set logins here, make sure also to set your account type properly (premium or collector) with the $zone variable below.

$site_login = '';
$site_pass = '';
$zone = 'prem';		//prem|col


//////////////////////////////////////////////////////////////////////////////////////////////////////////////
if (!($_REQUEST['action'] == 'COMMENCEUPLOAD') && !isset($_REQUEST['auul']))
{
	echo <<<EOF
		<div id=login width=100% align=center>Login to Site</div>
		<table border=0 style="width:350px;" cellspacing=0 align=center>
		<form method=post>
			<input type=hidden name=action value='COMMENCEUPLOAD' />
			<tr><td nowrap>&nbsp;Username*</td><td>&nbsp;<input type=text name=my_login value='' style="width:160px;" />&nbsp;</td></tr>
			<tr><td nowrap>&nbsp;Password*</td><td>&nbsp;<input type=password name=my_pass value='' style="width:160px;" />&nbsp;</td></tr>
			<tr><td nowrap>&nbsp;Account Type*</td><td>&nbsp;<select style="width:160px;" name='zone'><option value='prem'/>Premium</option><option value='col'/>Collector</option></td></tr>
			<tr><td colspan=2 align=center><input type=submit value='Upload'></td></tr>
			<tr><td align='center' colspan='2'><small>Submit the form without logins to use default values stored in rapidshare.com_2GB.php file</small></td></tr>
		</form>
		</table>
EOF;
exit;
}
else
{
	if (empty($_REQUEST['my_login']) || empty($_REQUEST['my_pass']))
	{
		if ($site_login && $site_pass && $zone)
		{
			$_REQUEST['my_login'] = $site_login;
			$_REQUEST['my_pass'] = $site_pass;
			$_REQUEST['zone'] = $zone;
			$_REQUEST['action'] = 'COMMENCEUPLOAD';
			echo "<center><b>Use Default login/pass...</b></center>\n";
		}
		else html_error('Not all required values were set. Either enter your user and pass and account type, or enter them inside the file.');
	}
	else
	{
		$_REQUEST['action'] = 'COMMENCEUPLOAD';
	}
}

echo "<script>document.getElementById('login').style.display='none';</script>";

try
{
	//initiate the RS uploader class
	$rs = new RS($lfile);

	//did the user set their account type?
	if ($_REQUEST['zone']) $rs->zone = $_REQUEST['zone'];

	//upload the file
	$rs->upload();

	echo "<script>document.getElementById('progressblock').style.display='none';</script>";
	$download_link = $rs->download_link;
	$delete_link = $rs->delete_link;
}
catch (Exception $e)
{
	html_error($e->getMessage());
}

class RS
{
	/////Only change the values below if you know what you are doing, or if you want to experiment!
	var $file;	// the full path to the file we want to upload
	var $filename;	// extracted from $this->file ( see getfilesize() )
	var $zone = 'prem';	// set to 'prem' or 'col' depending on your account type. Use premium account-type by default. You should do this from the same area as where you set your login and pass above!
	var $login;
	var $password;
	var $uploadpath = 'l3';	// depending on your [server|pc] location you can change this to any of the carriers rs.com uses such as 'cg' or others
	var $uploadserver;	// This is the next upload server number e.g. 530. Don't confuse with $uploadpath!
	var $fulluploadserver = array();	// an array resulting from a parse_url of the combined details above
	var $fsize;	// the size of the file we're uploading
	var $wantchunksize = 1000000;	// you might want to leave this as default! (rapidshare don't allow anything below this anyway, but you could try increasing it if you have very large files to upload)
	var $contentheader;
	var $boundary = '---------------------632865735RS4EVER5675865';
	var $useragent = 'RAPIDSHARE MANAGER Application Version: NOT INSTALLED VERSION STARTED';
	var $resumed = 0;
	var $complete = 0;
	var $replacefileid;
	var $replacekillcode;
	var $fileid;
	var $killcode;
	var $download_link;
	var $delete_link;


	function __construct($filename)
	{
		$this->login = trim($_REQUEST['my_login']);
		$this->password = trim($_REQUEST['my_pass']);
		$this->getfilesize($filename);
		$this->getuploadserver();
	}

	function getfilesize($filename)
	{
		$this->file = realpath($filename);
		if (!($this->fsize = filesize($this->file))) throw new Exception('Filesize not obtained - upload halted.'); //("File $this->file is empty or does not exist!\r\n");
		$this->filename = basename($this->file);
		echo "Filesize: " . $this->fsize;
	}

	function getuploadserver()
	{
		if (!($data = file_get_contents('http://rapidshare.com/cgi-bin/rsapi.cgi?sub=nextuploadserver_v1'))) throw new Exception("Unable to get next upload server!");
		if (!preg_match('/(\d+)/', $data, $uploadserver)) throw new Exception("Uploadserver invalid? Internal error!");
		$this->uploadserver = $uploadserver[1];
		$this->fulluploadserver = parse_url('http://rs' . $this->uploadserver . $this->uploadpath . '.rapidshare.com');
	}

	function upload()
	{
		require (TEMPLATE_DIR . '/uploadui.php');
		$timeStart = getmicrotime();
		if (!($fh = fopen($this->file, 'r'))) throw new Exception('Unable to open file: ' . $this->filename);
		$rsip = gethostbyname($this->fulluploadserver['host']);
		$cursize = 0;
		while ($cursize < $this->fsize)
		{
			if ($this->fsize > $this->wantchunksize)
			{
				$chunksize = $this->fsize - $cursize;
				if ($chunksize > $this->wantchunksize)
				{
					$chunksize = $this->wantchunksize;
				}
				else
				{
					$this->complete = 1;
				}
			}
			else
			{
				$chunksize = $this->fsize;
				$this->complete = 1;
			}

			print "Upload chunk is $chunksize bytes starting at $cursize...<br />";

			$this->contentheader = "$this->boundary\r\nContent-Disposition: form-data; name=\"rsapi_v1\"\r\n\r\n1\r\n";

			if ($this->resumed)
			{
				$this->contentheader .= "$this->boundary\r\nContent-Disposition: form-data; name=\"fileid\"\r\n\r\n$this->fileid\r\n";
				$this->contentheader .= "$this->boundary\r\nContent-Disposition: form-data; name=\"killcode\"\r\n\r\n$this->killcode\r\n";
				if ($this->complete) $this->contentheader .= "$this->boundary\r\nContent-Disposition: form-data; name=\"complete\"\r\n\r\n1\r\n";
			}

			if (!$this->resumed && $this->zone == "prem" && $this->login && $this->password)
			{
				$this->contentheader .= "$this->boundary\r\nContent-Disposition: form-data; name=\"login\"\r\n\r\n$this->login\r\n";
				$this->contentheader .= "$this->boundary\r\nContent-Disposition: form-data; name=\"password\"\r\n\r\n$this->password\r\n";
			}

			if (!$this->resumed && $this->zone == "col" && $this->login && $this->password)
			{
				$this->contentheader .= "$this->boundary\r\nContent-Disposition: form-data; name=\"freeaccountid\"\r\n\r\n$this->login\r\n";
				$this->contentheader .= "$this->boundary\r\nContent-Disposition: form-data; name=\"password\"\r\n\r\n$this->password\r\n";
			}

			if (!$this->complete) $this->contentheader .= "$this->boundary\r\nContent-Disposition: form-data; name=\"incomplete\"\r\n\r\n1\r\n";

			$this->contentheader .= "$this->boundary\r\nContent-Disposition: form-data; name=\"filecontent\"; filename=\"$this->filename\"\r\n\r\n";
			$contenttail = "\r\n$this->boundary--\r\n";
			$contentlength = strlen($this->contentheader) + $chunksize + strlen($contenttail);
			$header = 'POST /cgi-bin/' . ($this->resumed ? 'uploadresume.cgi' : 'upload.cgi') . " HTTP/1.1\r\nContent-Type: multipart/form-data; boundary=$this->boundary\r\nContent-Length: $contentlength\r\nUser-Agent: $this->useragent\r\n\r\n";

			if (!($socket = fsockopen($rsip, 80, $errno, $errstr, 30))) throw new Exception("Unable to open socket: $errstr");
			fwrite($socket, "$header$this->contentheader");
			$buffer = fread($fh, $this->wantchunksize);
			$bufferlen = strlen($buffer);
			$cursize += $bufferlen;
			$sentbytes = fwrite($socket, "$buffer");
			echo "Bytes written: $sentbytes<br />";
			$time = getmicrotime () - $timeStart;
			$chunkTime = $time - $lastChunkTime;
			$chunkTime = $chunkTime ? $chunkTime : 1;
			$lastChunkTime = $time;
			$speed = round ( $sentbytes / 1024 / $chunkTime, 2 );
			$percent = round ( $cursize / $this->fsize*100, 2 );
			fwrite($socket, $contenttail);
			fflush($socket);

			//$result = '';
			//while(!feof($socket)) $result .= fgets($socket, 256);
			//file_put_contents('rsresult.log', $result, FILE_APPEND);

			if (!$this->resumed)
			{
				$result = '';
				while(!feof($socket)) $result .= fgets($socket, 128);
				if (!$result) throw new Exception("Ooops! Did not receive any valid rapidshare server results? Upload halted.");
				preg_match('#/files/(\d+)/#', $result, $fileid);
				preg_match('#killcode=(\d+)\r?\n#', $result, $killcode);
				preg_match('%http://rapidshare\.com/((?!killcode).)+html%i', $result, $flink);
				preg_match('%http://rapidshare\.com/.*killcode.*%i', $result, $dlink);
				$this->download_link = trim($flink[0]);
				$this->delete_link = trim($dlink[0]);
				$this->fileid = $fileid[1];
				$this->killcode = $killcode[1];
				$this->resumed = 1;
			}
			fclose($socket);
			
			echo "<script type='text/javascript' language='javascript'>pr('" . $percent . "', '" . bytesToKbOrMb ( $cursize ) . "', '" . $speed . "');</script>\n";
			flush();
		}
		fclose($fh);
	}
}

//created by szalinski 2009
//latest update 05 Mar 2010 r5 beta
?>