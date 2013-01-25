<?php
if (!defined('RAPIDLEECH')) {
	require_once ("index.html");
	exit();
}

class d1fichier_com extends DownloadClass {

	public function Download($link) {
		$page = $this->GetPage($link);
		is_present($page, "The requested file could not be found ");
		is_present($page, "Le fichier demandé n'existe pas.", "The requested file could not be found "); //Francais
		is_present($page, "You already downloading some files", "You already downloading some files.Please wait a few seconds before downloading new ones");
		$Cookies = GetCookies($page);
		$post = array();
		$post['a'] = '1';
		$post['submit'] = 'Download+the+file';
		$page = $this->GetPage($link, $Cookies, $post, $link);
		if (!preg_match("/Location: (https?:\/\/[^\r\n]+)/i", $page, $dl)) html_error("Error: Download link not found!");
		$dlink = trim($dl[1]);
		$filename = basename(parse_url($dlink, PHP_URL_PATH));
		$this->RedirectDownload($dlink, $filename, $Cookies, 0, $link);
		exit;
	}

}

//by vdhdevil
//Updated by Ruud v.Tony for another 1fichier domain without adding new plugin 13-01-2012
//Updated by Tony Fauzi Wihana/Ruud v.Tony 11/01/2013
?>
