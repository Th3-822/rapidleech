<?php
######## Account Info ########
/* Warning: Login uses a lot of CPU, make sure to upload at least 1 file in your account with this plugin for saving account data before adding it here.*/
$upload_acc['mega_co_nz']['user'] = ''; //Set your email
$upload_acc['mega_co_nz']['pass'] = ''; //Set your password
########################

$chunk_UL = false; // Set to true to upload only 1 encrypted chunk per request to mega. (Switch to true when you are getting many Data sending errors)
$calcMacEachChunk = true; // Set to false for get the file's cbc-mac after upload. (Sv will read the file 2 times & too high cpu usage in short time)

$_GET['proxy'] = isset($_GET['proxy']) ? $_GET['proxy'] : '';
$not_done = true;
$T8 = array('seqno' => mt_rand(), 'sid' => '');

echo "<center>Mega.co.nz plugin by <b>Th3-822</b></center><br />\n"; // Please, do not remove or change this line contents. - Th3-822
if (!extension_loaded('mcrypt') || !in_array('rijndael-128', mcrypt_list_algorithms(), true)) html_error("Mcrypt module isn't installed or it doesn't have support for the needed encryption.");

// OpenSSL is Much Faster (Only Works Since 5.4)
if (version_compare(PHP_VERSION, '5.4.0', '>=') && extension_loaded('openssl') && in_array('aes-128-cbc', openssl_get_cipher_methods(), true)) {
	function aes_cbc_encrypt($data, $key) {
		$data = str_pad($data, 16 * ceil(strlen($data) / 16), "\0"); // OpenSSL needs this padded.
		return openssl_encrypt($data, 'aes-128-cbc', $key, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING, "\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0");
	}
	function aes_cbc_decrypt($data, $key) {
		$data = str_pad($data, 16 * ceil(strlen($data) / 16), "\0"); // OpenSSL needs this padded.
		return openssl_decrypt($data, 'aes-128-cbc', $key, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING, "\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0");
	}
} else {
	function aes_cbc_encrypt($data, $key) {
		return mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $data, MCRYPT_MODE_CBC, "\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0");
	}
	function aes_cbc_decrypt($data, $key) {
		return mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $data, MCRYPT_MODE_CBC, "\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0");
	}
}

if (!empty($upload_acc['mega_co_nz']['user']) && !empty($upload_acc['mega_co_nz']['pass'])) {
	$default_acc = true;
	$_REQUEST['T8']['up_login'] = $upload_acc['mega_co_nz']['user'];
	$_REQUEST['T8']['up_pass'] = $upload_acc['mega_co_nz']['pass'];
	$_REQUEST['action'] = 'Th3-822';
	echo "<center><b>Using Default Login.</b></center><br />\n";
} else $default_acc = false;

