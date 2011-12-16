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
		if (!preg_match('/"rkts":(\d+)/i', $page, $rk) || !preg_match('/"sece2":"(\w+)"/i', $page, $k1)) html_error('Video keys not found.');
		if (!preg_match_all('/\{"d":(false|true),"l":"([^"]+)","u":"([^"]+)"/i', $page, $st)) html_error('Video stream(s) not found.'); //Get streams
		$stream = array();
		for ($i = 0; $i < count($st[0]); $i++) {
			$stream[$st[2][$i]] = array(($st[1][$i] == "true" ? true : false), base64_decode($st[3][$i]).'&c='.$this->mvDecode($k1[1], $rk[1], (113296.5*2)));
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

	// From megavideo plugin...
	private function mvDecode ($str, $key1, $key2) {
		$_loc1 = array ();
		for($_loc3 = 0; $_loc3 < strlen ( $str ); ++ $_loc3) {
			switch ($str {$_loc3}) {
				case "0" :
					$_loc1 [] = "0000";
					break;
				case "1" :
					$_loc1 [] = "0001";
					break;
				case "2" :
					$_loc1 [] = "0010";
					break;
				case "3" :
					$_loc1 [] = "0011";
					break;
				case "4" :
					$_loc1 [] = "0100";
					break;
				case "5" :
					$_loc1 [] = "0101";
					break;
				case "6" :
					$_loc1 [] = "0110";
					break;
				case "7" :
					$_loc1 [] = "0111";
					break;
				case "8" :
					$_loc1 [] = "1000";
					break;
				case "9" :
					$_loc1 [] = "1001";
					break;
				case "a" :
					$_loc1 [] = "1010";
					break;
				case "b" :
					$_loc1 [] = "1011";
					break;
				case "c" :
					$_loc1 [] = "1100";
					break;
				case "d" :
					$_loc1 [] = "1101";
					break;
				case "e" :
					$_loc1 [] = "1110";
					break;
				case "f" :
					$_loc1 [] = "1111";
					break;
			}
		}
		$_loc1 = join ( "", $_loc1 );
		$_loc1 = str_split ( $_loc1 );
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
		$_loc2 = array ();
		for($_loc3 = 0; $_loc3 < count ( $_loc7 ); ++ $_loc3) {
			switch ($_loc7 [$_loc3]) {
				case "0000" :
					$_loc2 [] = "0";
					break;
				case "0001" :
					$_loc2 [] = "1";
					break;
				case "0010" :
					$_loc2 [] = "2";
					break;
				case "0011" :
					$_loc2 [] = "3";
					break;
				case "0100" :
					$_loc2 [] = "4";
					break;
				case "0101" :
					$_loc2 [] = "5";
					break;
				case "0110" :
					$_loc2 [] = "6";
					break;
				case "0111" :
					$_loc2 [] = "7";
					break;
				case "1000" :
					$_loc2 [] = "8";
					break;
				case "1001" :
					$_loc2 [] = "9";
					break;
				case "1010" :
					$_loc2 [] = "a";
					break;
				case "1011" :
					$_loc2 [] = "b";
					break;
				case "1100" :
					$_loc2 [] = "c";
					break;
				case "1101" :
					$_loc2 [] = "d";
					break;
				case "1110" :
					$_loc2 [] = "e";
					break;
				case "1111" :
					$_loc2 [] = "f";
					break;
			}
		}
		return (join ( $_loc2, "" ));
	}
}

//[15-4-2011]  Written by Th3-822 //Using some code of youtube plugin and it shows direct link to video too.
//[13-10-2011]  Fixed for new link format & Regexp for getting streams && Added DefaultParamArr in post. - Th3-822
//[26-11-2011]  Using info posted by czerep from: http://rapidleech.com/index.php?showtopic=12433 for fix the plugin - Added function from megavideo plugin. - Th3-822

?>