<?php

if (!defined('RAPIDLEECH')) {
	require_once('index.html');
	exit;
}

class mediafire_com extends DownloadClass {
	private $page = '', $cookie = array(), $fid, $link;
	public function Download($link) {
		if (!empty($_POST['mfpassword'])) {
			$this->cookie = StrToCookies(urldecode($_POST['cookie']));
			$this->page = $this->GetPage($link, $this->cookie, array('downloadp' => urlencode($_POST['mfpassword'])), $link);
			$this->cookie = GetCookiesArr($this->page, $this->cookie);
		}

		if (!preg_match('@https?://(?:[\w\-]+\.)*mediafire\.com/(?:(download/)|(download\.php|file/|view/)?\??)([\w\-\.]+)(?(1)(/[^/\s]+))?@i', $link, $this->fid)) html_error('Invalid Link?');

		$this->link = $GLOBALS['Referer'] = 'http://www.mediafire.com/download/' . $this->fid[3] . (!empty($this->fid[4]) ? $this->fid[4] : '');

		if (empty($_POST['step']) || $_POST['step'] != '1') {
			$this->page = $this->GetPage($this->link, $this->cookie, 0, 'http://www.mediafire.com/?' . $this->fid[3]);
			$this->cookie = GetCookiesArr($this->page, $this->cookie);
			if (preg_match('@\nLocation: .*(/download/([^/\r\n]+)/?[^\r\n]*)@i', $this->page, $redir)) {
				$this->link = $GLOBALS['Referer'] = 'http://www.mediafire.com'.$redir[1];
				$this->page = $this->GetPage($this->link, $this->cookie);
				$this->cookie = GetCookiesArr($this->page, $this->cookie);
			}
			if (preg_match('@/error\.php\?errno=\d+@i', $this->page, $redir)) {
				$this->page = $this->GetPage('http://www.mediafire.com'.$redir[0]);
				if (preg_match('@error_msg_title">\s*([^\r\n<>]+)\s*<@i', $this->page, $err)) html_error($err[1]);
				html_error('Link is not available');
			}
		}

		$this->MF_Captcha();
		if (strpos($this->page, 'name="downloadp" id="downloadp"')) {
			$DefaultParam = $this->DefaultParamArr(preg_replace('@/download/([^/]+)/?.*@i', '/?$1', $link), $this->cookie);
			$html = '<form action="index.php" method="POST">';
			foreach ($DefaultParam as $key => $value) $html.='<input type="hidden" name="' . $key . '" value="' . $value . '"/>';
			$html.='Enter your password here </br><input type="text" name="mfpassword" value="" placeholder="Enter your password here" autofocus="autofocus" required="required" /><input type="submit" name="action" value="Submit"/></form>';
			echo $html;
			exit;
		}
		if (preg_match('@Location: (http:\/\/[^\r\n]+)@i', $this->page, $dl) || preg_match('@\w+\s*=\s*\"(https?://[^\"]+)\"\s*;@i', $this->page, $dl)) {
			$dlink = trim($dl[1]);
			$this->RedirectDownload($dlink, 'Mediafire.com');
		} else html_error("Error: Download link [FREE] not found!");
	}

