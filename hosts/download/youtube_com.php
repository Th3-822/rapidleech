<?php

if (!defined('RAPIDLEECH')) {
	require_once('index.html');
	exit();
}

class youtube_com extends DownloadClass {
	private $page, $cookie, $fmtmaps, $vid, $sts = -1, $js, $playerJs, $sigJs, $jsVars, $cookieFile,
		$fmts = array(22 => '0|720|0|192', 45 => '1|720|1|192', 44 => '1|480|1|192', 35 => '2|480|0|128', 43 => '1|360|1|128', 34 => '2|360|0|128', 18 => '0|360|0|96', 6 => '2|270|3|64', 5 => '2|240|3|64', 36 => '3|240|0|36', 17 => '3|144|0|24');

	public function Download($link) {
		$this->cookieFile = DOWNLOAD_DIR.'YT_cookie.txt';
		$this->cookie = isset($_POST['step']) && !empty($_POST['cookie']) ? StrToCookies(decrypt(urldecode($_POST['cookie']))) : $this->loadCookie();
		$url = parse_url($link);
		$this->vid = array();

		if (host_matches('youtu.be', $url['host'])) preg_match('@/([\w\-\.]{11})@i', $url['path'], $this->vid);
		else if (empty($url['query']) || ($this->vid[1] = cut_str('&'.$url['query'].'&', '&v=', '&')) === false || !preg_match('@^[\w\-\.]{11}$@i', $this->vid[1])) preg_match('@/(?:v|(?:embed))/([\w\-\.]{11})@i', $url['path'], $this->vid);

		if (empty($this->vid[1])) html_error('Video ID not found.');
		$this->vid = $this->vid[1];
		$this->link = 'https://www.youtube.com/watch?v='.$this->vid;

		if (empty($_POST['step'])) $this->getFmtMaps();
		else $this->captcha();

		$yt_fmt = empty($_REQUEST['yt_fmt']) ? '' : $_REQUEST['yt_fmt'];
		if (empty($yt_fmt) && !isset($_GET['audl'])) return $this->QSelector();
		else if (isset($_REQUEST['ytube_mp4']) && $_REQUEST['ytube_mp4'] == 'on' && !empty($yt_fmt)) {
			//look for and download the highest quality we can find?
			if ($yt_fmt == 'highest') {
				foreach (array_keys($this->fmts) as $itag) if (!empty($this->fmtmaps[$itag])) break;
			} else if (!empty($this->fmtmaps[$yt_fmt])) {
				$itag = $yt_fmt;
			} else html_error('Specified video format not found');
		} else { //just get the one Youtube plays by default (in some cases it could also be the highest quality format)
			$itag = key($this->fmtmaps);
		}
		$fmt = $this->fmtmaps[$itag];
		$is_dash = empty($this->fmts[$itag]);

		if (empty($this->response['title'])) {
			if (empty($this->response['player_response']['videoDetails']['title'])) html_error('No video title found! Download halted.');
			else $this->response['title'] = $this->response['player_response']['videoDetails']['title'];
		}
		$filename = str_replace(str_split('\\\:*?"<>|=;'."\t\r\n\f"), '_', html_entity_decode(trim($this->response['title']), ENT_QUOTES));
		if (!empty($_REQUEST['cleanname'])) $filename = preg_replace('@[^ A-Za-z_\-\d\.,\(\)\[\]\{\}&\!\'\@\%\#]@u', '_', $filename);
		if (!empty($fmt['qualityLabel'])) $filename .= " [YT-{$fmt['qualityLabel']}]";
		else if (!empty($fmt['quality_label'])) $filename .= " [YT-{$fmt['quality_label']}]";
		else if (!$is_dash) {
			$v = explode('|', $this->fmts[$itag]);
			$filename .= " [YT-{$v[1]}p]";
		} else $filename .= " [YT-Audio]";
		$filename .= sprintf('[%s].%s', $this->vid, ($is_dash ? 'dash.' : '') . $fmt['_codecs']['container']);

		$this->RedirectDownload($fmt['url'], $filename, $this->cookie, 0, ($is_dash ? $this->link . "\r\nRange: bytes=0-" : 0), $filename);
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
		$url = 'https://www.youtube.com/das_captcha';
		if (!empty($_POST['step']) && $_POST['step'] == '1') {
			$post = $this->verifyReCaptchav2();
			$post['action_recaptcha_verify2'] = '1';
			$post['session_token'] = $_POST['session_token'];

			$page = $this->GetPage($url, $this->cookie, $post, $url);
			is_present($page, 'The verification code was invalid', 'The verification code was invalid or has timed out, please try again.');
			is_present($page, "\r\n\r\nAuthorization Error.", 'Error sending captcha.');
			is_notpresent($page, 'Set-Cookie: goojf=', 'Cannot get captcha cookie.');

			$this->cookie = GetCookiesArr($page, $this->cookie);
			unset($_POST['step']);
			$this->getFmtMaps();
		} else {
			$page = $this->GetPage($url, $this->cookie);
			$this->cookie = GetCookiesArr($page, $this->cookie);
			if (!preg_match('@class="g-recaptcha" data-sitekey="([\w\.\-]+)"@i', $page, $pid)) html_error('Error: reCAPTCHA2 not found.');

			$data = $this->DefaultParamArr($this->link, $this->cookie, 1, 1);
			$data['session_token'] = urlencode(cut_str($page, 'name="session_token" value="', '"'));
			if (isset($_REQUEST['ytube_mp4'])) $data['ytube_mp4'] = $_REQUEST['ytube_mp4'];
			if (isset($_REQUEST['ytdirect'])) $data['ytdirect'] = $_REQUEST['ytdirect'];
			if (isset($_REQUEST['yt_fmt'])) $data['yt_fmt'] = $_REQUEST['yt_fmt'];
			$data['step'] = '1';

			$this->reCAPTCHAv2($pid[1], $data);
		}
	}

