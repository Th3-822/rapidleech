<?php

if (!defined('RAPIDLEECH')) {
	require_once('index.html');
	exit();
}

if (!file_exists(HOST_DIR . 'download/GenericXFS_DL.php')) html_error('Cannot load "'.htmlentities(HOST_DIR).'download/GenericXFS_DL.php" (File doesn\'t exists)');
require_once(HOST_DIR . 'download/GenericXFS_DL.php');

class bestreams_net extends GenericXFS_DL {
	public $pluginVer = 6;
	public function Download($link) {
		$this->wwwDomain = false; // Switch to true if filehost forces it's domain with www.
		$this->cname = 'xfss'; // Session cookie name
		$this->sslLogin = false; // Force https on login.
		$this->embedDL = true; // Try to unpack player's js for finding download link. (Only hosts with video player)
		$this->unescaper = false; // Enable JS unescape decoder.

		$this->Start($link);
	}

	// Rapidleech doesn't support rtmp, so i must get to the real file, that it's somewhere.
	protected function testDL($name = '') {
		if (preg_match('@(https?://(?:[\w\-]+\.)+[\w\-]+(?:\:\d+)?/)i/(?:[^/\s\"\'<>]+/)*\w{12}(_t)?\.jpe?g@i', $this->page, $SV) && preg_match('@[?&]h(?:ash)?=(\w+)@i', $this->page, $hash)) {
			$DL = $SV[1] . $hash[1] . '/v.mp4'; // And that's how a XFS video download link is forged.
			if (empty($name)) $name = $this->getFileName($DL[0]);
			$this->RedirectDownload($DL, basename($name));
			return true;
		}
		return parent::testDL($name); // Keep trying original method.
	}
}

// Written by Th3-822.

?>