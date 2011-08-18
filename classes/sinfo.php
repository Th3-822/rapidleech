<?php
if (! isset ( $servername ) || $servername == "") {
	$theservername = $_SERVER ['SERVER_NAME'];
} else {
	$theservername = $servername;
}
if (! isset ( $customos ) || $customos == "") {
	$osname = checkos ();
} else {
	$os = "nocpu";
	$osname = $customos;
}
if (php_sapi_name () == "apache2handler") {
	$httpapp = "Apache";
} else {
	$httpapp = php_sapi_name ();
}
function checkos() {
	if (substr ( PHP_OS, 0, 3 ) == "WIN") {
		$osType = winosname ();
		$osbuild = php_uname ( 'v' );
		$os = "windows";
	} elseif (PHP_OS == "FreeBSD") {
		$os = "nocpu";
		$osType = "FreeBSD";
		$osbuild = php_uname ( 'r' );
	} elseif (PHP_OS == "Darwin") {
		$os = "nocpu";
		$osType = "Apple OS X";
		$osbuild = php_uname ( 'r' );
	} elseif (PHP_OS == "Linux") {
		$os = "linux";
		$osType = "Linux";
		$osbuild = php_uname ( 'r' );
	} else {
		$os = "nocpu";
		$osType = "Unknown OS";
		$osbuild = php_uname ( 'r' );
	}
	return $osType;
}
function winosname() {
	$wUnameB = php_uname ( "v" );
	$wUnameBM = php_uname ( "r" );
	$wUnameB = preg_replace ( "@build @i", "", $wUnameB );
	if ($wUnameBM == "5.0" && ($wUnameB == "2195")) {
		$wVer = "Windows 2000";
	}
	if ($wUnameBM == "5.1" && ($wUnameB == "2600")) {
		$wVer = "Windows XP";
	}
	if ($wUnameBM == "5.2" && ($wUnameB == "3790")) {
		$wVer = "Windows Server 2003";
	}
	if ($wUnameBM == "6.0" && (php_uname ( "v" ) == "build 6000")) {
		$wVer = "Windows Vista";
	}
	if ($wUnameBM == "6.0" && (php_uname ( "v" ) == "build 6001")) {
		$wVer = "Windows Vista SP1";
	}
	return $wVer;
}
if (PHP_OS == "WINNT") {
	$os = "windows";
	$osbuild = php_uname ( 'v' );
} elseif (PHP_OS == "Linux") {
	$os = "linux";
	$osbuild = php_uname ( 'r' );
} else {
	$os = "nocpu";
	$osbuild = php_uname ( 'r' );
}
function ZahlenFormatieren($Wert) {
	if ($Wert > 1099511627776) {
		$Wert = number_format ( $Wert / 1099511627776, 2, ".", "," ) . " TB";
	} elseif ($Wert > 1073741824) {
		$Wert = number_format ( $Wert / 1073741824, 2, ".", "," ) . " GB";
	} elseif ($Wert > 1048576) {
		$Wert = number_format ( $Wert / 1048576, 2, ".", "," ) . " MB";
	} elseif ($Wert > 1024) {
		$Wert = number_format ( $Wert / 1024, 2, ".", "," ) . " kB";
	} else {
		$Wert = number_format ( $Wert, 2, ".", "," ) . " Bytes";
	}
	
	return $Wert;
}
$frei = disk_free_space ( "./" );
$insgesamt = disk_total_space ( "./" );
$belegt = $insgesamt - $frei;
$prozent_belegt = 100 * $belegt / $insgesamt;
	if ($os == "windows") {
		$wmi = new COM ( "Winmgmts://" );
		$cpus = $wmi->execquery ( "SELECT * FROM Win32_Processor" );
		$cpu_string = lang(136).':';
		foreach ( $cpus as $cpu ) {
			$cpu_string .= "" . $cpu->loadpercentage;
		}
		$cpu_string .= '%<br /><img src="' . CLASS_DIR . 'bar.php?rating=' . round ( $cpu->loadpercentage, "2" ) . '" border="0" /><br />';
	} elseif ($os == "linux") {
		function getStat($_statPath) {
			if (trim ( $_statPath ) == '') {
				$_statPath = '/proc/stat';
			}
			
			ob_start ();
			@readfile($_statPath);
			$stat = ob_get_contents ();
			ob_end_clean ();
			
			if (substr ( $stat, 0, 3 ) == 'cpu') {
				$parts = explode ( " ", preg_replace ( "!cpu +!", "", $stat ) );
			} else {
				return false;
			}
			
			$return = array ();
			$return ['user'] = $parts [0];
			$return ['nice'] = $parts [1];
			$return ['system'] = $parts [2];
			$return ['idle'] = $parts [3];
			return $return;
		}
		
		function getCpuUsage($_statPath = '/proc/stat') {
			$time1 = getStat ( $_statPath );
			if (!$time1) return -1;
			sleep ( 1 );
			$time2 = getStat ( $_statPath );
			
			$delta = array ();
			
			foreach ( $time1 as $k => $v ) {
				$delta [$k] = $time2 [$k] - $v;
			}
			
			$deltaTotal = array_sum ( $delta );
			$percentages = array ();
			
			foreach ( $delta as $k => $v ) {
				$percentages [$k] = @round ( $v / $deltaTotal * 100, 2 );
			}
			return $percentages;
		}
		$cpu_string = '';
		if (($cpu = getCpuUsage()) === -1) { $cpu_string = -1; }
		else {
			$cpulast = 100 - $cpu ['idle'];
			$cpu_string .= lang(136).": <span id='cpuload'>" . round ( $cpulast, "0" ) . "</span>%<br />";
			if (extension_loaded('gd') && function_exists('gd_info')) {
				$cpu_string .= '<img src="' . CLASS_DIR . 'bar.php?rating=' . round ( $cpulast, "2" ) . '" border="0" name="cpupercent" id="cpupercent" alt="" />';
			}
			$cpu_string .= '<br />';
		}
	} elseif ($os == "nocpu") {
		$cpu_string = '';
	} else {
		$cpu_string = lang(136).'<br />';
		$cpu_string .= lang(136).": ".lang(137)."<br />";
	}
require(TEMPLATE_DIR.'/sinfo.php');
?>