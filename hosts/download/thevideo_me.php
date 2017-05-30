<?php

if (!defined('RAPIDLEECH')) {
	require_once('index.html');
	exit();
}

if (!file_exists(HOST_DIR . 'download/GenericXFS_DL.php')) html_error('Cannot load "'.htmlentities(HOST_DIR).'download/GenericXFS_DL.php" (File doesn\'t exists)');
require_once(HOST_DIR . 'download/GenericXFS_DL.php');

class thevideo_me extends GenericXFS_DL {
	public $pluginVer = 20;
	public function Download($link) {
		$this->wwwDomain = false; // Switch to true if filehost forces it's domain with www.
		$this->cname = 'xfsts'; // Session cookie name
		$this->httpsOnly = true; // Force https on all the site, supersedes $this->sslLogin when true
		$this->sslLogin = false; // Force https on login post only.
		$this->embedDL = false; // Try to unpack player's js for finding download link. (Only hosts with video player)
		$this->unescaper = false; // Enable JS unescape decoder

		// Custom Download Regexp
		$this->DLregexp = '@https?://(?:[\w\-]+\.)+[\w\-]+(?:\:\d+)?/(?:files|dl?|cgi-bin/dl\.cgi|[\da-zA-Z]{30,})/(?:[^\?\'\"\t<>\r\n\\\]{15,}|v(?:id(?:eo)?)?\.(?:flv|mp4))@i';

		$this->Start($link);
	}

	protected function checkLogin($page) {
		$return = parent::checkLogin($page);
		$cookie = GetCookiesArr($page);
		if (!empty($cookie['msg'])) {
			$err = htmlspecialchars(trim(rawurldecode($cookie['msg'])));
			is_present($err, 'Captcha is required', 'Login Is Asking For Captcha, Login with Cookie Instead');
			return html_error('Login Error: ' . $err);
		}
		return $return;
	}

	// I'm playing 'The Game' :P
	protected function testDL() {
		if (!empty($this->enableDecoders)) $this->runDecoders();

		if (preg_match_all($this->DLregexp, $this->page, $DL)) {
			$DL = array_reverse($DL[0]);

			if (!preg_match('@var\s+[\$_A-Za-z][\$\w]*\s*=\s*(\'|\")([\d\w+/=]{13,})\1@i', $this->page, $token)) html_error('[Error] Stream Token Not Found');
			$token = $token[2];

			$page = $this->GetPage($this->purl . 'vs'.'ig'.'n/pl'.'ayer/' . $token);
			if (!preg_match_all('@eval\s*\(\s*function\s*\(p,a,c,k,e,r\)\s*\{.+\}\s*\(\s*\'([^\r|\n]*)\'\s*,\s*\[\]\s*,\s*(\d+)\s*,\s*\'([^\']+)\'\.split\([\'|\"](.)[\'|\"]\)(?:\s*,\s*0\s*,\s*\{\})?\)\)@', $page, $js)) html_error('[Error] Encoded Stream Token Not Found');
			$cnt = count($js[0]);
			for ($i = 0; $i < $cnt; $i++) {
				$page = str_replace($js[0][$i], $this->XFSUnpacker($js[1][$i], 36, $js[2][$i], $js[3][$i], $js[4][$i]), $page);
			}
			if (!preg_match('@[\da-z]{50,}@', $page, $decToken)) html_error('[Error] Decoded Stream Token Not Found');

			$this->RedirectDownload($DL[0] . '?down'.'load=true&v'.'t=' . $decToken[0], basename($this->getFileName($DL[0])));
			return true;
		}
		return false;
	}

	protected function getVideoTitle() {
		if (preg_match('@<h2 class="page-header text-overflow">\s*([^"<>]+)\s*</h2>@i', $this->page, $title)) {
			return html_entity_decode(trim($title[1]), ENT_QUOTES, 'UTF-8');
		}
		return false;
	}
}

// Written by Th3-822.