if (empty($_REQUEST['action']) || $_REQUEST['action'] != 'Th3-822') {
	echo "<table border='0' style='width:270px;' cellspacing='0' align='center'>
	<form method='POST'>
	<input type='hidden' name='action' value='Th3-822' />
	<tr><td style='white-space:nowrap;'>&nbsp;Email*</td><td>&nbsp;<input type='text' name='T8[up_login]' value='' style='width:160px;' /></td></tr>
	<tr><td style='white-space:nowrap;'>&nbsp;Password*</td><td>&nbsp;<input type='password' name='T8[up_pass]' value='' style='width:160px;' /></td></tr>\n";
	echo "<tr><td colspan='2' align='center'><br /><input type='submit' value='Upload' /></td></tr>\n";
	echo "<tr><td colspan='2' align='center'><small>*You can set it as default in <b>".basename(__FILE__)."</b></small></td></tr>\n";
	echo "</table>\n</form>\n";
	echo "<p style='text-align:center'>Login step may last <b>1 minute or more with high CPU usage</b>, longer passwords will increase that time.<br /><small>After a correct login, session will be stored for skip the login step.</small></p>";
	echo "<script type='text/javascript'>self.resizeTo(700,350);</script>\n"; //Resize upload window
} else {
	$login = $not_done = false;
	$domain = 'mega.co.nz';

	// Login
	echo "<table style='width:600px;margin:auto;'>\n<tr><td align='center'>\n<div id='login' width='100%' align='center'>Login to $domain</div>\n";

	if (!empty($_REQUEST['T8']['up_login']) && !empty($_REQUEST['T8']['up_pass'])) SavedLogin($_REQUEST['T8']['up_login'], $_REQUEST['T8']['up_pass']);
	else html_error('Login Error: Email or Password empty.');

	// Retrive upload ID
	echo "<script type='text/javascript'>document.getElementById('login').style.display='none';</script>\n<div id='info' width='100%' align='center'>Retrive upload url</div>\n";

	$ul_url = apiReq(array('a' => 'u', 's' => $fsize));
	if (is_numeric($ul_url[0])) check_errors($ul_url[0], 'Error getting upload url');
	$up_url = $ul_url[0]['p'];

	$ul_key = array();
	for ($i = 0; $i < 6; $i++) $ul_key[] = get_rand(3);

	// Uploading
	echo "<script type='text/javascript'>document.getElementById('info').style.display='none';</script>\n";

	$mac_str = '';
	$upfiles = T8_mega_upload($up_url, $ul_key, $lfile, $lname, $mac_str);

	// Upload Finished
	echo "<script type='text/javascript'>document.getElementById('progressblock').style.display='none';</script>\n";

	is_page($upfiles);

	if ($calcMacEachChunk) {
		$file_mac = str_to_a32($mac_str);
		mcrypt_generic_deinit($m_td1);
		mcrypt_module_close($m_td1);
		mcrypt_generic_deinit($m_td2);
		mcrypt_module_close($m_td2);
	} else $file_mac = FileMac($lfile, $fsize, a32_to_str(array_slice($ul_key, 0, 4)), a32_to_str(array($ul_key[4], $ul_key[5], $ul_key[4], $ul_key[5])));

	$body = substr($upfiles, strpos($upfiles, "\r\n\r\n") + 4);
	if (is_numeric($body)) check_errors($body, 'Upload Error');

	$meta_mac = array($file_mac[0] ^ $file_mac[1], $file_mac[2] ^ $file_mac[3]);
	$attributes = array('n' => basename($lname));
	$enc_attributes = enc_attr($attributes, array_slice($ul_key, 0, 4));
	$key = array($ul_key[0] ^ $ul_key[4], $ul_key[1] ^ $ul_key[5], $ul_key[2] ^ $meta_mac[0], $ul_key[3] ^ $meta_mac[1], $ul_key[4], $ul_key[5], $meta_mac[0], $meta_mac[1]);

	$file = apiReq(array('a' => 'p', 't' => $T8['root_id'], 'n' => array(array('h' => $body, 't' => 0, 'a' => base64url_encode($enc_attributes), 'k' => a32_to_base64(encrypt_key($key, $T8['master_key']))))));
	if (is_numeric($file[0])) check_errors($file[0], 'Save file error');

	$public_handle = apiReq(array('a' => 'l', 'n' => $file[0]['f'][0]['h']));
	if (is_numeric($public_handle[0])) check_errors($public_handle[0], 'Error getting public fileid');
	$key = substr($file[0]['f'][0]['k'], strpos($file[0]['f'][0]['k'], ':'));
	$decrypted_key = a32_to_base64(decrypt_key(base64_to_a32($key), $T8['master_key']));

	$download_link = "https://$domain/#!{$public_handle[0]}!$decrypted_key";
}

// secure_rand() function from: http://www.zimuel.it/en/strong-cryptography-in-php/
function secure_rand($length) {
	if (function_exists('openssl_random_pseudo_bytes')) {
		$rnd = openssl_random_pseudo_bytes($length, $strong);
		if ($strong === TRUE) return $rnd;
	}
	$sha = $rnd = '';
	if (file_exists('/dev/urandom')) {
		$fp = fopen('/dev/urandom', 'rb');
		if ($fp) {
			if (function_exists('stream_set_read_buffer')) stream_set_read_buffer($fp, 0);
			$sha = fread($fp, $length);
			fclose($fp);
		}
	}
	for ($i=0; $i<$length; $i++) {
		$sha = hash('sha256',$sha.mt_rand());
		$char = mt_rand(0,62);
		$rnd .= chr(hexdec($sha[$char].$sha[$char+1]));
	}
	return $rnd;
}

