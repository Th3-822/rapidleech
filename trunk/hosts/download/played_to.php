<?php

if (!defined('RAPIDLEECH')) {
	require_once ('index.html');
	exit();
}

class played_to extends DownloadClass {
	private $page, $cookie;
	public function Download($link) {
		$this->cookie = array('lang' => 'english');

		if (empty($_POST['step']) || $_POST['step'] != '1') {
			$page = $this->GetPage($link, $this->cookie);
			is_present($page, '<b>File Not Found</b>', 'File Not Found');
			is_present($page, 'The file you were looking for could not be found');
			is_present($page, '>File was removed by user<', 'The file you were looking for could not be found');
			is_present($page, 'No such file with this filename', 'Error: Invalid filename, check your link and try again.');
		}

		if (preg_match('@You have to wait (?:\d+ \w+,\s)?\d+ \w+ till next download@', $page, $err)) html_error('Error: '.$err[0]);

		$page2 = cut_str($page, '<Form method="POST"', '</form>'); //Cutting page
		if (empty($page2)) html_error('Download form not found. File isn\'t available?');

		$post = array();
		$post['op'] = trim(cut_str($page2, 'name="op" value="', '"'));
		if (stripos($post['op'], 'download') !== 0) html_error('Error parsing download post data.');
		$post['id'] = trim(cut_str($page2, 'name="id" value="', '"'));
		$post['fname'] = urlencode(html_entity_decode(trim(cut_str($page2, 'name="fname" value="', '"'))));
		$post['referer'] = '';
		$post['hash'] = trim(cut_str($page2, 'name="hash" value="', '"'));
		$s_name = trim(cut_str($page2, 'input type="submit" name="', '"'));
		$post[$s_name] = trim(cut_str($page2, 'name="'.$s_name.'" value="', '"'));

		if (preg_match('@var\s+countdownNum\s*=\s*(\d+);@i', $page, $count) && $count[1] > 0) $this->CountDown($count[1]);

		$page = $this->GetPage($link, $this->cookie, $post);
		is_present($page, '>Skipped countdown', 'Error: Skipped countdown?.');
		is_present($page, '>Wrong captcha<', 'Error: File needs captcha, captcha was not found.');
		is_present($page, '>Expired session<', 'Error: Expired Download Session.');
		if (preg_match('@You can download files up to \d+ [KMG]b only.@i', $page, $err)) html_error('Error: '.$err[0]);
		if (!preg_match('@file[\'\"]?\s*:\s*[\'\"](https?://[^/\r\n]+/[^\'\"\t<>\r\n]+)[\'\"]@i', $page, $dlink)) html_error('Error: Download Link not found');

		$FileName = urldecode($post['fname']);
		$this->RedirectDownload($dlink[1], $FileName, 0, 0, 0, $FileName);
	}
}

// [06-10-2013]  Written by Th3-822. (XFS, XFS everywhere. D:)

?>