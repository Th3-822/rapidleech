<?php

if (!defined('RAPIDLEECH')) {
	require_once('index.html');
	exit();
}

if (!file_exists(HOST_DIR . 'download/GenericXFS_DL.php')) html_error('Cannot load "'.htmlentities(HOST_DIR).'download/GenericXFS_DL.php" (File doesn\'t exists)');
require_once(HOST_DIR . 'download/GenericXFS_DL.php');

class thevideo_me extends GenericXFS_DL {
	public $pluginVer = 10;
	public function Download($link) {
		$this->wwwDomain = false; // Switch to true if filehost forces it's domain with www.
		$this->cname = 'xfss'; // Session cookie name
		$this->sslLogin = false; // Force https on login.
		$this->embedDL = false; // Try to unpack player's js for finding download link. (Only hosts with video player)
		$this->unescaper = false; // Enable JS unescape decoder.

		// Custom Download Regexp
		$this->DLregexp = '@https?://(?:[\w\-]+\.)+[\w\-]+(?:\:\d+)?/\w{60}/[^\t\r\n<>\'\"\?\&]+(?=[\"\']\s*}])@i';

		$this->Start($link);
	}

	protected function FindPost($formOp = 'download[1-3]') {
		if (!parent::FindPost($formOp)) return false;
		if (preg_match('@^<form\s[^\>]*(?<=\s)id=([\"\'])(\w+)\1[^\>]*>@i', $this->form, $formId) && preg_match_all("@name\s*:\s*'(\w+)'\s*,\s*value\s*:\s*'(\w+)'\s*\}\s*\)\.prependTo\(([\"\'])#{$formId[2]}\\3\)@i", $this->page, $inputs)) {
			$this->post = array_merge($this->post, array_combine($inputs[1], $inputs[2]));
		}
		return true;
	}
}

// Written by Th3-822.

?>