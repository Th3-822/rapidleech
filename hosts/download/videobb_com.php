<?php

if (!defined('RAPIDLEECH')) {
	require_once ("index.html");
	exit;
}

class videobb_com extends DownloadClass {
	public function Download($link) {
		$link = str_replace('videobb.com/video/', 'videobb.com/watch_video.php?v=', $link);
		$page = $this->GetPage($link);
		is_present ($page, "Video is not available", "Video is not available - " . cut_str($page, '<font size="4">', '</font>'));

		$page = $this->GetPage(str_replace(array('videobb.com/video/','videobb.com/watch_video.php?v='), 'videobb.com/player_control/settings.php?v=', $link));
		is_notpresent ($page, "you still have quota left", "Error: Still have quota left?");

		if (!preg_match('/"video":\{"title":"([^"]+)"/i', $page, $tl)) html_error('Video title not found.');
		if (!preg_match_all('/\{"d":(false|true),"l":"([^"]+)","u":"([^"]+)"/i', $page, $st)) html_error('Video stream(s) not found.'); //Get streams

		$akey = $this->vbb_decode($page);

		$stream = array();
		for ($i = 0; $i < count($st[0]); $i++) {
			$stream[$st[2][$i]] = array(($st[1][$i] == "true" ? true : false), base64_decode($st[3][$i])."&$akey");
		}

		if (count($stream) > 1) {
			if (!empty($_REQUEST['vbb_qs'])) {
				$qs = trim($_REQUEST['vbb_qs']);
				if (array_key_exists($qs, $stream)) {
					$dllink = $stream[$qs][1];
				} else {
					foreach ($stream as $st => $dt) {
						if ($dt[0] == true) {
							$qs = $st;
							$this->changeMesg(lang(300)."<br />Error: Requested video format not found, using \"$qs\".");
							$dllink = $dt[1];
							break;
						}
					}
				}
			} else {
				global $PHP_SELF;

				echo "\n<br /><br /><h3 style='text-align: center;'>This video have more than 1 stream, please select one for download.</h4>";
				echo "\n<br /><center><form name='dl' action='$PHP_SELF' method='post'>\n";
				echo "<select name='vbb_qs' id='vbb_qs'>\n";
				foreach ($stream as $st => $da) {
					if ($da[0] == true) {
						echo "<option selected='selected' value='$st'>$st</option>\n";
					} else {
						echo "<option value='$st'>$st</option>\n";
					}
				}
				echo "</select>\n";
				$data = $this->DefaultParamArr($link);
				foreach ( $data as $n => $v )
					echo("<input type='hidden' name='$n' id='$n' value='$v' />\n");
				if ($_POST ['ytdirect'] == 'on') echo "<input type='hidden' name='ytdirect' id='ytdirect' value='on' />\n";
				echo "<input type='submit' name='submit' value='Download Video' />\n";
				echo "</form></center>\n</body>\n</html>";
				exit;
			}
		} else {
			$qs = array_rand($stream);
			$dllink = $stream[$qs][1];
		}

		if ($_POST ['ytdirect'] == 'on') {
			echo "<br /><br /><h4><a style='color:yellow' href='" . urldecode($dllink) . "'>Click here or copy the link to your download manager to download</a></h4>\n";
			echo "<input name='dlurl' style='text-align: center; width: 800px; border: 1px solid #55AAFF; background-color: #FFFFFF; padding:3px' value='" . urldecode($dllink) . "' onclick='javascript:this.select();' readonly></input>\n</body>\n</html>";
		} else {
			$filename = str_replace(Array ("\\", "/", ":", "*", "?", "\"", "<", ">", "|"), "_", html_entity_decode(trim($tl[1]))) . " [$qs]" . ".flv";
			$this->RedirectDownload($dllink,$filename,0,0,0,$filename);
		}
		exit;
	}

	private function Get_Reply($page) {
		if (!function_exists('json_decode')) html_error("Error: Please enable JSON in php.");
		$json = substr($page, strpos($page,"\r\n\r\n") + 4);
		$json = substr($json, strpos($json, "{"));$json = substr($json, 0, strrpos($json, "}") + 1);
		$rply = json_decode($json, true);
		if (!$rply || count($rply) == 0) html_error("Error getting json data.");
		return $rply;
	}

