<?php
if (!defined('RAPIDLEECH')) {
    require_once('index.html');
    exit();
}

class sharebeast_com extends DownloadClass {

    public function Download($link) {
        $page = $this->GetPage($link);
        is_present($page, 'The file expired', 'File not found!');
        is_present($page, 'The file was deleted by its owner', 'File not found!');
        is_present($page, 'The file was deleted by administration because it didn\'t comply with our Terms of Use', 'File not found!');

        $post = array('op' => 'download2', 'id' => trim(cut_str($page, 'name="id" value="','"')), 'rand' => trim(cut_str($page, 'name="rand" value="', '"')), 'referer' => $link, 'method_free' => '', 'method_premium' => '', 'down_script' => '1', 'x' => rand(0,92), 'y' => rand(0,33));
        $page = $this->GetPage($link, 0, $post, $link);
        if (!preg_match('/Location: (.+)/i', $page, $dl)) html_error('Error: Plugin out of date!');
        $Url = parse_url(trim($dl[1]));
        $FileName = basename($Url['path']);
        $this->RedirectDownload(trim($dl[1]), $FileName, 0, 0, $link);
    }
}

/*
 * Sharebeast.com free download plugin by Ruud v.Tony 04-09-2011
 */
?>