function get_rand($bytes) {
	return hexdec(bin2hex(secure_rand($bytes)));
}
function Get_Reply($content) {
	if (!function_exists('json_decode')) html_error('Error: Please enable JSON in php.');
	if (($pos = strpos($content, "\r\n\r\n")) > 0) $content = substr($content, $pos + 4);
	$cb_pos = strpos($content, '{');
	$sb_pos = strpos($content, '[');
	if ($cb_pos === false && $sb_pos === false) html_error('Json start braces not found.');
	$sb = ($cb_pos === false || $sb_pos < $cb_pos) ? true : false;
	$content = substr($content, strpos($content, ($sb ? '[' : '{')));$content = substr($content, 0, strrpos($content, ($sb ? ']' : '}')) + 1);
	if (empty($content)) html_error('No json content.');
	$rply = json_decode($content, true);
	if (!$rply || count($rply) == 0) html_error('Error reading json.');
	return $rply;
}
function apiReq($atrr) {
	$try = 0;
	do {
		if ($try > 0) sleep(mt_rand(2,3));
		$ret = doApiReq($atrr);
		$try++;
	} while ($try < 6 && $ret[0] == -3);
	return $ret;
}
function doApiReq($atrr) {
	global $T8;
	$domain = 'g.api.mega.co.nz';//$domain = 'eu.api.mega.co.nz';
	if (!function_exists('json_encode')) html_error('Error: Please enable JSON in php.');
	$cURL = $GLOBALS['options']['use_curl'] && extension_loaded('curl') && function_exists('curl_init') && function_exists('curl_exec') ? true : false;
	$chttps = false;
	if ($cURL) {
		$cV = curl_version();
		if (in_array('https', $cV['protocols'], true)) $chttps = true;
	}
	if (!extension_loaded('openssl') && !$chttps) html_error('You need to install/enable PHP\'s OpenSSL extension to support HTTPS connections.');
	elseif (!$chttps) $cURL = false;

	$sid = (!empty($T8['sid']) ? '&sid=' . $T8['sid'] : '');
	$post = json_encode($atrr);
	$referer = "https://mega.co.nz/\r\nContent-Type: application/json";
	if ($cURL) $page = cURL("https://$domain/cs?id=" . ($T8['seqno']++) . $sid, 0, "[$post]", $referer);
	else {
		global $pauth;
		$page = geturl($domain, 443, '/cs?id=' . ($T8['seqno']++) . $sid, $referer, 0, "[$post]", 0, 0, 0, 0, 'https');
		is_page($page);
	}
	list ($header, $page) = array_map('trim', explode("\r\n\r\n", $page, 2));
	if (is_numeric($page)) return array(intval($page));
	if (in_array(intval(substr($header, 9, 3)), array(500, 503))) return array(-3); //  500 Server Too Busy
	return Get_Reply($page);
}
function check_errors($err, $prefix = 'Error') {
	$isLogin = (stripos($prefix, 'login') !== false);
	switch ($err) {
		default: $msg = '*No message for this error*';break;
		case -1: $msg = 'An internal error has occurred';break;
		case -2: $msg = 'You have passed invalid arguments to this command, your rapidleech is outdated?';break;
		case -3: $msg = 'A temporary congestion or server malfunction prevented your request from being processed';break;
		case -4: $msg = 'You have exceeded your command weight per time quota. Please wait a few seconds, then try again';break;
		case -5: $msg = 'The upload has failed';break;
		case -6: $msg = 'Too many concurrent IP addresses are accessing this upload target URL';break;
		case -7: $msg = 'The upload file packet is out of range or not starting and ending on a chunk boundary';break;
		case -8: $msg = 'The upload target URL you are trying to access has expired. Please request a fresh one';break;
		case -9: $msg = ($isLogin ? 'Email/Password incorrect' : 'Resource not found or deleted');break;
		case -11: $msg = 'Access violation';break;
		case -13: $msg = ($isLogin ? 'Account not Activated yet' : 'Trying to access an incomplete file');break;
		case -14: $msg = 'A decryption operation failed';break;
		case -15: $msg = 'Invalid or expired user session, please relogin';break;
		case -16: $msg = 'User blocked';break;
		case -17: $msg = 'Request over quota';break;
		case -18: $msg = 'Resource temporarily not available, please try again later';break;
	}
	html_error("$prefix: [$err] $msg.");
}

