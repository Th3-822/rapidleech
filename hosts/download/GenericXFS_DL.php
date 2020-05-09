<?php
/*
@h@	/hosts/download/GenericXFS_DL.php

@h@	 GenericXFS_DL (alpha)
@h@	Written by Th3-822.
*/
if (!defined('RAPIDLEECH')) {
	require_once('index.html');
	exit();
}

class GenericXFS_DL extends DownloadClass {
	protected $page, $cookie, $baseCookie = array('lang' => 'english'), $scheme, $wwwDomain, $domain, $port, $host, $purl, $httpsOnly = false, $sslLogin = false, $cname = 'xfss', $cookieSave = false, $cJar = array(), $form, $lpass, $fid, $enableDecoders = false, $embedDL = false, $unescaper = false, $customDecoder = false, $reverseForms = true, $cErrsFDL = array(), $DLregexp = '@https?://(?:[\w\-]+\.)+[\w\-]+(?:\:\d+)?/(?:files|dl?|cgi-bin/dl\.cgi|[\da-zA-Z]{30,})/(?:[^\?\'\"\t<>\r\n\\\]{15,}|v(?:id(?:eo)?)?\.(?:flv|mp4))@i';
	private $classVer = 24;
	public $pluginVer, $pA;

	public function Download($link) {
		html_error('[GenericXFS_DL] This plugin can\'t be called directly.');
	}

	protected function onLoad() {} // Placeholder

	protected function onLoad_Post() {} // Placeholder

	protected function Start($link, $cErrs = array(), $cErrReplace = true) {
		if ($this->pluginVer > $this->classVer) html_error('GenericXFS_DL class is outdated, please install last version from: https://pastebin.com/e5TZcfQ2 ');

		$this->cookie = $this->baseCookie = empty($this->cookie) ? $this->baseCookie : array_merge($this->cookie, $this->baseCookie);
		$link = explode('|', str_ireplace('%7C', '|', $link), 2);
		if (count($link) > 1) $this->lpass = rawurldecode($link[1]);
		if (!preg_match('@https?://(?:[\w\-]+\.)+[\w\-]+(?:\:\d+)?/(\w{12})(?=(?:[/\.]|(?:\.html?))?)@i', str_ireplace('/embed-', '/', $link[0]), $url)) html_error('Invalid link?.');
		$this->fid = $url[1];
		$url = parse_url($url[0]);
		$url['scheme'] =  ($this->httpsOnly ? 'https' : strtolower($url['scheme']));
		$url['host'] = strtolower($url['host']);

		if ($this->wwwDomain && strpos($url['host'], 'www.') !== 0) $url['host'] = 'www.' . $url['host'];
		elseif (!$this->wwwDomain && strpos($url['host'], 'www.') === 0) $url['host'] = substr($url['host'], 4);

		$this->scheme = $url['scheme'];
		$this->domain = $url['host'];
		$this->port = (!empty($url['port']) && $url['port'] > 0 && $url['port'] < 65536) ? $url['port'] : 0;
		$this->host = $this->domain . (!empty($this->port) ? ':'.$this->port : '');
		$this->purl = $this->scheme.'://'.$this->host.'/';
		$this->link = $GLOBALS['Referer'] = rebuild_url($url);
		unset($url, $link);

		$this->enableDecoders = $this->embedDL || $this->unescaper || $this->customDecoder;

		$this->onLoad();

		if (empty($_POST['step']) || empty($_POST['captcha_type'])) {
			$this->page = $this->GetPage($this->link, $this->cookie);
			if ($this->scheme != 'https') is_present($this->page, "\nLocation: https://", '[GenericXFS_DL] Please Set "$this->httpsOnly" to true or add https:// to your link.');
			if (!empty($cErrs) && is_array($cErrs)) {
				foreach ($cErrs as $cErr) {
					if (is_array($cErr)) is_present($this->page, $cErr[0], $cErr[1]);
					else is_present($this->page, $cErr);
				}
				if ($cErrReplace) {
					$this->onLoad_Post();
					return $this->Login();
				}
			}
			is_present($this->page, 'The file you were looking for could not be found');
			is_present($this->page, 'The file was removed by administrator');
			is_present($this->page, 'The file was deleted by its owner');
			is_present($this->page, 'The file was deleted by administration');
			is_present($this->page, 'No such file with this filename', 'Error: Invalid filename, check your link and try again.'); // With the regexp i removed the filename part of the link, this error shouldn't be showed
		}

		$this->onLoad_Post();
		return $this->Login();
	}

