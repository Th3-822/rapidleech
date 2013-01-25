<?php
if (!defined('RAPIDLEECH')) {
	require_once ('index.html');
	exit;
}

class upgrand_com extends DownloadClass {

	public function Download($link) {
		global $premium_acc;

		if (!$_REQUEST['step']) {
			$this->cookie['lang'] = 'english';
			$this->page = $this->GetPage($link, $this->cookie);
			is_present($this->page, '<b>File Not Found</b>');
		}
		$this->link = $link;
		if ($_REQUEST['premium_acc'] == 'on' && (($_REQUEST['premium_user'] && $_REQUEST['premium_pass']) || ($premium_acc['upgrand_com']['user'] && $premium_acc['upgrand_com']['pass']))) {
			return $this->Premium();
		} else {
			return $this->Free();
		}
	}

	private function Premium() {
		html_error('Unsupported now!');
	}

	private function Free() {
		if ($_REQUEST['step'] == '1') {
			$this->link = urldecode($_POST['link']);
			$this->cookie = StrToCookies(urldecode($_POST['cookie']));
			$post = array();
			foreach ($_POST['tmp'] as $k => $v) {
				$post[$k] = $v;
			}
			$post['code'] = $_POST['captcha'];
			$page = $this->GetPage($this->link, $this->cookie, $post, $this->link);
		} else {
			$form = cut_str($this->page, '<Form method="POST" action=\'\'>', '</Form>');
			if (!preg_match_all('/<input type="(hidden|submit)" name="([^"]+)" value="([^"]+)?">/', $form, $match)) html_error('Error[Post Form 1 - FREE not found!]');
			$match = array_combine($match[2], $match[3]);
			$post = array();
			foreach ($match as $k => $v) {
				$post[$k] = $v;
			}
			$page = $this->GetPage($this->link, $this->cookie, $post, $this->link);
		}
		if (stristr($page, 'Enter code below')) {
			$form = cut_str($page, '<Form name="F1" method="POST"', '</Form>');
			if (stristr($form, cut_str($form, '<div class="err">', '</div>'))) echo ("<center><font color='red'><b>Wrong Captcha, Please Retry!</b></font></center>");
			if (!preg_match_all('/<input type="hidden" name="([^"]+)" value="([^"]+)?">/', $form, $match)) html_error('Error[Post Form 2 - FREE not found!]');
			$match = array_combine($match[1], $match[2]);
			if (!preg_match('/(\d+)<\/span> seconds/', $form, $w)) html_error('Error[Timer not found!]');
			$this->CountDown($w[1]);

			$data = $this->DefaultParamArr($this->link, $this->cookie);
			$data['step'] = '1';
			foreach ($match as $k => $v) {
				$data["tmp[$k]"] = $v;
			}
			// Download captcha image
			if (!preg_match('/http:\/\/www\.upgrand\.com\/captchas\/[^\'"]+/', $form, $img)) html_error('Error[Captcha Image not found!]');
			$page = $this->GetPage($img[0], $this->cookie);
			$capt_img = substr($page, strpos($page, "\r\n\r\n") + 4);
			$imgfile = DOWNLOAD_DIR . 'upgrand_captcha.jpg';

			if (file_exists($imgfile)) unlink($imgfile);
			if (!write_file($imgfile, $capt_img)) html_error('Error getting CAPTCHA image.');

			$this->EnterCaptcha($imgfile.'?'.time(), $data, 20);
			exit;
		}
		is_present($page, cut_str($page, '<div class="err">', '<br>'));
		if (!preg_match('/https?:\/\/sv\d+\.upgrand\.com(:\d+)?\/d\/[^\r\n\'"]+/', $page, $dl)) html_error('Error[Download Link - FREE not found!]');
		$dlink = trim($dl[0]);
		$filename = basename(parse_url($dlink, PHP_URL_PATH));
		$this->RedirectDownload($dlink, $filename, $this->cookie, 0, $this->link);
		exit;
	}

}

/*
 * Written by Tony Fauzi Wihana/Ruud v.Tony 28-12-2012
 */
?>
