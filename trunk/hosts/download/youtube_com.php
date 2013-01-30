<?php

if (!defined('RAPIDLEECH')) {
	require_once('index.html');
	exit();
}

class youtube_com extends DownloadClass {
	private $page, $cookie, $fmts, $fmturlmaps, $vid;
	public function Download($link) {
		$this->cookie = isset($_POST['yt_QS']) && !empty($_POST['cookie']) ? StrToCookies(decrypt(urldecode($_POST['cookie']))) : array();
		$url = parse_url($link);
		$this->vid = array();

		if (host_matchs('youtu.be', $url['host'])) preg_match('@/([\w\-\.]{11})@i', $url['path'], $this->vid);
		elseif (empty($url['query']) || ($this->vid[1] = cut_str('&'.$url['query'].'&', '&v=', '&')) === false || !preg_match('@^[\w\-\.]{11}$@i', $this->vid[1])) preg_match('@/(?:v|(?:embed))/([\w\-\.]{11})@i', $url['path'], $this->vid);

		if (empty($this->vid[1])) html_error('Video ID not found.');
		$this->vid = $this->vid[1];
		$link = 'http://www.youtube.com/watch?v='.$this->vid;

		$this->page = $this->GetPage('http://www.youtube.com/get_video_info?video_id='.$this->vid.'&asv=3&el=detailpage&hl=en_US', $this->cookie);
		$response = array_map('urldecode', $this->FormToArr(substr($this->page, strpos($this->page, "\r\n\r\n") + 4)));
		if (!empty($response['reason'])) html_error('['.htmlentities($response['errorcode']).'] '.htmlentities($response['reason']));

		if (isset($_REQUEST['step']) || preg_match('@Location: https?://(www\.)?youtube\.com/das_captcha@i', $this->page)) $this->captcha($link);

		if (empty($response['url_encoded_fmt_stream_map'])) html_error('Video links not found.');
		$fmt_url_maps = explode(',', $response['url_encoded_fmt_stream_map']);

		$this->fmts = array(38,37,22,45,35,44,34,43,18,5,17);
		$yt_fmt = empty($_REQUEST['yt_fmt']) ? '' : $_REQUEST['yt_fmt'];
		$this->fmturlmaps = $this->GetVideosArr($fmt_url_maps);

		if (empty($yt_fmt) && !isset($_GET['audl'])) return $this->QSelector($link);
		elseif (isset($_REQUEST['ytube_mp4']) && $_REQUEST['ytube_mp4'] == 'on' && !empty($yt_fmt)) {
			//look for and download the highest quality we can find?
			if ($yt_fmt == 'highest') {
				foreach ($this->fmts as $fmt) if (array_key_exists($fmt, $this->fmturlmaps)) {
					$furl = $this->fmturlmaps[$fmt];
					break;
				}
			} elseif (!$furl = $this->fmturlmaps[$yt_fmt]) html_error('Specified video format not found');
			else $fmt = $yt_fmt;
		} else { //just get the one Youtube plays by default (in some cases it could also be the highest quality format)
			$fmt = key($this->fmturlmaps);
			$furl = $this->fmturlmaps[$fmt];
		}

		$ext = '.flv';
		$fmtexts = array('.3gp' => array(17), '.mp4' => array(18,22,37,38), '.webm' => array(43,44,45));
		foreach ($fmtexts as $k => $v) {
			if (!is_array($v)) $v = array($v);  
			if (in_array($fmt, $v)) {
				$ext = $k;
				break;
			}
		}

		if (empty($response['title'])) html_error('No video title found! Download halted.');
		$FileName = str_replace(str_split('\\/:*?"<>|'), '_', html_entity_decode(trim($response['title']), ENT_QUOTES)) . "-[YT-f$fmt][{$this->vid}]$ext";

		if (isset($_REQUEST['ytdirect']) && $_REQUEST['ytdirect'] == 'on') {
			echo "<br /><br /><h4><a style='color:yellow' href='" . urldecode($furl) . "'>Click here or copy the link to your download manager to download</a></h4> (This may not work)";
			echo "<input name='dlurl' style='width: 1000px; border: 1px solid #55AAFF; background-color: #FFFFFF; padding:3px' value='" . urldecode($furl) . "' onclick='javascript:this.select();' readonly></input>";
		} else $this->RedirectDownload($furl, $FileName, $this->cookie, 0, 0, $FileName);
	}