	protected function FindPost($formOp = 'download[1-3]') {
		//if (!preg_match_all('@<form(?:[\s\t][^\>]*)?\>(?:[^<]+(?!\<form)<[^<]+)*</form>@i', $this->page, $forms)) return false;
		if (!preg_match_all('@(?><form(?:\s[^\>]*)?\>)(?>.*?</form>)@is', $this->page, $forms)) return false;
		$forms = ($this->reverseForms ? array_reverse($forms[0]) : $forms[0]); // In some hosts the freedl form is before the "premium" form so $this->reverseForms must be on false on those hosts.
		$found = false;
		foreach ($forms as $form) {
			if (preg_match('@<input\s*[^>]*\sname="op"[^>]*\svalue="' . $formOp . '"@i', $form)) {
				$found = true;
				break;
			}
		}
		if (!$found) return false;

		// Remove html commented inputs.
		while ($comment = cut_str($form, '<!--', '-->')) $form = str_replace('<!--'.$comment.'-->', '', $form);

		$this->form = $form;
		unset($forms, $form, $ret);

		preg_match_all('@<input\s*[^>]*\stype="hidden"[^>]*\sname="(\w+)"[^>]*\svalue="([^"]*)"@i', $this->form, $inputs);
		$data = array_map('html_entity_decode', array_combine($inputs[1], $inputs[2]));

		if (($pos = stripos($this->form, '<textarea')) !== false && preg_match_all('@<textarea\s+(?:[^>]*\s)?name="(\w+)"[^>]*>([^<]*)</textarea>@i', substr($this->form, $pos), $inputs)) $data = array_merge($data, array_map('html_entity_decode', array_combine($inputs[1], $inputs[2])));

		if ((stripos($this->form, 'type="submit"') !== false || stripos($this->form, 'type="image"') !== false || stripos($this->form, 'type="button"') !== false || stripos($this->form, '</button>') !== false) && preg_match_all('@<(?:input\s*[^>]*\stype="(?:submit|image|button)"|button)[^>]*\sname="(\w+)"[^>]*\svalue="([^"]*)"@i', $this->form, $inputs)) {
			$data = array_merge($data, array_map('html_entity_decode', array_combine($inputs[1], $inputs[2])));
			if (!empty($data['method_free']) && !empty($data['method_premium'])) $data['method_premium'] = '';
		}

		$this->post = $data;
		return true;
	}

	// Custom page decoder placeholder
	protected function pageDecoder() {
		html_error('[GenericXFS_DL] $this->customDecoder is enabled but there is no pageDecoder() function.');
	}

	protected function runDecoders() {
		// Packed embedded video decoder
		if (!empty($this->embedDL) && preg_match_all('@eval\s*\(\s*function\s*\(p,a,c,k,e,d\)\s*\{.+\}\s*\(\s*\'([^\r|\n]*)\'\s*,\s*(\d+)\s*,\s*(\d+)\s*,\s*\'([^\']+)\'\.split\([\'|\"](.)[\'|\"]\)(?:\s*,\s*\d\s*,\s*\{\})?\)\)@', $this->page, $js)) {
			$cnt = count($js[0]);
			for ($i = 0; $i < $cnt; $i++) {
				$this->page = str_replace($js[0][$i], $this->XFSUnpacker($js[1][$i], $js[2][$i], $js[3][$i], $js[4][$i], $js[5][$i]), $this->page);
			}
		}
		// JS unescape decoder
		if (!empty($this->unescaper) && preg_match_all('@eval\s*\(unescape\s*\(\s*(\"|\')([%\da-fA-F]+)\1\s*\)\s*\)\s*;?@', $this->page, $js)) {
			$cnt = count($js[0]);
			for ($i = 0; $i < $cnt; $i++) {
				$this->page = str_replace($js[0][$i], urldecode($js[2][$i]), $this->page);
			}
		}
		// Custom decoder function
		if (!empty($this->customDecoder)) $this->pageDecoder();
	}

	// (Placeholder) Returns video title if available to use on stream video downloads.
	protected function getVideoTitle() {
		return false;
	}

