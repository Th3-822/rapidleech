<?php
if (!defined('RAPIDLEECH')) {
	require('../deny.php');
	exit;
}
//Copyright Dman :p this has been coded by dman biatches!!!
//Optimized by zpikdum :D
//Moded by eqbal ;)
//Lets calulate the time required.
$time = explode(' ', microtime());
$time = $time[1] + $time[0];
$begintime = $time;
//User Enabled settings
$debug = 0; // change it to one to enable it.
//Override PHP's stardard time limit
set_time_limit(120);
$maxlinks = 300;
$lcver = 301;
if ($options['fgc'] != 1 && !extension_loaded('curl')) $options['fgc'] = 1;
//$options['fgc'] = 1;

//Lets use this as a function to visit the site.
function curl($link, $post = 0, $cookie = 0, $follow = 1, $refer = 0, $header = 1) {
	global $options, $debug;
	if ($follow && ($follow > 9 || $follow < 1)) $follow = 1;
	if ($post && is_array($post)) {
		$POST = '';
		foreach ($post as $k => $v) $POST .= "$k=$v&";
		$post = substr($POST, 0, -1);
		unset($POST);
	}
	if ($cookie && is_array($cookie)) {
		if (count($cookie) > 0) {
			$cookies = '';
			foreach ($cookie as $k => $v) $cookies .= "$k=$v; ";
			$cookie = substr($cookies, 0, -2);
			unset($cookies);
		} else $cookie = 0;
	}

	if($options['fgc'] == 1) {
		// Using file_get_contents.
		$opt = array(
			'method' => ($post != 0) ? 'POST' : 'GET',
			'content' => ($post != 0) ? $post : '',
			'max_redirects' => (!$follow) ? 1 : $follow + 1,
			'header' => "Accept-language: en-us;q=0.7,en;q=0.3\r\nAccept: text/html, application/xml;q=0.9, application/xhtml+xml, */*;q=0.1\r\n" .
			($refer ? "Referer: $refer\r\n" : "") .
			($cookie ? "Cookie: $cookie\r\n" : "") .
			"User-Agent: Opera/9.80 (Windows NT 6.1; U; en-US) Presto/2.10.289 Version/12.02\r\n"
		);

		$context = stream_context_create(array('http' => $opt));
		$page = @file_get_contents($link, false, $context);

		if ($header != 0) {
			$headers = implode("\r\n", $http_response_header);
			$page = $headers . "\r\n\r\n" . $page;
		}
	} else {
		// Using cURL.
		$ch = curl_init($link);
		curl_setopt($ch, CURLOPT_HEADER, $header);
		if($cookie) {
			curl_setopt($ch, CURLOPT_COOKIE, $cookie);
			curl_setopt($ch, CURLOPT_COOKIEJAR, "1");
			curl_setopt($ch, CURLOPT_COOKIEFILE, "1");
		}
		if($follow) {
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
			curl_setopt($ch, CURLOPT_MAXREDIRS, $follow+1);
		}
		curl_setopt($ch, CURLOPT_USERAGENT, 'Opera/9.80 (Windows NT 6.1; U; en-US) Presto/2.10.289 Version/12.02');

		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: text/html, application/xml;q=0.9, application/xhtml+xml, */*;q=0.1','Accept-Language: en-us;q=0.7,en;q=0.3'));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		if (!empty($refer)) {
			$arr = explode("\r\n", $refer);
			$header = array();
			if (count($arr) > 1) {
				$refer = $arr[0];
				unset($arr[0]);
				$header = array_filter(array_map('trim', $arr));
			}
			curl_setopt($ch, CURLOPT_REFERER, $refer);
			if (count($header) > 0) curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		}
		if($post != '0') {
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		}
		$page = curl_exec($ch);
		$errz = curl_errno($ch);
		$errz2 = curl_error($ch);
		$info = curl_getinfo($ch);
		curl_close($ch);
	}
	//if ($debug == 1 && !empty($_POST['debug'])){
	if ($debug == 1) {
		$debugtxt = "Request: $link\n";
		if ($refer) $debugtxt .= "Referer: $refer\n";
		if ($cookie) $debugtxt .= "Cookie: $cookie\n";
		if (isset($errz) && $errz != 0) $debugtxt .= "cURL($errz): $errz2\n";
		if ($post) $debugtxt .= 'POST: '.print_r($post, true)."\n";
		$debugtxt .= "\n" . $page;
		textarea($debugtxt, 170, 15);
	}
	return($page);
}


function check(&$link, $x, $regex, $szregex='', $pattern='', $replace='', $opt = array()) {
	if(!empty($pattern)) $link = preg_replace($pattern, $replace, $link);

	$cook = $ref = $bytes = $fixsize = $size = 0;
	$fol = $head = 1;
	if(!empty($opt)) {
		if (array_key_exists('bytes', $opt)) $bytes = ($opt['bytes']) ? 1 : 0;
		if (array_key_exists('fixsize', $opt)) {
			$fixsize = ($opt['fixsize']) ? 1 : 0;
			$fixsizeP = $fixsizeR = '';
			if ($fixsize && array_key_exists('fixsizeP', $opt)) {
				$fixsizeP = $opt['fixsizeP'];
				if (array_key_exists('fixsizeR', $opt)) $fixsizeR = $opt['fixsizeR'];
			}
		}
		if (array_key_exists('cookie', $opt) && $opt['cookie'] != false) $cookie = $opt['cookie'];
		if (array_key_exists('follow', $opt)) $fol = $opt['follow'];
		if (array_key_exists('referer', $opt) && $opt['referer'] != false) $ref = $opt['referer'];
		if (array_key_exists('header', $opt)) $head = ($opt['header']) ? 1 : 0;
	}

	$page = curl($link, 0, $cookie, $fol, $ref, $head);
	$link = htmlentities($link, ENT_QUOTES);

	if (($szregex !== true && preg_match('@'.$regex.'@i', $page)) || ($szregex === true && preg_match('@'.$regex.'@i', $page, $fz))) {
		if (!empty($fz) || (!empty($szregex) && preg_match('@'.$szregex.'@i', $page, $fz))) {
			if (!array_key_exists('size', $fz)) $fz['size'] = $fz[1];

			if ($bytes) $size = bytesToKbOrMbOrGb($fz['size']);
			else if ($fixsize) {
				if (!array_key_exists('XB', $fz)) $fz['XB'] = $fz[2];
				$fz['XB'] = str_replace(array('YTES', 'YTE'), '', strtoupper($fz['XB']));
				if (!empty($fixsizeP)) $fz['size'] = str_replace($fixsizeP, $fixsizeR, $fz['size']);
				$fz['size'] = str_replace(',', '.', $fz['size']);
				switch ($fz['XB']) { // KbOrMbOrGbToBytes :D
					case 'GB': $fz['size'] *= 1024;
					case 'MB': $fz['size'] *= 1024;
					case 'KB': $fz['size'] *= 1024;
				}
				$size = bytesToKbOrMbOrGb($fz['size']);
			} else {
				if (!array_key_exists('XB', $fz)) $fz['XB'] = strtoupper($fz[2]);
				$fz['size'] = str_replace(',', '.', $fz['size']);
				$size = $fz['size'].' '.$fz['XB'];
			}

			$size = explode(' ', $size);
			$size[0] = round($size[0], 2);
			$size = implode(' ', $size);
		}
		$chk = showlink($link, $size);
	} elseif (empty($page) || preg_match('@class="bott_p_access2?"@i', $page)) $chk = showlink($link, 0, 2);
	else $chk = showlink($link, 0, 0);

	return array($chk, $size);
}

function showlink($link, $size='', $chk=1, $title='') {
	global $x;
	switch ($chk) {
		case 1:
			$cl = 'g';
			$ret = "<a class='$cl' title='".lang(114)."' href='$link'><b>$link</b></a>";
			if (!$_POST['d']) $ret = "$x: ".lang(114).": $ret";
			if (!empty($size)) $ret .= " | <span title='".lang(56)."'>$size</span>";
			break;
		case 2:
			$cl = 'y';
			$ret = "<a class='$cl' title='".lang(115)."' href='$link'><b>$link</b></a>";
			if (!$_POST['d']) $ret = "$x: ".lang(115).": $ret";
			break;
		case 3:
			$cl = 'b';
			$ret = "<a class='$cl' title='".htmlentities($title, ENT_QUOTES)."' href='$link'><b>$link</b></a>";
			break;
		case 4:
			$cl = 'y';
			$ret = "<b>$link&nbsp;???</b>";
			break;
		default:
			$cl = 'r';
			$ret = "<a class='$cl' title='".lang(116)."' href='$link'><b>$link</b></a>";
			if (!$_POST['d']) $ret = "$x: ".lang(116).": $ret";
			break;
	}

	$ret = "<div class='$cl' style='text-align:left;'".(!empty($title)?" title='".htmlentities($title, ENT_QUOTES)."'":'').">$ret</div>\n";

	echo $ret;
	return $chk;
}

// Load /classes/http.php or paste the function?...
function GetCookiesArr($content, $cookie=array(), $del=true, $dval=array('','deleted')) {
	if (!is_array($cookie)) $cookie = array();
	if (($hpos = strpos($content, "\r\n\r\n")) > 0) $content = substr($content, 0, $hpos); // We need only the headers
	if (empty($content) || stripos($content, "\r\nSet-Cookie: ") === false || !preg_match_all ('/\r\nSet-Cookie: (.*)(;|\r\n)/U', $content, $temp)) return $cookie;
	foreach ($temp[1] as $v) {
		$v = explode('=', $v, 2);
		$cookie[$v[0]] = $v[1];
		if ($del) {
			if (!is_array($dval)) $dval = array($dval);
			if (in_array($v[1], $dval)) unset($cookie[$v[0]]);
		}
	}
	return $cookie;
}

function debug() {
	echo '<div style="text-align:left; margin:0 auto; width:450px;">';
	if ( !extension_loaded("curl") )
		echo lang(117)."<br />";
	else
		echo "<b>".lang(118)."</b><br />";
	if( PHP_VERSION < 5 ){
		echo lang(119)."<br />";
	}
	echo lang(120)."<br />";
	echo "</div>";
}
?>