	private function loadCookie() {
		if (@file_exists($this->cookieFile) && ($file = file_get_contents($this->cookieFile)) && ($saved = @unserialize($file)) && is_array($saved) && !empty($saved['hash']) && !empty($saved['cookie']) && ($saved['cookie'] = decrypt(base64_decode($saved['cookie']))) && sha1($saved['cookie']) == $saved['hash']) {
			return StrToCookies($saved['cookie']);
		}
		return array();
	}

	private function saveCookie() {
		if (!empty($this->cookie)) {
			$data = array('cookie' => CookiesToStr($this->cookie));
			$data['hash'] = sha1($data['cookie']);
			$data['cookie'] = base64_encode(encrypt($data['cookie']));
			file_put_contents($this->cookieFile, serialize($data));
		}
	}

	// Special Function Called by verifyReCaptchav2 When Captcha Is Incorrect, To Allow Retry. - Required
	protected function retryReCaptchav2() {
		$data = $this->DefaultParamArr($this->link, $this->cookie, 1, 1);
		foreach (array('step', 'action_recaptcha_verify2', 'session_token', 'ytube_mp4', 'ytdirect', 'yt_fmt') as $name) {
			if (!empty($_POST[$name])) $data[$name] = $_POST[$name];
		}
		return $this->reCAPTCHAv2($_POST['recaptcha2_public_key'], $data);
	}

	private function queryVideo($alt = 0) {
		$this->page = $this->GetPage('https://www.youtube.com/get_video_info?hl=en_US&video_id=' . $this->vid . ($alt ? '&eurl=https%3A%2F%2Fgoogle.com%2F' : '&el=detailpage') . ($this->sts > 0 ? '&sts=' . $this->sts : ''), $this->cookie);
		$this->cookie = GetCookiesArr($this->page, $this->cookie);
		$this->response = array_map('urldecode', $this->FormToArr(substr($this->page, strpos($this->page, "\r\n\r\n") + 4)));
		if (!empty($this->response['requires_purchase'])) html_error('[Unsupported Video] This Video or Channel Requires a Payment to Watch.');
		$this->response['player_response'] = json_decode($this->response['player_response'], true);
	}

