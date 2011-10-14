<?php
if (!defined('RAPIDLEECH')) {
    require_once("index.html");
    exit;
}

class sendspace_com extends DownloadClass {

    public function Download($link) {
        global $premium_acc, $Referer;
        if (!$_REQUEST['step']) {
            $page = $this->GetPage($link);
            if (preg_match('@Location: (http:\/\/.+sendspace\.com\/pro\/[^|\r|\n]+)@i', $page, $temp)) {
                $link = trim($temp[1]);
            }
            unset($page);
        }
        if (($_REQUEST['premium_acc'] == 'on' && $_REQUEST['premium_user'] && $_REQUEST['premium_pass']) || ($_REQUEST['premium_acc'] == 'on' && $premium_acc['sendspace']['user'] && $premium_acc['sendspace']['pass'])) {
            $this->Login($link);
        } else { //Free download?
            $cookie = GetCookies($page);
            $this->StartDownload($this->GetPage($link, $cookie, 0, $Referer), $link, $cookie);
        }
    }

    private function StartDownload($page, $link, $cookie) {
        global $Referer;

        is_present($page, 'Sorry, the file you requested is not available.');
        if (!preg_match('@http:\/\/fs(\d+)?n(\d+)?\.sendspace\.com\/[^|\r|\n|\'"]+@i', $page, $dl)) html_error("Error : Download link not found, plugin need to be updated!");
        $dlink = html_entity_decode(urldecode(trim($dl[0])), ENT_QUOTES, 'UTF-8');
        $filename = parse_url($dlink);
        $FileName = basename($filename['path']);
        $this->RedirectDownload($dlink, $FileName, $cookie, 0, $Referer);
        exit();
    }

    private function Login($link) {
        global $premium_acc;

        $pA = ($_REQUEST["premium_user"] && $_REQUEST["premium_pass"] ? true : false);
        $user = ($pA ? $_REQUEST["premium_user"] : $premium_acc["sendspace"]["user"]);
        $pass = ($pA ? $_REQUEST["premium_pass"] : $premium_acc["sendspace"]["pass"]);
        if (empty($user) || empty($pass)) {
            html_error("Login Failed: email or password is empty. Please check login data.");
        }
        $post['action'] = 'login';
        $post['submit'] = 'login';
        $post['target'] = urlencode('%2F');
        $post['action_type'] = 'login';
        $post['remember'] = '1';
        $post['username'] = $user;
        $post['password'] = $pass;
        $post['remember'] = 'on';
        $page = $this->GetPage('http://www.sendspace.com/login.html', 0, $post, 'http://www.sendspace.com/');
        $cookie = GetCookies($page);
        is_present($cookie, "ssal=deleted", "Login incorrect retype your username or password correctly");

        $page = $this->GetPage('http://www.sendspace.com/mysendspace/myindex.html', $cookie);
        is_notpresent($page, 'Your membership is valid', 'Account Free, login not validated!');

        return $this->StartDownload($this->GetPage($link, $cookie), $link, $cookie);
    }

}

// Use PREMIUM? [szalinski 09-May-09]
// fix free download by kaox 19-dec-2009
// Fix premium & free by Ruud v.Tony 03-Okt-2011
?>