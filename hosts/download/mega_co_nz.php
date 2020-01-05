<?php
if (!defined('RAPIDLEECH')) {
	require_once('index.html');
	exit;
}
// Using functions from: http://julien-marchand.fr/blog/using-the-mega-api-with-php-examples/
class mega_co_nz extends DownloadClass {
	private $useOpenSSL, $useOldFilter, $seqno, $cookie;
	public function Download($link) {
		$this->checkCryptDependences();
		$this->checkBug78902($link);

		$this->seqno = mt_rand();
		$this->changeMesg(lang(300).'<br />Mega.co.nz plugin by Th3-822'); // Please, do not remove or change this line contents. - Th3-822

		$fragment = parse_url($link, PHP_URL_FRAGMENT);
		if (preg_match('@^F!([^!]{8})!([\w\-\,]{22})(?:!([^!#]{8}))?(!less$)?@i', $fragment, $fid)) return $this->Folder($fid[1], $fid[2], (!empty($fid[3]) && $fid[3] != $fid[1] ? $fid[3] : 0), (empty($fid[4]) ? 1 : 0));
		if (!preg_match('@^(T8|N)?!([^!]{8})!([\w\-\,]{43})(?:(?:!|=###n=)([^!#]{8})(?:!|$))?@i', $fragment, $fid)) html_error('FileID or Key not found at link.');

		$pA = (empty($_REQUEST['premium_user']) || empty($_REQUEST['premium_pass']) ? false : true);
		if (!empty($_REQUEST['premium_acc']) && $_REQUEST['premium_acc'] == 'on' && ($pA || (!empty($GLOBALS['premium_acc']['mega_co_nz']['user']) && !empty($GLOBALS['premium_acc']['mega_co_nz']['pass'])))) {
			$user = ($pA ? $_REQUEST['premium_user'] : $GLOBALS['premium_acc']['mega_co_nz']['user']);
			$pass = ($pA ? $_REQUEST['premium_pass'] : $GLOBALS['premium_acc']['mega_co_nz']['pass']);
			if ($pA && !empty($_POST['pA_encrypted'])) {
				$user = decrypt(urldecode($user));
				$pass = decrypt(urldecode($pass));
				unset($_POST['pA_encrypted']);
			}
		}

		do {
			$reply = $this->apiReq(array('a' => 'g', 'g' => 1, (empty($fid[1]) ? 'p' : 'n') => $fid[2], 'ssl' => 0), (!empty($fid[1]) && !empty($fid[4]) ? $fid[4] : ''));
			if (is_numeric($reply[0])) $this->CheckErr($reply[0]);
			if (!empty($reply[0]['e']) && is_numeric($reply[0]['e'])) $this->CheckErr($reply[0]['e']);
			$tLimit = $this->checkTrafficLimit($reply[0]['g']);
		} while (!empty($user) && !empty($pass) && empty($this->cookie['sid']) && $tLimit && $this->cJar_load($user, $pass));

		if ($tLimit) {
			if (empty($this->cookie['sid'])) html_error('Anonymous Traffic Limit Reached, add an account then try again.');
			else html_error('Traffic Limit Reached.');
		}

		$key = $this->base64_to_a32($fid[3]);
		$key = array($key[0] ^ $key[4], $key[1] ^ $key[5], $key[2] ^ $key[6], $key[3] ^ $key[7]);
		$attr = $this->dec_attr($this->base64url_decode($reply[0]['at']), $key);
		if (empty($attr)) html_error((!empty($fid[1]) ? 'Folder Error: ' : '').'File\'s key isn\'t correct.');

		$this->RedirectDownload($reply[0]['g'], $attr['n'], 0, 0, $link, 0, 0, array('T8[fkey]' => $fid[3]));
	}

