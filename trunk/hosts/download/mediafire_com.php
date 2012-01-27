<?php

if (!defined('RAPIDLEECH')) {
    require_once("index.html");
    exit;
}

class mediafire_com extends DownloadClass {

    public function Download($link) {
        if (isset($_POST['mfpassword']) && ($_POST['mfpassword'] != "")) {
            $Cookies = urldecode($_POST['cookie']);
            $link = urldecode($_POST['link']);
            $page = $this->GetPage($link, $Cookies, array("downloadp" => $_POST['mfpassword']), $link);
        } else {
            $link = preg_replace("#http://(www.)?mediafire.com/(download.php)?#", "http://www.mediafire.com/", $link);
            $page = $this->GetPage($link);
            is_present($page, "error.php?errno=320", "Link is not available");
            $Cookies = GetCookies($page);
        }
        if (strpos($page, 'name="downloadp" id="downloadp"')) {
            $DefaultParam = $this->DefaultParamArr($link, $Cookies);
            $html = '<form action="index.php" method="POST">';
            foreach ($DefaultParam as $key => $value) {
                $html.='<input type="hidden" name="' . $key . '" value="' . $value . '"/>';
            }
            $html.='Enter your password here </br><input type="text" name="mfpassword" value="" placeholder="Enter your password here" autofocus="autofocus" required="required" /><input type="submit" name="action" value="Submit"/></form>';
            echo $html;
            exit;
        }
        if (preg_match('@Location: (http:\/\/[^\r\n]+)@i', $page, $dl) || preg_match('%<div class="download_link" style=".*" id=".*" name=".*"> <a href="(http:\/\/[^\r\n]+)" onclick=%U', $page, $dl)) {
            $dlink = trim($dl[1]);
            $this->RedirectDownload($dlink, "Mediafire.com", $Cookies);
            exit;
        } else {
            html_error("Error: Download link [FREE] not found!");
        }
    }

    private function get_mf($page) {
        preg_match_all('%<div class="download_link" style=".*" id=".*" name=".*"> <a href="(.*)" onclick=%U', $page, $match);
        $dl = array();
        $index = array();
        $re = '';
        $page = explode(';', trim(cut_str($page, 'unescape', 'eval')));
        $a = trim(cut_str($page[0], "('", "')"));
        $b = explode('=', $page[1]);
        $b = intval($b[1]);
        $c = (cut_str($page[4], '16)', '))'));
        for ($i = 0; $i < $b; $i++) {
            $d = HexDec(substr($a, $i * 2, 2));
            eval("\$d = \$d" . $c . ";");
            $re .= chr($d);
        }
        $pro = cut_str($re, "parseInt(\$oThis.css('z-index')) ", "));");
        for ($o = 0; $o < count($match[1]); $o++) {
            eval("\$key = \$match[1][\$o]" . $pro . ";");
            $dl[$key] = $match[2][$o];
            $index[] = $key;
        }
        sort($index);
        return $dl[$index[count($index) - 1]];
    }
}

/*
 * credit to farizemo [at] rapidleech forum
 * by vdhdevil
 * remove additional function for temporary fix until get finished - Ruud v.Tony 06-01-2011
 * fix for shared premium link by Ruud v.Tony 23-01-2012
 */
?>