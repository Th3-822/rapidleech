<?php
if (!defined('RAPIDLEECH')) {
    require_once ("index.html");
    exit ();
}

class d10upload_com extends DownloadClass {

    public function Download($link) {
        $page = $this->GetPage($link);
        is_present($page, 'No such file with this filename', 'No such file with this filename');
        is_present($page, 'File Not Found', 'File Not Found');

        $id = cut_str($page, 'name="id" value="','"');
        $fname = cut_str($page, 'name="fname" value="','"');

        $post = array();
        $post['op'] = "download1";
        $post['usr_login'] = "";
        $post['id'] = $id;
        $post['fname'] = $fname;
        $post['referer'] = $link;
        $post['method_free'] = "Free Download";
        $page = $this->GetPage($link, 0, $post, $link);
        if (preg_match('#You have to wait (\d+) minutes, (\d+) seconds till next download#', $page, $msg)) html_error($msg[0]);
        if (preg_match('#(\d+)</span> seconds#', $page, $wait)) $this->CountDown($wait[1]);
        if (preg_match_all("#<span style='position:absolute;padding-left:(\d+)px;padding-top:\d+px;'>&\#(\d+);</span>#", $page, $temp)) {
            for ($i = 0; $i < 3; $i++) {
                for ($j = $i + 1; $j <= 3; $j++) {
                    if ($temp[1][$i] > $temp[1][$j]) {
                        $t = $temp[1][$i];
                        $temp[1][$i] = $temp[1][$j];
                        $temp[1][$j] = $t;
                        $t = $temp[2][$i];
                        $temp[2][$i] = $temp[2][$j];
                        $temp[2][$j] = $t;
                    }
                }
            }
            $captcha = "";
            for ($i = 0; $i <= 3; $i++) {
                $captcha.=$temp[2][$i] - 48;
            }
        }
        unset($post);
        $post['op'] = "download2";
        $post['id'] = $id;
        $post['rand'] = cut_str($page, 'name="rand" value="','"');
        $post['referer'] = $link;
        $post['method_free'] = "Free Download";
        $post['method_premium'] = "";
        $post['code'] = $captcha;
        $post['down_direct'] = "1";
        $page = $this->GetPage($link, 0, $post, $link);
        if (!preg_match('#http:\/\/.+10upload\.com(:\d+)?\/d\/[^"]+#', $page, $dlink)) html_error('Error: Download link not found???');
        $Url = parse_url($dlink[0]);
        if (!$fname) $fname = basename($Url['path']);
        $this->RedirectDownload($dlink[0], $fname, 0, 0, $link);
        exit();
    }
}
/*
 * 10upload.com free download plugin by Ruud v.Tony 26-07-2011
 * Taken captcha code from vdhdevil pyramidfiles plugin
 */
?>
