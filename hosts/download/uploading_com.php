<?php
if (!defined('RAPIDLEECH')) {
    require_once("index.html");
    exit();
}

class uploading_com extends DownloadClass
{
    public function Download($link)
    {
        global $premium_acc;
        if (($_REQUEST["premium_acc"] == "on" && $_REQUEST["premium_user"] && $_REQUEST["premium_pass"]) || ($_REQUEST["premium_acc"] == "on" && $premium_acc["uploading"]["user"] && $premium_acc["uploading"]["pass"])) {
            $this->DownloadPremium($link);
        } else {
            $this->DownloadFree($link);
        }
    }
    
    private function DownloadFree($link)
    {
        $page = $this->GetPage($link);
        is_present($page, 'The requested file is not found', 'File not found');
        is_present($page, 'We are sorry, the file was removed either by its owner', 'File not found');
        
        $cookie = GetCookies($page);
        if (preg_match('%<form action="(.*)" method="post" id="downloadform">%', $page, $match)) {
            $flink = $match[1];
        }
        $fileid = cut_str($page, 'name="file_id" value="', '"');
        $code   = cut_str($page, 'name="code" value="', '"');
        
        $post = array(
            'action' => 'second_page',
            'file_id' => $fileid,
            'code' => $code
        );
        $page = $this->GetPage($flink, $cookie, $post, $link);
        if (preg_match('%<strong id="timer_count">(\d+)</strong>%', $page, $wait)) {
            $this->CountDown($wait[1]);
        }
        $tid = str_replace(".", "", microtime(true));
        
        $post = array(
            'file_id' => $fileid,
            'code' => $code,
            'action' => 'get_link',
            'pass' => ''
        );
        $page = $this->GetPage('http://uploading.com/files/get/?JsHttpRequest=' . $tid . '-xml', $cookie, $post, $flink);
        if (strpos($page, 'You still need to wait for the download start')) {
            html_error("You still need to wait for the download start. Please wait for some minute and reattempt");
        }
        $dlink = str_replace('\/', '/', cut_str($page, 'answer":{"link":"', '"'));
        if (!$dlink) {
            html_error("Download url error , Please wait for some minute and reattempt");
        }
        $Url      = parse_url($dlink);
        $FileName = basename($Url['path']);
        $this->RedirectDownload($dlink, $FileName, $cookie, 0, $flink);
        exit;
    }
    
    private function DownloadPremium($link)
    {
        global $premium_acc, $Referer;
        
        $tid              = str_replace(".", "", microtime(true));
        $loginUrl         = "http://uploading.com/general/login_form/?JsHttpRequest=" . $tid . "-xml";
        $usrEmail         = "";
        $post             = array();
        $usrEmail         = $_GET["premium_user"] ? $_GET["premium_user"] : $premium_acc["uploading"]["user"];
        $post["email"]    = $usrEmail;
        $post["password"] = $_GET["premium_pass"] ? $_GET["premium_pass"] : $premium_acc["uploading"]["pass"];
        $page             = $this->GetPage($loginUrl, 0, $post, 'http://uploading.com/login/');
        
        $cookie = GetCookies($page);
        if (strpos($cookie, "error=") != false) {
            html_error("Login Failed , Bad username/password combination.", 0);
        }
        $page = $this->GetPage($link, $cookie, 0, $Referer);
        is_present($page, 'Sorry, the file requested by you does not exist on our servers', 'Download link not found');
        is_present($page, 'We are sorry, the file was removed either by its owner', 'Download link not found');
        $code     = trim(cut_str($page, 'code: "', '",'));
        $Url      = parse_url($link);
        $tmp      = basename($Url["path"]);
        $FileName = str_replace(".html", "", $tmp);
        $tid      = str_replace(".", "", microtime(true));
        $sUrl     = "http://uploading.com/files/get/?JsHttpRequest=" . $tid . "-xml";
        unset($post);
        $post["code"]   = $code;
        $post["action"] = "get_link";
        $page           = $this->GetPage($sUrl, $cookie, $post, $Referer);
        $dUrl           = str_replace("\\", "", cut_str($page, 'answer":{"link":"', '"'));
        if ($dUrl == "") {
            html_error("Download url error , Please reattempt", 0);
        }
        $this->RedirectDownload($dUrl, $FileName, $cookie, 0, $Referer);
        exit;
    }
}

/**************************************************\  
WRITTEN by kaox 24-may-2009
UPDATE by kaox  29-nov-2009
UPDATE by rajmalhotra  20 Jan 2010
UPDATE by rajmalhotra Fix for downloading from Premium Accounts 23 Jan 2010 and converted in OOP's format
Fixed by rajmalhotra Fix for downloading from Free and Premium Accounts 07 Feb 2010. Basically fix changes due to change in Site
Fixed by VinhNhaTrang 27-10-2010
Fixed by Ruud v.Tony 02-08-2011
\**************************************************/
?>