	private function getFmtMaps() {
		$this->queryVideo();
		if (!empty($this->response['errorcode']) && $this->response['errorcode'] == 150 && $this->response['errordetail'] == 1) $this->queryVideo(1);

		if (!empty($this->response['reason'])) html_error('['.htmlspecialchars($this->response['errorcode']).'] '.htmlspecialchars($this->response['reason']));

		if (!empty($this->response['player_response']['playabilityStatus']['reason'])) html_error('['.htmlspecialchars($this->response['player_response']['playabilityStatus']['reason']).'] '.htmlspecialchars($this->response['player_response']['playabilityStatus']['errorScreen']['playerErrorMessageRenderer']['subreason']['simpleText']));

		if (!empty($this->response['player_response']['streamingData']['formats'])) {
			$this->response['_formats'] = $this->response['player_response']['streamingData']['formats'];
		}
		if (!empty($this->response['player_response']['streamingData']['adaptiveFormats'])) {
			$this->response['_adaptiveFormats'] = $this->response['player_response']['streamingData']['adaptiveFormats'];
		}

		if (in_array(substr($this->page, 9, 3), array('402', '429')) || preg_match('@Location: https?://(www\.)?youtube\.com/das_captcha@i', $this->page)) return $this->captcha();

		if (!empty($this->cookie['goojf'])) $this->saveCookie();

		$this->fmtmaps = array();
		$keys2Merge = array('cipher', 'signatureCipher');
		foreach (array('url_encoded_fmt_stream_map', 'adaptive_fmts', '_formats', '_adaptiveFormats') as $map) {
			if (empty($this->response[$map])) continue;
			if (!is_array($this->response[$map])) $this->response[$map] = explode(',', $this->response[$map]);
			foreach ($this->response[$map] as $fmt) {
				if (!is_array($fmt)) $fmt = array_map('urldecode', $this->FormToArr($fmt));
				foreach($keys2Merge as $key) if (!empty($fmt[$key])) {
					$fmt += array_map('urldecode', $this->FormToArr($fmt[$key]));
				}

				if (empty($fmt['itag']) || empty($fmt['url'])) continue;
				if (!empty($fmt['s']) && empty($this->encS)) {
					if ($this->sts < 1) return $this->getCipher();
					else html_error('[' . $this->sts . '] No decoded steps');
				}
				$fmt['url'] = parse_url($fmt['url']);
				$fmt['url']['query'] = array_map('urldecode', $this->FormToArr($fmt['url']['query']));
				if (!empty($fmt['s']) && !empty($fmt['sp']))
				{
					$fmt['url']['query']["{$fmt['sp']}"] = $this->sigDecode($fmt['s']);
				}
				else if (empty($fmt['s']) && !empty($fmt['sig']))
				{
					if (empty($fmt['url']['query']['signature'])) $fmt['url']['query']['signature'] = $fmt['sig'];
				}
				else if (empty($fmt['url']['query']['signature']) && empty($fmt['url']['query']['sig'])) html_error("Cannot get signature key name");

				if (empty($fmt['url']['query']['ratebypass'])) $fmt['url']['query']['ratebypass'] = 'yes'; // Fix for Slow Downloads of DASH Formats

				ksort($fmt['url']['query']);
				$fmt['url']['query'] = http_build_query($fmt['url']['query']);
				$fmt['url'] = rebuild_url($fmt['url']);

				if (!empty($fmt['mimeType']) && preg_match('@(audio|video)/(?:x-)?(\w+); codecs="(\w+)(?:\.\w+)*(?:, (\w+)(?:\.\w+)*)?"@', $fmt['mimeType'], $mimeType)) {
					$fmt['_codecs'] = array();
					$fmt['_codecs']['type'] = $mimeType[1];
					$fmt['_codecs']['container'] = $mimeType[2];
					if (!empty($mimeType[4])) {
						$fmt['_codecs']['type'] = 'dual';
						$fmt['_codecs']['video'] = $mimeType[3];
						$fmt['_codecs']['audio'] = $mimeType[4];
					} else if ($mimeType[1] == 'video') {
						$fmt['_codecs']['video'] = $mimeType[3];
					} else {
						$fmt['_codecs']['container'] = str_replace(array('mp4a', 'vorbis'), array('m4a', 'ogg'), $mimeType[3]);
						$fmt['_codecs']['audio'] = $mimeType[3];
					}
					if (!empty($fmt['_codecs']['video'])) $fmt['_codecs']['video'] = strtoupper(str_replace(array('av01', 'avc1'), array('AV1', 'H264'), $fmt['_codecs']['video']));
					if (!empty($fmt['_codecs']['audio'])) $fmt['_codecs']['audio'] = ucfirst(str_replace('mp4a', 'AAC', $fmt['_codecs']['audio']));
				}

				$this->fmtmaps[$fmt['itag']] = $fmt;
			}
		}

		if (empty($this->fmtmaps)) html_error('[' . $this->sts . '] Video links not found.');
	}

