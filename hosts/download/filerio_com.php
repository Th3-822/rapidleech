<?php
if (!defined('RAPIDLEECH')) {
    require_once('index.html');
    exit;
}

class filerio_com extends DownloadClass {

    public function Download($link) {
        if (strstr($link, "filekeen.com/")) {
            $link = str_replace("filekeen.com/", "filerio.com/", $link);
        }
        $page = $this->GetPage($link);
        is_present($page, "File Not Found");

        $id = cut_str($page, 'name="id" value="','"');
        $FileName = cut_str($page, 'name="fname" value="','"');

        $post = array();
        $post['op'] = "download1";
        $post['usr_login'] = "";
        $post['id'] = $id;
        $post['fname'] = $FileName;
        $post['referer'] = $link;
        $post['method_free'] = "Generate Download Link";
        $page = $this->GetPage($link, 0, $post, $link);
        if (preg_match_all("#<span style='[^\d]+(\d+)[^\d]+\d+\w+;'>\W+(\d+);</span>#", $page, $temp)) {
            for ($i=0;$i<count($temp[1])-1;$i++){
                for ($j=$i+1;$j<count($temp[1]);$j++){
                    if ($temp[1][$i]>$temp[1][$j]){
                        $n=1;
                        do {
                            $tmp=$temp[$n][$i];
                            $temp[$n][$i]=$temp[$n][$j];
                            $temp[$n][$j]=$tmp;
                            $n++;
                        } while ($n<=2);
                    }
                }
            }
            $captcha="";
            foreach($temp[2] as $value) {
                $captcha.=chr($value);
            }
        }
        $rand = cut_str($page, 'name="rand" value="','"');
        unset ($post);
        $post['op'] = "download2";
        $post['id'] = $id;
        $post['rand'] = $rand;
        $post['referer'] = $link;
        $post['method_free'] = "Generate Download Link";
        $post['method_premium'] = "";
        $post['code'] = $captcha;
        $post['down_script'] = "1";
        $page = $this->GetPage($link, 0, $post, $link);
        is_present($page, "Wrong captcha");
        if (!preg_match('@http:\/\/[\d.]+(:\d+)?\/d\/[^|\r|\n|"]+@i', $page, $dl)) {
            html_error("Error: Download link not found!");
        }
        $dlink = trim($dl[0]);
        $FileName = basename(parse_url($dlink, PHP_URL_PATH));
        $this->RedirectDownload($dlink, $FileName, 0, 0, $link);
        exit();
    }
}
/* 
 * imported from the existing filekeen code configuration by Ruud v.Tony 21-12-2011
 * updated to include filekeen.com in link by Ruud v.Tony 13-01-2012
 */

?>
