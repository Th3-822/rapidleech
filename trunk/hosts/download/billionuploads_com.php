<?php

if (!defined('RAPIDLEECH')) {
	require_once ('index.html');
	exit();
}

class billionuploads_com extends DownloadClass {
	private $page, $cookie, $form;
	public function Download($link) {
		$this->cookie = array('lang' => 'english');

		if (empty($_POST['step']) || $_POST['step'] != '1') {
			$this->loadCookie();
			$this->page = $this->_GetPage($link, $this->cookie);
			is_present($this->page, '<b>File Not Found</b>', 'File Not Found');
			is_present($this->page, 'The file you were looking for could not be found');
			is_present($this->page, '>File was removed by user<', 'The file you were looking for could not be found');
			is_present($this->page, 'No such file with this filename', 'Error: Invalid filename, check your link and try again.');
			is_present($this->page, '/public/ga/jsTest.html', '"CDN" challenge page found... Try again later.');
			is_present($this->page, 'Request unsuccessful. Incapsula incident ID', '"CDN" block page found... Try again later.');
			is_present($this->page, '<img src="/images/under.gif"', '"Site under maintenance", try again later.');
		}

		if (empty($_POST['step']) || $_POST['step'] != '1') {
			if (preg_match('@You have to wait (?:\d+ \w+,\s)?\d+ \w+ till next download@', $this->page, $err)) html_error('Error: '.$err[0]);
			is_present($this->page, 'type="password" name="password"', 'File is password protected.');

			$post = $this->FindPost();

			if (preg_match('@document\.getElementById\([\'"]\w+[\'"]\)\.innerHTML\s*=\s*decodeURIComponent\s*\(\s*[\'"]([%\w]+)[\'"]\s*\)@i', $this->form, $pr)) {
				$pr = rawurldecode($pr[1]);
				if (!preg_match_all('@<input\s*[^>]*\stype="hidden"[^>]*\sname="(\w+)"[^>]*\svalue="([^"<>]*)"@i', $pr, $pr2)) html_error('Cannot get antibot input.');
				$post = array_merge($post, array_map('html_entity_decode', array_combine($pr2[1], $pr2[2])));
			}

			if (preg_match('@\$\(document\.createElement\(\'input\'\)\)\.attr\(\'type\',\'hidden\'\)\.attr\(\'name\',\'(\w+)\'\)\.val\((?:["\']([^"\']+)["\']|\$\(\'(\w+)\[(\w+)="(\w+)"]\'\)\.val\(\))\)@i', $this->page, $pr)) {
				if (empty($pr[2])) {
					if (!(($pr[3] == 'textarea' && preg_match("@<textarea\s+(?:[^>]*\s)?{$pr[4]}=[\"']{$pr[5]}[\"'][^>]*>([^<]*)</textarea>@i", $this->page, $prv)) || ($pr[3] != 'textarea' && preg_match("@<input\s+(?:[^>]*\s)?{$pr[4]}=[\"']{$pr[5]}[\"'][^>]*\svalue=[\"']([^\"'<>]*)[\"']@i")))) html_error('Cannot get antibot input [2].');
					$post[$pr[1]] = $prv[1];
				} else $post[$pr[1]] = $pr[2];
			}
			if (preg_match_all('@\(\'(?:input|textarea|submit|image)\[name=["\'](\w+)["\']\]\'\)\.remove\(\)@i', $this->page, $pr)) foreach ($pr[1] as $rm) unset($post[$rm]);

			if (preg_match('@<span id="countdown_str">[^<>]+<span[^>]*>(\d+)</span>[^<>]+</span>@i', $this->form, $count) && $count[1] > 0) $this->CountDown($count[1]);

			if (preg_match('@https?://api\.solvemedia\.com/papi/challenge\.noscript\?k=([\w\.-]+)@i', $this->form, $skey)) {
				$data = $this->DefaultParamArr($link, (empty($this->cookie['xfss'])) ? 0 : encrypt(CookiesToStr($this->cookie)));
				foreach ($post as $k => $v) $data["T8[$k]"] = $v;
				$data['step'] = '1';

				$this->Solvemedia($skey[1], 'billionuploads', $data);
				exit;
			} else {
				$page = $this->_GetPage($link, $this->cookie, $post);
				is_present($page, '/public/ga/jsTest.html', '"CDN" block page found... Try again later.');
				is_present($page, '>Skipped countdown', 'Error: Skipped countdown?.');
				is_present($page, '>Wrong captcha<', 'Error: File needs captcha, captcha was not found.');
				is_present($page, '>Expired session<', 'Error: Expired Download Session.');
				is_present($page, '<img src="/images/under.gif"', '"Site under maintenance", try again later.');
				if (preg_match('@You can download files up to \d+ [KMG]b only.@i', $page, $err)) html_error('Error: '.$err[0]);
				if (!preg_match('@https?://[^/\r\n]+/(?:(?:files)|(?:dl?))/[^\'\"\t<>\r\n]+@i', $this->Artillery($page), $dlink)) html_error('Error: Download Link not found');

				$FileName = urldecode(basename(parse_url($dlink[0], PHP_URL_PATH)));
				$this->RedirectDownload($dlink[0], $FileName);
			}
		} else {
			if (!empty($_POST['cookie'])) $this->cookie = StrToCookies(decrypt(urldecode($_POST['cookie'])));

			$post = $this->Solvemedia_post();
			foreach ($_POST['T8'] as $k => $v) $post[$k] = $v;

			$page = $this->_GetPage($link, $this->cookie, $post);
			is_present($page, '/public/ga/jsTest.html', '"CDN" block page found... Try again later.');
			is_present($page, '>Skipped countdown', 'Error: Skipped countdown?.');
			is_present($page, '>Wrong captcha<', 'Error: Wrong Captcha Entered.');
			is_present($page, '>Expired session<', 'Error: Expired Download Session.');
			is_present($page, '<img src="/images/under.gif"', '"Site under maintenance", try again later.');
			if (preg_match('@You can download files up to \d+ [KMG]b only.@i', $page, $err)) html_error('Error: '.$err[0]);
			if (!preg_match('@https?://[^/\r\n]+/(?:(?:files)|(?:dl?))/[^\'\"\t<>\r\n]+@i', $this->Artillery($page), $dlink)) html_error('Error: Download Link not found.');

			$FileName = urldecode(basename(parse_url($dlink[0], PHP_URL_PATH)));
			$this->RedirectDownload($dlink[0], $FileName);
		}
	}