	private function FormToArr($content, $v1 = '&', $v2 = '=') {
		$rply = array();
		if (strpos($content, $v1) === false || strpos($content, $v2) === false) return $rply;
		foreach (array_filter(array_map('trim', explode($v1, $content))) as $v) {
			$v = array_map('trim', explode($v2, $v, 2));
			if ($v[0] != '') $rply[$v[0]] = $v[1];
		}
		return $rply;
	}

	private function captcha($link) {
		$url = 'http://www.youtube.com/das_captcha?next=' . urlencode($link);
		if (isset($_REQUEST['step']) && $_REQUEST['step'] == '1') {
			if (empty($_POST['recaptcha_response_field'])) html_error('You didn\'t enter the image verification code.');
			$post = array('recaptcha_challenge_field' => $_POST['recaptcha_challenge_field'], 'recaptcha_response_field' => $_POST['recaptcha_response_field']);
			$post['next'] = $_POST['next'];
			$post['action_recaptcha_verify'] = $_POST['action_recaptcha_verify'];
			$post['submit'] = $_POST['_submit'];
			$post['session_token'] = $_POST['session_token'];
			$cookie = urldecode($_POST['cookie']);

			$page = $this->GetPage($url, $cookie, $post, $url);
			is_present($page, 'The verification code was invalid', 'The verification code was invalid or has timed out, please try again.');
			is_present($page, "\r\n\r\nAuthorization Error.", 'Error sending captcha.');
			is_notpresent($page, 'Set-Cookie: goojf=', 'Cannot get captcha cookie.');

			$this->cookie = GetCookiesArr($page);
			$this->page = $this->GetPage('http://www.youtube.com/get_video_info?video_id='.$this->vid.'&asv=3&el=detailpage&hl=en_US', $this->cookie);
		} else {
			$page = $this->GetPage($url);
			if (!preg_match('@//(?:[^/]+\.)?(?:(?:google\.com/recaptcha/api)|(?:recaptcha\.net))/(?:(?:challenge)|(?:noscript))\?k=([\w|\-]+)@i', $page, $pid)) html_error('Error: reCAPTCHA not found.');

			$data = $this->DefaultParamArr($link, GetCookies($page));
			$data['next'] = urlencode(html_entity_decode(cut_str($page, 'name="next" value="', '"')));
			$data['action_recaptcha_verify'] = urlencode(cut_str($page, 'name="action_recaptcha_verify" value="', '"'));
			$data['_submit'] = urlencode(cut_str($page, 'type="submit" name="submit" value="', '"'));
			$data['session_token'] = urlencode(cut_str($page, "'XSRF_TOKEN': '", "'"));
			if (isset($_REQUEST['ytube_mp4'])) $data['ytube_mp4'] = $_REQUEST['ytube_mp4'];
			if (isset($_REQUEST['ytdirect'])) $data['ytdirect'] = $_REQUEST['ytdirect'];
			if (isset($_REQUEST['yt_fmt'])) $data['yt_fmt'] = $_REQUEST['yt_fmt'];
			$data['step'] = 1;

			$this->Show_reCaptcha($pid[1], $data);
		}
	}

	private function Show_reCaptcha($pid, $inputs, $sname = 'Download File') {
		global $PHP_SELF;
		if (!is_array($inputs)) html_error('Error parsing captcha data.');

		// Themes: 'red', 'white', 'blackglass', 'clean'
		echo "<script language='JavaScript'>var RecaptchaOptions = {theme:'red', lang:'en'};</script>\n\n<center><form name='recaptcha' action='$PHP_SELF' method='POST'><br />\n";
		foreach ($inputs as $name => $input) echo "<input type='hidden' name='$name' id='C_$name' value='$input' />\n";
		echo "<script type='text/javascript' src='http://www.google.com/recaptcha/api/challenge?k=$pid'></script><noscript><iframe src='http://www.google.com/recaptcha/api/noscript?k=$pid' height='300' width='500' frameborder='0'></iframe><br /><textarea name='recaptcha_challenge_field' rows='3' cols='40'></textarea><input type='hidden' name='recaptcha_response_field' value='manual_challenge' /></noscript><br /><input type='submit' name='submit' onclick='javascript:return checkc();' value='$sname' />\n<script type='text/javascript'>/*<![CDATA[*/\nfunction checkc(){\nvar capt=document.getElementById('recaptcha_response_field');\nif (capt.value == '') { window.alert('You didn\'t enter the image verification code.'); return false; }\nelse { return true; }\n}\n/*]]>*/</script>\n</form></center>\n</body>\n</html>";
		exit;
	}

