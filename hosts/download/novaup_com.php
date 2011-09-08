<?php
if (!defined('RAPIDLEECH')) {
	require_once ("index.html");
	exit();
}

class novaup_com extends DownloadClass{
    public function Download($link){
        $page=$this->GetPage($link);
        is_present($page, 'This file no longer exists on our servers', 'File is not available');
        if (!preg_match('#http://\w+.novaup.com/dl/\w+/\w+/[^"]+#', $page, $dlink)){
            html_error("Error 0x01: Plugin is out of date");
        }
        $this->RedirectDownload($dlink[0], basename(parse_url($dlink[0], PHP_URL_PATH)));
        exit;
    }
}

// by vdhdevil
?>
