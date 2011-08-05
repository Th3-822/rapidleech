<?php
if (!defined('RAPIDLEECH')) {
    require_once ("index.html");
    exit ();
}

class cash_file_net extends DownloadClass {

    public function Download($link) {
        $page = $this->GetPage($link);
        is_present($page, 'The file expired', 'File Not Found');
        is_present($page, 'The file was deleted by its owner', 'File Not Found');
        is_present($page, 'The file was deleted by administration', 'File Not Found');

        if (preg_match('#(\d+)</span> seconds#', $page, $wait)) {
            $this->CountDown($wait[1]);
        } else {
            html_error('Error : Timer not found!');
        }

        $post = array('op' => 'download2', 'id' => cut_str($page, 'name="id" value="','"'), 'rand' => cut_str($page, 'name="rand" value="','"'), 'referer' => $link, 'method_free' => '', 'method_premium' => '', 'down_direct' => '1');
        $page = $this->GetPage($link, 0, $post, $link);
        if (!preg_match('/<a href="(.+)">Download file/', $page, $dl)) html_error('Error: Download link not found');
        $Url = parse_url($dl[1]);
        $FileName = basename($Url['path']);
        $this->RedirectDownload($dl[1], $FileName, 0, 0, $link);
        exit;
    }
}

/*
 * cash-file.net free download plugin by Ruud v.Tony 30-07-2011
 */
?>