// Using some functions from: http://julien-marchand.fr/blog/using-the-mega-api-with-php-examples/
function base64url_encode($data) {
	return strtr(rtrim(base64_encode($data), '='), '+/', '-_');//return strtr(base64_encode($data), '+/=', '-_,');
}
function base64url_decode($data) {
	if (($s = (2 - strlen($data) * 3) % 4) < 2) $data .= substr(',,', $s);
	return base64_decode(strtr($data, '-_,', '+/='));
}
function a32_to_str($hex) {
	return call_user_func_array('pack', array_merge(array('N*'), $hex));
}
function a32_to_base64($a) {
	return base64url_encode(a32_to_str($a));
}
function str_to_a32($b) {
	// Add padding, we need a string with a length multiple of 4
	$b = str_pad($b, 4 * ceil(strlen($b) / 4), "\0");
	return array_values(unpack('N*', $b));
}
function base64_to_a32($s) {
	return str_to_a32(base64url_decode($s));
}
function aes_cbc_encrypt_a32($data, $key) {
	return str_to_a32(aes_cbc_encrypt(a32_to_str($data), a32_to_str($key)));
}
function aes_cbc_decrypt_a32($data, $key) {
	return str_to_a32(aes_cbc_decrypt(a32_to_str($data), a32_to_str($key)));
}
function enc_attr($attr, $key) {
	$attr = 'MEGA' . json_encode($attr);
	return aes_cbc_encrypt($attr, a32_to_str($key));
}
function stringhash($s, $aeskey) {
	$s32 = str_to_a32($s);
	$h32 = array(0, 0, 0, 0);
	for ($i = 0; $i < count($s32); $i++) $h32[$i % 4] ^= $s32[$i];
	for ($i = 0; $i < 0x4000; $i++) $h32 = aes_cbc_encrypt_a32($h32, $aeskey);
	return a32_to_base64(array($h32[0], $h32[2]));
}
function prepare_key($a) {
	$pkey = array(0x93C467E3, 0x7DB0C7A4, 0xD1BE3F81, 0x0152CB56);
	$count_a = count($a);
	for ($r = 0; $r < 0x10000; $r++) {
		for ($j = 0; $j < $count_a; $j += 4) {
			$key = array(0, 0, 0, 0);
			for ($i = 0; $i < 4; $i++) if ($i + $j < $count_a) $key[$i] = $a[$i + $j];
			$pkey = aes_cbc_encrypt_a32($pkey, $key);
		}
	}
	return $pkey;
}
function encrypt_key($a, $key) {
	$x = array();
	for ($i = 0; $i < count($a); $i += 4) $x = array_merge($x, aes_cbc_encrypt_a32(array_slice($a, $i, 4), $key));
	return $x;
}
function decrypt_key($a, $key) {
	$x = array();
	for ($i = 0; $i < count($a); $i += 4) $x = array_merge($x, aes_cbc_decrypt_a32(array_slice($a, $i, 4), $key));
	return $x;
}
function mpi2bc($s) {
	$s = bin2hex(substr($s, 2));
	$len = strlen($s);
	$n = 0;
	for ($i = 0; $i < $len; $i++) $n = bcadd($n, bcmul(hexdec($s[$i]), bcpow(16, $len - $i - 1)));
	return $n;
}
function bin2int($str) {
	$result = 0;
	$n = strlen($str);
	do {
		$result = bcadd(bcmul($result, 256), ord($str[--$n]));
	} while ($n > 0);
	return $result;
}
function int2bin($num) {
	$result = '';
	do {
		$result .= chr(bcmod($num, 256));
		$num = bcdiv($num, 256);
	} while (bccomp($num, 0));
	return $result;
}
function bitOr($num1, $num2, $start_pos) {
	$start_byte = intval($start_pos / 8);
	$start_bit = $start_pos % 8;
	$tmp1 = int2bin($num1);
	$num2 = bcmul($num2, 1 << $start_bit);
	$tmp2 = int2bin($num2);
	if ($start_byte < strlen($tmp1)) {
		$tmp2 |= substr($tmp1, $start_byte);
		$tmp1 = substr($tmp1, 0, $start_byte) . $tmp2;
	} else $tmp1 = str_pad($tmp1, $start_byte, "\0") . $tmp2;
	return bin2int($tmp1);
}
function bitLen($num) {
	$tmp = int2bin($num);
	$bit_len = strlen($tmp) * 8;
	$tmp = ord($tmp[strlen($tmp) - 1]);
	if (!$tmp) $bit_len -= 8;
	else while (!($tmp & 0x80)) {
		$bit_len--;
		$tmp <<= 1;
	}
	return $bit_len;
}
function rsa_decrypt($enc_data, $p, $q, $d) {
	$enc_data = int2bin($enc_data);
	$exp = $d;
	$modulus = bcmul($p, $q);
	$data_len = strlen($enc_data);
	$chunk_len = bitLen($modulus) - 1;
	$block_len = intval(ceil($chunk_len / 8));
	$curr_pos = 0;
	$bit_pos = 0;
	$plain_data = 0;
	while ($curr_pos < $data_len) {
		$tmp = bin2int(substr($enc_data, $curr_pos, $block_len));
		$tmp = bcpowmod($tmp, $exp, $modulus);
		$plain_data = bitOr($plain_data, $tmp, $bit_pos);
		$bit_pos += $chunk_len;
		$curr_pos += $block_len;
	}
	return int2bin($plain_data);
}

function getRootNode($files = 0) {
	global $T8;
	if (empty($files) || !is_array($files) || count($files) < 1) $files = apiReq(array('a' => 'f', 'c' => 1));
	if (is_numeric($files[0])) check_errors($files[0], 'Cannot get Root folder ID');
	foreach ($files[0]['f'] as $file) if ($file['t'] == 2) {
		$T8['root_id'] = $file['h'];
		break;
	}
	if (empty($T8['root_id'])) html_error('Root folder ID not found.');
}

function getNextChunkLength($len) {
	if ($len < 131072) return 131072;
	elseif ($len < 262144) return 262144;
	elseif ($len < 393216) return 393216;
	elseif ($len < 524288) return 524288;
	elseif ($len < 655360) return 655360;
	elseif ($len < 786432) return 786432;
	elseif ($len < 917504) return 917504;
	else return 1048576;
}

function calcChunkMac($data, $key, $iv) {
	global $m_td1, $m_td2;
	$size = strlen($data);
	if ($size % 16 > 0) {
		$data .= str_repeat("\0", (16 - $size % 16));
		$size = strlen($data);
	}

	$init = mcrypt_generic_init($m_td2, $key, $iv);
	if ($init === false || $init < 0) html_error('Cannot init mcrypt');

	$size -= 16;
	if ($size > 0) mcrypt_generic($m_td2, substr($data, 0, $size));
	return mcrypt_generic($m_td1, mcrypt_generic($m_td2, substr($data, $size)));
}

