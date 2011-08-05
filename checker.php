<?php
define ( 'RAPIDLEECH', 'yes' );
define ( 'CONFIG_DIR', 'configs/' );
require_once(CONFIG_DIR.'setup.php');
define ( 'TEMPLATE_DIR', 'templates/'.$options['template_used'].'/' );
require_once('classes/other.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head><title>Rapidleech Checker Script</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link title="Rapidleech Style" href="<?php echo TEMPLATE_DIR; ?>styles/rl_style_pm.css" rel="stylesheet" type="text/css" />
</head>
<body>
<?php
$phpver = phpversion();
$phpverr = str_replace(".", "", $phpver);

if ($phpverr >= 430) {
	$phpverr = '<span class="checkerp">'.lang(308).'</span>';
} else {
	$phpverr = '<span class="checkerf">'.lang(309).'</span>';
}

if ( ini_get('safe_mode') ){
	$safemode = '<span class="checkerf">'.lang(309).'</span>';
} else {
	$safemode = '<span class="checkerp">'.lang(308).'</span>';
}

if (!function_exists('stream_socket_client')) {
	$fsockopen = '<span class="checkerf">'.lang(309).'</span>';
} else {
	$fsockopen = '<span class="checkerp">'.lang(308).'</span>';
}

@ini_set('memory_limit', '64M');
if ((int)ini_get('memory_limit') > 32) {
	$memory_limit = '<span class="checkerp">'.lang(308).'</span>';
} else {
	$memory_limit = '<span class="checkerf">'.lang(309).'</span>';
}

if (!function_exists('curl_version')) {
	$curl = '<span class="checkerf">'.lang(309).'</span>';
} else {
	$curl = '<span class="checkerp">'.lang(308).'</span>';
}

if (!ini_get('allow_url_fopen')) {
	$fopen = '<span class="checkerf">'.lang(309).'</span>';
} else {
	$fopen = '<span class="checkerp">'.lang(308).'</span>';
}

if (!ini_get('allow_call_time_pass_reference')) {
	$call_time = '<span class="checkerf">'.lang(310).'</span>';
} else {
	$call_time = '<span class="checkerp">'.lang(308).'</span>';
}

if (!function_exists('passthru')) {
	$passthru = '<span class="checkerf">'.lang(311).'</span>';
} else {
	$passthru = '<span class="checkerp">'.lang(308).'</span>';
}

if (!function_exists('disk_free_space')) {
	$disk_free_space = '<span class="checkerf">'.lang(311).'</span>';
} else {
	$disk_free_space = '<span class="checkerp">'.lang(308).'</span>';
}

if (!extension_loaded('openssl')) {
    $ssl = '<span class="checkerf">'.lang(309).'</span>';
} else {
    $ssl = '<span class="checkerp">'.lang(308).'</span>';
}

if (function_exists('apache_get_version')) {
	$apache_version = apache_get_version();
	preg_match('/Apache\/([0-9])\./U',$apache_version,$apacver);
	if ($apacver[1] < 2) {
		$apacver = '<span class="checkerf">'.lang(312).'</span>';
	} else {
		$apacver = '<span class="checkerp">'.lang(308).'</span>';
	}
}
?>
<center><img src="<?php echo TEMPLATE_DIR; ?>images/logo_pm.gif" alt="RapidLeech PlugMod" border="0" /></center>
<br />
<table border="0" width="100%" align="center">
	<tr>
		<td>
			<div align="center">
				<table border="1" cellpadding="20" style="border-collapse: collapse">
					<tr>
						<td align="center" style="font-family: 'Verdana'; font-size: smaller">
							<b><?php echo lang(313); ?></b>
						</td>
					</tr>
					<tr>
						<td align="center" style="font-family: 'Verdana'; font-size: smaller;">
							<b><?php echo lang(314); ?></b>: <?php echo $fsockopen; ?><br /><br />
							<b><?php echo lang(315); ?></b>: <?php echo $memory_limit; ?><br /><br />
							<b><?php echo lang(316); ?></b>: <?php echo $safemode; ?><br /><br />
							<b><?php echo lang(317); ?></b>: <?php echo $curl; ?><br /><br />
							<b><?php echo lang(318); ?></b>: <?php echo $fopen; ?><br /><br />
							<b><?php echo lang(319); ?><?php echo $phpver; ?></b>: <?php echo $phpverr; ?><br /><br />
							<b><?php echo lang(320); ?></b>: <?php echo $call_time; ?><br /><br />
							<b><?php echo lang(321); ?></b>: <?php echo $passthru; ?><br /><br />
							<b><?php echo lang(322); ?></b>: <?php echo $disk_free_space; ?><br /><br />
							<b><?php echo lang(388); ?></b>: <?php echo $ssl; ?><br /><br />
<?php if ($apache_version) {
?>
							<b><?php echo lang(323); ?><?php echo $apache_version; ?></b>: <?php echo $apacver; ?><br /><br />
<?php
}?>
						</td>
					</tr>
				</table>
			</div>
		</td>
	</tr>
</table>
</body>
</html>
