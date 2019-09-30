<?php
if (!defined('RAPIDLEECH')) {
	require('../deny.php');
	exit();
}

// Allow user-agent to be changed easily
if (!defined('rl_UserAgent')) define('rl_UserAgent', 'Mozilla/5.0 (Windows NT 6.1; rv:52.0) Gecko/20100101 Firefox/52.0');

/*
 * Pauses for countdown timer in file hosts
 * @param int The number of seconds to count down
 * @param string The text you want to display when counting down
 * @param string The text you want to display when count down is complete
 * @param bool
 * @return bool
 */
function insert_timer($countd, $caption = '', $timeouttext = '', $hide = false) {
	if (empty($countd) || !is_numeric($countd) || $countd < 0) return false;
	$countd = ceil($countd);

	$timerid = jstime();
	echo "\n<div id='timer_$timerid' align='center'>\n\t<br /><span class='caption'>$caption</span>&nbsp;&nbsp;\n\t<span id='timerlabel_$timerid' class='caption'></span>\n</div>\n<script type='text/javascript'>/* <![CDATA[ */\n\tvar count_$timerid = $countd;\n\tfunction timer_$timerid() {\n\t\tif (count_$timerid > 0) {\n\t\t\t$('#timerlabel_$timerid').html('". sprintf(lang(87), "' + count_$timerid + '") . "');\n\t\t\tcount_$timerid--;\n\t\t\tsetTimeout('timer_$timerid()', 1000);\n\t\t}";
	if ($hide) echo "else $('#timer_$timerid').css('display', 'none');";
	elseif (!empty($timeouttext)) echo "else $('#timer_$timerid').html('" . addslashes($timeouttext) . "');";
	echo "\n\t} timer_$timerid();\n/* ]]> */</script>";
	flush();
	sleep($countd);
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
	if (!is_numeric($countd)) return html_error(lang(85));
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
	if (!$lpage) return html_error(lang(84) . "<br />$lastError");
}

function readCustomHeaders(&$referer) {
	$headers = array();
	if (!empty($referer)) {
		$tmp = array_map('trim', explode("\n", $referer));
		$referer = array_shift($tmp);
		if (count($tmp) > 0) {
			foreach (array_filter($tmp) as $tmp) {
				$tmp = array_map('trim', explode(':', $tmp, 2));
				// Avoid set an empty method header (key: '')
				if ($tmp[0] !== '' || $tmp[1] !== '') {
					// Key must be lowercase (for override default header)
					$headers[strtolower($tmp[0])] = $tmp[1];
				}
			}
		}
	}
	return $headers;
}

function headers2request(array $headers, $data = '') {
	if (empty($headers) || empty($headers[''])) return html_error('Empty headers array or Non HTTP method');
	$request = trim($headers['']) . "\r\n";
	unset($headers['']);
	foreach ($headers as $header => $value) {
		$header = strtolower($header);
		if ($header != 'connection' && $value !== '') {
			$request .= strtr(ucwords(strtr(trim($header), '-', ' ')), ' ', '-') . ': ' . trim($value) . "\r\n";
		}
	}
	$request .= "Connection: Close\r\n\r\n$data";
	return $request;
}

function geturl($host, $port, $url, $referer = 0, $cookie = 0, $post = 0, $saveToFile = 0, $proxy = 0, $pauth = 0, $auth = 0, $scheme = 'http', $resume_from = 0, $XMLRequest = 0) {
	global $nn, $lastError, $Resume, $bytesReceived, $fp, $fs, $force_name, $options, $sFilters;
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
		if (!extension_loaded('openssl')) return html_error('You need to install/enable PHP\'s OpenSSL extension to support downloading via HTTPS.');
		$scheme = 'ssl://';
		if ($port == 0 || $port == 80) $port = 443;
	} else if ($port == 0) $port = 80;

	if ($proxy) {
		list($proxyHost, $proxyPort) = explode(':', $proxy, 2);
		if ($scheme != 'ssl://') {
			$host = $host . ($port != 80 && $port != 443 ? ":$port" : '');
			$url = "$scheme$host$url";
		}
	}

	if ($scheme != 'ssl://') $scheme = '';

	$cHeaders = readCustomHeaders($referer);
	$request = array();
	$request[''] = $method . ' ' . str_replace(' ', '%20', $url) . ' HTTP/1.1';
	$request['host'] = $host;
	$request['user-agent'] = rl_UserAgent;
	$request['accept'] = 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';
	if (!$saveToFile) $request['accept-encoding'] = 'gzip, deflate';
	$request['accept-language'] = 'en-US,en;q=0.5';
	if (!empty($referer)) $request['referer'] = $referer;
	if (!empty($cookies)) $request['cookie'] = $cookies;
	$request['cache-control'] = $request['pragma'] = 'no-cache';
