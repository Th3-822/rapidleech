<?php

if (!defined('RAPIDLEECH')) {
	require_once('index.html');
	exit();
}

if (!file_exists(HOST_DIR . 'download/GenericXFS_DL.php')) html_error('Cannot load "'.htmlentities(HOST_DIR).'download/GenericXFS_DL.php" (File doesn\'t exists)');
require_once(HOST_DIR . 'download/GenericXFS_DL.php');

class axfiles_net extends GenericXFS_DL {
	public $pluginVer = 13;
	public function Download($link) {
		$this->wwwDomain = false; // Switch to true if filehost forces it's domain with www.
		$this->cname = 'xfss'; // Session cookie name
		$this->sslLogin = false; // Force https on login.
		$this->embedDL = false; // Try to unpack player's js for finding download link. (Only hosts with video player)
		$this->unescaper = false; // Enable JS unescape decoder.

		$this->Start($link);
	}

	protected function findCountdown() {
		if (preg_match('@<div[^>]*>(?>.*?<div\s+id=[\'"][\w\-]+[\'"][^>]*>)\s*(\d+)\s*</div>(?>.*?</div>)@sim', $this->form, $count)) {
			if ($count[1] > 0) $this->CountDown($count[1] + 2);
			return true;
		}
		return false;
	}
}

// Written by Th3-822.

?>