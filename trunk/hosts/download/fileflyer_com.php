<?php
if (!defined('RAPIDLEECH')) {
  require_once("index.html");
  exit;
}

class fileflyer_com extends DownloadClass {
    public function Download($link) {
        global $premium_acc, $Referer;
            //check the link
            $page = $this->GetPage($link);
            is_present($page, "Removed", "The file has been removed or has been expired!");
            unset ($page);
            if (($_REQUEST['premium_acc']== "on" && $_REQUEST['premium_pass']) || ($_REQUEST['premium_acc'] == "on" && $premium_acc['fileflyer_com']['pass'])) {
                $this->DownloadPremium($link);
            } else {
                $this->DownloadFree($link);
            }
    }

    private function DownloadPremium($link) {
        global $premium_acc, $Referer;
            $page = $this->GetPage($link);
            $cookie = GetCookies($page);
            if (stristr($page, 'class="handlinkblocked"')) {
                preg_match('/<form name="form1" method="post" action="(.*)"/U', $page, $pre);
                $prelink = $pre[1];

                $viewstate = cut_str($page, 'id="__VIEWSTATE" value="','"');
                $eventval = cut_str($page, 'id="__EVENTVALIDATION" value="','"');

                $post = array();
                $post['__EVENTTARGET'] = "";
                $post['__EVENTARGUMENT'] = "";
                $post['__VIEWSTATE'] = urlencode($viewstate);
                $post['__EVENTVALIDATION'] = urlencode($eventval);
                $post['SMSButton'] = "Go";
                $post['Password'] = $_REQUEST["premium_pass"] ? trim($_REQUEST["premium_pass"]) : $premium_acc ["fileflyer_com"] ["pass"];
                $post['TextBox1'] = "";
                $page = $this->GetPage($prelink, $cookie, $post, $link);
                $cookie = GetCookies($page); //I need to replace the existing cookies with the premium account :D
                is_notpresent($page, "Access enabled", "Invalid premium codes");
            }
            if (!preg_match('#(http:\/\/.+fileflyer\.com\/d\/[^\"]+)"#', $page, $dl)) {
                html_error("Error, premium code need to be updated. Contact the author with the link which u have this error!");
            }
            $dlink = trim($dl[1]);
            $Url = parse_url($dlink);
            $FileName = basename($Url['path']);
            $this->RedirectDownload($dlink, $FileName, $cookie, 0, $link);
            exit();
    }
    private function DownloadFree($link) {
        $page = $this->GetPage($link);
        is_present($page, 'class="handlinkblocked"', "You need to have premium access to unlock this link!");

        if (!preg_match('#(http:\/\/.+fileflyer\.com\/d\/[^\"]+)"#', $page, $dl)) {
            html_error("Error, free code couldn't be found. Contact the author with the link which u have this error!");
        }
        $dlink = trim($dl[1]);
        $Url = parse_url($dlink);
        $FileName = basename($Url['path']);
        $this->RedirectDownload($dlink, $FileName);
    }
}

//fileflyer download plugin by Ruud v.Tony 25-06-2011
?>