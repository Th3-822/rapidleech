<?php

if (!defined('RAPIDLEECH')) {
	require_once('index.html');
	exit();
}

if (!file_exists(HOST_DIR . 'download/GenericXFS_DL.php')) html_error('Cannot load "'.htmlentities(HOST_DIR).'download/GenericXFS_DL.php" (File doesn\'t exists)');
require_once(HOST_DIR . 'download/GenericXFS_DL.php');

class uploadex_com extends GenericXFS_DL {
	public $pluginVer = 8;
	public function Download($link) {
		$this->wwwDomain = false; // Switch to true if filehost forces it's domain with www.
		$this->cname = 'xfss'; // Session cookie name
		$this->sslLogin = false; // Force https on login.
		$this->embedDL = false; // Try to unpack player's js for finding download link. (Only hosts with video player)
		$this->unescaper = false; // Enable JS unescape decoder.

		$this->Start($link);
	}

	// Some links doesn't work with https, let's switch to http
	public function RedirectDownload($link, $FileName, $cookie = 0, $post = 0, $referer = 0, $force_name = 0, $auth = 0, $addon = array()) {
		$link = parse_url($link);
		$link['scheme'] = 'http';
		$link = rebuild_url($link);
		return parent::RedirectDownload($link, $FileName, $cookie, $post, $referer, $force_name, $auth, $addon);
	}
}

// Written by Th3-822.

?>