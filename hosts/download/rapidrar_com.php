<?php

if (!defined('RAPIDLEECH')) {
	require_once('index.html');
	exit();
}

if (!file_exists(HOST_DIR . 'download/GenericXFS_DL.php')) html_error('Cannot load "'.htmlentities(HOST_DIR).'download/GenericXFS_DL.php" (File doesn\'t exists)');
require_once(HOST_DIR . 'download/GenericXFS_DL.php');

class rapidrar_com extends GenericXFS_DL {
	public $pluginVer = 22;
	public function Download($link) {
		$this->wwwDomain = false; // Switch to true if filehost forces it's domain with www.
		$this->cname = 'xfss'; // Session cookie name
		$this->httpsOnly = true; // Force https on all the site, supersedes $this->sslLogin when true
		$this->sslLogin = false; // Force https on login post only.
		$this->embedDL = false; // Try to unpack player's js for finding download link. (Only hosts with video player)
		$this->unescaper = false; // Enable JS unescape decoder

		$this->Start($link);
	}

	protected function FreeDL($step = 1) {
		// Hacky Trick :P
		if ($step == 1) {
			$this->page = $this->GetPage($this->link, $this->cookie, array('op' => 'download1', 'id' => $this->fid, 'method_free' => 'Free%20Download'));
			$this->cookie = GetCookiesArr($this->page, $this->cookie);
			$step++;
		}
		return parent::FreeDL($step);
	}
}

// Written by Th3-822.