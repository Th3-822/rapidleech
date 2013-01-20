<?php
if (!defined('RAPIDLEECH')) {
	require_once ('index.html');
	exit;
}

class uploo_net extends DownloadClass {

	public function Download($link) {
		if (!$_REQUEST['step']) {
			$link = preg_replace('@uploo\.net\/([^\/]+)\/@', 'uploo.net/en/', $link);
			$this->page = $this->GetPage($link);
			if (preg_match('/Location: (https?:\/\/[^\r\n]+)/', $this->page, $rd)) {
				$link = trim($rd[1]);
				$this->page = $this->GetPage($link);
			}
			is_present($this->page, 'L\'id du fichier est erron', 'Invalid link or maybe expired?');
			$this->cookie = GetCookies($this->page);
		}
		$this->link = $link;
		if ($_REQUEST['step'] == '1') {
			return $this->AfterCaptcha();
		} else {
			return $this->BeforeCaptcha();
		}
	}

	private function AfterCaptcha() {
		$post = array();
		$post['post_captcha'] = strtoupper($_POST['captcha']);
		$post['sub_form'] = $_POST['sub_form'];
		$this->link = urldecode($_POST['link']);
		$this->cookie = urldecode($_POST['cookie']);
		$referer = urldecode($_POST['referer']);
		$page = $this->GetPage($this->link, $this->cookie, $post, $referer);
		is_present($page, 'Le captcha entré est incorrect.', 'Captcha is incorrect, please retry');
		if (!preg_match('/var t = (\d+);/', $page, $w)) html_error('Error [Timer not found!]');
		$this->CountDown($w[1]);
		$rlink = $this->link . "/true";
		$page = $this->GetPage($rlink, $this->cookie, 0, $this->link);
		$dlink = trim(substr($page, strpos($page, "\r\n\r\n") + 4));
		$filename = basename(parse_url($dlink, PHP_URL_PATH));
		$this->RedirectDownload($dlink, $filename, $this->cookie, 0, $this->link);
		exit;
	}

	private function BeforeCaptcha() {
		if (!preg_match('/<a href="(http:\/\/[^\r\n]+)" title="" target="_self" class="dl-gratuit">Free download<\/a>/', $this->page, $tmp)) html_error('Error [Free Link not found!]');
		$flink = trim($tmp[1]);
		$page = $this->GetPage($flink, $this->cookie, 0, $this->link);
		// get the captcha
		$cap = $this->GetPage('http://uploo.net/captcha/captcha.php', $this->cookie);
		$capt_img = substr($cap, strpos($cap, "\r\n\r\n") + 4);
		$imgfile = DOWNLOAD_DIR . "uploo_net_captcha.png";

		if (file_exists($imgfile)) unlink($imgfile);
		if (empty($capt_img) || !write_file($imgfile, $capt_img)) html_error("Error getting CAPTCHA image.", 0);
		// Captcha img downloaded
		$data = $this->DefaultParamArr($flink, $this->cookie, $this->link);
		$data['step'] = '1';
		$data['sub_form'] = cut_str($page, '<input name="sub_form" type="submit" class="submit-gen" value="', '" />');
		$this->EnterCaptcha($imgfile, $data);
		exit();
	}

}

/*
 * Written by Tony Fauzi Wihana/Ruud v.Tony 12-10-2012...Ge Je >> Galau Jenonk :((:((=))
 */
?>
