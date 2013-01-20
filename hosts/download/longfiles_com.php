<?php
if (!defined('RAPIDLEECH')) {
	require_once('index.html');
	exit;
}

class longfiles_com extends DownloadClass {
	
	public function Download($link) {
		
		$cookie = 'lang=english';
		$page = $this->GetPage($link, $cookie);
		is_present($page, 'The file you were looking for could not be found, sorry for any inconvenience.');
		$form = cut_str($page, '<Form method="POST" action=\'\'>', '</Form>');
		if (!preg_match_all('/<input type="(hidden|submit)" name="([^"]+)" value="([^"]+)?">/', $form, $match)) html_error('Error[Post Form 1 Not Found!]');
		$match = array_combine($match[2], $match[3]);
		$post = array();
		foreach ($match as $key => $value) {
			$post[$key] = $value;
		}
		$page = $this->GetPage($link, $cookie, $post, $link);
		unset($post);
		is_present($page, cut_str($page, '<div class="err">', '<br>'));
		$form = cut_str($page, '<h3>Download File</h3>', '</Form>');
		if (!preg_match('/(\d+)<\/span> seconds/', $form, $w)) html_error('Error[Timer Not Found!]');
		$this->CountDown($w[1]);
		if (!preg_match_all('/<input type="hidden" name="([^"]+)" value="([^"]+)?">/', $form, $match)) html_error('Error[Post Form 2 Not Found!]');
		$match = array_combine($match[1], $match[2]);
		$post = array();
		foreach ($match as $key => $value) {
			$post[$key] = $value;
		}
		$page = $this->GetPage($link, $cookie, $post, $link);
		$dlink = cut_str($page, "Location: ", "\n");
		if (empty($dlink)) html_error('Error[Download Link Not Found!]');
		$filename = basename(parse_url($dlink, PHP_URL_PATH));
		$this->RedirectDownload($dlink, $filename, $cookie, 0, $link, $filename);
		exit;
	}
}

/*
 * Written by Ruud v.Tony 10-10-2012
 */
?>
