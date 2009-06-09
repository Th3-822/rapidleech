<html>
<head><title>Rapidleech Checker Script!</title></head>
<body>
<?php
$phpver = phpversion();
$phpverr = str_replace(".", "", $phpver);

if ($phpverr >= 430) {
	$phpverr = "<font face=\"Verdana\" size=\"2\" color=\"#008000\">Passed</font>";
} else {
	$phpverr = "<font face=\"Verdana\" size=\"2\" color=\"#FF0000\">Failed</font>";
}

if ( ini_get('safe_mode') ){
	$safemode = "<font face=\"Verdana\" size=\"2\" color=\"#FF0000\">Failed</font>";
} else {
	$safemode = "<font face=\"Verdana\" size=\"2\" color=\"#008000\">Passed</font>";
}

if (!function_exists('fsockopen')) {
	$fsockopen = "<font face=\"Verdana\" size=\"2\" color=\"#FF0000\">Failed</font>";
} else {
	$fsockopen = "<font face=\"Verdana\" size=\"2\" color=\"#008000\">Passed</font>";
}

if ((int)ini_get('memory_limit') > 32) {
	$memory_limit = "<font face=\"Verdana\" size=\"2\" color=\"#008000\">Passed</font>";
} else {
	$memory_limit = "<font face=\"Verdana\" size=\"2\" color=\"#FF0000\">Failed</font>";
}

if (!function_exists('curl_version')) {
	$curl = "<font face=\"Verdana\" size=\"2\" color=\"#FF0000\">Failed</font>";
} else {
	$curl = "<font face=\"Verdana\" size=\"2\" color=\"#008000\">Passed</font>";
}

if (!ini_get('allow_url_fopen')) {
	$fopen = "<font face=\"Verdana\" size=\"2\" color=\"#FF0000\">Failed</font>";
} else {
	$fopen = "<font face=\"Verdana\" size=\"2\" color=\"#008000\">Passed</font>";
}

if (!ini_get('allow_call_time_pass_reference')) {
	$call_time = "<font face=\"Verdana\" size=\"2\" color=\"#FF0000\">You might see warnings without this turned on</font>";
} else {
	$call_time = "<font face=\"Verdana\" size=\"2\" color=\"#008000\">Passed</font>";
}

if (!function_exists('passthru')) {
	$passthru = "<font face=\"Verdana\" size=\"2\" color=\"#FF0000\">You might not be able to turn on server stats</font>";
} else {
	$passthru = "<font face=\"Verdana\" size=\"2\" color=\"#008000\">Passed</font>";
}

if (!function_exists('disk_free_space')) {
	$disk_free_space = "<font face=\"Verdana\" size=\"2\" color=\"#FF0000\">You might not be able to turn on server stats</font>";
} else {
	$disk_free_space = "<font face=\"Verdana\" size=\"2\" color=\"#008000\">Passed</font>";
}

if (function_exists('apache_get_version')) {
	$apache_version = apache_get_version();
	preg_match('/Apache\/([0-9])\./U',$apache_version,$apacver);
	if ($apacver[1] < 2) {
		$apacver = "<font face=\"Verdana\" size=\"2\" color=\"#FF0000\">Your server might not be able to support files more than 2 GB</font>";
	} else {
		$apacver = "<font face=\"Verdana\" size=\"2\" color=\"#008000\">Passed</font>";
	}
}

?>
<table border="0" width="100%" height="100%" cellpadding="5" style="border-collapse: collapse" align="center">
	<tr>
		<td>
			<div align="center">
				<table border="1" cellpadding="20" style="border-collapse: collapse" bordercolor="#000000">
					<tr>
						<td align="center">
							<img border="0" src="http://www.rapidleech.com/logo.gif" /><br />
							<font face="Verdana" size="2"><b>Rapidleech Checker Script</b>
							</font>
						</td>
					</tr>
					<tr>
						<td align="center">
							<font face="Verdana" size="2"><b>fsockopen</b>:</font> <?php echo $fsockopen; ?><br /><br />
							<font face="Verdana" size="2"><b>memory_limit</b>:</font> <?php echo $memory_limit; ?><br /><br />
							<font face="Verdana" size="2"><b>safe_mode</b>:</font> <?php echo $safemode; ?><br /><br />
							<font face="Verdana" size="2"><b>cURL</b>:</font> <?php echo $curl; ?><br /><br />
							<font face="Verdana" size="2"><b>allow_url_fopen</b>:</font> <?php echo $fopen; ?><br /><br />
							<font face="Verdana" size="2"><b>PHP Version - <?php echo $phpver; ?></b>:</font> <?php echo $phpverr; ?><br /><br />
							<font face="Verdana" size="2"><b>allow_call_time_pass_reference</b>:</font> <?php echo $call_time; ?><br /><br />
							<font face="Verdana" size="2"><b>passthru</b>:</font> <?php echo $passthru; ?><br /><br />
							<font face="Verdana" size="2"><b>Disk Space Functions</b>:</font> <?php echo $disk_free_space; ?><br /><br />
<?php if ($apache_version) {
?>
							<font face="Verdana" size="2"><b>Apache Version - <?php echo $apache_version; ?></b>:</font> <?php echo $apacver; ?><br /><br />
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