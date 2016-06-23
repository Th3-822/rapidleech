<?php

if (!defined('RAPIDLEECH')) {
	require_once('index.html');
	exit();
}

if (!file_exists(HOST_DIR . 'download/GenericXFS_DL.php')) html_error('Cannot load "'.htmlentities(HOST_DIR).'download/GenericXFS_DL.php" (File doesn\'t exists)');
require_once(HOST_DIR . 'download/GenericXFS_DL.php');

class solidfiles_com extends GenericXFS_DL
{
	public function Download($link)
	{
		$page = $this->GetPage($link);

		if (!preg_match("@https?:\\/\\/s(\\d+\\.)?solidfilesusercontent\\.com\\/[^\"\\'><\\r\\n\\t]+@i", urldecode($page), $downl)) html_error('Error: Download link not found.');

		$filename=substr(basename($downl[0]),0,strpos(basename($downl[0]),"&"));

		$this->RedirectDownload(substr($downl[0],0,strpos($downl[0],"&")), $filename, 0, 0, $filename);

	}

}

// Written by MosTec1991.

?>