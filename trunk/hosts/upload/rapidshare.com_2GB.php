<?php

//Input your RS.com username and password. Also make sure to set your account type properly (premium or collector) with the $zone variable below.
$site_login = '';
$site_pass = '';
$zone = 'prem';		//prem|col



/////////////////////////////////////////////////
$not_done=true;
$continue_up=false;
if ($site_login && $site_pass)
{
	$_REQUEST['my_login'] = $site_login;
	$_REQUEST['my_pass'] = $site_pass;
	$_REQUEST['action'] = "FORM";
	echo "<center><b>Use Default login/pass...</b></center>\n";
}
if ($_REQUEST['action'] == "FORM")
{
	$continue_up=true;
}
else
{
	echo <<<EOF
	<div id=login width=100% align=center>Login to Site</div>
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

	if ( empty($_REQUEST['my_login']) || empty($_REQUEST['my_pass']) ) html_error('No user and pass given', 0);
	echo "<script>document.getElementById('login').style.display='none';</script>";

	/////Only change the values below if you know what you are doing, or if you want to experiment!
	
	class RS
	{
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
			if (!($this->fsize = filesize($this->file))) die("File $this->file is empty or does not exist!\r\n");
			$this->filename = basename($this->file);
			echo "Filesize: " . $this->fsize;
			//$this->wantchunksize = GetChunkSize($this->fsize);
		}

		function getuploadserver()
		{
			if (!($data = file_get_contents('http://rapidshare.com/cgi-bin/rsapi.cgi?sub=nextuploadserver_v1'))) die ("Unable to get next upload server!");
			if (!preg_match('/(\d+)/', $data, $uploadserver)) die ("Uploadserver invalid? Internal error!\r\n");
			$this->uploadserver = $uploadserver[1];
			$this->fulluploadserver = parse_url('http://rs' . $this->uploadserver . $this->uploadpath . '.rapidshare.com');
		}

		function upload()
		{
			require (TEMPLATE_DIR . '/uploadui.php');
			$timeStart = getmicrotime ();
			if (!($fh = fopen($this->file, 'r'))) die('Unable to open file: ' . $this->filename);
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

				/*if (!$this->resumed)
				{
					$this->contentheader .= "$this->boundary\r\nContent-Disposition: form-data; name=\"replacefileid\"\r\n\r\n$this->replacefileid\r\n";
					$this->contentheader .= "$this->boundary\r\nContent-Disposition: form-data; name=\"replacekillcode\"\r\n\r\n$this->replacekillcode\r\n";
				}*/

				if (!$this->complete) $this->contentheader .= "$this->boundary\r\nContent-Disposition: form-data; name=\"incomplete\"\r\n\r\n1\r\n";

				$this->contentheader .= "$this->boundary\r\nContent-Disposition: form-data; name=\"filecontent\"; filename=\"$this->filename\"\r\n\r\n";
				$contenttail = "\r\n$this->boundary--\r\n";
				$contentlength = strlen($this->contentheader) + $chunksize + strlen($contenttail);
				$header = 'POST /cgi-bin/' . ($this->resumed ? 'uploadresume.cgi' : 'upload.cgi') . " HTTP/1.1\r\nContent-Type: multipart/form-data; boundary=$this->boundary\r\nContent-Length: $contentlength\r\nUser-Agent: $this->useragent\r\n\r\n";

				//echo ftell($fh) . '<br />';
				//fseek($fh, $cursize, 0);
				//echo ftell($fh) . '<br />';
				$socket = fsockopen($rsip, 80, $errno, $errstr, 30) or die ("Unable to open socket: $errstr");
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

				echo "<script type='text/javascript' language='javascript'>pr('" . $percent . "', '" . bytesToKbOrMb ( $cursize ) . "', '" . $speed . "');</script>\n";
				flush();

				if (!$this->resumed)
				{
					$result = '';
					while(!feof($socket)) $result .= fgets($socket, 128);
					if (!$result) die("Ooops! Did not receive any valid rapidshare server results?");
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
				//$this->contentheader = '';
				fclose($socket);
			}
			fclose($fh);
		}
	}

	//initiate the RS uploader class
	$rs = new RS($lfile);

	//did the user set their account type?
	if ($zone) $rs->zone = $zone;

	//upload the file
	$rs->upload();
	
	echo "<script>document.getElementById('progressblock').style.display='none';</script>";
	$download_link = $rs->download_link;
	$delete_link = $rs->delete_link;
}
?>