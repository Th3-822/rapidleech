<?php
if (!defined('RAPIDLEECH')) {
    require_once("index.html");
    exit;
}

class zshare_net extends DownloadClass {
    
    public function Download($link) {
        if (stristr($link, '/video/')) {
            $link = str_replace('/video/', '/download/', $link);
        } elseif (stristr($link, '/audio/')) {
            $link = str_replace('/audio/', '/download/', $link);
        }

        $page = $this->GetPage($link);
        //textarea($page, $cols, $rows, true);
        is_present($page, '/file-404.html', 'File not found!');
        $cookie = GetCookies($page);

        $post = array('referer2' => cut_str($page, 'id="referer2" value="', '"'), 'download' => '1', 'imageField.x' => rand(0,153), 'imageField.y' => rand(0,25));
        $page = $this->GetPage($link, $cookie, $post, $link);
        $cookie = $cookie."; ".GetCookies($page);
        if (!preg_match('#here[|](\d+)[|]class#', $page, $wait)) html_error('Error: Timer not found!');
        $this->CountDown($wait[1]);
        $dlink = cut_str($page, 'var link_enc=new Array(', ');');
        $dlink = preg_replace('@[,\']@i', '', $dlink);
        if (!isset($dlink)) html_error('Error: Download link not found!');
        $filename = parse_url($dlink);
        $FileName = basename($filename['path']);
        $this->RedirectDownload($dlink, $FileName, $cookie, 0, $link);
    }
}

/*
 * zshare.net free download plugin by Ruud v.Tony 22-09-2011
 */
?>