	// http://www.youtube.com/watch?v=5PWW8d4sw90
	private function Artillery($page) {
		// Find crappy obfuscated dllink. - With sony guts
		$v = '[\$_A-Za-z][\$\w]*';
		if (!preg_match("@($v)\.html\(\)\.split\([\'\"]([^\'\"]+)[\'\"]\)\[(\d+)\]@i", $page, $artillery) || !preg_match('@var\s+'.preg_quote($artillery[1]).'\s*=\s*\$\([\'\"](\w+)\s*\[(\w+)=[\'\"]([^\'\"]+)[\'\"]\][\'\"]\)\s*;@i', $page, $rpg) || !preg_match("@<{$rpg[1]}\s*[^\>]*{$rpg[2]}\s*=\s*[\'\"]{$rpg[3]}[\'\"][^\>]*>\s*([\w\+/=]+)\s*</{$rpg[1]}>@i", $page, $friedGrenades)) return false;
		$friedGrenades = explode($artillery[2], $friedGrenades[1]);
		if (empty($friedGrenades) || count($friedGrenades) <= $artillery[3]) return false;
		return base64_decode(base64_decode($friedGrenades[(int)$artillery[3]]));
	}

	private function Solvemedia($skey, $data, $name) {
		if (!is_array($data)) html_error('Post needs to be sended in a array.');
		if (empty($skey) || preg_match('@[^\w\-]@', $skey)) html_error('Invalid value for $skey');
		$page = $this->_GetPage("http://api.solvemedia.com/papi/challenge.noscript?k=$skey", 0, 0, $link);
		if (!preg_match('@<img [^/<>]*src\s?=\s?\"((https?://[^/\"<>]+)?/papi/media[^\"<>]+)\"@i', $page, $imgurl)) html_error('CAPTCHA img not found.');
		$imgurl = (empty($imgurl[2])) ? 'http://api.solvemedia.com'.$imgurl[1] : $imgurl[1];

		if (!preg_match_all('@<input [^/|<|>]*type\s?=\s?\"?hidden\"?[^/<>]*\s?name\s?=\s?\"(\w+)\"[^/<>]*\s?value\s?=\s?\"([^\"<>]+)\"[^/<>]*/?\s*>@i', $page, $forms)) html_error('CAPTCHA data not found.');
		$forms = array_combine($forms[1], $forms[2]);
		foreach ($forms as $n => $v) $data["T8_sm[$n]"] = urlencode($v);

		//Download captcha img.
		$page = $this->_GetPage($imgurl);
		$capt_img = substr($page, strpos($page, "\r\n\r\n") + 4);
		$imgfile = DOWNLOAD_DIR . basename($name) . '_captcha.gif';

		if (file_exists($imgfile)) unlink($imgfile);
		if (!write_file($imgfile, $capt_img)) html_error('Error getting CAPTCHA image.');

		$this->EnterCaptcha($imgfile.'?'.time(), $data, 20);
		exit;
	}