function FileMac($file, $fsize, $key, $iv) {
	$fs = fopen($file, 'rb');
	$csize = 131072;
	$_data = '';
	$readed = 0;
	$m_td1 = mcrypt_module_open('rijndael-128', '', 'cbc', '');
	$m_td2 = mcrypt_module_open('rijndael-128', '', 'cbc', '');
	$init = mcrypt_generic_init($m_td1, $key, "\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0");
	if ($init === false || $init < 0) html_error('Cannot init mcrypt');
	echo "\n<script type='text/javascript'>document.getElementById('T8').innerHTML = 'CBC-MAC File: 0 %';document.getElementById('T8').style.display = 'block';</script>";
	while (!feof($fs)) {
		$data = fread($fs, $csize);
		if ($data === false) {
			fclose($fs);
			html_error(lang(112));
		}
		if ($_data !== '') {
			$data = $_data . $data;
			$_data = '';
		}
		if (strlen($data) < $csize && !feof($fs)) {
			$_data .= $data;
			continue;
		}
		if (strlen($data) > $csize) {
			$_data .= substr($data, $csize);
			$data = substr($data, 0, $csize);
		}

		$readed += $size = strlen($data);
		if ($size % 16 > 0) {
			$data .= str_repeat("\0", (16 - $size % 16));
			$size = strlen($data);
		}

		$init = mcrypt_generic_init($m_td2, $key, $iv);
		if ($init === false || $init < 0) html_error('Cannot init mcrypt');

		$size -= 16;
		if ($size > 0) mcrypt_generic($m_td2, substr($data, 0, $size));
		$mac_str = mcrypt_generic($m_td1, mcrypt_generic($m_td2, substr($data, $size)));

		if ($csize < 1048576) $csize = getNextChunkLength($csize);
		echo "\n<script type='text/javascript'>document.getElementById('T8').innerHTML = 'CBC-MAC File: ".($readed < $fsize ? round($readed / $fsize * 100, 2) : '100')." %';</script>";
		flush();
	}
	echo "\n<script type='text/javascript'>document.getElementById('T8').innerHTML = 'CBC-MAC File: 100 %';document.getElementById('T8').style.display = 'none';</script>";
	mcrypt_generic_deinit($m_td1);
	mcrypt_module_close($m_td1);
	mcrypt_generic_deinit($m_td2);
	mcrypt_module_close($m_td2);
	fclose($fs);
	return str_to_a32($mac_str);
}

function Login($user, $pass) {
	global $T8;
	if (!extension_loaded('bcmath')) html_error('This plugin needs BCMath extension for login.');
	$password_aes = prepare_key(str_to_a32($pass));
	$T8['user_handle'] = stringhash($user, $password_aes);
	$res = apiReq(array('a' => 'us', 'user' => $user, 'uh' => $T8['user_handle']));
	if (is_numeric($res[0])) check_errors($res[0], 'Cannot login');
	$T8['master_key'] = decrypt_key(base64_to_a32($res[0]['k']), $password_aes);
	$privk = a32_to_str(decrypt_key(base64_to_a32($res[0]['privk']), $T8['master_key']));
	$rsa_priv_key = array(0, 0, 0, 0);
	for ($i = 0; $i < 4; $i++) {
		$l = ((ord($privk[0]) * 256 + ord($privk[1]) + 7) / 8) + 2;
		$rsa_priv_key[$i] = mpi2bc(substr($privk, 0, $l));
		$privk = substr($privk, $l);
	}
	$T8['sid'] = rsa_decrypt(mpi2bc(base64url_decode($res[0]['csid'])), $rsa_priv_key[0], $rsa_priv_key[1], $rsa_priv_key[2]);
	$T8['sid'] = base64url_encode(substr(strrev($T8['sid']), 0, 43));
	getRootNode();
	t8ArrToCookieArr($rsa_priv_key);

	$quota = apiReq(array('a' => 'uq', 'strg' => 1));
	if (!is_array($quota[0])) check_errors($quota[0], 'Cannot get disk quota');
	SaveCookies($user, $pass); // Update cookies file.
	$cookie = '';
	if (($quota[0]['mstrg'] - $quota[0]['cstrg']) < $GLOBALS['fsize']) html_error('Insufficient Free Space in Account for Upload this File.');
}

function IWillNameItLater($cookie, $decrypt=true) {
	if (!is_array($cookie)) {
		if (!empty($cookie)) return $decrypt ? decrypt(urldecode($cookie)) : urlencode(encrypt($cookie));
		return '';
	}
	if (count($cookie) < 1) return $cookie;
	$keys = array_keys($cookie);
	$values = array_values($cookie);
	$keys = $decrypt ? array_map('decrypt', array_map('urldecode', $keys)) : array_map('urlencode', array_map('encrypt', $keys));
	$values = $decrypt ? array_map('decrypt', array_map('urldecode', $values)) : array_map('urlencode', array_map('encrypt', $values));
	return array_combine($keys, $values);
}

