<?php
if (!defined('RAPIDLEECH')) {
	require_once('index.html');
	exit;
}
class uploadfiles_io extends DownloadClass {
	public function Download($link) {
        $page = $this->GetPage($link);
        $cookie = GetCookies($page);
        if (!preg_match('/<div class="details">\n<h3>(.*?)<\/h3>/', $page, $filename)) html_error('File not found!');
        $filename = $filename[1];
        preg_match('/http?s:\/\/uploadfiles\.io\/(.*?)$/', $link, $dlink);
        $dlink = $dlink[1];
        $dlink = 'https://down.uploadfiles.io/get/'.$dlink;
        $this->RedirectDownload($dlink, $filename, $cookie, 0, $link);
    }
}

// [11-08-2017] Written by NimaH79.