	private function checkBug78902($link = '') {
		// >= 7.4, >=7.3.11 & >= 7.2.24
		if (substr(PHP_OS, 0, 3) == 'WIN' || version_compare(PHP_VERSION, '7.2.24', '<') || version_compare(PHP_VERSION, '7.3.11', '<')) return;

		if (empty($link)) echo('<div id="mesg" width="100%" align="center"></div><br />');
		$this->changeMesg('Warning: Running on PHP v' . PHP_VERSION . '<br />Downloads with this PHP release may be affected by the bug <a href="https://bugs.php.net/bug.php?id=78902">#78902</a> and will leak RAM until exhausted.<br />File may stop at a random size (up to ~1 GB).');

		if (!empty($_GET['method']) && $_GET['method'] == '78902') return;
		if (!empty($link)) {
			$form = $this->DefaultParamArr($link);
			$form['method'] = '78902';

			echo "\n<form name='f78902' action='{$_SERVER['SCRIPT_NAME']}' method='POST'>\n";
			foreach ($form as $name => $input) echo "\t<input type='hidden' name='$name' id='$name' value='" . htmlspecialchars($input, ENT_QUOTES) . "' />\n";
			echo "\t<div><br /><input type='submit' value='Continue' /></div></form>\n";
			include(TEMPLATE_DIR.'footer.php');
			exit();
		}
	}

	private function checkCryptDependences() {
		$this->useOpenSSL = (version_compare(PHP_VERSION, '5.4.0', '>=') && extension_loaded('openssl') && in_array('AES-128-CBC', ($ossl_ciphers = openssl_get_cipher_methods()), true));

		if (!$this->useOpenSSL || !in_array('AES-128-CTR', $ossl_ciphers, true))
		{
			$this->useOldFilter = true;
			if (!extension_loaded('mcrypt') || !in_array('rijndael-128', mcrypt_list_algorithms(), true)) html_error("OpenSSL / Mcrypt module isn't installed or it doesn't have support for the needed encryption.");
		} else $this->useOldFilter = false;
	}

	// It's seems to not work properly with a HEAD request neither with a GET with a small Range (TODO: Add here a small copy of geturl that stops after getting the headers or add a parameter to geturl that does that)
	private function checkTrafficLimit($link) {
		if (extension_loaded('curl') && function_exists('curl_init') && function_exists('curl_exec')) {
			$page = cURL($link, 0, 0, 0, 0, array(CURLOPT_NOBODY => true));
		} else if (function_exists('readCustomHeaders')) {
			$page = $this->GetPage($link, 0, 0, "https://mega.nz/\r\n:HEAD " . parse_url($link, PHP_URL_PATH) . " HTTP/1.1");
		} else html_error('Your rapidleech is outdated and doesn\'t support a required check.');
		return (intval(substr($page, 9, 3)) == 509);
	}

	private function CheckErr($code, $prefix = 'Error') {
		$isLogin = (stripos($prefix, 'login') !== false);
		switch ($code) {
			default: $msg = '*No message for this error*';break;
			case -1: $msg = 'An internal error has occurred';break;
			case -2: $msg = 'You have passed invalid arguments to this command, your rapidleech is outdated?';break;
			case -3: $msg = 'A temporary congestion or server malfunction prevented your request from being processed';break;
			case -4: $msg = 'You have exceeded your command weight per time quota. Please wait a few seconds, then try again';break;
			case -9: $msg = ($isLogin ? 'Email/Password incorrect' : 'File/Folder not found');break;
			case -11: $msg = 'Access violation';break;
			case -13: $msg = ($isLogin ? 'Account not Activated yet' : 'Trying to access an incomplete file');break;
			case -14: $msg = 'A decryption operation failed';break;
			case -15: $msg = 'Invalid or expired user session, please relogin';break;
			case -16: $msg = ($isLogin ? 'Account blocked' : 'File/Folder not available, uploader\'s account is banned');break;
			case -17: $msg = 'Request over quota';break;
			case -18: $msg = ($isLogin ? 'Login service' : 'File/Folder') . ' temporarily not available, please try again later';break;
			// Confirmed at page:
			case -6: $msg = 'File not found, account was deleted';break;
		}
		html_error("$prefix: [$code] $msg.");
	}