function SavedLogin($user, $pass) {
	global $T8, $cookie, $secretkey;
	if (!defined('DOWNLOAD_DIR')) {
		global $options;
		if (substr($options['download_dir'], -1) != '/') $options['download_dir'] .= '/';
		define('DOWNLOAD_DIR', (substr($options['download_dir'], 0, 6) == 'ftp://' ? '' : $options['download_dir']));
	}

	$user = strtolower($user);
	$filename = DOWNLOAD_DIR.basename('mega_ul.php');
	if (!file_exists($filename) || filesize($filename) <= 6) return Login($user, $pass);

	$file = file($filename);
	$savedcookies = unserialize($file[1]);
	unset($file);

	$hash = hash('crc32b', $user.':'.$pass);
	if (is_array($savedcookies) && array_key_exists($hash, $savedcookies)) {
		$_secretkey = $secretkey;
		$secretkey = hash('crc32b', $pass).sha1($user.':'.$pass).hash('crc32b', $user); // A 56 char key should be safer. :D
		$cookie = (decrypt(urldecode($savedcookies[$hash]['enc'])) == 'OK') ? IWillNameItLater($savedcookies[$hash]['cookie']) : '';
		$secretkey = $_secretkey;
		if ((is_array($cookie) && count($cookie) < 1) || empty($cookie)) return Login($user, $pass);

		$T8['sid'] = $cookie['sid'];
		$T8['user_handle'] = $cookie['user_handle'];
		$T8['master_key'] = base64_to_a32($cookie['master_key']);
		$T8['root_id'] = $cookie['root_id'];
		$rsa_priv_key = explode('/T8\\', $cookie['rsa_priv_key']);

		$quota = apiReq(array('a' => 'uq', 'strg' => 1)); // I'm using the 'User quota details' request for validating the session id.
		if (is_numeric($quota[0]) && $quota[0] < 0) {
			if ($quota[0] == -15) { // Session code expired... We need to get a newer one.
				if (!extension_loaded('bcmath')) html_error('This plugin needs BCMath extension for login.');
				$T8['sid'] = false; // Do not send old sid or it will get '-15' error.
				$res = apiReq(array('a' => 'us', 'user' => $user, 'uh' => $T8['user_handle']));
				if (is_numeric($res[0])) check_errors($res[0], 'Cannot re-login');
				$T8['sid'] = rsa_decrypt(mpi2bc(base64url_decode($res[0]['csid'])), $rsa_priv_key[0], $rsa_priv_key[1], $rsa_priv_key[2]);
				$T8['sid'] = base64url_encode(substr(strrev($T8['sid']), 0, 43));
				t8ArrToCookieArr();
			} else check_errors($quota[0], 'Cannot validate saved-login');
		}
		SaveCookies($user, $pass); // Update last used time.
		$cookie = '';
		if (($quota[0]['mstrg'] - $quota[0]['cstrg']) < $GLOBALS['fsize']) html_error('Insufficient Free Space in Account for Upload this File');
		return;
	}
	return Login($user, $pass);
}

function t8ArrToCookieArr($rsa_priv_key = 0) {
	global $cookie, $T8;
	if (empty($cookie) || !is_array($cookie)) $cookie = array();
	$cookie['sid'] = $T8['sid'];
	$cookie['user_handle'] = $T8['user_handle'];
	$cookie['master_key'] = a32_to_base64($T8['master_key']);
	$cookie['root_id'] = $T8['root_id'];
	if (!empty($rsa_priv_key) && is_array($rsa_priv_key) && count($rsa_priv_key) > 2) $cookie['rsa_priv_key'] = implode('/T8\\', array_slice($rsa_priv_key, 0, 3)); // For decrypt the SID we need only the first 3 elements of the priv key, so we will only save those 3 elements.
}

function SaveCookies($user, $pass) {
	global $cookie, $secretkey;
	$maxdays = 30; // Max days to keep cookies for more than 1 user.
	$filename = DOWNLOAD_DIR.basename('mega_ul.php');
	if (file_exists($filename) && filesize($filename) > 6) {
		$file = file($filename);
		$savedcookies = unserialize($file[1]);
		unset($file);

		// Remove old cookies
		if (is_array($savedcookies)) {
			foreach ($savedcookies as $k => $v) if (time() - $v['time'] >= ($maxdays * 24 * 60 * 60)) unset($savedcookies[$k]);
		} else $savedcookies = array();
	} else $savedcookies = array();
	$hash = hash('crc32b', $user.':'.$pass);
	$_secretkey = $secretkey;
	$secretkey = hash('crc32b', $pass).sha1($user.':'.$pass).hash('crc32b', $user); // A 56 char key should be safer. :D
	$savedcookies[$hash] = array('time' => time(), 'enc' => urlencode(encrypt('OK')), 'cookie' => IWillNameItLater($cookie, false));
	$secretkey = $_secretkey;

	write_file($filename, "<?php exit(); ?>\r\n" . serialize($savedcookies));
}