	protected function getFileName($url) {
		$fname = basename(parse_url($url, PHP_URL_PATH));
		if (preg_match("@^(?:v(?:id(?:eo)?)?|{$this->fid})?\.(mp4|flv)$@i", $fname, $vExt)) { // Possible video/stream
			// Try to get original filename or title for renaming the file.
			if (!empty($this->post['fname'])) $newname = $this->post['fname'];
			else if (($title = $this->getVideoTitle())) $newname = $title;
			else $newname = false;

			// I always like to add a letter to mark it as a reconverted video stream and remove the original video .ext
			if (!empty($newname)) $fname = preg_replace('@(?:\.(?:mp4|flv|mkv|webm|wmv|(m2)?ts|rm(vb)?|mpe?g?|vob|avi|[23]gp))+$@i', '', basename($newname)) . '_S.' . strtolower($vExt[1]);
		}
		return $fname;
	}

	protected function testDL() {
		if (!empty($this->enableDecoders)) $this->runDecoders();

		if (preg_match($this->DLregexp, $this->page, $DL)) {
			$this->RedirectDownload($DL[0], basename($this->getFileName($DL[0])));
			return true;
		}
		return false;
	}

	protected function XFSUnpacker($p,$a,$c,$k,$ed) {
		$k = explode($ed, $k);
		while ($c--) if ($k[$c]) $p = preg_replace('@\b'.base_convert($c, 10, $a).'\b@', $k[$c], $p);
		return $p;
	}

	protected function findCaptcha() {
		if (!empty($this->captcha)) return false;
		if (($pos = stripos($this->page, $this->scheme . '://' . $this->host . '/captchas/')) !== false && preg_match('@(https?://(?:[\w\-]+\.)+[\w\-]+(?:\:\d+)?)?/captchas/[\w\-]+\.(?:jpe?g|png|gif)@i', substr($this->page, $pos), $gdCaptcha)) {
			// gd Captcha
			if (empty($gdCaptcha[1])) $gdCaptcha[0] = $this->scheme . '://' . $this->host . $gdCaptcha[0];
			$this->captcha = array('type' => 1, 'url' => $gdCaptcha[0]);
		} elseif (substr_count($this->form, "<span style='position:absolute;padding-left:") > 3 && preg_match_all("@<span style='[^\'>]*padding-left\s*:\s*(\d+)[^\'>]*'[^>]*>((?:&#\w+;)|(?:\d))</span>@i", $this->form, $txtCaptcha)) {
			// Text Captcha (decodeable)
			$txtCaptcha = array_combine($txtCaptcha[1], $txtCaptcha[2]);
			ksort($txtCaptcha, SORT_NUMERIC);
			$txtCaptcha = trim(html_entity_decode(implode($txtCaptcha), ENT_QUOTES, 'UTF-8'));
			$this->captcha = array('type' => 2, 'key' => $txtCaptcha);
		} elseif ((stripos($this->page, 'google.com/recaptcha/api/') !== false || stripos($this->page, 'recaptcha.net/') !== false) && preg_match('@(?:https?:)?//(?:[\w\-]+\.)?(?:google\.com/recaptcha/api|recaptcha\.net)/(?:challenge|noscript)\?k=([\w\.\-]+)@i', $this->page, $reCaptcha)) {
			// Old reCAPTCHA
			$this->captcha = array('type' => 3, 'key' => $reCaptcha[1]);
		} elseif (preg_match('@(?:https?:)?//api(?:-secure)?\.solvemedia\.com/papi/challenge\.(?:no)?script\?k=([\w\.\-]+)@i', $this->page, $smCaptcha)) {
			// SolveMedia Captcha
			$this->captcha = array('type' => 4, 'key' => $smCaptcha[1]);
		} elseif (preg_match('@(?:class|id)=["\']g-recaptcha["\']\s+data-sitekey=["\']([\w\.\-]+)["\']@i', $this->page, $reCaptcha2)) {
			// reCAPTCHA2
			$this->captcha = array('type' => 5, 'key' => $reCaptcha2[1]);
		}
		return (!empty($this->captcha));
	}

