<?php
if (!defined('RAPIDLEECH')) {
    require_once ('index.html');
    exit();
}

class midupload_com extends DownloadClass {
    
    public function Download($link) {
        $page = $this->GetPage($link, "lang=english");
        is_present($page, 'The file you were looking for could not be found, sorry for any inconvenience.');
        $form = cut_str($page, '<Form method="POST" action=\'\'>', '</Form>'); //test this with var_dump($form) or textarea($form), that post data we need to send to the filehost
        if (!preg_match_all('%<input type="hidden" name="([^"]+)" value="([^"]+)?">%', $form, $one) || !preg_match_all('%<input class="btn" type="submit" name="(\w+_free)" value="([^"]+)">%', $form, $two)) html_error("Error [Post Data 1 not found!]");
        $match = array_merge(array_combine($one[1], $one[2]), array_combine($two[1], $two[2]));
        $post = array();
        foreach ($match as $k => $v) {
            $post[$k] = $v;
        }
        $page = $this->GetPage($link, "lang=english", $post, $link);
        is_present($page, cut_str($page, '<p class="err">', '<br>')); // this will display "You have to wait bla...bla...bla..."
        unset($post);
        $form = cut_str($page, '<Form name="F1" method="POST"', '</Form>');
        if (preg_match('/(\d+)<\/span> seconds/', $form, $w)) $this->CountDown ($w[1]);
        if (!preg_match_all('%<input type="hidden" name="([^"]+)" value="([^"]+)?">%', $form, $ck)) html_error('Error [Post Data 2 not found!]');
        if (!preg_match_all("#<span style='[^\d]+(\d+)[^\d]+\d+\w+;'>\W+(\d+);</span>#", $form, $temp)) html_error('Error [Captcha Data not found!]');
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
        $match = array_merge(array_combine($ck[1], $ck[2]), array('code' => $captcha));
        $post = array();
        foreach ($match as $k => $v) {
            $post[$k] = $v;
        }
        $page = $this->GetPage($link, "lang=english", $post, $link);
        is_present($page, cut_str($page, '<p class="err">', '</p>')); // incase the captcha layout have been broken, we can fix that!
        is_present($page, cut_str($page, '<font class="err">', '<br>')); // same error message "You have to wait bla...bla...bla..."
        if (!preg_match('/Location: (http:\/\/[^\r\n]+)/i', $page, $dl)) html_error('Error [Download Link not found!]');
        $filename = basename(parse_url($dl[1], PHP_URL_PATH));
        $this->RedirectDownload($dl[1], $filename, "lang=english", 0, $link);
        exit();
    }
}
// download plug-in writted by rajmalhotra  12 Dec 2009
// fixed by Ruud v.Tony 10-02-2012
?>