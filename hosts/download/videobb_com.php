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
		$stream = array();
		for ($i = 0; $i < count($st[0]); $i++) {
			$stream[$st[2][$i]] = array(($st[1][$i] == "true" ? true : false), base64_decode($st[3][$i]));
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
}

//[15-4-2011]  Written by Th3-822 //Using some code of youtube plugin and it shows direct link to video too.
//[13-10-2011]  Fixed for new link format & Regexp for getting streams && Added DefaultParamArr in post. - Th3-822

?>