<?php

if (!defined('RAPIDLEECH')) {
	require_once ("index.html");
	exit ();
}

class youtube_com extends DownloadClass {
	private $page, $cookie, $fmts, $fmturlmaps;
	public function Download($link) {
		$this->cookie = isset($_REQUEST['yt_QS']) && !empty($_POST['cookie']) ? decrypt(urldecode($_POST['cookie'])) : array();
		if (!is_array($this->cookie) && (stripos($this->cookie, 'SID=') === false && stripos($this->cookie, 'goojf=') === false)) {
			$this->cookie = array();
		}
		$this->page = $this->GetPage($link, $this->cookie);

		if (preg_match('#^HTTP/1.(0|1) 403 Forbidden#i', $this->page)) {
			is_present($this->page, "This video contains content from", "This video has content with copyright and it's blocked in this server's country.");
			html_error('403 Forbidden');
		}
		if (preg_match('#^HTTP/1.(0|1) 404 Not Found#i', $this->page)) {
			is_present($this->page, "The video you have requested is not available.");
			is_present($this->page, "This video has been removed by the user.");
			html_error('404 Page Not Found');
		}

		if (isset($_REQUEST["step"]) || preg_match('#Location: http://(www.)?youtube.com/das_captcha#i', $this->page)) {
			$this->captcha($link);
		}
		$Mesg = lang(300);
		if (preg_match('#Location: http://(www.)?youtube.com/verify_age#i', $this->page)) {
			$Mesg .= "<br /><br />Verify_age page found:<br />This video may contain content that is inappropriate for some users<br /><br />Logging in to Youtube...<br />Direct Link option may not work.";
			$this->changeMesg($Mesg);
			$this->verify_age($link);
		}
		if (preg_match('#Location: http://(www.)?youtube.com/verify_controversy#i', $this->page)) {
			$Mesg .= "<br /><br />Verify_controversy page found:<br />The following content has been identified by the YouTube community as being potentially offensive or inappropriate. Viewer discretion is advised.";
			$this->changeMesg($Mesg);
			$this->verify_controversy($link);
		}

		if (!preg_match('#fmt_stream_map=(.+?)(&|(\\\u0026))#', $this->page, $fmt_url_map)) html_error('Video link not found.');
		$fmt_url_maps = explode(',', urldecode($fmt_url_map[1]));

		$this->fmts = array(38,37,22,45,35,44,34,43,18,5,17);
		$yt_fmt = empty($_REQUEST['yt_fmt']) ? '' : $_REQUEST['yt_fmt'];
		$this->fmturlmaps = $this->GetVideosArr($fmt_url_maps);
		textarea(array_keys($this->fmturlmaps));

		if (empty($yt_fmt) && !isset($_GET["audl"])) return $this->QSelector($link);
		elseif (isset($_REQUEST['ytube_mp4']) && $_REQUEST['ytube_mp4'] == 'on' && !empty($yt_fmt)) {
			//look for and download the highest quality we can find?
			if ($yt_fmt == 'highest') {
				foreach ($this->fmts as $fmt) {
					if (array_key_exists($fmt, $this->fmturlmaps)) {
						$furl = $this->fmturlmaps[$fmt];
						break;
					}
				}
			} else { //get the format the user specified (making sure it actually exists)
				if (!$furl = $this->fmturlmaps[$yt_fmt]) html_error ('Specified video format not found');
				$fmt = $yt_fmt;
			}
		} else { //just get the one Youtube plays by default (in some cases it could also be the highest quality format)
			$fmt = key($this->fmturlmaps);
			$furl = $this->fmturlmaps[$fmt];
		}

		if (preg_match ('%^5|34|35$%', $fmt)) $ext = '.flv';
		elseif (preg_match ('%^17$%', $fmt)) $ext = '.3gp';
		elseif (preg_match ('%^18|22|37|38$%', $fmt)) $ext = '.mp4';
		elseif (preg_match ('%^43|44|45$%', $fmt)) $ext = '.webm';
		else $ext = '.flv';

		if (!preg_match('#<title>(.*)\s+-\sYouTube[\r|\n|\t|\s]*</title>#Us', $this->page, $title)) html_error('No video title found! Download halted.');
		if (!preg_match ('/video_id=(.+?)(\\\|"|&|(\\\u0026))/', $this->page, $video_id)) html_error('Video id not found.');

		$FileName = str_replace (Array ("\\", "/", ":", "*", "?", "\"", "<", ">", "|"), "_", html_entity_decode(trim($title[1]), ENT_QUOTES)) . "-[{$video_id[1]}][f$fmt]$ext";

		if (stristr($furl, '|')) {
			$u_arr = explode('|', $furl);
			$furl = preg_replace('#://([^/]+)#', "://".$u_arr[2], $u_arr[0]);
		}
		if (isset($_REQUEST['ytdirect']) && $_REQUEST['ytdirect'] == 'on')
		{
			echo "<br /><br /><h4><a style='color:yellow' href='" . urldecode($furl) . "'>Click here or copy the link to your download manager to download</a></h4>";
			echo "<input name='dlurl' style='width: 1000px; border: 1px solid #55AAFF; background-color: #FFFFFF; padding:3px' value='" . urldecode($furl) . "' onclick='javascript:this.select();' readonly></input>";
		}
		else
		{
			$this->RedirectDownload (urldecode($furl), $FileName, $this->cookie, 0, 0, $FileName);
		}
	}

