<?php

if (!defined('RAPIDLEECH')) {
	require_once('index.html');
	exit;
}

class videoraj_to extends DownloadClass {
	public function Download($link) {
		if (!preg_match('@(https?://(?:[\w\-]+\.)+[\w\-]+(?:\:\d+)?/)(?:file/|v/|embed\.php\?id=)(\w+)@i', $link, $vid)) html_error('Invalid Link.');
		$link = $GLOBALS['Referer'] = $vid[1] . 'v/' . $vid[2];

		$page = $this->GetPage($link);
		if (!preg_match('@<h4 class="title">\s*([^"<>]+)\s*</h4>@i', $page, $title) && !preg_match('@<title>\s*([^"<>|]+?)(?>\s+\|)@i', $page, $title))  {
			is_present($page, 'The video no longer exists', 'Video not found or it was deleted.');
			is_present($page, 'The file is being transfered', 'Video is temporarily unavailable.');
			html_error('Error: Video title not found.');
		}

		$embed = $this->GetPage("{$vid[1]}embed.php?id={$vid[2]}&autoplay=1");
		if (!preg_match('@domain\s*:\s*"([^"]+)"\s*,\s*key\s*:\s*"([^"]+)"@', $embed, $matches)) html_error('Error: Download link not found.');

		$url = $matches[1] . '/api/player.api.php?user=undefined&codes=1&file=' . $vid[2] . '&pass=undefined&key=' . urlencode($matches[2]);
		$page2 = $this->GetPage($url);
		is_present($page, ' is being transfered', 'Video is temporarily unavailable');

		if (!preg_match('@url=(http://[^&]+)@', urldecode($page2), $downl)) html_error('Error: Download link not found.');

		if (!preg_match('@\.(?:mp4|flv|webm|avi)$@i', basename($downl[1]), $ext)) $ext = array('.flv');
		$filename = preg_replace('@(?:\.(?:mp4|flv|mkv|webm|wmv|(m2)?ts|rm(vb)?|mpe?g?|vob|avi|[23]gp))+$@i', '', preg_replace('@[^ A-Za-z_\-\d\.,\(\)\[\]\{\}&\!\'\@\%\#]@u', '_', html_entity_decode(trim($title[1]), ENT_QUOTES, 'UTF-8')));
		$filename .= sprintf(' [Videoraj][%s]%s', $vid[2], $ext[0]);

		$this->RedirectDownload($downl[1], $filename, 0, 0, 0, $filename);
	}
}

//[28-12-2015]  Written by Th3-822.
//[22-1-2016]  Fixed link parse & case when video reports not found, but still downloadable. - Th3-822

?>