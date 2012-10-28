<?php
error_reporting(0);
set_time_limit(0);
define('RAPIDLEECH', 'yes');
define('CLASS_DIR', 'classes/');
define('CONFIG_DIR', 'configs/');
require_once(CONFIG_DIR."config.php");
require_once(CLASS_DIR . 'other.php');
define ( 'TEMPLATE_DIR', 'templates/'.$options['template_used'].'/' );
$nn = "\r\n";
// For ajax calls, lets make it use less resource as possible
switch ($_GET['ajax']) {
	case 'server_stats':
		if ($options['server_info'] && $options['ajax_refresh']) {
			ob_start();
			require(CLASS_DIR.'sinfo.php');
			ob_end_clean();
			$AjaxReturn = array();
			$AjaxReturn['FreeSpace'] = ZahlenFormatieren($frei);
			$AjaxReturn['InUse'] = ZahlenFormatieren($belegt);
			$AjaxReturn['InUsePercent'] = round($prozent_belegt, '2');
			$AjaxReturn['DiskPercent'] = round($prozent_belegt, '2');
			$AjaxReturn['DiskSpace'] = ZahlenFormatieren($insgesamt);
			$AjaxReturn['CPULoad'] = round($cpulast, '0');
			$AjaxReturn['CPUPercent'] = round($cpulast, '2');
			echo array_to_json($AjaxReturn);
		}
		break;
	case 'linkcheck':
		require_once(CLASS_DIR.'linkchecker.php');
		$glinks = array();
		if(!empty($_POST['debug'])) {
			if($debug == 1)
			debug();
			else
			echo lang(16);
		}
		if (isset($_POST['submit'])) {
			$alllinks = $_POST['links'];
			$alllinks = explode(' ', $alllinks);
			$alllinks = implode("\n", $alllinks);
			$alllinks = explode("\n", $alllinks);

			$x = $x2 = 1;
			$l = ($_POST['k']) ? false : true;
			$d = ($_POST['d']) ? false : true;
			$dk = (!$l && !$d) ? true : false;

			$alllinks = array_unique($alllinks); //removes duplicates
			echo "<div id='listlinks'>\n";
			foreach($alllinks as $link) {
				if (empty($link)) continue;
				$x2++;
				$link = trim($link);
				$Checked = $skip = $Kl = false;

				if (preg_match("/^(http)\:\/\/(www\.)?anonym\.to\/\?/i", $link)) {
					$link = explode('?', $link);
					unset($link[0]);
					$link = implode($link, '?');
					$Kl = 'anonym.to';
				}

				if (preg_match('@^http://(www\.)?((adf\.ly)|([jq]\.gs)|([9u]\.bb))/((\d+/.+)|([^/|\r|\n]+))@i', $link, $m)) {
					$page = curl($m[0]);
					if (!preg_match("@window\.location = '(((https?:)|(/go/))[^']+)'@i", $page, $match)) 
						if (!preg_match("@self\.location = '(((https?:)|(/go/))[^']+)'@i", $page, $match)) 
							preg_match("@var url = '(((https?:)|(/go/))[^']+)'@i", $page, $match);
					if (!empty($match[1]) && $match[1] != $link) {
						if (preg_match('@^(?:https?://([^/]+\.)?((adf\.ly)|([jq]\.gs)|([9u]\.bb)))?/go/\w+/([^/|\?]+)@i', $match[1], $b64redir)) $match[1] = base64_decode(urldecode($b64redir[count($b64redir)-1]));
						$link = $match[1];
						$Kl = 'adf.ly';
					} else {
						$link = $m[0].' - ERROR.';
						$skip = true;
					}
				}

				if (!$skip && preg_match('@^http://(www\.)?zpag\.es/((\d+/.+)|([^/|\r|\n]+))@i', $link, $m)) {
					$page = curl($m[0]);
					preg_match('/window\.location = "(https?:[^"]+)"/i', $page, $match);
					if (!empty($match[1]) && $match[1] != $link) {
						$link = $match[1];
						$Kl = 'zpag.es';
					} else {
						$link = $m[0].' - ERROR.';
						$skip = true;
					}
				}

				if (!$skip && preg_match('@^http://(?:www\.)?adfoc\.us/(?:serve/\?id=)?(\d+)@i', $link, $m)) {
					$page = curl('http://adfoc.us/serve/click/?id='.$m[1].'&servehash=1&timestamp='.time(), 0, 0);
					if (!preg_match('@Location: (https?://[^\r|\n]+)@i', $page, $match)) {
						$page = curl($m[0], 0, 0);
						$page = str_ireplace('http://adfoc.us/serve/click/', '', $page);
						preg_match('@var click_url = "(https?://[^\"\s\t<>]+)"@i', $page, $match);
					}
					if (!empty($match[1]) && $match[1] != $link) {
						$link = $match[1];
						$Kl = 'adfoc.us';
					} else {
						$link = $m[0].' - ERROR.';
						$skip = true;
					}
				}

				if (!$skip && preg_match('@^http://(www\.)?bc\.vc/(\w+)@i', $link, $m)) {
					$page = curl($m[0]);
					if (!preg_match('@Location: (https?://[^\r\n]+)@i', $page, $match) && preg_match('@aid:(\d+)\s?,\s?lid:(\d+)\s?,\s?oid:(\d+)@i', $page, $args)) {
						$post = array('opt'=>'check_log','args%5Baid%5D'=>$args[1],'args%5Blid%5D'=>$args[2],'&args%5Boid%5D'=>$args[3], 'args%5Bref%5D'=>'');
						$L = 'http://bc.vc/fly/ajax.fly.php';
						$page = curl($L, $post);
						$cookie = GetCookiesArr($page);
						$page = curl($L, $post, $cookie);
						if (stripos($page, '"message":false') !== false) {
							$page = curl($L, $post, $cookie);
							$cookie = GetCookiesArr($page, $cookie);
						}
						$post['opt'] = 'make_log';
						sleep(6); // (rand(5,7));
						$page = curl($L, $post, $cookie);
						preg_match('@"url":"(https?:[^\r\n\"\}\{]+)"@i', $page, $match);
						if (!empty($match[1])) $match[1] = stripslashes($match[1]);
						unset($L);
					}
					if (!empty($match[1]) && $match[1] != $link) {
						$link = $match[1];
						$Kl = 'bv.vc';
					} else {
						$link = $m[0].' - ERROR.';
						$skip = true;
					}
				}

				if (!$skip && preg_match('/^http:\/\/(www\.)?lnk\.co\/\w+/i', $link, $m)) {
					$page = curl($m[0],0,0,0);
					if (!preg_match('/id=\'linkurl\' href="(https?:[^"]+)"/i', $page, $match)) 
						if (!preg_match("/id='urlholder' value='(https?:[^']+)'/i", $page, $match)) 
							preg_match('@Location: (https?://[^\r|\n]+)@i', $page, $match);
					if (!empty($match[1]) && $match[1] != $link) {
						$link = $match[1];
						$Kl = 'lnk.co';
					} else {
						$link = $m[0].' - ERROR.';
						$skip = true;
					}
				}

				if (!$skip && preg_match('@^http://(\w+\.)?linkbucks\.com/?(link/[^/\r\n]+)?@i' , $link, $m)) {
					$page = curl($m[0],0,0,0);
					sleep(2); // linkbucks now show a flood warning, waiting 2 seconds.
					if (!preg_match("/(?:(?:Linkbucks)|(?:Lbjs)).TargetUrl = '(https?:[^']+)'/i" , $page , $match)) 
						if (!preg_match('/<iframe id="content" src="(https?:[^"]+)"/i' , $page , $match)) 
							preg_match("/Location: (https?:.+)/i", $page, $match);
					if (!empty($match[1]) && $match[1] != $link) {
						$link = $match[1];
						$Kl = 'linkbucks.com';
					} else {
						$link = $m[0].' - ERROR.';
						$skip = true;
					}
				}

				if (!$skip && preg_match('@^http://((\d+\.)|(\w+\.))?((rsmonkey\.com)|((\w+\.)?canhaz\.it))(/\d+)?@i', $link, $m)) {
					$page = curl($m[0]);
					preg_match("/top\.location\.replace\('(https?:[^']+)'\)/i", $page, $match);
					if (!empty($match[1]) && $match[1] != $link) {
						$link = $match[1];
						$Kl = 'rsmonkey.com';
					} else {
						$link = $m[0].' - ERROR.';
						$skip = true;
					}
				}

				if (!$skip && preg_match('@^http://(?:www\.)?((?:linksafe\.me)|(?:safelinking\.net))/d/([^/\W]+(?:/[^/\r\n]+)?)/?@i', $link, $m)) {
					$page = curl($m[0],0,0,0);
					if (!preg_match('/Location: (https?:.+)/i', $page, $match)) 
						preg_match('/window\.location="(https?:[^"]+)"/i', $page, $match);
					if (!empty($match[1]) && $match[1] != $m[0]) {
						$link = $match[1];
						$Kl = $m[1];
					} else {
						$link = $m[0].' - ERROR.';
						$skip = true;
					}
				}

				if (!$skip && preg_match('@^http://(?:www\.)?multiupload\.nl/(\w{2}_)?\w+@i', $link, $m)) {
					$page = curl($m[0],0,0,0);
					if (stristr($page, "the link you have clicked is not available")) {
						$link = $m[0].' - Not found.';
						$skip = true;
					}
					if (empty($m[1]) && !$skip) {
						if (preg_match_all('@(http://(?:www\.)?multiupload\.nl/(?:\w{2}_\w+))</a@i', $page, $match)) {
							foreach ($match[1] as $link) showlink($link, 0, 3, "Removed folder: multiupload.nl");
							$Checked = true;
							CountandCheck($x);
						}
						else $link = $m[0].' - ERROR.';
					} elseif (!$skip) {
						preg_match('/Location: (https?:.+)/i', $page, $match);
						if (!empty($match[1]) && $match[1] != $m[0]) {
							$link = $match[1];
							$Kl = 'multiupload.nl';
						} else $link = $m[0].' - ERROR.';
					}
				}

				// Some hosts needs other way to check file and get the filesize
				if ($l && !$skip && preg_match('@^https?://(?:[^/]+\.)?mediafire\.com/(?:(?:imageview\.php)|(?:view/))?\?(?:quickkey=)?(\w+)@i', $link, $m)) {
					$page = curl('http://www.mediafire.com/api/file/get_info.php?quick_key='.$m[1],0,0,0,0,0);
					$link = 'http://www.mediafire.com/?'.$m[1];
					$size = '';
					if (stripos($page, '<result>Success</result>') !== false && preg_match('@<size>(\d+)</size>@i', $page, $sz) && $sz[1] > 0) {
						$chk = 1;
						$size = explode(' ', bytesToKbOrMbOrGb($sz[1]));
						$size[0] = round($size[0], 2);
						$size = implode(' ', $size);
					} elseif (stripos($page, '<message>Access denied</message>') !== false) $chk = 2;
					else {
						$chk = 0;
						$page = curl('http://www.mediafire.com/api/folder/get_content.php?folder_key='.$m[1].'&content_type=files',0,0,0,0,0);
						if (stripos($page, '<result>Success</result>') !== false && preg_match_all('@<quickkey>([^<]+)</quickkey>@i', $page, $qids)) {
							foreach ($qids[1] as $qid) showlink("http://www.mediafire.com/?$qid", 0, 3, 'Removed folder: mediafire.com');
							$skip = true;
						} elseif (stripos($page, '<message>Access denied</message>') !== false) $chk = 2;
					}

					if (!$skip) {
						showlink($link, $size, $chk);
						if ($chk == 1) $glinks[$link] = $size;
					}
					$Checked = $skip = true;
					CountandCheck($x);
				}

				if ($l && !$skip && preg_match('@^https?://(?:[^/]+\.)?rapidshare\.com/(?:(?:files/(\d+)/([^/#\r\n]+))|(?:\??#!download\|[^\|]+\|(\d+)\|([^\|\r\n]+)))@i', $link, $m)) { // O.O
					if (!empty($m[1])) {
						$m['id'] = $m[1];
						$m['filename'] = $m[2];
					} else {
						$m['id'] = $m[3];
						$m['filename'] = $m[4];
					}
					$page = curl('http://api.rapidshare.com/cgi-bin/rsapi.cgi', "sub=checkfiles&files={$m['id']}&filenames=".$m['filename'],0,0,0,0);
					$page = explode(',', $page);
					if ($page[2] > 0) {
						if ($page[4] == 1) $chk = 1;
						elseif ($page[4] == 3) $chk = 2;
						else $chk = 0;
					} else $chk = 0;

					$link = "http://rapidshare.com/files/{$page[0]}/".$page[1];
					if ($chk == 1) {
						$size = explode(' ', bytesToKbOrMbOrGb($page[2]));
						$size[0] = round($size[0], 2);
						$size = implode(' ', $size);
						$glinks[$link] = $size;
					} else $size = 0;
					showlink($link, $size, $chk);
					$Checked = $skip = true;
					CountandCheck($x);
				}

				if ($l && !$skip && preg_match('@^https?://(?:[^/]+\.)?share-online\.biz/(?:(?:download\.php\?id=)|(?:dl/))(\w+)@i', $link, $m)) {
					$page = curl('http://api.share-online.biz/linkcheck.php?links='.$m[1],0,0,0,0,0);
					$page = explode(';', $page);
					$page = array_map('trim', $page);

					$link = 'http://www.share-online.biz/dl/'.$m[1];
					$size = '';
					switch ($page[1]) {
						case 'OK' : $chk = 1; break;
						case 'DELETED' : case 'NOT FOUND' : $chk = 0; break;
						default : $chk = 4; break;
					}

					if ($chk == 1) {
						$size = explode(' ', bytesToKbOrMbOrGb($page[3]));
						$size[0] = round($size[0], 2);
						$size = implode(' ', $size);
						$glinks[$link] = $size;
					}
					showlink($link, $size, $chk);
					$Checked = $skip = true;
					CountandCheck($x);
				}

				if ($l && !$skip) {
					foreach($sites as $site) {
						if (!empty($site['link']) && preg_match('@'.$site['link'].'@i', $link)) {
							$szregex = $pattern = $replace = '';
							$opt = array();
							if (($d || $dk) && !empty($site['szregex'])) $szregex = $site['szregex'];
							if (isset($site['pattern'])) $pattern = $site['pattern'];
							if (isset($site['replace'])) $replace = $site['replace'];
							if (array_key_exists('options', $site) && is_array($site['options'])) $opt = $site['options'];
							list($chk, $size) = check($link, $x, $site['regex'], $szregex, $pattern, $replace, $opt);
							if ($chk == 1) $glinks[$link] = $size;
							flush();//ob_flush();
							$Checked = true;
							CountandCheck($x);
						}
					}
				}

				if (($d || $dk) && !$Checked && $Kl != false) {
					showlink($link, 0, 3, "Removed: $Kl");
					CountandCheck($x);
					flush();
				} elseif($d && !$Checked) {
					showlink($link, 0, 4, 'Unknown Link');
					//CountandCheck($x); //Count?
					flush();
				}
			}
			echo "</div>\n";
			$time = explode(' ', microtime());
			$time = $time[1] + $time[0];
			$endtime = $time;
			$totaltime = ($endtime - $begintime);
			$x--;$x2--;
			$plural = ($x == 1) ? '' : lang(19);
			($options['fgc'] == 0) ? $method = 'cURL' : $method = 'file_get_contents';
			echo '<p style="text-align:center">';
			printf(lang(18),$x,$plural,$totaltime,$method);
			echo "</p>\n";
			if (count($glinks) > 1 && @is_file('audl.php')) {
				$links = '';
				$fsize = 0;
				$afhs = true; // 
				foreach ($glinks as $lnk => $sz) {
					$links .= "$lnk\n";
					if (!empty($sz) && $sz > 0) {
						$sz = explode(' ', strtoupper($sz));
						switch ($sz[1]) { // KbOrMbOrGbToBytes :D
							case 'GB': $sz[0] *= 1024;
							case 'MB': $sz[0] *= 1024;
							case 'KB': $sz[0] *= 1024;
						}
						$fsize += $sz[0];
					} else $afhs = false;
				}
				$fsize = explode(' ', bytesToKbOrMbOrGb($fsize));
				$fsize[0] = round($fsize[0], 2);
				$fsize = implode(' ', $fsize);
				$fsize = (!$afhs ? '&gt;' : '').$fsize;
				echo "<div style='text-align:center;'>\n";
				echo "<br /><form action='".((($pos = strrpos($_SERVER['SCRIPT_NAME'], '/')) !== false) ? substr($_SERVER['SCRIPT_NAME'], 0, $pos + 1).'audl.php?GO=GO' : 'audl.php?GO=GO')."' method='POST' >\n";
				echo "<input type='hidden' name='links' value='" . $links . "'>\n";
				//$key_array = array("useproxy", "proxy", "proxyuser", "proxypass", "premium_acc", "premium_user", "premium_pass", "cookieuse", "cookie");
				//foreach ($key_array as $v) if (array_key_exists($v, $_GET)) echo "<input type='hidden' name='".$v."' value='".$_GET[$v]."' />\n";
				if ($x == $x2) $btext = "Send links to AUDL ($fsize)";
				else $btext = "Send working links to AUDL ($fsize)";
				echo "<input type='submit' value='$btext' />\n";
				echo "</form>\n";
				echo "</div>\n";
			}
			flush();
		}
		break;
}

