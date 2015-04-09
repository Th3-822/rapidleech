<?php

if (!defined('RAPIDLEECH')) {
	require_once('index.html');
	exit();
}

if (!file_exists(HOST_DIR . 'download/GenericXFS_DL.php')) html_error('Cannot load "'.htmlentities(HOST_DIR).'download/GenericXFS_DL.php" (File doesn\'t exists)');
require_once(HOST_DIR . 'download/GenericXFS_DL.php');

class uptobox_com extends GenericXFS_DL {
	public $pluginVer = 2;
	public function Download($link) {
		$this->wwwDomain = false; // Switch to true if filehost forces it's domain with www.
		$this->cname = 'xfss'; // Session cookie name
		$this->sslLogin = false; // Force https on login.
		$this->embedDL = false; // Try to unpack player's js for finding download link. (Only hosts with video player)

		$this->Start($link);
	}

	// Patched function for UTB bad https login.
	protected function sendLogin($post) {
		return $this->GetPage((!empty($this->sslLogin) ? 'https://'.$this->host.'/' : $this->purl) . '?op=login', $this->cookie, $post, 'https://login.uptobox.com/');
	}
}

// Written by Th3-822.

?>