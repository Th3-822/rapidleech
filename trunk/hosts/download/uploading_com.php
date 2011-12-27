<?php
if (!defined('RAPIDLEECH')) {
    require_once ("index.html");
    exit();
}

class uploading_com extends DownloadClass {

    public function Download($link) {
        global $premium_acc;
        if (($_REQUEST['premium_acc'] == 'on' && $_REQUEST['premium_user'] && $_REQUEST['premium_pass']) || ($_REQUEST['premium_acc'] == 'on' && $premium_acc['uploading']['user'] && $premium_acc['uploading']['pass'])) {
            $this->Premium($link);
        } else {
            $this->Free($link);
        }
    }

    private function Free($link) {
        global $Referer;
        $page = $this->GetPage($link);
        is_present($page, "The requested file is not found");
        $CookieArr = GetCookiesArr($page);
        $cookie = CookiesToStr($CookieArr);
        if (preg_match("@SID=(\w+)@", $cookie, $sd)) $sid = $sd[1];
        if (preg_match('%<form action="(.*)" method="post" id="downloadform">%', $page, $match)) $flink = $match[1];
        $post['action'] = cut_str($page, 'name="action" value="', '"');
        $post['id'] = cut_str($page, 'name="file_id" value="', '"');
        $post['code'] = cut_str($page, 'name="code" value="', '"');
        $page = $this->GetPage($flink, $cookie, $post, $link);
        $CookieArr = array_merge($CookieArr, GetCookiesArr($page));
        $cookie = CookiesToStr($CookieArr);
        if (!preg_match('%<strong id="timer_count">(\d+)</strong>%', $page, $wait)) html_error("Timer not found!");
        $this->CountDown($wait[1]);
        unset($post);
        $post['action'] = cut_str($page, "action: '", "'");
        $post['code'] = cut_str($page, 'code: "', '"');
        $post['pass'] = '';
        $page = $this->GetPage('http://uploading.com/files/get/?SID=' . $sid . '&JsHttpRequest=' . round(microtime(true) * 1000) . '-xml', $cookie, $post, $flink);
        if (preg_match('@"js":\{"(\w+)":\{?"(([^"]+)"?:?"?([^|\r|\n|\"]+)?)"\}@i', $page, $match)) {
            switch ($match[1]) {
                case 'answer':
                    switch ($match[3]) {
                        case 'link':
                            $dlink = str_replace('\/', '/', $match[4]);
                            $FileName = basename($dlink, PHP_URL_PATH);
                            $this->RedirectDownload($dlink, $FileName, $cookie, 0, $flink);
                            break;
                    }
                    break;
                default:
                    html_error("$match[1], $match[2]");
                    break;
            }
        }
    }

    private function Premium($link) {
        global $premium_acc, $Referer;
        $tid = str_replace(".", "", microtime(true));
        $posturl = 'http://uploading.com/general/login_form/?JsHttpRequest=' . $tid . '-xml';

        $post = array();
        $post['email'] = $_REQUEST["premium_user"] ? trim($_REQUEST["premium_user"]) : $premium_acc ["uploading"] ["user"];
        $post['password'] = $_REQUEST["premium_user"] ? trim($_REQUEST["premium_user"]) : $premium_acc ["uploading"] ["pass"];
        $post['remember'] = "on";
        $page = $this->GetPage($posturl, 0, $post, 'http://uploading.com/');
        is_present($page, '"error":', "Incorrect e-mail or password combination.");
        $cookie = GetCookies($page);

        $page = $this->GetPage($link, $cookie);
        is_present($page, 'The requested file is not found', 'File not found');
        is_present($page, 'We are sorry, the file was removed either by its owner', 'File not found');
        is_present($page, 'Your account premium traffic has been limited', 'Traffic volume for premium downloads has been expired or need to renew!');
        if (preg_match('/Location: (.*)/', $page, $dl)) {
            $dlink = trim($dl[1]);
        } else {
            $post = array('action' => 'get_link', 'code' => cut_str($page, 'code: "', '",'), 'pass' => '');
            $page = $this->GetPage('http://uploading.com/files/get/?JsHttpRequest=' . $tid . '-xml', $cookie, $post, $link);
            $dlink = str_replace('\/', '/', cut_str($page, 'answer":{"link":"', '"'));
        }
        $dlink = str_replace("\r\n", "", $dlink);
        if (!$dlink) html_error("Error: Premium Download link not found!");
        $Url = parse_url($dlink);
        $FileName = basename($Url['path']);
        $this->RedirectDownload($dlink, $FileName, $cookie, 0, $link);
        exit();
    }

}

/* * ************************************************\  
  WRITTEN by kaox 24-may-2009
  UPDATE by kaox  29-nov-2009
  UPDATE by rajmalhotra  20 Jan 2010
  UPDATE by rajmalhotra Fix for downloading from Premium Accounts 23 Jan 2010 and converted in OOP's format
  Fixed by rajmalhotra Fix for downloading from Free and Premium Accounts 07 Feb 2010. Basically fix changes due to change in Site
  Fixed by VinhNhaTrang 27-10-2010
  Fixed by Ruud v.Tony 02-08-2011
  Updated the premium code by Ruud v.Tony 15-08-2011 (I hate chunked file!)
  Updated the free download code by Ruud v.Tony 28-11-2011
  \************************************************* */
?>