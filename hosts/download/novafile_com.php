<?php

if (!defined('RAPIDLEECH')) {
	require_once('index.html');
	exit();
}

if (!file_exists(HOST_DIR . 'download/GenericXFS_DL.php')) html_error('Cannot load "'.htmlentities(HOST_DIR).'download/GenericXFS_DL.php" (File doesn\'t exists)');
require_once(HOST_DIR . 'download/GenericXFS_DL.php');

class novafile_com extends GenericXFS_DL {
	public $pluginVer = 18;
	public function Download($link) {
		$this->wwwDomain = false; // Switch to true if filehost forces it's domain with www.
		$this->cname = 'xfss'; // Session cookie name
		$this->httpsOnly = true; // Force https on all the site, supersedes $this->sslLogin when true
		$this->sslLogin = false; // Force https on login post only.
		$this->embedDL = false; // Try to unpack player's js for finding download link. (Only hosts with video player)
		$this->unescaper = false; // Enable JS unescape decoder.

		// Custom Download Regexp
		$this->DLregexp = '@https?://s\d+\.novafile\.com/\w{72}/[^\t\r\n<>\'\"\?\&]+@i';

		// Extra FreeDL Error Messages.
		$this->cErrsFDL = array('This file can only be downloaded by Premium Members');

		$this->Start($link);
	}

	// Edited to fix login url
	protected function sendLogin($post) {
		$page = $this->GetPage((!empty($this->sslLogin) ? 'https://'.$this->host.'/' : $this->purl) . 'login', $this->cookie, $post);
		return $page;
	}
}

// Written by Th3-822.