	private function vbb_decode($page) {
		$json = $this->Get_Reply($page);

		$key1 = $key2 = $key3 = false;
		if (!$key1 = $json["settings"]["config"]["rkts"]) html_error("Error getting keys [1]");
		if (!$key2 = $json["settings"]["login_status"]["pepper"]) html_error("Error getting keys [2]");
		if (!$key3 = $json["settings"]["banner"]["lightbox2"]["time"]) html_error("Error getting keys [3]");

		list ($spn, $outk) = explode(";", $this->hextostr($this->bitDecrypt($json["settings"]["login_status"]["spen"], $json["settings"]["login_status"]["salt"], 950569)), 2);
		if (!$spn || !$outk) html_error("Error parsing link [1]");

		$outs = array();
		foreach (explode("&",$outk) as $out) {
			$tmp = explode("=", $out, 2);
			$outs[$tmp[0]] = $tmp[1];
		}
		$ikey = $this->getikey($outs["ik"]);

		$spn = explode('&', $spn);
		$akey = "";
		$decoded = 0;
		foreach ($spn as $value) {
			$val = explode('=', $value);
			if ($val[1] == 1) {
				if (!$ks1 = $json["settings"]["video_details"]["sece2"]) break;
				$akey .= $val[0].'='.$this->mvDecode($ks1, $key1, $ikey).'&'; // decrypt32byte
			} elseif ($val[1] == 2) {
				if (!$ks1 = $json["settings"]["banner"]["g_ads"]["url"]) break;
				$akey .= $val[0].'='.$this->bitDecrypt($ks1, $key1, $ikey).'&'; // bitDecrypt
			} elseif ($val[1] == 3) {
				if (!$ks1 = $json["settings"]["banner"]["g_ads"]["type"]) break;
				$akey .= $val[0].'='.$this->bitDecrypt($ks1, $key1, $ikey,26,25431,56989,93,32589,784152).'&'; // d9300
			} elseif ($val[1] == 4) {
				if (!$ks1 = $json["settings"]["banner"]["g_ads"]["time"]) break;
				$akey .= $val[0].'='.$this->bitDecrypt($ks1, $key1, $ikey,82,84669,48779,32,65598,115498).'&'; // lion
			} elseif ($val[1] == 5) {
				if (!$ks1 = $json["settings"]["login_status"]["euno"]) break;
				$akey .= $val[0].'='.$this->bitDecrypt($ks1, $key2, $ikey,10,12254,95369,39,21544,545555).'&'; // heal
			} elseif ($val[1] == 6) {
				if (!$ks1 = $json["settings"]["login_status"]["sugar"]) break;
				$akey .= $val[0].'='.$this->bitDecrypt($ks1, $key3, $ikey,22,66595,17447,52,66852,400595).'&'; // brokeup
			} else html_error("Error parsing link [2-?-{$val[0]}-{$val[1]}]");
			$decoded++;
		}
		if ($decoded != count($spn)) html_error("Error parsing link [2-{$val[0]}-{$val[1]}]");
		$akey .= "start=0";
		return $akey;
	}

	private function getikey($int) { 
		switch($int) {
			default:html_error("Error getting ikey [$int]");
			case 1:return 226593;
			case 2:return 441252;
			case 3:return 301517;
			case 4:return 596338;
			case 5:return 852084;
		}
	}

	// Finded at php.net
	private function hextostr($x) { 
		$s=''; 
		foreach(explode("\n",trim(chunk_split($x,2))) as $h) $s.=chr(hexdec($h)); 
		return($s); 
	}

	// Shorter string2bin and bin2String functions by rootwarex
	private function string2bin($str) {
		for ($i = 0; $i < strlen($str); ++ $i) {
			$bin .= str_pad(decbin(hexdec($str{$i})), 4, '0', STR_PAD_LEFT);
		}
		return $bin;
	}

	private function bin2String($bin) {
		$bin = join($bin);
		for ($i = strlen($bin)-4; $i >= 0; $i -= 4) {
			$hex .= dechex(bindec(substr($bin, $i, 4)));
		}
		return strrev($hex);
	}

