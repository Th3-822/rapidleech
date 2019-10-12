<?php

if (!defined('RAPIDLEECH')) {
	require_once('index.html');
	exit();
}

if (!file_exists(HOST_DIR . 'download/GenericXFS_DL.php')) html_error('Cannot load "'.htmlentities(HOST_DIR).'download/GenericXFS_DL.php" (File doesn\'t exists)');
require_once(HOST_DIR . 'download/GenericXFS_DL.php');

class uploader_link extends GenericXFS_DL {
	public $pluginVer = 14;
	
	public function Download($link) {
		$this->wwwDomain = false; // Switch to true if filehost forces it's domain with www.
		$this->cname = 'xfss'; // Session cookie name
		$this->sslLogin = true; // Force https on login.
		$this->embedDL = false; // Try to unpack player's js for finding download link. (Only hosts with video player)
		$this->unescaper = false; // Enable JS unescape decoder.
		$this->httpsOnly = true;
		
		$this->Start($link);
	}
	
	// Edited to add login captcha decoder.
	protected function Login() {
		$devil = preg_match('@uploader.link/f/[^\r\n\s\t<>\'\"]+@', $this->page, $dlink);
		if($devil==0){
			html_error('Unable to get download link');
		}
		$this->page = $this->GetPage('https://'.$dlink[0]);
		
		return parent::Login();
	}
}

// Written by miyuru

?>