	private function apiReq($atrr, $node = '') {
		$try = 0;
		do {
			if ($try > 0) sleep(2);
			$ret = $this->doApiReq($atrr, $node);
			$try++;
		} while ($try < 6 && $ret[0] == -3);
		return $ret;
	}

	private function doApiReq($atrr, $node='') {
		if (!function_exists('json_encode')) html_error('Error: Please enable JSON in php.');
		$page = $this->GetPage('https://g.api.mega.co.nz/cs?id=' . ($this->seqno++) . (!empty($node) ? "&n=$node" : '') . (!empty($this->cookie['sid']) ? "&sid={$this->cookie['sid']}" : ''), 0, json_encode((!empty($atrr[0]) && is_array($atrr[0])) ? $atrr : array($atrr)), "https://mega.nz/\r\nContent-Type: application/json");
		if (in_array(intval(substr($page, 9, 3)), array(500, 503))) return array(-3); //  500 Server Too Busy
		list ($header, $page) = array_map('trim', explode("\r\n\r\n", $page, 2));
		if (is_numeric($page)) return array(intval($page));
		return $this->json2array($page);
	}

	private function str_to_a32($b) {
		// Add padding, we need a string with a length multiple of 4
		$b = str_pad($b, 4 * ceil(strlen($b) / 4), "\0");
		return array_values(unpack('N*', $b));
	}

	private function a32_to_str($hex) {
		return call_user_func_array('pack', array_merge(array('N*'), $hex));
	}

	private function base64url_encode($data) {
		return strtr(rtrim(base64_encode($data), '='), '+/', '-_');
	}

	private function a32_to_base64($a) {
		return $this->base64url_encode($this->a32_to_str($a));
	}

	private function base64url_decode($data) {
		if (($s = (2 - strlen($data) * 3) % 4) < 2) $data .= substr(',,', $s);
		return base64_decode(strtr($data, '-_,', '+/='));
	}

	private function base64_to_a32($s) {
		return $this->str_to_a32($this->base64url_decode($s));
	}

