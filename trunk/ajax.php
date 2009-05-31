<?php
error_reporting(0);
set_time_limit(0);
define('RAPIDLEECH', 'yes');
define('CLASS_DIR', 'classes/');
define('CONFIG_DIR', 'configs/');
// For ajax calls, lets make it use less resource as possible
switch ($_GET['ajax']) {
	case 'server_stats':
		require_once(CONFIG_DIR."config.php");
		if ($server_info && $ajax_refresh) {
			ob_start();
			require(CLASS_DIR."sinfo.php");
			ob_end_clean();
			$AjaxReturn = array();
			$AjaxReturn['FreeSpace'] = ZahlenFormatieren($frei);
			$AjaxReturn['InUse'] = ZahlenFormatieren($belegt);
			$AjaxReturn['InUsePercent'] = round($prozent_belegt,"2");
			$AjaxReturn['DiskPercent'] = round($prozent_belegt,"2");
			$AjaxReturn['DiskSpace'] = ZahlenFormatieren($insgesamt);
			$AjaxReturn['CPULoad'] = round($cpulast,"0");
			$AjaxReturn['CPUPercent'] = round($cpulast, "2");
			echo array_to_json($AjaxReturn);
		}
		break;
	case 'linkcheck':
		require_once(CLASS_DIR.'linkchecker.php');
		if(isset($_POST['debug'])) {
			if($debug == 1) 
				debug();
			else
			echo "Please change the debug mode to <b>1</b>";
			if ($_POST['k'] == 1) {
				$kl = 1;
				$l = 0; 
			}
		}
		if (isset($_POST['submit'])) {
			$alllinks = $_POST['links'];
			$alllinks = explode(" ", $alllinks);
			$alllinks = implode("\n", $alllinks);
			$alllinks = explode("\n", $alllinks);
			$l = 1;
			$x = 1;

			$alllinks = array_unique($alllinks); //removes duplicates
			foreach($alllinks as $link) {
				if (empty($link)) continue;
			   $link = trim($link); 
				if(eregi("^(http)\:\/\/(www\.)?anonym\.to\/\?", $link)){
					$link = explode("?", $link);
					unset($link[0]);
					$link = implode($link, "?");
					  if($kl == 1)
					  echo"<div class=\"n\"><a href=\"$link\"><b>$link</b></a></div>\n";
					  flush();
				}
				
			   if(eregi("^(http)\:\/\/(www\.)?lix\.in\/", $link)){
				  $post = 'tiny='.trim(substr(strstr($link, 'n/'), 2)).'&submit=continue';
				  preg_match('@name="ifram" src="(.+?)"@i', curl($link, $post), $match);
				  $link = $match[1];
					if($kl == 1)
					echo"<div class=\"n\"><a href=\"$link\"><b>$link</b></a></div>\n";
					flush();
			   }
				
				if(eregi("^(http)\:\/\/(www\.)?linkbucks\.com\/link\/" , $link)) {
				   $page = curl($link);
				   preg_match("/<a href=\"(.+)\" id=\"aSkipLink\">/" , $page , $match);
				   $link = $match[1];
					 if($kl == 1)
					 echo"<div class=\"n\"><a href=\"$link\"><b>$link</b></a></div>\n";
					 flush();
				}
			
				 if(eregi("usercash\.com" , $link)) {
					$page = curl($link);
					preg_match("/<TITLE>(.+)<\/TITLE>/" , $page , $match);
					$link = $match[1];
					  if($kl == 1)
					  echo"<div class=\"n\"><a href=\"$link\"><b>$link</b></a></div>\n";
					  flush();
				 }  
				 
				   if(eregi("rapidshare\.com\/users\/" , $link)) {
				  $page = curl($link);
				  preg_match_all("/<a href=\"(.+)\" target=\"_blank\">/" , $page , $match);
				  unset($match[1][0]);
					foreach($match[1] as $link)
					 {
						if($l == 1)
					   {
						  check(trim($link), $x, "You would like to download the following file::" );
						  $x++;
					   }
					if($kl == 1)
					 echo"<div class=\"n\"><a href=\"$link\"><b>$link</b></a></div>\n";
					 flush();
					   }
			   }
			
			if($l == 1) {
				$sites = 
				array(
					array("rapidshare\.com\/files\/", "(FILE DOWNLOAD|This file is larger than 200 Megabyte)"),
					array("megashares\.com\/\?d01=", "Click here to download"),
					array("megaupload\.com/([a-z]{2}\/)?\?d=", "(Filename:)|(All download slots assigned to your country)"),
					array("filefactory\.com\/file\/", "(download link)|(Please try again later)"),
					array("rapidshare\.de\/files\/", "You want to download"),
					array("mediafire\.com\/(download\.php)?\?", "You requested"),
					array("netload\.in\/datei[0-9a-z]{32}\/", "download_load"),	
					array("depositfiles\.com\/([a-z]{2}\/)?files\/", "File Name", "@(com\/files\/)|(com\/[a-z]{2}\/files\/)@i", "com/en/files/"),
					array("sendspace\.com\/file\/", "The download link is located below."),
					array("ifile\.it\/", "Request Ticket"),
					array("usaupload\.net\/d\/", "This is the download page for file"),
					array("badongo\.com\/([a-z]{2}\/)?(file)|(vid)\/", "fileBoxMenu"),
					array("uploading\.com\/files\/", "Download file"),
					array("savefile\.com\/files\/", "link to this file"),
					array("cocoshare\.cc\/[0-9]+\/", "Filesize:"),
					array("axifile\.com\/?", "You have request", "@com\?@i", "com/?"),
					array("(d\.turboupload\.com\/)|(turboupload.com\/download\/)", "(Please wait while we prepare your file.)|(You have requested the file)"),
					array("files\.to\/get\/", "You requested the following file"),
					array("gigasize\.com\/get\.php\?d=", "Downloaded"),
					array("ziddu\.com\/", "Download Link"),
					array("zshare\.net\/(download|audio|video)\/", "Last Download"),
					array("uploaded\.to\/(\?id=|file\/)", "Filename:"),
					array("filefront\.com\/", "http://static4.filefront.com/ffv6/graphics/b_download_still.gif"),
					array("uploadpalace\.com\/[a-zA-Z]{2}\/file\/[0-9]+\/", "Filename:"),
					array("speedyshare\.com\/[0-9]+\.html", "\/data\/"),
					array("momupload\.com\/files\/", "You want to download the file"),
					array("rnbload\.com\/file/" , "Filename:"),
					array("ifolder\.ru\/[0-9]+", "ints_code"),
					array("adrive\.com\/public\/", "view"),
					array("easy-share\.com" , "file url:"),
					array("bitroad\.net\/download\/[0-9a-z]+\/", "File:"),
					array("megarotic\.com/([a-z]{2}\/)?\?d=", "(Filename:)|(All download slots assigned to your country)"),  
					array("egoshare\.com" , "You have requested"),
					array("flyupload\.flyupload.com\/get\?fid" , "Download Now"),

					array("megashare\.com\/[0-9]+", "Free")	
				);
				
				foreach($sites as $site) {
					if(eregi($site[0], $link)) {
						check(trim($link), $x, $site[1], $site[2], $site[3]);
						$x++;
					}
				}
				
				if($x > $maxlinks) {
					echo "<p style=\"text-align:center\">Maximum No ($maxlinks) Of links have been reached.";
					exit();
				}
				   }
			}
			$time = explode(" ", microtime());
			$time = $time[1] + $time[0];
			$endtime = $time;
			$totaltime = ($endtime - $begintime);
			$x--;
			$plural = ($x == 1) ? "" : "s";
			($fgc == 0) ? $method = 'cURL' : $method = 'file_get_contents';
			echo "<p style=\"text-align:center\">$x Link$plural checked in $totaltime seconds. (Method: <b>$method</b>)</p>";
		}
		break;
}

