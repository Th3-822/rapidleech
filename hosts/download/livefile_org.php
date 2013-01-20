<?php

if (!defined('RAPIDLEECH')) {
	require_once ("index.html");
	exit();
}

class livefile_org extends DownloadClass {
	private $page, $cookie;
	public function Download($link) {
		global $premium_acc;

		if (empty($_POST['step']) || $_POST['step'] != '1') {
			$this->page = $this->GetPage($link);
			is_present($this->page, "The requested file is deleted or is not available for download");
			$this->cookie = GetCookiesArr($this->page);
		}
		if ($_REQUEST["premium_acc"] == "on" && ((!empty($_REQUEST["premium_user"]) && $_REQUEST["premium_user"] == 'key' && !empty($_REQUEST["premium_pass"])) || !empty($premium_acc["livefile_org"]["key"]))) {
			$this->lastmesg = lang(300)."<br />Downloading with premium key.";
			$this->changeMesg($this->lastmesg);
			$pkey = (empty($_REQUEST["premium_user"]) || $_REQUEST["premium_user"] != 'key' || empty($_REQUEST["premium_pass"])) ? $premium_acc["livefile_org"]["key"] : trim($_REQUEST["premium_pass"]);
			$this->PremiumKey($link, $pkey);
		} elseif ($_REQUEST["premium_acc"] == "on" && ((!empty($_REQUEST["premium_user"]) && !empty($_REQUEST["premium_pass"])) || (!empty($premium_acc["livefile_org"]["user"]) && !empty($premium_acc["livefile_org"]["pass"])))) {
			$this->Login($link);
		} else {
			$this->FreeDL($link);
		}
	}

	private function FreeDL($link) {
		if (empty($_POST['step']) || $_POST['step'] != '1') {
			if (!preg_match('@href="((https?://[^/|\"|\<|\>]+)?/get/\w+/[^/|\"|\<|\>]+/free)"@i', $this->page, $plink)) html_error("Error: cannot find continue link.");
			$plink = (empty($plink[2])) ? "http://livefile.org".$plink[1] : $plink[1];
			$page = $this->GetPage($plink, $this->cookie);
			is_present($page, '/onlyonefree', 'Parallel downloading is not supported in free download.');

			if (!preg_match("@FreeDlWait\s*\(\s*'(\w+)'\s*,\s*'(\w+)'\s*,\s*'?(\d+)'?\s*,\s*'[^\)|\']+'\s*,\s*'[^\)|\']+'\s*,\s*'([^\)|\']+)'\s*\)\s*;@i", $page, $DlWait)) html_error("Error: Countdown not found");
			if ($DlWait[3] > 0) $this->CountDown($DlWait[3]);

			$page = $this->GetPage("http://livefile.org/captcha.php?f={$DlWait[1]}&dl={$DlWait[2]}&name={$DlWait[4]}&ok=true", $this->cookie, 0, "$plink");

			if (!preg_match('@<img [^\<|\>]* src="((https?://[^/|\"|\<|\>]+)?/[^/|\"|\<|\>]+)"@i', $page, $imgurl)) html_error("Captcha not found");
			$imgurl = (empty($imgurl[2])) ? "http://livefile.org".$imgurl[1] : $imgurl[1];

			$data = $this->DefaultParamArr($plink, $this->cookie, $link);
			$data['step'] = 1;

			//Download captcha img.
			$page = $this->GetPage($imgurl, $this->cookie);
			$capt_img = substr($page, strpos($page, "\r\n\r\n") + 4);
			$imgfile = DOWNLOAD_DIR . "livefile_captcha.png";

			if (file_exists($imgfile)) unlink($imgfile);
			if (!write_file($imgfile, $capt_img)) html_error("Error getting CAPTCHA image.", 0);

			$this->EnterCaptcha($imgfile.'?'.time(), $data);
			exit;
		} else {
			if (empty($_POST['captcha'])) html_error("You didn't enter the image verification code.");
			$this->cookie = (!empty($_POST['cookie'])) ? urldecode($_POST['cookie']) : array();

			$post = array('checkcaptcha' => 1, 'cpid' => $_POST['captcha']);
			$page = $this->GetPage($link, $this->cookie, $post);
			is_present($page, 'Captcha Error', 'Wrong CAPTCHA entered.');

			if (!preg_match('@Location: (https?://[^/|\r|\n]+/(?:(?:getfile)|(?:dl))/[^\r|\n]+)@i', $page, $dlink)) html_error('Error: Download link not found.');

			$FileName = urldecode(basename(parse_url($dlink[1], PHP_URL_PATH)));
			$this->RedirectDownload($dlink[1], $FileName);
		}
	}

	private function PremiumKey($link, $key) {
		$post = array('keycode' => urlencode($key));
		if (!($post['file']=cut_str($this->page, 'name="file" value="', '"'))) html_error("File key not found.");

		$page = $this->GetPage("http://livefile.org/valid.php", $this->cookie, $post);
		is_present($page, 'Key code not found', 'Invalid premium keycode');

		$page = $this->GetPage($link, $this->cookie);
		$this->lastmesg .= "<br />Remaining traffic: <b>".cut_str($page, 'Remaining traffic: <b>', '</b>')."</b>";
		$this->changeMesg($this->lastmesg);

		if (!preg_match('@https?://[^/|\"|\'|\r|\n]+/(?:(?:getfile)|(?:dl))/[^\"|\'|\r|\n]+@i', $page, $dlink)) html_error('Error: Download-link not found.');

		$FileName = urldecode(basename(parse_url($dlink[0], PHP_URL_PATH)));
		$this->RedirectDownload($dlink[0], $FileName);
	}

	private function Login($link) {
		html_error('[T8] It\'s not done yet... Maybe later... (I need an account for making this).');
	}
}

// [17-6-2012]  Written by Th3-822. (Free and premium key support only)
// I don't know why this was deleted, so i'm uploading it again... I think that it's the title, so i've renamed it. - Th3-822

?>