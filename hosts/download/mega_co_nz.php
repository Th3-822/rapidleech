<?php
if (!defined('RAPIDLEECH')) {
	require_once('index.html');
	exit;
}
// Using functions from: http://julien-marchand.fr/blog/using-the-mega-api-with-php-examples/
class mega_co_nz extends DownloadClass {
	private $seqno;
	public function Download($link) {
		if (!extension_loaded('mcrypt') || !in_array('rijndael-128', mcrypt_list_algorithms(), true)) html_error("Mcrypt module isn't installed or it doesn't have support for the needed encryption.");
		$this->RLCheck();
		$this->seqno = mt_rand();
		$this->changeMesg(lang(300).'<br />Mega.co.nz plugin by Th3-822'); // Please, do not remove or change this line contents. - Th3-822

		$fragment = parse_url($link, PHP_URL_FRAGMENT);
		if (preg_match('@^F!([^!]{8})!([\w\-\,]{22})@i', $fragment, $fid)) return $this->Folder($fid[1], $fid[2]);
		if (!preg_match('@^(T8)?!([^!]{8})!([\w\-\,]{43})(?:!([^!]{8})!)?@i', $fragment, $fid)) html_error('FileID or Key not found at link.');

		$reply = $this->apiReq(array('a' => 'g', 'g' => '1', (empty($fid[1]) ? 'p' : 'n') => $fid[2], 'ssl'=> '0'), (!empty($fid[1]) && !empty($fid[4]) ? $fid[4] : ''));
		$this->CheckErr($reply[0]);
		if (!empty($reply[0]['e'])) $this->CheckErr($reply[0]['e']);

		$key = $this->base64_to_a32($fid[3]);
		$iv = array_merge(array_slice($key, 4, 2), array(0, 0));
		$key = array($key[0] ^ $key[4], $key[1] ^ $key[5], $key[2] ^ $key[6], $key[3] ^ $key[7]);
		$attr = $this->dec_attr($this->base64url_decode($reply[0]['at']), $key);
		if (empty($attr)) html_error((!empty($fid[1]) ? 'Folder Error: ' : '').'File\'s key isn\'t correct.');

		$this->RedirectDownload($reply[0]['g'], $attr['n'], 0, 0, $link, 0, 0, array('T8[fkey]' => $fid[3]));
	}

	private function CheckErr($code) {
		if (is_numeric($code)) {
			switch ($code) {
				default: $msg = '*No message for this error*';break;
				case -1: $msg = 'An internal error has occurred';break;
				case -2: $msg = 'You have passed invalid arguments to this command, your rapidleech is outdated?';break;
				case -3: $msg = 'A temporary congestion or server malfunction prevented your request from being processed';break;
				case -4: $msg = 'You have exceeded your command weight per time quota. Please wait a few seconds, then try again';break;
				case -9: $msg = 'File/Folder not found';break;
				case -11: $msg = 'Access violation';break;
				case -13: $msg = 'Trying to access an incomplete file';break;
				case -14: $msg = 'A decryption operation failed';break;
				case -16: $msg = 'User blocked';break;
				case -17: $msg = 'Request over quota';break;
				case -18: $msg = 'File temporarily not available, please try again later';break;
				// Confirmed at page:
				case -6: $msg = 'File not found, account was deleted';break;
			}
			html_error("[Error: $code] $msg.");
		}
	}

	private function RLCheck() {
		if (!function_exists('cURL') || (!function_exists('host_matchs') && !function_exists('host_matches')) || !function_exists('GetDefaultParams')) html_error("Your rapidleech version is outdated and it doesn't support this plugin.");
	}

	private function apiReq($atrr, $node='') {
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
		$page = $this->GetPage('https://g.api.mega.co.nz/cs?id=' . ($this->seqno++) . (!empty($node) ? "&n=$node" : ''), 0, json_encode(array($atrr)), "https://mega.co.nz/\r\nContent-Type: application/json");
		list ($header, $page) = array_map('trim', explode("\r\n\r\n", $page, 2));
		if (is_numeric($page)) return array((int)$page);
		if (in_array((int)substr($header, 9, 3), array(500, 503))) return array(-3); //  500 Server Too Busy
		return $this->Get_Reply($page);
	}

	private function Get_Reply($content) {
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

	private function aes_cbc_decrypt($data, $key) {
		return mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $data, MCRYPT_MODE_CBC, "\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0");
	}

	private function aes_cbc_decrypt_a32($data, $key) {
		return $this->str_to_a32($this->aes_cbc_decrypt($this->a32_to_str($data), $this->a32_to_str($key)));
	}

	private function decrypt_key($a, $key) {
		$x = array();
		for ($i = 0; $i < count($a); $i += 4) $x = array_merge($x, $this->aes_cbc_decrypt_a32(array_slice($a, $i, 4), $key));
		return $x;
	}

	private function dec_attr($attr, $key) {
		$attr = trim($this->aes_cbc_decrypt($attr, $this->a32_to_str($key)));
		if (substr($attr, 0, 6) != 'MEGA{"') return false;
		$attr = substr($attr, 4);$attr = substr($attr, 0, strrpos($attr, '}') + 1);
		return $this->Get_Reply($attr);
	}