	private function decError($msg) {
		html_error("Error while decoding [{$this->sts}][{$this->js[2]}]: $msg");
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
				if (!preg_match("@(?<=^|,)\s*$fName:function\($v(?:,($v))?\)\{([^}]+)\}\s*(?=,|$)@", $this->jsVars[$obj]['src'], $src)) $this->decError("Cannot find function '$obj.$fName'");
				$src[0] = trim($src[0]);
				$this->jsVars[$obj]['fn'][$fName] = array('src' => $src);

				if (empty($src[1])) return $this->jsVars[$obj]['fn'][$fName]['step'] = 'r';
				else if (preg_match("@var\s+($v)=($v)\[0\];\\2\[0\]=\\2\[{$src[1]}%\\2(?:\.length|\[$v\])\];\\2\[{$src[1]}(?:%\\2(?:\.length|\[$v\]))?\]=\\1@", $src[2])) {
					$this->jsVars[$obj]['fn'][$fName]['step'] = 'w%d';
					return "w$num";
				} else if (preg_match("@(?:$v=)?$v(?:\.s(p)?lice|\[$v\])\((?(1)0,){$src[1]}\)@", $src[2])) {
					$this->jsVars[$obj]['fn'][$fName]['step'] = 's%d';
					return "s$num";
				} else if (preg_match("@(?:$v=)?$v(?:\.reverse|\[$v\])\(\)@", $src[2])) return $this->jsVars[$obj]['fn'][$fName]['step'] = 'r';
				else $this->decError("Error parsing function '$obj.$fName'");
			} else return sprintf($this->jsVars[$obj]['fn'][$fName]['step'], $num);
		}
		if (empty($this->jsVars[$fName]['step'])) {
			if (($spos = strpos($this->playerJs, "function $fName(")) === false || ($epos = strpos($this->playerJs, '};', $spos)) === false) $this->decError("Cannot find function '$fName'");
			$this->jsVars[$fName] = array('src' => substr($this->playerJs, $spos, $epos - $spos));
			$v = '[\$_A-Za-z][\$\w]*';
			if (!preg_match("@^function\s+$fName\($v(?:,($v))?\)\{(.*)$@", $this->jsVars[$fName]['src'], $pars)) $this->decError("Cannot parse function '$fName'");
			if (empty($pars[1])) return $this->jsVars[$fName]['step'] = 'r';
			else if (preg_match("@var\s+($v)=($v)\[0\];\\2\[0\]=\\2\[{$pars[1]}%\\2(?:\.length|\[$v\])\];\\2\[{$pars[1]}(?:%\\2(?:\.length|\[$v\]))?\]=\\1@", $src[2])) {
				$this->jsVars[$fName]['step'] = 'w%d';
				return "w$num";
			} else if (preg_match("@(?:$v=)?$v(?:\.s(p)?lice|\[$v\])\((?(1)0,){$src[1]}\)@", $src[2])) {
				$this->jsVars[$fName]['step'] = 's%d';
				return "s$num";
			} else if (preg_match("@(?:$v=)?$v(?:\.reverse|\[$v\])\(\)@", $src[2])) return $this->jsVars[$fName]['step'] = 'r';
			else $this->decError("Error parsing function '$fName'");
		} else return sprintf($this->jsVars[$fName]['step'], $num);
	}

	// getCipher & sigDecode are based on jwz's youtubedown code.
	private function getCipher() {
		$this->changeMesg('<br />Video with ciphered signature, trying to decode it.', 1);
		$page = $this->GetPage('https://www.youtube.com/embed/'.$this->vid, $this->cookie);
		$this->cookie = GetCookiesArr($page, $this->cookie);

		if (preg_match('@"sts"\s*:\s*(\d+)@i', $page, $this->sts) && intval($this->sts[1])) {
			$this->sts = intval($this->sts[1]);
		}

		$savefile = DOWNLOAD_DIR.'YT_lastjs.txt';
		if (!preg_match('@(?<=[\"\'])(?:(?:https?:?)?//((?:[\w\-]+\.)+[\w\-]+(?:\:\d+)?))?(/(?:[^\"\'/]+/)+?(?:html5)?player[-_][\w\-\.]+(?:(?:/\w+)?/[\w\-\.]+)?)\.js@i', str_replace('\\/', '/', $page), $this->js)) html_error('YT\'s player javascript not found.');
		if (@file_exists($savefile) && ($file = file_get_contents($savefile, NULL, NULL, -1, 822)) && ($saved = @unserialize($file)) && is_array($saved) && !empty($saved['js']) && !empty($saved['sts']) && !empty($saved['steps']) && ((!$this->sts && $saved['js'] == $this->js[2]) || $saved['sts'] == $this->sts) && preg_match('@^\s*([ws]\d+|r)( ([ws]\d+|r))*\s*$@', $saved['steps'])) {
			$this->changeMesg(sprintf('<br />Using cached decoder: [%d] %s.', $saved['sts'], $saved['steps']), 1);
			$this->encS = explode(' ', trim($saved['steps']));
			if (empty($this->sts)) $this->sts = $saved['sts'];
		} else {
			$this->changeMesg('<br />Loading video player data.', 1);
			$this->playerJs = $this->GetPage('https://' . (!empty($this->js[1]) ? $this->js[1] : 'www.youtube.com') . $this->js[2] . '.js', $this->cookie, 0, 'https://www.youtube.com/embed/'.$this->vid);
			$v = '[\$_A-Za-z][\$\w]*';
			$v3 = '[\$_A-Za-z][\$\w]{3,}';
			if (empty($this->sts)) {
				if (preg_match('@\b(?:sts|signatureTimestamp)\s*[:=]\s*(\d+)@i', $this->playerJs, $sts)) {
					$this->sts = intval($sts[1]);
				} else if (preg_match("@[:=]($v+(?:\.$v+)?\(\s*\"STS\"[^()?:]+?\))\s*\?\s*\1\s*:\s*(\d\d+)\b@i", $this->playerJs, $sts)) {
					$this->sts = intval($sts[2]);
				} else if (preg_match("@\b(?:sts|signatureTimestamp)\s*[:=]\s*($v)@", $this->playerJs, $sts, PREG_OFFSET_CAPTURE) && preg_match("@(?:var\s+|[,{};])\s*{$sts[1][0]}\s*[=:]\s*(\d\d+)@", $this->playerJs, $sts2, 0, strrpos($this->playerJs, "\n", $sts[0][1] - strlen($this->playerJs)))) {
					$this->sts = intval($sts2[1]);
				}
				if (empty($this->sts)) html_error('Signature TimeStamp not found.');
			}
			//if (($spos = strpos($this->playerJs, '.sig||')) === false) $this->decError('Not found (".sig||")');
			//if (($cut1 = cut_str(substr($this->playerJs, $spos), '{', '}')) == false) $this->decError('Cannot get inner content of "if(X.sig||X.s)"');
			if (!preg_match("@(?:\.sig\|\||\.set\(\"signature\",|\|\"signature\",|$v\.sp,|$v=)(?:\(0,$v(?:\.$v)*\)\(|$v3\()?($v)\((?:\(0,$v(?:\.$v)*\)\(|$v3\()?$v\.s\)@", $this->playerJs, $fn)) $this->decError('Cannot get decoder function name');
			$fn = preg_quote($fn[1], '@');
			if (!preg_match("@(?:function\s+$fn\s*\(|var\s+$fn\s*=\s*function\s*\(|(?<=(?:{|,|;))\s*$fn\s*=\s*function\s*\()@", $this->playerJs, $fpos, PREG_OFFSET_CAPTURE)) $this->decError('Cannot find decoder function');
			$fpos = $fpos[0][1];
			if (($cut2 = cut_str(substr($this->playerJs, $fpos), '{', '}')) == false) $this->decError('Cannot get decoder function contents');
			$this->sigJs = preg_replace("@var $v=$v\[0\];$v\[0\]=($v)\[(\d+)%$v(?:\.length|\[$v\])\];$v\[\d+\]=$v;@", '$1=T8($1,$2);', trim($cut2));
			$this->encS = array();
			foreach (array_map('trim', explode(';', '{'.$this->sigJs.'}')) as $step) {
				if (($step{0} == '{' || substr($step, strlen($step) - 1, 1) == '}') && (preg_match("@^\{(?:var\s+)?$v=$v(?:\.split|\[$v\])\(\"\"\)$@", $step) || preg_match("@^return\s+$v(?:\.join|\[$v\])\(\"\"\);?\}$@", $step))) continue;
				else if (preg_match("@^(?:$v=)?((?:$v.)*$v)\($v\,(\d+)\)$@", $step, $s)) $this->encS[] = $this->findFunction($s[1], $s[2]);
				else if (preg_match("@^(?:$v=)?$v(?:\.s(p)?lice|\[$v\])\((?(1)0,)(\d+)\)$@", $step, $s)) $this->encS[] = 's'.$s[2];
				else if (preg_match("@^(?:$v=)?$v(?:\.reverse|\[$v\])\(\)$@", $step)) $this->encS[] = 'r';
				else $this->decError($step.' | Unknown step on decoder function.');
			}

			if (empty($this->encS)) $this->decError('Empty decoded result');
			$this->changeMesg(sprintf('<br />Decoder: [%d] %s.', $this->sts, implode(' ', $this->encS)), 1);
			file_put_contents($savefile, serialize(array('js' => $this->js[2], 'sts' => $this->sts, 'steps' => implode(' ', $this->encS))));
		}

		// Request video fmts with the current sts
		$this->getFmtMaps();
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

	private function bitrate2KMG($bitrate) {
		if (!is_numeric($bitrate)) return 'Unknown';
		$s = array('', 'K', 'M', 'G');
		$e = min(floor(log($bitrate) / log(1000)), count($s) - 1);
		return sprintf("%.2f {$s[$e]}bps", ($bitrate / pow(1000, $e)));
	}

	private function QSelector() {
		$C = array('V' => array('MP4', 'WebM', 'FLV', '3GP'), 'A' => array('AAC', 'Vorbis', 'Opus', 'MP3'));

		$sizes = array();
		/* Add a // at the start of this line for enable this code.
		if (extension_loaded('curl') && function_exists('curl_init') && function_exists('curl_exec') && !trim(ini_get('open_basedir'))) {
			$sizes = array();
			$opt = array(CURLOPT_FOLLOWLOCATION => true, CURLOPT_MAXREDIRS => 5, CURLOPT_NOBODY => true);
			foreach ($this->fmtmaps as $itag => $fmt) {
				if (empty($this->fmts[$itag])) continue;
				$headers = explode("\r\n\r\n", cURL($fmt['url'], $this->cookie, 0, 0, 0, $opt));
				$headers = ((count($headers) > 2) ? $headers[count($headers) - 2] : $headers[0]) . "\r\n\r\n";
				if (substr($headers, 9, 3) == '200' && ($CL = cut_str($headers, "\nContent-Length: ", "\n")) && $CL > 1024) $sizes[$itag] = bytesToKbOrMbOrGb(trim($CL));
				if (substr($headers, 9, 3) == '403') {
					$this->changeMesg('<br />Cannot Ask Video Filesize (Signature Error?)');
					break;
				}
			}
			unset($headers, $CL);
		} //*/

		echo "\n<br /><br /><h3 style='text-align: center;'>".lang(216).".</h4>";
		echo "\n<center><form name='YT_QS' action='{$_SERVER['SCRIPT_NAME']}' method='POST'>\n";
		echo "<input type='hidden' name='yt_QS' value='on' />\n";
		echo '<label><input type="checkbox" name="cleanname" checked="checked" value="1" /><small>&nbsp;Remove non-supported characters from filename</small></label><br />';
		echo "<select name='yt_fmt' id='QS_fmt'>\n";
		foreach ($this->fmtmaps as $itag => $fmt) {
			if (!empty($this->fmts[$itag])) {
				// Classic Formats
				$size = (!empty($fmt['contentLength']) ? ' ('.bytesToKbOrMbOrGb($fmt['contentLength']).')' : (!empty($fmt['clen']) ? ' ('.bytesToKbOrMbOrGb($fmt['clen']).')' : (!empty($sizes[$itag]) ? ' ('.$sizes[$itag].')' : '')));
				if (($I = explode('|', $this->fmts[$itag]))) printf("<option value='%d'>[%1\$d] Video: %s %dp | Audio: %s ~%d Kbps%s</option>\n", $itag, $C['V'][$I[0]], $I[1], $C['A'][$I[2]], $I[3], $size);
			} else if (!empty($fmt['_codecs'])) {
				// DASH Streams
				$size = (!empty($fmt['contentLength']) ? ' ('.bytesToKbOrMbOrGb($fmt['contentLength']).')' : (!empty($fmt['clen']) ? ' ('.bytesToKbOrMbOrGb($fmt['clen']).')' : ''));
				switch ($fmt['_codecs']['type']) {
					case 'video':
						printf("<option value='%d'>[%1\$d] Video only: %s (%s) @ %s%s</option>\n", $itag, str_replace('WEBM', 'WebM', strtoupper($fmt['_codecs']['container'])), $fmt['_codecs']['video'], (!empty($fmt['qualityLabel']) ? $fmt['qualityLabel'] : $fmt['quality_label']), $size);
						break;
					case 'audio':
						printf("<option value='%d'>[%1\$d] Audio only: %s @ ~%s%s</option>\n", $itag, $fmt['_codecs']['audio'], $this->bitrate2KMG((!empty($fmt['averageBitrate']) ? $fmt['averageBitrate'] : $fmt['bitrate'])), $size);
						break;
				}
			}
		}
		echo "</select>\n";

		$data = $this->DefaultParamArr($this->link);
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
// [13-4-2015]  Fixed captcha detection. - Th3-822
// [05-2-2016]  Fixed captcha (Now uses reCaptcha2) & Added cookie storage for it. - Th3-822
// [08-6-2016]  Added support to download DASH formats & Revised video formats handling. - Th3-822
// [30-8-2016]  Fixed slow speed while downloading DASH streams. - Th3-822
// [30-4-2017]  Fixed signature decoding functions. - Th3-822
// [25-1-2018]  Fixed get_video_info. - Th3-822
// [03-2-2019]  Fixed signature decoding functions. - Th3-822
// [25-5-2019]  Fixed embed JS regex. - Th3-822
// [19-6-2019]  Fixed signature key name. - Th3-822
// [10-7-2019]  Fixed signature search and related functions. - Th3-822
// [27-8-2019]  Fixed video title code. - Th3-822
// [04-1-2020]  Fixed fmts handling & Fixed signature search. - Th3-822
// [02-2-2020]  Fixed signature search. - Th3-822
// [15-3-2020]  Fixed embed JS regex. - Th3-822
// [31-5-2020]  Fixed signature search & Rewrote quality selector to parse and show all available formats. - Th3-822
// [17-8-2020]  Fixed signature timestamp search. - Th3-822