	private function captcha($link) {
		$url = "http://www.youtube.com/das_captcha?next=" . urlencode($link);
		if (isset($_REQUEST["step"]) && $_REQUEST["step"] == 1) {
			$post['challenge_enc'] = $_POST['challenge_enc'];
			$post['next'] = $_POST['next'];
			$post['response'] = $_POST['captcha'];
			$post['action_verify'] = $_POST['action_verify'];
			$post['submit'] = $_POST['submit'];
			$post['session_token'] = $_POST['session_token'];
			$cookie = urldecode($_POST['cookie']);

			$page = $this->GetPage($url, $cookie, $post, $url);
			is_present($page, "The verification code was invalid", "The verification code was invalid or has timed out, please try again.");
			is_present($page, "\r\n\r\nAuthorization Error.", "Error sending captcha.");
			is_notpresent($page, "Set-Cookie: goojf=", "Cannot get captcha cookie.");

			$this->cookie = GetCookiesArr($page);
			$this->page = $this->GetPage($link, $this->cookie);
		} else {
			$page = $this->GetPage($url);

			$data = $this->DefaultParamArr($link, GetCookies($page));
			$data['challenge_enc'] = urlencode(cut_str($page, 'name="challenge_enc" value="', '"'));
			$data['next'] = urlencode(cut_str($page, 'name="next" value="', '"'));
			$data['action_verify'] = urlencode(cut_str($page, 'name="action_verify" value="', '"'));
			$data['submit'] = urlencode(cut_str($page, 'type="submit" name="submit" value="', '"'));
			$data['session_token'] = urlencode(cut_str($page, "'XSRF_TOKEN': '", "'"));
			if (isset($_REQUEST['ytube_mp4'])) $data['ytube_mp4'] = $_REQUEST['ytube_mp4'];
			if (isset($_REQUEST['ytdirect'])) $data['ytdirect'] = $_REQUEST['ytdirect'];
			if (isset($_REQUEST['yt_fmt'])) $data['yt_fmt'] = $_REQUEST['yt_fmt'];
			$data['step'] = 1;

			$this->EnterCaptcha("http://www.youtube.com" . cut_str($page, 'img name="verificationImg" src="', '"'), $data, 20);
			exit;
		}
	}

	private function login($link) {
		global $premium_acc;
		if (!is_array($this->cookie) && stripos($this->cookie, 'SID=') !== false) return;

		if (!empty($_REQUEST["premium_user"]) && !empty($_REQUEST["premium_pass"])) {
			$user = $_REQUEST["premium_user"];
			$pass = $_REQUEST["premium_pass"];
		} else {
			$user = $premium_acc["youtube_com"]['user'];
			$pass = $premium_acc["youtube_com"]['pass'];
		}
		if (empty($user) || empty($pass)) html_error("Login Failed: Login Empty.", 0);

		$post = array();
		$post["Email"] = urlencode($user);
		$post["Passwd"] = urlencode($pass);
		$post["service"] = 'youtube';

		$page = $this->GetPage("https://www.google.com/accounts/ClientLogin", 0, $post, "https://www.google.com/accounts/ClientLogin");
		is_present($page, "Error=BadAuthentication", "Login Failed: The login/password entered are incorrect.");
		is_present($page, "Error=NotVerified", "Login Failed: The account has not been verified.");
		is_present($page, "Error=TermsNotAgreed", "Login Failed: The account has not agreed to terms.");
		is_present($page, "Error=CaptchaRequired", "Login Failed: Need CAPTCHA. (Not supported yet)... Or check you login and try again.");
		is_present($page, "Error=Unknown", "Login Failed.");
		is_present($page, "Error=AccountDeleted", "Login Failed: The user account has been deleted.");
		is_present($page, "Error=AccountDisabled", "Login Failed: The user account has been disabled.");
		is_present($page, "Error=ServiceDisabled", "Login Failed: The user's access to the specified service has been disabled.");
		is_present($page, "Error=ServiceUnavailable", "Login Failed: Service is not available; try again later.");

		if (!preg_match('@SID=([^\r|\n]+)@i', $page, $sid)) html_error("Login Failed: SessionID token not found.", 0);

		$this->cookie["SID"] = $sid[1];
		$this->page = $this->GetPage($link, $this->cookie);
		$this->cookie = array_merge($this->cookie, GetCookiesArr($this->page));
	}

