<?php

if (!defined('RAPIDLEECH')) {
	require_once('index.html');
	exit();
}

if (!file_exists(HOST_DIR . 'download/GenericXFS_DL.php')) html_error('Cannot load "'.htmlentities(HOST_DIR).'download/GenericXFS_DL.php" (File doesn\'t exists)');
require_once(HOST_DIR . 'download/GenericXFS_DL.php');

class mightyupload_com extends GenericXFS_DL {
	public $pluginVer = 6;
	public function Download($link) {
		$this->wwwDomain = false; // Switch to true if filehost forces it's domain with www.
		$this->cname = 'xfss'; // Session cookie name
		$this->sslLogin = false; // Force https on login.
		$this->embedDL = false; // Try to unpack player's js for finding download link. (Only hosts with video player)
		$this->unescaper = false; // Enable JS unescape decoder.

		$this->Start($link);
	}

	// Remove player links
	protected function testDL($name = '') {
		if (strpos($this->page, "jwplayer('container').setup({") !== false) {
			// Removing the http will avoid to be catched by the download regexp and download with a wrong name.
			$this->page = str_replace("file: 'http", "file: 'xxxx", $this->page);
		}
		return parent::testDL($name);
	}
}

// Written by Th3-822.

?>