function CountandCheck(&$x) {
	global $maxlinks;
	$x++;
	if($x > $maxlinks) {
		echo '<p style="text-align:center; color: red; background-color: #fec; padding: 3px; border: 2px solid $FFAA00; line-height: 25px">';
		printf(lang(17), $maxlinks);
		echo("</p></div>\n");
		include(TEMPLATE_DIR.'/footer.php');
		exit();
	}
}

function array_to_json($array){

	if (!is_array($array)) return false;

	$associative = count(array_diff(array_keys($array), array_keys(array_keys($array))));
	if ($associative) {
		$construct = array();
		foreach ($array as $key => $value){

			// We first copy each key/value pair into a staging array,
			// formatting each key and value properly as we go.

			// Format the key:
			if (is_numeric($key)) $key = "key_$key";
			$key = "'".addslashes($key)."'";

			// Format the value:
			if (is_array($value)) $value = array_to_json($value);
			elseif(!is_numeric($valu ) || is_string($value)) $value = "'".addslashes($value)."'";

			// Add to staging array:
			$construct[] = "$key: $value";
		}

		// Then we collapse the staging array into the JSON form:
		$result = "{ " . implode(", ", $construct) . " }";

	} else { // If the array is a vector (not associative):

		$construct = array();
		foreach ($array as $value){

			// Format the value:
			if (is_array($value)) $value = array_to_json($value);
			elseif (!is_numeric($value) || is_string($value)) $value = "'".addslashes($value)."'";

			// Add to staging array:
			$construct[] = $value;
		}

		// Then we collapse the staging array into the JSON form:
		$result = "[ " . implode(", ", $construct) . " ]";
	}

	return $result;
}

?>