	private function verify_age($link) {
		$this->login($link);

		if (!preg_match('#Location: http://(www.)?youtube.com/verify_age#i', $this->page)) return;

		$url = "http://www.youtube.com/verify_age?next_url=" . urlencode($link);
		$page = $this->GetPage($url, $this->cookie);

		$post = array();
		$post['next_url'] = urlencode($link);
		$post['set_racy'] = 'true';
		$post['session_token'] = urlencode(cut_str($page, "'XSRF_TOKEN': '", "'"));

		$urlc = "http://www.youtube.com/verify_age?action_confirm=true";
		$page = $this->GetPage($urlc, $this->cookie, $post, $url);
		$this->page = $this->GetPage("$link&has_verified=1", $this->cookie, 0, $urlc);
	}

	private function verify_controversy($link) {
		$url = "http://www.youtube.com/verify_controversy?next_url=" . urlencode($link);
		$page = $this->GetPage($url, $this->cookie);

		$post = array();
		$post['next_url'] = urlencode($link);
		// $post['ignorecont'] = 'on';
		$post['session_token'] = urlencode(cut_str($page, "'XSRF_TOKEN': '", "'"));

		$urlc = "http://www.youtube.com/verify_controversy?action_confirm=1";
		$page = $this->GetPage($urlc, $this->cookie, $post, $url);
		$this->page = $this->GetPage("$link&skipcontrinter=1", $this->cookie, 0, $urlc);
	}

	private function GetVideosArr($fmtmaps) {
		$fmturls = array();
		foreach ($fmtmaps as $fmtlist) {
			$arr1 = explode('&', $fmtlist);
			$fmtlist = $arr3 = array();
			foreach ($arr1 as $key => $val) {
				$arr2 = explode('=', $val);
				foreach ($arr2 as $key2 => $val2) {
					$arr3[] = $val2;
				}
			}
			for ($i = 0; $i <= count($arr3); $i += 2) {
				if (array_key_exists($i, $arr3)) {
					if ($arr3[$i] != "") {
						$fmtlist[trim($arr3[$i])] = $arr3[$i+1];
					}
				}
			}
			$fmturls[$fmtlist['itag']] = urldecode($fmtlist['url']);
		}
		return $fmturls;
	}

	private function QSelector($link) {
		global $PHP_SELF;
		$fmtlangs = array(38 => 377, 37 => 228, 22 => 227, 45 => 225, 35 => 223, 44 => 389, 34 => 222, 43 => 224, 18 => 226, 5 => 221, 17 => 220);

		echo "\n<br /><br /><h3 style='text-align: center;'>".lang(216).".</h4>";
		echo "\n<center><form name='dl' action='$PHP_SELF' method='post'>\n";
		echo "<input type='hidden' name='yt_QS' value='on' />\n";
		echo '<input type="checkbox" name="ytdirect" /><small>&nbsp;'.lang(217).'</small><br />';
		echo "<select name='yt_fmt' id='vbb_qs'>\n";
		foreach ($this->fmturlmaps as $fmt => $url) {
			if (in_array($fmt, $this->fmts)) echo "<option ".($fmt == 18 ? "selected='selected' " : '')."value='$fmt'>".lang($fmtlangs[$fmt])."</option>\n";
		}
		echo "</select>\n";
		if (count($this->cookie) > 0) $this->cookie = encrypt(CookiesToStr($this->cookie));
		$data = $this->DefaultParamArr($link, $this->cookie);
		$data['ytube_mp4'] = 'on';
		foreach ($data as $n => $v) echo("<input type='hidden' name='$n' id='$n' value='$v' />\n");

		echo "<input type='submit' name='submit' value='".lang(209)."' />\n";
		echo "</form></center>\n</body>\n</html>";
		exit;
	}
}

//re-written by szal based on original plugin by eqbal
//updated 07 June 2010
// [28-03-2011]  Fixed (!$video_id) regex. - Th3-822
// [29-03-2011]  Added support for captcha. - Th3-822
// [02-04-2011]  Fixed redirect error. [26-04-2011]  Added error msgs.  - Th3-822
// [04-8-2011]  Fixed for recent changes in fmt_stream_map content & some edits maded for work fine. (Redirect is needed yet) - Th3-822
// [12-8-2011]  Added support for videos that need login for verify age & Changed fmt order by quality & Fixed regexps for fileext. - Th3-822
// [13-8-2011]  Some fixes & removed not working code & fixed verify_age function. - Th3-822
// [17-9-2011]  Added function for skip 'verify_controversy' on youtube && Fixed cookies after captcha && Little changes. - Th3-822
// [26-1-2012]  Fixed regexp for get title, added a quality selector (if the one in template is removed) and some changes in the code. - Th3-822
// [12-4-2012]  Added edits from the updated plugin from lastest svn plugin (With some functions changed for work). - Th3-822

?>