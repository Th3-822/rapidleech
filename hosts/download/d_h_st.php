<?php
if (!defined('RAPIDLEECH')) {
	require_once ('index.html');
	exit();
}

class d_h_st extends DownloadClass {

	public function Download($link) {

		$link = preg_replace('/\/((www\.)?dev-host\.org)\//', '/d-h.st/', $link);
		$page = $this->GetPage($link);
		is_present($page, 'The file you were looking for could not be found, sorry for any inconvenience.');
		if (!preg_match("/href='(https?:\/\/[^\r\n\s\t']+)'\">Download/", $page, $dl)) html_error('Error[FREE - Download link not found!]');
		$dlink = trim($dl[1]);
		$filename = basename(parse_url($dlink, PHP_URL_PATH));
		$this->RedirectDownload($dlink, $filename, 0, 0, $link);
	}

}

/*
 * Dev-host.org free download plugin by Ruud v.Tony 04-10-2011
 * Updated as they change their domain by Tony Fauzi Wihana/Ruud v.Tony 15/03/2013
 */
?>
