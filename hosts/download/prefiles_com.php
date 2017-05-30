<?php

if (!defined('RAPIDLEECH')) {
	require_once('index.html');
	exit();
}

if (!file_exists(HOST_DIR . 'download/GenericXFS_DL.php')) html_error('Cannot load "'.htmlentities(HOST_DIR).'download/GenericXFS_DL.php" (File doesn\'t exists)');
require_once(HOST_DIR . 'download/GenericXFS_DL.php');

class prefiles_com extends GenericXFS_DL {
	public $pluginVer = 19;
	public function Download($link) {
		$this->wwwDomain = false; // Switch to true if filehost forces it's domain with www.
		$this->cname = 'xfss'; // Session cookie name
		$this->httpsOnly = false; // Force https on all the site, supersedes $this->sslLogin when true.
		$this->sslLogin = false; // Force https on login post only.
		$this->embedDL = false; // Try to unpack player's js for finding download link. (Only hosts with video player)
		$this->unescaper = false; // Enable JS unescape decoder.

		$this->Start($link);
	}

	protected function findCountdown() {
		if (preg_match('@Your subsequent download will be started in next (?:(?:\s*|\s*<br\s*/?\s*>\s*)?\d+ \w+,?\s){0,2}\d+ \w+@i', $this->page, $err)) html_error('Error: ' . strip_tags($err[0]));
		if (preg_match('@\.circularCountDown\({\s*duration\s*:\s*{(?:\s*hours\s*:\s*(\d+)?\s*,)(?:\s*minutes\s*:\s*(\d+)\s*,)\s*seconds:\s*(\d+)\s*}@', $this->page, $count)) {
			$count = (!empty($count[1]) ? $count[1] * 3600 : 0) + (!empty($count[2]) ? $count[2] * 60 : 0) + (!empty($count[3]) ? $count[3] : 0);
			if ($count > 0) $this->CountDown($count + 2);
			return true;
		}
		return false;
	}
}

// Written by Th3-822.