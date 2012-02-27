<?php
if (!defined('RAPIDLEECH')) {
    require_once ("index.html");
    exit();
}

class speedyshare_com extends DownloadClass {

    public function Download($link) {
        global $premium_acc;
        
        if (!$_REQUEST['step']) {
            $this->page = $this->GetPage($link);
            if (preg_match('@Location: (http:\/\/www\.speedyshare\.com\/[^\r\n]+)@i', $this->page, $redir)) {
                $link = trim($redir[1]);
                $this->page = $this->GetPage($link);
            }
            is_present($this->page, 'File not found. It has been deleted or it never existed at all.');
            $this->cookie = GetCookiesArr($this->page);
        }
        if ($_REQUEST['premium_acc'] == 'on' && (($_REQUEST['premium_user'] && $_REQUEST['premium_pass'])||($premium_acc['speedyshare_com']['user'] && $premium_acc['speedyshare_com']['pass']))) {
            return $this->Premium($link);
        } else {
            return $this->Free($link);
        }
    }
    
    private function Free($link) {
        is_present($this->page, 'Private file ACCESS DISABLED');
        is_present($this->page, 'This file is not available for public download.');
        if (!preg_match("%<a class=downloadfilename href='([^']+)'>%", $this->page, $rd)) html_error('Error [Redirect Link FREE not found!]');
        $rlink = 'http://www.speedyshare.com'.$rd[1];
        $page = $this->GetPage($rlink, $this->cookie, 0, $link);
        if (!preg_match('/Location: (http:\/\/[^\r\n]+)/i', $page, $dl)) html_error('Error [Download Link FREE not found!]');
        $dlink = trim($dl[1]);
        $filename = basename(parse_url($dlink, PHP_URL_PATH));
        $this->RedirectDownload($dlink, $filename, $this->cookie, 0, $link);
        exit();
    }
    
    private function Premium($link) {
        
        $cookie = $this->login();
        $page = $this->GetPage($link, $cookie);
        is_present($page, 'File not found. It has been deleted or it never existed at all.');
        if (!stripos($page, 'Location:')) {
            if (!preg_match("%<a class=downloadfilename href='([^']+)'>%", $this->page, $rd)) html_error('Error [Redirect Link PREMIUM not found!]');
            $rlink = 'http://www.speedyshare.com'.$rd[1];
            $page = $this->GetPage($rlink, $cookie);
        }
        $dlink = trim(cut_str($page, "ocation: ", "\r\n"));
        if (empty($dlink)) html_error('Error [Download Link PREMIUM not found!]');
        $filename = basename(parse_url($dlink, PHP_URL_PATH));
        $this->RedirectDownload($dlink, $filename, $cookie);
    }
    
    private function login() {
        global $premium_acc;
        
        $user = ($_REQUEST["premium_user"] ? trim($_REQUEST["premium_user"]) : $premium_acc ["speedyshare_com"] ["user"]);
        $pass = ($_REQUEST["premium_pass"] ? trim($_REQUEST["premium_pass"]) : $premium_acc ["speedyshare_com"] ["pass"]);
        if (empty($user) || empty($pass)) html_error("Login failed, $user [user] or $pass [password] is empty!");
        
        $post['redir'] = urlencode(cut_str($link, "http://www.speedyshare.com", "\r\n"));
        $post['login'] = $user;
        $post['pass'] = $pass;
        $post['remember'] = 'on';
        $page = $this->GetPage('https://www.speedyshare.com/login.php', $this->cookie, $post, $link);
        is_present($page, 'Error occured', 'Invalid username or password!');
        $cookie = GetCookiesArr($page, $this->cookie);
        if (!$cookie['spl']) html_error('Error [Invalid account]');
        
        return $cookie;
    }

}

// Created by rajmalhotra on 20 Jan 2010
// rebuild by Ruud v.Tony 15-02-2012
?>