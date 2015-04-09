<?php

if (!defined('RAPIDLEECH')) {
	require_once('index.html');
	exit();
}

class youtube_com extends DownloadClass {
	private $page, $cookie, $fmturlmaps, $vid, $sts, $js, $playerJs, $sigJs, $jsVars,
		$fmts = array(38,37,46,22,45,44,35,43,34,18,6,5,36,17);

	public function Download($link) {
		$this->cookie = isset($_POST['yt_QS']) && !empty($_POST['cookie']) ? StrToCookies(decrypt(urldecode($_POST['cookie']))) : array();
		$url = parse_url($link);
		$this->vid = array();

		if (host_matches('youtu.be', $url['host'])) preg_match('@/([\w\-\.]{11})@i', $url['path'], $this->vid);
		elseif (empty($url['query']) || ($this->vid[1] = cut_str('&'.$url['query'].'&', '&v=', '&')) === false || !preg_match('@^[\w\-\.]{11}$@i', $this->vid[1])) preg_match('@/(?:v|(?:embed))/([\w\-\.]{11})@i', $url['path'], $this->vid);

		if (empty($this->vid[1])) html_error('Video ID not found.');
		$this->vid = $this->vid[1];
		$this->link = 'https://www.youtube.com/watch?v='.$this->vid;

		$this->getFmtMaps();

		$this->fmturlmaps = $this->GetVideosArr();

		$yt_fmt = empty($_REQUEST['yt_fmt']) ? '' : $_REQUEST['yt_fmt'];
		if (empty($yt_fmt) && !isset($_GET['audl'])) return $this->QSelector();
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

		if (empty($this->response['title'])) html_error('No video title found! Download halted.');
		$FileName = str_replace(str_split('\\\:*?"<>|=;'."\t\r\n\f"), '_', html_entity_decode(trim($this->response['title']), ENT_QUOTES));
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

	private function captcha() {
		$url = 'https://www.youtube.com/das_captcha?next=' . urlencode($this->link);
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
			unset($_REQUEST['step']);
			$this->getFmtMaps();
		} else {
			$page = $this->GetPage($url);
			if (!preg_match('@//(?:[^/]+\.)?(?:(?:google\.com/recaptcha/api)|(?:recaptcha\.net))/(?:(?:challenge)|(?:noscript))\?k=([\w\.\-]+)@i', $page, $pid)) html_error('Error: reCAPTCHA not found.');

			$data = $this->DefaultParamArr($this->link, GetCookies($page));
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

	private function getFmtMaps() {
		$this->page = $this->GetPage('https://www.youtube.com/get_video_info?video_id='.$this->vid.'&asv=3&el=ve'.'vo&hl=en_US&s'.'t'.'s'.'='.(!empty($this->sts) ? urlencode($this->sts) : 0), $this->cookie);
		$this->response = array_map('urldecode', $this->FormToArr(substr($this->page, strpos($this->page, "\r\n\r\n") + 4)));
		if (!empty($this->response['reason'])) html_error('['.htmlspecialchars($this->response['errorcode']).'] '.htmlspecialchars($this->response['reason']));

		if (isset($_REQUEST['step']) || preg_match('@Location: https?://(www\.)?youtube\.com/das_captcha@i', $this->page)) $this->captcha();

		if (empty($this->response['url_encoded_fmt_stream_map'])) html_error("[{$this->sts}] Video links not found.");
		$this->fmtmaps = explode(',', $this->response['url_encoded_fmt_stream_map']);
	}

	private function decError($msg) {
		html_error("Error while decoding [{$this->sts}][{$this->js[1]}]: $msg");
	}

	private function findFunction($fName, $num) {
		if ($fName == 'T8') return "w$num";
		$obj = explode('.', $fName, 3);
		if (count($obj) > 2) $this->decError("Cannot search function: '$fName'");
		if (count($obj) > 1) {
			$fName = $obj[1];
			$obj = $obj[0];
			if (empty($this->jsVars[$obj]['src'])) {
				if (($spos = strpos($this->playerJs, "var $obj={")) === false || ($epos = strpos($this->playerJs, '};', $spos)) === false) $this->decError("Cannot find object '$obj'");
				$spos += strlen("var $obj={");
				$this->jsVars[$obj] = array('src' => substr($this->playerJs, $spos, $epos - $spos), 'fn' => array());
			}
			if (empty($this->jsVars[$obj]['fn'][$fName]['step'])) {
				$v = '[\$_A-Za-z][\$\w]*';
				if (!preg_match("@(?<=^|,)$fName:function\($v(?:,($v))?\)\{([^}]+)\}(?=,|$)@", $this->jsVars[$obj]['src'], $src)) $this->decError("Cannot find function '$obj.$fName'");
				$this->jsVars[$obj]['fn'][$fName] = array('src' => $src);

				if (empty($src[1])) return $this->jsVars[$obj]['fn'][$fName]['step'] = 'r';
				elseif (preg_match("@var\s+($v)=($v)\[0\];\\2\[0\]=\\2\[{$src[1]}%\\2(?:\.length|\[$v\])\];\\2\[{$src[1]}\]=\\1@", $src[2])) {
					$this->jsVars[$obj]['fn'][$fName]['step'] = 'w%d';
					return "w$num";
				} elseif (preg_match("@(?:$v=)?$v(?:\.s(p)?lice|\[$v\])\((?(1)0,){$src[1]}\)@", $src[2])) {
					$this->jsVars[$obj]['fn'][$fName]['step'] = 's%d';
					return "s$num";
				} elseif (preg_match("@(?:$v=)?$v(?:\.reverse|\[$v\])\(\)@", $src[2])) return $this->jsVars[$obj]['fn'][$fName]['step'] = 'r';
				else $this->decError("Error parsing function '$obj.$fName'");
			} else return sprintf($this->jsVars[$obj]['fn'][$fName]['step'], $num);
		}
		if (empty($this->jsVars[$fName]['step'])) {
			if (($spos = strpos($this->playerJs, "function $fName(")) === false || ($epos = strpos($this->playerJs, '};', $spos)) === false) $this->decError("Cannot find function '$fName'");
			$this->jsVars[$fName] = array('src' => substr($this->playerJs, $spos, $epos - $spos));
			$v = '[\$_A-Za-z][\$\w]*';
			if (!preg_match("@^function\s+$fName\($v(?:,($v))?\)\{(.*)$@", $this->jsVars[$fName]['src'], $pars)) $this->decError("Cannot parse function '$fName'");
			if (empty($pars[1])) return $this->jsVars[$fName]['step'] = 'r';
			elseif (preg_match("@var\s+($v)=($v)\[0\];\\2\[0\]=\\2\[{$pars[1]}%\\2(?:\.length|\[$v\])\];\\2\[{$pars[1]}\]=\\1@", $src[2])) {
				$this->jsVars[$fName]['step'] = 'w%d';
				return "w$num";
			} elseif (preg_match("@(?:$v=)?$v(?:\.s(p)?lice|\[$v\])\((?(1)0,){$src[1]}\)@", $src[2])) {
				$this->jsVars[$fName]['step'] = 's%d';
				return "s$num";
			} elseif (preg_match("@(?:$v=)?$v(?:\.reverse|\[$v\])\(\)@", $src[2])) return $this->jsVars[$fName]['step'] = 'r';
			else $this->decError("Error parsing function '$fName'");
		} else return sprintf($this->jsVars[$fName]['step'], $num);
	}

	// getCipher & sigDecode are based on jwz's youtubedown code.
	private function getCipher() {
		$this->changeMesg('<br />Video with ciphered signature, trying to decode it.', 1);
		$page = $this->GetPage('https://www.youtube.com/embed/'.$this->vid, $this->cookie);
		$this->cookie = GetCookiesArr($page, $this->cookie);

		if (!preg_match('@"sts"\s*:\s*(\d+)@i', $page, $this->sts)) html_error('Signature timestamp not found.');
		$this->sts = $this->sts[1];

		$savefile = DOWNLOAD_DIR.'YT_lastjs.txt';
		if (!preg_match('@html5player-([\w\-\.]+(?:/\w+)?)\.js@i', str_replace('\\/', '/', $page), $this->js)) html_error('YT\'s player javascript not found.');
		if (@file_exists($savefile) && ($file = file_get_contents($savefile, NULL, NULL, -1, 822)) && ($saved = @unserialize($file)) && is_array($saved) && !empty($saved['sts']) && $saved['sts'] == $this->sts && !empty($saved['steps']) && preg_match('@^\s*([ws]\d+|r)( ([ws]\d+|r))*\s*$@', $saved['steps'])) {
			$this->encS = explode(' ', trim($saved['steps']));
		} else {
			$this->playerJs = $this->GetPage('https://s.ytimg.com/yts/jsbin/'.$this->js[0], $this->cookie, 0, 'https://www.youtube.com/embed/'.$this->vid);
			if (($spos = strpos($this->playerJs, '.sig||')) === false) $this->decError('Not found (".sig||")');
			if (($cut1 = cut_str(substr($this->playerJs, $spos), '{', '}')) == false) $this->decError('Cannot get inner content of "if(X.sig||X.s)"');
			$v = '[\$_A-Za-z][\$\w]*';
			if (!preg_match("@(?<=\.sig\|\|)$v(?=\($v\.s\))@", $cut1, $fn)) $this->decError('Cannot get decoder function name');
			$fn = $fn[0];
			if (($fpos = strpos($this->playerJs, "function $fn(")) === false) $this->decError('Cannot find decoder function');
			if (($cut2 = cut_str(substr($this->playerJs, $fpos), '{', '}')) == false) $this->decError('Cannot get decoder function contents');
			$this->sigJs = preg_replace("@var $v=$v\[0\];$v\[0\]=($v)\[(\d+)%$v(?:\.length|\[$v\])\];$v\[\d+\]=$v;@", '$1=T8($1,$2);', trim($cut2));
			$this->encS = array();
			foreach (array_map('trim', explode(';', '{'.$this->sigJs.'}')) as $step) {
				if (($step{0} == '{' || substr($step, strlen($step) - 1, 1) == '}') && (preg_match("@^\{(?:var\s+)?$v=$v(?:\.split|\[$v\])\(\"\"\)$@", $step) || preg_match("@^return\s+$v(?:\.join|\[$v\])\(\"\"\);?\}$@", $step))) continue;
				elseif (preg_match("@^(?:$v=)?((?:$v.)*$v)\($v\,(\d+)\)$@", $step, $s)) $this->encS[] = $this->findFunction($s[1], $s[2]);
				//elseif (preg_match("@^$v=$v\($v\,(\d+)\)$@", $step, $s)) $this->encS[] = 'w'.$s[1];
				elseif (preg_match("@^(?:$v=)?$v(?:\.s(p)?lice|\[$v\])\((?(1)0,)(\d+)\)$@", $step, $s)) $this->encS[] = 's'.$s[2];
				elseif (preg_match("@^(?:$v=)?$v(?:\.reverse|\[$v\])\(\)$@", $step)) $this->encS[] = 'r';
				else $this->decError($step.' | Unknown step on decoder function.');
			}

			if (empty($this->encS)) $this->decError('Empty decoded result');
			file_put_contents($savefile, serialize(array('sts' => $this->sts, 'js' => $this->js[1], 'steps' => implode(' ', $this->encS))));
		}

		// Request video fmts with the current sts
		$this->getFmtMaps();

		return $this->GetVideosArr();
	}

	private function sigDecode($sig) {
		if (empty($this->encS)) $this->decError('sigDecode() can\'t be called before getCipher()');
		$_sig = $sig;
		$sig = str_split($sig);
		foreach ($this->encS as $_step) {
			if (!preg_match('@^\s*([wrs])(\d*)\s*$@', $_step, $step) || ($step[1] != 'r' && !array_key_exists(2, $step))) $this->decError("Unknown decoding step \"$_step\"");
			switch ($step[1]) {
				case 'w': $step[2] = (int)$step[2];$x = $sig[0];$sig[0] = $sig[$step[2] % count($sig)];$sig[$step[2]] = $x; break;
				case 's': $step[2] = (int)$step[2];$sig = array_slice($sig, $step[2]); break;
				case 'r': $sig = array_reverse($sig); break;
			}
		}
		return implode($sig);
	}

	private function GetVideosArr() {
		$fmturls = array();
		foreach ($this->fmtmaps as $fmtlist) {
			$fmtlist = array_map('urldecode', $this->FormToArr($fmtlist));
			if (!in_array($fmtlist['itag'], $this->fmts)) continue;
			if (!empty($fmtlist['s']) && empty($this->encS)) {
				if (empty($this->sts)) return $this->getCipher();
				else $this->decError('No decoded steps');
			}
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

	private function QSelector() {
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
		echo '<label><input type="checkbox" name="cleanname" checked="checked" value="1" /><small>&nbsp;Remove non-supported characters from filename</small></label><br />';
		echo "<select name='yt_fmt' id='QS_fmt'>\n";
		foreach ($this->fmturlmaps as $fmt => $url) if (in_array($fmt, $this->fmts) && ($I = str_split($vinfo[$fmt]))) echo '<option '.($fmt == 18 ? "selected='selected' " : '')."value='$fmt'>[$fmt] Video: {$VC[$I[1]]} {$VR[$I[0]]}p | Audio: {$AC[$I[2]]} ~{$AB[$I[3]]} kbps".(!empty($sizes[$fmt]) ? ' ('.$sizes[$fmt].')' : '')."</option>\n";
		echo "</select>\n";
		if (count($this->cookie) > 0) $this->cookie = encrypt(CookiesToStr($this->cookie));
		$data = $this->DefaultParamArr($this->link, $this->cookie);
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
// [16-3-2014]  Added functions for decoding ciphered signatures. - Th3-822
// [28-7-2014]  Fixed signature decoding functions. - Th3-822
// [17-12-2014]  Forced https on all the requests for avoid redirect errors. - Th3-822
// [14-1-2015]  Fixed Age Restrictions. (Please, do not annoy my inbox when a plugin fails, go to the forum) - Th3-822
// [21-1-2015]  Fixed backslash in filename when cleanname is off. - Th3-822

?>