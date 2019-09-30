<?php

if (!defined('RAPIDLEECH')) {
	require_once('index.html');
	exit();
}

if (!file_exists(HOST_DIR . 'download/GenericXFS_DL.php')) html_error('Cannot load "'.htmlentities(HOST_DIR).'download/GenericXFS_DL.php" (File doesn\'t exists)');
require_once(HOST_DIR . 'download/GenericXFS_DL.php');

class uploadkadeh_com extends GenericXFS_DL {
	public $pluginVer = 23;
	public function Download($link) {
		$this->wwwDomain = false; // Switch to true if filehost forces it's domain with www.
		$this->cname = 'xfss'; // Session cookie name
		$this->httpsOnly = true; // Force https on all the site, supersedes $this->sslLogin when true
		$this->sslLogin = false; // Force https on login post only.
		$this->cookieSave = false; // Save login cookies to file. (For hosts that limit/bans repeated logins)
		$this->embedDL = false; // Try to unpack player's js for finding download link. (Only hosts with video player)
		$this->unescaper = false; // Enable JS unescape decoder

		$this->Start($link);
	}
}

// Written by Th3-822.