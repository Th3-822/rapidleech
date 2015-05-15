<?php

if (!defined('RAPIDLEECH')) {
	require_once('index.html');
	exit();
}

if (!file_exists(HOST_DIR . 'download/GenericXFS_DL.php')) html_error('Cannot load "'.htmlentities(HOST_DIR).'download/GenericXFS_DL.php" (File doesn\'t exists)');
require_once(HOST_DIR . 'download/GenericXFS_DL.php');

class mediafree_co extends GenericXFS_DL {
	public $pluginVer = 5;
	public function Download($link) {
		$this->wwwDomain = false; // Switch to true if filehost forces it's domain with www.
		$this->cname = 'xfss'; // Session cookie name
		$this->sslLogin = false; // Force https on login.
		$this->embedDL = false; // Try to unpack player's js for finding download link. (Only hosts with video player)

		$this->Start($link);
	}

	// Edit for add a closed form tag where it's needed.
	protected function FindPost($formOp = 0) {
		// I don't know why it's that comment just when there should be that </form>
		$this->page = str_replace('<!-- Family-safe ads -->', '</form>', $this->page);

		if (empty($formOp)) return parent::FindPost();
		else return parent::FindPost($formOp);
	}
}
// Written by Th3-822.
?>