	protected function showCaptcha($step) {
		if (!empty($this->captcha)) {
			if (!empty($this->cookie[$this->cname])) {
				$data = $this->DefaultParamArr($this->link, $this->cookie, 1, 1);
				$data['cookie_encrypted'] = 1;
			} else {
				$data = $this->DefaultParamArr($this->link);
			}
			if (!empty($this->post)) foreach ($this->post as $k => $v) $data["T8gXFS[$k]"] = $v;
			$data['step'] = $step;
			$data['captcha_type'] = $this->captcha['type'];
			switch ($this->captcha['type']) {
				default: return html_error('Unknown captcha type.');
				case 1:
					list($headers, $imgBody) = explode("\r\n\r\n", $this->GetPage($this->captcha['url']), 2);
					if (substr($headers, 9, 3) != '200') html_error('[1] Error downloading CAPTCHA img.');
					$mimetype = (preg_match('@image/[\w+]+@', $headers, $mimetype) ? $mimetype[0] : 'image/jpg');
					return $this->EnterCaptcha("data:$mimetype;base64,".base64_encode($imgBody), $data);
				case 2:
					$this->post['code'] = $this->captcha['key'];
					// postCaptcha won't be needed on this case.
					return true;
				case 3: return $this->reCAPTCHA($this->captcha['key'], $data);
				case 4: return $this->SolveMedia($this->captcha['key'], $data);
				case 5: return $this->reCAPTCHAv2($this->captcha['key'], $data);
			}
		}
		return false;
	}

	protected function postCaptcha(&$step) {
		if (empty($_POST['step']) || empty($_POST['captcha_type'])) return false;
		$post = (!empty($_POST['T8gXFS']) && is_array($_POST['T8gXFS']) ? array_map('urlencode', $_POST['T8gXFS']) : array());
		switch ($_POST['captcha_type']) {
			default: 
				return html_error('Invalid captcha type.');
			case '1': // Image (gd) Captcha
				$this->captcha = array('type' => 1);
				if (empty($_POST['captcha'])) html_error('[1] You didn\'t enter the image verification code.');
				$post['code'] = urlencode($_POST['captcha']);
				break;
			case '3': // Old reCAPTCHA
				$this->captcha = array('type' => 3);
				if (empty($_POST['recaptcha_response_field'])) html_error('[3] You didn\'t enter the image verification code.');
				if (empty($_POST['recaptcha_challenge_field'])) html_error('[3] Empty reCAPTCHA challenge.');
				$post['recaptcha_challenge_field'] = urlencode($_POST['recaptcha_challenge_field']);
				$post['recaptcha_response_field'] = urlencode($_POST['recaptcha_response_field']);
				break;
			case '4': // Solvemedia
				$this->captcha = array('type' => 4);
				$post = array_merge($post, $this->verifySolveMedia());
				break;
			case '5': // reCAPTCHA2
				$this->captcha = array('type' => 5);
				$post = array_merge($post, $this->verifyReCaptchav2());
				break;
		}
		$step = intval($_POST['step']);
		$_POST['step'] = $_POST['captcha_type'] = false;
		$this->page = $this->GetPage($this->link, $this->cookie, $post);
		$this->cookie = GetCookiesArr($this->page, $this->cookie);
		return true;
	}

	// Finds FreeDL countdown on $this->page and calls $this->CountDown(X) for it.
	// return true if there is a countdown, false otherwise.
	protected function findCountdown() {
		if (preg_match('@<span[^>]*>(?>.*?<span\s+id=[\'"][\w\-]+[\'"][^>]*>)\s*(\d+)\s*</span>(?>.*?</span>)@sim', $this->page, $count) || preg_match('@<span[^>]*>(?>[\w\s]*?<span\s+class=[\'\"](?:[\w-]+\s+)*seconds(?:\s+\w+)*[\'\"][^>]*>)\s*(\d+)\s*</span>(?>[\w\s]*?</span>)@sim', $this->form, $count)) {
			if ($count[1] > 0) $this->CountDown($count[1] + 2);
			return true;
		}
		return false;
	}

	protected function checkCaptcha($step) {
		if (preg_match('@>\s*Wrong captcha\s*<@i', $this->page)) {
			if (empty($this->captcha)) html_error("Error: Unknown captcha. [$step]");
			else if ($this->captcha['type'] == 2) html_error("Error: Error Decoding Captcha. [$step]");
			else html_error("Error: Wrong Captcha Entered. [$step]");
		}
	}

