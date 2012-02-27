<?php
if (!defined('RAPIDLEECH')) {
    require_once ('index.html');
    exit();
}

class uploads_bizhat_com extends DownloadClass {

    public function Download($link) {
        $page = $this->GetPage($link);
        is_present($page, 'File not found.');
        $cookie = GetCookies($page);
        if (preg_match('/dnl_counter=(\d+);/', $page, $w)) $this->CountDown($w[1]);

        $post = array();
        $post['id'] = cut_str($page, 'name="id" value="', '"');
        $post['download'] = cut_str($page, 'name="download" value="', '"');
        $page = $this->GetPage($link, $cookie, $post, $link);
        if (!preg_match('@http:\/\/\w+\.uploads\.bizhat\.com\/dl\/[^\']+@', $page, $dl)) html_error('Error [Download Link not found!]');
        $dlink = trim($dl[0]);
        $filename = basename(parse_url($dlink, PHP_URL_PATH));
        $this->RedirectDownload($dlink, $filename, $cookie, 0, $link);
        exit();
    }

}

// written by Ruud v.Tony 15-02-2012
?>