	private function Solvemedia_post() {
		if (empty($_POST['captcha'])) html_error('You didn\'t enter the image verification code.');
		$post = array();
		foreach ($_POST['T8_sm'] as $n => $v) $post[urlencode($n)] = $v;
		$post['adcopy_response'] = $_POST['captcha'];

		$url = 'http://api.solvemedia.com/papi/verify.noscript';
		$page = $this->_GetPage($url, 0, $post, $link);

		if (!preg_match('@(https?://[^/\'\"<>\r\n]+)?/papi/verify\.pass\.noscript\?[^/\'\"<>\r\n]+@i', $page, $resp)) {
			is_present($page, '/papi/challenge.noscript', 'Wrong CAPTCHA entered.');
			html_error('Error sending CAPTCHA.');
		}
		$resp = (empty($resp[1])) ? 'http://api.solvemedia.com'.$resp[0] : $resp[0];

		$page = $this->_GetPage($resp, 0, 0, $url);
		if (!preg_match('@>[\s\t\r\n]*([^<>\r\n]+)[\s\t\r\n]*</textarea>@i', $page, $gibberish)) html_error('CAPTCHA response not found.');

		return array('adcopy_challenge' => urlencode($gibberish[1]), 'adcopy_response' => 'manual_challenge');
	}

	// Ra Ta Ta Ta
	// From the unfinished GenericXFS_DL class
	private function FindPost() {
		if (!preg_match_all('@<form(?:[\s\t][^\>]*)?\>(?:[^<]+(?!\<form)<[^<]+)*</form>@i', $this->page, $forms)) html_error('No forms found at page.');
		$forms = array_reverse($forms[0]); // It should be the last form, so lets reverse the array order for find it faster.
		$found = false;
		foreach($forms as $form) {
			if (preg_match('@<input\s*[^>]*\sname="op"[^>]*\svalue="download[123]"@i', $form)) {
				$found = true;
				break;
			}
		}
		if (!$found) html_error('Non aceptable form found.');
		$this->form = $form;
		unset($forms, $form, $ret);

		if (!preg_match_all('@<input\s*[^>]*\stype="hidden"[^>]*\sname="(\w+)"[^>]*\svalue="([^"<>]*)"@i', $this->form, $inputs)) html_error('No inputs found at form.');
		$data = array_map('html_entity_decode', array_combine($inputs[1], $inputs[2]));

		if (preg_match_all('@<textarea\s+(?:[^>]*\s)?name="(\w+)"[^>]*>([^<]*)</textarea>@i', $this->form, $inputs)) $data = array_merge($data, array_map('html_entity_decode', array_combine($inputs[1], $inputs[2])));

		if (preg_match('@<input\s*[^>]*\stype="(?:submit|image)"[^>]*\sname="(\w+)"[^>]*\svalue="([^"<>]*)"@i', $this->form, $inputs)) $data[$inputs[1]] = html_entity_decode($inputs[2]);

		return $data;
	}