	protected function FreeDL($step = 1) {
		if (!$this->postCaptcha($step) && $step == 1 && !empty($this->cookie[$this->cname])) {
			// Member DL: We need to reload the page with the user's cookies.
			$this->page = $this->GetPage($this->link, $this->cookie);
			$this->cookie = GetCookiesArr($this->page, $this->cookie);
		}
		if ((($pos = stripos($this->page, 'You have to wait')) !== false || ($pos = stripos($this->page, 'Please wait ')) !== false) && preg_match('@(?:You have to|Please) wait[\W\S]?(?:(?:\s*|\s*<br\s*/?\s*>\s*)?\d+ \w+,?\s){0,2}\d+ \w+(?:\s*|\s*<br\s*/?\s*>\s*)?(?:un)?till? (?:the )?next download@i', substr($this->page, $pos), $err)) html_error('Error: '.strip_tags($err[0]));
		if (($pos = stripos($this->page, 'You can download files up to ')) !== false && preg_match('@You can download files up to \d+ [KMG]b only.@i', substr($this->page, $pos), $err)) html_error('Error: '.$err[0]);
		if (($pos = stripos($this->page, 'You have reached the download')) !== false && preg_match('@You have reached the download[- ]limit(?: of|:) \d+ [KMGT]b for(?: the)? last \d+ days?@i', substr($this->page, $pos), $err)) html_error('Error: '.$err[0]);
		if ($this->testDL()) return true;
		if (!$this->FindPost()) {
			is_present($this->page, 'Downloads are disabled for your country:', 'Downloads are disabled for your server\'s country.');
			is_present($this->page, 'This server is in maintenance mode. Refresh this page in some minutes.', 'File is not available at this moment, try again later.');
			is_present($this->page, 'This file is available for Premium Users only.');
			is_present($this->page, 'This file reached max downloads limit', 'Error: This file reached max downloads limit.');
			is_present($this->page, 'Error happened when generating Download Link.', 'Error: Download server is not available at this moment, try again later.');
			if (!empty($this->cErrsFDL) && is_array($this->cErrsFDL)) {
				foreach ($this->cErrsFDL as $cErr) {
					if (is_array($cErr)) is_present($this->page, $cErr[0], $cErr[1]);
					else is_present($this->page, $cErr);
				}
			}
			return html_error('Non aceptable form found.');
		}
		if (preg_match('@>\s*Skipped countdown@i', $this->page)) html_error("Error: Skipped countdown? [$step].");
		$this->checkCaptcha($step);
		switch ($this->post['op']) {
			default: html_error('Unknown form op.');
			case 'download1':
				$fstep = 1;
				break;
			case 'download2':
				$fstep = 2;
				break;
		}
		if ($step > $fstep) html_error("Loop Detected [$fstep]");
		is_present($this->page, '>Expired session<', "Error: Expired Download Session. [$fstep]");
		$this->findCaptcha();
		$this->findCountdown();
		$this->showCaptcha($fstep);
		$this->page = $this->GetPage($this->link, $this->cookie, array_map('urlencode', $this->post));
		$this->cookie = GetCookiesArr($this->page, $this->cookie);
		return $this->FreeDL($fstep + 1);
	}

	protected function PremiumDL() {
		$this->page = $this->GetPage($this->link, $this->cookie);
		if (($pos = stripos($this->page, 'You have reached the download')) !== false && preg_match('@You have reached the download[- ]limit(?: of|:) \d+ [TGMK]b for(?: the)? last \d+ days?@i', substr($this->page, $pos), $err)) html_error('Error: '.$err[0]);

		if (!$this->testDL()) {
			if (!$this->FindPost()) {
				is_present($this->page, 'Downloads are disabled for your country:', 'Downloads are disabled for your server\'s country.');
				is_present($this->page, 'This server is in maintenance mode. Refresh this page in some minutes.', 'File is not available at this moment, try again later.');
				is_present($this->page, 'Error happened when generating Download Link.', 'Error: Download server is not available at this moment, try again later.');
				html_error('[PremiumDL] Non aceptable form found.');
			}
			if (!isset($this->post['method_premium']) || $this->post['method_premium'] === '') $this->post['method_premium'] = 1;
			sleep(1); // This should avoid errors at massive usage.
			$this->page = $this->GetPage($this->link, $this->cookie, array_map('urlencode', $this->post));
			if (($pos = stripos($this->page, 'You have reached the download')) !== false && preg_match('@You have reached the download[- ]limit(?: of|:) \d+ [TGMK]b for(?: the)? last \d+ days?@i', substr($this->page, $pos), $err)) html_error('Error: '.$err[0]);
			if (!$this->testDL()) html_error('Error: Download-link not found.');
		} else return true;
	}

