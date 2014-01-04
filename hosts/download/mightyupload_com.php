<?php

if (!defined('RAPIDLEECH')) {
	require_once('index.html');
	exit();
}

class mightyupload_com extends DownloadClass {
	private $page, $cookie;
	public function Download($link) {
		$this->cookie = array('lang' => 'english');
		$this->page = $this->GetPage($link, $this->cookie);
		is_present($this->page, '<b>File Not Found</b>', 'File Not Found');
		is_present($this->page, 'The file you were looking for could not be found');
		is_present($this->page, 'No such file with this filename', 'Error: Invalid filename, check your link and try again.');

		if (preg_match('@You have to wait (?:\d+ \w+,\s)?\d+ \w+ till next download@', $this->page, $err)) html_error('Error: '.$err[0]);

		$page2 = cut_str($this->page, '<form name="F1" method="POST"', '</form>'); //Cutting page
		if (empty($page2)) html_error('Download form not found. File isn\'t available?');

		$post = array();
		$post['op'] = trim(cut_str($page2, 'name="op" value="', '"'));
		if (stripos($post['op'], 'download') !== 0) html_error('Error parsing download post data.');
		$post['id'] = trim(cut_str($page2, 'name="id" value="', '"'));
		$post['rand'] = trim(cut_str($page2, 'name="rand" value="', '"'));
		$post['method_free'] = urlencode(html_entity_decode(cut_str($page2, 'name="method_free" value="', '"')));
		$post['plugins_are_not_allowed'] = cut_str($page2, 'name="plugins_are_not_allowed" value="', '"');

		if (preg_match('@<span id="countdown_str">[^<>]+<span[^>]*>(\d+)</span>[^<>]+</span>@i', $page2, $count) && $count[1] > 0) $this->CountDown($count[1]);

		$page = $this->GetPage($link, $this->cookie, $post);
		is_present($page, '>Skipped countdown', 'Error: Skipped countdown?.');
		is_present($page, '>Wrong captcha<', 'Error: File needs captcha, captcha was not found.');
		is_present($page, '>Expired session<', 'Error: Expired Download Session.');
		if (preg_match('@You can download files up to \d+ [KMG]b only.@i', $page, $err)) html_error('Error: '.$err[0]);
		if (!preg_match('@(?<=[\'\"\t\s>\r\n])https?://[^/\r\n]+/(?:(?:files)|(?:dl?))/[^\'\"\t<>\r\n]+@i', $page, $dlink)) html_error('Error: Download link not found.');

		$this->RedirectDownload($dlink[0], urldecode(basename(parse_url($dlink[0], PHP_URL_PATH))));
	}
}

// [03-9-2013]  Written by Th3-822. (XFS, XFS everywhere. D:)

?>