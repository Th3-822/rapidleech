<?php
if (!defined('RAPIDLEECH')) {
	require_once('index.html');
	exit;
}

class tune_pk extends DownloadClass {
	public function Download($link) {
		if (!preg_match('@://(?:[^/]+\.)?tune\.pk/video/(\d+)@i', $link, $vid)) html_error('Video ID not found. Check url.');
		$page = $this->GetPage('http://tune.pk/video/'.$vid[1].'/');
		is_present($page, 'Video does not exist');
		is_present($page, '404 Page not found');

		if (!preg_match('@var \s*video_title\s*=\s*\'([^\']+)\';@i', $page, $title)) html_error('Video title not found.');
		$title = str_replace(str_split('<>:"/\\|?*\'@#+~{}[]^'), '_', html_entity_decode(trim($title[1]), ENT_QUOTES));

		if (stripos($page, 'watch_video_hd_button') !== false && preg_match('@var \s*hq_video_file\s*=\s*\'(http://[^\']+)\'@i', $page, $dl)) $title .= '_HQ';
		elseif (!preg_match('@var \s*normal_video_file\s*=\s*\'(http://[^\']+)\'@i', $page, $dl)) html_error('Download link not found.');
		$dllink = $dl[1];

		$ext = strrchr($dllink, '.');if (empty($ext)) $ext = '.flv';
		$fname = $title . $ext;
		$this->RedirectDownload($dllink, $fname);
	}
}

//[12-12-2012] Written by Th3-822.

?>