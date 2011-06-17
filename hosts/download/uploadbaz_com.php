<?php
if (!defined('RAPIDLEECH')) {
    require_once ("index.html");
    exit ();
}

class uploadbaz_com extends DownloadClass {

    public function Download($link) {
        $page = $this->GetPage($link);
        is_present($page, "File Not Found", "File Not Found");
        
        $id = cut_str($page, 'name="id" value="','"');
        $FileName = cut_str($page, 'name="fname" value="','"');

        $post = array();
        $post['op'] = "download1";
        $post['usr_login'] = "";
        $post['id'] = $id;
        $post['fname'] = $FileName;
        $post['referer'] = $link;
        $post['method_free'] = "Free Download";
        $page = $this->GetPage($link, 0, $post, $link);

        $rand = cut_str($page, 'name="rand" value="','"');
        unset($post);
        $post['op'] = "download2";
        $post['id'] = $id;
        $post['rand'] = $rand;
        $post['referer'] = $link;
        $post['method_free'] = "Free Download";
        $post['method_premium'] = "";
        $post['down_direct'] = "1";
        $page = $this->GetPage($link, 0, $post, $link);

        if (!preg_match('/(http:\/\/s\d+\.uploadbaz\.com\/\w+\/([0-9])\/.+)"/', $page, $dl)) {
            html_error("Error: Download link not found!");
        }
        $Url=parse_url($dl[1]);
        if (!$FileName) $FileName=basename($Url['path']);
        $this->RedirectDownload($dl[1], $FileName, 0, 0, $link);
        exit();

    }
}
?>
