<?php

if (!defined('RAPIDLEECH')) {
	require_once('index.html');
	exit;
}

class aparat_com extends DownloadClass {
	public $link;
	
	public function Download($link) {
		$this->link = $link;
		
		if (!preg_match('@aparat\.com/v/(\w+)@i', $link, $vid)) html_error('Video ID not found.');
		$vid = $vid[1];

		$page = $this->GetPage("https://www.aparat.com/v/$vid");
		is_present($page, "ویدیو مشابهی یافت نشد.", 'Video not found or it was deleted.');
		if (!preg_match('@<title>(?>(.*?)</title>)@is', $page, $title)) html_error('Error: Video title not found.');
		
		$qualities = $this->Qualities($page);
		
		if (!isset($_GET['submit']) && !isset($_GET['audl'])) {
			echo $this->QSelector($qualities);
			return;
		}
		
		if (isset($_GET['audl'])) {
			reset($qualities);
			$_GET['quality'] = key($qualities);
		}
		
		$quality = $qualities[$_GET['quality']]['quality'];
		$video = $qualities[$_GET['quality']]['video'];
		$filename = html_entity_decode(trim($title[1]), ENT_QUOTES, 'UTF-8');
		if (isset($_GET['clean_name'])) {
			$filename = preg_replace('@[^ A-Za-z_\-\d\.,\(\)\[\]\{\}&\!\'\@\%\#]@u', '_', $filename);
		}
		$filename = preg_replace('@(?:\.(?:mp4|flv|mkv|webm|wmv|(m2)?ts|rm(vb)?|mpe?g?|vob|avi|[23]gp))+$@i', '', $filename);
		$filename .= sprintf(' [Aparat%s][%s].mp4', (empty($quality) ? '' : '-' . $quality), $vid);

		$this->RedirectDownload($video, $filename, 0, 0, 0, $filename);
	}
	
	public function Qualities($page) {
		if (!preg_match_all('@<a.*?href="(https?://(?:[\w-]+\.)*aparat\.com/aparat-video/\w+(?:-(\d+p))?__\w+\.mp4)".*?><span.*?>(.*?)<@im', $page, $matches, PREG_SET_ORDER, 0)) {
			html_error('Download link not found.');
		}
		$return = array();
		foreach ($matches as $match) {
			$return[] = array(
				'video' => $match[1],
				'quality' => $match[2],
				'title' => $match[3],
			);
		}
		if (!$return) {
			html_error('Download link not found.');
		}
		krsort($return);
		return $return;
	}
	
	public function QSelector($qualities) {
		if (!is_array($qualities)) {
			html_error('Quality not found.');
		}
		$str = "";
		$str .= "\n<center><form action='{$_SERVER['SCRIPT_NAME']}' method='POST'>\n";
		$str .= '<label><input type="checkbox" name="clean_name" value="1" /><small>&nbsp;Remove non-supported characters from filename</small></label><br />';
		$str .= "<select name='quality'>\n";
		foreach ($qualities as $key => $quality) {
			$str .= "<option value='{$key}'>Quality: [" . ($quality['quality'] ? $quality['quality'] : 'Normal') . "]</option>\n";
		}
		$str .= "</select>\n";
		$data = $this->DefaultParamArr($this->link);
		foreach ($data as $n => $v) {
			$str .= "<input type='hidden' name='$n' id='QS_$n' value='$v' />\n";
		}
		$str .= "<input type='submit' name='submit' value='".lang(209)."' />\n";
		$str .= "</form></center>\n</body>\n</html>";
		return $str;
	}
}

//[23-12-2015]  Written by Th3-822.
//[27-12-2015]  Fixed Regexp. - Th3-822
//[02-11-2018]  Fixed video URL pattern - Nabi K.A.Z. <www.nabi.ir>
//[02-11-2018]  Added aparat quality selector - Nabi K.A.Z. <www.nabi.ir>
//[02-11-2018]  Fixed bug in multi downloader - Nabi K.A.Z. <www.nabi.ir>

?>
