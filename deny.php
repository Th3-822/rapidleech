<?php
// Access denied page
define('RAPIDLEECH', 'yes');
error_reporting(0);
//ini_set('display_errors', 1);
set_time_limit(0);
ini_alter("memory_limit", "1024M");
ob_end_clean();
ob_implicit_flush(TRUE);
ignore_user_abort(1);
clearstatcache();
$PHP_SELF = $_SERVER ['SCRIPT_NAME'];
define('HOST_DIR', 'hosts/');
define('CLASS_DIR', 'classes/');
define('CONFIG_DIR', 'configs/');
define('RAPIDLEECH', 'yes');
define('ROOT_DIR', realpath("./"));
define('PATH_SPLITTER', (strstr(ROOT_DIR, "\\") ? "\\" : "/"));
require_once("configs/config.php");
if (substr($options['download_dir'],-1) != '/') $options['download_dir'] .= '/';
define('DOWNLOAD_DIR', (substr($options['download_dir'], 0, 6) == "ftp://" ? '' : $options['download_dir']));
define ( 'TEMPLATE_DIR', 'templates/'.$options['template_used'].'/' );
define('IMAGE_DIR', TEMPLATE_DIR . 'images/');
$nn = "\r\n";
require_once("classes/other.php");
include(TEMPLATE_DIR.'header.php');
echo('<div align="center">');
echo('<h1>'.lang(1).'</h1>');
echo('<p>'.lang(2).'</p>');
echo('</div>');
include(TEMPLATE_DIR.'footer.php');
?>