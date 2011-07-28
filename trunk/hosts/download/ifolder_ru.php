<?php

if (!defined('RAPIDLEECH')) {
	require_once ("index.html");
	exit;
}

class ifolder_ru extends DownloadClass {
	public function Download($link) {
		global $Referer;

		$page = $this->GetPage($link);
		$fid = basename($link);
		is_present($page, "Файл номер <b>$fid</b> не найден !!!", 'Request file not found');
		is_present($page, "Файл номер <b>$fid</b> удален", 'Request file deleted');

		if ($_GET["action"] == "1") {
			if (empty($_POST['captcha'])) {
				html_error("You didn't enter the image verification code.");
			}
			$sesid = $_POST['session'];

			$post = array();
			$post["confirmed_number"] = $_POST['captcha'];
			$post["session"] = $sesid;
			$post["ints_session"] = $_POST['ints_session'];
			$post["action"] = 1;
			$post[$_POST["xtravar"]] = 1;

			$cookie = urldecode($_POST['cookie']);
			$page = $this->GetPage("http://ints.ifolder.ru/ints/frame/?session=$sesid", $cookie, $post);
			is_present($page, "/random/images/?session=", "Error: Entered CAPTCHA was incorrect.");
			if (!preg_match('@Location: (http://ifolder.ru/[^\r|\n]+)@i', $page, $loc)) html_error("Redirection not found.");
			$page = $this->GetPage($loc[1], $cookie);
		} else {
			$page = $this->GetPage("http://ints.ifolder.ru/ints/?ifolder.ru/$fid?ints_code=");
			$cookie = GetCookies($page);
			if (!preg_match_all('@http://ints.ifolder.ru/ints/sponsor/\?bi=\d+&session=(\w+)&u=(http%3A%2F%2F[^>|"|\']+)@i', $page, $match)) html_error("Ads not found.");
			$sesid = $match[1][0];
			$match = array_unique($match[0]);
			$this->GetPage($match[array_rand($match)]); //Get rand ad.

			$frame = "http://ints.ifolder.ru/ints/frame/?session=$sesid";
			$page = $this->GetPage($frame);
			if (!preg_match('@var delay = (\d+);@i', $page, $T)) html_error("Timer not found.");
			$this->CountDown($T[1]+1);

			$page = $this->GetPage($frame);
			is_notpresent($page, "/random/images/?session=", "CAPTCHA not found.");

			$data = $this->DefaultParamArr($link, $cookie);
			$data['session'] = urlencode($sesid);
			$data['ints_session'] = urlencode(cut_str($page, 'tag.value = "', '"'));
			$data['xtravar'] = urlencode(substr(cut_str($page, "var s= '", "';"),cut_str($page,'.substring(',')')));
			$data['action'] = "1";

			$this->EnterCaptcha("http://ints.ifolder.ru/random/images/?session=$sesid", $data);
			exit;
		}

		if (!preg_match('@id="download_file_href" href="(http://stg\d+.ifolder.ru/download/[^"]+)"@i', $page, $D)) html_error("Download-link not found.");
		$dllink = $D[1];

		$filename = html_entity_decode(cut_str($page,'Название:</span> <b>','</b>'));
		$this->RedirectDownload($dllink, $filename);
	}
}

//[24-4-2011]  Rewritten by Th3-822 (Free download only).
?>