	// Allow Custom Login Post.
	protected function sendLogin($post) {
		$page = $this->GetPage((!empty($this->sslLogin) ? 'https://'.$this->host.'/' : $this->purl) . '?op=login', $this->cookie, $post, $this->purl);
		return $page;
	}

	protected function Login() {
		$this->pA = (empty($_REQUEST['premium_user']) || empty($_REQUEST['premium_pass']) ? false : true);
		$pkey = str_ireplace(array('www.', '.'), array('', '_'), $this->domain);
		if (empty($_POST['cookie']) && ($_REQUEST['premium_acc'] != 'on' || (!$this->pA && (empty($GLOBALS['premium_acc'][$pkey]['user']) || empty($GLOBALS['premium_acc'][$pkey]['pass'])) && empty($GLOBALS['premium_acc'][$pkey]['cookie'])))) return $this->FreeDL();

		if (!empty($_POST['cookie']) || !empty($GLOBALS['premium_acc'][$pkey]['cookie'])) {
			if (!empty($_POST['cookie'])) {
				if (!empty($_POST['cookie_encrypted'])) {
					$_POST['cookie'] = decrypt(urldecode($_POST['cookie']));
					unset($_POST['cookie_encrypted']);
				}
				$this->cookie = StrToCookies($_POST['cookie']);
				$_POST['cookie'] = false;
			} else {
				$cookie = $GLOBALS['premium_acc'][$pkey]['cookie'];
				$this->cookie = (is_array($cookie) ? $cookie : StrToCookies($cookie));
			}
		} else {
			$user = ($this->pA ? $_REQUEST['premium_user'] : $GLOBALS['premium_acc'][$pkey]['user']);
			$pass = ($this->pA ? $_REQUEST['premium_pass'] : $GLOBALS['premium_acc'][$pkey]['pass']);
			if ($this->pA && !empty($_POST['pA_encrypted'])) {
				$user = decrypt(urldecode($user));
				$pass = decrypt(urldecode($pass));
				unset($_POST['pA_encrypted']);
			}

			if (empty($user) || empty($pass)) html_error('Login Failed: User or Password is empty. Please check login data.');
			
			if ($this->cookieSave && ($page = $this->cJar_load($user, $pass))) {
				if ($page === true) $page = $this->GetPage($this->purl.'?op=my_account', $this->cookie, 0, $this->purl);
				return $this->checkAccount($page);
			} else {
				$post = array();
				$post['op'] = 'login';
				$post['redirect'] = '';
				$post['login'] = urlencode($user);
				$post['password'] = urlencode($pass);

				$page = $this->sendLogin($post);
				if (empty($this->sslLogin) && $this->scheme != 'https') is_present($page, "\nLocation: https://", '[GenericXFS_DL] Please Set "$this->sslLogin" to true.');

				if (!$this->checkLogin($page)) html_error('Login Error: checkLogin() returned false.');
				$this->cookie = GetCookiesArr($page);
			}
		}

		if (empty($this->cookie[$this->cname])) html_error('Login Error: Cannot find session cookie.');
		$this->cookie = array_merge($this->cookie, $this->baseCookie);

		$page = $this->isLoggedIn();
		if (!$page) html_error('Login Error: isLoggedIn() returned false.');

		if ($this->cookieSave && !empty($this->cJar)) $this->cJar_save();

		if ($page === true) $page = $this->GetPage($this->purl.'?op=my_account', $this->cookie, 0, $this->purl);
		return $this->checkAccount($page);
	}

	private function cJar_encrypt($data, $key) {
		if (empty($data)) return false;
		global $secretkey;
		$_secretkey = $secretkey;
		$secretkey = $key;
		$data = base64_encode(encrypt(json_encode($data)));
		$secretkey = $_secretkey;
		return $data;
	}