	private function MF_Captcha() {
		if (!empty($this->page) && stripos($this->page, ">Authorize Download</a>") === false) return;
		if (!empty($_POST['step']) && $_POST['step'] == '1') {
			$_POST['step'] = false;
			if (empty($_POST['recaptcha_response_field']) && empty($_POST['captcha'])) html_error('You didn\'t enter the image verification code.');
			if (empty($_POST['captcha'])) $post = array('recaptcha_challenge_field' => urlencode($_POST['recaptcha_challenge_field']), 'recaptcha_response_field' => urlencode($_POST['recaptcha_response_field']));
			else {
				$post = array();
				foreach ($_POST['T8'] as $n => $v) $post[urlencode($n)] = $v;
				$post['adcopy_response'] = $_POST['captcha'];

				$url = 'http://api.solvemedia.com/papi/verify.noscript';
				$this->page = $this->GetPage($url, 0, $post, $this->link);

				if (!preg_match('@(https?://[^/\'\"<>\r\n]+)?/papi/verify\.pass\.noscript\?[^/\'\"<>\r\n]+@i', $this->page, $resp)) {
					is_present($this->page, '/papi/challenge.noscript', 'Wrong CAPTCHA entered.');
					html_error('Error sending CAPTCHA.');
				}
				$resp = (empty($resp[1])) ? 'http://api.solvemedia.com'.$resp[0] : $resp[0];

				$this->page = $this->GetPage($resp, 0, 0, $url);
				if (!preg_match('@>[\s\t\r\n]*([^<>\r\n]+)[\s\t\r\n]*</textarea>@i', $this->page, $gibberish)) html_error('CAPTCHA response not found.');

				$post = array('adcopy_challenge' => urlencode($gibberish[1]), 'adcopy_response' => 'manual_challenge');
			}
			$this->cookie = StrToCookies(urldecode($_POST['cookie']));

			$purl = 'http://www.mediafire.com/?' . $this->fid[3];

			$this->page = $this->GetPage($purl, $this->cookie, $post);
			$this->cookie = GetCookiesArr($this->page, $this->cookie);
			is_present($this->page, 'Your entry was incorrect, please try again!');

			$this->page = $this->GetPage($this->link, $this->cookie, 0, $purl);
			$this->cookie = GetCookiesArr($this->page, $this->cookie);
			is_present($this->page, 'Your entry was incorrect, please try again!.');

			$this->MF_Captcha();
		} else {
			if (!preg_match('@https?://(?:[^/]+\.)?(?:(?:google\.com/recaptcha/api)|(?:recaptcha\.net))/(?:(?:challenge)|(?:noscript))\?k=([\w\.\-]+)@i', $this->page, $pid) && !preg_match('@http://api\.solvemedia\.com/papi/challenge\.noscript\?k=[\w\.\-]+@i', $this->page, $spid)) html_error('Error: CAPTCHA not found.');
			$data = $this->DefaultParamArr($this->link, $this->cookie);
			$data['step'] = 1;
			if (!empty($spid)) {
				$page = $this->GetPage($spid[0], 0, 0, $this->link);
				if (!preg_match('@<img [^/<>]*src\s?=\s?\"((https?://[^/\"<>]+)?/papi/media[^\"<>]+)\"@i', $page, $imgurl)) html_error('CAPTCHA img not found.');
				$imgurl = (empty($imgurl[2])) ? 'http://api.solvemedia.com'.$imgurl[1] : $imgurl[1];

				if (!preg_match_all('@<input [^/|<|>]*type\s?=\s?\"?hidden\"?[^/<>]*\s?name\s?=\s?\"(\w+)\"[^/<>]*\s?value\s?=\s?\"([^\"<>]+)\"[^/<>]*/?\s*>@i', $page, $forms)) html_error('CAPTCHA data not found.');
				$forms = array_combine($forms[1], $forms[2]);
				foreach ($forms as $n => $v) $data["T8[$n]"] = urlencode($v);

				//Download captcha img.
				$page = $this->GetPage($imgurl);
				$capt_img = substr($page, strpos($page, "\r\n\r\n") + 4);
				$imgfile = DOWNLOAD_DIR . 'mediafire_captcha.gif';

				if (file_exists($imgfile)) unlink($imgfile);
				if (!write_file($imgfile, $capt_img)) html_error('Error getting CAPTCHA image.');

				$this->EnterCaptcha($imgfile.'?'.time(), $data, 20);
				exit();
			} else $this->reCAPTCHA($pid[1], $data);
		}
	}
}

/*
 * credit to farizemo [at] rapidleech forum
 * by vdhdevil
 * remove additional function for temporary fix until get finished - Ruud v.Tony 06-01-2011
 * fix for shared premium link by Ruud v.Tony 23-01-2012
 * regex fix for download link not found by Th3-822 24-02-2012
 * added support for captcha by Th3-822 14-04-2012
 * freedl regexp fixed again by Th3-822 10-05-2012
 * incomplete fix for getting dllink by Th3-822 14-05-2012
 * added solvemedia captcha support && fixed dead link msgs by Th3-822 06-10-2012
 * quick fix for new format/redirect for share links && fixed capcha submit url by Th3-822 24-05-2013
 * fixed password post url by Th3-822 27-07-2013
 * fixed redirects by Th3-822 24-09-2013
 * fixed captcha forms by Th3-822 11-04-2014
 */
 
?>