<?php

if (!defined('RAPIDLEECH')) {
	require_once('index.html');
	exit;
}

class filesend_co extends DownloadClass {
	private $page, $cookie = array();
	public function Download($link) {
		$this->link = $GLOBALS['Referer'] = str_ireplace('http://', 'https://', $link);

		$this->page = $this->GetPage($this->link, $this->cookie);
		is_present($this->page, 'Nothing has been found', 'File Not Found.');
		is_present($this->page, 'There are no files to be shown', 'File Not Found or Deleted.');
		is_present($this->page, 'Files have been removed due to lack of activity', 'File Deleted due to Inactivity.');
		is_present($this->page, 'Files have been reported', 'File Deleted due to Abuse.');
		$this->cookie = GetCookiesArr($this->page, $this->cookie);

		// Only FreeDL at the Moment.
		return $this->FreeDL();
	}

	private function FreeDL() {
		$json = $this->getMetaData($this->page, 'FreeDL_1');
		if (empty($json['app']['downloader'])) {
			if (empty($json['app']['folderView']['entities'])) html_error('Files Metadata Not Found.');
			$files = $json['app']['folderView']['entities'];
			if (empty($files[1])) {
				// Just 1 File, Download Directly
				$file = $files[0];
				if (empty($file['downloadUrl'])) html_error('Redirect Link Not Found.');
				$redir = (strpos($file['downloadUrl'], '://') !== false ? $file['downloadUrl'] : 'https://filesend.co' . $file['downloadUrl']);
				$page = $this->GetPage($redir, $this->cookie);
				$this->cookie = GetCookiesArr($page, $this->cookie);

				$json = $this->getMetaData($page, 'FreeDL_2');
				if (empty($json['app']['downloader'])) html_error('Download Metadata Not Found.');
			} else {
				// More Than 1 File
				$links = array();
				foreach ($files as $file) {
					if (!empty($file['downloadUrl'])) {
						$links[] = (strpos($file['downloadUrl'], '://') !== false ? $file['downloadUrl'] : 'https://filesend.co' . $file['downloadUrl']);
					}
				}
				return $this->moveToAutoDownloader($links);
			}
		}
		$downloader = $json['app']['downloader'];
		if (empty($downloader['url'])) html_error('Download Link Not Found.');

		$DL = (strpos($downloader['url'], '://') !== false ? $downloader['url'] : 'https://filesend.co' . $downloader['url']);
		return $this->RedirectDownload($DL);
	}

	private function getMetaData($page, $suffix) {
		if (!preg_match('@\[\'tEGurWM89B6p'.'hAeXuNMH'.'xYUfkUiV'.'UUWoPHnKV'.'yHkWhr2TLqf'.'nh3KLnAB'.'tkEVkLRc\'\]\s*=\s*"([^"]+)"@i', $page, $data)) html_error("Page Metadata Not Found [$suffix]");
		$data = trim(rawurldecode($this->b64XorDecoder('3YKqDNZ'.'AFtzW4av'.'QrrNhRLjR'.'ArAPoaCY4'.'KeZVugyPG'.'ddTezMZzP'.'gAe9MN'.'RHbLX3G', $data[1])));
		is_notpresent($data, '{"app":', "Cannot validate metadata. Key has changed? [$suffix]");
		return $this->json2array($data, "Metadata Parse Error][$suffix");
	}

	private function b64XorDecoder($key, $str) {
		$key = str_split($key);
		$str = str_split(base64_decode($str));
		$kLen = count($key);
		$sLen = count($str);
		$output = '';
		for ($i = 0; $i < $sLen; $i++) $output .= chr(ord($str[$i]) ^ ord($key[floor($i % $kLen)]));
		return $output;
	}
}

// [06-12-2016] Written by Th3-822.

?>