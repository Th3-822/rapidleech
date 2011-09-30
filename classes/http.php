<?php
if (! defined ( 'RAPIDLEECH' )) {
	require ('../deny.php');
	exit ();
}

/**
 * Pauses for countdown timer in file hosts
 * @param int The number of seconds to count down
 * @param string The text you want to display when counting down
 * @param string The text you want to display when count down is complete
 * @param bool
 * @return bool
 */
function insert_timer($countd, $caption = "", $timeouttext = "", $hide = false) {
	global $disable_timer;

	if ($disable_timer === true)
		return true;
	if (! $countd || ! is_numeric ( $countd ))
		return false;

	$timerid = rand ( 1000, time () );
	echo ('<div align="center">');
	echo ('<span id="global' . $timerid . '">');
	echo ('<br />');
	echo ('<span class="caption">' . $caption . '</span>&nbsp;&nbsp;');
	echo ('<span id="timerlabel' . $timerid . '" class="caption"></span></span>');
	echo ('</div>');
	echo ('<script type="text/javascript">');
	echo ('var count' . $timerid . '=' . $countd . ';');
	echo ('function timer' . $timerid . '() {');
	echo ('if(count' . $timerid . ' > 0) {');
	echo ('$("#timerlabel' . $timerid . '").html("' . sprintf ( lang ( 87 ), '" + count' . $timerid . ' + "' ) . '");');
	echo ('count' . $timerid . '--;');
	echo ('setTimeout("timer' . $timerid . '()", 1000);');
	echo ('}');
	echo ('}');
	echo ('timer' . $timerid . '();');
	echo ('</script>');
	flush ();
	for($nnn = 0; $nnn < $countd; $nnn ++) {
		sleep ( 1 );
	}
	flush ();

	if ($hide === true) {
		echo ('<script type="text/javascript">$("#global' . $timerid . '").css("display","none");</script>');
		flush ();
		return true;
	}

	if ($timeouttext) {
		echo ('<script type="text/javascript">$("#global' . $timerid . '").html("' . $timeouttext . '");</script>');
		flush ();
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
	if (! is_numeric ( $countd )) {
		html_error ( lang ( 85 ) );
	}
	echo ('<div id="code"></div>');
	echo ('<div align="center">');
	echo ('<div id="dl"><h4>' . lang ( 86 ) . '</h4></div></div>');
	echo ('<script type="text/javascript">var c = ' . $countd . ';fc("' . $caption . '","' . $displaytext . '");</script>');
	if (! empty ( $text )) {
		print $text;
	}
	require (TEMPLATE_DIR . '/footer.php');
}

/**
 * Function to check if geturl function has completed successfully
 */
function is_page($lpage) {
	global $lastError;
	if (! $lpage) {
		html_error ( lang ( 84 ) . "<br />$lastError", 0 );
	}
}

function geturl($host, $port, $url, $referer = 0, $cookie = 0, $post = 0, $saveToFile = 0, $proxy = 0, $pauth = 0, $auth = 0, $scheme = "http", $resume_from = 0, $XMLRequest=0) {
	global $nn, $lastError, $PHP_SELF, $AUTH, $IS_FTP, $FtpBytesTotal, $FtpBytesReceived, $FtpTimeStart, $FtpChunkSize, $Resume, $bytesReceived, $fs, $force_name, $options;
	$scheme .= "://";

	if (($post !== 0) && ($scheme == "http://" || $scheme == "https://")) {
		$method = "POST";
		$postdata = formpostdata ( $post );
		$length = strlen ( $postdata );
		$content_tl = "Content-Type: application/x-www-form-urlencoded" . $nn . "Content-Length: " . $length . $nn;
	} else {
		$method = "GET";
		$postdata = "";
		$content_tl = "";
	}

	if ($cookie) {
		if (is_array ( $cookie )) {
			$cookies = "Cookie: " . CookiesToStr ( $cookie ) . $nn;
		} else {
			$cookies = "Cookie: " . $cookie . $nn;
		}
	}
	$referer = $referer ? "Referer: " . $referer . $nn : "";

	if ($scheme == "https://") {
		$scheme = "ssl://";
		$port = 443;
	}

	if ($proxy) {
		list ( $proxyHost, $proxyPort ) = explode ( ":", $proxy );
		$host = $host . ($port != 80 && $port != 443 ? ":" . $port : "");
		$url = $scheme . $host . $url;
	}

	if ($scheme != "ssl://") {
		$scheme = "";
	}

	$http_auth = ($auth) ? "Authorization: Basic " . $auth . $nn : "";
	$proxyauth = ($pauth) ? "Proxy-Authorization: Basic " . $pauth . $nn : "";

	$request = $method . " " . str_replace ( " ", "%20", $url ) . " HTTP/1.1" . $nn . "Host: " . $host . $nn . "User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.14) Gecko/20080404 Firefox/2.0.0.14" . $nn . "Accept: */*" . $nn . "Accept-Language: en-us;q=0.7,en;q=0.3" . $nn . "Accept-Charset: utf-8,windows-1251;q=0.7,*;q=0.7" . $nn . "Pragma: no-cache" . $nn . "Cache-Control: no-cache" . $nn . ($Resume ["use"] === TRUE ? "Range: bytes=" . $Resume ["from"] . "-" . $nn : "") . $http_auth . $proxyauth . $referer .($XMLRequest ? "X-Requested-With: XMLHttpRequest" . $nn : ""). $cookies . "Connection: Close" . $nn . $content_tl . $nn . $postdata;

	$errno = 0;
	$errstr = "";
	$hosts = ($proxyHost ? $scheme . $proxyHost : $scheme . $host) . ':' . ($proxyPort ? $proxyPort : $port);
	$fp = @stream_socket_client ( $hosts, $errno, $errstr, 120, STREAM_CLIENT_CONNECT );
	//$fp = @fsockopen($proxyHost ? $scheme.$proxyHost : $scheme.$host, $proxyPort ? $proxyPort : $port, $errno, $errstr, 15);


	if (! $fp) {
		$dis_host = $proxyHost ? $proxyHost : $host;
		$dis_port = $proxyPort ? $proxyPort : $port;
		html_error ( sprintf ( lang ( 88 ), $dis_host, $dis_port ) );
	}

	if ($errno || $errstr) {
		$lastError = $errstr;
		return false;
	}

	if ($saveToFile) {
		if ($proxy) {
			echo '<p>'.sprintf(lang(89),$proxyHost,$proxyPort).'<br />';
			echo "GET: <b>" . $url . "</b>...<br />\n";
		} else {
			echo "<p>";
			printf(lang(90),$host,$port);
			echo "</p>";
		}
	}

	#########################################################################
	fputs ( $fp, $request );
	fflush ( $fp );
	$timeStart = getmicrotime ();

	// Rewrote the get header function according to the proxy script
	// Also made sure it goes faster and I think 8192 is the best value for retrieving headers
	// Oops.. The previous function hooked up everything and now I'm returning it back to normal
	do {
		$header .= fgets ( $fp, 16384 );
	} while ( strpos ( $header, $nn . $nn ) === false );

	#########################################################################


	if (! $header) {
		$lastError = lang(91);
		return false;
	}

	$responsecode = "";
	preg_match ( '/^HTTP\/1\.0|1 ([0-9]+) .*/', $header, $responsecode );
	if (($responsecode [1] == 404 || $responsecode [1] == 403) && $saveToFile) {
		// Do some checking, please, at least tell them what error it was
		if ($responsecode [1] == 403) {
			$lastError = lang(92);
		} elseif ($responsecode [1] == 404) {
			$lastError = lang(93);
		} else {
			// Weird, it shouldn't come here...
			$lastError = lang(94);
		}
		return false;
	}



	if ($saveToFile) {
		//$bytesTotal = intval ( trim ( cut_str ( $header, "Content-Length:", "\n" ) ) );
      if (preg_match("#(a-z0-9.]+)?(([a-z]+).[a-z]+([.a-z]+)?)#", $host, $tmp)) {
            $hostclass = $tmp[2];
            if (substr($hostclass, 0, 1) > 0) {
                $hostclass = "d" . $hostclass;
            }
            $hostclass=str_replace(array("-","."), "_", $hostclass);
            if (file_exists(HOST_DIR."download/".$hostclass.".php")){
                /* @var $hostvar DownloadClass */
                require(HOST_DIR."DownloadClass.php");
                require(HOST_DIR."download/".$hostclass.".php");
                $hostvar = new $hostclass();
                $hostvar->CheckBack($header);
            }
        }
		$bytesTotal = trim ( cut_str ( $header, "Content-Length:", "\n" ) );

		global $options;
		if ($options['file_size_limit'] > 0) {
			if ($bytesTotal > $options['file_size_limit']*1024*1024) {
				$lastError = lang(336) . bytesToKbOrMbOrGb ( $options['file_size_limit']*1024*1024 ) .".";
				return false;
			}
}
		if (stristr ( $host, "rapidshare" ) && $bytesTotal < 10000) {
			while ( ! feof ( $fp ) ) {
				$page_src .= fread ( $fp, 1024 * 8 );
			}
			is_present ( $page_src, "is already in use with another ip", lang(100) );
		}
		if (stristr ( $host, "imageshack" ) && $bytesTotal < 15000) {
			while ( ! feof ( $fp ) ) {
				$page_src .= fread ( $fp, 1024 * 8 );
			}
			is_present ( $page_src, "To avoid creation of corrupted zip files, you cannot create a zip on this torrent until it is done downloading" );
		}
		$redir = "";
		if (trim ( preg_match ( '/[^\-]Location: *(.+)(\r|\n)+/i', $header, $redir ) )) {
			$redirect = $redir [1];
			$lastError = sprintf(lang(95),$redirect);
			return FALSE;
		}
		if (in_array ( cut_str ( $header, "WWW-Authenticate: ", " " ), array ("Basic", "Digest" ) )) {
			$lastError = lang(96);
			return FALSE;
		}
		$ContentType = trim ( cut_str ( $header, "Content-Type:", "\n" ) );
		if (stristr ( $host, "rapidshare" ) && stristr ( $ContentType, "text/html" ) && stristr ( $header, "404 Not Found" )) {
			unset ( $saveToFile );
			$NoDownload = TRUE;
		} elseif (stristr ( $host, "megaupload" ) && stristr ( $ContentType, "text/html" )) {
			unset ( $saveToFile );
			$NoDownload = TRUE;
		}
		if ($Resume ["use"] === TRUE && ! stristr ( $header, "Content-Range:" )) {
			if (stristr ( $header, "503 Limit Exceeded" )) {
				$lastError = lang(97);
			} else {
				$lastError = lang(98);
			}
			return FALSE;
		}

		if ($force_name)
		{
			$FileName = $force_name;
			$saveToFile = dirname ( $saveToFile ) . PATH_SPLITTER . $FileName;
		}
		else
		{
			$ContentDisposition = trim ( cut_str ( $header, "Content-Disposition:", "\n" ) ) . "\n";
			if ($ContentDisposition && stripos ( $ContentDisposition, "filename=" ) !== false)
			{
				$FileName = trim ( trim ( trim ( trim ( trim ( cut_str ( $ContentDisposition, "filename=", "\n" ) ), "=" ), "?" ), ";" ), '"' );
				if (strpos($FileName,"/") !== false) $FileName = basename($FileName);
				$saveToFile = dirname ( $saveToFile ) . PATH_SPLITTER . $FileName;
			}
		}

		if (! empty ( $options['rename_prefix'] )) {
			$File_Name = $options['rename_prefix'] . '_' . basename ( $saveToFile );
			$saveToFile = dirname ( $saveToFile ) . PATH_SPLITTER . $File_Name;
		}
		if (! empty ( $options['rename_suffix'] )) {
			$ext = strrchr ( basename ( $saveToFile ), "." );
			$before_ext = explode ( $ext, basename ( $saveToFile ) );
			$File_Name = $before_ext [0] . '_' . $options['rename_suffix'] . $ext;
			$saveToFile = dirname ( $saveToFile ) . PATH_SPLITTER . $File_Name;
		}
		if($options['rename_underscore']){
			$File_Name = str_replace(array(' ', '%20'), '_', basename($saveToFile));
			$saveToFile = dirname($saveToFile).PATH_SPLITTER.$File_Name;
		}
		$filetype = strrchr ( $saveToFile, "." );
		if (is_array ( $options['forbidden_filetypes'] ) && in_array ( strtolower ( $filetype ), $options['forbidden_filetypes'] )) {
			if ($options['forbidden_filetypes_block']) {
				$lastError = sprintf(lang(82),$filetype);
				return false;
			}
			else {
				$saveToFile = str_replace ( $filetype, $options['rename_these_filetypes_to'], $saveToFile );
			}
		}

		if (@file_exists ( $saveToFile ) && $options['bw_save']) {
			html_error ( lang(99).': '.link_for_file($saveToFile), 0 );
		}
		if (@file_exists ( $saveToFile ) && $Resume ["use"] === TRUE) {
			$fs = @fopen ( $saveToFile, "ab" );
			if (! $fs) {
				$lastError = sprintf(lang(101),basename ( $saveToFile ),dirname ( $saveToFile )).'<br />'.lang(102).'<br /><a href="javascript:location.reload();">'.lang(103).'</a>';
				return FALSE;
			}
		} else {
			if (@file_exists ( $saveToFile )) {
				$saveToFile = dirname ( $saveToFile ) . PATH_SPLITTER . time () . "_" . basename ( $saveToFile );
			}
			$fs = @fopen ( $saveToFile, "wb" );
			if (! $fs) {
				$secondName = dirname ( $saveToFile ) . PATH_SPLITTER . str_replace ( ":", "", str_replace ( "?", "", basename ( $saveToFile ) ) );
				$fs = @fopen ( $secondName, "wb" );
				if (! $fs) {
					$lastError = sprintf(lang(101),basename ( $saveToFile ),dirname ( $saveToFile )).'<br />'.lang(102).'<br /><a href="javascript:location.reload();">'.lang(103).'</a>';
					return FALSE;
				}
			}

		}

		flock ( $fs, LOCK_EX );
		if ($Resume ["use"] === TRUE && stristr ( $header, "Content-Range:" )) {
			list ( $temp, $Resume ["range"] ) = explode ( " ", trim ( cut_str ( $header, "Content-Range:", "\n" ) ) );
			list ( $Resume ["range"], $fileSize ) = explode ( "/", $Resume ["range"] );
			$fileSize = bytesToKbOrMbOrGb ( $fileSize );
		} else {
			$fileSize = bytesToKbOrMbOrGb ( $bytesTotal );
		}
		$chunkSize = GetChunkSize ( $bytesTotal );
		echo(lang(104).' <b>'.basename($saveToFile).'</b>, '.lang(56).' <b>'.$fileSize.'</b>...<br />');

		//$scriptStarted = false;
		require (TEMPLATE_DIR . '/transloadui.php');
		if ($Resume ["use"] === TRUE) {
			$received = bytesToKbOrMbOrGb ( filesize ( $saveToFile ) );
			$percent = round ( $Resume ["from"] / ($bytesTotal + $Resume ["from"]) * 100, 2 );
			echo '<script type="text/javascript">pr('."'" . $percent . "', '" . $received . "', '0');</script>";
			//$scriptStarted = true;
			flush ();
		}
	} else {
		$page = $header;
	}

	do {
		$data = @fread ( $fp, ($saveToFile ? $chunkSize : 16384) );	// 16384 saw this value in Pear HTTP_Request2 package // (fix - szal) using this actually just causes massive cpu usage for large files, too much data is flushed to the browser!)
		if ($data == '')
			break;
		if ($saveToFile) {
			$bytesSaved = fwrite ( $fs, $data );
			if ($bytesSaved > - 1) {
				$bytesReceived += $bytesSaved;
			} else {
				$lastError = sprintf(lang(105),$saveToFile);
				return false;
			}
			if ($bytesReceived >= $bytesTotal) {
				$percent = 100;
			} else {
				$percent = @round ( ($bytesReceived + $Resume ["from"]) / ($bytesTotal + $Resume ["from"]) * 100, 2 );
			}
			if ($bytesReceived > $last + $chunkSize) {
				$received = bytesToKbOrMbOrGb ( $bytesReceived + $Resume ["from"] );
				$time = getmicrotime () - $timeStart;
				$chunkTime = $time - $lastChunkTime;
				$chunkTime = $chunkTime ? $chunkTime : 1;
				$lastChunkTime = $time;
				$speed = @round ( $chunkSize / 1024 / $chunkTime, 2 );
				/*if (!$scriptStarted) {
					echo('<script type="text/javascript">');
					$scriptStarted = true;
				}*/
				echo '<script type="text/javascript">pr('."'" . $percent . "', '" . $received . "', '" . $speed . "');</script>";
				$last = $bytesReceived;
			}
		} else {
			$page .= $data;
		}
	}while( strlen($data)> 0 );
	//echo('</script>');

	if ($saveToFile) {
		flock ( $fs, LOCK_UN );
		fclose ( $fs );
		if ($bytesReceived <= 0) {
			$lastError = lang(106);
			fclose ( $fp );
			return FALSE;
		}
	}
	fclose ( $fp );
	if ($saveToFile) {
		return array ("time" => sec2time ( round ( $time ) ), "speed" => @round ( $bytesTotal / 1024 / (getmicrotime () - $timeStart), 2 ), "received" => true, "size" => $fileSize, "bytesReceived" => ($bytesReceived + $Resume ["from"]), "bytesTotal" => ($bytesTotal + $Resume ["from"]), "file" => $saveToFile );
	} else {
		if ($NoDownload) {
			if (stristr ( $host, "rapidshare" )) {
				is_present ( $page, "You have reached the limit for Free users", lang(107), 0 );
				is_present ( $page, "The download session has expired", lang(108), 0 );
				is_present ( $page, "Wrong access code.", lang(109), 0 );
				is_present ( $page, "You have entered a wrong code too many times", lang(110), 0 );
				print $page;
			} elseif (stristr ( $host, "megaupload" )) {
				is_present ( $page, "Download limit exceeded", lang(111), 0 );
				print $page;
			}
		} else {
			return $page;
		}
	}
}


//simple curl function for https:// logins
function sslcurl($link, $post = 0, $cookie = 0, $refer = 0)
{
	$mm = !empty($post) ? 1 : 0;
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $link);
	curl_setopt($ch, CURLOPT_HEADER, 1);
	curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U;Windows NT 5.1; de;rv:1.8.0.1)\r\nGecko/20060111\r\nFirefox/1.5.0.1');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	if ($mm == 1)
	{
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, formpostdata($post));
	}
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


