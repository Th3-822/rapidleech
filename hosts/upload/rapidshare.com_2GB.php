<?php

//If you don't submit form logins when uploading, and these values are set, these default values will be used. For auto-upload, you must set these values here.
//If you do set logins here, make sure also to set your account type properly (premium or collector) with the $zone variable below.
//According to RSM (for XP), only **Rapidshare Premium** account is allowed to upload files > 200MB up to 2GB, whereas Collector accounts can only upload up to 200MB.

$site_login = '';
$site_pass = '';
$zone = 'prem';		//prem|col - max filesize 200MB for collector, see RSM (Rapidshare Manager)
$carrier = 'l3';	//which upload carrier to use depending on your location, usually 'l3' (Level3) is most suitable


//////////////////////////////////////////////////////////////////////////////////////////////////////////////
if (!($_REQUEST['action'] == 'COMMENCEUPLOAD') && !isset($_REQUEST['auul']))
{
	echo <<<HTML
		<div id='login' width='100%' align='center'>Enter your Rapidshare Login Details</div><br />
		<table border='0' style="width:350px;" cellspacing='0' align='center'>
		<form action='' method='post'>
			<input type='hidden' name='action' value='COMMENCEUPLOAD' />
			<tr><td nowrap>&nbsp;Username*</td><td>&nbsp;<input type='text' name='my_login' value='' style="width:160px;" />&nbsp;</td></tr>
			<tr><td nowrap>&nbsp;Password*</td><td>&nbsp;<input type='password' name='my_pass' value='' style="width:160px;" />&nbsp;</td></tr>
			<tr><td nowrap>&nbsp;Upload Carrier</td><td>&nbsp;
			<select style="width:160px;" name='carrier'>
					<option value='l3'/>Level 3 (default)</option>
					<option value='tl'/>Telia</option>
					<option value='tl2'/>Telia2</option>
					<option value='cg'/>Cogent</option>
			</select>
			</td></tr>
			<tr><td nowrap>&nbsp;Account Type*</td><td>&nbsp;<select style="width:160px;" name='zone'><option value='prem'/>Premium</option><option value='col'/>Collector</option></select></td></tr>
			<tr><td colspan='2' align='center'><input type='submit' value='Upload' onclick='$(this).fade();'></td></tr>
			<tr><td align='center' colspan='2'><small>Submit the form without logins to use default values stored in rapidshare.com_2GB.php</small></td></tr>
		</form>
		</table>
HTML;
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
			$_REQUEST['carrier'] = $carrier;
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

try
{
	//initiate the RS uploader class
	$rs = new RS($lfile, $_REQUEST['zone'], $_REQUEST['carrier']);

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
	public $file;	// the full path to the file we want to upload
	public $zone = 'prem';	// set to 'prem' or 'col' depending on your account type. Use premium account-type by default. You should do this from the same area as where you set your login and pass above!
	public $uploadpath = 'l3';	// depending on your [server|pc] location you can change this to any of the carriers rs.com uses such as 'cg' or others
	public $download_link;
	public $delete_link;
	private $filename;	// extracted from $this->file ( see getfilesize() )
	private $login;
	private $password;
	private $uploadserver;	// This is the next upload server number e.g. 530. Don't confuse with $uploadpath!
	private $fulluploadserver = array();	// an array resulting from a parse_url of the combined details above
	private $fsize;	// the size of the file we're uploading
	private $wantchunksize = 1000000;	// you might want to leave this as default! (rapidshare don't allow anything below this anyway, but you could try increasing it if you have very large files to upload)
	private $contentheader;
	private $boundary = '---------------------632865735RS4EVER5675865';
	private $useragent = 'RAPIDSHARE MANAGER Application Version: NOT INSTALLED VERSION STARTED';
	private $resumed = 0;
	private $complete = 0;
	private $fileid;
	private $killcode;
	
	public function __construct($file,$zone,$carrier)
	{
		$this->login = trim($_REQUEST['my_login']);
		$this->password = trim($_REQUEST['my_pass']);
		if ($zone) $this->zone = $zone;
		if ($carrier) $this->uploadpath = $carrier;
		$this->getfilesize($file);
		$this->getuploadserver();
	}

	private function getfilesize($file)
	{
		$this->file = realpath($file);
		if (!($this->fsize = filesize($this->file))) throw new Exception('Filesize not obtained - upload halted.'); //("File $this->file is empty or does not exist!\r\n");
		if (($this->fsize > 200*pow(1024, 2)) && $this->zone == 'col') throw new Exception('FILE TOO BIG - Only premium accounts can upload files over 200MB in size');
		$this->filename = basename($this->file);
		echo "<center><b>Total Filesize (bytes): " . $this->fsize . '</b></center>';
	}

	private function getuploadserver()
	{
		if (!($data = file_get_contents('http://rapidshare.com/cgi-bin/rsapi.cgi?sub=nextuploadserver_v1'))) throw new Exception("Unable to get next upload server!");
		if (!preg_match('/(\d+)/', $data, $uploadserver)) throw new Exception("Uploadserver invalid? Internal error!");
		$this->uploadserver = $uploadserver[1];
		$this->fulluploadserver = parse_url('http://rs' . $this->uploadserver . $this->uploadpath . '.rapidshare.com');
	}

	public function upload()
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

			//echo "Upload chunk is $chunksize bytes starting at $cursize...<br />";

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

			$result = '';
			while(!feof($socket)) $result .= fgets($socket, 16384);
			//file_put_contents('rsresult.log', $result . "\r\n\r\n", FILE_APPEND);

			if (preg_match('#(ERROR: .+)#', $result, $errmat)) throw new Exception($errmat[1]);

			if (!$this->resumed)
			{
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
//latest update 16 Apr 2010 r7 beta
?>