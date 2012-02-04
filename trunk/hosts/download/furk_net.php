<?php
if (!defined('RAPIDLEECH')) {
    require_once ("index.html");
    exit();
}

class furk_net extends DownloadClass {

    public function Download($link) {
        global $premium_acc;

        if (!$_REQUEST['step']) {
            $link = str_replace("https", "http", $link);
            $this->page = $this->GetPage($link);
            is_present($this->page, "This torrent is not ready for <strong>direct HTTP download</strong> yet.");
            $this->cookie = GetCookiesArr($this->page);
        }
        $this->link = $link;
        if ($_REQUEST['premium_acc'] == 'on' && (($_REQUEST['premium_user'] && $_REQUEST['premium_pass']) || ($premium_acc['furk_net']['user'] && $premium_acc['furk_net']['pass']))) {
            return $this->Premium();
        } else {
            return $this->Free();
        }
    }

    private function Free() {
        if (!preg_match("@action=\"([^\r\n\"]+)\" method=\"post\">@", $this->page, $fd)) html_error("Error[getFreeLink]");
        if (!preg_match('@<div id="free_dl_countdown">(\d+)<\/div>@', $this->page, $wait)) html_error("Error[getTimer]");

        $this->CountDown($wait[1]);

        $dlink = trim($fd[1]);
        $filename = basename(parse_url($dlink, PHP_URL_PATH));
        $this->RedirectDownload($dlink, $filename, $this->cookie, array('submit' => 'Download the file'));
        exit();
    }

    private function Premium() {
        global $premium_acc;

        $user = ($_REQUEST["premium_user"] ? trim($_REQUEST["premium_user"]) : $premium_acc ["furk_net"] ["user"]);
        $pass = ($_REQUEST["premium_pass"] ? trim($_REQUEST["premium_pass"]) : $premium_acc ["furk_net"] ["pass"]);
        if (empty($user) || empty($pass)) html_error("Login failed, username[$username] or password[$password] is empty!");

        $Url = 'https://www.furk.net';
        $post['url'] = '';
        $post['gigya_uid'] = '';
        $post['login'] = $user;
        $post['pwd'] = $pass;
        $check = $this->GetPage($Url . '/api/login/login/', 0, $post, $Url . "/login/", 0, 1);
        $this->cookie = GetCookiesArr($check, $this->cookie);
        if (preg_match('@\{"(\w+)":"(\w+)","(\w+)":"([^"]+)"\}@', $check, $match)) {
            if ($match[2] == 'ok') {
                switch ($match[4]) {
                    case '/billing':
                        $this->changeMesg(lang(300) . "<br />Furk.net Free Account");
                        $this->page = $this->GetPage($this->link, $this->cookie, 0, $this->link);

                        return $this->Free();
                        break;

                    case '/users/account':
                        $this->changeMesg(lang(300) . "<br />Furk.net Premium Account");

                        $this->page = $this->GetPage($this->link, $this->cookie, 0, $this->link);
                        if (!preg_match("@<a class=\"dl_link button-large\" href=\"([^\r\n\"]+)\"@", $this->page, $dl)) html_error("Error[getPremiumDownloadLink!]");
                        $dlink = trim($dl[1]);
                        $filename = basename(parse_url($dlink, PHP_URL_PATH));
                        $this->RedirectDownload($dlink, $filename, $this->cookie);
                        break;
                }
            }
            is_present($match[2], 'error', $match[3]);
        }
    }

}

// Created by rajmalhotra on 05 Dec 09
// Fixed countdown by Th3-822 on 31 Dec 10
// Add premium and free account also implement new function for example by Ruud v.Tony 04-02-2012
?>