	// Intercept GetPage calls for find and try to skip the antibot page.
	public function _GetPage() {
		if (func_num_args() < 1) return false;
		$args = func_get_args();
		$page = call_user_func_array(array($this, 'GetPage'), $args);
		if (!empty($args[2]) && substr($page, 9, 2) == '30' && stripos($page, "\nLocation: {$args[0]}") !== false) {
			$this->cookie = GetCookiesArr($page, $this->cookie);
			$page = $this->GetPage($args[0], $this->cookie);
		}
		if (stripos($page, '/public/ga/jsTest.html') !== false) {
			$dom = 'http://billionuploads.com';
			if (!preg_match('@var\s+[\$_A-Za-z][\$\w]*\s*=\s*["\']([a-fA-F0-9]+)["\']@i', $page, $js)) html_error('Cannot skip antibot page (1), try again later.');
			$js = pack('H*', $js[1]);
			if (!preg_match('@,\s*["\'](/_Incapsula_Resource\?\w+=[\d\,]+)["\']@i', $js, $jsU)) html_error('Cannot skip antibot page (2), try again later.');
			$jsU = $dom.$jsU[1];
			$page = $this->GetPage($jsU, $args[1]);
			if (substr($page, 9, 3) !== '200') html_error('Cannot skip antibot page (3), try again later.');
			$args[1] = $this->cookie = GetCookiesArr($page); // I don't wanna store too many old cookies.
			$page = call_user_func_array(array($this, 'GetPage'), $args);
			$this->cookie = GetCookiesArr($page, $this->cookie);
			$this->saveCookie();
		} elseif (stripos($this->page, "\nSet-Cookie: ") !== false) {
			$this->cookie = GetCookiesArr($page, $this->cookie);
			unset($this->cookie['aff']);
			$this->saveCookie();
		}
		return $page;
	}

	private function loadCookie() {
		$filename = DOWNLOAD_DIR . 'billionuploads_com_cookie.php';
		if (!file_exists($filename)) return false;
		$file = file($filename);
		$cookies = decrypt(base64_decode($file[1]));
		unset($file);
		if (!empty($cookies)) {
			$this->cookie = StrToCookies($cookies);
			unset($this->cookie['aff']);
			return true;
		}
		return false;
	}

	private function saveCookie() {
		if (empty($this->cookie)) return false;
		$filename = DOWNLOAD_DIR . 'billionuploads_com_cookie.php';
		$cookies = (is_array($this->cookie) ? CookiesToStr($this->cookie) : $this->cookie);
		file_put_contents($filename, "<?php exit(); ?>\r\n" . base64_encode(encrypt($cookies)), LOCK_EX);
		return true;
	}
}

// [28-8-2012]  Written by Th3-822. (XFS, XFS everywhere. D:)
// [09-2-2013]  Added captcha support & Fixed regexp for files with whitespaces at name. - Th3-822
// [18-9-2013]  Replaced recaptcha for solvemedia captcha (non-tested) & Using code for deobfuscate the correct dllink. - Th3-822
// [23-12-2013]  Fixed download post, and added a function for bypass the (new) cdn block page. - Th3-822

?>