<?php
if (!defined('RAPIDLEECH')) {
    require_once('index.html');
    exit();
}

class real_debrid_com extends DownloadClass {

    public function Download($link) {
        global $premium_acc;
        $user = urlencode($_REQUEST["premium_user"] ? $_REQUEST["premium_user"] : $premium_acc["realdebrid_com"]["user"]);
        $pass = urlencode($_REQUEST["premium_pass"] ? $_REQUEST["premium_pass"] : $premium_acc["realdebrid_com"]["pass"]);
        if (empty($user) || empty($pass)) html_error('Username or password is empty, you need to insert your login detail!');

        $posturl = 'http://real-debrid.com/';
        $page = $this->GetPage($posturl . "ajax/login.php?user=$user&pass=$pass", "lang=en", 0, $posturl . "\r\nX-Requested-With: XMLHttpRequest");
        $cookie = GetCookies($page)."; lang=en";
        is_present($page, 'Your login informations are incorrect !');
        //check account
        $page = $this->GetPage($posturl."account", $cookie, 0, $posturl);
        is_present($page, 'A dedicated server has been detected and your account will not be Premium on this IP address.');
        is_present($page, '<strong>Free</strong>', 'Account Free, login not validated!');
        //start download link
        if (preg_match('#'.$posturl.'[?]([^\r\n]+)#i', $link, $ck)) {
            $check = $ck[1];
            if (stristr($check, "|")) {
                $arr = explode('|', $check);
                $urlhost = urlencode($arr[0]);
                $password = $arr[1];
            } else { //no password input
                $urlhost = urlencode($check);
            }
        } else {
            html_error('Format link unknown, please input like this http://real-debrid.com/?http://www.megaupload.com/?d=VLV1UJ0C');
        }

        $page = $this->GetPage($posturl."ajax/deb.php?link=$urlhost&password=$password&remote=0", $cookie, 0, $posturl."downloaders\r\nX-Requested-With: XMLHttpRequest");
        is_present($page, 'No server is available for this hoster.');
        is_present($page, 'Your file is unavailable on the hoster.');
        if (substr($page, strpos($page, "\r\n\r\n") + 10) == '0') html_error('Error: This host is not supported!');
        if (!preg_match('@http:\/\/s(\d+)?\.real-debrid\.com\/dl\/[^|\r|\n|\'"]+@i', $page, $dl)) html_error("Error: Download link not found!");
        $dlink = trim($dl[0]);
        $filename = parse_url($dlink);
        $FileName = basename($filename['path']);
        $this->RedirectDownload($dlink, $FileName, $cookie, 0, $posturl."downloaders");
        $this->CheckBack($dlink);
    }

    public function  CheckBack($content) {
        if (!strpos($content, "ontent-Disposition: attachment; ")) {
            html_error('This file is password protected, please input link with format http://real-debrid.com/?http://www.megaupload.com/?d=VLV1UJ0C | password');
        }
        return;
    }
}

// real-debrid download plugin by Ruud v.Tony 23-10-2011

?>
