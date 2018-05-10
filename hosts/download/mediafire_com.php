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

		if (!preg_match('@https?://(?:[\w\-]+\.)*mediafire\.com/(?:(download|file)/|(download\.php|file/|view/)?\??)([\w\-\.]+)(?(1)(/[^/\s]+))?@i', $link, $this->fid)) html_error('Invalid Link?');

		$this->link = $GLOBALS['Referer'] = 'http://www.mediafire.com/file/' . $this->fid[3] . (!empty($this->fid[4]) ? $this->fid[4] : '');

		if (empty($_POST['step']) || $_POST['step'] != '1') {
			$this->page = $this->GetPage($this->link, $this->cookie, 0, 'http://www.mediafire.com/?' . $this->fid[3]);
			$this->cookie = GetCookiesArr($this->page, $this->cookie);
			if (preg_match('@\nLocation: .*(/file/([^/\r\n]+)/?[^\r\n]*)@i', $this->page, $redir)) {
				$this->link = $GLOBALS['Referer'] = 'http://www.mediafire.com'.$redir[1];
				$this->page = $this->GetPage($this->link, $this->cookie);
				$this->cookie = GetCookiesArr($this->page, $this->cookie);
			}
			if (preg_match('@/error\.php\?errno=\d+@i', $this->page, $redir)) {
				$this->page = $this->GetPage('http://www.mediafire.com'.$redir[0]);
				if (preg_match('@error_msg_title(?: notranslate)?">\s*([^\r\n<>]+)\s*<@i', $this->page, $err)) html_error($err[1]);
				html_error('Link is not available');
			}
		}

		$this->MF_Captcha();
		if (strpos($this->page, 'name="downloadp" id="downloadp"')) {
			$DefaultParam = $this->DefaultParamArr(preg_replace('@/file/([^/]+)/?.*@i', '/?$1', $link), $this->cookie);
			echo "<form action='".htmlspecialchars($GLOBALS['PHP_SELF'], ENT_QUOTES)."' method='POST'>\n";
			foreach ($DefaultParam as $key => $value) echo "<input type='hidden' name='$key' value='" . htmlspecialchars($value, ENT_QUOTES) . "' />\n";
			echo "Enter your password here:<br />\n<input type='text' name='mfpassword' value='' placeholder='Enter file password here' autofocus='autofocus' required='required' />\n<input type='submit' />\n</form>";
			return html_error('File requires password');
		}
		if (!preg_match('@https?://(?:[\w\-]+\.)+[\w\-]+(?:\:\d+)?/[\w\-\.]{5,}/' . preg_quote($this->fid[3]) . '/[^\?\'\"\t<>\r\n\\\]+@i', $this->page, $dl)) return html_error("Error: Download link [FREE] not found!");
		$this->RedirectDownload($dl[0], 'Mediafire.com');
	}

	private function MF_Captcha() {
		if (!empty($this->page) && stripos($this->page, ">Authorize Download</a>") === false) return;
		if (!empty($_POST['step']) && $_POST['step'] == '1') {
			$_POST['step'] = false;
			if (empty($_POST['recaptcha2_response_field']) && empty($_POST['recaptcha_response_field']) && empty($_POST['adcopy_response']) && empty($_POST['mf_captcha_response'])) html_error('You didn\'t enter the image verification code.');
			if (empty($_POST['mf_captcha_response'])) {
				if (empty($_POST['adcopy_response'])) {
					if (empty($_POST['recaptcha2_public_key'])) $post = array('recaptcha_challenge_field' => urlencode($_POST['recaptcha_challenge_field']), 'recaptcha_response_field' => urlencode($_POST['recaptcha_response_field']));
					else $post = $this->verifyReCaptchav2();
				} else $post = $this->verifySolveMedia();
				$this->cookie = StrToCookies(urldecode($_POST['cookie']));
			} else {
				$post = array('mf_captcha_response' => $_POST['mf_captcha_response']);
			}

			$purl = 'http://www.mediafire.com/?' . $this->fid[3];

			$this->page = $this->GetPage($purl, $this->cookie, $post);
			$this->cookie = GetCookiesArr($this->page, $this->cookie);
			is_present($this->page, 'Your entry was incorrect, please try again!');

			$this->page = $this->GetPage($this->link, $this->cookie, 0, $purl);
			$this->cookie = GetCookiesArr($this->page, $this->cookie);
			is_present($this->page, 'Your entry was incorrect, please try again!.');

			$this->MF_Captcha();
		} else {
			$data = $this->DefaultParamArr($this->link, $this->cookie);
			$data['step'] = 1;

			if (($pos = stripos($this->page, 'data-sitekey=')) !== false && preg_match('@data-sitekey=\s*[\"\']([\w\.\-]+)[\"\']@i', $this->page, $cKey, 0, $pos)) {
				// reCAPTCHA v2
				return $this->reCAPTCHAv2($cKey[1], $data);
			} else if (($pos = stripos($this->page, '://api.solvemedia.com/')) !== false && preg_match('@https?://api\.solvemedia\.com/papi/challenge\.(?:no)?script\?k=([\w\.\-]+)@i', $this->page, $cKey, 0, $pos)) {
				// SolveMedia
				return $this->SolveMedia($cKey[1], $data);
			} else if (preg_match('@https?://(?:[^/]+\.)?(?:(?:google\.com/recaptcha/api)|(?:recaptcha\.net))/(?:(?:challenge)|(?:noscript))\?k=([\w\.\-]+)@i', $this->page, $cKey)) {
				// Old reCAPTCHA
				return $this->reCAPTCHA($cKey[1], $data);
			} else if (($pseudoCaptcha = cut_str($this->page, 'name="mf_captcha_response" value="', '"'))) {
				// Best... CAPTCHA... Ever... :D
				if (!empty($_POST['mf_captcha_response'])) html_error('Captcha Loop?');
				$_POST['step'] = '1';
				$_POST['mf_captcha_response'] = html_entity_decode($pseudoCaptcha);
				return $this->MF_Captcha();
			}

			html_error('Error: CAPTCHA not found.');
		}
	}

	// Special Function Called by verifyReCaptchav2 When Captcha Is Incorrect, To Allow Retry. - Required
	protected function retryReCaptchav2() {
		$data = $this->DefaultParamArr($this->link, $this->cookie);
		$data['step'] = '1';

		return $this->reCAPTCHAv2($_POST['recaptcha2_public_key'], $data);
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
 * added recaptcha v2 captcha support && fixed dead link msgs by Th3-822 07-04-2015
 * added checkbox "captcha" support (non tested) by Th3-822 18-06-2015
 * fixed link regexp, redirect by Th3-822 18-10-2016
 * fixed download regexp by Th3-822 09-05-2018
 */
