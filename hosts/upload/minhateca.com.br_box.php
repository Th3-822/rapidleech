<?php

####### Free Account Info. ###########
$minhateca_login = ""; //set your id (login)
$minhateca_pass = ""; //set your  password

##############################

$host="box.minhateca.com.br";

$not_done=true;
$continue_up=false;
if ($minhateca_login && $minhateca_pass){
		$_REQUEST['my_login'] = $minhateca_login;
		$_REQUEST['my_pass'] = $minhateca_pass;
		$_REQUEST['action'] = "FORM";
		echo "<b><center>Use Default login/pass.</center></b>\n";
}
if ($_REQUEST['action'] == "FORM")
	$continue_up=true;
else{
?>
<table border=0 style="width:270px;" cellspacing=0 align=center>
<form method=post>
<input type=hidden name=action value='FORM' />
<tr><td nowrap>&nbsp;User*<td>&nbsp;<input type=text name=my_login value='' style="width:160px;" />&nbsp;</tr>
<tr><td nowrap>&nbsp;Password*<td>&nbsp;<input type=password name=my_pass value='' style="width:160px;" />&nbsp;</tr>
<tr><td colspan=2 align=center><input type=submit value='Upload' /></tr>
<tr><td colspan=2 align=center><small>*You can set it as default in <b><?php echo $page_upload["minhateca.com.br_box"]; ?></b></small></tr>
</table>
</form>
<?php
		}