function array_to_json( $array ){

    if( !is_array( $array ) ){
        return false;
    }

    $associative = count( array_diff( array_keys($array), array_keys( array_keys( $array )) ));
    if( $associative ){

        $construct = array();
        foreach( $array as $key => $value ){

            // We first copy each key/value pair into a staging array,
            // formatting each key and value properly as we go.

            // Format the key:
            if( is_numeric($key) ){
                $key = "key_$key";
            }
            $key = "'".addslashes($key)."'";

            // Format the value:
            if( is_array( $value )){
                $value = array_to_json( $value );
            } else if( !is_numeric( $value ) || is_string( $value ) ){
                $value = "'".addslashes($value)."'";
            }

            // Add to staging array:
            $construct[] = "$key: $value";
        }

        // Then we collapse the staging array into the JSON form:
        $result = "{ " . implode( ", ", $construct ) . " }";

    } else { // If the array is a vector (not associative):

        $construct = array();
        foreach( $array as $value ){

            // Format the value:
            if( is_array( $value )){
                $value = array_to_json( $value );
            } else if( !is_numeric( $value ) || is_string( $value ) ){
                $value = "'".addslashes($value)."'";
            }

            // Add to staging array:
            $construct[] = $value;
        }

        // Then we collapse the staging array into the JSON form:
        $result = "[ " . implode( ", ", $construct ) . " ]";
    }

    return $result;
}
?>