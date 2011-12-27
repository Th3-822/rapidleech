<?php

if (!defined('RAPIDLEECH')) {
	require_once ("index.html");
	exit();
}

class glumbouploads_com extends DownloadClass {
	public function Download($link) {
		$page = $this->GetPage($link, 'lang=english');
		is_present($page, "The file you were looking for could not be found");

		$page2 = cut_str($page, '<Form method="POST" action=\'\'>', '</Form>'); //Cutting page

		$post = array();
		$post['op'] = cut_str($page2, 'name="op" value="', '"');
		$post['usr_login'] = '';
		$post['id'] = cut_str($page2, 'name="id" value="', '"');
		$post['fname'] = cut_str($page2, 'name="fname" value="', '"');
		$post['referer'] = '';
		$post['method_free'] = 'Slow+Download';

		$page = $this->GetPage($link, 'lang=english', $post);

		$page2 = cut_str($page, '<form name="F1" method="POST"', '</form>'); //Cutting page

		$post = array();
		$post['op'] = cut_str($page2, 'name="op" value="', '"');
		$post['id'] = cut_str($page2, 'name="id" value="', '"');
		$post['rand'] = cut_str($page2, 'name="rand" value="', '"');
		$post['referer'] = urlencode(cut_str($page2, 'name="referer" value="', '"'));
		$post['method_free'] = 'Slow+Download';
		$post['down_direct'] = 1;

		if (!preg_match('@var\s+cdnum\s*=\s*(\d+);@i', $page, $count)) html_error("Timer not found.");
		$this->CountDown($count[1]);

		$page = $this->GetPage($link, 'lang=english', $post);

		if (!preg_match('@href="(http://[^/|\"]+/d/[^\"]+)"@i', $page, $dlink)) html_error('Error: Download link not found.', 0);

		$url = parse_url($dlink[1]);
		$FileName = basename($url["path"]);
		$this->RedirectDownload($dlink[1], $FileName);
	}
}

// [15-12-2011]  Written by Th3-822.

?>