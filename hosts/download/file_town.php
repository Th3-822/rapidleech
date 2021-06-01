<?php
if (!defined('RAPIDLEECH')) {
	require_once('index.html');
	exit;
}
class file_town extends DownloadClass {
	public function Download($link) {
        $page = $this->GetPage($link);
        if (!preg_match('/<b>Filename:<\/b> (.*?)<br>/', $page, $filename)) html_error('File not found!');
        $filename = $filename[1];
        preg_match('/href="\.\.\/uploads\/(.*?)"/', $page, $dlink);
        $dlink = $dlink[1];
        $dlink = 'https://file.town/uploads/'.$dlink;
        $this->RedirectDownload($dlink, $filename, 0, 0, $link);
    }
}

//[11-08-2017]  Written by NimaH79.