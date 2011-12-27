<?php

if (!defined('RAPIDLEECH')) {
	require_once ("index.html");
	exit();
}

class hostingbulk_com extends DownloadClass {
	public function Download($link) {
		if ($_REQUEST['hb_free'] == 'yes') {
			$post = array();
			$post['op'] = $_REQUEST['hb_op'];
			$post['id'] = $_REQUEST['hb_id'];
			$post['rand'] = $_REQUEST['hb_rand'];
			$post['referer'] = '';
			$post['method_free'] = '';
			$post['down_direct'] = 1;

			$page = $this->GetPage($link, 'lang=english', $post);
			if (!preg_match('@href="(http://[^/|\"]+/d/[^\"]+)"@i', $page, $dlink)) html_error('Error: Download link not found 2.');

			$url = parse_url($dlink[1]);
			$FileName = basename($url["path"]);
			return $this->RedirectDownload($dlink[1], $FileName);
		}

		$page = $this->GetPage($link, 'lang=english');
		is_present($page, "The file you were looking for could not be found");

		$page2 = cut_str($page, '<form name="F1" method="POST"', '</form>'); //Cutting page

		$post = array();
		$post['op'] = cut_str($page2, 'name="op" value="', '"');
		$post['id'] = cut_str($page2, 'name="id" value="', '"');
		$post['rand'] = cut_str($page2, 'name="rand" value="', '"');
		$post['referer'] = '';
		$post['method_free'] = '';
		$post['down_direct'] = 1;

		$count = array(1=>0); // Testing... With no countdown has worked (Saved 180 seconds)... If it stop working, uncomment next line
		// if (!preg_match('@<span id="countdown_str">Wait <span[^>]*>(\d+)</span> seconds?</span>@i', $page2, $count)) html_error("Timer not found.");

		if ($count[1] <= 120) $this->CountDown($count[1]);
		else {
			$data = $this->DefaultParamArr($link);
			$data['hb_op'] = $post['op'];
			$data['hb_id'] = $post['id'];
			$data['hb_rand'] = $post['rand'];
			$data['hb_free'] = 'yes';
			return $this->JSCountdown($count[1], $data);
		}

		$page = $this->GetPage($link, 'lang=english', $post);
		if (!preg_match('@href="(http://[^/|\"]+/d/[^\"]+)"@i', $page, $dlink)) html_error('Error: Download link not found.');

		$url = parse_url($dlink[1]);
		$FileName = basename($url["path"]);
		$this->RedirectDownload($dlink[1], $FileName);
	}
}

// [15-12-2011]  Written by Th3-822.

?>