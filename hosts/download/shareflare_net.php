<?php
if (!defined('RAPIDLEECH')) {
	require_once("index.html");
	exit;
}

class shareflare_net extends DownloadClass {

	public function Download($link) {
		global $premium_acc;

		if (!$_REQUEST['step']) {
			$this->cookie['lang'] = 'en';
			$this->page = $this->GetPage($link, $this->cookie);
			if (preg_match('/Location: (https?:\/\/[^\r\n]+)/i', $this->page, $rd)) {
				$link = trim($rd[1]);
				$this->cookie = GetCookiesArr($this->page, $this->cookie);
				$this->page = $this->GetPage($link, $this->cookie);
			}
			is_present($this->page, 'File not found');
			$this->cookie = GetCookiesArr($this->page, $this->cookie);
		}
		$this->link = $link;
		if ($_REQUEST['premium_acc'] == 'on' && (($_REQUEST['premium_pass']) || ($premium_acc['shareflare_net']['pass']))) {
			html_error('Not supported now!');
		} else {
			$this->Free();
		}
	}

	private function Free() {

		if ($_REQUEST['step'] == '1') {
			$post = array();
			$post['recaptcha_challenge_field'] = $_POST['recaptcha_challenge_field'];
			$post['recaptcha_response_field'] = $_POST['recaptcha_response_field'];
			$post['recaptcha_control_field'] = rawurlencode(rawurldecode($_POST['recaptcha_control_field']));
			$this->link = urldecode($_POST['link']);
			$this->cookie = urldecode($_POST['cookie']);
			$page = $this->GetPage($this->link, $this->cookie, $post, $this->link, 0, 1);
		} else {
			if (!preg_match('/https?:\/\/([^\/]+)\/download\/[^\r\n]+/', $this->link, $sv)) html_error('Error[Shareflare server not found!]');
			$server = trim($sv[1]);
			if (!preg_match('/<form action="(.*)" method="post" id="dvifree">/i', $this->page, $rl)) html_error('Error[Shareflare free link not found!]');
			$form = cut_str($this->page, "<form action=\"$rl[1]\"", "</form>");
			$post = $this->AutomatePost($form);
			$this->link = 'http://' . $server . $rl[1];
			$page = $this->GetPage($this->link, $this->cookie, $post, $this->link);
			$this->cookie = GetCookiesArr($page, $this->cookie);
			unset($post);
			if (!preg_match('/<form action="(.*)" method="post" id="dvifree">/i', $page, $rl)) html_error('Error: Redirect link 1 can\'t be found!');
			$this->link = trim($rl[1]);
			$form = cut_str($page, "<form action=\"$rl[1]\"", "</form>");
			$post = $this->AutomatePost($form);
			$post['frameset'] = cut_str($form, '<input class="clink lid-download" name="frameset" type="submit" value=\'', '\'/>');
			$page = $this->GetPage($this->link, $this->cookie, $post, $this->link);
			if (!preg_match('/(\d+)<\/span> seconds/', $page, $wait)) html_error('Error[Timer not found!]');
			$this->CountDown($wait[1]);
			$checklink = 'http://shareflare.net' . cut_str($page, "ajax_check_url = '", "'");
			$this->GetPage($checklink, $this->cookie, array(), $this->link, 0, 1); //empty array in post variable needed...

			if (!preg_match('@https?://(?:[^/]+\.)?(?:(?:google\.com/recaptcha/api)|(?:recaptcha\.net))/(?:(?:challenge)|(?:noscript))\?k=([\w|\-]+)@i', $page, $pid)) html_error('reCAPTCHA not found.');
			if (!preg_match('@var[\s\t]+recaptcha_control_field[\s\t]*=[\s\t]*\'([^\'\;]+)\'@i', $page, $ctrl)) html_error('Captcha control field not found.');

			$data = $this->DefaultParamArr("http://shareflare.net/ajax/check_recaptcha.php", $this->cookie);
			$data['step'] = '1';
			$data['recaptcha_control_field'] = rawurlencode($ctrl[1]);
			$this->reCAPTCHA($pid[1], $data);
			exit;
		}
		is_present($page, 'error_wrong_captcha', 'Error: Wrong Captcha Entered.');
		is_present($page, 'error_free_download_blocked', 'Error: FreeDL limit reached.');
		if (!preg_match('/https?:\/\/[\w.]+\/d\/[^\r\n]+/', $page, $dl)) html_error('Error[Download Link - FREE not found!]');
		$dlink = trim($dl[0]);
		$filename = basename(parse_url($dlink, PHP_URL_PATH));
		$this->RedirectDownload($dlink, $filename, $this->cookie, 0, $this->link);
		exit;
	}

	private function AutomatePost($form) {
		if (!preg_match_all('/<input type="hidden" name="([^"]+)" value="([^"]+)?" \/>/i', $form, $match)) html_error("Error: Post Data not found!");
		$post = array();
		$match = array_combine($match[1], $match[2]);
		foreach ($match as $k => $v)
			$post[$k] = $v;
		return $post;
	}

}

//shareflare.net free download plugin by Ruud v.Tony 14-10-2011
//fixed by Tony Fauzi Wihana/Ruud v.Tony 11-01-2013, some recaptcha code taken in letitbit plugin code by Th3-822
?>
