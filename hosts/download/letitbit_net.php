<?php
if (!defined('RAPIDLEECH')) {
    require_once("index.html");
    exit();
}

class letitbit_net extends DownloadClass {

    public function Download($link) {
        global $premium_acc, $Referer;
        if (!$_REQUEST['step']) {
            $page = $this->GetPage($link, 'lang=en'); //set the page to english
            is_present($page, "File not found", "The requested file was not found");
        }
        unset($page);
        if (($_REQUEST ['premium_acc'] == 'on' && $_REQUEST['premium_user'] && $_REQUEST ['premium_pass']) || ($_REQUEST ['premium_acc'] == 'on' && (!empty($premium_acc ['letitbit_net'] ['user']) && !empty($premium_acc ['letitbit_net'] ['pass'])))) {
            $this->Login($link);
        } else {
            $cookie = GetCookies($page). "; lang=en";
            $post['uid5'] = cut_str($page, 'name="uid5" value="', '"');
            $post['uid'] = cut_str($page, 'name="uid" value="', '"');
            $post['seo_name'] = cut_str($page, 'name="seo_name" value="', '"');
            $post['name'] = cut_str($page, 'name="name" value="', '"');
            $post['pin'] = cut_str($page, 'name="pin" value="', '"');
            $post['realuid'] = cut_str($page, 'name="realuid" value="', '"');
            $post['realname'] = cut_str($page, 'name="realname" value="', '"');
            $post['host'] = cut_str($page, 'name="host" value="', '"');
            $post['ssserver'] = cut_str($page, 'name="ssserver" value="', '"');
            $post['sssize'] = cut_str($page, 'name="sssize" value="', '"');
            $post['index'] = cut_str($page, 'name="index" value="', '"');
            $post['dir'] = cut_str($page, 'name="dir" value="', '"');
            $post['optiondir'] = cut_str($page, 'name="optiondir" value="', '"');
            $post['lsarrserverra'] = cut_str($page, 'name="lsarrserverra" value="', '"');
            $post['pin_wm'] = cut_str($page, 'name="pin_wm" value="', '"');
            $post['md5crypt'] = cut_str($page, 'name="md5crypt" value="', '"');
            $post['realuid_free'] = cut_str($page, 'name="realuid_free" value="', '"');
            $post['pin_wm_tarif'] = 'default';
            if (($_REQUEST['premium_acc'] == 'on' && $_REQUEST['premium_pass']) || ($_REQUEST['premium_acc'] == 'on' && (!empty($premium_acc['letitbit_net']['pass'])))) {
                $post['pass'] = $_REQUEST["premium_pass"] ? trim($_REQUEST["premium_pass"]) : $premium_acc ["letitbit_net"] ["pass"];
                $post['submit_sms_ways_have_pass'] = 'Download file';
                $link = 'http://letitbit.net'. cut_str(cut_str($page, 'id="password_area">', '<div class="clear-line">'), '<form action="', '"');
                return $this->Premium($this->GetPage($link, $cookie, $post, $Referer), $link, $cookie);
            } else {
                $link = "http://letitbit.net" . cut_str($page, 'id="ifree_form" action="', '" ');
                return $this->Free($this->GetPage($link, $cookie, $post, $Referer), $link, $cookie);
            }
        }
    }

