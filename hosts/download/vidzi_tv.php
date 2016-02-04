<?php

if (!defined('RAPIDLEECH')) {
	require_once('index.html');
	exit();
}

if (!file_exists(HOST_DIR . 'download/GenericXFS_DL.php')) html_error('Cannot load "'.htmlentities(HOST_DIR).'download/GenericXFS_DL.php" (File doesn\'t exists)');
require_once(HOST_DIR . 'download/GenericXFS_DL.php');

class vidzi_tv extends GenericXFS_DL {
	public $pluginVer = 10;
	public function Download($link) {
		$this->wwwDomain = false; // Switch to true if filehost forces it's domain with www.
		$this->cname = 'xfss'; // Session cookie name
		$this->sslLogin = false; // Force https on login.
		$this->embedDL = true; // Try to unpack player's js for finding download link. (Only hosts with video player)
		$this->unescaper = false; // Enable JS unescape decoder.

		// Custom Download Regexp
		$this->DLregexp = '@https?://(?:[\w\-]+\.)+[\w\-]+(?:\:\d+)?/\w{60}/[^\t\r\n<>\'\"\?\&]+@i';

		$this->Start($link);
	}

	protected function getVideoTitle() {
		if (preg_match('@<h2 class="video-title">\s*([^"<>]+)\s*</h2>@i', $this->page, $title)) {
			return html_entity_decode(trim($title[1]), ENT_QUOTES, 'UTF-8');
		}
		return false;
	}
}

// Written by Th3-822.

?>