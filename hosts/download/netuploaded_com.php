<?php
if (!defined("RAPIDLEECH")) {
    require_once("index.html");
    exit();
}

class netuploaded_com extends DownloadClass {
    
    public function Download($link) {
        global $premium_acc;
        
        $this->link = $link;
        if (!$_REQUEST['step']) {
            $this->page = $this->GetPage($this->link, "lang=english");//keep page in english
            is_present($this->page, "The file you were looking for could not be found, sorry for any inconvenience.");
            $this->cookie = GetCookies($this->page). "; lang=english";
        }
        if ($_REQUEST['premium_acc'] == 'on' && (($_REQUEST['premium_user'] && $_REQUEST['premium_pass'])||($premium_acc['netuploaded_com']['user'] && $premium_acc['netuploaded_com']['pass']))) {
            return $this->Premium();
        } else {
            return $this->Free();
        }
    }
    
    private function Free() {
        
        $form = cut_str($this->page, "<Form method=\"POST\" action=''>", '</Form>');
        if (!preg_match_all('@<input type="hidden" name="([^"]+)" value="([^"]+)?">@', $form, $one) || !preg_match_all('@<input type="submit" name="(\w+_free)" value="([^"]+)?">@', $form, $two)) html_error("Error: Post Data 1 [FREE] not found!");
        $match = array_merge(array_combine($one[1], $one[2]), array_combine($two[1], $two[2]));
        $post = array();
        foreach ($match as $key => $value) {
            $post[$key] = $value;
        }
        $this->page = $this->GetPage($this->link, $this->cookie, $post, $this->link);
        is_present($this->page, '<p class="err">', cut_str($this->page, '<p class="err">', '<br>'));
        unset($post);
        $form = cut_str($this->page, '<Form name="F1"', '</Form>');
        if (!preg_match('/(\d+)<\/span> seconds/', $form, $wait)) html_error("Error: Timer not found!");
        $this->CountDown($wait[1]);
        if (!preg_match_all('@<input type="hidden" name="([^"]+)" value="([^"]+)?">@', $form, $match)) html_error("Error: Post Data 2 [FREE] not found!");
        $match = array_combine($match[1], $match[2]);
        $post = array();
        foreach ($match as $key => $value) {
            $post[$key] = $value;
        }
        $this->page = $this->GetPage($this->link, $this->cookie, $post, $this->link);
        unset($post);
        $form = cut_str($this->page, '<Form method="POST"', '</Form>');
        if (!preg_match("@action='([^']+)'@", $form, $dl)) html_error("Error: Download link [FREE] not found!");
        if (!preg_match('%<input type="([^"]+)" value="([^"]+)">%', $form, $match)) html_error("Error: Post Data 3 [FREE] not found!");
        $dlink = trim($dl[1]);
        $filename = basename(parse_url($dlink, PHP_URL_PATH));
        $this->RedirectDownload($dlink, $filename, $this->cookie, array($match[1] => $match[2]), $this->link);
        exit();
    }
    
    private function Premium() {
        
        $this->cookie = $this->login();
        $this->page = $this->GetPage($this->link, $this->cookie, 0, $this->link);
        if (!preg_match("@Location: (http(s)?:\/\/[^\r\n]+)@i", $this->page, $dl)) {
            $edl = false;
            $form = cut_str($this->page, '<Form name="F1" method="POST"', '</Form>');
            if (!preg_match_all('@<input type="hidden" name="([^"]+)" value="([^"]+)?">@', $form, $match)) html_error("Error: Post Data 1 [PREMIUM] not found!");
            $match = array_combine($match[1], $match[2]);
            $post = array();
            foreach ($match as $key => $value) {
                $post[$key] = $value;
            }
            $this->page = $this->GetPage($this->link, $this->cookie, $post, $this->link);
            unset($post);
            $form = cut_str($this->page, '<Form method="POST"', '</Form>');
            if (!preg_match("@action='([^']+)'@", $form, $dl)) html_error("Error: Download link [PREMIUM] non direct link not found!");
            if (!preg_match('%<input type="([^"]+)" value="([^"]+)">%', $form, $match)) html_error("Error: Post Data 2 [PREMIUM] not found!");
        }
        $dlink = trim($dl[1]);
        $filename = basename(parse_url($dlink, PHP_URL_PATH));
        if ($edl) {
            $this->RedirectDownload($dlink, $filename, $this->cookie);
        } else {
            $this->RedirectDownload($dlink, $filename, $this->cookie, array($match[1] => $match[2]));
       }
    }
    
    private function login() {
        global $premium_acc;
        
        $user = ($_REQUEST["premium_user"] ? trim($_REQUEST["premium_user"]) : $premium_acc ["netuploaded_com"] ["user"]);
        $pass = ($_REQUEST["premium_pass"] ? trim($_REQUEST["premium_pass"]) : $premium_acc ["netuploaded_com"] ["pass"]);
        if (empty($user) || empty($pass)) html_error("Login failed, $user [user] or $pass [password] is empty!");

        $Url = 'http://www.netuploaded.com/';
        $post['op'] = 'login';
        $post['redirect'] = $Url;
        $post['login'] = $user;
        $post['password'] = $pass;
        $post['x'] = rand(11,54);
        $post['y'] = rand(11,21);
        $check = $this->GetPage($Url, $this->cookie, $post, $Url."login.html");
        is_present($check, cut_str($check, '<b class=\'err\'>', '</b>'));
        $cookie = GetCookies($check). "; lang=english";
            
        $check = $this->GetPage($Url."?op=my_account", $cookie, 0, $Url);
        is_notpresent($check, "<TD>Username:</TD>", "Account is not valid!");
        is_notpresent($check, "<TD>Premium-Account expire:</TD>", "Account type : FREE!");
        
        return $cookie;
    }
}

/*
 * netuploaded.com download plugin by Ruud v.Tony 26-01-2012
 */
?>