if ($continue_up)
		{
				$not_done=false;
?>
<table width=600 align=center>
</td></tr>
<tr><td align=center>
<div id=login width=100% align=center>Login to <?php echo $host; ?></div>
<?php 
						$post = 
							'<?xml version="1.0" encoding="UTF-8"?>' .
							'<s:Envelope xmlns:s="http://schemas.xmlsoap.org/soap/envelope/" s:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">' .
							  '<s:Body>' .
							    '<Auth xmlns="http://chomikuj.pl/">' .
							      '<name>' . $_REQUEST['my_login'] . '</name>' .
							      '<passHash>' . strtolower(md5($_REQUEST['my_pass'])) . '</passHash>' .
							      '<ver>4</ver>' .
							      '<client>' .
							        '<name>chomikbox</name>' .
							        '<version>2.0.8.1</version>' .
							      '</client>' .
							    '</Auth>' .
							  '</s:Body>' .
							'</s:Envelope>';
						
						$page = geturl($host, 80, "/services/ChomikBoxService.svc", "http://".$host."/\r\nSOAPAction: http://chomikuj.pl/IChomikBoxService/Auth\r\nContent-Type: text/xml;charset=utf-8\r\nUser-Agent: Mozilla/5.0", 0, $post, 0, $_GET["uproxy"], $pauth);
						is_page($page);
						
						
						preg_match('/\<a:token\>(.*?)\<\/a:token\>/', $page, $temp);
						if($temp)
						{
							$auth_token = $temp[1];
						} else {
							html_error ('Login error');
						}
						
						preg_match('/\<a:hamsterId\>(.*?)\<\/a:hamsterId\>/', $page, $temp);
						if($temp)
						{
							$chomik_id = $temp[1];
						} else {
							html_error ('Login error');
						}

		?>
<!--<script>document.getElementById('login').style.display='none';</script>-->
<div id=info width=100% align=center>Retrive upload ID</div>
<script>document.getElementById('info').style.display='none';</script>
<?php 
						$post = 
							'<?xml version="1.0" encoding="UTF-8"?>' .
							'<s:Envelope xmlns:s="http://schemas.xmlsoap.org/soap/envelope/" s:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">' . 
							  '<s:Body>' .
							    '<UploadToken xmlns="http://chomikuj.pl/">' .
							      '<token>' . $auth_token . '</token>' .
							      '<folderId>0</folderId>' .
							      '<fileName>' . $lname . '</fileName>' .
							    '</UploadToken>' .
							  '</s:Body>' .
							'</s:Envelope>';

						$page = geturl($host, 80, "/services/ChomikBoxService.svc", "http://".$host."/\r\nSOAPAction: http://chomikuj.pl/IChomikBoxService/UploadToken\r\nContent-Type: text/xml;charset=utf-8\r\nUser-Agent: Mozilla/5.0", 0, $post, 0, $_GET["uproxy"], $pauth);
						is_page($page);

						preg_match('/\<a:key\>(.*?)\<\/a:key\>/', $page, $temp);
						if($temp)
						{
							$upload_key = $temp[1];
						} else {
							html_error ('Error fetching upload page');
						}
						
						preg_match('/\<a:stamp\>(.*?)\<\/a:stamp\>/', $page, $temp);
						if($temp)
						{
							$upload_time = $temp[1];
						} else {
							html_error ('Error fetching upload page');
						}
						
						preg_match('/\<a:server\>(.*?)\<\/a:server\>/', $page, $temp);
						if($temp)
						{
							$upload_server = $temp[1];
							$upload_host = explode(":", $upload_server)[0];
							$upload_port = explode(":", $upload_server)[1];
						} else {
							html_error ('Error fetching upload page');
						}

						$upfiles = MT_upfile($upload_host, $upload_port, $upload_time, $chomik_id, $upload_key, $lfile, $lname, $_GET["uproxy"], $pauth)
						
?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?php   
						is_page($upfiles);					
						is_notpresent($upfiles, 'HTTP/1.1 200', 'Upload error');
						
						$pos = strpos($upfiles, '<resp res="1" fileid=');
						if($pos == false)
						{
							html_error ('Upload error');
						}
						
						preg_match('/fileid\="(.*?)"/', $upfiles, $temp);
						if($temp)
						{
							$fileid = $temp[1];
						} else {
							html_error ('Fileid not found');
						}
						
						$post = 
							'<?xml version="1.0" encoding="UTF-8"?>' .
							'<s:Envelope xmlns:s="http://schemas.xmlsoap.org/soap/envelope/" s:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">' .
								'<s:Body>' .
									'<Download xmlns="http://chomikuj.pl/">' .
										'<token>' . $auth_token . '</token>' .
										'<sequence>' .
											'<stamp>' . rand(0,25000) . '</stamp>' .
											'<part>0</part>' .
											'<count>1</count>' .
										'</sequence>' .
										'<disposition>download</disposition>' .
										'<list>' .
											'<DownloadReqEntry>' .
												'<id>' . $fileid . '</id>' .
											'</DownloadReqEntry>' .
										'</list>' .
									'</Download>' .
								'</s:Body>' .
							'</s:Envelope>';
						
						$page = geturl($host, 80, "/services/ChomikBoxService.svc", "http://".$host."/\r\nSOAPAction: http://chomikuj.pl/IChomikBoxService/Download\r\nContent-Type: text/xml;charset=utf-8\r\nUser-Agent: Mozilla/5.0", 0, $post, 0, $_GET["uproxy"], $pauth);
						is_page($page);
						
						$dl = array();
						preg_match('/\<globalId\>(.*?)\<\/globalId\>/', $page, $temp);
						if($temp)
						{
							$dl['globalId'] = $temp[1];
						} else {
							html_error ('Error retrive download link!');
						}
						
						preg_match_all('/\<name\>(.*?)\<\/name\>/', $page, $temp);
						if($temp)
						{
							$dl['name'] = $temp[1][1];
							$dl['filename'] = pathinfo($dl['name'], PATHINFO_FILENAME);
							$dl['extension'] = pathinfo($dl['name'], PATHINFO_EXTENSION);
						} else {
							html_error ('Error retrive download link!');
						}
						
						$download_link = 'http://minhateca.com.br'.$dl['globalId'].'/'.$dl['filename'].','.$fileid.'.'.$dl['extension'];
						
		}


