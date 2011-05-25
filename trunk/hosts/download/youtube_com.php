<?php
if (! defined ('RAPIDLEECH'))
{
	require_once ("index.html");
	exit ();
}

class youtube_com extends DownloadClass
{
	private $page;
	
	public function Download($link)
	{
		$this->page = $this->GetPage($link);
		if (preg_match('#^HTTP/1.(0|1) 404 Not Found#i', $this->page)) {
				is_present($this->page, "The video you have requested is not available.");
				is_present($this->page, "This video contains content from", "This video has content with copyright and it's blocked in this server's country.");
				html_error('404 Page Not Found');
		}				
		if (preg_match('#Location: http://(www.)?youtube.com/das_captcha#', $this->page) || $_GET["step"]) {
				$this->captcha($link);
		}
		if (!preg_match('#fmt_url_map=(.+?);#', $this->page, $fmt_url_map)) html_error('Video link not found.');
		$fmt_url_maps = preg_split('%,%', urldecode(str_replace('\u0026amp','', $fmt_url_map[1])));
		$fmts = array(37,22,35,18,34,6,5,0,17,13);
		$yt_fmt = $_POST['yt_fmt'];

		if ($_POST['ytube_mp4'] == 'on')
		{
			foreach ($fmt_url_maps as $fmtlist)
			{
				$furlmap = preg_split('%\|%', $fmtlist);
				$fmturlmaps[$furlmap[0]] = $furlmap[1];
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
				if (!$furl = $fmturlmaps[$yt_fmt])
				{
					preg_match ('/"t": "([^\"]+)/', $this->page, $video_t);
					preg_match ('/"video_id": "([^\"]+)/', $this->page, $video_id);
					preg_match ('%var swfUrl = canPlayV9Swf\(\) \? "(.+)\.swf" :%U', $this->page, $refmatch);
					$cookies = GetCookies ($this->page);
					$gurl = "http://www.youtube.com/get_video?video_id=" . $video_id [1] . "&t=" . $video_t [1] . "&el=detailpage&ps=&fmt=$yt_fmt";
					$page = $this->GetPage ($gurl, $cookies, 0, $refmatch [1]);
					if (! preg_match ('%ocation: (.+)\r\n%', $page, $durl)) html_error ('Specified video format not found');
					$furl = $durl[1];
				}
			}
		}
		else //just get the one Youtube plays by default (in some cases it could also be the highest quality format)
		{
			foreach ($fmt_url_maps as $fmtlist)
			{
				$furlmap = preg_split('%\|%', $fmtlist);
				$fmturlmaps[] = $furlmap;
			}
			$fmt = $fmturlmaps[0][0];
			$furl = $fmturlmaps[0][1];
		}

		if (preg_match ('%0|5|6|34|35%', $yt_fmt)) $ext = '.flv';
		elseif (preg_match ('%18|22|37%', $yt_fmt)) $ext = '.mp4';
		elseif (preg_match ('%13|17%', $yt_fmt)) $ext = '.3gp';
		elseif (preg_match ('%highest%', $yt_fmt)) $ext = '.mp4';
		else $ext = '.flv';
		
		if (!preg_match('#<title>\s*YouTube[\s\-]+(.*?)\s*</title>#', $this->page, $title)) html_error('No video title found! Download halted.');
		if (!$video_id)
		{
			preg_match ('#video_id=(.+?);#', $this->page, $video_id);
			$video_id = str_replace('\u0026amp', '', $video_id[1]);
		}

		$FileName = str_replace (Array ("\\", "/", ":", "*", "?", "\"", "<", ">", "|"), "_", html_entity_decode (trim($title[1]))) . (isset ($_POST ['yt_fmt']) && $_POST ['yt_fmt'] !== 'highest' ? '-[' . $video_id . '][f' . $_POST ['yt_fmt'] . ']' : '-[' . $video_id . '][f' . $fmt . ']') . $ext;

		if ($_POST ['ytdirect'] == 'on')
		{
			echo "<br /><br /><h4><a style='color:yellow' href='" . urldecode($furl) . "'>Click here or copy the link to your download manager to download</a></h4>";
			echo "<input name='dlurl' style='width: 1000px; border: 1px solid #55AAFF; background-color: #FFFFFF; padding:3px' value='" . urldecode($furl) . "' onclick='javascript:this.select();' readonly></input>";
		}
		else
		{
			// Add the force_name this way:
			//$params = array ('force_name' => $FileName);
			$this->RedirectDownload (urldecode($furl), $FileName, $cookies, 0, $refmatch [1], $FileName);
		}
	}
	private function captcha($link) {
		$url = "http://www.youtube.com/das_captcha?next=" . urlencode($link);
		if ($_GET["step"] == 1) {
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
			global $Referer;
			$page = $this->GetPage($url);

			$data['challenge_enc'] = urlencode(cut_str($page, 'name="challenge_enc" value="', '"'));
			$data['next'] = urlencode(cut_str($page, 'name="next" value="', '"'));
			$data['action_verify'] = urlencode(cut_str($page, 'name="action_verify" value="', '"'));
			$data['submit'] = urlencode(cut_str($page, 'type="submit" name="submit" value="', '"'));
			$data['session_token'] = urlencode(cut_str($page, "'XSRF_TOKEN': '", "'"));
			$data['step'] = 1;
			$data['link'] = urlencode($link);
			$data['cookie'] = urlencode(GetCookies($page));
			$data['referer'] = urlencode($Referer);

			$this->EnterCaptcha("http://www.youtube.com" . cut_str($page, 'img name="verificationImg" src="', '"'), $data, 20);
			exit;
		}
	}
}
//re-written by szal based on original plugin by eqbal
//updated 07 June 2010
// [29-03-2011]  Added support for captcha. - Th3-822
// [27-04-2011]  Added error message - Th3-822
?>