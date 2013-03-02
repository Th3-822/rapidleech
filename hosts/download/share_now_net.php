<?php
if (!defined('RAPIDLEECH')) {
	require_once("index.html");
	exit;
}

class share_now_net extends DownloadClass {

	public function Download($link) {
		$page = $this->GetPage($link);
		$cookie = GetCookies($page);
		is_notpresent($cookie, 'PHPSESSID', 'File not found!');
		if (!preg_match('/(\d+)<\/span> Seconds/', $page, $w)) html_error('Error[Timer not found!]');
		$this->CountDown($w[1]);
		$page = $this->GetPage($link, $cookie, array('Submit.x' => rand(0,51), 'Submit.y' => rand(0,13)), $link);
		if (!preg_match('/location: (http:\/\/[^\r\n]+)/i', $page, $dl)) html_error('Error[Download link not found!]');
		$dlink = trim($dl[1]);
		$this->RedirectDownload($dlink, "sharenow", $cookie, $link);
	}
}

/*
 * Written by Tony Fauzi Wihana/Ruud v.Tony 13/02/2013
 */
?>