	public function CheckBack($header) {
		$statuscode = intval(substr($header, 9, 3));
		if ($statuscode != 200) switch ($statuscode) {
			case 509: html_error('[Mega] Transfer quota exeeded.');
			case 503: html_error('Too many connections for this download.');
			case 403: html_error('Link used/expired.');
			case 404: html_error('Link expired.');
			default : html_error('[HTTP] '.trim(substr($header, 9, strpos($header, "\n") - 8)));
		}

		global $fp;
		if (empty($fp) || !is_resource($fp)) html_error("Error: Your rapidleech version is outdated and it doesn't support this plugin.");
		if (!empty($_GET['T8']['fkey'])) $key = $this->base64_to_a32(urldecode($_GET['T8']['fkey']));
		elseif (preg_match('@^(T8)?!([^!]{8})!([\w\-\,]{43})@i', parse_url($_GET['referer'], PHP_URL_FRAGMENT), $dat)) $key = $this->base64_to_a32($dat[2]);
		else html_error("[CB] File's key not found.");

		$iv = array_merge(array_slice($key, 4, 2), array(0, 0));
		$key = array($key[0] ^ $key[4], $key[1] ^ $key[5], $key[2] ^ $key[6], $key[3] ^ $key[7]);
		$opts = array('iv' => $this->a32_to_str($iv), 'key' => $this->a32_to_str($key), 'mode' => 'ctr');
		stream_filter_register('MegaDlDecrypt', 'Th3822_MegaDlDecrypt');
		stream_filter_prepend($fp, 'MegaDlDecrypt', STREAM_FILTER_READ, $opts);
	}

	private function FSort($a, $b) {
		$a = $a['n'];$b = $b['n'];
		return strcmp($a, $b); // Case Sensitive Sort
		//return strcasecmp($a, $b); // Case Insensitive Sort
		//return strnatcmp($a, $b); // Case Sensitive "Natural" Sort
		//return strnatcasecmp($a, $b); // Case Insensitive "Natural" Sort
	}

	private function Folder($fnid, $fnk) {
		$files = $this->apiReq(array('a' => 'f', 'c' => 1, 'r' => 1), $fnid);
		if (is_numeric($files[0])) $this->CheckErr($files[0], 'Cannot get folder contents');
		$dfiles = array();

		foreach ($files[0]['f'] as $file) if ($file['t'] == 0) {
			$keys = array();
			foreach (explode('/', $file['k']) as $key) if (strpos($key, ':') !== false && $key = explode(':', $key, 2)) $keys[$key[0]] = $key[1];
			if (empty($keys)) continue;
			$key = $this->decrypt_key($this->base64_to_a32(reset($keys)), $this->base64_to_a32($fnk));
			if (empty($key)) continue;
			$attr = $this->dec_attr($this->base64url_decode($file['a']), array($key[0] ^ $key[4], $key[1] ^ $key[5], $key[2] ^ $key[6], $key[3] ^ $key[7]));
			if (!empty($attr)) $dfiles[$file['h']] = array('k' => $this->a32_to_base64($key), 'n' => $attr['n']);
		}
		if (empty($dfiles)) html_error('Error while decoding folder: Empty Folder?.');
		uasort($dfiles, array($this, 'FSort'));

		$files = array();
		foreach ($dfiles as $file => $key) $files[] = "https://mega.co.nz/#T8!$file!{$key['k']}!$fnid!Rapidleech";
		$this->moveToAutoDownloader($files);
	}
}

class Th3822_MegaDlDecrypt extends php_user_filter {
	private $_td, $_data, $_dlen, $_clen, $bucket;
	public function onCreate() {
		if (empty($this->params['iv']) || empty($this->params['key']) || empty($this->params['mode'])) return false;
		$this->_td = mcrypt_module_open('rijndael-128', '', $this->params['mode'], '');
		$init = mcrypt_generic_init($this->_td, $this->params['key'], $this->params['iv']);
		if ($init === false || $init < 0) return false;
		return true;
	}

	public function filter($in, $out, &$consumed, $stop) {
		while ($bucket = stream_bucket_make_writeable($in)) {
			if ($bucket->datalen == 0) continue;
			$this->bucket = $bucket;
			$this->bucket->data = mdecrypt_generic($this->_td, $this->bucket->data);
			$consumed += $bucket->datalen;
			stream_bucket_append($out, $this->bucket);
		}
		return PSFS_PASS_ON;
	}

	public function onClose() {
		mcrypt_generic_deinit($this->_td);
		mcrypt_module_close($this->_td);
	}
}

//[24-2-2013] Written by Th3-822. (Rapidleech r415 or newer required)
//[02-3-2013] Added "checks" for validating rapidleech version & added 2 error msg. - Th3-822
//[27-3-2013] Simplified Stream decrypt function (The other one was not working well... After many tests looks like it's better now :D). - Th3-822
//[20-7-2013] Fixed link regexp. - Th3-822
//[09-8-2013] Added folder support and small fixes from upload plugin. (Download links that are fetched from a folder link are not public and only can be downloaded with this plugin.) - Th3-822
//[25-1-2014] Added sub-folders support. - Th3-822
//[30-1-2014] Fixed download from folders. - Th3-822
//[09-2-2014] Fixed issues at link parsing. - Th3-822

?>