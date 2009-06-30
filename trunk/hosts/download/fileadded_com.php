<?php
if (!defined('RAPIDLEECH')) {
	require_once("index.html");
	exit;
}

class fileadded_com extends DownloadClass {
	public function Download($link) {
		$page = $this->GetPage($link);
		is_present($page, "File Not Found! REASON: This file has been deleted either by the Uploader or Admin.");
		$FileAddr = urldecode(cut_str($page, "unescape('", "'"));
		if (!$FileAddr) {
	       html_error("Error getting download link", 0);
		}
        $Url = parse_url($FileAddr);
        $FileName = basename($Url["path"]);
		$FileName = ! $FileName ? basename ( $Url ["path"] ) : $FileName;
		$this->RedirectDownload($FileAddr, $FileName);
	}
}
?>