	private function aes_cbc_encrypt($data, $key) {
		if ($this->useOpenSSL) {
			$data = str_pad($data, 16 * ceil(strlen($data) / 16), "\0"); // OpenSSL needs this padded.
			return openssl_encrypt($data, 'AES-128-CBC', $key, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING, "\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0");
		} else return mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $data, MCRYPT_MODE_CBC, "\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0");
	}

	private function aes_cbc_decrypt($data, $key) {
		if ($this->useOpenSSL) {
			$data = str_pad($data, 16 * ceil(strlen($data) / 16), "\0"); // OpenSSL needs this padded.
			return openssl_decrypt($data, 'AES-128-CBC', $key, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING, "\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0");
		} else return mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $data, MCRYPT_MODE_CBC, "\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0");
	}

	private function aes_cbc_encrypt_a32($data, $key) {
		return $this->str_to_a32($this->aes_cbc_encrypt($this->a32_to_str($data), $this->a32_to_str($key)));
	}

	private function aes_cbc_decrypt_a32($data, $key) {
		return $this->str_to_a32($this->aes_cbc_decrypt($this->a32_to_str($data), $this->a32_to_str($key)));
	}

	private function stringhash($s, $aeskey) {
		$s32 = $this->str_to_a32($s);
		$h32 = array(0, 0, 0, 0);
		for ($i = 0; $i < count($s32); $i++) $h32[$i % 4] ^= $s32[$i];
		for ($i = 0; $i < 0x4000; $i++) $h32 = $this->aes_cbc_encrypt_a32($h32, $aeskey);
		return $this->a32_to_base64(array($h32[0], $h32[2]));
	}

	private function prepare_key($a) {
		$pkey = array(0x93C467E3, 0x7DB0C7A4, 0xD1BE3F81, 0x0152CB56);
		$count_a = count($a);
		for ($r = 0; $r < 0x10000; $r++) {
			for ($j = 0; $j < $count_a; $j += 4) {
				$key = array(0, 0, 0, 0);
				for ($i = 0; $i < 4; $i++) if ($i + $j < $count_a) $key[$i] = $a[$i + $j];
				$pkey = $this->aes_cbc_encrypt_a32($pkey, $key);
			}
		}
		return $pkey;
	}

	private function decrypt_key($a, $key) {
		$x = array();
		for ($i = 0; $i < count($a); $i += 4) $x = array_merge($x, $this->aes_cbc_decrypt_a32(array_slice($a, $i, 4), $key));
		return $x;
	}

	private function mpi2bc($s) {
		$s = bin2hex(substr($s, 2));
		$len = strlen($s);
		$n = 0;
		for ($i = 0; $i < $len; $i++) $n = bcadd($n, bcmul(hexdec($s[$i]), bcpow(16, $len - $i - 1)));
		return $n;
	}

	private function bin2int($str) {
		$result = 0;
		$n = strlen($str);
		do {
			$result = bcadd(bcmul($result, 256), ord($str[--$n]));
		} while ($n > 0);
		return $result;
	}

	private function int2bin($num) {
		$result = '';
		do {
			$result .= chr(bcmod($num, 256));
			$num = bcdiv($num, 256);
		} while (bccomp($num, 0));
		return $result;
	}

	private function bitOr($num1, $num2, $start_pos) {
		$start_byte = intval($start_pos / 8);
		$start_bit = $start_pos % 8;
		$tmp1 = $this->int2bin($num1);
		$num2 = bcmul($num2, 1 << $start_bit);
		$tmp2 = $this->int2bin($num2);
		if ($start_byte < strlen($tmp1)) {
			$tmp2 |= substr($tmp1, $start_byte);
			$tmp1 = substr($tmp1, 0, $start_byte) . $tmp2;
		} else $tmp1 = str_pad($tmp1, $start_byte, "\0") . $tmp2;
		return $this->bin2int($tmp1);
	}

	private function bitLen($num) {
		$tmp = $this->int2bin($num);
		$bit_len = strlen($tmp) * 8;
		$tmp = ord($tmp[strlen($tmp) - 1]);
		if (!$tmp) $bit_len -= 8;
		else while (!($tmp & 0x80)) {
			$bit_len--;
			$tmp <<= 1;
		}
		return $bit_len;
	}

	private function rsa_decrypt($enc_data, $p, $q, $d) {
		$enc_data = $this->int2bin($enc_data);
		$exp = $d;
		$modulus = bcmul($p, $q);
		$data_len = strlen($enc_data);
		$chunk_len = $this->bitLen($modulus) - 1;
		$block_len = intval(ceil($chunk_len / 8));
		$curr_pos = 0;
		$bit_pos = 0;
		$plain_data = 0;
		while ($curr_pos < $data_len) {
			$tmp = $this->bin2int(substr($enc_data, $curr_pos, $block_len));
			$tmp = bcpowmod($tmp, $exp, $modulus);
			$plain_data = $this->bitOr($plain_data, $tmp, $bit_pos);
			$bit_pos += $chunk_len;
			$curr_pos += $block_len;
		}
		return $this->int2bin($plain_data);
	}

	private function dec_attr($attr, $key) {
		$attr = trim($this->aes_cbc_decrypt($attr, $this->a32_to_str($key)));
		if (substr($attr, 0, 6) != 'MEGA{"') return false;
		$attr = substr($attr, 4);$attr = substr($attr, 0, strrpos($attr, '}') + 1);
		return $this->json2array($attr);
	}

	public function CheckBack($header) {
		$this->checkBug78902();
		if (($statuscode = intval(substr($header, 9, 3))) != 200) {
			switch ($statuscode) {
				case 509: html_error('[Mega_co_nz] Transfer quota exceeded.');
				case 503: html_error('[Mega_co_nz] Too many connections for this download.');
				case 403: html_error('[Mega_co_nz] Link used/expired.');
				case 404: html_error('[Mega_co_nz] Link expired.');
				default : html_error('[Mega_co_nz][HTTP] '.trim(substr($header, 9, strpos($header, "\n") - 8)));
			}
		}

		global $fp, $sFilters;
		if (empty($fp) || !is_resource($fp)) html_error("Error: Your rapidleech version is outdated and it doesn't support this plugin.");
		$this->checkCryptDependences();
		if (!empty($_GET['T8']['fkey'])) $key = $this->base64_to_a32(urldecode($_GET['T8']['fkey']));
		elseif (preg_match('@^(T8|N)?!([^!]{8})!([\w\-\,]{43})@i', parse_url($_GET['referer'], PHP_URL_FRAGMENT), $dat)) $key = $this->base64_to_a32($dat[2]);
		else html_error("[CB] File's key not found.");
		$iv = array_merge(array_slice($key, 4, 2), array(0, 0));
		$key = array($key[0] ^ $key[4], $key[1] ^ $key[5], $key[2] ^ $key[6], $key[3] ^ $key[7]);
		$opts = array('iv' => $this->a32_to_str($iv), 'key' => $this->a32_to_str($key));

		if (!stream_filter_register('MegaDlDecrypt', ($this->useOldFilter ? 'Th3822_MegaDlDecrypt_Old' : 'Th3822_MegaDlDecrypt')) && !in_array('MegaDlDecrypt', stream_get_filters())) html_error('Error: Cannot register "MegaDlDecrypt" filter.');

		if (!isset($sFilters) || !is_array($sFilters)) $sFilters = array();
		if (empty($sFilters['MegaDlDecrypt'])) $sFilters['MegaDlDecrypt'] = stream_filter_append($fp, 'MegaDlDecrypt', STREAM_FILTER_READ, $opts);
		if (!$sFilters['MegaDlDecrypt']) html_error('Error: Unknown error while initializing MegaDlDecrypt filter, cannot continue download.');
	}

	private function FSort($a, $b) {
		return strcmp($a['n'], $b['n']);
	}

	private function Folder($fnid, $fnk, $sfolder, $recursive) {
		$files = $this->apiReq(array('a' => 'f', 'c' => 1, 'r' => (!empty($sfolder) || $recursive ? 1 : 0)), $fnid);
		if (is_numeric($files[0])) $this->CheckErr($files[0], 'Cannot get folder contents');
		$sfolder = (!empty($sfolder) ? array($sfolder => 1) : array());

		foreach ($files[0]['f'] as $file) {
			switch ($file['t']) {
				case 0: // File
					if (!empty($sfolder) && empty($sfolder[$file['p']])) break;
					$keys = array();
					foreach (explode('/', $file['k']) as $key) if (strpos($key, ':') !== false && $key = explode(':', $key, 2)) $keys[$key[0]] = $key[1];
					if (empty($keys)) {
						$key = $this->base64_to_a32($fnk);
						$attr = $this->dec_attr($this->base64url_decode($file['a']), array($key[0] ^ $key[4], $key[1] ^ $key[5], $key[2] ^ $key[6], $key[3] ^ $key[7]));
						if (!empty($attr)) textarea($attr);
						break;
					}
					$key = $this->decrypt_key($this->base64_to_a32(reset($keys)), $this->base64_to_a32($fnk));
					if (empty($key)) break;
					$attr = $this->dec_attr($this->base64url_decode($file['a']), array($key[0] ^ $key[4], $key[1] ^ $key[5], $key[2] ^ $key[6], $key[3] ^ $key[7]));
					if (!empty($attr)) $dfiles[$file['h']] = array('k' => $this->a32_to_base64($key), 'n' => $attr['n'], 'p' => $file['p']);
					break;
				case 1: // Folder
					if (!empty($sfolder) && $recursive && !empty($sfolder[$file['p']])) $sfolder[$file['h']] = 1;
					break;
			}
		}

		if (empty($dfiles)) html_error('Error while decoding folder: Empty'.(!empty($sfolder) ? ' or Inexistent Sub-' : ' ').'Folder? [Subfolders: '.(!empty($sfolder) || $recursive ? 'Yes' : 'No').']');
		uasort($dfiles, array($this, 'FSort'));

		$files = array();
		foreach ($dfiles as $file => $key) $files[] = "https://mega.nz/#N!$file!{$key['k']}!$fnid!Rapidleech";
		$this->moveToAutoDownloader($files);
	}

	private function cJar_encrypt($data, $key = 0) {
		if (empty($data)) return false;
		if (!empty($key)) {
			global $secretkey;
			$_secretkey = $secretkey;
			$secretkey = $key;
		}
		if (is_array($data)) {
			$data = array_combine(array_map('base64_encode', array_map('encrypt', array_keys($data))), array_map('base64_encode', array_map('encrypt', array_values($data))));
		} else {
			$data = base64_encode(encrypt($data));
		}
		if (!empty($key)) $secretkey = $_secretkey;
		return $data;
	}

	private function cJar_decrypt($data, $key = 0) {
		if (empty($data)) return false;
		if (!empty($key)) {
			global $secretkey;
			$_secretkey = $secretkey;
			$secretkey = $key;
		}
		if (is_array($data)) {
			$data = array_combine(array_map('decrypt', array_map('base64_decode', array_keys($data))), array_map('decrypt', array_map('base64_decode', array_values($data))));
		} else {
			$data = decrypt(base64_decode($data));
		}
		if (!empty($key)) $secretkey = $_secretkey;
		return $data;
	}

	private function cJar_load($user, $pass, $filename = 'mega_dl.php') {
		if (empty($user) || empty($pass)) html_error('Login Failed: User or Password is empty.');

		$user = strtolower($user);
		$filename = DOWNLOAD_DIR . basename($filename);
		if (!file_exists($filename) || !($savedcookies = file($filename)) || !is_array($savedcookies = unserialize($savedcookies[1]))) return $this->Login($user, $pass);

		$hash = sha1("$user$pass");
		if (array_key_exists($hash, $savedcookies)) {
			$key = substr(base64_encode(hash('sha512', "$user$pass", true)), 0, 56); // 56 chars cropped base64 encoded key to avoid blowfish issues with \0
			$testCookie = ($this->cJar_decrypt($savedcookies[$hash]['enc'], $key) == 'OK') ? $this->cJar_decrypt($savedcookies[$hash]['cookie'], $key) : 0;
			if (!empty($testCookie)) return $this->cJar_test($user, $pass, $testCookie, true);
		}
		return $this->Login($user, $pass);
	}

	private function cJar_test($user, $pass, $cookie, $preLogin = false) {
		$this->cookie = array('sid' => $cookie['sid']);
		$quota = $this->apiReq(array('a' => 'uq')); // I'm using the 'User quota details' request for validating the session id.
		if (is_numeric($quota[0]) && $quota[0] < 0) {
			if ($quota[0] == -15) { // Session code expired... We need to get a newer one.
				if (!extension_loaded('bcmath')) html_error('This plugin needs BCMath extension for re-login.');
				$this->cookie['sid'] = $cookie['sid'] = false; // Do not send old sid or it will get '-15' error.
				$res = $this->apiReq(array('a' => 'us', 'user' => $user, 'uh' => $cookie['user_handle']));
				if (is_numeric($res[0])) $this->CheckEr($res[0], 'Cannot re-login');
				$rsa_priv_key = explode('/T8\\', $cookie['rsa_priv_key']);
				$cookie['sid'] = $this->base64url_encode(substr(strrev($this->rsa_decrypt($this->mpi2bc($this->base64url_decode($res[0]['csid'])), $rsa_priv_key[0], $rsa_priv_key[1], $rsa_priv_key[2])), 0, 43));
			} else $this->CheckEr($quota[0], 'Cannot validate saved-login');
		}
		$this->cookie = $cookie;
		$this->cJar_save($user, $pass); // Update last used time.
		return true;
	}

	private function cJar_save($user, $pass, $filename = 'mega_dl.php') {
		$maxTime = 31 * 86400; // Max time to keep unused cookies saved (31 days)
		$filename = DOWNLOAD_DIR . basename($filename);
		if (file_exists($filename) && ($savedcookies = file($filename)) && is_array($savedcookies = unserialize($savedcookies[1]))) {
			// Remove old cookies
			foreach ($savedcookies as $k => $v) if (time() - $v['time'] >= $maxTime) unset($savedcookies[$k]);
		} else $savedcookies = array();
		$hash = sha1("$user$pass");
		$key = substr(base64_encode(hash('sha512', "$user$pass", true)), 0, 56); // 56 chars cropped base64 encoded key to avoid blowfish issues with \0
		$savedcookies[$hash] = array('time' => time(), 'enc' => $this->cJar_encrypt('OK', $key), 'cookie' => $this->cJar_encrypt($this->cookie, $key));

		file_put_contents($filename, "<?php exit(); ?>\r\n" . serialize($savedcookies), LOCK_EX);
	}

	private function Login($user, $pass) {
		if (!extension_loaded('bcmath')) html_error('This plugin needs BCMath extension for login.');
		$this->cookie = array();
		$password_aes = $this->prepare_key($this->str_to_a32($pass));
		$this->cookie['user_handle'] = $this->stringhash($user, $password_aes);
		$res = $this->apiReq(array('a' => 'us', 'user' => $user, 'uh' => $this->cookie['user_handle']));
		if (is_numeric($res[0])) $this->CheckEr($res[0], 'Cannot login');
		$master_key = $this->decrypt_key($this->base64_to_a32($res[0]['k']), $password_aes);
		$privk = $this->a32_to_str($this->decrypt_key($this->base64_to_a32($res[0]['privk']), $master_key));
		$rsa_priv_key = array(0, 0, 0, 0);
		for ($i = 0; $i < 4; $i++) {
			$l = ((ord($privk[0]) * 256 + ord($privk[1]) + 7) / 8) + 2;
			$rsa_priv_key[$i] = $this->mpi2bc(substr($privk, 0, $l));
			$privk = substr($privk, $l);
		}
		unset($privk, $rsa_priv_key[3]);
		$this->cookie['sid'] = $this->base64url_encode(substr(strrev($this->rsa_decrypt($this->mpi2bc($this->base64url_decode($res[0]['csid'])), $rsa_priv_key[0], $rsa_priv_key[1], $rsa_priv_key[2])), 0, 43));
		$this->cookie['rsa_priv_key'] = implode('/T8\\', $rsa_priv_key);
		$this->cJar_save($user, $pass); // Update cookies file.
		return true;
	}
}

