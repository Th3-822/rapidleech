<?php
if (!defined('RAPIDLEECH')) {
	require('../deny.php');
	exit;
}

function create_hosts_file($host_file = 'hosts.php') { // To be rewritten or deleted
	$fp = opendir(HOST_DIR . 'download/');
	while (($file = readdir($fp)) !== false) {
		if (substr($file, -4) == '.inc') require_once (HOST_DIR . "download/$file");
	}
	if (!is_array($host)) html_error(lang(127));
	else {
		$fs = fopen(HOST_DIR . "download/$host_file", 'wb');
		if (!$fs) html_error(lang(128));
		else {
			fwrite($fs, "<?php\r\n\$host = array(\r\n");
			$i = 0;
			foreach ($host as $site => $file) {
				if ($i != (count($host) - 1)) fwrite($fs, "'" . $site . "' => '" . $file . "',\r\n");
				else fwrite($fs, "'" . $site . "' => '" . $file . "');\r\n?>");
				$i++;
			}
			closedir($fp);
			fclose($fs);
		}
	}
}

function login_check() {
	global $options;
	if ($options['login']) {
		function logged_user($ul) {
			foreach ($ul as $user => $pass) {
				if ($_SERVER['PHP_AUTH_USER'] == $user && isset($_SERVER['PHP_AUTH_PW']) && $_SERVER['PHP_AUTH_PW'] == $pass) return true;
			}
			return false;
		}
		if (empty($_SERVER['PHP_AUTH_USER']) && (!empty($_SERVER['HTTP_AUTHORIZATION']) || !empty($_SERVER['REDIRECT_HTTP_AUTHORIZATION']))) {
			$auth = !empty($_SERVER['HTTP_AUTHORIZATION']) ? $_SERVER['HTTP_AUTHORIZATION'] : $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
			if (stripos($auth, 'Basic ') === 0 && strpos(($auth = base64_decode(substr($auth, 6))), ':') > 0) list($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']) = explode(':', $auth, 2);
			unset($auth);
		}
		if (empty($_SERVER['PHP_AUTH_USER']) || !logged_user($options['users'])) {
			header('WWW-Authenticate: Basic realm="RAPIDLEECH PLUGMOD"');
			header('HTTP/1.0 401 Unauthorized');
			include('deny.php');
			exit;
		}
	}
}

function is_present($lpage, $mystr, $strerror = '', $head = 0) {
	if (stripos($lpage, $mystr) !== false) html_error((!empty($strerror) ? $strerror : $mystr), $head);
}

function is_notpresent($lpage, $mystr, $strerror, $head = 0) {
	if (stripos($lpage, $mystr) === false) html_error($strerror, $head);
}

function insert_location($inputs, $action = 0) {
	if (!is_array($inputs)) {
		if (strpos($inputs, '?') !== false) list($action, $inputs) = explode('?', $inputs, 2);
		$query = explode('&', $inputs);
		$inputs = array();
		foreach($query as $q) {
			list($name, $value) = explode('=', $q, 2);
			if (empty($name) || empty($value)) continue;
			$inputs[$name] = $value;
		}
		unset($query);
	}
	if (isset($_GET['GO']) && $_GET['GO'] == 'GO') $_GET = array_merge($_GET, $inputs);
	else {
		if ($action === 0) $action = $_SERVER['SCRIPT_NAME'];
		$fname = 'r'.time().'l';
		echo "\n<form name='$fname' ".(!empty($action) ? "action='$action' " : '')."method='POST'>\n";
		foreach($inputs as $name => $value) echo "\t<input type='hidden' name='$name' value='$value' />\n";
		echo "</form>\n<script type='text/javascript'>void(document.$fname.submit());</script>\n</body>\n</html>";
		flush();
	}
}

function pause_download() { // To make sure that the files pointers and streams are closed and unlocked.
	global $PHP_SELF, $fs, $fp, $file, $pathWithName;
	if (!empty($fs) && is_resource($fs)) {
		flock($fs, LOCK_UN);
		if (get_resource_type($fs) == 'stream') stream_socket_shutdown($fs, STREAM_SHUT_RDWR);
		fclose($fs);
	}
	if (!empty($fp) && is_resource($fp)) {
		flock($fp, LOCK_UN);
		if (get_resource_type($fp) == 'stream') stream_socket_shutdown($fp, STREAM_SHUT_RDWR);
		fclose($fp);
	}
}

function cut_str($str, $left, $right) {
	$str = substr(stristr($str, $left), strlen($left));
	$leftLen = strlen(stristr($str, $right)); 
	$leftLen = $leftLen ? -($leftLen) : strlen($str);
	$str = substr($str, 0, $leftLen);
	return $str;
}

// tweaked cutstr with pluresearch functionality
function cutter($str, $left, $right, $cont = 1) {
	for($iii = 1; $iii <= $cont; $iii++) $str = substr(stristr($str, $left), strlen($left));
	$leftLen = strlen(stristr($str, $right));
	$leftLen = $leftLen ? -($leftLen) : strlen($str);
	$str = substr($str, 0, $leftLen);
	return $str;
}

function write_file($file_name, $data, $trunk = 1) {
	if ($trunk == 1) $mode = 'wb';
	elseif ($trunk == 0) $mode = 'ab';
	$fp = fopen($file_name, $mode);
	if (!$fp || !flock($fp, LOCK_EX) || !fwrite($fp, $data) || !flock($fp, LOCK_UN) || !fclose($fp)) return FALSE;
	return TRUE;
}

function read_file($file_name, $count = -1) {
	if ($count == -1) $count = filesize($file_name);
	$fp = fopen($file_name, 'rb');
	flock($fp, LOCK_SH);
	$ret = fread($fp, $count);
	flock($fp, LOCK_UN);
	fclose($fp);
	return $ret;
}

function pre($var) {
	echo "<pre>";
	print_r($var);
	echo "</pre>";
}

function getmicrotime() {
	list ($usec, $sec) = explode(' ', microtime());
	return ((float)$usec + (float)$sec);
}

function html_error($msg, $head = 1) {
	global $PHP_SELF, $options;

	if (strtolower(basename($PHP_SELF)) == 'audl.php' && isset($_REQUEST['GO']) && $_REQUEST['GO'] == 'GO' && $_REQUEST['server_side'] == 'on' && !empty($GLOBALS['isHost'])) throw new Exception($msg); // Audl-Server Side, called from a plugin.
	else {
		//if ($head == 1)
		if (!headers_sent()) include(TEMPLATE_DIR.'header.php');
		echo '<div align="center">';
		echo "<span class='htmlerror'><b>$msg</b></span><br /><br />";
		if (isset($_GET['audl'])) echo '<script type="text/javascript">parent.nextlink();</script>';
		if (!empty($options['new_window'])) echo '<a href="javascript:window.close();">'.lang(378).'</a>';
		else echo '<a href="'.$PHP_SELF.'">'.lang(13).'</a>';
		echo '</div>';
	}
	pause_download();
	include(TEMPLATE_DIR.'footer.php');
	exit();
}

function sec2time($time) {
	$hour = round($time / 3600, 2);
	if ($hour >= 1) {
		$hour = floor($hour);
		$time -= $hour * 3600;
	}
	$min = round($time / 60, 2);
	if ($min >= 1) {
		$min = floor($min);
		$time -= $min * 60;
	}
	$sec = $time;
	$hour = ($hour > 1) ? $hour . ' ' . lang(129) . ' ' : ($hour == 1) ? $hour . ' ' . lang(130).' ' : '';
	$min = ($min > 1) ? $min . ' ' . lang(131) . ' ' : ($min == 1) ? $min . ' ' . lang(132).' ' : '';
	$sec = ($sec > 1) ? $sec . ' ' . lang(133) : ($sec == 1 || $sec == 0) ? $sec . ' ' . lang(134) : '';
	return $hour . $min . $sec;
}

// Updated function to be able to format up to Yotabytes!
function bytesToKbOrMbOrGb($bytes) {
	if (is_numeric($bytes)) {
		$s = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
		$e = floor(log($bytes) / log(1024));
		return sprintf('%.2f ' . $s[$e], @($bytes / pow(1024, floor($e))));
	} else $size = 'Unknown';
	return $size;
}

function updateListInFile($list) {
	if (count($list) > 0) {
		foreach($list as $key => $value) $list[$key] = serialize($value);
		if (!@write_file(CONFIG_DIR . 'files.lst', implode("\r\n", $list) . "\r\n") && count($list) > 0) return FALSE;
		else return TRUE;
	} elseif (@file_exists(CONFIG_DIR . 'files.lst')) {
		// Truncate files.lst instead of removing it since we don't have full
		// read/write permission on the configs folder
		$fh = fopen(CONFIG_DIR . 'files.lst','w');
		fclose($fh);
		return true;
	}
}

function _cmp_list_enums($a, $b) {
	return strcmp($a['name'], $b['name']);
}

function file_data_size_time($file) {
	global $options;
	$size = $time = false;
	if (is_file($file)) {
		$size = @filesize($file);
		$time = @filemtime($file);
	}
	if ($size === false && $options['2gb_fix'] && file_exists($file) && !is_dir($file) && !is_link($file)) {
		if (substr(PHP_OS, 0, 3) !== 'WIN') {
			@exec('stat' . (stripos(@php_uname('s'), 'bsd') !== false ? '-f %m ' : ' -c %Y ') . escapeshellarg($file), $time, $tmp);
			if ($tmp == 0) $time = trim(implode($time));
			@exec('stat' . (stripos(@php_uname('s'), 'bsd') !== false ? '-f %z ' : ' -c %s ') . escapeshellarg($file), $size, $tmp);
			if ($tmp == 0) $size = trim(implode($size));
		}
	}
	if ($size === false || $time === false) { return false; }
	return array($size, $time);
}

function _create_list() {
	global $list, $_COOKIE, $options;
	$glist = array();
	if (($options['show_all'] === true) && (isset($_COOKIE['showAll']) && $_COOKIE['showAll'] == 1)) {
		$dir = dir(DOWNLOAD_DIR);
		while(false !== ($file = $dir->read())) {
			if (($tmp = file_data_size_time(DOWNLOAD_DIR.$file)) === false) continue;
			list($size, $time) = $tmp;
			if ($file != '.' && $file != '..' && (!is_array($options['forbidden_filetypes']) || !in_array(strtolower(strrchr($file, '.')), $options['forbidden_filetypes']))) {
				$file = DOWNLOAD_DIR . $file;
				while(isset($glist[$time])) $time++;
				$glist[$time] = array('name' => realpath($file), 'size' => bytesToKbOrMbOrGb($size), 'date' => $time);
			}
		}
		$dir->close();
		@uasort($glist, '_cmp_list_enums');
	} else {
		if (@file_exists(CONFIG_DIR . 'files.lst') && ($glist = file(CONFIG_DIR . 'files.lst')) !== false) {
			foreach($glist as $key => $record) {
				foreach(unserialize($record) as $field => $value) {
					$listReformat[$key][$field] = $value;
					if ($field == 'date') $date = $value;
				}
				$glist[$date] = $listReformat[$key];
				unset($glist[$key], $listReformat[$key]);
			}
		}
	}
	$list = $glist;
}

function checkmail($mail) {
	if (strlen($mail) == 0 || strpos($mail, '@') === false || strpos($mail, '.') === false || !preg_match('/^[a-z0-9_\.-]{1,20}@(([a-z0-9-]+\.)+(com|net|org|mil|edu|gov|arpa|info|biz|inc|name|[a-z]{2})|[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3})$/is', $mail)) return false;
	return true;
}

/* Fixed Shell exploit by: icedog */
function fixfilename($fname, $fpach = '') {
	$f_name = basename($fname);
	$f_dir = dirname(preg_replace('@\.\./@i', '', $fname));
	$f_dir = ($f_dir == '.') ? '' : $f_dir;
	$f_dir = preg_replace('@\.\./@i', '', $f_dir);
	$fpach = preg_replace('@\.\./@i', '', $fpach);
	$f_name = preg_replace('@\.(((s|\d)?php)|(hta)|(p[l|y])|(cgi)|(sph))@i', '.xxx', $f_name);
	$ret = ($fpach) ? $fpach . DIRECTORY_SEPARATOR . $f_name : ($f_dir ? $f_dir . DIRECTORY_SEPARATOR : '') . $f_name;
	return $ret;
}

function getfilesize($f) {
	global $is_windows;
	$stat = stat($f);

	if ($is_windows || (($stat[11] * $stat[12]) < 4 * 1024 * 1024 * 1024)) return sprintf('%u', $stat[7]);

	global $max_4gb;
	if ($max_4gb === false) {
		$tmp_ = trim(@shell_exec('ls -Ll ' . @escapeshellarg($f)));
		while(strstr($tmp_, '  ')) $tmp_ = @str_replace('  ', ' ', $tmp_);
		$r = @explode(' ', $tmp_);
		$size_ = $r[4];
	} else $size_ = -1;

	return $size_;
}

function bytesToKbOrMb($bytes) {
	$size = ($bytes >= (1024 * 1024 * 1024 * 1024)) ? round($bytes / (1024 * 1024 * 1024 * 1024), 2) . ' TB' : (($bytes >= (1024 * 1024 * 1024)) ? round($bytes / (1024 * 1024 * 1024), 2) . ' GB' : (($bytes >= (1024 * 1024)) ? round($bytes / (1024 * 1024), 2) . ' MB' : round($bytes / 1024, 2) . ' KB'));
	return $size;
}

function defport($urls) {
	if (!empty($urls['port'])) return $urls['port'];
	switch(strtolower($urls['scheme'])) {
		case 'http' :
			return '80';
		case 'https' :
			return '443';
		case 'ftp' :
			return '21';
	}
}

function getSize($file) {
	$size = filesize($file);
	if ($size < 0) {
		if (strtoupper(substr(PHP_OS, 0, 3)) != 'WIN') {
			$file = @escapeshellarg($file);
			$size = trim(`stat -c%s $file`);
		} else {
			$fsobj = new COM('Scripting.FileSystemObject');
			$f = $fsobj->GetFile($file);
			$size = $file->Size;
		}
	}
	return $size;
}

function purge_files($delay) {
	if (file_exists(CONFIG_DIR . 'files.lst') && is_numeric($delay) && $delay > 0) {
		$files_lst = file(CONFIG_DIR . 'files.lst');
		$files_new = '';
		foreach ($files_lst as $files_line) {
			$files_data = unserialize(trim($files_line));
			if (file_exists($files_data['name']) && is_file($files_data['name'])) {
				if (time() - $files_data['date'] >= $delay) @unlink ($files_data['name']);
				else $files_new .= $files_line;
			}
		}
		file_put_contents(CONFIG_DIR . 'files.lst', $files_new);
	}
}

// Using this function instead due to some compatibility problems
function is__writable($path) {
	//will work in despite of Windows ACLs bug
	//NOTE: use a trailing slash for folders!!!
	//see http://bugs.php.net/bug.php?id=27609
	//see http://bugs.php.net/bug.php?id=30931

	if ($path[strlen($path) - 1] == '/') return is__writable($path . uniqid(mt_rand()) . '.tmp');// recursively return a temporary file path
	else if (is_dir($path)) return is__writable($path . '/' . uniqid(mt_rand()) . '.tmp');
	// check tmp file for read/write capabilities
	$rm = file_exists($path);
	$f = @fopen($path, 'a');
	if ($f === false) return false;
	fclose($f);
	if (!$rm) unlink($path);
	return true;
}

function link_for_file($filename, $only_link = FALSE, $style = '') {
	$inCurrDir = strpos(dirname($filename), ROOT_DIR) !== FALSE ? TRUE : FALSE;
	if ($inCurrDir) {
		$Path = parse_url($_SERVER ['SCRIPT_NAME']);
		$Path = substr($Path['path'], 0, strlen($Path['path']) - strlen(strrchr($Path['path'], '/')));
		$Path = str_replace('\\', '/', $Path.substr(dirname($filename), strlen(ROOT_DIR)));
	} elseif (dirname($_SERVER ['SCRIPT_NAME'].'safe') != '/') {
		$in_webdir_path = dirname(str_replace('\\', '/', $_SERVER ['SCRIPT_NAME'].'safe'));
		$in_webdir_sub = substr_count($in_webdir_path, '/');
		$in_webdir_root = ROOT_DIR.'/';
		for ($i=1; $i <= $in_webdir_sub; $i++) {
			$in_webdir_path = substr($in_webdir_path, 0, strrpos($in_webdir_path, '/'));
			$in_webdir_root = realpath($in_webdir_root.'/../').'/';
			$in_webdir = (strpos(str_replace('\\', '/', dirname($filename).'/'), str_replace('\\', '/', $in_webdir_root)) === 0) ? TRUE : FALSE;
			if ($in_webdir) {
				$Path = dirname($in_webdir_path.'/'.substr($filename, strlen($in_webdir_root)));
				break;
			}
		}
	} else {
		$Path = FALSE;
		if ($only_link) return '';
	}
	$basename = htmlspecialchars(basename($filename));
	$Path = htmlspecialchars($Path).'/'.rawurlencode(basename($filename));
	if ($only_link) return 'http://'.urldecode($_SERVER['HTTP_HOST']).$Path;
	elseif ($Path === FALSE) return "<span>$basename</span>";
	else return '<a href="'.$Path.'"'.($style !== '' ? ' '.$style : '').'>'.$basename.'</a>';
}

function lang($id) {
	global $options, $lang;
	if (!is_array($lang)) $lang = array();
	if (basename($options['default_language']) != 'en' && file_exists('languages/en.php')) require_once('languages/en.php');
	require_once('languages/'.basename($options['default_language']).'.php');
	return $lang[$id];
}

#need to keep premium account cookies safe!
function encrypt($string) {
	global $secretkey;
	if (!$secretkey) return html_error('Value for $secretkey is empty, please create a random one (56 chars max) in config.php!');
	require_once 'class.pcrypt.php';

	/*
	MODE: MODE_ECB or MODE_CBC
	ALGO: BLOWFISH
	KEY:  Your secret key :) (max lenght: 56)
	*/
	$crypt = new pcrypt(MODE_CBC, 'BLOWFISH', "$secretkey");

	// to encrypt
	$ciphertext = $crypt->encrypt($string);

	return $ciphertext;
}

function decrypt($string) {
	global $secretkey;
	if (!$secretkey) return html_error('Value for $secretkey is empty, please create a random one (56 chars max) in config.php!');
	require_once 'class.pcrypt.php';

	/*
	MODE: MODE_ECB or MODE_CBC
	ALGO: BLOWFISH
	KEY:  Your secret key :) (max lenght: 56)
	*/
	$crypt = new pcrypt(MODE_CBC, 'BLOWFISH', "$secretkey");

	// to decrypt
	$decrypted = $crypt->decrypt($string);

	return $decrypted;
}

/**
 * Textarea for debugging variable
 * @param string The variable you want to debug
 * @param int Column for variable display
 * @param int Rows for variable display
 * @param bool Options to continue or not process
 * @param string Charset encoding for htmlspecialchars
 */
function textarea($var, $cols = 100, $rows = 30, $stop = false, $char = 'UTF-8') {
	$cols = ($cols == 0) ? 100 : $cols;
	$rows = ($rows == 0) ? 30 : $rows;
	if ($char === false) $char = 'ISO-8859-1';
	echo "\n<br /><textarea cols='$cols' rows='$rows' readonly='readonly'>";
	if (is_array($var)) $text = htmlspecialchars(print_r($var, true), ENT_QUOTES, $char);
	else $text = htmlspecialchars($var, ENT_QUOTES, $char);
	if (empty($text) && !empty($var)) { // Fix "empty?" textarea bug
		$char = ($char == 'ISO-8859-1') ? '' : 'ISO-8859-1';
		if (is_array($var)) $text = htmlspecialchars(print_r($var, true), ENT_QUOTES, $char);
		else $text = htmlspecialchars($var, ENT_QUOTES, $char);
	}
	echo "$text</textarea><br />\n";
	if ($stop) exit;
}

// Get time in miliseconds, like getTime() in javascript
function jstime() {
	list($u, $s) = explode(' ', microtime()); 
	return sprintf('%d%03d', $s, $u*1000);
}

function check_referer() {
	$refhost = !empty($_SERVER['HTTP_REFERER']) ? cut_str($_SERVER['HTTP_REFERER'], '://', '/') : false;
	if (empty($refhost)) return;

	//Remove the port.
	$httphost = ($pos = strpos($_SERVER['HTTP_HOST'], ':')) !== false ? substr($_SERVER['HTTP_HOST'], 0, $pos) : $_SERVER['HTTP_HOST'];
	$refhost = ($pos = strpos($refhost, ':')) !== false ? substr($refhost, 0, $pos) : $refhost;
	// If there is a login on the referer, remove it.
	$refhost = ($pos = strpos($refhost, '@')) !== false ? substr($refhost, $pos+1) : $refhost;

	$whitelist = array($httphost, 'localhost', 'rapidleech.com');
	$is_ext = ($refhost == $_SERVER['SERVER_ADDR'] ? false : true);
	if ($is_ext)
		foreach ($whitelist as $host)
			if (host_matches($host, $refhost)) {
				$is_ext = false;
				break;
			}

	if ($is_ext) {
		// Uncomment next line if you want rickroll the users from Form leechers.
		// header("Location: http://www.youtube.com/watch?v=oHg5SJYRHA0");
		html_error(sprintf(lang(7), $refhost, 'External referer not allowed.'));
	}
}

function rebuild_url($url) {
	return $url['scheme'] . '://' . (!empty($url['user']) && !empty($url['pass']) ? rawurlencode($url['user']) . ':' . rawurlencode($url['pass']) . '@' : '') . $url['host'] . (!empty($url['port']) && $url['port'] != 80 && $url['port'] != 443 ? ':' . $url['port'] : '') . (empty($url['path']) ? '/' : $url['path']) . (!empty($url['query']) ? '?' . $url['query'] : '') . (!empty($url['fragment']) ? '#' . $url['fragment'] : '');
}

if (!function_exists('http_chunked_decode')) {
	// Added implementation from a comment at php.net's function page
	function http_chunked_decode($chunk) {
		$pos = 0;
		$len = strlen($chunk);
		$dechunk = null;

		while(($pos < $len) && ($chunkLenHex = substr($chunk, $pos, ($newlineAt = strpos($chunk, "\n", $pos + 1)) - $pos))) {
			if (!is_hex($chunkLenHex)) {
				trigger_error('Value is not properly chunk encoded_', E_USER_WARNING);
				return false;
			}

			$pos = $newlineAt + 1;
			$chunkLen = hexdec(rtrim($chunkLenHex, "\r\n"));
			$dechunk .= substr($chunk, $pos, $chunkLen);
			$pos = strpos($chunk, "\n", $pos + $chunkLen) + 1;
		}
		return $dechunk;
	}

	function is_hex($hex) {
		$hex = strtolower(trim(ltrim($hex, '0')));
		if (empty($hex)) $hex = 0;
		$dec = hexdec($hex);
		return ($hex == dechex($dec));
	}
}

function host_matches($site, $host) {
	if (empty($site) || empty($host)) return false;
	if (strtolower($site) == strtolower($host)) return true;
	if (($pos = strripos($host, $site)) !== false && ($pos + strlen($site) == strlen($host)) && $pos > 1 && substr($host, $pos - 1, 1) == '.') return true;
	return false;
}

function GetDefaultParams() {
	global $options;
	$DParam = array();
	if (isset($_GET['useproxy']) && $_GET['useproxy'] == 'on' && !empty($_GET['proxy'])) {
		global $pauth;
		$DParam['useproxy'] = 'on';
		$DParam['proxy'] = $_GET['proxy'];
		if ($pauth) $DParam['pauth'] = urlencode(encrypt($pauth));
	}
	if (isset($_GET['autoclose'])) $DParam['autoclose'] = '1';
	if (isset($_GET['audl'])) $DParam['audl'] = 'doum';
	if ($options['download_dir_is_changeable'] && !empty($_GET['path'])) $DParam['saveto'] = urlencode($_GET['path']);
	$params = array('add_comment', 'domail', 'comment', 'email', 'split', 'partSize', 'method', 'uploadlater', 'uploadtohost');
	foreach ($params as $key) if (!empty($_GET[$key])) $DParam[$key] = $_GET[$key];
	return $DParam;
}

?>