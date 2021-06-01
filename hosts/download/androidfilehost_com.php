<?php
if (!defined('RAPIDLEECH')) {
	require_once('index.html');
	exit;
}
class androidfilehost_com extends DownloadClass {
	public function Download($link) {
		if(!preg_match('/fid=([0-9]+)/', $link, $fid)) html_error('URL not valid!');
        $ch = curl_init('https://androidfilehost.com/libs/otf/mirrors.otf.php');
        curl_setopt($ch, CURLOPT_POSTFIELDS, ['submit' => 'submit', 'action' => 'getdownloadmirrors', 'fid' => $fid[1]]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
        $result = curl_exec($ch);
        curl_close($ch);
        if($result === false) html_error('Curl error: '.curl_error($ch));
        $result = json_decode($result, true);
        if(empty($result['MIRRORS'])) html_error(json_encode($result));
        $this->RedirectDownload($result['MIRRORS'][0]['url'], 0, 0, 0, $link);
    }
}

// [03-01-2018] Written by NimaH79.