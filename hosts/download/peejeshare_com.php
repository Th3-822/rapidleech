<?php
if (!defined('RAPIDLEECH')) {
	require 'index.html';
	exit;
}

class peejeshare_com extends DownloadClass {
	
	public function Download($link) {
		$page = $this->GetPage($link);
		is_present($page, 'All download slots for this file are currently filled.<br />Please try again momentarily.<br />');
		$cookie = GetCookies($page);
		$form = cut_str($page, '<form method="POST">', '</form>');
		if (!preg_match_all('/<input type="(hidden|submit)" name="([^"]+)" value="([^"]+)?"/', $form, $match)) html_error('Error[Post Data FREE not found!]');
		$match = array_combine($match[2], $match[3]);
		$post = array();
		foreach ($match as $key => $value) {
			$post[$key] = $value;
		}
		$page = $this->GetPage($link, $cookie, $post, $link);
		if (!preg_match('/https?:\/\/ww\d+\.peejeshare\.com\/dl\/[^\r\n"]+/', $page, $dl)) html_error ('Error[Download Link FREE not found!]');
		$dlink = trim($dl[0]);
		$filename = basename(parse_url($dlink, PHP_URL_PATH));
		$this->RedirectDownload($dlink, $filename, $cookie);
	}
}

/*
 * Written by Ruud v.Tony 26-06-2012
 */
?>