function MT_upfile($host, $port, $time, $chomik_id, $key, $file, $filename, $proxy = 0, $pauth = 0) {
	global $nn, $lastError, $fp, $fs;
	
	if (!isset($nn)) $nn = "\r\n";
	
	$scheme = 'http://';
	$url = '/file/';
	
	$upagent = 'Mozilla/5.0';
	$bound = "!CHB$time";
	$saveToFile = 0;
	$postdata = '';
	
	//chomik_id
	$postdata .= '--' . $bound . $nn;
	$postdata .= "name=\"chomik_id\"$nn";
	$postdata .= "Content-Type: text/plain$nn$nn";
	$postdata .= "$chomik_id$nn";
	
	//folder_id
	$postdata .= '--' . $bound . $nn;
	$postdata .= "name=\"folder_id\"$nn";
	$postdata .= "Content-Type: text/plain$nn$nn";
	$postdata .= "0$nn";
	
	//key
	$postdata .= '--' . $bound . $nn;
	$postdata .= "name=\"key\"$nn";
	$postdata .= "Content-Type: text/plain$nn$nn";
	$postdata .= "$key$nn";
	
	//time
	$postdata .= '--' . $bound . $nn;
	$postdata .= "name=\"time\"$nn";
	$postdata .= "Content-Type: text/plain$nn$nn";
	$postdata .= "$time$nn";
	
	//client
	$postdata .= '--' . $bound . $nn;
	$postdata .= "name=\"client\"$nn";
	$postdata .= "Content-Type: text/plain$nn$nn";
	$postdata .= "MinhaBox.br-2.0.8.1$nn";
	
	//locale
	$postdata .= '--' . $bound . $nn;
	$postdata .= "name=\"locale\"$nn";
	$postdata .= "Content-Type: text/plain$nn$nn";
	$postdata .= "BR$nn";

	$fieldname = 'file';
	if (!is_readable($file)) {
		$lastError = sprintf(lang(65), $file);
		return FALSE;
	}
	$fileSize = getSize($file);

	$postdata .= '--' . $bound . $nn;
	$postdata .= "name=\"$fieldname\"; filename=\"$filename\"$nn$nn";
	if (!empty($cookie)) {
		if (is_array($cookie)) $cookies = (count($cookie) > 0) ? CookiesToStr($cookie) : 0;
		else $cookies = trim($cookie);
	}
	if ($scheme == 'https://') {
		if (!extension_loaded('openssl')) return html_error('You need to install/enable PHP\'s OpenSSL extension to support uploading via HTTPS.');
		$scheme = 'tls://';
		if ($port == 0 || $port == 80) $port = 443;
	} else if ($port == 0) $port = 80;
	if (!empty($referer) && ($pos = strpos("\r\n", $referer)) !== 0) {
		$origin = parse_url($pos ? substr($referer, 0, $pos) : $referer);
		$origin = strtolower($origin['scheme']) . '://' . strtolower($origin['host']) . (!empty($origin['port']) && $origin['port'] != defport(array('scheme' => $origin['scheme'])) ? ':' . $origin['port'] : '');
	} else $origin = ($scheme == 'tls://' ? 'https://' : $scheme) . $host . ($port != 80 && ($scheme != 'tls://' || $port != 443) ? ':' . $port : '');
	if ($proxy) {
		list($proxyHost, $proxyPort) = explode(':', $proxy, 2);
		if ($scheme != 'tls://') {
			$host = $host . ($port != 80 && $port != 443 ? ":$port" : '');
			$url = "$scheme$host$url";
		}
	}
	if ($scheme != 'tls://') $scheme = '';
	$cHeaders = readCustomHeaders($referer);
	$request = array();
	$request[''] = 'POST ' . str_replace(' ', '%20', $url) . ' HTTP/1.1';
	
	$request['Content-Type'] = "multipart/mixed; boundary=$bound";
	$request['User-Agent'] = $upagent;
	$request['Pragma'] = 'no-cache';
	$request['Cache-Control'] = 'no-cache';
	$request['Host'] = $host.':'.$port;

	if (!empty($pauth) && !$scheme) $request['proxy-authorization'] = "Basic $pauth";
	$request['content-length'] = (strlen($postdata) + strlen($nn . "--" . $bound . "--" . $nn) + $fileSize);
	$request = headers2request(array_merge($request, $cHeaders), $postdata);
	$errno = 0;
	$errstr = '';
	if ($scheme == 'tls://') {
		$hosts = (!empty($proxyHost) ? $proxyHost : $scheme . $host) . ':' . (!empty($proxyPort) ? $proxyPort : $port);
		if ($proxy) $url = "https://$host$url"; // For the 'connected to' message
	} else $hosts = (!empty($proxyHost) ? $scheme . $proxyHost : $scheme . $host) . ':' . (!empty($proxyPort) ? $proxyPort : $port);
	$fp = @stream_socket_client($hosts, $errno, $errstr, 120, STREAM_CLIENT_CONNECT);
	if (!$fp) {
		if (!function_exists('stream_socket_client')) return html_error('[ERROR] stream_socket_client() is disabled.');
		$dis_host = !empty($proxyHost) ? $proxyHost : $host;
		$dis_port = !empty($proxyPort) ? $proxyPort : $port;
		return html_error(sprintf(lang(88), $dis_host, $dis_port));
	}
	if ($errno || $errstr) {
		$lastError = $errstr;
		return false;
	}
	if ($proxy) echo '<p>' . sprintf(lang(89), $proxyHost, $proxyPort) . '<br />UPLOAD: <b>' . htmlspecialchars($url) . "</b>...<br />\n";
	else echo '<p>'.sprintf(lang(90), $host, $port).'</p>';
	if ($scheme == 'tls://' && $proxy) {
		$connRequest = array();
		$connRequest[''] = "CONNECT $host:$port HTTP/1.1";
		if (!empty($pauth)) $connRequest['proxy-authorization'] = "Basic $pauth";
		$connRequest['proxy-connection'] = 'Close';
		$connRequest = headers2request($connRequest);
		fwrite($fp, $connRequest);
		fflush($fp);
		$llen = 0;
		$header = '';
		do {
			$header .= fgets($fp, 16384);
			$len = strlen($header);
			if (!$header || $len == $llen) {
				$lastError = 'No response from proxy after CONNECT.';
				stream_socket_shutdown($fp, STREAM_SHUT_RDWR);
				fclose($fp);
				return false;
			}
			$llen = $len;
		} while (strpos($header, $nn . $nn) === false);
		$status = intval(substr($header, 9, 3));
		if ($status != 200) {
			return html_error("Proxy returned $status after CONNECT.");
		}
		// Start TLS.
		if (!stream_socket_enable_crypto($fp, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) return html_error('TLS Startup Error.');
	}
	echo(lang(104) . ' <b>' . htmlspecialchars($filename) . '</b>, ' . lang(56) . ' <b>' . bytesToKbOrMbOrGb($fileSize) . '</b>...<br />');
	$GLOBALS['id'] = md5(time() * rand(0, 10));
	require (TEMPLATE_DIR . '/uploadui.php');
	flush();
	fwrite($fp, $request);
	fflush($fp);
	$timeStart = microtime(true);
	$chunkSize = GetChunkSize($fileSize);
	$fs = fopen($file, 'r');
	$totalsend = $time = $lastChunkTime = 0;
	while (!feof($fs) && !$errno && !$errstr) {
		$data = fread($fs, $chunkSize);
		if ($data === false) {
			fclose($fs);
			fclose($fp);
			return html_error(lang(112));
		}
		$sendbyte = @fwrite($fp, $data);
		fflush($fp);
		if ($sendbyte === false || strlen($data) > $sendbyte) {
			fclose($fs);
			fclose($fp);
			return html_error(lang(113));
		}
		$totalsend += $sendbyte;
		$time = microtime(true) - $timeStart;
		$chunkTime = $time - $lastChunkTime;
		$chunkTime = $chunkTime ? $chunkTime : 1;
		$chunkTime = ($chunkTime > 0) ? $chunkTime : 1;
		$lastChunkTime = $time;
		$speed = round($sendbyte / 1024 / $chunkTime, 2);
		$percent = round($totalsend / $fileSize * 100, 2);
		echo "<script type='text/javascript'>pr('$percent', '" . bytesToKbOrMbOrGb($totalsend) . "', '$speed');</script>\n";
		flush();
	}
	if ($errno || $errstr) {
		$lastError = $errstr;
		return false;
	}
	fclose($fs);
	fwrite($fp, $nn . "--" . $bound . "--" . $nn);
	fflush($fp);
	$llen = 0;
	$header = '';
	do {
		$header .= fgets($fp, 16384);
		$len = strlen($header);
		if (!$header || $len == $llen) {
			$lastError = lang(91);
			stream_socket_shutdown($fp, STREAM_SHUT_RDWR);
			fclose($fp);
			return false;
		}
		$llen = $len;
	} while (strpos($header, $nn . $nn) === false);
	// Array for active stream filters
	$sFilters = array();
	if (stripos($header, "\nTransfer-Encoding: chunked") !== false && in_array('dechunk', stream_get_filters())) $sFilters['dechunk'] = stream_filter_append($fp, 'dechunk', STREAM_FILTER_READ); // Add built-in dechunk filter
	$length = trim(cut_str($header, "\nContent-Length: ", "\n"));
	if (!$length || !is_numeric($length)) $length = -1;
	$page = stream_get_contents($fp, $length);
	stream_socket_shutdown($fp, STREAM_SHUT_RDWR);
	fclose($fp);
	if (empty($sFilters['dechunk']) && stripos($header, "\nTransfer-Encoding: chunked") !== false && function_exists('http_chunked_decode')) {
		$dechunked = http_chunked_decode($page);
		if ($dechunked !== false) $page = $dechunked;
		unset($dechunked);
	}
	if (stripos($header, "\nContent-Encoding: gzip") !== false) {
		$decompressed = gzinflate(substr($page, 10));
		if ($decompressed !== false) $page = $decompressed;
		unset($decompressed);
	} else if (stripos($header, "\nContent-Encoding: deflate") !== false) {
		$decompressed = gzinflate(in_array(substr($page, 0, 2), array("x\x01", "x\x9C", "x\xDA")) ? substr($page, 2) : $page);
		if ($decompressed !== false) $page = $decompressed;
		unset($decompressed);
	}
	$page = $header.$page;
	return $page;
}		
		

// tech - Written in 26/11/2016
?>
