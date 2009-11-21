<html>
<head><title>Rapidleech Checker Script!</title></head>
<body>
<?php
$phpver = phpversion();
$phpverr = str_replace(".", "", $phpver);

if ($phpverr >= 430) {
	$phpverr = "<font face=\"Verdana\" size=\"2\" class='checkerp'>".lang(308)."</font>";
} else {
	$phpverr = "<font face=\"Verdana\" size=\"2\" class='checkerf'>".lang(309)."</font>";
}

if ( ini_get('safe_mode') ){
	$safemode = "<font face=\"Verdana\" size=\"2\" class='checkerf'>".lang(309)."</font>";
} else {
	$safemode = "<font face=\"Verdana\" size=\"2\" class='checkerp'>".lang(308)."</font>";
}

if (!function_exists('stream_socket_client')) {
	$fsockopen = "<font face=\"Verdana\" size=\"2\" class='checkerf'>".lang(309)."</font>";
} else {
	$fsockopen = "<font face=\"Verdana\" size=\"2\" class='checkerp'>".lang(308)."</font>";
}

if ((int)ini_get('memory_limit') > 32) {
	$memory_limit = "<font face=\"Verdana\" size=\"2\" class='checkerp'>".lang(308)."</font>";
} else {
	$memory_limit = "<font face=\"Verdana\" size=\"2\" class='checkerf'>".lang(309)."</font>";
}

if (!function_exists('curl_version')) {
	$curl = "<font face=\"Verdana\" size=\"2\" class='checkerf'>".lang(309)."</font>";
} else {
	$curl = "<font face=\"Verdana\" size=\"2\" class='checkerp'>".lang(308)."</font>";
}

if (!ini_get('allow_url_fopen')) {
	$fopen = "<font face=\"Verdana\" size=\"2\" class='checkerf'>".lang(309)."</font>";
} else {
	$fopen = "<font face=\"Verdana\" size=\"2\" class='checkerp'>".lang(308)."</font>";
}

if (!ini_get('allow_call_time_pass_reference')) {
	$call_time = "<font face=\"Verdana\" size=\"2\" class='checkerf'>".lang(310)."</font>";
} else {
	$call_time = "<font face=\"Verdana\" size=\"2\" class='checkerp'>".lang(308)."</font>";
}

if (!function_exists('passthru')) {
	$passthru = "<font face=\"Verdana\" size=\"2\" class='checkerf'>".lang(311)."</font>";
} else {
	$passthru = "<font face=\"Verdana\" size=\"2\" class='checkerp'>".lang(308)."</font>";
}

if (!function_exists('disk_free_space')) {
	$disk_free_space = "<font face=\"Verdana\" size=\"2\" class='checkerf'>".lang(311)."</font>";
} else {
	$disk_free_space = "<font face=\"Verdana\" size=\"2\" class='checkerp'>".lang(308)."</font>";
}

if (function_exists('apache_get_version')) {
	$apache_version = apache_get_version();
	preg_match('/Apache\/([0-9])\./U',$apache_version,$apacver);
	if ($apacver[1] < 2) {
		$apacver = "<font face=\"Verdana\" size=\"2\" class='checkerf'>".lang(312)."</font>";
	} else {
		$apacver = "<font face=\"Verdana\" size=\"2\" class='checkerp'>".lang(308)."</font>";
	}
}

?>
<table border="0" width="100%" height="100%" cellpadding="5" style="border-collapse: collapse" align="center">
	<tr>
		<td>
			<div align="center">
				<table border="1" cellpadding="20" style="border-collapse: collapse">
					<tr>
						<td align="center">
							<img border="0" src="http://www.rapidleech.com/logo.gif" /><br />
							<font face="Verdana" size="2"><b><?php echo lang(313); ?></b>
							</font>
						</td>
					</tr>
					<tr>
						<td align="center">
							<font face="Verdana" size="2"><b><?php echo lang(314); ?></b>:</font> <?php echo $fsockopen; ?><br /><br />
							<font face="Verdana" size="2"><b><?php echo lang(315); ?></b>:</font> <?php echo $memory_limit; ?><br /><br />
							<font face="Verdana" size="2"><b><?php echo lang(316); ?></b>:</font> <?php echo $safemode; ?><br /><br />
							<font face="Verdana" size="2"><b><?php echo lang(317); ?></b>:</font> <?php echo $curl; ?><br /><br />
							<font face="Verdana" size="2"><b><?php echo lang(318); ?></b>:</font> <?php echo $fopen; ?><br /><br />
							<font face="Verdana" size="2"><b><?php echo lang(319); ?><?php echo $phpver; ?></b>:</font> <?php echo $phpverr; ?><br /><br />
							<font face="Verdana" size="2"><b><?php echo lang(320); ?></b>:</font> <?php echo $call_time; ?><br /><br />
							<font face="Verdana" size="2"><b><?php echo lang(321); ?></b>:</font> <?php echo $passthru; ?><br /><br />
							<font face="Verdana" size="2"><b><?php echo lang(322); ?></b>:</font> <?php echo $disk_free_space; ?><br /><br />
<?php if ($apache_version) {
?>
							<font face="Verdana" size="2"><b><?php echo lang(323); ?><?php echo $apache_version; ?></b>:</font> <?php echo $apacver; ?><br /><br />
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
