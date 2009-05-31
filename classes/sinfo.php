<hr>
<SCRIPT LANGUAGE="JavaScript"> 
<!-- Begin 
    function getthedate(){ 
        var mydate=new Date(); 
        var hours=mydate.getHours(); 
        var minutes=mydate.getMinutes(); 
        var seconds=mydate.getSeconds(); 
        var dn="AM"; 
        if (hours>=12) dn="PM"; 
        if (hours>12) hours=hours-12;        
        if (hours==0) hours=12; 
		if (hours<=9) hours="0"+hours; 
        if (minutes<=9) minutes="0"+minutes; 
        if (seconds<=9)    seconds="0"+seconds; 
        

        var cdate="<span style=\"color:#994A1D\">Local Time:</span> &nbsp;&nbsp;&nbsp;<span style=\"color:#999\">"+hours+":"+minutes+":"+seconds+" "+dn+"</span><BR>";
        if (document.all) 
            document.all.clock.innerHTML=cdate; 
        else if (document.getElementById) 
            document.getElementById("clock").innerHTML=cdate; 
        else 
            document.write(cdate); 
		setTimeout("getthedate()",1000); 
    } 
    if (!document.all&&!document.getElementById) { getthedate(); js_clock(); }

    function goforit(){ 
        if (document.all||document.getElementById) {
			setTimeout("getthedate()",1000); 
			js_clock();
		}
    }
	var clock_hours = <?php echo date("G"); ?>;
	var clock_minutes = <?php echo date("i"); ?>;
	var clock_seconds = <?php echo date("s"); ?>;
	function js_clock(){
		clock_seconds++;
		if (clock_seconds == 60) {
			clock_seconds = 0;
			clock_minutes++;
			if (clock_minutes == 60) {
				clock_minutes = 0;
				clock_hours++;
				if (clock_hours == 24) {
					clock_hours = 0;
				}
			}
		}
		var disp_minutes = clock_minutes;
		var disp_seconds = clock_seconds;
		var disp_hours = clock_hours;
		var clock_suffix = "AM";
		if (clock_hours > 11){
			clock_suffix = "PM";
			disp_hours = disp_hours - 12;
		}
		if (disp_hours == 0){
			disp_hours = 12;
		}
		if (disp_hours < 10){
			disp_hours = "0" + disp_hours;
		}
		if (clock_minutes < 10){
			disp_minutes = "0" + disp_minutes;
		}
		if (clock_seconds < 10){
			disp_seconds = "0" + disp_seconds;
		}
		var clock_div = document.getElementById('server');
		clock_div.innerHTML = "<span style=\"color:#FF8700\">Server Time:</span> &nbsp;&nbsp;&nbsp;<span style=\"color:#999\">" + disp_hours + ":" + disp_minutes + ":" + disp_seconds + " " + clock_suffix+"</span><BR>";
		setTimeout("js_clock()", 1000);
		}
    window.onload=goforit; 
