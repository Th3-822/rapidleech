<?php
if (!defined('RAPIDLEECH')) {
	require_once 'index.html';
	exit;
}

class trainbit_com extends DownloadClass {
	
	public function Download($link) {
		
		$page = $this->GetPage($link);
		is_present($page, 'Desired file is removed.');
		is_present($page, 'Desired folder owner account is disabled.');
		if (!preg_match('/(\d+)<\/span> sec/', $page, $w)) html_error('Error[Timer not found!]');
		$this->CountDown($w[1]);
		if (!preg_match('/href="(https?:\/\/[^\r\n"]+)" id="b_download"/', $page, $dl)) html_error('Error[Download link - FREE not found!]');
		$dlink = trim($dl[1]);
		$filename = basename(parse_url($dlink, PHP_URL_PATH));
		$this->RedirectDownload($dlink, $filename, 0, 0, $link);
	}
}

/*
 * Written by Tony Fauzi Wihana/Ruud v.Tony 25-01-2013
 */
?>
