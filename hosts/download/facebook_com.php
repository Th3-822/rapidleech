<?php
if (!defined('RAPIDLEECH')) {
	require_once('index.html');
	exit;
}

class facebook_com extends DownloadClass {
	public function Download($link) {
		if (!preg_match('@://(?:[^/]+\.)?facebook\.com/(?:(?:video/video|photo)\.php\?v=|[\w\-]+/videos/)(\d+)@i', $link, $vid)) html_error('Video ID not found.');
		$page = $this->GetPage('https://www.facebook.com/video/video.php?v='.$vid[1]);
		is_present($page, '>This content is currently unavailable<', 'Video unavailable or deleted.');

		if (!preg_match('@"video_title"\s?,\s?"([^"]+)"@i', $page, $title) && !preg_match('@<title id="pageTitle">[\s\t\r\n]*([^<>]+)[\s\t\r\n]*\|[\s\t\r\n]*Facebook[\s\t\r\n]*</title>@i', $page, $title)) {
			html_error('Video title not found.');
		}
		$title = preg_replace('@[^ A-Za-z_\-\d\.,\(\)\[\]\{\}&\!\'\@\%\#]@u', '_', urldecode(html_entity_decode(preg_replace('@\\\u(\d{4})@i', '&#$1;', trim($title[1])), ENT_QUOTES, 'UTF-8')));

		if (!preg_match('@"video_src"\s?,\s?"(http[^"]+)"@i', $page, $dl)) {
			if (!preg_match('@\[\s?\"params\"\\s?,\s?\"([^\"\]]+)@i', $page, $params)) html_error('Download link not found.');
			$params = json_decode(urldecode(str_replace('\u0025', '%', $params[1])), true);
			$video = reset($params['video_data']);
			$dllink = !empty($video['hd_src']) ? $video['hd_src'] : $video['sd_src'];
		} else $dllink = urldecode(str_replace('\u0025', '%', $dl[1]));

		$ext = strrchr(basename(parse_url($dllink, PHP_URL_PATH)), '.');if (empty($ext)) $ext = '.flv';
		$fname = "$title [FB-" . (!empty($video['hd_src']) ? 'HD' : 'SD') . "][{$vid[1]}]$ext";
		// Remove rate limit
		$dllink = preg_replace('@&rl=\d+@i', '', $dllink);
		$this->RedirectDownload($dllink, $fname);
	}
}

//[25-10-2012] Written by Th3-822. (I won't reply "media-related" plugin requests by Mail/IM/PM, only at forum.)
//[20-9-2016] Small Fixes. - Th3-822

?>