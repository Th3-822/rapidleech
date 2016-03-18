<?php

if (!defined('RAPIDLEECH')) {
	require_once('index.html');
	exit();
}

if (!file_exists(HOST_DIR . 'download/GenericXFS_DL.php')) html_error('Cannot load "'.htmlentities(HOST_DIR).'download/GenericXFS_DL.php" (File doesn\'t exists)');
require_once(HOST_DIR . 'download/GenericXFS_DL.php');

class uploadrocket_net extends GenericXFS_DL {
	public $pluginVer = 10;
	public function Download($link) {
		$this->wwwDomain = false; // Switch to true if filehost forces it's domain with www.
		$this->cname = 'xfss'; // Session cookie name
		$this->sslLogin = false; // Force https on login.
		$this->embedDL = false; // Try to unpack player's js for finding download link. (Only hosts with video player)
		$this->unescaper = false; // Enable JS unescape decoder.

		$this->Start($link);
	}

	protected function FindPost($formOp = 'download[1-3]') {
		$return = parent::FindPost($formOp);
		if (!empty($this->post['method_isfree']) && !empty($this->post['method_ispremium'])) $this->post['method_ispremium'] = '';
		return $return;
	}
}

// Written by Th3-822.

?>