	private function cJar_decrypt($data, $key) {
		if (empty($data)) return false;
		global $secretkey;
		$_secretkey = $secretkey;
		$secretkey = $key;
		$data = json_decode(decrypt(base64_decode($data)), true);
		$secretkey = $_secretkey;
		return (!empty($data) ? $data : false);
	}

	private function cJar_load($user, $pass) {
		if (empty($user) || empty($pass)) return html_error('Login Failed: User or Password is empty.');

		$user = strtolower($user);
		$this->cJar['file'] = DOWNLOAD_DIR . get_class($this) . '_dl.php';
		$this->cJar['hash'] = base64_encode(sha1("$user$pass", true));
		$this->cJar['key'] = substr(base64_encode(hash('sha512', "$user$pass", true)), 0, 56);

		if (file_exists($this->cJar['file']) && ($cFile = file($this->cJar['file'])) && is_array($cFile = unserialize($cFile[1])) && array_key_exists($this->cJar['hash'], $cFile) && ($testCookie = $this->cJar_decrypt($cFile[$this->cJar['hash']]['cookie'], $this->cJar['key']))) {
			return $this->cJar_test($testCookie);
		} else return false;
	}

	private function cJar_test($cookie) {
		if (empty($this->cJar) || empty($cookie[$this->cname])) return false;
		$oldCookie = $this->cookie;
		$this->cookie = array_merge($cookie, $this->baseCookie);

		if (!($page = $this->isLoggedIn())) {
			$this->cookie = $oldCookie;
			return false;
		}
		$this->cJar_save(); // Update last used time.
		return $page;
	}

	private function cJar_save() {
		if (empty($this->cJar)) return;
		$maxTime = 31 * 86400; // Max time to keep unused cookies saved (31 days)
		if (file_exists($this->cJar['file']) && ($savedcookies = file($this->cJar['file'])) && is_array($savedcookies = unserialize($savedcookies[1]))) {
			// Remove old cookies
			foreach ($savedcookies as $k => $v) if (time() - $v['time'] >= $maxTime) unset($savedcookies[$k]);
		} else $savedcookies = array();
		$savedcookies[$this->cJar['hash']] = array('time' => time(), 'cookie' => $this->cJar_encrypt($this->cookie, $this->cJar['key']));

		file_put_contents($this->cJar['file'], "<?php exit(); ?>\r\n" . serialize($savedcookies), LOCK_EX);
	}

	// Checks For Login Errors on $page and Calls html_error() For Them.
	// return true if there are no login errors, false otherwise.
	protected function checkLogin($page) {
		is_present($page, 'op=resend_activation', 'Login failed: Your account isn\'t confirmed yet.');
		is_present($page, 'Your account was banned by administrator.', 'Login failed: Account is Banned.');
		is_present($page, 'Your IP is banned', 'Login Error: IP banned temporally for too many wrong logins.');
		if (preg_match('@Incorrect (Username|Login) or Password@i', $page)) html_error('Login failed: User/Password incorrect.');
		return true;
	}

	// Checks if account is logged in.
	// return $page - If it's logged in and the $page loaded is usable for checkAccount() too, if not, return true or false
	protected function isLoggedIn() {
		$page = $this->GetPage($this->purl.'?op=my_account', $this->cookie, 0, $this->purl);
		if (stripos($page, '/?op=logout') === false && stripos($page, '/logout') === false) return false;
		return $page;
	}

	// A simpler function for check if account is premium in $page contents, easier to override on plugins for specific hosts.
	// return true if user is premium, false otherwise.
	protected function isAccPremium($page) {
		if (stripos($page, 'Premium account expire') !== false || stripos($page, 'Premium-account expire') !== false || stripos($page, 'Premium Expires') !== false || stripos($page, 'Expiration date') !== false) return true;
		return false;
	}

	protected function checkAccount($page) {
		is_present($page, 'Your account was banned by administrator.', '[checkAccount] Account is Banned.');
		if ($this->isAccPremium($page)) return $this->PremiumDL();

		// FreeDL() shouldn't have issues using it with a premium account... But PremiumDL() uses less checks.
		$this->changeMesg('<br /><b>Account isn\'t premium?</b>', true);
		return $this->FreeDL();
	}

}

// GenericXFS_DL (alpha)
// Written by Th3-822.