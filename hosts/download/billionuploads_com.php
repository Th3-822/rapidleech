<?php

if (!defined('RAPIDLEECH')) {
	require_once ('index.html');
	exit();
}

class billionuploads_com extends DownloadClass {
	public function Download($link) {
		$cookie = array('lang' => 'english');
		$page = $this->GetPage($link, $cookie);
		is_present($page, 'The file you were looking for could not be found');
		is_present($page, 'No such file with this filename', 'Error: Invalid filename, check your link and try again.');
		if (preg_match('@You have to wait (?:\d+ \w+,\s)?\d+ \w+ till next download@', $page, $err)) html_error('Error: '.$err[0]);

		$page2 = cut_str($page, '<form name="F1" method="POST"', '</form>'); //Cutting page
		$post = array();
		$post['op'] = cut_str($page2, 'name="op" value="', '"');
		$post['id'] = cut_str($page2, 'name="id" value="', '"');
		$post['rand'] = cut_str($page2, 'name="rand" value="', '"');
		$post['referer'] = '';
		$post['method_free'] = cut_str($page2, 'name="method_free" value="', '"');
		$post['method_premium'] = cut_str($page2, 'name="method_premium" value="', '"');
		$post['down_direct'] = '1';

		if (preg_match('@<span id="countdown_str"[^>]*>[^<>]+<span[^>]*>(\d+)</span>[^<>]+</span>@i', $page2, $count) && $count[1] > 0) $this->CountDown($count[1]);

		$page = $this->GetPage($link, $cookie, $post);

		is_present($page, '>Skipped countdown', 'Error: Skipped countdown?.');
		if (preg_match('@You have to wait (?:\d+ \w+,\s)?\d+ \w+ till next download@', $page, $err) || preg_match('@You can download files up to \d+ [KMG]?b only.@i', $page, $err)) html_error('Error: '.$err[0]);

		if (!preg_match('@https?://[^/\'\"\t<>\r\n\:]+(?:\:\d+)?/(?:(?:files)|(?:dl?)|(?:cgi\-bin/dl\.cgi))/[^\'\"\t<>\r\n]+@i', $page, $dlink)) html_error('Error: Download link not found.');

		$FileName = urldecode(basename(parse_url($dlink[0], PHP_URL_PATH)));
		$this->RedirectDownload($dlink[0], $FileName);
	}
}

// [29-11-2012]  Written by Th3-822. (XFS, XFS everywhere. D:)

?>