// End --> 
</SCRIPT>
<table cellspacing="2" cellpadding="2">
<tr>
<?php
if (!isset($servername) || $servername == "")
{
    $theservername = $_SERVER['SERVER_NAME'];
}
else
{
    $theservername = $servername;
}
if (!isset($customos) || $customos == "")
{
    $osname = checkos();
}
else
{
    $os = "nocpu";
    $osname = $customos;
}
if (php_sapi_name() == "apache2handler")
{
    $httpapp = "Apache";
}
else
{
    $httpapp = php_sapi_name();
}
function checkos()
{
    if (substr(PHP_OS, 0, 3) == "WIN")
    {
        $osType = winosname();
        $osbuild = php_uname('v');
        $os = "windows";
    } elseif (PHP_OS == "FreeBSD")
    {
        $os = "nocpu";
        $osType = "FreeBSD";
        $osbuild = php_uname('r');
    } elseif (PHP_OS == "Darwin")
    {
        $os = "nocpu";
        $osType = "Apple OS X";
        $osbuild = php_uname('r');
    } elseif (PHP_OS == "Linux")
    {
        $os = "linux";
        $osType = "Linux";
        $osbuild = php_uname('r');
    }
    else
    {
        $os = "nocpu";
        $osType = "Unknown OS";
        $osbuild = php_uname('r');
    }
    return $osType;
}
function winosname()
{
    $wUnameB = php_uname("v");
    $wUnameBM = php_uname("r");
    $wUnameB = eregi_replace("build ", "", $wUnameB);
    if ($wUnameBM == "5.0" && ($wUnameB == "2195"))
    {
        $wVer = "Windows 2000";
    }
    if ($wUnameBM == "5.1" && ($wUnameB == "2600"))
    {
        $wVer = "Windows XP";
    }
    if ($wUnameBM == "5.2" && ($wUnameB == "3790"))
    {
        $wVer = "Windows Server 2003";
    }
    if ($wUnameBM == "6.0" && (php_uname("v") == "build 6000"))
    {
        $wVer = "Windows Vista";
    }
    if ($wUnameBM == "6.0" && (php_uname("v") == "build 6001"))
    {
        $wVer = "Windows Vista SP1";
    }
    return $wVer;
}
if (PHP_OS == "WINNT")
{
    $os = "windows";
    $osbuild = php_uname('v');
} elseif (PHP_OS == "Linux")
{
    $os = "linux";
    $osbuild = php_uname('r');
}
else
{
    $os = "nocpu";
    $osbuild = php_uname('r');
}
function ZahlenFormatieren($Wert)
{
    if ($Wert > 1099511627776)
    {
        $Wert = number_format($Wert / 1099511627776, 2, ".", ",") . " TB";
    } elseif ($Wert > 1073741824)
    {
        $Wert = number_format($Wert / 1073741824, 2, ".", ",") . " GB";
    } elseif ($Wert > 1048576)
    {
        $Wert = number_format($Wert / 1048576, 2, ".", ",") . " MB";
    } elseif ($Wert > 1024)
    {
        $Wert = number_format($Wert / 1024, 2, ".", ",") . " kB";
    }
    else
    {
        $Wert = number_format($Wert, 2, ".", ",") . " Bytes";
    }

    return $Wert;
}
$frei = disk_free_space("./");
$insgesamt = disk_total_space("./");
$belegt = $insgesamt - $frei;
$prozent_belegt = 100 * $belegt / $insgesamt;
?>
<td align="left" valign="top" style="color:ccc"><span style="color:#FF8700">Server Space:</span><br>
In Use = <b><span id="inuse"><?php echo ZahlenFormatieren($belegt); ?></span></b>(<span id="inusepercent"><?php echo round($prozent_belegt,"2"); ?></span> %)<br>
<img src="<?php echo CLASS_DIR ?>bar.php?rating=<?php echo round($prozent_belegt,"2"); ?>" border="0" name="diskpercent" id="diskpercent"><br>
Free Space = <b><span id="freespace"><?php echo ZahlenFormatieren($frei); ?></span></b><br>
Disk Space = <b><span id="diskspace"><?php echo ZahlenFormatieren($insgesamt); ?></span></b></td>
<?php
{
    if ($os == "windows")
    {
        $wmi = new COM("Winmgmts://");
        $cpus = $wmi->execquery("SELECT * FROM Win32_Processor");
        echo '<td align="left" valign="top" style="color:ccc"><span style="color:#FF8700">CPU:</span><br>';
        echo 'CPU Load:';
        foreach ($cpus as $cpu)
        {
            echo "" . $cpu->loadpercentage . "%<br />";
        }
        echo '<img src="'.CLASS_DIR.'bar.php?rating=' . round($cpu->loadpercentage, "2") . '" border="0"><br>';
		echo '<span id="server"></span>';
		echo '<span id="clock"></span>';
		echo '</td>';
    } elseif ($os == "linux")
    {
        function getStat($_statPath)
        {
            if (trim($_statPath) == '')
            {
                $_statPath = '/proc/stat';
            }

            ob_start();
            passthru('cat ' . $_statPath);
            $stat = ob_get_contents();
            ob_end_clean();


            if (substr($stat, 0, 3) == 'cpu')
            {
                $parts = explode(" ", preg_replace("!cpu +!", "", $stat));
            }
            else
            {
                return false;
            }

            $return = array();
            $return['user'] = $parts[0];
            $return['nice'] = $parts[1];
            $return['system'] = $parts[2];
            $return['idle'] = $parts[3];
            return $return;
        }

        function getCpuUsage($_statPath = '/proc/stat')
        {
            $time1 = getStat($_statPath) or die("getCpuUsage(): couldn't access STAT path or STAT file invalid\n");
            sleep(1);
            $time2 = getStat($_statPath) or die("getCpuUsage(): couldn't access STAT path or STAT file invalid\n");

            $delta = array();

            foreach ($time1 as $k => $v)
            {
                $delta[$k] = $time2[$k] - $v;
            }

            $deltaTotal = array_sum($delta);
            $percentages = array();

            foreach ($delta as $k => $v)
            {
                $percentages[$k] = round($v / $deltaTotal * 100, 2);
            }
            return $percentages;
        }
        $cpu = getCpuUsage();
        $cpulast = 100 - $cpu['idle'];
        echo '<td align="left" valign="top" style="color:ccc"><span style="color:#FF8700">CPU:</span><br>';
        echo "CPU Load: <span id='cpuload'>" . round($cpulast,"0") . "</span>%<br>";
        echo '<img src="'.CLASS_DIR.'bar.php?rating=' . round($cpulast, "2") . '" border="0" name="cpupercent" id="cpupercent"><br>';
		echo '<span id="server"></span><span id="clock"></span>';
		echo '</td>';
    } elseif ($os == "nocpu")
    {
        echo "";
    }
    else
    {
        echo 'CPU Load<br>';
        echo "CPU Load: There Was An Error.<br>";
    }
}
?>
</tr>
</table>