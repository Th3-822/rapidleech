<?php

if (!defined('RAPIDLEECH')) {
	require_once('index.html');
	exit;
}

class ok_ru extends DownloadClass {
	private $xid, $streams, $formats = array('full' => 1080, 'hd' => 720, 'sd' => 480, 'low' => 360, 'lowest' => 240, 'mobile' => 144);
	public function Download($link) {
		if (!preg_match('@/video(?:embed)?/(\d+)@i', $link, $xid)) html_error('Video ID not found.');
		$this->xid = $xid[1];
		$this->link = 'http://ok.ru/video/' . $this->xid;

		$page = $this->GetPage($this->link);
		is_present($page, "Video has not been found", 'Video not found or it was deleted.');

		$json = cut_str($page, 'data-options="', '"');
		if (empty($json)) html_error('Video Data not Found.');
		$json = $this->json2array(html_entity_decode(str_replace('\\\\u0026', '&amp;', $json), ENT_QUOTES, 'UTF-8'), 'Error Parsing Video Data.');
		if (!empty($json['flashvars']['metadata']) && !is_array($json['flashvars']['metadata'])) $json['flashvars']['metadata'] = $this->json2array($json['flashvars']['metadata'], 'Error Parsing Video Metadata.');
		else if (empty($json['flashvars']['metadata'])) html_error('Video Metadata Not Found');
		$json = $json['flashvars']['metadata'];

		if (empty($json['movie']['title'])) html_error('Video Title Not Found');

		$this->streams = array();
		foreach ($json['videos'] as $video) {
			if (array_key_exists($video['name'], $this->formats) && !empty($video['url'])) $this->streams[$video['name']] = $video['url'];
		}
		if (empty($this->streams)) html_error('Non Aceptable Video Streams Found.');

		// I did see a couple of videos that links to youtube, i will add code to send them to the youtube plugin.
		if (count($json['videos']) == 1 && empty($json['movie']['collageInfo']))
		{
			// Looks Like A Video for a External Site.
			$extLink = $this->streams[key($this->streams)];
			if (preg_match('@youtube\.com/(?:v|embed)/([\w\-\.]{11})@i', $extLink, $YT)) {
				// It's a  YT link, send it to the youtube plugin.
				insert_location($this->DefaultParamArr('https://www.youtube.com/watch?v=' . $YT[1]));
				exit();
			}
			html_error('This video doesn\'t seems to be hosted on ok.ru, link: ' . htmlspecialchars($extLink));
		}

		if (empty($_POST['dlstream']) && !isset($_GET['audl'])) return $this->QSelector();
		elseif (empty($_POST['dlstream']) || !empty($this->streams[$_POST['dlstream']])) {
			$key = (empty($_POST['dlstream']) ? key($this->streams) : $_POST['dlstream']);
			$DL = $this->streams[$key];
		} else html_error('Selected video stream was not found.');

		$filename = preg_replace('@[^ A-Za-z_\-\d\.,\(\)\[\]\{\}&\!\'\@\%\#]@u', '_', html_entity_decode(trim($json['movie']['title']), ENT_QUOTES, 'UTF-8'));
		$filename .= " [OK-{$this->formats[$key]}p][{$this->xid}].mp4";

		$this->RedirectDownload($DL, $filename, 0, 0, 0, $filename);
	}

	private function QSelector() {
		echo "\n<br /><br /><h3 style='text-align: center;'>Video Quality Selector</h4>";
		echo "\n<center><form name='T8_QS' action='{$_SERVER['SCRIPT_NAME']}' method='POST'>\n";
		echo "<select name='dlstream' id='QS_fmt'>\n";
		foreach ($this->formats as $fmt => $res) if (array_key_exists($fmt, $this->streams)) echo "<option value='$fmt'>" . ucfirst($fmt) . " ({$res}p)</option>\n";
		echo "</select>\n";
		$data = $this->DefaultParamArr($this->link);
		foreach ($data as $n => $v) echo("<input type='hidden' name='$n' id='QS_$n' value='$v' />\n");
		echo "<input type='submit' name='Th3-822' value='" . lang(209) . "' />\n";
		echo "</form></center>\n</body>\n</html>";
		exit;
	}
}

//[18-11-2015]  Written by Th3-822.

?>