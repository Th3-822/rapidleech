<?php

// This file must be included to work
if (count(get_included_files()) == 1) {
	require('deny.php');
	exit;
}

@set_time_limit(0);
@ini_set('memory_limit', '1024M');
if (ini_get('zlib.output_compression')) @ini_set('zlib.output_compression', 0);
if (ob_get_level()) ob_end_clean();
ob_implicit_flush(true);
header('X-Accel-Buffering: no');
clearstatcache();
error_reporting(6135);
$nn = "\r\n";
$fromaddr = 'RapidLeech';
$dev_name = 'Development Stage';
$rev_num = '43';
$plusrar_v = '4.2';
$PHP_SELF = $_SERVER['SCRIPT_NAME'];
define('RAPIDLEECH', 'yes');
define('ROOT_DIR', realpath('./'));
define('PATH_SPLITTER', ((strpos(ROOT_DIR, '\\') !== false) ? '\\' : '/'));
define('HOST_DIR', 'hosts/');
define('CLASS_DIR', 'classes/');
define('CONFIG_DIR', 'configs/');
define('BUILD', '30May2011');
define('CREDITS', '<a href="http://www.rapidleech.com/" class="rl-link"><b>RapidLeech</b></a>&nbsp;<b class="rev-dev">PlugMod (eqbal) rev. ' . $rev_num . '</b> <span class="rev-dev">' . $dev_name . '</span><br><small class="small-credits">Credits to Pramode &amp; Checkmate &amp; Kloon</small>');

require_once(CONFIG_DIR . 'setup.php');

// $options['download_dir'] should always end with a '/'
if (substr($options['download_dir'], - 1) != '/') $options['download_dir'] .= '/';
define('DOWNLOAD_DIR', (substr($options['download_dir'], 0, 6) == 'ftp://' ? '' : $options['download_dir']));
define('TEMPLATE_DIR', 'templates/' . $options['template_used'] . '/');
define('IMAGE_DIR', TEMPLATE_DIR . 'images/');
header('X-Frame-Options: SAMEORIGIN');
// Avoid Caching
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Last-Modified: ' . gmdate ("D, d M Y H:i:s") . 'GMT');
header('Cache-Control: max-age=0, no-store, no-cache, must-revalidate, proxy-revalidate, post-check=0, pre-check=0');
header('Pragma: no-cache');

require_once(CLASS_DIR . 'other.php');


?>