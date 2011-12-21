<?php

if (!defined('RAPIDLEECH')) {
    require_once("index.html");
    exit;
}

class mediafire_com extends DownloadClass {

    private function get_mf($page) {
        preg_match_all('%<div class="download_link" style=".*;z-index:(.*)" id=".*" name=".*"> <a href="(.*)" onclick=%U', $page, $match);
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

    public function Download($link) {
        if (isset($_POST['mfpassword']) && ($_POST['mfpassword'] != "")) {
            $Cookies=$_POST['cookie'];
            $page=$this->GetPage($link, $Cookies, array("downloadp"=>$_POST['mfpassword']), $link);
            $dlink=$this->get_mf($page);
            $this->RedirectDownload($dlink, "Mediafire.com",$Cookies);
            exit;
        } else {
            $link = preg_replace("#http://(www.)?mediafire.com/(download.php)?#", "http://www.mediafire.com/", $link);
            $page = $this->GetPage($link);
            is_present($page, "error.php?errno=320","Link is not available");
            $Cookies = GetCookies($page);
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
            $dlink = $this->get_mf($page);
            $this->RedirectDownload($dlink, "Mediafire.com", $Cookies);
            exit;
        }
    }

}
/*
 * credit to farizemo [at] rapidleech forum
 * by vdhdevil
 */
?>