	// From megavideo plugin...
	// Edited...
	private function mvDecode ($str, $key1, $key2) {
		$_loc1 = $this->string2bin($str);
		$_loc1 = str_split($_loc1);
		$_loc6 = array ();
		$kx = 0;
		for($_loc3 = 0; $_loc3 < 384; ++ $_loc3) {
			$key1 = ($key1 * 11 + 77213) % 81371;
			$key2 = ($key2 * 17 + 92717) % 192811;
			$_loc6 [$_loc3] = ($key1 + $key2) % 128;
		}
		for($_loc3 = 256; $_loc3 >= 0; -- $_loc3) {
			$_loc5 = $_loc6 [$_loc3];
			$_loc4 = $_loc3 % 128;
			$_loc8 = $_loc1 [$_loc5];
			$_loc1 [$_loc5] = $_loc1 [$_loc4];
			$_loc1 [$_loc4] = $_loc8;
		}
		for($_loc3 = 0; $_loc3 < 128; ++ $_loc3) {
			$_loc1 [$_loc3] = $_loc1 [$_loc3] ^ $_loc6 [$_loc3 + 256] & 1;
		}
		$_loc12 = join ( $_loc1, "" );
		$_loc7 = array ();
		for($_loc3 = 0; $_loc3 < strlen ( $_loc12 ); $_loc3 = $_loc3 + 4) {
			$_loc9 = substr ( $_loc12, $_loc3, 4 );
			$_loc7 [] = $_loc9;
		}
		return $this->bin2String($_loc7);
	}

	private function bitDecrypt($param1, $param2, $param3, $param4 = 11, $param5 = 77213, $param6 = 81371, $param7 = 17, $param8 = 92717, $param9 = 192811) {
		$_loc17 = 0;
		$_loc18 = 0;
		$_loc10 = array();
		$_loc10 = $this->string2bin($param1);
		$_loc11 = strlen($_loc10) * 2;
		$_loc10 = str_split($_loc10);
		$_loc12 = array();
		$_loc13 = 0;
		while ($_loc13 < $_loc11 * 1.5) {
			$param2 = ($param2 * $param4 + $param5) % $param6;
			$param3 = ($param3 * $param7 + $param8) % $param9;
			$_loc12[$_loc13] = ($param2 + $param3) % ($_loc11 * 0.5);
			$_loc13++;
		}
		$_loc13 = $_loc11;
		while ($_loc13 >= 0) {
			$_loc17 = $_loc12[$_loc13];
			$_loc18 = $_loc13 % ($_loc11 * 0.5);
			$_loc19 = $_loc10[$_loc17];
			$_loc10[$_loc17] = $_loc10[$_loc18];
			$_loc10[$_loc18] = $_loc19;
			$_loc13 = $_loc13 - 1;
		}
		$_loc13 = 0;
		while ($_loc13 < $_loc11 * 0.5) {
			$_loc10[$_loc13] = $_loc10[$_loc13] ^ $_loc12[$_loc13 + $_loc11] & 1;
			$_loc13++;
		}
		$_loc14 = implode($_loc10);
		$_loc15 = array();
		$_loc13 = 0;
		while ($_loc13 < strlen($_loc14)) {
			$_loc20 = substr($_loc14, $_loc13, 4);
			$_loc15[] = $_loc20;
			$_loc13 = $_loc13 + 4;
		}
		return $this->bin2String($_loc15);
	}
}

//[15-4-2011]  Written by Th3-822 //Using some code of youtube plugin and it shows direct link to video too.
//[13-10-2011]  Fixed for new link format & Regexp for getting streams && Added DefaultParamArr in post. - Th3-822
//[26-11-2011]  Using info posted by czerep from: http://rapidleech.com/index.php?showtopic=12433 for fix the plugin - Added function from megavideo plugin. - Th3-822
//[04-12-2011]  Function mvDecode was called more than 1 time... Fixed. - Th3-822
//[15-12-2011]  Videobb uses now more than 1 decrypt function, added more functions, and mvDecode edited for make 2 more functions. - Th3-822
//[17-12-2011]  Fixed 2 regexps and a typo in a if() && Added shorted string2bin and bin2String functions posted by rootwarex. - Th3-822
//[22-12-2011]  Now using json decode for vbb_decode && Fixed again... - Th3-822 

?>