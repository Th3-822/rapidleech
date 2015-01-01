<?php
if (!defined('RAPIDLEECH')) {
	require('../deny.php');
	exit();
}

// Allow user-agent to be changed easily
if (!defined('rl_UserAgent')) define('rl_UserAgent', 'Opera/9.80 (Windows NT 6.1) Presto/2.12.388 Version/12.17');

/*
 * Pauses for countdown timer in file hosts
 * @param int The number of seconds to count down
 * @param string The text you want to display when counting down
 * @param string The text you want to display when count down is complete
 * @param bool
 * @return bool
 */
function insert_timer($countd, $caption = '', $timeouttext = '', $hide = false) {
	global $disable_timer;

	if ($disable_timer === true) return true;
	if (!$countd || !is_numeric($countd)) return false;

	$timerid = rand(1000, time());
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
	echo ('$("#timerlabel' . $timerid . '").html("' . sprintf(lang(87), '" + count' . $timerid . ' + "') . '");');
	echo ('count' . $timerid . '--;');
	echo ('setTimeout("timer' . $timerid . '()", 1000);');
	echo ('}');
	echo ('}');
	echo ('timer' . $timerid . '();');
	echo ('</script>');
	flush();
	for ($nnn = 0; $nnn < $countd; $nnn++) {
		sleep(1);
	}
	flush();

	if ($hide === true) {
		echo ('<script type="text/javascript">$("#global' . $timerid . '").css("display","none");</script>');
		flush();
		return true;
	}

	if ($timeouttext) {
		echo ('<script type="text/javascript">$("#global' . $timerid . '").html("' . $timeouttext . '");</script>');
		flush();
		return true;
	}
	return true;
}

/*
 * Counter for those filehosts that displays mirror after countdown
 * @param int The number of seconds to count down
 * @param string Text you want to display above the counter
 * @param string The text you want to display when counting down
 * @param string The text you want to display when count down is complete
 */
function insert_new_timer($countd, $displaytext, $caption = '', $text = '') {
	if (!is_numeric($countd)) html_error(lang(85));
	echo ('<div id="code"></div>');
	echo ('<div align="center">');
	echo ('<div id="dl"><h4>' . lang(86) . '</h4></div></div>');
	echo ('<script type="text/javascript">var c = ' . $countd . ';fc("' . $caption . '","' . $displaytext . '");</script>');
	if (!empty($text)) print $text;
	require (TEMPLATE_DIR . '/footer.php');
}

/*
 * Function to check if geturl function has completed successfully
 */
function is_page($lpage) {
	global $lastError;
	if (!$lpage) html_error(lang(84) . "<br />$lastError", 0);
}

