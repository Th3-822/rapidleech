<?php
//Copyright Dman :p this has been coded by dman biatches!!!
//Optimized by zpikdum :D
//Moded by eqbal ;)
//Lets calulate the time required.
$time = explode(' ', microtime());
$time = $time[1] + $time[0];
$begintime = $time;
//User Enabled settings 
$debug = 1; // change it to one to enable it.
//Override PHP's stardard time limit
set_time_limit(120);
$maxlinks = 300;
$lcver = 301;
$fgc = (extension_loaded("curl") ? 0 : 1);
//Lets use this as a function to visit the site.	
function curl($link, $post='0')
{
	global $fgc;
	
    if($fgc == 1)
	{
        file_get_contents($link);
	}
    else 
	{

		$ch = curl_init($link);
		curl_setopt($ch, CURLOPT_HEADER, 0);
	    if(eregi("megashares\.com" , $link))
		{
            curl_setopt($ch, CURLOPT_COOKIE, 1);
		    curl_setopt($ch, CURLOPT_COOKIEJAR, "1");
		    curl_setopt($ch, CURLOPT_COOKIEFILE, "1");
		}
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		if($post != '0') 
		{

			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		}
		$page = curl_exec($ch);
		curl_close($ch);
		return($page);		
	}

}
	
	
function check($link, $x, $regex, $pattern='', $replace='') 
{
    if(!empty($pattern)) 
    {


	    $link = preg_replace($pattern, $replace, $link);
    }
	$page = curl($link);
	$link = htmlentities($link, ENT_QUOTES);
	flush();
	ob_flush();
	
	if($_POST['d'] && eregi($regex, $page)) 
	{

		echo "<div class=\"g\"><a href=\"$link\"><b>$link</b></a></div>\n";  
	} 
	elseif($_POST['d'] && eregi("The file you are trying to access is temporarily unavailable.", $page)) 
	{

		echo "<div class=\"y\"><a href=\"$link\"><b>$link</b></a></div>\n";
	}
	elseif($_POST['d'] && !eregi($regex, $page)) 
	{

		echo "<div class=\"r\"><a href=\"$link\"><b>$link</b></a></div>\n";			
	}
	elseif(!$_POST['d'] && eregi($regex, $page)) 
	{

		echo "<div class=\"g\">$x: Active: <a href=\"$link\"><b>$link</b></a></div>\n";  
	} 
	elseif(!$_POST['d'] && eregi("The file you are trying to access is temporarily unavailable.", $page)) 
	{

		echo "<div class=\"y\">$x: Unavailable: <a href=\"$link\"><b>$link</b></a></div>\n";
	}
	else 
	{

		echo "<div class=\"r\">$x: Dead: <a href=\"$link\"><b>$link</b></a></div>\n";			
	}
}
	
function debug() {
	echo '<div style="text-align:left; margin:0 auto; width:450px;">';
	if ( !extension_loaded("curl") )
	echo "You need to load/activate the cURL extension (http://www.php.net/cURL) or you can set $fgc = 1 in config.php.<br/>";
		else
		echo "<b>cURL is enabled</b><br/>";
	if( PHP_VERSION < 5 ){ 
	echo "PHP version 5 is recommended although it is not obligatory<br/>";
	}
	//echo "This will display all of your PHP info on your server<br/>";
	echo "Check if your safe mode is turned off as the script cannot work with safe mode on<br/>";
	echo "</div>";
}
?>