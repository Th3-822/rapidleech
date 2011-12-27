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
$debug = 1; // change it to one to enable it.
//Override PHP's stardard time limit
set_time_limit(120);
$maxlinks = 300;
$lcver = 301;
if ($options['fgc'] != 1 && !extension_loaded("curl")) $options['fgc'] = 1;
//$options['fgc'] = 1;

//Lets use this as a function to visit the site.
function curl($link, $post = 0, $cook = 0, $follow = 1, $refer = 0, $header = 1) {
	global $options, $debug;
	if ($follow && ($follow > 9 || $follow < 1)) $follow = 1;
	if ($post && is_array($post)) {
			$POST = "";
			foreach ($post as $k => $v) {
				$POST .= "$k=$v&";
			}
			$post = substr($POST, 0, -1);
			unset($POST);
	}

	if($options['fgc'] == 1) {
		// Using file_get_contents.
		$opt = array(
			'method' => ($post != 0) ? "POST" : "GET",
			'content' => ($post != 0) ? $post : '',
			'max_redirects' => (!$follow) ?  1 : $follow+1,
			'header' => "Accept-language: en\r\n" .
			($refer ? "Referer: $refer\r\n" : "") .
			($cook ? "Cookie: $cook\r\n" : "") .
			"User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.7.6) Gecko/20050317 Firefox/1.0.2\r\n"
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
		if($cook) {
			curl_setopt($ch, CURLOPT_COOKIE, $cook);
			curl_setopt($ch, CURLOPT_COOKIEJAR, "1");
			curl_setopt($ch, CURLOPT_COOKIEFILE, "1");
		}
		if($follow) {
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
			curl_setopt($ch, CURLOPT_MAXREDIRS, $follow+1);
		}
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.7.6) Gecko/20050317 Firefox/1.0.2");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		if ($refer) curl_setopt($ch, CURLOPT_REFERER, $refer);
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
	if ($debug == 1 && !empty($_POST['debug'])){
		echo '<textarea rows="15" cols="170" readonly="readonly">Request: ' . htmlentities($link) . "\n";
		if ($refer) echo "Referer: " . htmlentities($refer) . "\n";
		if ($cook) echo "Cookie: " . htmlentities($cook) . "\n";
		if ($errz != 0)  echo "cURL($errz): $errz2\n";
		if ($post) {
			echo "POST: ";var_dump($post);echo "\n";
		}
		echo "\n" . htmlentities($page) . "</textarea><br />\n";
	}
	return($page);
}


function check(&$link, $x, $regex, $szregex='', $pattern='', $replace='', $opt = array()) {
	if(!empty($pattern)) $link = preg_replace($pattern, $replace, $link);

	$cook = $ref = $bytes = $fixsize = $size = 0;
	$fol = $head = 1;
	if(!empty($opt)) {
		if (array_key_exists('bytes', $opt)) {
			$bytes = ($opt['bytes']) ? 1 : 0;
		}
		if (array_key_exists('fixsize', $opt)) {
			$fixsize = ($opt['fixsize']) ? 1 : 0;
			$fixsizeP = $fixsizeR = '';
			if ($fixsize) {
				if (array_key_exists('fixsizeP', $opt)) {
					$fixsizeP = $opt['fixsizeP'];
					if (array_key_exists('fixsizeR', $opt)) $fixsizeR = $opt['fixsizeR'];
				}
			}
		}
		if (array_key_exists('cookie', $opt) && $opt['cookie'] != false) {
			$cook = $opt['cookie'];
		}
		if (array_key_exists('follow', $opt)) {
			$fol = $opt['follow'];
		}
		if (array_key_exists('referer', $opt) && $opt['referer'] != false) {
			$ref = $opt['referer'];
		}
		if (array_key_exists('header', $opt)) {
			$head = ($opt['header']) ? 1 : 0;
		}
	}
	$page = curl($link, 0, $cook, $fol, $ref, $head);
	$link = htmlentities($link, ENT_QUOTES);

	if (preg_match('@'.$regex.'@i', $page)) {
		if (!empty($szregex) && preg_match('@'.$szregex.'@i', $page, $fz)) {
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
	} elseif (empty($page) || preg_match("@The file you are trying to access is temporarily unavailable.@i", $page)) $chk = showlink($link, 0, 2);
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
			if (!empty($size) && $size != 0) $ret .= " | <span title='".lang(56)."'>$size</span>";
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