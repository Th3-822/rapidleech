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

		if (host_matches('youtu.be', $url['host'])) preg_match('@/([\w\-\.]{11})@i', $url['path'], $this->vid);
		elseif (empty($url['query']) || ($this->vid[1] = cut_str('&'.$url['query'].'&', '&v=', '&')) === false || !preg_match('@^[\w\-\.]{11}$@i', $this->vid[1])) preg_match('@/(?:v|(?:embed))/([\w\-\.]{11})@i', $url['path'], $this->vid);

		if (empty($this->vid[1])) html_error('Video ID not found.');
		$this->vid = $this->vid[1];
		$link = 'http://www.youtube.com/watch?v='.$this->vid;

		$this->page = $this->GetPage('http://www.youtube.com/get_video_info?video_id='.$this->vid.'&asv=3&el=detailpage&hl=en_US&s'.'t'.'s'.'=0', $this->cookie);
		$response = array_map('urldecode', $this->FormToArr(substr($this->page, strpos($this->page, "\r\n\r\n") + 4)));
		if (!empty($response['reason'])) html_error('['.htmlentities($response['errorcode']).'] '.htmlentities($response['reason']));

		if (isset($_REQUEST['step']) || preg_match('@Location: https?://(www\.)?youtube\.com/das_captcha@i', $this->page)) $this->captcha($link);

		if (empty($response['url_encoded_fmt_stream_map'])) html_error('Video links not found.');
		$fmt_url_maps = explode(',', $response['url_encoded_fmt_stream_map']);

		$this->fmts = array(38,37,46,22,45,44,35,43,34,18,6,5,36,17);
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
		$fmtexts = array('.mp4' => array(18,22,37,38), '.webm' => array(43,44,45,46), '.3gp' => array(36,17));
		foreach ($fmtexts as $k => $v) {
			if (!is_array($v)) $v = array($v);  
			if (in_array($fmt, $v)) {
				$ext = $k;
				break;
			}
		}

		if (empty($response['title'])) html_error('No video title found! Download halted.');
		$FileName = str_replace(str_split('\\:*?"<>|=;'."\t\r\n\f"), '_', html_entity_decode(trim($response['title']), ENT_QUOTES));
		if (!empty($_REQUEST['cleanname'])) $FileName = preg_replace('@[^ A-Za-z_\-\d\.,\(\)\[\]\{\}&\!\'\@\%\#]@u', '_', $FileName);
		$FileName .= " [YT-f$fmt][{$this->vid}]$ext";

		$this->RedirectDownload($furl, $FileName, $this->cookie, 0, 0, $FileName);
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

			$this->reCAPTCHA($pid[1], $data);
		}
	}

	private function sigDecode($sig) {
		html_error('Encoded signature found D:');
	}

	private function GetVideosArr($fmtmaps) {
		$fmturls = array();
		foreach ($fmtmaps as $fmtlist) {
			$fmtlist = array_map('urldecode', $this->FormToArr($fmtlist));
			if (!in_array($fmtlist['itag'], $this->fmts)) continue;
			$fmtlist['url'] = parse_url($fmtlist['url']);
			$fmtlist['url']['query'] = array_map('urldecode', $this->FormToArr($fmtlist['url']['query']));
			if (empty($fmtlist['url']['query']['signature'])) $fmtlist['url']['query']['signature'] = (!empty($fmtlist['s']) ? $this->sigDecode($fmtlist['s']) : $fmtlist['sig']);
			foreach (array_diff(array_keys($fmtlist), array('signature', 'sig', 's', 'url')) as $k) $fmtlist['url']['query'][$k] = $fmtlist[$k];
			ksort($fmtlist['url']['query']);
			$fmtlist['url']['query'] = http_build_query($fmtlist['url']['query']);
			$fmturls[$fmtlist['itag']] = rebuild_url($fmtlist['url']);
		}
		return $fmturls;
	}

	private function QSelector($link) {
		$VR = array('>1080', 1080, 720, 480, 360, 270, 240, 144);
		$VC = array('MP4', 'WebM', 'FLV', '3GP');
		$AC = array('AAC', 'Vorbis', 'MP3');
		$AB = array(192, 128, 96, 64, 36, 24);
		$vinfo = array(38=>'0000',37=>'1000',46=>'1110',22=>'2000',45=>'2110',44=>'3111',35=>'3201',43=>'4111',34=>'4201',18=>'4002',6=>'5223',5=>'6223',36=>'6304',17=>'7305'); // VR VC AC AB

		$sizes = array();
		/* Add a // at the start of this line for enable this code.
		if (extension_loaded('curl') && function_exists('curl_init') && function_exists('curl_exec')) {
			$sizes = array();
			$opt = array(CURLOPT_FOLLOWLOCATION => true, CURLOPT_MAXREDIRS => 5, CURLOPT_NOBODY => true); // Redirects may fail with open_basedir enabled
			foreach ($this->fmturlmaps as $fmt => $url) {
				if (!in_array($fmt, $this->fmts)) continue;
				$headers = explode("\r\n\r\n", cURL($url, $this->cookie, 0, 0, 0, $opt));
				$headers = ((count($headers) > 2) ? $headers[count($headers) - 2] : $headers[0]) . "\r\n\r\n";
				if (substr($headers, 9, 3) == '200' && ($CL = cut_str($headers, "\nContent-Length: ", "\n")) && $CL > 1024) $sizes[$fmt] = bytesToKbOrMbOrGb(trim($CL));
			}
			unset($headers, $CL);
		} //*/

		echo "\n<br /><br /><h3 style='text-align: center;'>".lang(216).".</h4>";
		echo "\n<center><form name='YT_QS' action='{$GLOBALS['PHP_SELF']}' method='POST'>\n";
		echo "<input type='hidden' name='yt_QS' value='on' />\n";
		echo '<label><input type="checkbox" name="cleanname" checked="checked" /><small>&nbsp;Remove non-supported characters from filename</small></label><br />';
		echo "<select name='yt_fmt' id='QS_fmt'>\n";
		foreach ($this->fmturlmaps as $fmt => $url) if (in_array($fmt, $this->fmts) && ($I = str_split($vinfo[$fmt]))) echo '<option '.($fmt == 18 ? "selected='selected' " : '')."value='$fmt'>[$fmt] Video: {$VC[$I[1]]} {$VR[$I[0]]}p | Audio: {$AC[$I[2]]} ~{$AB[$I[3]]} kbps".(!empty($sizes[$fmt]) ? ' ('.$sizes[$fmt].')' : '')."</option>\n";
		echo "</select>\n";
		if (count($this->cookie) > 0) $this->cookie = encrypt(CookiesToStr($this->cookie));
		$data = $this->DefaultParamArr($link, $this->cookie);
		$data['ytube_mp4'] = 'on';
		foreach ($data as $n => $v) echo("<input type='hidden' name='$n' id='QS_$n' value='$v' />\n");

		echo "<input type='submit' name='Th3-822' value='".lang(209)."' />\n";
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
// [02-10-2013]  Fixed issues with videos with ciphered signature & Rewritten quality selector (Now it doesn't use lang) & Remove direct-link option & Added option for sanitize filenames & small changes. - Th3-822
// [04-3-2014]  Re-Added Support for 3GP quality. - Th3-822

?>