class Th3822_MegaDlDecrypt extends php_user_filter {
	private $key, $iv, $start, $lastBlock, $waste;
	public function onCreate() {
		if (empty($this->params['iv']) || empty($this->params['key'])) return false;
		$this->key = $this->params['key'];
		$this->iv = $this->params['iv'];
		$this->waste = $this->lastBlock = $this->start = 0;
		if (!empty($this->params['startFrom']) && is_numeric($this->params['startFrom']) && $this->params['startFrom'] > 0) {
			$this->setNextStart($this->params['startFrom']);
		}
		return true;
	}

	public function filter($in, $out, &$consumed, $stop) {
		while ($bucket = stream_bucket_make_writeable($in)) {
			if ($bucket->datalen > 0) {
				if ($this->waste > 0) $bucket->data = str_repeat('*', $this->waste) . $bucket->data;
				$bucket->data = openssl_decrypt($bucket->data, 'AES-128-CTR', $this->key, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING, $this->iv);
				if ($this->waste > 0) $bucket->data = substr($bucket->data, $this->waste);
				$consumed += $bucket->datalen;
				$this->setNextStart($bucket->datalen);
				stream_bucket_append($out, $bucket);
			}
		}
		return PSFS_PASS_ON;
	}

	private function setNextStart($nextStart) {
		$start = $this->start + $nextStart;
		$block = floor($start / 16);
		if (($incBlocks = $block - $this->lastBlock) > 0) $this->increaseIV($incBlocks);
		$this->start = $start;
		$this->lastBlock = $block;
		$this->waste = $start % 16;
	}

