<?php

if (!defined('RAPIDLEECH')) {
    require_once ("index.html");
    exit ();
}

class depositfiles_com extends DownloadClass {

    public function Download($link) {
        global $premium_acc, $Referer;
        $page=$this->GetPage("http://depositfiles.com/");
        preg_match("#Location: /(\w+)/#", $page,$lang);
        $link=preg_replace("#.com/\w+/files#", ".com/$lang[1]/files", $link);
        $link=str_replace("com/files", "com/$lang[1]/files", $link);
        if (($_REQUEST ["premium_acc"] == "on" && $_REQUEST ["premium_user"] && $_REQUEST ["premium_pass"]) ||
                ($_REQUEST ["premium_acc"] == "on" && $premium_acc ["depositfiles"] ["user"] && $premium_acc ["depositfiles"] ["pass"])) {
            $this->DownloadPremium($link,$lang=$lang[1]);
        } else {
            $this->DownloadFree($link);
        }
    }

    private function DownloadFree($link) {
        global $Referer;

            $page = $this->GetPage($link);
            if (preg_match("/Location: *(.+)/i", $page, $loc)) {
                $link = "http://depositfiles.com$loc[1]";
                $page = $this->GetPage($link);
            }
            $tcookies = GetCookies($page);
            $cookie = $tcookies;
            is_present($page, "Such file does not exist or it has been removed for infringement of copyrights.");
            is_present($page, "Your IP is already downloading a file from our system.");
            is_present($page, "We are sorry, but all downloading slots for your country are busy.");

            if (preg_match('/<form action="(.*)" method="post" onsubmit/', $page, $match)) {
                $link = "http://depositfiles.com$match[1]";
            }
            $post = array();
            $post['gateway_result'] = "1";
            $page = $this->GetPage($link, $cookie, $post, $link);
            $cookie = $tcookies . "; " . GetCookies($page);
            if (stristr($page, 'You used up your limit for file downloading!')) {
                preg_match('/([0-9]+) minute\(s\)/', $page, $minutes);
                html_error("Download limit exceeded. Try again in " . trim($minutes [1]) . " minute(s)", 0);
            } else {
                preg_match('/<span id="download_waiter_remain">(.*)<\/span>/', $page, $wait);
                $this->CountDown($wait[1]);
            }

            if (!preg_match("#/get_file.php[^&|']+#", $page, $temp)) {
                html_error("Error");
            }
            $tlink = "http://depositfiles.com$temp[0]";
            $cookie = $tcookies . "; " . GetCookies($page);
            $page = $this->GetPage($tlink, $cookie, $post, $link);
            if (preg_match("/Location: (.+)/i", $page, $linkdf)) {
                $tlink = "http://depositfiles.com$linkdf[1]";
                $page = $this->GetPage($tlink, $cookie, $post, $link);
                preg_match('/<form action="(.*)" method="get"/U', $page, $dl);
                $dlink = trim($dl[1]);
                $Url = parse_url($dlink);
                $FileName = basename($Url['path']);
                $this->RedirectDownload($dlink, $FileName, $cookie);
                exit();
            } else {
                html_error("error");
            }
            exit();
    }

    private function DownloadPremium($link,$lang) {
        global $premium_acc, $Referer;
        $login = "http://depositfiles.com/$lang/login.php";
        $post = array();
        $post['go'] = "1";
        $post['login'] = $_REQUEST["premium_user"] ? trim($_REQUEST["premium_user"]) : $premium_acc ["depositfiles"] ["user"];
        $post['password'] = $_REQUEST["premium_pass"] ? trim($_REQUEST["premium_pass"]) : $premium_acc ["depositfiles"] ["pass"];
        $page = $this->GetPage($login, 0, $post, "http://depositfiles.com/$lang/");
        $cookie = GetCookies($page);
        is_notpresent($cookie, "autologin", "Login Failed , Bad username/password combination");
        $page = $this->GetPage($link, $cookie, 0, $link);
        switch ($lang) {
            case "en":
            case "es":
            case "pt":
                is_present($page,"You have exceeded the 15 GB 24-hour limit", "You have exceeded the 15 GB 24-hour limit");
                is_present($page, "has been removed", "The file has been removed");
                break;
            case "de":
                is_present($page, "Entweder existiert diese Datei nicht oder sie wurde aufgrund von Verstößen gegen Urheberrechte gelöscht.","The file has been removed");
                break;
            case "ru":
            is_present($page, "Такого файла не существует или он был удален из-за нарушения авторских прав.", "The file has been removed");
                break;
        }
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