function chunk_ul($scheme, $host, $port, $url, $onlyOpen = false) {
	global $nn, $pauth, $fp, $errno, $errstr, $fsize, $pbChunkSize, $data, $zapros;
	if ($scheme == 'https://') {
		$scheme = 'tls://';
		$port = 443;
	}

	if (!empty($_GET['proxy'])) {
		$proxy = true;
		list($proxyHost, $proxyPort) = explode(':', $_GET['proxy'], 2);
		$host = $host . ($port != 80 && ($scheme != 'tls://' || $port != 443) ? ':' . $port : '');
		$url = $scheme . $host . $url;
	} else $proxy = false;

	if ($scheme != 'tls://') $scheme = '';
	$request = array();
	$request[] = 'POST ' . str_replace(' ', '%20', $url) . ' HTTP/1.0';
	$request[] = "Host: $host";
	$request[] = 'User-Agent: '. (defined('rl_UserAgent') ? rl_UserAgent : 'Opera/9.80 (Windows NT 6.1) Presto/2.12.388 Version/12.17');
	$request[] = 'Accept: */*';
	$request[] = 'Accept-Language: en-US;q=0.7,en;q=0.3';
	$request[] = 'Accept-Charset: utf-8,windows-1251;q=0.7,*;q=0.7';
	$request[] = 'Content-Type: application/octet-stream';
	$request[] = "Content-Length: ".($onlyOpen ? $fsize : strlen($data));
	if ($proxy && !empty($pauth)) $request[] = "Proxy-Authorization: Basic $pauth\r\n";
	$request[] = 'Connection: Close';

	$zapros = implode("\r\n", $request) . "\r\n\r\n";
	$errno = 0; $errstr = '';
	$posturl = (!empty($proxyHost) ? $scheme . $proxyHost : $scheme . $host) . ':' . (!empty($proxyPort) ? $proxyPort : $port);
	$fp = @stream_socket_client($posturl, $errno, $errstr, 120, STREAM_CLIENT_CONNECT);

	if (!$fp) {
		$dis_host = $proxy ? $proxyHost : $host;
		$dis_port = $proxy ? $proxyPort : $port;
		html_error(sprintf(lang(88), $dis_host, $dis_port));
	} elseif ($errno || $errstr) html_error($errstr);

	if (!@fputs($fp, $zapros)) html_error('Cannot send request headers.');
	fflush($fp);

	require_once(TEMPLATE_DIR . '/uploadui.php');
	echo "\n<script type='text/javascript'>document.getElementById('ul_con').innerHTML ='".($proxy ? (sprintf(lang(89), $proxyHost, $proxyPort) . "<br />'UPLOAD: <b>$url</b>...<br />") : sprintf(lang(90), $host, $port))."';document.getElementById('ul_fname').style.display = 'block';</script>";
	flush();

	if ($onlyOpen) return;
	global $timeStart, $totalsend, $time, $lastChunkTime;
	$dlen = strlen($data);
	$sended = 0;
	for ($s = 0; $s < ($dlen - 1); $s += $pbChunkSize) {
		$chunk = ($pbChunkSize >= ($dlen - $s)) ? substr($data, $s) : substr($data, $s, $pbChunkSize);
		$sendbyte = @fputs($fp, $chunk);
		fflush($fp);

		if ($sendbyte === false || strlen($chunk) > $sendbyte) {
			fclose($fp);
			html_error(lang(113));
		}

		$totalsend += $sendbyte;
		$sended += $sendbyte;

		$time = getmicrotime() - $timeStart;
		$chunkTime = $time - $lastChunkTime;
		if (($s + $sendbyte) <= ($dlen - 1) && $chunkTime < 1) continue;
		$chunkTime = (!($chunkTime < 0) && $chunkTime > 0) ? $chunkTime : 1;
		$lastChunkTime = $time;
		$speed = round($sended / 1024 / $chunkTime, 2);
		$percent = round($totalsend / $fsize * 100, 2);
		echo "<script type='text/javascript'>pr('$percent', '" . bytesToKbOrMbOrGb($totalsend) . "', '$speed');</script>\n";
		flush();
		$sended = 0;
	}
	if ($errno || $errstr) html_error($errstr);
	fflush($fp);

	$page = '';
	while (!feof($fp)) {
		$data = fgets($fp, 16384);
		if ($data === false) break;
		$page .= $data;
	}

	fclose($fp);
	$body = substr($page, strpos($page, "\r\n\r\n") + 4);
	if (is_numeric($body) && $body < 0) check_errors($body, 'Error while uploading chunk');
	return $page;
}

