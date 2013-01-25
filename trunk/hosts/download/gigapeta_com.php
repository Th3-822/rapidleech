<?php
if (!defined('RAPIDLEECH')) {
	require_once('index.html');
	exit();
}

class gigapeta_com extends DownloadClass {

	public function Download($link) {
		if ($_REQUEST['step'] == 'Captcha') {
			$post['captcha_key'] = $_POST['captcha_key'];
			$post['captcha'] = $_POST['captcha'];
			$post['download'] = 'Download';
			$link = urldecode($_POST['link']);
			$cookie = urldecode($_POST['cookie']);
			$page = $this->GetPage($link, $cookie, $post, $link);
		} else {
			$page = $this->GetPage($link, array('lang' => 'us'));
			is_present($page, 'Attention! This file has been deleted from our server by user who uploaded it on the server.');
			is_notpresent($page, '<h1>Download file</h1>', 'Download file is not available for ur server area!');
			$cookie = GetCookiesArr($page, array('lang' => 'us'));

			if (!preg_match('/(\d+)<\/b> second/', $page, $wait)) html_error('Error[Timer not found!]');
			$this->CountDown($wait[1]);

			$time = time();
			$data = $this->DefaultParamArr($link, $cookie);
			$data['step'] = 'Captcha';
			$data['captcha_key'] = $time;

			// get the captcha
			$cap = $this->GetPage('http://gigapeta.com/img/captcha.gif?x=' . $time, $cookie);
			$capt_img = substr($cap, strpos($cap, "\r\n\r\n") + 4);
			$imgfile = DOWNLOAD_DIR . "gigapeta_captcha.gif";
			if (file_exists($imgfile)) unlink($imgfile);
			if (!write_file($imgfile, $capt_img)) html_error("Error getting CAPTCHA image.");

			$this->EnterCaptcha($imgfile, $data, 20);
			exit();
		}
		is_present($page, 'Entered figures don&#96;t coincide with the picture', 'You entered a wrong CAPTCHA code. Please try again.');
		if (!preg_match('/Location: (http:\/\/[a-z0-9]+\.gigapeta\.com\/download[^|\r|\n]+)/i', $page, $dl)) html_error('Error: Download link not found!');
		$dlink = trim($dl[1]);
		$FileName = basename(parse_url($dlink, PHP_URL_PATH));
		$this->RedirectDownload($dlink, $FileName, $cookie, 0, $link);
		exit();
	}

}

/*
 * Gigapeta.com free download plugin by Ruud v.Tony 05-11-2011
 * Small fix by Tony Fauzi Wihana/Ruud v.Tony 21-01-2013
 */
?>
