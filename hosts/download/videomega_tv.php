<?php

if (!defined('RAPIDLEECH')) {
	require_once('index.html');
	exit;
}

class videomega_tv extends DownloadClass {
	public function Download($link) {
		if (!preg_match('@(https?://(?:[\w\-]+\.)+[\w\-]+(?:\:\d+)?/)(?:index.php|view\.php)?\?ref=(\w+)@i', $link, $vid)) html_error('Invalid Link.');
		$GLOBALS['Referer'] = $vid[1] . '?ref=' . $vid[2];
		$link = $vid[1] . 'view.php?ref=' . $vid[2];

		$page = $this->GetPage($link);
		if (!preg_match('@<title>Videomega.tv -\s+([^"<>]+)\s*</title>@i', $page, $title)) html_error('Error: Video title not found. Video deleted?');

		if (!preg_match('@eval\s*\(\s*function\s*\(p,a,c,k,e,d\)\s*\{.+\}\s*\(\s*\'([^\r|\n]*)\'\s*,\s*(\d+)\s*,\s*(\d+)\s*,\s*\'([^\']+)\'\.split\([\'|\"](.)[\'|\"]\)(?:\s*,\s*0\s*,\s*\{\})?\)\)@', $page, $js)) html_error('Error: Embed Code Not Found.');
		$embed = $this->VideoMegaUnpacker($js[1], $js[2], $js[3], explode($js[5], $js[4]));
		if (!preg_match('@https?://(?:[\w\-]+\.)+[\w\-]+(?:\:\d+)?/v/[^\'\"\t<>\r\n\\\]+@i', $embed, $DL)) html_error('Error: Download link not found.');

		if (!preg_match('@\.(?:mp4|flv|webm|avi)$@i', basename($DL[0]), $ext)) $ext = array('.mp4');
		$filename = preg_replace('@(?:\.(?:mp4|flv|mkv|webm|wmv|(m2)?ts|rm(vb)?|mpe?g?|vob|avi|[23]gp))+$@i', '', preg_replace('@[^ A-Za-z_\-\d\.,\(\)\[\]\{\}&\!\'\@\%\#]@u', '_', html_entity_decode(trim($title[1]), ENT_QUOTES, 'UTF-8')));
		$filename .= sprintf(' [Videomega][%s]%s', $vid[2], $ext[0]);

		$this->RedirectDownload($DL[0], $filename, 0, 0, 0, $filename);
	}

	private function VideoMegaUnpacker($p,$a,$c,$k) {
		while ($c--) if($k[$c]) $p = preg_replace('@\b'.(($c = $c % $a) > 35 ? chr($c + 29) : base_convert($c, 10, 36)).'\b@', $k[$c], $p);
		return $p;
	}
}

//[20-2-2016]  Written by Th3-822.

?>