function T8_mega_upload($link, $ul_key, $file, $filename, &$mac_str = '') {
	global $nn, $fp, $fs, $errno, $errstr, $fsize, $pbChunkSize, $T8, $chunk_UL, $calcMacEachChunk, $zapros;
	$pbChunkSize = GetChunkSize($fsize);
	$_link = parse_url($link);
	$scheme = $_link['scheme'] . '://';
	$host = $_link['host'];
	$port = defport($_link);
	$url = $_link['path']. (!empty($_link['query']) ? '?'.$_link['query'] : '');
	unset($_link);
	$key = a32_to_str(array_slice($ul_key, 0, 4));

	$_td = mcrypt_module_open('rijndael-128', '', 'ctr', '');
	$init = mcrypt_generic_init($_td, $key, a32_to_str(array($ul_key[4], $ul_key[5], 0, 0)));
	if ($init === false || $init < 0) html_error('Cannot init mcrypt');

	if (!is_readable($file)) html_error(sprintf(lang(65), $file));

	echo "\n<p id='ul_con'></p>\n<p id='ul_fname' style='display:none'>" . lang(104) . " <b>$filename</b>, " . lang(56) . ' <b>' . bytesToKbOrMbOrGb($fsize) . "</b>...<br /></p><p id='T8' style='display:none'></p>\n";
	flush();

	if ($chunk_UL) global $chunkSize, $timeStart, $data, $totalsend, $time, $lastChunkTime;
	else chunk_ul($scheme, $host, $port, $url, true);

	$fs = fopen($file, 'rb');
	$chunkSize = 131072;
	$totalsend = $time = $lastChunkTime = 0;
	$_data = '';
	if ($calcMacEachChunk) {
		global $m_td1, $m_td2;
		$m_td1 = mcrypt_module_open('rijndael-128', '', 'cbc', '');
		$m_td2 = mcrypt_module_open('rijndael-128', '', 'cbc', '');
		$init = mcrypt_generic_init($m_td1, $key, "\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0");
		if ($init === false || $init < 0) html_error('Cannot init mcrypt');
		$c_mac = a32_to_str(array($ul_key[4], $ul_key[5], $ul_key[4], $ul_key[5]));
	}

	$timeStart = microtime(true);

	while (!feof($fs) && !$errno && !$errstr) {
		$data = fread($fs, $chunkSize);
		if ($data === false) {
			fclose($fs);
			if (!$chunk_UL) fclose($fp);
			html_error(lang(112));
		}
		if ($_data !== '') {
			$data = $_data . $data;
			$_data = '';
		}
		if (strlen($data) < $chunkSize && !feof($fs)) {
			$_data .= $data;
			continue;
		}
		if (strlen($data) > $chunkSize) {
			$_data .= substr($data, $chunkSize);
			$data = substr($data, 0, $chunkSize);
		}
		if ($calcMacEachChunk) $mac_str = calcChunkMac($data, $key, $c_mac);
		if ($chunkSize < 1048576) $chunkSize = getNextChunkLength($chunkSize);

		$data = mcrypt_generic($_td, $data);
		if ($chunk_UL) $page = chunk_ul($scheme, $host, $port, "$url/$totalsend-" . (($totalsend + strlen($data)) - 1));
		else {
			$dlen = strlen($data);
			$sended = 0;
			for ($s = 0; $s < ($dlen - 1); $s += $pbChunkSize) {
				$chunk = ($pbChunkSize >= ($dlen - $s)) ? substr($data, $s) : substr($data, $s, $pbChunkSize);
				$sendbyte = @fputs($fp, $chunk);
				fflush($fp);

				if ($sendbyte === false || strlen($chunk) > $sendbyte) {
					fclose($fs);
					fclose($fp);
					html_error(lang(113));
				}

				$totalsend += $sendbyte;
				$sended += $sendbyte;

				$time = getmicrotime() - $timeStart;
				$chunkTime = $time - $lastChunkTime;
				if (($s + $sendbyte) <= ($dlen - 1) && $chunkTime < 1) continue;
				$chunkTime = (!($chunkTime < 0) && $chunkTime > 0) ? $chunkTime : 1;
				$lastChunkTime = $time;
				$speed = round($sended / 1024 / $chunkTime, 2);
				$percent = round($totalsend / $fsize * 100, 2);
				echo "<script type='text/javascript'>pr('$percent', '" . bytesToKbOrMbOrGb($totalsend) . "', '$speed');</script>\n";
				flush();
				$sended = 0;
			}
		}
	}
	mcrypt_generic_deinit($_td);
	mcrypt_module_close($_td);
	if ($errno || $errstr) {
		$lastError = $errstr;
		return false;
	}

	if (!$chunk_UL) {
		fflush($fp);
		$page = '';
		while (!feof($fp)) {
			$data = fgets($fp, 16384);
			if ($data === false) break;
			$page .= $data;
		}
		fclose($fp);
	}
	fclose($fs);
	return $page;
}

//[23-7-2013] Written by Th3-822.
//[30-1-2014] Ephemeral account support removed, mega is not allowing anon users to generate public links. - Th3-822
//[15-4-2014] Fixed re-login error. - Th3-822
//[19-5-2016] Using OpenSSL where is possible for better login speed & Added free space check. - Th3-822

?>