//	if ($Resume['use'] === TRUE) $request['range'] = 'bytes=' . $Resume['from'] . '-';
	if (!empty($auth)) $request['authorization'] = "Basic $auth";
	if (!empty($pauth) && !$scheme) $request['proxy-authorization'] = "Basic $pauth";
	if ($method == 'POST') {
		$request['content-type'] = 'application/x-www-form-urlencoded';
		$request['content-length'] = strlen($postdata);
	}
	if ($XMLRequest) $request['x-requested-with'] = 'XMLHttpRequest';

	$request = headers2request(array_merge($request, $cHeaders), $postdata);

	$errno = 0;
	$errstr = '';
	if ($scheme == 'ssl://') {
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

	stream_set_timeout($fp, 120);

	if ($saveToFile) {
		if ($proxy) echo '<p>' . sprintf(lang(89), $proxyHost, $proxyPort) . '<br />GET: <b>' . htmlspecialchars($url) . "</b>...<br />\n";
		else echo '<p>'.sprintf(lang(90), $host, $port).'</p>';
	}

	if ($scheme == 'ssl://' && $proxy) {
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
		if (!stream_socket_enable_crypto($fp, true, (defined('STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT') ? STREAM_CRYPTO_METHOD_TLSv1_0_CLIENT | STREAM_CRYPTO_METHOD_TLSv1_1_CLIENT | STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT : STREAM_CRYPTO_METHOD_TLS_CLIENT))) return html_error('TLS Startup Error.');
	}

	#########################################################################
	fwrite($fp, $request);
	fflush($fp);
	$timeStart = microtime(true);

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

	// Array for active stream filters
	$sFilters = array();
	if (stripos($header, "\nTransfer-Encoding: chunked") !== false && in_array('dechunk', stream_get_filters())) {
		// Add built-in dechunk filter
		$sFilters['dechunk'] = stream_filter_append($fp, 'dechunk', STREAM_FILTER_READ);
		if (!$sFilters['dechunk'] && $saveToFile) return html_error('Unknown error while initializing dechunk filter, cannot continue download.');
	}

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
				else if (preg_match('@filename=(\")?([^\r\n]+?)(?(1)\"|[;\r\n])@i', $ContentDisposition, $fn)) {
					if (preg_match('@&(?:[A-Z]+|#[0-9]+|#X[0-9A-F]+);@i', $fn[2])) $fn[3] = html_entity_decode($fn[2], ENT_QUOTES, 'UTF-8');
					$FileName = (empty($fn[3]) ? $fn[2] : $fn[3]);
				}
				else $FileName = $saveToFile;
			} else $FileName = $saveToFile;
		}
		$FileName = str_replace(array_merge(range(chr(0), chr(31)), str_split("<>:\"/|?*\x5C\x7F")), '', basename(trim($FileName)));

		$extPos = strrpos($FileName, '.');
		$ext = ($extPos ? substr($FileName, $extPos) : '');
		if (is_array($options['forbidden_filetypes']) && in_array(strtolower($ext), array_map('strtolower', $options['forbidden_filetypes']))) {
			if ($options['forbidden_filetypes_block']) return html_error(sprintf(lang(82), $ext));
			if (empty($options['rename_these_filetypes_to'])) $options['rename_these_filetypes_to'] = '.xxx';
			else if (strpos($options['rename_these_filetypes_to'], '.') === false) $options['rename_these_filetypes_to'] = '.' . $options['rename_these_filetypes_to'];
			$FileName = substr_replace($FileName, $options['rename_these_filetypes_to'], $extPos);
		}

		if (!empty($options['rename_prefix'])) $FileName = $options['rename_prefix'] . '_' . $FileName;
		if (!empty($options['rename_suffix'])) $FileName = ($extPos > 0 ? substr($FileName, 0, $extPos) : $FileName) . '_' . $options['rename_suffix'] . $ext;
		if (!empty($options['rename_underscore'])) $FileName = str_replace(array(' ', '%20'), '_', $FileName);

		$saveToFile = dirname($saveToFile) . PATH_SPLITTER . $FileName;

		if (@file_exists($saveToFile) && $Resume['use'] !== TRUE) {
			if ($options['bw_save']) return html_error(lang(99) . ': ' . link_for_file($saveToFile));
			$FileName = time() . '_' . $FileName;
			$saveToFile = dirname($saveToFile) . PATH_SPLITTER . $FileName;
		}
		$fs = @fopen($saveToFile, ($Resume['use'] === TRUE ? 'ab' : 'wb'));
		if (!$fs) {
			$lastError = sprintf(lang(101), $FileName, dirname($saveToFile)) . '<br />' . lang(102) . '<br /><a href="javascript:location.reload();">' . lang(103) . '</a>';
			return FALSE;
		}

		flock($fs, LOCK_EX);
		if ($Resume['use'] === TRUE && stripos($header, "\nContent-Range: ") !== false) {
			list($temp, $Resume['range']) = explode(' ', trim(cut_str($header, "\nContent-Range: ", "\n")));
			list($Resume['range'], $fileSize) = explode('/', $Resume['range']);
			$fileSize = bytesToKbOrMbOrGb($fileSize);
		} else $fileSize = bytesToKbOrMbOrGb($bytesTotal);
		$chunkSize = GetChunkSize($bytesTotal);
		echo(lang(104) . " <b>$FileName</b>, " . lang(56) . " <b>$fileSize</b>...<br />");

		//$scriptStarted = false;
		require_once(TEMPLATE_DIR . '/transloadui.php');
		if ($Resume['use'] === TRUE) {
			$received = bytesToKbOrMbOrGb(filesize($saveToFile));
			$percent = round($Resume['from'] / ($bytesTotal + $Resume['from']) * 100, 2);
			echo "<script type='text/javascript'>pr('$percent', '$received', '0');</script>";
			//$scriptStarted = true;
			flush();
		}

		$time = $last = $lastChunkTime = 0;
		do {
			$data = @fread($fp, $chunkSize);
			$datalen = strlen($data);
			if ($datalen <= 0) break;
			$bytesSaved = fwrite($fs, $data);
			if ($bytesSaved !== false && $datalen == $bytesSaved) {
				$bytesReceived += $bytesSaved;
			} else {
				$lastError = sprintf(lang(105), $FileName);
				// unlink($saveToFile);
				return false;
			}
			if ($bytesReceived >= $bytesTotal) $percent = 100;
			else $percent = @round(($bytesReceived + $Resume['from']) / ($bytesTotal + $Resume['from']) * 100, 2);
			if ($bytesReceived > $last + $chunkSize && (!$lastChunkTime || !(((microtime(true) - $timeStart) - $lastChunkTime) < 1))) {
				$received = bytesToKbOrMbOrGb($bytesReceived + $Resume['from']);
				$time = microtime(true) - $timeStart;
				$chunkTime = $time - $lastChunkTime;
				$chunkTime = ($chunkTime > 0) ? $chunkTime : 1;
				$lastChunkTime = $time;
				$speed = @round((($bytesReceived - $last) / 1024) / $chunkTime, 2);
				echo "<script type='text/javascript'>pr('$percent', '$received', '$speed');</script>";
				flush();
				$last = $bytesReceived;
			}
			if (!empty($bytesTotal) && ($bytesReceived + $chunkSize) > $bytesTotal) $chunkSize = $bytesTotal - $bytesReceived;
		} while (!feof($fp));

		flock($fs, LOCK_UN);
		fclose($fs);
		if ($bytesReceived <= 0) {
			$lastError = lang(106);
			stream_socket_shutdown($fp, STREAM_SHUT_RDWR);
			fclose($fp);
			return FALSE;
		}
	} else {
		$length = trim(cut_str($header, "\nContent-Length: ", "\n"));
		if (!$length || !is_numeric($length)) $length = -1;
		$page = stream_get_contents($fp, $length);
	}

	stream_socket_shutdown($fp, STREAM_SHUT_RDWR);
	fclose($fp);
	if ($saveToFile) {
		return array('time' => sec2time(round($time)), 'speed' => @round($bytesTotal / 1024 / (microtime(true) - $timeStart), 2), 'received' => true, 'size' => $fileSize, 'bytesReceived' => ($bytesReceived + $Resume['from']), 'bytesTotal' => ($bytesTotal + $Resume ['from']), 'file' => $saveToFile, 'name' => $FileName);
	} else {
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
}

