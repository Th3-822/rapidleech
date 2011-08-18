<?php

if (!defined('RAPIDLEECH')) {
	require_once ("index.html");
	exit ();
}

class youtube_com extends DownloadClass {
	private $page;
	public function Download($link) {
		$this->page = $this->GetPage($link);
		if (preg_match('#^HTTP/1.(0|1) 404 Not Found#i', $this->page)) {
			is_present($this->page, "The video you have requested is not available.");
			is_present($this->page, "This video has been removed by the user.");
			is_present($this->page, "This video contains content from", "This video has content with copyright and it's blocked in this server's country.");
			html_error('404 Page Not Found');
		}

		if ($_REQUEST["step"] || preg_match('#Location: http://(www.)?youtube.com/das_captcha#i', $this->page)) {
			$this->captcha($link);
		}
		if (preg_match('#Location: http://(www.)?youtube.com/verify_age#i', $this->page)) {
			$this->changeMesg(lang(300)."<br />Verify_age page found:<br />This video may contain content that is inappropriate for some users<br />Logging in to Youtube...");
			$this->verify_age($link);
		}

		if (!preg_match('#fmt_stream_map=(.+?)(&|(\\\u0026))#', $this->page, $fmt_url_map)) html_error('Video link not found.');
		$fmt_url_maps = explode(',', urldecode($fmt_url_map[1]));

		$fmts = array(38,37,22,45,35,44,34,43,18,5,17);
		$yt_fmt = $_REQUEST['yt_fmt'];

		if ($_REQUEST['ytube_mp4'] == 'on')
		{
			foreach ($fmt_url_maps as $fmtlist)
			{
				$furlmap = explode('&type=', urldecode($fmtlist), 2);
				$furlmap[1] = str_replace('=', '', substr($furlmap[1], - 2));
				$fmturlmaps[$furlmap[1]] = $furlmap[0];
			}

			//look for and download the highest quality we can find?
			if ($yt_fmt == 'highest')
			{
				foreach ($fmts as $fmt)
				{
					if (in_array($fmt, array_keys($fmturlmaps)))
					{
						$furl = $fmturlmaps[$fmt];
						break;
					}
				}
			}
			else //get the format the user specified (making sure it actually exists)
			{
				if (!$furl = $fmturlmaps[$yt_fmt]) html_error ('Specified video format not found');
				$fmt = $yt_fmt;
			}
		}
		else //just get the one Youtube plays by default (in some cases it could also be the highest quality format)
		{
			foreach ($fmt_url_maps as $fmtlist)
			{
				$furlmap = explode('&type=', urldecode($fmtlist), 2);
				$furlmap[1] = str_replace('=', '', substr($furlmap[1], - 2));
				$fmturlmaps[] = $furlmap;
			}
			$fmt = $fmturlmaps[0][1];
			$furl = $fmturlmaps[0][0];
		}

		$furl = str_replace('url=', '', $furl);
		if (preg_match ('%^5|34|35$%', $fmt)) $ext = '.flv';
		elseif (preg_match ('%^17$%', $fmt)) $ext = '.3gp';
		elseif (preg_match ('%^18|22|37|38$%', $fmt)) $ext = '.mp4';
		elseif (preg_match ('%^43|44|45$%', $fmt)) $ext = '.webm';
		else $ext = '.flv';

		if (!preg_match('#<title>.*YouTube.*-(.*)</title>#Us', $this->page, $title)) html_error('No video title found! Download halted.');
		if (!preg_match ('/video_id=(.+?)(&|(\\\u0026))/', $this->page, $video_id)) html_error('Video id not found.');

		$FileName = str_replace (Array ("\\", "/", ":", "*", "?", "\"", "<", ">", "|"), "_", html_entity_decode(trim($title[1]))) . "-[{$video_id[1]}][f$fmt]$ext";

		if (stristr($furl, '|')) {
			$u_arr = explode('|', $furl);
			$furl = preg_replace('#://([^/]+)#', "://".$u_arr[2], $u_arr[0]);
		}

		if ($_REQUEST['ytdirect'] == 'on')
		{
			echo "<br /><br /><h4><a style='color:yellow' href='" . urldecode($furl) . "'>Click here or copy the link to your download manager to download</a></h4>";
			echo "<input name='dlurl' style='width: 1000px; border: 1px solid #55AAFF; background-color: #FFFFFF; padding:3px' value='" . urldecode($furl) . "' onclick='javascript:this.select();' readonly></input>";
		}
		else
		{
			$this->RedirectDownload (urldecode($furl), $FileName, $cookies, 0, 0, $FileName);
		}
	}

	private function captcha($link) {
		$url = "http://www.youtube.com/das_captcha?next=" . urlencode($link);
		if ($_REQUEST["step"] == 1) {
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

			$this->page = $this->GetPage($link, GetCookies($page));
		} else {
			$page = $this->GetPage($url);

			$data = $this->DefaultParamArr($link, GetCookies($page));
			$data['challenge_enc'] = urlencode(cut_str($page, 'name="challenge_enc" value="', '"'));
			$data['next'] = urlencode(cut_str($page, 'name="next" value="', '"'));
			$data['action_verify'] = urlencode(cut_str($page, 'name="action_verify" value="', '"'));
			$data['submit'] = urlencode(cut_str($page, 'type="submit" name="submit" value="', '"'));
			$data['session_token'] = urlencode(cut_str($page, "'XSRF_TOKEN': '", "'"));
			$data['ytube_mp4'] = $_REQUEST['ytube_mp4'];
			$data['ytdirect'] = $_REQUEST['ytdirect'];
			$data['yt_fmt'] = $_REQUEST['yt_fmt'];
			$data['step'] = 1;

			$this->EnterCaptcha("http://www.youtube.com" . cut_str($page, 'img name="verificationImg" src="', '"'), $data, 20);
			exit;
		}
	}

	private function login() {
		global $premium_acc;

		if (!empty($_REQUEST["premium_user"]) && !empty($_REQUEST["premium_pass"])) {
			$user = $_REQUEST["premium_user"];
			$pass = $_REQUEST["premium_pass"];
		} else {
			$user = $premium_acc["youtube_com"]['user'];
			$pass = $premium_acc["youtube_com"]['pass'];
		}
		if (empty($user) || empty($pass)) html_error("Login Failed: Login Empty.", 0);

		$post = array();
		$post["Email"] = $user;
		$post["Passwd"] = $pass;
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
		return "SID={$sid[1]}";
	}

	private function verify_age($link) {
		$cookie = $this->login();
		$this->page = $this->GetPage($link, $cookie);
		$cookie = "SID={$sid[1]} ;" . GetCookies($this->page);
		if (!preg_match('#Location: http://(www.)?youtube.com/verify_age#i', $this->page)) return;

		$url = "http://www.youtube.com/verify_age?next_url=" . urlencode($link);
		$page = $this->GetPage($url, $cookie);

		$post = array();
		$post['next_url'] = urlencode($link);
		$post['set_racy'] = 'true';
		$post['session_token'] = urlencode(cut_str($page, "'XSRF_TOKEN': '", "'"));

		$page = $this->GetPage("http://www.youtube.com/verify_age?action_confirm=true", $cookie, $post, $url);
		$this->page = $this->GetPage("$link&has_verified=1", $cookie, 0, "http://www.youtube.com/verify_age?action_confirm=true");
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

?>
