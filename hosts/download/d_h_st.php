<?php

if (!defined('RAPIDLEECH')) {
	require_once('index.html');
	exit();
}

class d_h_st extends DownloadClass {
	public function Download($link) {
		if (stripos($link, '://d-h.st/') === false) {
			$link = parse_url($link);
			$link['host'] = 'd-h.st';
			$link = rebuild_url($link);
		}
		$page = $this->GetPage($link);
		is_present($page, 'The file you were looking for could not be found', 'File Not Found.');

		if (!preg_match('@https?://fs\d*\.d-h\.st/download/\w+/\w+/[^\s<>\'\"]+@i', $page, $dl)) html_error('Download Link Not Found');
		return $this->RedirectDownload($dl[0], basename(urldecode(parse_url($dl[0], PHP_URL_PATH))), 0, 0, $link);
	}
}

// [26-7-2015] Rewritten by Th3-822.

?>
