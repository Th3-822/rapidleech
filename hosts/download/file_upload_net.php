<?php
if (!defined('RAPIDLEECH')) {
    require_once('index.html');
    exit();
}

class file_upload_net extends DownloadClass {
    
    public function Download($link) {
        $page = $this->GetPage($link);
        is_present($page, 'Datei existiert nicht auf unserem Server!', 'File not found!');
        if (!preg_match('%<form action="([^"]+)" method="post" >%', $page, $rd)) html_error('Error [Post Link not found!]');
        $redir = html_entity_decode(trim($rd[1]), ENT_QUOTES, 'UTF-8');
        
        $post = array();
        $post['valid'] = cut_str($page, 'name="valid" value="', '"');
        $post['load6'] = ' ';
        $page = $this->GetPage($redir, 0, $post, $link);
        if (!preg_match('@Location: (http:\/\/[^\r\n]+)@i', $page, $dl)) html_error('Error [Download Link not found!]');
        $filename = basename(parse_url($dl[1], PHP_URL_PATH));
        $this->RedirectDownload($dl[1], $filename, 0, 0, $link);
        exit();
    }
}

/*
 * by Ruud v.Tony 14-02-2012
 */
?>
