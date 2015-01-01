<?php

if (!defined('RAPIDLEECH')) {
	require_once('index.html');
	exit();
}

class filesflash_com extends DownloadClass {
	private $page, $cookie;
	public function Download($link) {
		$this->DLregexp = '@https?://(?:[\w\-]+\.)+filesflash\.(?:com|net)(?:\:\d+)?/\w{32}/\w{32}/[^\'\"\t<>\r\n]+@i';
		$this->cookie = array();

		$link = parse_url($link);
		$link['host'] = 'filesflash.com';
		if (!empty($link['port']) && !in_array($link['port'], array(80, 443))) $link['port'] = (strtolower($link['scheme']) == 'https' ? 443 : 80);
		$link = rebuild_url($link);

		$this->link = $GLOBALS['Referer'] = $link;
		if (empty($_POST['step']) || $_POST['step'] != '1') {
			$this->page = $this->GetPage($this->link, $this->cookie);
			is_present($this->page, 'That file has been deleted.');
			is_present($this->page, 'That is not a valid url.', 'Error: Invalid Link?.');
		}

		$this->FreeDL();
	}

	private function FreeDL() {
		if (!empty($_POST['step']) && $_POST['step'] == '1') {
			if (!empty($_POST['cookie'])) $this->cookie = StrToCookies(decrypt(urldecode($_POST['cookie'])));
			if (empty($_POST['recaptcha_response_field'])) html_error('You didn\'t enter the image verification code.');
			if (empty($_POST['recaptcha_challenge_field'])) html_error('Empty reCAPTCHA challenge.');

			$post = array();
			$post['token'] = $_POST['token'];
			$post['recaptcha_challenge_field'] = urlencode($_POST['recaptcha_challenge_field']);
			$post['recaptcha_response_field'] = urlencode($_POST['recaptcha_response_field']);
			$post['submit'] = 'Submit';

			$page = $this->GetPage('http://filesflash.com/freedownload.php', $this->cookie, $post);

			is_present($page, 'Your IP address is already downloading another link.');
			if (stripos($page, 'google.com/recaptcha/api/') !== false || stripos($page, 'recaptcha.net/') !== false) html_error('Wrong captcha entered.');

			if (!preg_match($this->DLregexp, $page, $dlink)) html_error('Error: Download link not found.');
			if (!preg_match('@\scount\s*=\s*(\d+)\s*;@i', $page, $wait)) html_error('Error: Countdown not found.');
			if ($wait[1] > 0) $this->CountDown($wait[1]);

			return $this->RedirectDownload($dlink[0], urldecode(basename(parse_url($dlink[0], PHP_URL_PATH))));
		}
		$this->cookie = GetCookiesArr($this->page, $this->cookie);

		$post = array();
		$post['token'] = urlencode(cut_str($this->page, 'name="token" value="', '"')) or html_error('Download Token not Found.');
		$post['freedl'] = urlencode(cut_str($this->page, 'name="freedl" value="', '"')) or $post['submit'] = '+Start+free+download+';

		$page = $this->GetPage('http://filesflash.com/freedownload.php', $this->cookie, $post);

		is_present($page, 'Your IP address is already downloading another link.');

		$data = $this->DefaultParamArr($this->link, $this->cookie, true, true);
		$data['step'] = 1;
		$data['token'] = $post['token'];

		// Modified regexp
		if (!preg_match('@https?://(?:[\w\-]+\.)?(?:google\.com/recaptcha/api|recaptcha\.net)/(?:challenge|noscript)\?(?:[^\'\"<>&]+&(?:amp;)?)*k=([\w\.\-]+)@i', $page, $reCaptcha)) html_error('reCAPTCHA not found.');

		$this->reCAPTCHA($reCaptcha[1], $data);
		exit;
	}

	public function CheckBack($header) {
		if (stripos($header, "\nContent-Type: text/html") !== false) {
			global $fp, $sFilters;
			if (empty($fp) || !is_resource($fp)) html_error('[filesflash_com] Cannot check download error.');
			if (empty($sFilters)) $sFilters = array();
			if (in_array('dechunk', stream_get_filters()) && empty($sFilters['dechunk'])) $sFilters['dechunk'] = stream_filter_append($fp, 'dechunk', STREAM_FILTER_READ);
			$body = stream_get_contents($fp);
			if (empty($sFilters['dechunk']) && stripos($header, "\nTransfer-Encoding: chunked") !== false && function_exists('http_chunked_decode')) {
				$dechunked = http_chunked_decode($body);
				if ($dechunked !== false) $body = $dechunked;
				unset($dechunked);
			}
			is_present($body, 'Your IP address is not valid for this link.', '[filesflash_com] Your IP address is not valid for this link.');
			is_present($body, 'Your IP address is already downloading another link.', '[filesflash_com] Your IP address is already downloading another link.');
			is_present($body, 'Your link has expired.', '[filesflash_com] Your link has expired.');
			is_present($body, 'Interrupted free downloads cannot be resumed.', '[filesflash_com] Interrupted free downloads cannot be resumed.');
			html_error('[filesflash_com] Unknown download error.');
		}
	}
}

// [15-6-2014]  Written by Th3-822. (FreeDl only.)

?>