// This new function requires less line and actually reduces filesize :P
// Besides, using less globals means more variables available for us to use
function formpostdata($post=array()) {
	$postdata = "";
	foreach ( $post as $k => $v ) {
		$postdata .= "$k=$v&";
	}
	// Remove the last '&'
	$postdata = substr ( $postdata, 0, - 1 );
	return $postdata;
}

// function to reconvert array cookie into string
function CookiesToStr($cookie=array()) {
	$cookies = "";
	foreach ($cookie as $k => $v) {
		$cookies .= "$k=$v;";
	}
	// Remove the last ';'
	$cookies = substr($cookies, 0, -1);
	return $cookies;
}

function GetCookies($content) {
	// The U option will make sure that it matches the first character
	// So that it won't grab other information about cookie such as expire, domain and etc
	preg_match_all ( '/Set-Cookie: (.*)(;|\r\n)/U', $content, $temp );
	$cookie = $temp [1];
	$cook = implode ( '; ', $cookie );
	return $cook;
}

/**
 * Function to get cookies & converted into array
 * @param string The content you want to get the cookie from
 * @param bool Options to remove temporary cookie (usually it named as 'deleted')
 * @param string The default name for temporary cookie
 */
function GetCookiesArr($content, $del=true, $dval='deleted') {
	preg_match_all ('/Set-Cookie: (.*)(;|\r\n)/U', $content, $temp);
	$cookie = array();
	foreach ($temp[1] as $v) {
		$v = explode('=', $v, 2);
		$cookie[$v[0]] = $v[1];
		if ($del && $v[1] == $dval) unset($cookie[$v[0]]);
	}
	return $cookie;
}

