<?php

if (!defined('RAPIDLEECH')) {
	require_once ("index.html");
	exit;
}

class putlocker_com extends DownloadClass {
	public function Download($link) {
		$page = $this->GetPage($link);
		is_present($page, "/?404", 'File not found.');
		$cookie = GetCookies($page);

		//if (!preg_match('@<h1>([^<|\r|\n]+)<strong>@i', $page, $fname)) html_error('Error: Title not found.');
		is_present($page, "You have exceeded the daily stream limit", "Error: You have exceeded the daily stream limit for your country. Please try again tomorrow.");
		if (!preg_match("@countdownNum = (\d+);@i", $page, $cW) && !preg_match("@get_file\.php\?stream=(\w+)@i", $page, $getv) && !preg_match("@get_file\.php\?download=(\w+&key=\w+)@i", $page, $getf)) html_error("Error: Countdown/File info not found.");

		if ($cW) {
			$post = array();
			if (!$post['hash'] = cut_str($page, 'value="', '" name="hash"')) html_error('Error: Post data not found.');
			$post['confirm'] = 'Continue as Free User';

			$this->CountDown($cW[1] + 2);

			$this->GetPage($link, $cookie, $post);
			$page = $this->GetPage($link, $cookie);
			is_present($page, "You have exceeded the daily stream limit", "Error: You have exceeded the daily stream limit for your country. Please try again tomorrow.");
			if (!preg_match("@get_file\.php\?stream=(\w+)@i", $page, $getv) && !preg_match("@get_file\.php\?download=(\w+&key=\w+)@i", $page, $getf)) html_error('Error: File info not found.');
		}

		if ($getv) {
			$page = $this->GetPage("http://www.putlocker.com/get_file.php?stream=".$getv[1], $cookie);
			is_present($page, "/images/expired_link.gif", 'Error: The stream link has expired.');
			if (!preg_match('@"(http://[^\.|"]+\.putlocker.com/\w+/\w+/([^/|"]+)/[^"]+)"@i', $page, $downl)) html_error('Error: Stream link not found.');

			if(!preg_match('@\.[^\.]+$@i', basename($downl[2]), $ext)) $ext = array('.flv');
			$fname = str_replace(Array ("\\", "/", ":", "*", "?", "\"", "<", ">", "|"), "_", urldecode(trim($downl[2]))) . " [{$getv[1]}]" . $ext[0];
			$this->RedirectDownload($downl[1], $fname, $cookie, 0, 0, $fname);
		} else {
			$page = $this->GetPage("http://www.putlocker.com/get_file.php?download=".$getf[1], $cookie);
			is_present($page, "/images/expired_link.gif", 'Error: The download link has expired.');

			if (!preg_match('@Location: (http://[^\.|"]+\.putlocker.com/\w+/\w+/([^/|\r|\n]+)/[^\r|\n]+)@i', $page, $downl)) {
				$page = $this->GetPage($link, $cookie);
				is_present($page, "This content server is down for maintenance", "Error: This content server is down for maintenance. Please try again a bit later.");
				html_error('Error: Download link not found.');
			}

			if (!$fname = cut_str($page, 'attachment; filename="', '"')) $fname = $downl[2];
			$this->RedirectDownload($downl[1], $fname, $cookie, 0, 0, $fname);
		}
	}
}

//[18-8-2011]  Written by Th3-822.

?>