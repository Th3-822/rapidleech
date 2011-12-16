<?php

if (!defined('RAPIDLEECH')) {
	require_once ("index.html");
	exit;
}

class videozer_com extends DownloadClass {
	public function Download($link) {
		$page = $this->GetPage($link);
		is_present ($page, "The page you have requested cannot be found", "Video is not found - Try checking the link.");
		is_present ($page, "Video is not available - ", "Video is not available - " . cut_str($page, "Video is not available - ", "\n"));

		$page = $this->GetPage(str_replace('videozer.com/video/', 'videozer.com/player_control/settings.php?v=', $link));
		is_notpresent ($page, "you still have quota left", "Error: Still have quota left?");

		if (!preg_match('/"video":\{"title":"([^"]+)"/i', $page, $tl)) html_error('Video title not found.');
		if (!preg_match('/"rkts":(\d+)/i', $page, $rk)) html_error('Video key1 not found.');
		if (!preg_match_all('/\{"d":(false|true),"l":"([^"]+)","u":"([^"]+)"/i', $page, $st)) html_error('Video stream(s) not found.'); //Get streams

		$akey = $this->vbb_decode($page, $rk[1]);

		$stream = array();
		for ($i = 0; $i < count($st[0]); $i++) {
			$stream[$st[2][$i]] = array(($st[1][$i] == "true" ? true : false), base64_decode($st[3][$i])."&$akey");
		}

		if (count($stream) > 1) {
			if (!empty($_REQUEST['vzr_qs'])) {
				$qs = trim($_REQUEST['vzr_qs']);
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
				echo "<select name='vzr_qs' id='vzr_qs'>\n";
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

	private function vbb_decode($page, $key1) {
		if (!preg_match('/"spn":"([^\"]+)"/i', $page, $spn)) html_error("Error parsing link [1]");
		$spn = explode('&', base64_decode($spn[1]));
		$akey = "";
		$decoded = 0;
		$page2 = cut_str($page, '"g_ads":{', '}');
		foreach ($spn as $value) {
			$val = explode('=', $value);
			if ($val[1] == 1) {
				if (!preg_match('/"sece2":"(\w{64})"/i', $page, $kk)) break;
				$akey .= $val[0].'='.$this->mvDecode($kk[1], $key1, 215678).'&'; // decrypt32byte
			} elseif ($val[1] == 2) {
				if (!preg_match('/"url":"(\w{64})"/i', $page2, $kk)) break;
				$akey .= $val[0].'='.$this->bitDecrypt($kk[1], $key1, 215678).'&'; // bitDecrypt
			} elseif ($val[1] == 3) {
				if (!preg_match('/"type":"(\w{64})"/i', $page2, $kk)) break;
				$akey .= $val[0].'='.$this->bitDecrypt($kk[1], $key1, 215678,26,25431,56989,93,32589,784152).'&'; // d9300
			} elseif ($val[1] == 4) {
				if (!preg_match('/"time":"(\w{64})"/i', $page2, $kk)) break;
				$akey .= $val[0].'='.$this->bitDecrypt($kk[1], $key1, 215678,82,84669,48779,32,65598,115498).'&'; // lion
			} else html_error("Error parsing link [2-E-$decoded]");
			$decoded++;
		}
		if ($decoded =! count($spn)) html_error("Error parsing link [2-{$val[0]}-{$val[1]}]");
		$akey .= "start=0";
		return $akey;
	}

	private function string2bin($str) {
		$bin = array();
		for($i = 0; $i < strlen ($str); ++ $i) {
			switch ($str {$i}) {
				case "0" :
					$bin[] = "0000";
					break;
				case "1" :
					$bin[] = "0001";
					break;
				case "2" :
					$bin[] = "0010";
					break;
				case "3" :
					$bin[] = "0011";
					break;
				case "4" :
					$bin[] = "0100";
					break;
				case "5" :
					$bin[] = "0101";
					break;
				case "6" :
					$bin[] = "0110";
					break;
				case "7" :
					$bin[] = "0111";
					break;
				case "8" :
					$bin[] = "1000";
					break;
				case "9" :
					$bin[] = "1001";
					break;
				case "a" :
					$bin[] = "1010";
					break;
				case "b" :
					$bin[] = "1011";
					break;
				case "c" :
					$bin[] = "1100";
					break;
				case "d" :
					$bin[] = "1101";
					break;
				case "e" :
					$bin[] = "1110";
					break;
				case "f" :
					$bin[] = "1111";
					break;
			}
		}
		$bin = implode($bin);
		return $bin;
	}

	private function bin2String($bin) {
		$str = array();
		for($i = 0; $i < count ($bin); ++ $i) {
			switch ($bin[$i]) {
				case "0000" :
					$str[] = "0";
					break;
				case "0001" :
					$str[] = "1";
					break;
				case "0010" :
					$str[] = "2";
					break;
				case "0011" :
					$str[] = "3";
					break;
				case "0100" :
					$str[] = "4";
					break;
				case "0101" :
					$str[] = "5";
					break;
				case "0110" :
					$str[] = "6";
					break;
				case "0111" :
					$str[] = "7";
					break;
				case "1000" :
					$str[] = "8";
					break;
				case "1001" :
					$str[] = "9";
					break;
				case "1010" :
					$str[] = "a";
					break;
				case "1011" :
					$str[] = "b";
					break;
				case "1100" :
					$str[] = "c";
					break;
				case "1101" :
					$str[] = "d";
					break;
				case "1110" :
					$str[] = "e";
					break;
				case "1111" :
					$str[] = "f";
					break;
			}
		}
		$str = implode($str);
		return $str;
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

//[15-4-2011]  Written by Th3-822 //Using some code of youtube plugin and it shows direct link to video too. [videobb_com plugin]
//[16-4-2011]  Renamed 'videobb' to 'videozer' & Edited error msgs for work with videozer. - Th3-822
//[13-10-2011]  Fixed Regexp for getting streams && Added DefaultParamArr in post. - Th3-822
//[26-11-2011]  Using info posted by czerep from: http://rapidleech.com/index.php?showtopic=12433 for fix the plugin - Added function from megavideo plugin. - Th3-822
//[04-12-2011]  Function mvDecode was called more than 1 time... Fixed. - Th3-822
//[16-12-2011]  Added lastest edits from videobb plugin. - Th3-822

?>