	private function increaseIV($inc) {
		$i = 16;
		while ($inc > 0 && --$i >= 0) {
			$sum = ord($this->iv{$i}) + $inc;
			$this->iv{$i} = chr($sum & 0xFF);
			$inc = $sum >> 8;
		}
	}
}

class Th3822_MegaDlDecrypt_Old extends php_user_filter {
	private $td;
	public function onCreate() {
		if (empty($this->params['iv']) || empty($this->params['key'])) return false;
		$this->td = mcrypt_module_open('rijndael-128', '', 'ctr', '');
		$iv = $this->params['iv'];
		if (!empty($this->params['startFrom']) && is_numeric($this->params['startFrom']) && $this->params['startFrom'] > 0) {
			$blocks = floor($this->params['startFrom'] / 16);
			$waste = $this->params['startFrom'] % 16;
			if ($blocks > 0) $this->increaseIV($iv, $blocks);
		} else $waste = 0;
		$init = mcrypt_generic_init($this->td, $this->params['key'], $iv);
		if ($init === false || $init < 0) return false;
		if ($waste > 0) mdecrypt_generic($this->td, str_repeat('*', $waste));
		return true;
	}

	public function filter($in, $out, &$consumed, $stop) {
		while ($bucket = stream_bucket_make_writeable($in)) {
			if ($bucket->datalen > 0) {
				$bucket->data = mdecrypt_generic($this->td, $bucket->data);
				$consumed += $bucket->datalen;
				stream_bucket_append($out, $bucket);
			}
		}
		return PSFS_PASS_ON;
	}

