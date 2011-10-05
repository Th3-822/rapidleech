<?php
if (!defined('RAPIDLEECH')) {
    require_once("index.html");
    exit;
}

class filesend_net extends DownloadClass {

    public function Download($link) {

        $page = $this->GetPage($link);
        is_present($page, 'File Not Found');
        $cookie = GetCookies($page);

        if (!preg_match('#time = (\d+);#', $page, $wait)) html_error('Error: Timer id not found???');
        $this->CountDown($wait[1]);
        $temp = cut_str($page, '<form method="POST"', '</form>');
        if (!preg_match('%<input type="hidden" name="(\w+)" value="(\w+)">%', $temp, $check)) html_error('Error: Post ID not found???');
        $post = array($check[1] => $check[2], 'download' => '');
        $dlink = cut_str($temp, 'action="', '"');
        if (!$dlink) html_error('Error: Download link not found???');
        $Url = parse_url($dlink);
        $FileName = basename($Url['path']);
        $this->RedirectDownload($dlink, $FileName, $cookie, $post);
        exit();
    }
}

/*
 * Filesend.net free download plugin by Ruud v.Tony 04-10-2011
 */
?>