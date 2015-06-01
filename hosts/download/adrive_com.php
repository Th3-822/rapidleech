<?php
if (!defined('RAPIDLEECH')) {
	require_once ('index.html');
	exit();
}

class adrive_com extends DownloadClass {
	public function Download($link) {
		if (!preg_match('@https?://(?:www\.)?adrive\.com/public/(?!(?:dir)?view)(\w+)(?:/|\.html?|$)@i', $link, $fid)) {
			if (!preg_match('@https?://(?:\w+\.)*adrive\.com/public/dirview/(\w+)/(\d+)@i', $link, $fid)) html_error('Invalid Link?');
			html_error('TODO: Add Folder Support.');
		}

		$page = $this->GetPage($link);
		is_present($page, 'The file you are trying to access is no longer available publicly', 'File Private/Not Found');
		is_present($page, 'The public file you are trying to download is associated with a non-valid ADrive account.');
		$cookie = GetCookiesArr($page);

		if (!preg_match("@https?://(?:\w+\.)*adrive\.com/public/view/{$fid[1]}(?:/[^\s\"\'<>]*|\.html?|$)@i", $page, $dlLink)) {
			if (preg_match('@https?://(?:\w+\.)*adrive.com/public/{$fid[1]}.html@i', $page, $folder)) {
				html_error('TODO: Add (Sub)?Folder Support.');
			}
			html_error('Download Link Not Found.');
		}
		$this->RedirectDownload($dlLink[0], 'adrive_com', $cookie);
	}
}

// [15-5-15] Rewritten By Th3-822.

?>