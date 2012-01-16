<?php
if (!defined('RAPIDLEECH')) {
    require_once ("index.html");
    exit();
}

class filesflash_com extends DownloadClass {

    public function Download($link) {
        global $premium_acc;

        $page = $this->GetPage($link);
        is_present($page, 'That file is not available for download');
        $CookieArr = GetCookiesArr($page);
        $cookie = CookiesToStr($CookieArr);
        $url = "http://filesflash.com/";
        $email = ($_REQUEST["premium_user"] ? $_REQUEST["premium_user"] : $premium_acc["filesflash_com"]["user"]);
        $pass = ($_REQUEST["premium_pass"] ? $_REQUEST["premium_pass"] : $premium_acc["filesflash_com"]["pass"]);
        if (!empty($email) && !empty($pass)) {
            $post = array();
            $post['email'] = urlencode($email);
            $post['password'] = urlencode($pass);
            $post['submit'] = 'Login';
            $page = $this->GetPage($url . "login.php", $cookie, $post, $url);
            is_present($page, "Invalid email address or password.");
            $cookie = CookiesToStr(array_merge($CookieArr, GetCookiesArr($page)));
            // check account
            $page = $this->GetPage($url . "myaccount.php", $cookie, 0, $url . "index.php");
            is_present($page, "<td>Premium Status:</td><td>Not Premium", "Account Status: Free");
            // start download link
            $page = $this->GetPage($link, $cookie);
            if (!preg_match("@Location: (http:\/\/[^\r\n]+)@i", $page, $dl)) html_error("Error: Download Link [PREMIUM] not found!");
            $dlink = trim($dl[1]);
            $filename = basename(parse_url($dlink, PHP_URL_PATH));
            $this->RedirectDownload($dlink, $filename, $cookie);
        } else {
            $post = array();
            $post['token'] = cut_str($page, 'name="token" value="', '"');
            $post['freedl'] = " Free Download ";
            $page = $this->GetPage($url . 'freedownload.php', $cookie, $post, $link);
            is_present($page, "Your link has expired. Please try again.");
            if (!preg_match('/count=(\d+)/', $page, $wait)) html_error("Error: Timer not found!");
            $this->CountDown($wait[1]);
            $dlink = cut_str($page, '<div id="link" style="display:none"><a href="', '">');
            if (!$dlink) html_error("Error: Download Link [FREE] not found???");
            $FileName = basename(parse_url($dlink, PHP_URL_PATH));
            $this->RedirectDownload($dlink, $FileName, $cookie, 0, $url . 'freedownload.php');
            exit();
        }
    }
}

/*
 * Filesflash free download plugin by Ruud v.Tony 26/07/2011
 * Updated to support premium by Ruud v.Tony 11-01-2012
 * Small fix in premium setting so it wont mess up with other by Ruud v.Tony 12-01-2012
 */
?>