    private function Free($page, $link, $cookie) {
        global $Referer;

        $t = explode(";", GetCookies($page));
        $cookie .=";" . $t[0] . ";" . $t[2];
        if (!preg_match('%<form action="(.*)" method="post" id="dvifree">%', $page, $match)) html_error('Error: redirect link 1 not found!');
        $link = $match[1];
        unset($post);
        $post['uid5'] = cut_str($page, 'name="uid5" value="', '"');
        $post['uid'] = cut_str($page, 'name="uid" value="', '"');
        $post['seo_name'] = cut_str($page, 'name="seo_name" value="', '"');
        $post['name'] = cut_str($page, 'name="name" value="', '"');
        $post['pin'] = cut_str($page, 'name="pin" value="', '"');
        $post['realuid'] = cut_str($page, 'name="realuid" value="', '"');
        $post['realname'] = cut_str($page, 'name="realname" value="', '"');
        $post['host'] = cut_str($page, 'name="host" value="', '"');
        $post['ssserver'] = cut_str($page, 'name="ssserver" value="', '"');
        $post['sssize'] = cut_str($page, 'name="sssize" value="', '"');
        $post['index'] = cut_str($page, 'name="index" value="', '"');
        $post['dir'] = cut_str($page, 'name="dir" value="', '"');
        $post['optiondir'] = cut_str($page, 'name="optiondir" value="', '"');
        $post['lsarrserverra'] = cut_str($page, 'name="lsarrserverra" value="', '"');
        $post['pin_wm'] = cut_str($page, 'name="pin_wm" value="', '"');
        $post['md5crypt'] = cut_str($page, 'name="md5crypt" value="', '"');
        $post['realuid_free'] = cut_str($page, 'name="realuid_free" value="', '"');
        $post['pin_wm_tarif'] = 'default';
        $post['ac_http_referer'] = cut_str($page, 'name="ac_http_referer" value="', '"');
        $post['links_sent'] = "1";
        $post['rand'] = cut_str($page, 'name="rand" value="', '"');
        $page = $this->GetPage($link, $cookie, $post, $Referer);
        if (!preg_match('@(\d+)<\/span> seconds@', $page, $wait)) html_error('Error: Timer not found!');
        $this->CountDown($wait[1]);
        if (!preg_match("@ajax_check_url = '([^|\r|\n]+)'@", $page, $match)) html_error('Error: Redirect link 2 not found');
        $tlink = $match[1];
        $page = $this->GetPage($tlink, $cookie, $post, $link."\r\nX-Requested-With: XMLHttpRequest");
        if (!preg_match('@http:\/\/.+download(\d+)?\/let(\d+)?\/[^|\r|\n]+@', $page, $dl)) html_error('Error: Free Download link can\'t be found!');
        $dlink = trim($dl[0]);
        $filename = parse_url($dlink);
        $Filename = basename($filename['path']);
        $this->RedirectDownload($dlink, $Filename, $cookie, 0, $rlink);
        exit();
    }

    private function Login($link) {
        global $premium_acc;

        $user = ($_REQUEST ["premium_user"] ? $_REQUEST ["premium_user"] : $premium_acc ["letitbit_net"] ["user"]);
        $password = ($_REQUEST ["premium_pass"] ? $_REQUEST ["premium_pass"] : $premium_acc ["letitbit_net"] ["pass"]);
        if (empty($user) || empty($password)) {
            html_error("Login Failed: Username or Password is empty. Please check login data.");
        }

        $post = array();
        $post['act'] = 'login';
        $post['login'] = $user;
        $post['password'] = $password;
        $page = $this->GetPage('http://letitbit.net/', 'lang=en', $post, 'http://letitbit.net/');
        is_present($page, 'Authorization data is invalid', 'Authorization data is invalid, please check ur account!');
        $cookie = GetCookies($page). "; lang=en";

        return $this->Premium($this->GetPage($link, $cookie), $link, $cookie);

    }

    private function Premium($page, $link, $cookie) {
        
        $cookie = $cookie . "; " . GetCookies($page);
        if (stristr($page, "Location:")) {
            $link = trim(cut_str($page, "Location: ", "\n"));
            $page = $this->GetPage($link, $cookie);
        }
        $tlink = cut_str(cut_str($page, '<iframe', '</iframe>'), 'src="', '"');
        if (empty($tlink)) html_error('Error: Please check your premium account!');
        $page = $this->GetPage($tlink, $cookie, 0, $link);
        if (!preg_match('@http:\/\/.+downloadp(\d+)?\/let(\d+)?\/[^\'"]+@i', $page, $dl)) html_error('Error: Can\'t found premium download link!');
        $dlink = trim($dl[0]);
        $Url = parse_url($dlink);
        $FileName = basename($Url['path']);
        $this->RedirectDownload($dlink, $FileName, $cookie, 0, $tlink);
    }
}

/***********************************************************************************************\
  WRITTEN BY VinhNhaTrang 15-11-2010
  Fix the premium code by code by vdhdevil
  Fix the free download code by vdhdevil & Ruud v.Tony 25-3-2011
  Updated the premium code by Ruud v.Tony 19-5-2011
  Updated for site layout change by Ruud v.Tony 24-7-2011
  Updated for joining between premium user & pass with only single key by Ruud v.Tony 13-10-2011
\***********************************************************************************************/
?>