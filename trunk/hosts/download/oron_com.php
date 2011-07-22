<?php

if (!defined('RAPIDLEECH')) {
    require_once ("index.html");
    exit ();
}

class oron_com extends DownloadClass {

    public function Download($link) {
        global $premium_acc;
        if (($_REQUEST ["premium_acc"] == "on" && $_REQUEST ["premium_user"] && $_REQUEST ["premium_pass"]) || ($_REQUEST ["premium_acc"] == "on" && $premium_acc ["oron_com"] ["user"] && $premium_acc ["oron_com"] ["pass"])) {
            $this->DownloadPremium($link);
        } elseif ($_POST['step'] == "1") {
            $this->DownloadFree($link);
        } else {
            $this->Retrieve($link);
        }
    }

    private function DownloadFree($link) {
        global $Referer;
        $post['op'] = 'download2';
        $post['method_free'] = 'Free Download';
        $post['method_premium'] = '';
        $post['down_direct'] = '1';
        $post['referer'] = $link;
        $post['recaptcha_challenge_field'] = $_POST['recaptcha_challenge_field'];
        $post['recaptcha_response_field'] = $_POST['captcha'];
        $post['id'] = $_POST['id'];
        $post['rand'] = $_POST['rand'];
        $page = $this->GetPage($link, 0, $post, $link);
        is_present($page, "Wrong captcha", "Wrong captcha . Go to main page and reattempt", 0);
        is_present($page, "Expired session", "Expired session . Go to main page and reattempt", 0);
        $snap = cut_str($page, 'Filename:', '</table>');
        $dwn = cut_str($snap, 'href="', '"');
        if (!$dwn)
            html_error("Error getting download link", 0);
        $Url = parse_url($dwn);
        $FileName = basename($dwn);
        $loc = "$PHP_SELF?filename=" . urlencode($FileName) .
                "&host=" . $Url ["host"] .
                "&port=" . $Url ["port"] .
                "&path=" . urlencode($Url ["path"] . ($Url ["query"] ? "?" . $Url ["query"] : "")) .
                "&referer=" . urlencode($Referer) .
                "&email=" . ($_GET ["domail"] ? $_GET ["email"] : "") .
                "&partSize=" . ($_GET ["split"] ? $_GET ["partSize"] : "") .
                "&method=" . $_GET ["method"] .
                "&proxy=" . ($_GET ["useproxy"] ? $_GET ["proxy"] : "") .
                "&saveto=" . $_GET ["path"] .
                "&link=" . urlencode($LINK) .
                ($_GET ["add_comment"] == "on" ? "&comment=" . urlencode($_GET ["comment"]) : "") .
                $auth .
                ($pauth ? "&pauth=$pauth" : "");
        insert_location($loc);
    }

    private function Retrieve($link) {
        global $options;
        $page = $this->GetPage($link);
        is_present($page,"403 Forbidden","Oron banned this server");
        is_present($page, "File Not Found", "File Not Found", 0);
        $id = cut_str($page, 'name="id" value="', '"');
        $fname = cut_str($page, 'name="fname" value="', '"');
        $post = array();
        $post['op'] = 'download1';
        $post['usr_login'] = '';
        $post['id'] = $id;
        $post['fname'] = $fname;
        $post['referer'] = '';
        $post['method_free'] = ' Free Download ';
        $page = $this->GetPage($link, 0, $post, $link);
        insert_timer(60);
        $rand = cut_str($page, 'name="rand" value="', '"');
        $referer = cut_str($page, 'referer" value="', '"');
        $down_direct = cut_str($page, 'own_direct" value="', '"');
        $page = $this->GetPage("http://www.google.com/recaptcha/api/challenge?k=6LdzWwYAAAAAAAzlssDhsnar3eAdtMBuV21rqH2N");
        is_present($page, "Expired session", "Expired session . Go to main page and reattempt", 0);
        $cookie = GetCookies($page);
        $ch = cut_str($page, "challenge : '", "'");
        $img = "http://www.google.com/recaptcha/api/image?c=" . $ch;
        $page = $this->GetPage($img);
        $headerend = strpos($page, "\r\n\r\n");
        $pass_img = substr($page, $headerend + 4);
        write_file($options['download_dir'] . "oron_captcha.jpg", $pass_img);
        $data = $this->DefaultParamArr($link);
        $data['step'] = 1;
        $data['id'] = $id;
        $data['rand'] = $rand;
        $data['recaptcha_challenge_field'] = $ch;
        $this->EnterCaptcha($options['download_dir'] . "oron_captcha.jpg", $data, 10);
        exit();
    }

    private function DownloadPremium($link) {
        global $premium_acc;
        $Referer = "http://oron.com/";
        $post = array();
        $post['login'] = $_REQUEST["premium_user"] ? trim($_REQUEST["premium_user"]) : $premium_acc ["oron_com"] ["user"];
        $post['password'] = $_REQUEST["premium_pass"] ? trim($_REQUEST["premium_pass"]) : $premium_acc ["oron_com"] ["pass"];
        $post['op'] = "login";
        $post['redirect'] = $link;
        $post['rand']="";
        $page = $this->GetPage("http://oron.com/login", 0, $post, "http://oron.com/login.html");
        is_present($page,"403 Forbidden","Oron banned this server");
        $cookie = GetCookies($page);
        is_notpresent($cookie, "login", "Login Failed , Bad username/password combination");
        
        $page = $this->GetPage($link, $cookie, 0, $Referer);
        is_present($page, "File could not be found due to its possible expiration or removal by the file owner.", "File could not be found due to its possible expiration or removal by the file owner.");
        is_present($page, "You have reached the download limit: 15000 Mb ", "You have reached the download limit: 15000 Mb ");
        $id = cut_str($page, 'name="id" value="', '"');
        $rand = cut_str($page, 'name="rand" value="', '"');
        $referer = cut_str($page, 'referer" value="', '"');
        $down_direct = cut_str($page, 'own_direct" value="', '"');
        $Referer = $link;
        unset($post);
        $post['op'] = 'download2';
        $post['id'] = $id;
        $post['rand'] = $rand;
        $post['referer'] = $Referer;
        $post['method_free'] = '';
        $post['method_premium'] = '1';
        $post['down_direct'] = '1';
        $page = $this->GetPage($link, $cookie, $post, $Referer);
        $cookie.="; ".GetCookies($page);
        if (preg_match('#http://(\w+).oron.com[^"]+#', $page, $prelink)) {
            $FileName = basename($prelink[0]);
            $this->RedirectDownload($prelink[0], $FileName, $cookie);
        }
        exit();
    }

}

/* * ************************************************\
  WRITTEN BY KAOX 03-oct-09
  UPDATE BY KAOX 06-oct-09 ADD SUPPORT TO CAPTCHA
  UPDATE BY Slider324 17-oct-10 UPDATE SUPPORT TO CAPTCHA
  UPDATE BY vdhdevil  04-Nov-10 UPDATE SUPPORT PREMIUM ACCOUNT
  UPDATE BY vdhdevil  19-March-10 [FIX]Login premium account
 * \************************************************* */
?>