<?php

if (!defined('RAPIDLEECH')) {
    require_once ("index.html");
    exit ();
}

class enterupload_com extends DownloadClass {

    public function Download($link) {
        global $premium_acc;
        if (($_REQUEST ["premium_acc"] == "on" && $_REQUEST ["premium_user"] && $_REQUEST ["premium_pass"]) ||
           ($_REQUEST ["premium_acc"] == "on" && $premium_acc ["enterupload_com"] ["user"] && $premium_acc ["enterupload_com"] ["pass"])) {
            $this->DownloadPremium($link);
        } else {
            $this->DownloadFree($link);
        }
    }

    private function DownloadFree($link) {
        $page = $this->GetPage($link);
        is_present($page, "File Not Found","File Not Found");

        $id = cut_str($page, 'name="id" value="','"');
        $fname = cut_str($page, 'name="fname" value="','"');

        $post = array ();
        $post['op'] = "download1";
        $post['usr_login'] = "";
        $post['id'] = $id;
        $post['fname']= $fname;
        $post['referer'] = "";
        $post['method_free']= "Free Download";
        $page = $this->GetPage($link, 0, $post, $link);
        is_present($page,"You can download files up to 400 Mb only","You can download files up to 400 Mb only");
        if (preg_match("#You have to wait (\d+) minutes, (\d+) seconds till next download#",$page,$message)){
            html_error($message[0]);
        }
        if (preg_match("#(\d+)</span> seconds#",$page,$wait)){
            $this->CountDown($wait[1]);
        }
        $rand = cut_str($page, 'name="rand" value="','"');
        unset ($post);
        $post['op']="download2";
        $post['id'] = $id;
        $post['rand'] = $rand;
        $post['referer'] = $link;
        $post['method_free'] = "Free Download";
        $post['method_premium']="";
        $post['down_direct']="1";
        $page = $this->GetPage($link, 0, $post, $link);
        if (!preg_match('#(http:\/\/serv\d+\.enterupload\.com(:\d+)?\/d\/.+)" #', $page, $dl)){
            html_error("Error: Plugin out of date!!!");
        }

        $dlink = trim($dl[1]);
        $Url=parse_url($dlink);
        $FileName=basename($Url['path']);
        $this->RedirectDownload($dlink, $FileName, 0, 0, $link);
        exit();
    }

    private function DownloadPremium($link) {
        global $premium_acc;

        $post = array();
        $post['op'] = "login";
        $post['redirect'] = "";
        $post['login'] = $_REQUEST["premium_user"] ? trim($_REQUEST["premium_user"]) : $premium_acc ["enterupload_com"] ["user"];
        $post['password'] = $_REQUEST["premium_pass"] ? trim($_REQUEST["premium_pass"]) : $premium_acc ["enterupload_com"] ["pass"];
        $post['x'] = rand(0,50);
        $post['y'] = rand(0,12);
        $page = $this->GetPage("http://www.enterupload.com/login.html", 0, $post, "http://enterupload.com/");
        $cookie = GetCookies($page);
        is_present($page, "Incorrect", "Incorrect Login or Password");
        if (preg_match('/Location: (.*)/i', $page, $match)) {
            $home = $match[1];
            $page = $this->GetPage($home, $cookie, 0, "http://enterupload.com/");
        }

        $page = $this->GetPage($link, $cookie);
        is_present($page, "File Not Found","File Not Found");

        if (!preg_match('/Location: (.*)/i', $page, $dl)) {
            $id = cut_str($page, 'name="id" value="','"');
            $rand = cut_str($page, 'name="rand" value="','"');
            $referer = cut_str($page, 'name="referer" value="','"');
            unset($post);
            $post['op'] = "download2";
            $post['id'] = $id;
            $post['rand'] = $rand;
            $post['referer'] = $referer;
            $post['method_free'] = "";
            $post['method_premium'] = "1";
            $post['down_direct'] = "1";
            $page = $this->GetPage($link, $cookie, $post, $link);
            preg_match('/(http:\/\/serv\d+\.enterupload\.com(:\d+)?\/d\/.+)" /', $page, $dl);
        }
        $dlink = trim($dl[1]);
        if (!$dlink) {
            html_error("Error: Download link not found!");
        }
        $Url = parse_url($dlink);
        $FileName = basename($Url['path']);
        $this->RedirectDownload($dlink, $FileName, $cookie, 0, $link);
        exit();
    }

}

//Enterupload free download plugin by Ruud v.Tony 28 Feb 2011
//Updated 13 May 2011 for supporting premium account with enable/disable direct link
?>