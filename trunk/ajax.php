<?php
error_reporting(0);
set_time_limit(0);
define('RAPIDLEECH', 'yes');
define('CLASS_DIR', 'classes/');
define('CONFIG_DIR', 'configs/');
require_once(CONFIG_DIR."config.php");
require_once(CLASS_DIR . 'other.php');
define ( 'TEMPLATE_DIR', 'templates/'.$options['template_used'].'/' );
// For ajax calls, lets make it use less resource as possible
switch ($_GET['ajax']) {
	case 'server_stats':
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
			echo lang(16);
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
					foreach($sites as $site) {
						if(preg_match('@'.$site['link'].'@i', $link)) {
							$pattern = '';
							$replace = '';
							if (isset($site['pattern'])) $pattern = $site['pattern'];
							if (isset($site['replace'])) $replace = $site['replace'];
							check(trim($link), $x, $site['regex'], $pattern, $replace);
							$x++;
						}
					}

					if($x > $maxlinks) {
						echo "<p style=\"text-align:center\">";
						printf(lang(17),$maxlinks);
						echo('</p>');
						include(TEMPLATE_DIR.'/footer.php');
						exit();
					}
				}
			}
			$time = explode(" ", microtime());
			$time = $time[1] + $time[0];
			$endtime = $time;
			$totaltime = ($endtime - $begintime);
			$x--;
			$plural = ($x == 1) ? "" : lang(19);
			($fgc == 0) ? $method = 'cURL' : $method = 'file_get_contents';
			echo "<p style=\"text-align:center\">";
			printf(lang(18),$x,$plural,$totaltime,$method);
			echo "</p>";
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