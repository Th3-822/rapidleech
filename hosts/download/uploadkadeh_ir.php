<?php

if (!defined('RAPIDLEECH')) {
	require_once('index.html');
	exit();
}

if (!file_exists(HOST_DIR . 'download/GenericXFS_DL.php')) html_error('Cannot load "'.htmlentities(HOST_DIR).'download/GenericXFS_DL.php" (File doesn\'t exists)');
require_once(HOST_DIR . 'download/GenericXFS_DL.php');

class uploadkadeh_ir extends GenericXFS_DL {
	public $pluginVer = 10;
	public function Download($link) {
		$this->wwwDomain = false; // Switch to true if filehost forces it's domain with www.
		$this->cname = 'xfss'; // Session cookie name
		$this->sslLogin = false; // Force https on login.
		$this->embedDL = false; // Try to unpack player's js for finding download link. (Only hosts with video player)
		$this->unescaper = false; // Enable JS unescape decoder.

		$this->Start($link);
	}

	public function GetPage($link, $cookie = 0, $post = 0, $referer = 0, $auth = 0, $XMLRequest = 0) {
		if ($link == $this->link) {
			$link = $this->purl . 'download';
			if (empty($cookie)) $cookie = array('file_code' => $this->fid);
			else if (is_array($cookie)) $cookie['file_code'] = $this->fid;
			else $cookie = "file_code={$this->fid}&";
		}
		return parent::GetPage($link, $cookie, $post, $referer, $auth, $XMLRequest);
	}
}

// Written by Th3-822.

?>