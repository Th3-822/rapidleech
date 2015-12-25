<?php

if (!defined('RAPIDLEECH')) {
	require_once('index.html');
	exit();
}

if (!file_exists(HOST_DIR . 'download/GenericXFS_DL.php')) html_error('Cannot load "'.htmlentities(HOST_DIR).'download/GenericXFS_DL.php" (File doesn\'t exists)');
require_once(HOST_DIR . 'download/GenericXFS_DL.php');

class uptobox_com extends GenericXFS_DL {
	public $pluginVer = 7;
	public function Download($link) {
		$this->wwwDomain = false; // Switch to true if filehost forces it's domain with www.
		$this->cname = 'xfss'; // Session cookie name
		$this->sslLogin = false; // Force https on login.
		$this->embedDL = false; // Try to unpack player's js for finding download link. (Only hosts with video player)
		$this->unescaper = false; // Enable JS unescape decoder.

		// Custom link-check error messages
		$errMsgs = array('The file you were looking for could not be found',
				'The file was removed by administrator',
				'Unfortunately, the file you want is not available.',
				'Sorry, Uptobox.com is not available in your country'
		);

		$link = str_ireplace('uptostream.com', 'uptobox.com', $link);
		$this->Start($link, $errMsgs);
	}

	protected function findCountdown() {
		if (parent::findCountdown()) return true;
		if (stripos($this->page, 'Happy hour!!!')) {
			$this->CountDown(3); // I still do get some Skipped CountDown even with longer countdowns it may be a bug at the site at "happy hour".
			return true;
		}
		return false;
	}

	// Patched function for UTB new ajax login.
	protected function sendLogin($post) {
		return $this->GetPage('https://login.uptobox.com/logarithme', $this->cookie, $post, 'https://login.uptobox.com/');
	}

	protected function isAccPremium($page) {
		if (stripos($page, 'Free member') === false) return true;
		else return false;
	}
}

// Written by Th3-822.

?>