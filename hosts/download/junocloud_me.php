<?php

if (!defined('RAPIDLEECH')) {
	require_once('index.html');
	exit();
}

if (!file_exists(HOST_DIR . 'download/GenericXFS_DL.php')) html_error('Cannot load "'.htmlentities(HOST_DIR).'download/GenericXFS_DL.php" (File doesn\'t exists)');
require_once(HOST_DIR . 'download/GenericXFS_DL.php');

class junocloud_me extends GenericXFS_DL {
	public $pluginVer = 10;
	public function Download($link) {
		$this->wwwDomain = false; // Switch to true if filehost forces it's domain with www.
		$this->cname = 'xfss'; // Session cookie name
		$this->sslLogin = false; // Force https on login.
		$this->embedDL = false; // Try to unpack player's js for finding download link. (Only hosts with video player)
		$this->unescaper = false; // Enable JS unescape decoder.

		// Fix link and check for the dlX. redirect
		$link = preg_replace('@(?<=://)dl\d*\.(junocloud\.me)(?=/)@i', '$1', $link);
		$test = explode("\r\n\r\n", $this->GetPage($link), 2);
		if (preg_match('@\nLocation: (https?://dl\d*\.junocloud\.me/\w{12}(?=(?:[/\.]|(?:\.html?))?))@i', $test[0], $redir)) $link = $redir[1];

		$this->Start($link);
	}
}

// Written by Th3-822.

?>