	public function onClose() {
		mcrypt_generic_deinit($this->td);
		mcrypt_module_close($this->td);
	}

	private function increaseIV(&$iv, $inc = 1) {
		$i = 16;
		while ($inc > 0 && --$i >= 0) {
			$sum = ord($iv{$i}) + $inc;
			$iv{$i} = chr($sum & 0xFF);
			$inc = $sum >> 8;
		}
	}
}

//[24-2-2013] Written by Th3-822. (Rapidleech r415 or newer required)
//[02-3-2013] Added "checks" for validating rapidleech version & added 2 error msg. - Th3-822
//[27-3-2013] Simplified Stream decrypt function (The other one was not working well... After many tests looks like it's better now :D). - Th3-822
//[20-7-2013] Fixed link regexp. - Th3-822
//[09-8-2013] Added folder support and small fixes from upload plugin. (Download links that are fetched from a folder link are not public and only can be downloaded with this plugin.) - Th3-822
//[30-1-2014] Fixed download from folders. - Th3-822
//[09-2-2014] Fixed issues at link parsing. - Th3-822
//[29-1-2015] Replaced 'T8' prefix at folder->file links for support on third-party downloaders using links with 'N' as prefix. - Th3-822
//[04-2-2016] Added sub-folders support (fully) and added support for link suffix "!less" to disable recursive sub-folder download. - Th3-822
//[27-12-2016] Added Login support for increase traffic limits & forced SSL on downloads to avoid corrupted downloads. - Th3-822
//[12-11-2017] Removed SSL from downloads to increase download speed & updated Th3822_MegaDlDecrypt class. - Th3-822
//[18-10-2019] Added new Th3822_MegaDlDecrypt class using OpenSSL & Removed mcrypt dependence to run, now it can be used with only the OpenSSL module (Btw, it's Faster). - Th3-822