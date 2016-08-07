<?php

if (!defined('RAPIDLEECH')) {
	require_once('index.html');
	exit();
}

if (!file_exists(HOST_DIR . 'download/GenericXFS_DL.php')) html_error('Cannot load "'.htmlentities(HOST_DIR).'download/GenericXFS_DL.php" (File doesn\'t exists)');
require_once(HOST_DIR . 'download/GenericXFS_DL.php');

class thevideos_tv extends GenericXFS_DL {
	public $pluginVer = 14;
	public function Download($link) {
		$this->wwwDomain = false; // Switch to true if filehost forces it's domain with www.
		$this->cname = 'xfss'; // Session cookie name
		$this->sslLogin = false; // Force https on login.
		$this->embedDL = false; // Try to unpack player's js for finding download link. (Only hosts with video player)
		$this->unescaper = false; // Enable JS unescape decoder.

		// Custom download regexp to match last url from format list.
		$this->DLregexp = '@https?://(?:[\w\-]+\.)+[\w\-]+(?:\:\d+)?/\w{60}/(?:v(?:id(?:eo)?)?|\w{12})?\.(?:flv|mp4)(?="\s*,[^{}]+}\s*])@i';

		$this->Start($link);
	}

	// Extra CountDown Time
	public function CountDown($countDown) {
		return parent::CountDown($countDown + 7);
	}
}

// Written by Th3-822.

?>