function GetChunkSize($fsize) {
	if ($fsize <= 1024 * 1024) {
		return 4096;
	}
	if ($fsize <= 1024 * 1024 * 10) {
		return 4096 * 10;
	}
	if ($fsize <= 1024 * 1024 * 40) {
		return 4096 * 30;
	}
	if ($fsize <= 1024 * 1024 * 80) {
		return 4096 * 47;
	}
	if ($fsize <= 1024 * 1024 * 120) {
		return 4096 * 65;
	}
	if ($fsize <= 1024 * 1024 * 150) {
		return 4096 * 70;
	}
	if ($fsize <= 1024 * 1024 * 200) {
		return 4096 * 85;
	}
	if ($fsize <= 1024 * 1024 * 250) {
		return 4096 * 100;
	}
	if ($fsize <= 1024 * 1024 * 300) {
		return 4096 * 115;
	}
	if ($fsize <= 1024 * 1024 * 400) {
		return 4096 * 135;
	}
	if ($fsize <= 1024 * 1024 * 500) {
		return 4096 * 170;
	}
	if ($fsize <= 1024 * 1024 * 1000) {
		return 4096 * 200;
	}
	return 4096 * 210;
}

function upfile($host, $port, $url, $referer, $cookie, $post, $file, $filename, $fieldname, $field2name = "", $proxy = 0, $pauth = 0, $upagent = "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.0.1) Gecko/2008070208 Firefox/3.1") {
    global $nn, $lastError, $sleep_time, $sleep_count;

    $scheme = "http://";
    $bound = "--------" . md5(microtime());
    $saveToFile = 0;

    if ($post) {
        foreach ($post as $key => $value) {
            $postdata .= "--" . $bound . $nn;
            $postdata .= 'Content-Disposition: form-data; name="' . $key . '"' . $nn . $nn;
            $postdata .= $value . $nn;
        }
    }

    $fileSize = getSize($file);

    $fieldname = $fieldname ? $fieldname : file . md5($filename);

    if (!is_readable($file)) {
        $lastError = sprintf(lang(65), $file);
        return FALSE;
    }
    if ($field2name != '') {
        $postdata .= "--" . $bound . $nn;
        $postdata .= 'Content-Disposition: form-data; name="' . $field2name . '"; filename=""' . $nn;
        $postdata .= "Content-Type: application/octet-stream" . $nn . $nn;
    }

    $postdata .= "--" . $bound . $nn;
    $postdata .= 'Content-Disposition: form-data; name="' . $fieldname . '"; filename="' . $filename . '"' . $nn;
    $postdata .= "Content-Type: application/octet-stream" . $nn . $nn;


    if ($cookie) {
        if (is_array($cookie)) {
            $cookies = "Cookie: " . CookiesToStr($cookie) . $nn;
        } else {
            $cookies = "Cookie: " . trim($cookie) . $nn;
        }
    }
    $referer = $referer ? "Referer: " . $referer . $nn : "";

    if ($scheme == "https://") {
        $scheme = "ssl://";
        $port = 443;
    }

    if ($proxy) {
        list ( $proxyHost, $proxyPort ) = explode(":", $proxy);
        $url = $scheme . $host . ":" . $port . $url;
        $host = $host . ":" . $port;
    }

    if ($scheme != "ssl://") {
        $scheme = "";
    }

    $http_auth = ($auth) ? "Authorization: Basic " . $auth . $nn : "";
    $proxyauth = ($pauth) ? "Proxy-Authorization: Basic " . $pauth . $nn : "";

    $zapros = "POST " . str_replace(" ", "%20", $url) . " HTTP/1.0" . $nn . "Host: " . $host . $nn . $cookies . "Content-Type: multipart/form-data; boundary=" . $bound . $nn . "Content-Length: " . (strlen($postdata) + strlen($nn . "--" . $bound . "--" . $nn) + $fileSize) . $nn . "User-Agent: " . $upagent . $nn . "Accept: text/xml,application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5" . $nn . "Accept-Language: en-en,en;q=0.5" . $nn . "Accept-Charset: utf-8,windows-1251;koi8-r;q=0.7,*;q=0.7" . $nn . "Connection: Close" . $nn . $http_auth . $proxyauth . $referer . $nn . $postdata;
    $errno = 0; $errstr = "";
    $posturl = ($proxyHost ? $scheme . $proxyHost : $scheme . $host) . ':' . ($proxyPort ? $proxyPort : $port);
    $fp = @stream_socket_client($posturl, $errno, $errstr, 120, STREAM_CLIENT_CONNECT);
    //$fp = @fsockopen ( $host, $port, $errno, $errstr, 150 );
    //stream_set_timeout ( $fp, 300 );

    if (!$fp) {
        $dis_host = $proxyHost ? $proxyHost : $host;
        $dis_port = $proxyPort ? $proxyPort : $port;
        html_error(sprintf(lang(88), $dis_host, $dis_port));
    }

    if ($errno || $errstr) {
        $lastError = $errstr;
        return false;
    }

    if ($proxy) {
        echo '<p>' . sprintf(lang(89), $proxyHost, $proxyPort) . '<br />';
        echo "UPLOAD: <b>" . $url . "</b>...<br />\n";
    } else {
        echo "<p>";
        printf(lang(90), $host, $port);
        echo "</p>";
    }

    echo(lang(104) . ' <b>' . $filename . '</b>, ' . lang(56) . ' <b>' . bytesToKbOrMb($fileSize) . '</b>...<br />');
    global $id;
    $id = md5(time () * rand(0, 10));
    require (TEMPLATE_DIR . '/uploadui.php');
    flush ();

    $timeStart = getmicrotime ();

    //$chunkSize = 16384;		// Use this value no matter what (using this actually just causes massive cpu usage for large files, too much data is flushed to the browser!)
    $chunkSize = GetChunkSize($fileSize);

    fputs($fp, $zapros);
    fflush($fp);

    $fs = fopen($file, 'r');

    $local_sleep = $sleep_count;
    //echo('<script type="text/javascript">');
    while (!feof($fs)) {
        $data = fread($fs, $chunkSize);
        if ($data === false) {
            fclose($fs);
            fclose($fp);
            html_error(lang(112));
        }

        if (($sleep_count !== false) && ($sleep_time !== false) && is_numeric($sleep_time) && is_numeric($sleep_count) && ($sleep_count > 0) && ($sleep_time > 0)) {
            $local_sleep--;
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

        $time = getmicrotime () - $timeStart;
        $chunkTime = $time - $lastChunkTime;
        $chunkTime = $chunkTime ? $chunkTime : 1;
        $lastChunkTime = $time;
        $speed = round($sendbyte / 1024 / $chunkTime, 2);
        $percent = round($totalsend / $fileSize * 100, 2);
        echo '<script type="text/javascript">pr(' . "'" . $percent . "', '" . bytesToKbOrMb($totalsend) . "', '" . $speed . "');</script>\n";
        flush ();
    }
    //echo('</script>');
    fclose($fs);

    fputs($fp, $nn . "--" . $bound . "--" . $nn);
    fflush($fp);

    while (!feof($fp)) {
        $data = fgets($fp, 16384);
        if ($data === false) {
            break;
        }
        $page .= $data;
    }

    fclose($fp);

    return $page;
}
?>