	private function GetVideosArr($fmtmaps) {
		$fmturls = array();
		foreach ($fmtmaps as $fmtlist) {
			$fmtlist = array_map('urldecode', $this->FormToArr($fmtlist));
			$fmturls[$fmtlist['itag']] = $fmtlist['url'];
			if (stripos($fmtlist['url'], '&signature=') === false) $fmturls[$fmtlist['itag']] .= '&signature='.$fmtlist['sig'];
		}
		return $fmturls;
	}

	private function QSelector($link) {
		global $PHP_SELF;
		$fmtlangs = array(38 => 377, 37 => 228, 22 => 227, 45 => 225, 35 => 223, 44 => 389, 34 => 222, 43 => 224, 18 => 226, 5 => 221, 17 => 220);

		$sizes = array();
		/* Add a // at the start of this line for enable this code.
		if (extension_loaded('curl') && function_exists('curl_init') && function_exists('curl_exec')) {
			$sizes = array();
			$opt = array(CURLOPT_FOLLOWLOCATION => true, CURLOPT_MAXREDIRS => 5, CURLOPT_NOBODY => true); // Redirects may fail with open_basedir enabled
			if (!empty($this->cookie)) $opt[CURLOPT_COOKIE] = CookiesToStr($this->cookie);
			foreach ($this->fmturlmaps as $fmt => $url) {
				if (!in_array($fmt, $this->fmts)) continue;
				$headers = explode("\r\n\r\n", cURL($url, $this->cookie, 0, 0, 0, $opt));
				$headers = ((count($headers) > 2) ? $headers[count($headers) - 2] : $headers[0]) . "\r\n\r\n";
				if (substr($headers, 9, 3) == '200' && ($CL = cut_str($headers, "\nContent-Length: ", "\n")) && $CL > 1024) $sizes[$fmt] = bytesToKbOrMbOrGb(trim($CL));
			}
			unset($headers, $CL);
		} //*/

		echo "\n<br /><br /><h3 style='text-align: center;'>".lang(216).".</h4>";
		echo "\n<center><form name='YT_QS' action='$PHP_SELF' method='POST'>\n";
		echo "<input type='hidden' name='yt_QS' value='on' />\n";
		echo '<input type="checkbox" name="ytdirect" /><small>&nbsp;'.lang(217).'</small><br />';
		echo "<select name='yt_fmt' id='QS_fmt'>\n";
		foreach ($this->fmturlmaps as $fmt => $url) if (in_array($fmt, $this->fmts)) echo '<option '.($fmt == 18 ? "selected='selected' " : '')."value='$fmt'>".lang($fmtlangs[$fmt]).(!empty($sizes[$fmt]) ? ' ('.$sizes[$fmt].')' : '')."</option>\n";
		echo "</select>\n";
		if (count($this->cookie) > 0) $this->cookie = encrypt(CookiesToStr($this->cookie));
		$data = $this->DefaultParamArr($link, $this->cookie);
		$data['ytube_mp4'] = 'on';
		foreach ($data as $n => $v) echo("<input type='hidden' name='$n' id='QS_$n' value='$v' />\n");

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
// [17-5-2012]  Fixed captcha (Now uses reCaptcha). - Th3-822
// [14-9-2012]  Fixed Download links & small changes. - Th3-822
// [07-10-2012]  Fixed for redirect at link. - Th3-822
// [02-1-2013]  Using new way for getting links and video info, now it doesn't need login for restricted videos. - Th3-822

?>