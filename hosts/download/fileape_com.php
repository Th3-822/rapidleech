<?php
if (!defined('RAPIDLEECH')) {
    require_once('index.html');
    exit();
}

class fileape_com extends DownloadClass {

    public function Download($link) {
        global $premium_acc;
        if (!$_REQUEST['step']) {
            $this->page = $this->GetPage($link);
            if (preg_match('@Location: (http:\/\/fileape\.com\/index.php[^\r\n]+)@i', $this->page, $temp)) {
                $link = $temp[1];
                $this->page = $this->GetPage($link);
            }
            is_present($this->page,"This file is either temporarily unavailable or does not exist");
        }
        if (($_REQUEST['premium_acc'] == 'on' && $_REQUEST['premium_user'] && $_REQUEST['premium_pass']) || ($_REQUEST['premium_acc'] == 'on' && $premium_acc['fileape_com']['user'] && $premium_acc['fileape_com']['pass'])) {
            $this->Premium($link);
        } else {
            $this->Free($link);
        }
    }

    public function  CheckBack($header) {
        is_notpresent($header, 'HTTP/1.1 200 OK', 'Download link expired, please retry again!');
    }

    private function Free($link) {
        if (!preg_match('@\/\?act=download[^"]+@', $this->page, $free)) html_error('Error: Free link not found!');
        $flink = "http://fileape.com". $free[0];
        $page = $this->GetPage($flink, 0, 0, $link);
        if (!preg_match('@wait = (\d+)@', $page, $wait)) html_error('Error: Timer not found');
        $this->CountDown ($wait[1]);
        if (!preg_match("@window\.location = '([^|\r|\n|']+)@", $page, $redir)) html_error('Error: Redirect link not found!');
        $rlink = $redir[1];
        $page = $this->GetPage($rlink, 0, 0, $flink);
        if (!preg_match('@http:\/\/tx(\d+)?\.fileape\.com\/[^|\r|\n|"]+@', $page, $dl)) html_error ('Error: Free Download link not found!');
        $dlink = trim($dl[0]);
        $FileName = basename(parse_url($dlink, PHP_URL_PATH));
        $this->RedirectDownload($dlink, $FileName, 0, 0, $rlink);
        exit();
    }

    private function Premium($link) {
        global $premium_acc;

        $posturl = "http://fileape.com/";
        $post['username'] = $_REQUEST ["premium_user"] ? trim($_REQUEST ["premium_user"]) : $premium_acc ["fileape_com"] ["user"];
        $post['password'] = $_REQUEST ["premium_pass"] ? trim($_REQUEST ["premium_pass"]) : $premium_acc ["fileape_com"] ["pass"];
        $page = $this->GetPage($posturl."?act=login", 0, $post, $posturl."?act=account");
        is_present($page, "<b>there was an error. entered the wrong username or password?</b>");
        $cookie = GetCookies($page);
        $page = $this->GetPage($posturl."?act=premium", $cookie, 0, $posturl);
        is_present($page, "Your premium account has expired");
        is_present($page, "Click Here to Purchase", "Account Free, You can't use premium services!");
        is_present($page, "Buy More Premium Bandwidth!", "You have reach your premium account bandwidth limit!");

        $page = $this->GetPage($link, $cookie);
        if (!preg_match('@Location: ([^|\r|\n]+)@i', $page, $dl)) html_error('Error: Premium Download link not found!');
        $dlink = trim($dl[1]);
        $FileName = basename(parse_url($dlink, PHP_URL_PATH));
        $this->RedirectDownload($dlink, $FileName, $cookie);
    }
}

/*
 * by Ruud v.Tony 09-11-2011
 */
?>
