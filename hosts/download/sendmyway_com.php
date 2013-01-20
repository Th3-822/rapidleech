<?php
if (!defined('RAPIDLEECH')) {
    require_once ("index.html");
    exit();
}

class sendmyway_com extends DownloadClass {

    public function Download($link) {
		
		$page = $this->GetPage($link, 'lang=english');
		is_present($page, '<b>File Not Found</b>');
		$form = cut_str($page, '<form name="F1" id="download_form"', '</form>');
		if (!preg_match_all('/<input type="hidden" name="([^"]+)" value="([^"]+)?">/', $form, $match)) html_error('Error[Post Data - FREE not found!]');
		$match = array_combine($match[1], $match[2]);
		$post = array();
		foreach ($match as $k => $v) {
			$post[$k] = $v;
		}
		$page = $this->GetPage($link, 'lang=english', $post, $link);
		if (!preg_match('/<a href="(https?:\/\/[^\r\n]+)" id="download_link">/', $page, $dl)) html_error ('Error[Download Link - FREE not found!]');
		$dlink = trim($dl[1]);
		$filename = basename(parse_url($dlink, PHP_URL_PATH));
		$this->RedirectDownload($dlink, $filename, 'lang=english', 0, $link);
		exit;
    }

}

/*
 * sendmyway.com free download plugin by Ruud v.Tony 08-08-2011
 * fixed for sendmyway captcha layout by Ruud v.Tony 02-02-2012
 * fixed by Tony Fauzi Wihana/Ruud v.Tony 16-01-2013
 */
?>