function geturl($host, $port, $url, $referer = 0, $cookie = 0, $post = 0, $saveToFile = 0, $proxy = 0, $pauth = 0, $auth = 0, $scheme = 'http', $resume_from = 0, $XMLRequest = 0) {
	global $nn, $lastError, $Resume, $bytesReceived, $fp, $fs, $force_name, $options;
	$scheme = strtolower($scheme) . '://';

	if (($post !== 0) && ($scheme == 'http://' || $scheme == 'https://')) {
		$method = 'POST';
		$postdata = is_array($post) ? formpostdata($post) : $post;
	} else {
		$method = 'GET';
		$postdata = '';
	}

	if (!empty($cookie)) {
		if (is_array($cookie)) $cookies = (count($cookie) > 0) ? CookiesToStr($cookie) : 0;
		else $cookies = trim($cookie);
	}

	if ($scheme == 'https://') {
		if (!extension_loaded('openssl')) html_error('This server doesn\'t support https connections.');
		$scheme = 'ssl://';
		if ($port == 0 || $port == 80) $port = 443;
	}

	if ($proxy) {
		list($proxyHost, $proxyPort) = explode(':', $proxy, 2);
		$host = $host . ($port != 80 && ($scheme != 'ssl://' || $port != 443) ? ':' . $port : '');
		$url = "$scheme$host$url";
	}

	if ($scheme != 'ssl://') $scheme = '';

	$request = array();
	$request[] = $method . ' ' . str_replace(' ', '%20', $url) . ' HTTP/1.1';
	$request[] = "Host: $host";
	$request[] = 'User-Agent: ' . rl_UserAgent;
	$request[] = 'Accept: */*';
	$request[] = 'Accept-Language: en-US,en;q=0.9';
	$request[] = 'Accept-Charset: utf-8,windows-1251;q=0.7,*;q=0.7';
	if (!empty($referer)) $request[] = "Referer: $referer";
	if (!empty($cookies)) $request[] = "Cookie: $cookies";
	$request[] = 'Pragma: no-cache';
	$request[] = 'Cache-Control: no-cache';
//	if ($Resume['use'] === TRUE) $request[] = 'Range: bytes=' . $Resume['from'] . '-';
	if (!empty($auth)) $request[] = "Authorization: Basic $auth";
	if (!empty($pauth)) $request[] = "Proxy-Authorization: Basic $pauth";
	if ($method == 'POST') {
		if (!empty($referer) && stripos($referer, "\nContent-Type: ") === false) $request[] = 'Content-Type: application/x-www-form-urlencoded';
		$request[] = 'Content-Length: ' . strlen($postdata);
	}
	if ($XMLRequest) $request[] = 'X-Requested-With: XMLHttpRequest';
	$request[] = 'Connection: Close';

	$request = implode($nn, $request). $nn . $nn . $postdata;

	$errno = 0;
	$errstr = '';
	$hosts = (!empty($proxyHost) ? $scheme . $proxyHost : $scheme . $host) . ':' . (!empty($proxyPort) ? $proxyPort : $port);
	$fp = @stream_socket_client($hosts, $errno, $errstr, 120, STREAM_CLIENT_CONNECT);
	//$fp = @fsockopen($proxyHost ? $scheme.$proxyHost : $scheme.$host, $proxyPort ? $proxyPort : $port, $errno, $errstr, 15);


	if (!$fp) {
		if (!function_exists('stream_socket_client')) html_error('[ERROR] stream_socket_client() is disabled.');
		$dis_host = !empty($proxyHost) ? $proxyHost : $host;
		$dis_port = !empty($proxyPort) ? $proxyPort : $port;
		html_error(sprintf(lang(88), $dis_host, $dis_port));
	}

	if ($errno || $errstr) {
		$lastError = $errstr;
		return false;
	}

	if ($saveToFile) {
		if ($proxy) {
			echo '<p>'.sprintf(lang(89), $proxyHost, $proxyPort).'<br />';
			echo 'GET: <b>' . $url . "</b>...<br />\n";
		} else echo '<p>' . sprintf(lang(90), $host, $port) . '</p>';
	}

	#########################################################################
	fwrite($fp, $request);
	fflush($fp);
	$timeStart = getmicrotime();

	// Rewrote the get header function according to the proxy script
	// Also made sure it goes faster and I think 8192 is the best value for retrieving headers
	// Oops.. The previous function hooked up everything and now I'm returning it back to normal

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

	#########################################################################

	if ($saveToFile) {
		if (!isset($_GET['dis_plug']) || $_GET['dis_plug'] != 'on') {
			$cbhost = (strpos($host, ':') !== false) ? substr($host, 0, strpos($host, ':')) : $host; // Remove the port that may be added when it's using proxy
			$chkhost = preg_match('/^\d{1,3}.\d{1,3}.\d{1,3}.\d{1,3}$/', $cbhost) ? false : true;
			if (!empty($referer)) {
				$cbrefhost = (stripos($referer, 'www.') === 0) ? substr($referer, 4) : $referer;
				$cbrefhost = parse_url($cbrefhost, PHP_URL_HOST);
				$chkref = (empty($cbrefhost) || preg_match('/^\d{1,3}.\d{1,3}.\d{1,3}.\d{1,3}$/', $cbrefhost)) ? false : (($chkhost && strtolower($cbhost) == strtolower($cbrefhost)) ? false : true);
			} else $chkref = false;
			$found = false;
			if ($chkhost || $chkref) foreach ($GLOBALS['host'] as $site => $file) {
				if ($chkhost && host_matches($site, $cbhost)) {
					$found = true;
					break;
				} elseif ($chkref && host_matches($site, $cbrefhost)) {
					$found = true;
					break;
				}
			}
				if ($found) {
					require_once(HOST_DIR . 'DownloadClass.php');
					require_once(HOST_DIR . "download/$file");
					$class = substr($file, 0, -4);
					$firstchar = substr($file, 0, 1);
					if ($firstchar > 0) $class = "d$class";
				if (class_exists($class) && method_exists($class, 'CheckBack')) { // is_callable(array($class , 'CheckBack'))
					$hostClass = new $class(false);
					$hostClass->CheckBack($header);
				}
			}
			unset($cbhost, $cbrefhost, $chkhost, $chkref, $found);
		}
		if (preg_match('/^HTTP\/1\.[0|1] (\d+) .*/', $header, $responsecode) && ($responsecode[1] == 404 || $responsecode[1] == 403)) {
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
		//$bytesTotal = intval ( trim ( cut_str ( $header, "Content-Length:", "\n" ) ) );
		$bytesTotal = trim(cut_str($header, "\nContent-Length: ", "\n"));

		global $options;
		if ($options['file_size_limit'] > 0 && ($bytesTotal > ($options['file_size_limit'] * 1024 * 1024))) {
			$lastError = lang(336) . bytesToKbOrMbOrGb ($options['file_size_limit'] * 1024 * 1024) . '.';
			return false;
		}
		if (stripos($header, "\nLocation: ") !== false && preg_match('/\nLocation: ([^\r\n]+)/i', $header, $redir)) {
			$redirect = trim($redir[1]);
			$lastError = sprintf(lang(95), $redirect);
			return FALSE;
		}
		if (in_array(cut_str($header, "\nWWW-Authenticate: ", ' '), array('Basic', 'Digest'))) {
			$lastError = lang(96);
			return FALSE;
		}
		//$ContentType = trim (cut_str($header, "\nContent-Type:", "\n")); // Unused
		if ($Resume['use'] === TRUE && stripos($header, "\nContent-Range: ") === false) {
			$lastError = (stripos($header, '503 Limit Exceeded') !== false) ? lang(97) : lang(98);
			return FALSE;
		}

		if ($force_name) $FileName = $force_name;
		else {
			$ContentDisposition = cut_str($header, "\nContent-Disposition: ", "\n");
			if (!empty($ContentDisposition) && stripos($ContentDisposition, 'filename') !== false) {
				if (preg_match("@filename\*=UTF-8''((?:[\w\-\.]|%[0-F]{2})+)@i", $ContentDisposition, $fn)) $FileName = rawurldecode($fn[1]);
				elseif (preg_match('@filename=\"?([^\r\n\"\;]+)\"?@i', $ContentDisposition, $fn)) $FileName = $fn[1];
				else $FileName = $saveToFile;
			} else $FileName = $saveToFile;
		}
		$FileName = str_replace(array_merge(range(chr(0), chr(31)), str_split("<>:\"/|?*\x5C\x7F")), '', basename(trim($FileName)));

		if (!empty($options['rename_prefix'])) $FileName = $options['rename_prefix'] . '_' . $FileName;
		if (!empty($options['rename_suffix'])) {
			$ext = strrchr($FileName, '.');
			$before_ext = explode($ext, $FileName);
			$FileName = $before_ext[0] . '_' . $options['rename_suffix'] . $ext;
		}
		if ($options['rename_underscore']) $FileName = str_replace(array(' ', '%20'), '_', $FileName);

		$saveToFile = dirname($saveToFile) . PATH_SPLITTER . $FileName;

		$filetype = strrchr($saveToFile, '.');
		if (is_array($options['forbidden_filetypes']) && in_array(strtolower($filetype), $options['forbidden_filetypes'])) {
			if ($options['forbidden_filetypes_block']) {
				$lastError = sprintf(lang(82), $filetype);
				return false;
			} else $saveToFile = str_replace($filetype, $options['rename_these_filetypes_to'], $saveToFile);
		}

		if (@file_exists($saveToFile) && $options['bw_save']) {
			// Skip in audl.
			if (isset($_GET['audl'])) echo '<script type="text/javascript">parent.nextlink();</script>';
			html_error(lang(99) . ': ' . link_for_file($saveToFile), 0);
		}
		if (@file_exists($saveToFile) && $Resume['use'] === TRUE) {
			$fs = @fopen($saveToFile, 'ab');
			if (!$fs) {
				$lastError = sprintf(lang(101), basename($saveToFile), dirname($saveToFile)) . '<br />' . lang(102) . '<br /><a href="javascript:location.reload();">' . lang(103) . '</a>';
				return FALSE;
			}
		} else {
			if (@file_exists($saveToFile)) $saveToFile = dirname($saveToFile) . PATH_SPLITTER . time() . '_' . basename($saveToFile);
			$fs = @fopen($saveToFile, 'wb');
			if (!$fs) {
				$secondName = dirname($saveToFile) . PATH_SPLITTER . str_replace(':', '', str_replace('?', '', basename($saveToFile)));
				$fs = @fopen($secondName, 'wb');
				if (!$fs) {
					$lastError = sprintf(lang(101), basename($saveToFile), dirname($saveToFile)) . '<br />' . lang(102) . '<br /><a href="javascript:location.reload();">' . lang(103) . '</a>';
					return FALSE;
				}
			}
		}

		flock($fs, LOCK_EX);
		if ($Resume['use'] === TRUE && stripos($header, "\nContent-Range: ") !== false) {
			list($temp, $Resume['range']) = explode(' ', trim(cut_str($header, "\nContent-Range: ", "\n")));
			list($Resume['range'], $fileSize) = explode('/', $Resume['range']);
			$fileSize = bytesToKbOrMbOrGb($fileSize);
		} else $fileSize = bytesToKbOrMbOrGb($bytesTotal);
		$chunkSize = GetChunkSize($bytesTotal);
		echo(lang(104) . ' <b>' . basename($saveToFile) . '</b>, ' . lang(56) . " <b>$fileSize</b>...<br />");

		//$scriptStarted = false;
		require_once(TEMPLATE_DIR . '/transloadui.php');
		if ($Resume['use'] === TRUE) {
			$received = bytesToKbOrMbOrGb(filesize($saveToFile));
			$percent = round($Resume['from'] / ($bytesTotal + $Resume['from']) * 100, 2);
			echo "<script type='text/javascript'>pr('$percent', '$received', '0');</script>";
			//$scriptStarted = true;
			flush();
		}
	} else $page = '';

	$time = $last = $lastChunkTime = 0;
	do {
		$data = @fread($fp, ($saveToFile ? $chunkSize : 16384)); // 16384 saw this value in Pear HTTP_Request2 package // (fix - szal) using this actually just causes massive cpu usage for large files, too much data is flushed to the browser!)
		if ($data == '') break;
		if ($saveToFile) {
			$bytesSaved = fwrite($fs, $data);
			if ($bytesSaved !== false && strlen($data) == $bytesSaved) { //if ($bytesSaved > - 1) {
				$bytesReceived += $bytesSaved;
			} else {
				$lastError = sprintf(lang(105), basename($saveToFile));
				// unlink($saveToFile);
				return false;
			}
			if ($bytesReceived >= $bytesTotal) $percent = 100;
			else $percent = @round(($bytesReceived + $Resume['from']) / ($bytesTotal + $Resume['from']) * 100, 2);
			if ($bytesReceived > $last + $chunkSize) {
				$received = bytesToKbOrMbOrGb($bytesReceived + $Resume['from']);
				$time = getmicrotime() - $timeStart;
				$chunkTime = $time - $lastChunkTime;
				$chunkTime = ($chunkTime > 0) ? $chunkTime : 1;
				$lastChunkTime = $time;
				$speed = @round(($bytesReceived - $last) /*$chunkSize*/ / 1024 / $chunkTime, 2);
				echo "<script type='text/javascript'>pr('$percent', '$received', '$speed');</script>";
				$last = $bytesReceived;
			}
			if (!empty($bytesTotal) && ($bytesReceived + $chunkSize) > $bytesTotal) $chunkSize = $bytesTotal - $bytesReceived;
		} else $page .= $data;
	} while (!feof($fp) && strlen($data) > 0);

	if ($saveToFile) {
		flock($fs, LOCK_UN);
		fclose($fs);
		if ($bytesReceived <= 0) {
			$lastError = lang(106);
			stream_socket_shutdown($fp, STREAM_SHUT_RDWR);
			fclose($fp);
			return FALSE;
		}
	}
	stream_socket_shutdown($fp, STREAM_SHUT_RDWR);
	fclose($fp);
	if ($saveToFile) {
		return array('time' => sec2time(round($time)), 'speed' => @round($bytesTotal / 1024 / (getmicrotime() - $timeStart), 2), 'received' => true, 'size' => $fileSize, 'bytesReceived' => ($bytesReceived + $Resume['from']), 'bytesTotal' => ($bytesTotal + $Resume ['from']), 'file' => $saveToFile);
	} else {
		if (stripos($header, "\nTransfer-Encoding: chunked") !== false && function_exists('http_chunked_decode')) {
			$dechunked = http_chunked_decode($page);
			if ($dechunked !== false) $page = $dechunked;
			unset($dechunked);
		}
		$page = $header.$page;
		return $page;
	}
}

function cURL($link, $cookie = 0, $post = 0, $referer = 0, $auth = 0, $opts = 0) {
	global $pauth;
	static $ch;
	if (empty($link) || !is_string($link)) html_error(lang(24));
	if (!extension_loaded('curl') || !function_exists('curl_init') || !function_exists('curl_exec')) html_error('cURL isn\'t enabled or cURL\'s functions are disabled');
	$arr = explode("\r\n", $referer);
	$header = array();
	if (count($arr) > 1) {
		$referer = $arr[0];
		unset($arr[0]);
		$header = array_filter(array_map('trim', $arr));
	}
	$link = str_replace(array(' ', "\r", "\n"), array('%20'), $link);
	$opt = array(CURLOPT_HEADER => 1, CURLOPT_SSL_VERIFYPEER => 0,
		CURLOPT_SSL_VERIFYHOST => 0, CURLOPT_RETURNTRANSFER => 1,
		CURLOPT_FOLLOWLOCATION => 0, CURLOPT_FAILONERROR => 0,
		CURLOPT_FORBID_REUSE => 0, CURLOPT_FRESH_CONNECT => 0,
		CURLINFO_HEADER_OUT => 1, CURLOPT_URL => $link,
		CURLOPT_USERAGENT => rl_UserAgent);

	$opt[CURLOPT_REFERER] = !empty($referer) ? $referer : false;
	$opt[CURLOPT_COOKIE] = !empty($cookie) ? (is_array($cookie) ? CookiesToStr($cookie) : trim($cookie)) : false;

	if (isset($_GET['useproxy']) && !empty($_GET['proxy'])) {
		$opt[CURLOPT_HTTPPROXYTUNNEL] = strtolower(parse_url($link, PHP_URL_SCHEME) == 'https') ? true : false; // cURL https proxy support... Experimental.
		// $opt[CURLOPT_HTTPPROXYTUNNEL] = false; // Uncomment this line for disable https proxy over curl.
		$opt[CURLOPT_PROXY] = $_GET['proxy'];
		$opt[CURLOPT_PROXYUSERPWD] = (!empty($pauth) ? base64_decode($pauth) : false);
	} else $opt[CURLOPT_PROXY] = false;

	// Send more headers...
	$headers = array('Accept-Language: en-US,en;q=0.9', 'Accept-Charset: utf-8,windows-1251;q=0.7,*;q=0.7', 'Pragma: no-cache', 'Cache-Control: no-cache', 'Connection: Keep-Alive');
	if (empty($opt[CURLOPT_REFERER])) $headers[] = 'Referer:';
	if (empty($opt[CURLOPT_COOKIE])) $headers[] = 'Cookie:';
	if (!empty($opt[CURLOPT_PROXY]) && empty($opt[CURLOPT_PROXYUSERPWD])) $headers[] = 'Proxy-Authorization:';
	if (count($header) > 0) $headers = array_merge($headers, $header);
	$opt[CURLOPT_HTTPHEADER] = $headers;

	if ($post != '0') {
		$opt[CURLOPT_POST] = 1;
		$opt[CURLOPT_POSTFIELDS] = is_array($post) ? formpostdata($post) : $post;
	} else $opt[CURLOPT_HTTPGET] = 1;

	if ($auth) {
		$opt[CURLOPT_HTTPAUTH] = CURLAUTH_BASIC;
		$opt[CURLOPT_USERPWD] = base64_decode($auth);
	} else $opt[CURLOPT_HTTPAUTH] = false;

	$opt[CURLOPT_CONNECTTIMEOUT] = $opt[CURLOPT_TIMEOUT] = 120;
	if (is_array($opts) && count($opts) > 0) foreach ($opts as $O => $V) $opt[$O] = $V;

	if (!isset($ch)) $ch = curl_init();

	foreach ($opt as $O => $V) curl_setopt($ch, $O, $V); // Using this instead of 'curl_setopt_array'
	$page = curl_exec($ch);
	$info = curl_getinfo($ch);
	$errz = curl_errno($ch);
	$errz2 = curl_error($ch);
	// curl_close($ch);

	if (substr($page, 9, 3) == '100' || !empty($opt[CURLOPT_HTTPPROXYTUNNEL])) $page = preg_replace("@^HTTP/1\.[01] \d{3}(?:\s[^\r\n]+)?\r\n\r\n(HTTP/1\.[01] \d+ [^\r\n]+)@i", "$1", $page, 1); // The "100 Continue" or "200 Connection established" can break some functions in plugins, lets remove it...
	if ($errz != 0) html_error("[cURL:$errz] $errz2");

	return $page;
}

// This new function requires less line and actually reduces filesize :P
// Besides, using less globals means more variables available for us to use
function formpostdata($post=array()) {
	$postdata = '';
	foreach ($post as $k => $v) $postdata .= "$k=$v&";

	// Remove the last '&'
	$postdata = substr($postdata, 0, -1);
	return $postdata;
}

// function to convert an array of cookies into a string
function CookiesToStr($cookie=array()) {
	if (count($cookie) == 0) return '';
	$cookies = '';
	foreach ($cookie as $k => $v) $cookies .= "$k=$v; ";

	// Remove the last '; '
	$cookies = substr($cookies, 0, -2);
	return $cookies;
}

function GetCookies($content) {
	if (($hpos = strpos($content, "\r\n\r\n")) > 0) $content = substr($content, 0, $hpos); // We need only the headers
	if (empty($content) || stripos($content, "\nSet-Cookie: ") === false) return '';
	// The U option will make sure that it matches the first character
	// So that it won't grab other information about cookie such as expire, domain and etc
	preg_match_all('/\nSet-Cookie: (.*)(;|\r\n)/U', $content, $temp);
	$cookie = $temp[1];
	$cookie = implode('; ', $cookie);
	return $cookie;
}

/**
 * Function to get cookies & converted into array
 * @param string The content you want to get the cookie from
 * @param array Array of cookies to be updated [optional]
 * @param bool Options to remove "deleted" or expired cookies (usually it named as 'deleted') [optional]
 * @param mixed The default name for temporary cookie, values are accepted in an array [optional]
 */
function GetCookiesArr($content, $cookie=array(), $del=true, $dval=array('','deleted')) {
	if (!is_array($cookie)) $cookie = array();
	if (($hpos = strpos($content, "\r\n\r\n")) > 0) $content = substr($content, 0, $hpos); // We need only the headers
	if (empty($content) || stripos($content, "\nSet-Cookie: ") === false || !preg_match_all ('/\nSet-Cookie: ([^\r\n]+)/', $content, $temp)) return $cookie;
	foreach ($temp[1] as $v) {
		if (strpos($v, ';') !== false) list($v, $p) = explode(';', $v, 2);
		else $p = false;
		$v = explode('=', $v, 2);
		$cookie[$v[0]] = $v[1];
		if ($del) {
			if (!is_array($dval)) $dval = array($dval);
			if (in_array($v[1], $dval)) unset($cookie[$v[0]]);
			elseif (!empty($p)) {
				if (stripos($p, 'Max-Age=') !== false && preg_match('/[ \;]?Max-Age=(-?\d+)/i', $p, $P) && (int)$P[1] < 1) unset($cookie[$v[0]]);
				elseif (stripos($p, 'expires=') !== false && preg_match('/[ \;]?expires=([a-zA-Z]{3}, \d{1,2} [a-zA-Z]{3} \d{4} \d{1,2}:\d{1,2}:\d{1,2} GMT)/i', $p, $P) && ($P = strtotime($P[1])) !== false && $P <= time()) unset($cookie[$v[0]]);
			}
		}
	}
	return $cookie;
}

/**
 * Function to convert a string of cookies into an array
 * @param string The existing string cookie value
 * @param array The existing array cookie value that we want to merged/updated [optional]
 * @param bool Options to remove temporary cookie (usually it named as 'deleted') [optional]
 * @param mixed The default name for temporary cookie, values are accepted in an array [optional]
 */
function StrToCookies($cookies, $cookie=array(), $del=true, $dval=array('','deleted')) {
	if (!is_array($cookie)) $cookie = array();
	$cookies = trim($cookies);
	if (empty($cookies)) return $cookie;
	foreach (array_filter(array_map('trim', explode(';', $cookies))) as $v) {
		$v = array_map('trim', explode('=', $v, 2));
		$cookie[$v[0]] = $v[1];
		if ($del) {
			if (!is_array($dval)) $dval = array($dval);
			if (in_array($v[1], $dval)) unset($cookie[$v[0]]);
		}
	}
	return $cookie;
}

function GetChunkSize($fsize) {
	if ($fsize <= 0) return 4096;
	if ($fsize < 4096) return (int)$fsize;
	if ($fsize <= 1024 * 1024) return 4096;
	if ($fsize <= 1024 * 1024 * 10) return 4096 * 10;
	if ($fsize <= 1024 * 1024 * 40) return 4096 * 30;
	if ($fsize <= 1024 * 1024 * 80) return 4096 * 47;
	if ($fsize <= 1024 * 1024 * 120) return 4096 * 65;
	if ($fsize <= 1024 * 1024 * 150) return 4096 * 70;
	if ($fsize <= 1024 * 1024 * 200) return 4096 * 85;
	if ($fsize <= 1024 * 1024 * 250) return 4096 * 100;
	if ($fsize <= 1024 * 1024 * 300) return 4096 * 115;
	if ($fsize <= 1024 * 1024 * 400) return 4096 * 135;
	if ($fsize <= 1024 * 1024 * 500) return 4096 * 170;
	if ($fsize <= 1024 * 1024 * 1000) return 4096 * 200;
	return 4096 * 210;
}

function upfile($host, $port, $url, $referer, $cookie, $post, $file, $filename, $fieldname, $field2name = '', $proxy = 0, $pauth = 0, $upagent = 0, $scheme = 'http') {
	global $nn, $lastError, $fp, $fs;

	if (empty($upagent)) $upagent = rl_UserAgent;
	$scheme = strtolower("$scheme://");

	$bound = '--------' . md5(microtime());
	$saveToFile = 0;

	$postdata = '';
	if (!empty($post) && is_array($post)) foreach ($post as $key => $value) {
		$postdata .= '--' . $bound . $nn;
		$postdata .= "Content-Disposition: form-data; name=\"$key\"$nn$nn";
		$postdata .= $value . $nn;
	}

	$fieldname = $fieldname ? $fieldname : 'file' . md5($filename);

	if (!is_readable($file)) {
		$lastError = sprintf(lang(65), $file);
		return FALSE;
	}

	$fileSize = getSize($file);

	if (!empty($field2name)) {
		$postdata .= '--' . $bound . $nn;
		$postdata .= "Content-Disposition: form-data; name=\"$field2name\"; filename=\"\"$nn";
		$postdata .= "Content-Type: application/octet-stream$nn$nn";
	}

	$postdata .= '--' . $bound . $nn;
	$postdata .= "Content-Disposition: form-data; name=\"$fieldname\"; filename=\"$filename\"$nn";
	$postdata .= "Content-Type: application/octet-stream$nn$nn";

	if (!empty($cookie)) {
		if (is_array($cookie)) $cookies = (count($cookie) > 0) ? CookiesToStr($cookie) : 0;
		else $cookies = trim($cookie);
	}

	if ($scheme == 'https://') {
		if (!extension_loaded('openssl')) html_error('This server doesn\'t support https connections.');
		$scheme = 'ssl://';
		if ($port == 0 || $port == 80) $port = 443;
	}

	if (!empty($referer) && ($pos = strpos("\r\n", $referer)) !== 0) {
		$origin = parse_url($pos ? substr($referer, 0, $pos) : $referer);
		if (empty($origin['port'])) $origin['port'] = defport($origin);
		$origin = strtolower($origin['scheme']) . '://' . strtolower($origin['host']) . ($origin['port'] != 80 && ($scheme != 'ssl://' || $origin['port'] != 443) ? ':' . $origin['port'] : '');
	} else $origin = ($scheme == 'ssl://' ? 'https://' : $scheme) . $host . ($port != 80 && ($scheme != 'ssl://' || $port != 443) ? ':' . $port : '');

	if ($proxy) {
		list($proxyHost, $proxyPort) = explode(':', $proxy, 2);
		$host = $host . ($port != 80 && ($scheme != 'ssl://' || $port != 443) ? ':' . $port : '');
		$url = "$scheme$host$url";
	}

	if ($scheme != 'ssl://') $scheme = '';

	$request = array();
	$request[] = 'POST ' . str_replace(' ', '%20', $url) . ' HTTP/1.0';
	$request[] = "Host: $host";
	$request[] = "User-Agent: $upagent";
	$request[] = 'Accept: text/html, application/xml;q=0.9, application/xhtml+xml, image/png, image/webp, image/jpeg, image/gif, image/x-xbitmap, */*;q=0.1';
	$request[] = 'Accept-Language: en-US,en;q=0.9';
	$request[] = 'Accept-Charset: utf-8,windows-1251;q=0.7,*;q=0.7';
	if (!empty($referer)) $request[] = "Referer: $referer";
	if (!empty($cookies)) $request[] = "Cookie: $cookies";
	if (!empty($pauth)) $request[] = "Proxy-Authorization: Basic $pauth";
	$request[] = "Origin: $origin";
	$request[] = "Content-Type: multipart/form-data; boundary=$bound";
	$request[] = 'Content-Length: ' . (strlen($postdata) + strlen($nn . "--" . $bound . "--" . $nn) + $fileSize);
	$request[] = 'Connection: Close';

	$request = implode($nn, $request). $nn . $nn . $postdata;

	$errno = 0;
	$errstr = '';
	$hosts = (!empty($proxyHost) ? $scheme . $proxyHost : $scheme . $host) . ':' . (!empty($proxyPort) ? $proxyPort : $port);
	$fp = @stream_socket_client($hosts, $errno, $errstr, 120, STREAM_CLIENT_CONNECT);

	if (!$fp) {
		if (!function_exists('stream_socket_client')) html_error('[ERROR] stream_socket_client() is disabled.');
		$dis_host = !empty($proxyHost) ? $proxyHost : $host;
		$dis_port = !empty($proxyPort) ? $proxyPort : $port;
		html_error(sprintf(lang(88), $dis_host, $dis_port));
	}

	if ($errno || $errstr) {
		$lastError = $errstr;
		return false;
	}

	if ($proxy) echo '<p>' . sprintf(lang(89), $proxyHost, $proxyPort) . '<br />UPLOAD: <b>' . htmlspecialchars($url) . "</b>...<br />\n";
	else echo '<p>'.sprintf(lang(90), $host, $port).'</p>';

	echo(lang(104) . ' <b>' . htmlspecialchars($filename) . '</b>, ' . lang(56) . ' <b>' . bytesToKbOrMbOrGb($fileSize) . '</b>...<br />');
	$GLOBALS['id'] = md5(time() * rand(0, 10));
	require (TEMPLATE_DIR . '/uploadui.php');
	flush();

	$timeStart = getmicrotime();

	$chunkSize = GetChunkSize($fileSize);

	fwrite($fp, $request);
	fflush($fp);

	$fs = fopen($file, 'r');

	$totalsend = $time = $lastChunkTime = 0;
	while (!feof($fs) && !$errno && !$errstr) {
		$data = fread($fs, $chunkSize);
		if ($data === false) {
			fclose($fs);
			fclose($fp);
			html_error(lang(112));
		}

		$sendbyte = @fwrite($fp, $data);
		fflush($fp);

		if ($sendbyte === false || strlen($data) > $sendbyte) {
			fclose($fs);
			fclose($fp);
			html_error(lang(113));
		}

		$totalsend += $sendbyte;

		$time = getmicrotime() - $timeStart;
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

	$page = '';
	while (!feof($fp)) {
		$data = fread($fp, 16384);
		if ($data === false) break;
		$page .= $data;
	}

	fclose($fp);

	return $page;
}

function putfile($host, $port, $url, $referer, $cookie, $file, $filename, $proxy = 0, $pauth = 0, $upagent = 0, $scheme = 'http') {
	global $nn, $lastError, $fp, $fs;

	if (empty($upagent)) $upagent = rl_UserAgent;
	$scheme = strtolower("$scheme://");

	if (!is_readable($file)) {
		$lastError = sprintf(lang(65), $file);
		return FALSE;
	}

	$fileSize = getSize($file);
	if ($cambialo) $fileSize++;

	if (!empty($cookie)) {
		if (is_array($cookie)) $cookies = (count($cookie) > 0) ? CookiesToStr($cookie) : 0;
		else $cookies = trim($cookie);
	}

	if ($scheme == 'https://') {
		if (!extension_loaded('openssl')) html_error('This server doesn\'t support https connections.');
		$scheme = 'ssl://';
		if ($port == 0 || $port == 80) $port = 443;
	}

	if (!empty($referer) && ($pos = strpos("\r\n", $referer)) !== 0) {
		$origin = parse_url($pos ? substr($referer, 0, $pos) : $referer);
		if (empty($origin['port'])) $origin['port'] = defport($origin);
		$origin = strtolower($origin['scheme']) . '://' . strtolower($origin['host']) . ($origin['port'] != 80 && ($scheme != 'ssl://' || $origin['port'] != 443) ? ':' . $origin['port'] : '');
	} else $origin = ($scheme == 'ssl://' ? 'https://' : $scheme) . $host . ($port != 80 && ($scheme != 'ssl://' || $port != 443) ? ':' . $port : '');

	if ($proxy) {
		list($proxyHost, $proxyPort) = explode(':', $proxy, 2);
		$host = $host . ($port != 80 && ($scheme != 'ssl://' || $port != 443) ? ':' . $port : '');
		$url = "$scheme$host$url";
	}

	if ($scheme != 'ssl://') $scheme = '';

	$request = array();
	$request[] = 'PUT ' . str_replace(' ', '%20', $url) . ' HTTP/1.1';
	$request[] = "Host: $host";
	$request[] = "User-Agent: $upagent";
	$request[] = 'Accept: text/html, application/xml;q=0.9, application/xhtml+xml, image/png, image/webp, image/jpeg, image/gif, image/x-xbitmap, */*;q=0.1';
	$request[] = 'Accept-Language: en-US,en;q=0.9';
	$request[] = 'Accept-Charset: utf-8,windows-1251;q=0.7,*;q=0.7';
	if (!empty($referer)) $request[] = "Referer: $referer";
	if (!empty($cookies)) $request[] = "Cookie: $cookies";
	if (!empty($pauth)) $request[] = "Proxy-Authorization: Basic $pauth";
	$request[] = "X-File-Name: $filename";
	$request[] = "X-File-Size: $fileSize";
	$request[] = "Origin: $origin";
	$request[] = 'Content-Disposition: attachment';
	$request[] = 'Content-Type: multipart/form-data';
	$request[] = "Content-Length: $fileSize";
	$request[] = 'Connection: Close';

	$request = implode($nn, $request). $nn . $nn;

	$errno = 0;
	$errstr = '';
	$hosts = (!empty($proxyHost) ? $scheme . $proxyHost : $scheme . $host) . ':' . (!empty($proxyPort) ? $proxyPort : $port);
	$fp = @stream_socket_client($hosts, $errno, $errstr, 120, STREAM_CLIENT_CONNECT);

	if (!$fp) {
		if (!function_exists('stream_socket_client')) html_error('[ERROR] stream_socket_client() is disabled.');
		$dis_host = !empty($proxyHost) ? $proxyHost : $host;
		$dis_port = !empty($proxyPort) ? $proxyPort : $port;
		html_error(sprintf(lang(88), $dis_host, $dis_port));
	}

	if ($errno || $errstr) {
		$lastError = $errstr;
		return false;
	}

	if ($proxy) echo '<p>' . sprintf(lang(89), $proxyHost, $proxyPort) . '<br />PUT: <b>' . htmlspecialchars($url) . "</b>...<br />\n";
	else echo '<p>'.sprintf(lang(90), $host, $port).'</p>';

	echo(lang(104) . ' <b>' . htmlspecialchars($filename) . '</b>, ' . lang(56) . ' <b>' . bytesToKbOrMbOrGb($fileSize) . '</b>...<br />');
	$GLOBALS['id'] = md5(time() * rand(0, 10));
	require (TEMPLATE_DIR . '/uploadui.php');
	flush();

	$timeStart = getmicrotime();

	$chunkSize = GetChunkSize($fileSize);

	fwrite($fp, $request);
	fflush($fp);

	$fs = fopen($file, 'r');

	$totalsend = $time = $lastChunkTime = 0;
	while (!feof($fs) && !$errno && !$errstr) {
		$data = fread($fs, $chunkSize);
		if ($data === false) {
			fclose($fs);
			fclose($fp);
			html_error(lang(112));
		}

		$sendbyte = @fwrite($fp, $data);
		fflush($fp);

		if ($sendbyte === false || strlen($data) > $sendbyte) {
			fclose($fs);
			fclose($fp);
			html_error(lang(113));
		}

		$totalsend += $sendbyte;

		$time = getmicrotime() - $timeStart;
		$chunkTime = $time - $lastChunkTime;
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

	$page = '';
	do {
		$data = @fread($fp, 16384);
		if ($data == '') break;
		$page .= $data;
	} while (!feof($fp) && strlen($data) > 0);

	stream_socket_shutdown($fp, STREAM_SHUT_RDWR);
	fclose($fp);

	if (stripos($header, "\nTransfer-Encoding: chunked") !== false && function_exists('http_chunked_decode')) {
		$dechunked = http_chunked_decode($page);
		if ($dechunked !== false) $page = $dechunked;
		unset($dechunked);
	}
	$page = $header.$page;
	return $page;
}
?>