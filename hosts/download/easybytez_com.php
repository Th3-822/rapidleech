<?php

if (!defined('RAPIDLEECH')) {
	require_once('index.html');
	exit();
}

if (!file_exists(HOST_DIR . 'download/GenericXFS_DL.php')) html_error('Cannot load "'.htmlentities(HOST_DIR).'download/GenericXFS_DL.php" (File doesn\'t exists)');
require_once(HOST_DIR . 'download/GenericXFS_DL.php');

class easybytez_com extends GenericXFS_DL {
	public $pluginVer = 10;
	public function Download($link) {
		$this->wwwDomain = false; // Switch to true if filehost forces it's domain with www.
		$this->cname = 'xfss'; // Session cookie name
		$this->sslLogin = false; // Force https on login.
		$this->embedDL = false; // Try to unpack player's js for finding download link. (Only hosts with video player)
		$this->unescaper = false; // Enable JS unescape decoder.

		$this->Start($link);
	}

	protected function sendLogin($post) {
		$purl = (!empty($this->sslLogin) ? 'https://'.$this->host.'/' : $this->purl) . '?op=login';
		$page = $this->GetPage($purl, $this->cookie);
		if (!($form = cut_str($page, '<form', '</form>'))) html_error('Cannot find login form.');
		if (!($post['rand'] = cut_str($page, 'name="rand" value="', '"'))) html_error('Login form "rand" not found.');
		$post['op'] .= '2';
		return parent::sendLogin($post);
	}

	protected function checkLogin($page) {
		if (preg_match('@Your IP is temporarily blocked due to \d+ incorrect login attempts<br(?:\s*/)?>This block will last for \d+ \w+.@i', $page, $err)) html_error($err[0]);
		return parent::checkLogin($page);
	}

	// It always show expire date... So i will have to check it.
	protected function isAccPremium($page) {
		if (!preg_match('@Premium account expire:\s*</TD>\s*<TD>\s*<b>\s*(\d+ \w+ \d{4})\s*</b>@i', $page, $date) || !($date = strtotime($date[1]))) html_error('Cannot check premium status.');
		return (time() < $date ? true : false);
	}
}

// Written by Th3-822.

?>