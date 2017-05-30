<?php

if (!defined('RAPIDLEECH')) {
	require_once('index.html');
	exit();
}

if (!file_exists(HOST_DIR . 'download/GenericXFS_DL.php')) html_error('Cannot load "'.htmlentities(HOST_DIR).'download/GenericXFS_DL.php" (File doesn\'t exists)');
require_once(HOST_DIR . 'download/GenericXFS_DL.php');

class subyshare_com extends GenericXFS_DL {
	public $pluginVer = 20;
	public function Download($link) {
		$this->wwwDomain = false; // Switch to true if filehost forces it's domain with www.
		$this->cname = 'xfss'; // Session cookie name
		$this->httpsOnly = false; // Force https on all the site, supersedes $this->sslLogin when true.
		$this->sslLogin = false; // Force https on login post only.
		$this->embedDL = false; // Try to unpack player's js for finding download link. (Only hosts with video player)
		$this->unescaper = false; // Enable JS unescape decoder.

		$this->Start($link);
	}

	// Edited to add login captcha decoder.
	protected function sendLogin($post) {
		$purl = (!empty($this->sslLogin) ? 'https://'.$this->host.'/' : $this->purl) . '?op=login';
		$page = $this->GetPage($purl, $this->cookie);
		if (!($form = cut_str($page, '<form', '</form>'))) html_error('Cannot find login form.');
		if (($post['rand'] = cut_str($page, 'name="rand" value="', '"')) === false) html_error('Login form "rand" not found.');
	
		if (substr_count($form, "<span style='position:absolute;padding-left:") > 3 && preg_match_all("@<span style='[^\'>]*padding-left\s*:\s*(\d+)[^\'>]*'[^>]*>((?:&#\w+;)|(?:\d))</span>@i", $form, $txtCaptcha)) {
			// Text Captcha (decodeable)
			$txtCaptcha = array_combine($txtCaptcha[1], $txtCaptcha[2]);
			ksort($txtCaptcha, SORT_NUMERIC);
			$txtCaptcha = trim(html_entity_decode(implode($txtCaptcha), ENT_QUOTES, 'UTF-8'));
			$post['code'] = $txtCaptcha;
		} else {
			// This captcha seems to be optional, so i will remove the error msg.
			// html_error('Login captcha not found.');
		}

		// Don't remove this sleep or you may get "Error Decoding Captcha. [Login]"
		sleep(3); // 2 or 3 seconds.
		return parent::sendLogin($post);
	}

	// Added login captcha error msg.
	protected function checkLogin($page) {
		is_present($page, '>Wrong captcha code<', 'Error: Error Decoding Captcha. [Login]');
		return parent::checkLogin($page);
	}
}

// Written by Th3-822.