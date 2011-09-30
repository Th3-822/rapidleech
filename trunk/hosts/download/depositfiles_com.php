<?php
if (!defined('RAPIDLEECH')) {
    require_once ("index.html");
    exit ();
}

class depositfiles_com extends DownloadClass {

    public function Download($link) {
        global $premium_acc;
        if (($_REQUEST ["premium_acc"] == "on" && $_REQUEST ["premium_user"] && $_REQUEST ["premium_pass"]) || ($_REQUEST ["premium_acc"] == "on" && $premium_acc ["depositfiles"] ["user"] && $premium_acc ["depositfiles"] ["pass"])) {
            $this->DownloadPremium($link);
        } else {
            $this->DownloadFree($link);
        }
    }

    private function DownloadFree($link) {
        $page = $this->GetPage($link, "lang_current=en");
        $cookie = GetCookies($page). "; lang_current=en";

        $page = $this->GetPage($link, $cookie, array('gateway_result' => '1'), $link);
        is_present($page, '<div class="no_download_msg">', "Such file does not exist or it has been removed for infringement of copyrights.");
        if (preg_match('%<span id="download_waiter_remain">(\d+)<\/span>%', $page, $wait)) $this->CountDown($wait[1]);
        $cookie = $cookie . "; " . GetCookies($page);
        if (!preg_match("#/get_file.php[^&|']+#", $page, $temp)) html_error('Error: Redirect link not found!');
        $page = $this->GetPage("http://depositfiles.com$temp[0]", $cookie, 0, $link);
        if (!preg_match('%<form action="(.*)" method="get"%', $page, $dl)) html_error('Error: Final link not found!');
        $filename = parse_url($dl[1]);
        $FileName = basename($filename['path']);
        $this->RedirectDownload($dl[1], $FileName, $cookie, 0, $link);
        exit();
    }

    private function DownloadPremium($link) {
        global $premium_acc;
        $post = array();
        $post['go'] = "1";
        $post['login'] = $_REQUEST["premium_user"] ? trim($_REQUEST["premium_user"]) : $premium_acc ["depositfiles"] ["user"];
        $post['password'] = $_REQUEST["premium_pass"] ? trim($_REQUEST["premium_pass"]) : $premium_acc ["depositfiles"] ["pass"];
        $page = $this->GetPage("http://depositfiles.com/login.php", "lang_current=en", $post, "http://depositfiles.com/");
        $cookie = GetCookies($page). "; lang_current=en";
        is_notpresent($cookie, "autologin", "Login Failed , Bad username/password combination");
        $page = $this->GetPage($link, $cookie, 0, $link);
        is_present($page,"You have exceeded the 15 GB 24-hour limit", "You have exceeded the 15 GB 24-hour limit");
        is_present($page, "has been removed", "The file has been removed");
        if (!preg_match("/http:\/\/.+auth-[^'\"]+/i", $page, $dlink)){
            html_error("Error 1x01:Plugin is out of date");
        }
        $Url = parse_url($dlink[0]);
        $FileName = basename($Url['path']);
        $this->RedirectDownload($dlink[0], $FileName, $cookie);
        exit();
    }
}

//Depositfiles Download Plugin by vdhdevil & Ruud v.Tony 19-3-2011
//Updated 30-April-2011: Updated Download Premium
//Updated 16-May-2011: Updated Download Free
?>