function cURL($link, $cookie = 0, $post = 0, $referer = 0, $auth = 0, $opts = 0) {
	static $NSS, $ch, $lastProxy;
	if (empty($link) || !is_string($link)) return html_error(lang(24));
	if (!extension_loaded('curl') || !function_exists('curl_init') || !function_exists('curl_exec')) return html_error('cURL isn\'t enabled or cURL\'s functions are disabled');
	if (!empty($referer)) {
		$arr = array_map('trim', explode("\n", $referer));
		$referer = array_shift($arr);
		$header = array_filter($arr);
	} else $header = array();
	$link = str_replace(array(' ', "\r", "\n"), array('%20'), $link);
	$opt = array(CURLOPT_HEADER => 1, CURLOPT_SSL_VERIFYPEER => 0,
		CURLOPT_SSL_VERIFYHOST => 0, CURLOPT_RETURNTRANSFER => 1,
		CURLOPT_FOLLOWLOCATION => 0, CURLOPT_FAILONERROR => 0,
		CURLOPT_FORBID_REUSE => 0, CURLOPT_FRESH_CONNECT => 0,
		CURLINFO_HEADER_OUT => 1, CURLOPT_URL => $link,
		CURLOPT_SSLVERSION => (defined('CURL_SSLVERSION_TLSv1') ? CURL_SSLVERSION_TLSv1 : 1),
		CURLOPT_ENCODING => 'gzip, deflate', CURLOPT_USERAGENT => rl_UserAgent);

	// Fixes "Unknown cipher in list: TLSv1" on cURL with NSS
	if (!isset($NSS)) {
		$cV = curl_version();
		$NSS = (!empty($cV['ssl_version']) && strtoupper(substr($cV['ssl_version'], 0, 4)) == 'NSS/');
	}
	if (!$NSS) $opt[CURLOPT_SSL_CIPHER_LIST] = 'TLSv1';


	// Uncomment next line if do you have IPv6 problems
	// $opt[CURLOPT_IPRESOLVE] = CURL_IPRESOLVE_V4;

	$opt[CURLOPT_REFERER] = !empty($referer) ? $referer : false;
	$opt[CURLOPT_COOKIE] = !empty($cookie) ? (is_array($cookie) ? CookiesToStr($cookie) : trim($cookie)) : false;

	if (!empty($_GET['useproxy']) && !empty($_GET['proxy'])) {
		$opt[CURLOPT_HTTPPROXYTUNNEL] = strtolower(parse_url($link, PHP_URL_SCHEME) == 'https') ? true : false; // cURL https proxy support... Experimental.
		// $opt[CURLOPT_HTTPPROXYTUNNEL] = false; // Uncomment this line for disable https proxy over curl.
		$opt[CURLOPT_PROXY] = $_GET['proxy'];
		$opt[CURLOPT_PROXYUSERPWD] = (!empty($GLOBALS['pauth']) ? base64_decode($GLOBALS['pauth']) : false);
	} else $opt[CURLOPT_PROXY] = false;

	// Send more headers...
	$headers = array('Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8', 'Accept-Language: en-US,en;q=0.5', 'Pragma: no-cache', 'Cache-Control: no-cache', 'Connection: Keep-Alive');
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

	if (!isset($lastProxy)) $lastProxy = $opt[CURLOPT_PROXY];
	if (!isset($ch)) $ch = curl_init();
	elseif ($lastProxy != $opt[CURLOPT_PROXY]) {
		// cURL seems that doesn't like switching proxies on a active resource, there is a bug about that @ https://bugs.php.net/bug.php?id=68211
		curl_close($ch);
		$ch = curl_init();
		$lastProxy = $opt[CURLOPT_PROXY];
	}

	foreach ($opt as $O => $V) curl_setopt($ch, $O, $V); // Using this instead of 'curl_setopt_array'

	$page = curl_exec($ch);
	$info = curl_getinfo($ch);
	$errz = curl_errno($ch);
	$errz2 = curl_error($ch);
	// curl_close($ch);

	if (substr($page, 9, 3) == '100' || !empty($opt[CURLOPT_PROXY])) $page = preg_replace("@^HTTP/1\.[01] \d{3}(?:\s[^\r\n]+)?\r\n\r\n(HTTP/1\.[01] \d+ [^\r\n]+)@i", "$1", $page, 1); // The "100 Continue" or "200 Connection established" can break some functions in plugins, lets remove it...
	if ($errz != 0) return html_error("[cURL:$errz] $errz2");

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
	if (empty($cookie)) return '';
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
	preg_match_all('/\nSet-Cookie: (.*)(;|\r\n)/Ui', $content, $temp);
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
	if (empty($content) || stripos($content, "\nSet-Cookie: ") === false || !preg_match_all ('/\nSet-Cookie: ([^\r\n]+)/i', $content, $temp)) return $cookie;
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

	$fileSize = filesize($file);

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
		if (!extension_loaded('openssl')) return html_error('You need to install/enable PHP\'s OpenSSL extension to support uploading via HTTPS.');
		$scheme = 'ssl://';
		if ($port == 0 || $port == 80) $port = 443;
	} else if ($port == 0) $port = 80;

	if (!empty($referer) && ($pos = strpos("\r\n", $referer)) !== 0) {
		$origin = parse_url($pos ? substr($referer, 0, $pos) : $referer);
		$origin = strtolower($origin['scheme']) . '://' . strtolower($origin['host']) . (!empty($origin['port']) && $origin['port'] != defport(array('scheme' => $origin['scheme'])) ? ':' . $origin['port'] : '');
	} else $origin = ($scheme == 'ssl://' ? 'https://' : $scheme) . $host . ($port != 80 && ($scheme != 'ssl://' || $port != 443) ? ':' . $port : '');

	if ($proxy) {
		list($proxyHost, $proxyPort) = explode(':', $proxy, 2);
		if ($scheme != 'ssl://') {
			$host = $host . ($port != 80 && $port != 443 ? ":$port" : '');
			$url = "$scheme$host$url";
		}
	}

	if ($scheme != 'ssl://') $scheme = '';

	$cHeaders = readCustomHeaders($referer);
	$request = array();
	$request[''] = 'POST ' . str_replace(' ', '%20', $url) . ' HTTP/1.1';
	$request['host'] = $host;
	$request['user-agent'] = $upagent;
	$request['accept'] = 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';
	$request['accept-encoding'] = 'gzip, deflate';
	$request['accept-language'] = 'en-US,en;q=0.5';
	if (!empty($referer)) $request['referer'] = $referer;
	if (!empty($cookies)) $request['cookie'] = $cookies;
	if (!empty($pauth) && !$scheme) $request['proxy-authorization'] = "Basic $pauth";
	$request['origin'] = $origin;
	$request['content-type'] = "multipart/form-data; boundary=$bound";
	$request['content-length'] = (strlen($postdata) + strlen($nn . "--" . $bound . "--" . $nn) + $fileSize);

	$request = headers2request(array_merge($request, $cHeaders), $postdata);

	$errno = 0;
	$errstr = '';
	if ($scheme == 'ssl://') {
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

	if ($scheme == 'ssl://' && $proxy) {
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
		if (!stream_socket_enable_crypto($fp, true, (defined('STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT') ? STREAM_CRYPTO_METHOD_TLSv1_0_CLIENT | STREAM_CRYPTO_METHOD_TLSv1_1_CLIENT | STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT : STREAM_CRYPTO_METHOD_TLS_CLIENT))) return html_error('TLS Startup Error.');
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

function putfile($host, $port, $url, $referer, $cookie, $file, $filename, $proxy = 0, $pauth = 0, $upagent = 0, $scheme = 'http') {
	global $nn, $lastError, $fp, $fs;

	if (empty($upagent)) $upagent = rl_UserAgent;
	$scheme = strtolower("$scheme://");

	if (!is_readable($file)) {
		$lastError = sprintf(lang(65), $file);
		return FALSE;
	}

	$fileSize = filesize($file);

	if (!empty($cookie)) {
		if (is_array($cookie)) $cookies = (count($cookie) > 0) ? CookiesToStr($cookie) : 0;
		else $cookies = trim($cookie);
	}

	if ($scheme == 'https://') {
		if (!extension_loaded('openssl')) return html_error('You need to install/enable PHP\'s OpenSSL extension to support uploading via HTTPS.');
		$scheme = 'ssl://';
		if ($port == 0 || $port == 80) $port = 443;
	} else if ($port == 0) $port = 80;

	if (!empty($referer) && ($pos = strpos("\r\n", $referer)) !== 0) {
		$origin = parse_url($pos ? substr($referer, 0, $pos) : $referer);
		$origin = strtolower($origin['scheme']) . '://' . strtolower($origin['host']) . (!empty($origin['port']) && $origin['port'] != defport(array('scheme' => $origin['scheme'])) ? ':' . $origin['port'] : '');
	} else $origin = ($scheme == 'ssl://' ? 'https://' : $scheme) . $host . ($port != 80 && ($scheme != 'ssl://' || $port != 443) ? ':' . $port : '');

	if ($proxy) {
		list($proxyHost, $proxyPort) = explode(':', $proxy, 2);
		if ($scheme != 'ssl://') {
			$host = $host . ($port != 80 && $port != 443 ? ":$port" : '');
			$url = "$scheme$host$url";
		}
	}

	if ($scheme != 'ssl://') $scheme = '';

	$cHeaders = readCustomHeaders($referer);
	$request = array();
	$request[''] = 'PUT ' . str_replace(' ', '%20', $url) . ' HTTP/1.1';
	$request['host'] = $host;
	$request['user-agent'] = $upagent;
	$request['accept'] = 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';
	$request['accept-encoding'] = 'gzip, deflate';
	$request['accept-language'] = 'en-US,en;q=0.5';
	if (!empty($referer)) $request['referer'] = $referer;
	if (!empty($cookies)) $request['cookie'] = $cookies;
	if (!empty($pauth) && !$scheme) $request['proxy-authorization'] = "Basic $pauth";
	$request['origin'] = $origin;
	$request['content-disposition'] = 'attachment';
	$request['content-type'] = 'multipart/form-data';
	$request['x-file-name'] = $filename;
	$request['x-file-size'] = $request['content-length'] = $fileSize;

	$request = headers2request(array_merge($request, $cHeaders));

	$errno = 0;
	$errstr = '';
	if ($scheme == 'ssl://') {
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

	if ($proxy) echo '<p>' . sprintf(lang(89), $proxyHost, $proxyPort) . '<br />PUT: <b>' . htmlspecialchars($url) . "</b>...<br />\n";
	else echo '<p>'.sprintf(lang(90), $host, $port).'</p>';

	if ($scheme == 'ssl://' && $proxy) {
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
		if (!stream_socket_enable_crypto($fp, true, (defined('STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT') ? STREAM_CRYPTO_METHOD_TLSv1_0_CLIENT | STREAM_CRYPTO_METHOD_TLSv1_1_CLIENT | STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT : STREAM_CRYPTO_METHOD_TLS_CLIENT))) return html_error('TLS Startup Error.');
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