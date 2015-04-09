<?php
if (!defined('RAPIDLEECH')) {
	require_once("index.html");
	exit;
}

class demo_ovh_eu extends DownloadClass {
	public function Download($link) {
		if (preg_match('@^https?://demo\.ovh\.eu/download/(\w+)/([^\t\r\n]+)$@i', $link, $directLink)) {
			// Allow direct links.
			return $this->RedirectDownload($directLink[0], urldecode(basename($directLink[2])), 0, 0, 'http://demo.ovh.eu/en/' . $directLink[1]);
		}
		$page = $this->GetPage(str_ireplace('https://', 'http://', $link));
		if (preg_match('@^HTTP/1\.[0|1] 404 Not Found@i', $page)) html_error('File Not Found');
		$count = preg_match_all('@href="/(download/\w+/[^\"]+)"@i', $page, $links);
		if ($count > 1) {
			usort($links[1], array($this, 'sortFiles'));
			foreach ($links[1] as &$path) $path = "http://demo.ovh.eu/$path";
			return $this->moveToAutoDownloader($links[1]);
		} elseif ($count < 1) html_error('Download link Not Found');

		$this->RedirectDownload('http://demo.ovh.eu/' . $links[1][0], urldecode(basename($links[1][0])));
	}

	public function sortFiles($a, $b) {
		return strcmp(substr(strrchr($a, '/'), 1), substr(strrchr($b, '/'), 1));
	}

}

//[25-2-2015] Written by Th3-822

?>
