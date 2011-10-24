<?php
if (!defined('RAPIDLEECH')) {
    require_once ("index.html");
    exit ();
}

class oron_com extends DownloadClass {

    public function Download($link) {
        global $premium_acc;
        if ($_POST['step'] == "Captcha") {
            $this->DownloadPremium($link, 1);
        }else $cap=0;
        if (($_REQUEST ["premium_acc"] == "on" && $_REQUEST ["premium_user"] && $_REQUEST ["premium_pass"]) || ($_REQUEST ["premium_acc"] == "on" && $premium_acc ["oron_com"] ["user"] && $premium_acc ["oron_com"] ["pass"])) {
            $this->DownloadPremium($link, $cap);
        } elseif ($_POST['step'] == "1") {
            $this->DownloadFree($link);
        } else {
            $this->Retrieve($link);
        }
    }

    private function ContinueDownload($link) {
        $post = array();
        $post['login'] = $_REQUEST["premium_user"] ? trim($_REQUEST["premium_user"]) : $premium_acc ["oron_com"] ["user"];
        $post['password'] = $_REQUEST["premium_pass"] ? trim($_REQUEST["premium_pass"]) : $premium_acc ["oron_com"] ["pass"];
        $post['recaptcha_challenge_field'] = $_POST['recaptcha_challenge_field'];
        $post['recaptcha_response_field'] = $_POST['captcha'];
        $post['op'] = "login";
        $post['redirect'] = $link;
        $post['rand'] = $_POST['rand'];
    }

    private function DownloadFree($link) {
        $post = array();
        $post['op'] = 'download2';
        $post['id'] = $_POST['id'];
        $post['rand'] = $_POST['rand'];
        $post['referer'] = $_POST['link_referer'];
        $post['method_free'] = ' Regular Download ';
        $post['method_premium'] = '';
        $post['recaptcha_challenge_field'] = $_POST['recaptcha_challenge_field'];
        $post['recaptcha_response_field'] = $_POST['captcha'];
        $post['down_direct'] = '1';
        $link = urldecode($_POST['link']);
        $page = $this->GetPage($link, 0, $post, $link);
        is_present($page, "Wrong captcha", "Wrong captcha . Go to main page and reattempt", 0);
        is_present($page, "Expired session", "Expired session . Go to main page and reattempt", 0);

        if (!preg_match('%href="(.*)" class="atitle">%', $page, $dl)) html_error('Error: Free download link can\'t be found!');
        $Url = parse_url($dl[1]);
        $FileName = basename($Url['path']);
        $this->RedirectDownload($dl[1], $FileName, 0, 0, $link);
        exit();
    }

    private function Retrieve($link) {
        $page = $this->GetPage($link);
        is_present($page, "403 Forbidden", "Oron banned this server");
        is_present($page, "File Not Found", "File could not be found due to its possible expiration or removal by the file owner.");
        is_present($page, "This file can only be downloaded by Premium Users.");

        $id = cut_str($page, 'name="id" value="', '"');
        $fname = cut_str($page, 'name="fname" value="', '"');

        $post = array();
        $post['op'] = 'download1';
        $post['usr_login'] = '';
        $post['id'] = $id;
        $post['fname'] = $fname;
        $post['referer'] = $link;
        $post['method_free'] = ' Regular Download ';
        $page = $this->GetPage($link, 0, $post, $link);
        if (preg_match('%<p class="err">(.*)<br>%', $page, $msg)) html_error($msg[1]);
        if (preg_match('#(\d+)</span> seconds#', $page, $wait)) $this->CountDown($wait[1]);

        $rand = cut_str($page, 'name="rand" value="', '"');
        $k = cut_str($page, 'api/challenge?k=', '"');
        $page = $this->GetPage("http://www.google.com/recaptcha/api/challenge?k=$k");
        $ch = cut_str($page, "challenge : '", "'");
        $img = "http://www.google.com/recaptcha/api/image?c=" . $ch;
        $page = $this->GetPage($img);
        $pass_img = substr($page, strpos($page, "\r\n\r\n") + 4);
        $imgfile = DOWNLOAD_DIR . "oron_captcha.jpg";
        if (file_exists($imgfile)) {
            unlink($imgfile);
        }
        write_file($imgfile, $pass_img);

        $data = $this->DefaultParamArr($link);
        $data['step'] = '1';
        $data['id'] = $id;
        $data['rand'] = $rand;
        $data['link_referer'] = $link;
        $data['recaptcha_challenge_field'] = $ch;
        $this->EnterCaptcha($imgfile, $data, 10);
        exit();
    }

    private function DownloadPremium($link, $cap) {
        global $premium_acc, $options;
        $Referer = "http://oron.com/";
        $post = array();
        $post['login'] = $_REQUEST["premium_user"] ? trim($_REQUEST["premium_user"]) : $premium_acc ["oron_com"] ["user"];
        $post['password'] = $_REQUEST["premium_pass"] ? trim($_REQUEST["premium_pass"]) : $premium_acc ["oron_com"] ["pass"];
        $post['op'] = "login";
        $post['redirect'] = $link;
        $post['rand'] = "";
        if ($cap == 1) {
            $post['rand'] = $_POST['rand'];
            $post['recaptcha_challenge_field'] = $_POST['recaptcha_challenge_field'];
            $post['recaptcha_response_field'] = $_POST['captcha'];
        }
        $page = $this->GetPage("http://oron.com/login", 0, $post, "http://oron.com/login.html");
        if (strpos($page, "6LdzWwYAAAAAAAzlssDhsnar3eAdtMBuV21rqH2N") && ($cap == 0)) {//recaptcha
            $rand = cut_str($page, 'name="rand" value="', '"');
            $page = $this->GetPage("http://www.google.com/recaptcha/api/challenge?k=6LdzWwYAAAAAAAzlssDhsnar3eAdtMBuV21rqH2N");
            $cookie = GetCookies($page);
            $ch = cut_str($page, "challenge : '", "'");
            $img = "http://www.google.com/recaptcha/api/image?c=" . $ch;
            $page = $this->GetPage($img);
            $headerend = strpos($page, "\r\n\r\n");
            $pass_img = substr($page, $headerend + 4);
            write_file($options['download_dir'] . "oron_captcha.jpg", $pass_img);
            $data = $this->DefaultParamArr($link);
            $data['step'] = "Captcha";
            $data['rand'] = $rand;
            $data['recaptcha_challenge_field'] = $ch;
            $this->EnterCaptcha($options['download_dir'] . "oron_captcha.jpg", $data, 7);
            exit;
        }
        is_present($page, "403 Forbidden", "Oron banned this server");
        is_present($page, "Incorrect Login or Password", "Incorrect Login or Password");
        $cookie = GetCookies($page);

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
        $cookie.="; " . GetCookies($page);
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
  UPDATE BY Ruud v.Tony 29-Sept-2011 [FIX] Free Download code
 * \************************************************* */
?>