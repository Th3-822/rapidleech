<?php

if (!defined('RAPIDLEECH')) {
	require_once('index.html');
	exit();
}

if (!file_exists(HOST_DIR . 'download/GenericXFS_DL.php')) html_error('Cannot load "'.htmlentities(HOST_DIR).'download/GenericXFS_DL.php" (File doesn\'t exists)');
require_once(HOST_DIR . 'download/GenericXFS_DL.php');

class novafile_com extends GenericXFS_DL {
	public $pluginVer = 5;
	public function Download($link) {
		$this->wwwDomain = false; // Switch to true if filehost forces it's domain with www.
		$this->cname = 'xfss'; // Session cookie name
		$this->sslLogin = false; // Force https on login.
		$this->embedDL = false; // Try to unpack player's js for finding download link. (Only hosts with video player)

		// Custom Download Regexp
		$this->DLregexp = '@https?://s\d+\.novafile\.com/\w{72}/[^\t\r\n<>\'\"\?\&]+@i';

		$this->Start($link);
	}

	// Edited to fix login url
	protected function sendLogin($post) {
		$page = $this->GetPage((!empty($this->sslLogin) ? 'https://'.$this->host.'/' : $this->purl) . 'login', $this->cookie, $post);
		return $page;
	}
}

// Written by Th3-822.

?>