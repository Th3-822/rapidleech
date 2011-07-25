<?php
if (!defined('RAPIDLEECH')) {
	require_once("index.html");
	exit;
}

class gigasize_com extends DownloadClass {
	public function Download($link) {
		global $premium_acc;
			if (($_REQUEST ["premium_acc"] == "on" && $_REQUEST ["premium_user"] && $_REQUEST ["premium_pass"]) || ($_REQUEST ["premium_acc"] == "on" && $premium_acc ["gigasize"] ["user"] && $premium_acc ["gigasize"] ["pass"])) {
				$this->Premium($link);
			} elseif ($_POST['step'] == "1") {
				$this->Free($link);
			} else {
				$this->Retrieve($link);
			}
	}
	
	private function Retrieve($link) {
		$page = $this->GetPage($link);
		is_present($page, "The file you are looking for is not available");
		is_present($page, "has been removed because we have received", "The file you are looking was removed");
		$cookie = GetCookies($page);

		if (!$fname = cut_str($page, '<strong title="','"')) html_error("Filename not found.", 0);
		if (!preg_match('/name="fileId" value="([^"]+)"/i', $page, $fileid)) html_error("Link fileId not found.", 0);
		$link = 'http://www.gigasize.com/get/'.$fileid[1];

		if (!$k = cut_str($page, "adscaptcha.com/Get.aspx?", "'")) html_error("Error getting CAPTCHA data.", 0);
		$page = $this->GetPage("http://api.adscaptcha.com/Get.aspx?$k");

		if (!$ch = cut_str($page, "challenge: '","'")) html_error("Error getting CAPTCHA image.", 0);

		$data = $this->DefaultParamArr($link, $cookie);
		$data['step'] = '1';
		$data['fileId'] = urlencode($fileid[1]);
		$data['fname'] = urlencode($fname);
		$data['adscaptcha_challenge_field'] = urlencode($ch);
		$this->EnterCaptcha("http://api.adscaptcha.com/Challenge.aspx?cid=$ch&w=180", $data, 25);
		exit();
	}

	private function Free($link) {
		if (!$fname = urldecode($_POST['fname'])) html_error("Cannot get filename.", 0);
		$post = array();
		$post['fileId'] = $_POST['fileId'];
		$post['adUnder'] = '';
		$post['adscaptcha_response_field'] = $_POST['captcha'];
		$post['adscaptcha_challenge_field'] = $_POST['adscaptcha_challenge_field'];
		$cookie = urldecode($_POST['cookie']);

		$page = $this->GetPage("http://www.gigasize.com/getoken", $cookie, $post);
		if (!preg_match('/"status":(\d+)/i', $page, $st)) html_error("Token status not found.");

		if ($st[1] != 1) {
			if ($st[1] == 0) return $this->Retrieve($link); // Bad CAPTCHA
			if ($st[1] == 2) html_error("Password protected files aren't supported.");
			html_error("Unknown error. ({$st[1]})");
		}

		$token = $this->GetPage('http://www.gigasize.com/formtoken', $cookie);
		$token = trim(substr($token, strpos($token, "\r\n\r\n") + 4));

		$this->CountDown(32);

		$post = array();
		$post['fileId'] = $_POST['fileId'];
		$post['token'] = $token;
		$page = $this->GetPage("http://www.gigasize.com/getoken", $cookie, $post);
		if (!preg_match('@"status":(\d+)(?:,"redirect":"([^"]+)")?@i', $page, $dl)) html_error("Download data not found.");
		if ($dl[1] == 0) html_error("Error... Limit reached or need premium for download this file.");
		if ($dl[1] != 1) html_error("Unknown error 2. ({$dl[1]})");

		$page = $this->GetPage(str_replace('\\', '', $dl[2]), $cookie);
		if (!preg_match('@Location: (http://(www\d+\.)?gigasize\.com(:\d+)?/[^\r|\n]+)@i', $page, $dllink)) html_error("Download link not found.");

		$this->RedirectDownload($dllink[1], $fname, $cookie);
	}

	private function Premium($link) {
		html_error("Not supported now!");
	}
	
}

/*
 * Gigasize download plugin(free) by Th3-882 22-07-2011
 */

?>