<?php

if (!defined('RAPIDLEECH')) {
	require_once('index.html');
	exit();
}

class dailymotion_com extends DownloadClass {
	private $cookie, $xid, $title, $streams,
	$formats = array('1080' => 'stream_h264_hd1080_url', '720' => 'stream_h264_hd_url',
		'480' => 'stream_h264_hq_url', '384' => 'stream_h264_url', '240' => 'stream_h264_ld_url');

	public function Download($link) {
		$this->cookie = !empty($_POST['step']) && !empty($_POST['cookie']) ? StrToCookies(decrypt(urldecode($_POST['cookie']))) : array('ff' => 'off');
		if (!preg_match('@/video/(x[0-9a-zA-Z]+)@i', $link, $xid)) html_error('Video ID not found.');
		$this->xid = $xid[1];
		$this->link = 'http://www.dailymotion.com/video/'.$this->xid;

		if (empty($_POST['step'])) {
			$page = $this->GetPage($this->link, $this->cookie);
			$this->cookie = GetCookiesArr($page, $this->cookie);

			$status = (int)substr($page, 9, 3);
			switch ($status) {
				case 200: break;
				case 403: html_error('This video is forbidden to download!');break;
				case 404: html_error('404 Not Found');break;
				case 410: html_error('This video has been removed by the user.');break;
				default: html_error("Unexpected response: $status");break;
			}
		}

		$this->getVideoInfo();

		if (empty($_POST['dlstream']) && !isset($_GET['audl'])) return $this->QSelector();
		elseif (empty($_POST['dlstream']) || !empty($this->streams[$_POST['dlstream']])) {
			$key = (empty($_POST['dlstream']) ? key($this->streams) : $_POST['dlstream']);
			$DL = $this->streams[$key];
		} else html_error('Selected video stream was not found.');

		$filename = preg_replace('@[^ A-Za-z_\-\d\.,\(\)\[\]\{\}&\!\'\@\%\#]@u', '_', trim($this->title), ENT_QUOTES);
		$filename .= " [DM-{$key}p][{$this->xid}].mp4";

		$page = $this->GetPage($DL, $this->cookie);
		if (!preg_match('@https?://[^/\s]+/video/[^\s<>\'\"]+@i', $page, $DL)) html_error('Download Link not Found.');

		$this->RedirectDownload($DL[0], $filename, $this->cookie, 0, 0, $filename);
	}

	private function Get_Reply($content) {
		if (!function_exists('json_decode')) html_error('Error: Please enable JSON in php.');
		if (($pos = strpos($content, "\r\n\r\n")) > 0) $content = substr($content, $pos + 4);
		$cb_pos = strpos($content, '{');
		$sb_pos = strpos($content, '[');
		if ($cb_pos === false && $sb_pos === false) html_error('Json start braces not found.');
		$sb = ($cb_pos === false || $sb_pos < $cb_pos) ? true : false;
		$content = substr($content, strpos($content, ($sb ? '[' : '{')));$content = substr($content, 0, strrpos($content, ($sb ? ']' : '}')) + 1);
		if (empty($content)) html_error('No json content.');
		$rply = json_decode($content, true);
		if (!$rply || count($rply) == 0) html_error('Error reading json.');
		return $rply;
	}

	private function getVideoInfo() {
		$page = $this->GetPage("http://www.dailymotion.com/json/video/{$this->xid}?fields=title%2C".implode('%2C', $this->formats), $this->cookie);
		$json = $this->Get_Reply($page);

		if (empty($json['title'])) html_error('Video Title not Found.');
		$this->title = $json['title'];

		$this->streams = array();
		foreach ($this->formats as $key => $fmt) if (!empty($json[$fmt])) $this->streams[$key] = $json[$fmt];
		if (empty($this->streams)) html_error('Video Streams not Found.');
	}

	private function QSelector() {
		echo "\n<br /><br /><h3 style='text-align: center;'>Video Quality Selector</h4>";
		echo "\n<center><form name='T8_QS' action='{$GLOBALS['PHP_SELF']}' method='POST'>\n";
		echo "<select name='dlstream' id='QS_fmt'>\n";
		foreach ($this->streams as $fmt => $url) echo "<option value='$fmt'>{$fmt}p</option>\n";
		echo "</select>\n";
		if (count($this->cookie) > 0) $this->cookie = encrypt(CookiesToStr($this->cookie));
		$data = $this->DefaultParamArr($this->link, $this->cookie);
		$data['step'] = 1;
		foreach ($data as $n => $v) echo("<input type='hidden' name='$n' id='QS_$n' value='$v' />\n");
		echo "<input type='submit' name='Th3-822' value='".lang(209)."' />\n";
		echo "</form></center>\n</body>\n</html>";
		exit;
	}
}

//[07-3-2014] Written by Th3-822.

?>