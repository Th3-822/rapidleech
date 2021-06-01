<?php
if (!defined('RAPIDLEECH')) {
	require_once('index.html');
	exit;
}
class cdn_persiangig_com extends DownloadClass {
	public function Download($link) {
        $page = $this->GetPage($link);
        if (!preg_match('/<h1 class="title pull-right">(.*?)<\/h1>/', $page, $filename)) html_error('File not found!');
        preg_match('/https?:\/\/cdn\.persiangig\.com\/download\/(.*?)\/(.*?)$/', $link, $info);
        $id = $info[1];
        $name = str_replace('/dl', '', $info[2]);
        $token = file_get_contents('http://cdn.persiangig.com/cfs/rest/publicAccess/'.$id.'/generateDownloadLink');
        $token = json_decode($token, true)['token'];
        $dlink = 'http://cdn.persiangig.com/dl/'.$token.'/'.$id.'/'.$name;
        $filename = $filename[1];
        $this->RedirectDownload($dlink, $filename, 0, 0, $link);
    }
}

// [11-08-2017] Written by NimaH79.