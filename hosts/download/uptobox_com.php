<?php

if (!defined('RAPIDLEECH')) {
	require_once('index.html');
	exit();
}

if (!file_exists(HOST_DIR . 'download/GenericXFS_DL.php')) html_error('Cannot load "'.htmlentities(HOST_DIR).'download/GenericXFS_DL.php" (File doesn\'t exists)');
require_once(HOST_DIR . 'download/GenericXFS_DL.php');

class uptobox_com extends GenericXFS_DL {
	public $pluginVer = 23;
	public function Download($link) {
		$this->wwwDomain = false; // Switch to true if filehost forces it's domain with www.
		$this->cname = 'xfss'; // Session cookie name
		$this->httpsOnly = true; // Force https on all the site, supersedes $this->sslLogin when true
		$this->sslLogin = true; // Force https on login post only.
		$this->cookieSave = true; // Save login cookies to file. (For hosts that limit/bans repeated logins)
		$this->embedDL = false; // Try to unpack player's js for finding download link. (Only hosts with video player)
		$this->unescaper = false; // Enable JS unescape decoder

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
		if (preg_match('@data-remaining-time=\'(\d+)\'@i', $this->page, $count)) {
			if ($count[1] > 0) $this->CountDown($count[1] + 2);
			return true;
		}
		return false;
	}

	// FreeDL is totally different now, so it needs it's own function
	protected function FreeDL($step = 1) {
		$this->page = $this->GetPage($this->link, $this->cookie);
		$this->cookie = GetCookiesArr($this->page, $this->cookie);

		if (!$this->testDL()) {
			if (!preg_match('@name=\'waitingToken\'\s+value=\'([\w\-]+)\'@i', $this->page, $token)) {
				if (preg_match('@wait\s+(?:\d+ \w+,?\s){0,2}\d+ \w+ to launch a new download@i', $this->page, $err)) html_error('Error: You need to ' . strip_tags($err[0]));
				return html_error('FreeDL Token Not Found.');
			}
			if (!$this->findCountdown()) html_error('CountDown not Found.');

			$this->page = $this->GetPage($this->link, $this->cookie, array('waitingToken' => $token[1]));
			$this->cookie = GetCookiesArr($this->page, $this->cookie);

			if (!$this->testDL()) {
				return html_error('FreeDL Download Link not Found.');
			}
		}
		return true;
	}

	// Full login url, just in case.
	protected function sendLogin($post) {
		return $this->GetPage('https://uptobox.com/?op=login&referer=homepage', $this->cookie, $post, 'https://uptobox.com/?op=login&referer=homepage');
	}

	protected function isAccPremium($page) {
		if (stripos($page, 'Free member') === false) return true;
		else return false;
	}

	protected function checkLogin($page) {
		is_present($page, 'You are trying to log in from a different country', 'Login Failed: Login Blocked By IP, Check Account Email And Follow The Steps To Add IP to Whitelist.');
		return parent::checkLogin($page);
	}
}

// Written by Th3-822.