<?php

if (!defined('RAPIDLEECH')) {
	require_once('index.html');
	exit();
}

if (!file_exists(HOST_DIR . 'download/GenericXFS_DL.php')) html_error('Cannot load "'.htmlentities(HOST_DIR).'download/GenericXFS_DL.php" (File doesn\'t exists)');
require_once(HOST_DIR . 'download/GenericXFS_DL.php');

class vidbull_com extends GenericXFS_DL {
	public $pluginVer = 6;
	public function Download($link) {
		$this->wwwDomain = true; // Switch to true if filehost forces it's domain with www.
		$this->cname = 'xfss'; // Session cookie name
		$this->sslLogin = false; // Force https on login.
		$this->embedDL = true; // Try to unpack player's js for finding download link. (Only hosts with video player)
		$this->unescaper = false; // Enable JS unescape decoder.
		$this->customDecoder = true; // Enable custom pageDecoder().

		// Custom link-check error messages
		$errMsgs = array('The file you were looking for could not be found',
			'The file was removed by administrator',
			'No available download slots for this region please check back later'
		);

		$this->Start($link, $errMsgs);
	}

	// Decoder: http://vidbull.com/player/obc.swf
	protected function pageDecoder() {
		if (preg_match('@file\s*:\s*"((?:[0-9a-fA-F]{2})+)"@i', $this->page, $encoded)) {
			$encoded = $encoded[1];
			$this->page = str_replace($encoded, rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_128, pack('H*', 'a949376e37b369f17bc7d3c7a04c5721'), pack('H*', $encoded), MCRYPT_MODE_ECB)), $this->page);
		}
	}

	protected function getFileName($url) {
		$fname = parent::getFileName($url);
		if (preg_match("@^(?:v(?:ideo)|{$this->fid})?\.(mp4|flv)$@i", $fname, $vExt) && preg_match('@<h3>Watch\s+([^\s<>\"\']+)\s*</h3>@i', $this->page, $title)) {
			$fname = preg_replace('@(?:\.(?:mp4|flv|mkv|webm|wmv|(m2)?ts|rm(vb)?|mpe?g?|vob|avi|[23]gp))+$@i', '', trim($title[1])) . '_S.' . strtolower($vExt[1]);
		}
		return $fname;
	}
}

// Written by Th3-822.

?>