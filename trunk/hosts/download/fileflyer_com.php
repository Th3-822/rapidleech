<?php
if (!defined('RAPIDLEECH')) {
    require_once("index.html");
    exit;
}

class fileflyer_com extends DownloadClass {

    public function Download($link) {
        global $premium_acc, $Referer;

        $page = $this->GetPage($link);
        is_present($page, "Removed", "The file has been removed or has been expired!");
        if (stristr($page, 'class="handlinkblocked"')) {

            $pass = ($_REQUEST ["premium_pass"] ? $_REQUEST ["premium_pass"] : $premium_acc ["fileflyer_com"] ["pass"]);
            if (empty($pass)) html_error("This link required premium account!");

            if (!preg_match('/<form name="form1" method="post" action="(.*)"/U', $page, $pre)) html_error('Error: Premium link not found?');
            $link = $pre[1];

            $post = array();
            $post['__EVENTTARGET'] = "";
            $post['__EVENTARGUMENT'] = "";
            $post['__VIEWSTATE'] = urlencode(cut_str($page, 'id="__VIEWSTATE" value="', '"'));
            $post['__EVENTVALIDATION'] = urlencode(cut_str($page, 'id="__EVENTVALIDATION" value="', '"'));
            $post['SMSButton'] = "Go";
            $post['Password'] = $pass;
            $post['TextBox1'] = "";
            $page = $this->GetPage($link, 0, $post, $Referer);
            is_notpresent($page, "Access enabled", "Invalid premium codes");
        }
        $cookie = GetCookies($page);
        if (!preg_match('@http:\/\/.+fileflyer\.com\/d\/[^\"]+@', $page, $dl)) html_error ('Error: Download link not found, plugin need to be updated!');
        $dlink = trim($dl[0]);
        $filename = parse_ul($dlink);
        $FileName = basename($filename['path']);
        $this->RedirectDownload($dlink, $FileName, $cookie, 0, $Referer);
        exit();
    }
}

//fileflyer download plugin by Ruud v.Tony 25-06-2011
//updated by Ruud v.Tony 14-10-2011
?>