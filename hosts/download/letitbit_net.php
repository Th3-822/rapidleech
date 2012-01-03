<?php
if (!defined('RAPIDLEECH')) {
    require_once("index.html");
    exit();
}

class letitbit_net extends DownloadClass {

    public function Download($link) {
        global $premium_acc, $Referer;
        //check link
        if (!$_REQUEST['step']) {
            $this->page = $this->GetPage($link, 'lang=en');
            is_present($this->page, "File not found", "The requested file was not found");
            $this->cookiearr = GetCookiesArr($this->page);
            //We dont need to always convert the cookie from array into string, I do that becuz htmlentities cant validate array value even it's working
            $this->cookie = CookiesToStr($this->cookiearr) . "; lang=en"; //keep page in english
        }
        $this->link = $link;
        if (($_REQUEST ['premium_acc'] == 'on' && $_REQUEST['premium_user'] && $_REQUEST ['premium_pass']) || ($_REQUEST ['premium_acc'] == 'on' && (!empty($premium_acc ['letitbit_net'] ['user']) && !empty($premium_acc ['letitbit_net'] ['pass'])))) {
            $this->Login();
        } elseif ($_REQUEST['step'] == '1') {
            $this->Free();
        } else {
            if (($_REQUEST['premium_acc'] == 'on' && $_REQUEST['premium_pass']) || ($_REQUEST['premium_acc'] == 'on' && (!empty($premium_acc['letitbit_net']['pass'])))) {
                $form = cut_str($this->page, '<div class="hide-block" id="password_area">', '<div class="column label" style="width:200px">');
                if (empty($form)) html_error("Error: Empty Premium Key Form!");
                $post = $this->AutomatePost($form);
                //additional post data
                $post['pass'] = $_REQUEST ["premium_pass"] ? $_REQUEST ["premium_pass"] : $premium_acc ["letitbit_net"] ["pass"];
                $post['submit_sms_ways_have_pass'] = 'Download file';
                //textarea($post, 0, 0, true);
                $this->link = "http://letitbit.net" . cut_str($form, '<form action="', '"');
                $this->page = $this->GetPage($this->link, $this->cookie, $post, $Referer);
                return $this->Premium();
            } else {
                $form = cut_str($this->page, '<form id="ifree_form"', '<div class="wrapper-centered">');
                if (empty($form)) html_error("Error: Empty Free Form 1!");
                $post = $this->AutomatePost($form);
                $this->link = "http://letitbit.net" . cut_str($form, 'action="', '"');
                $this->page = $this->GetPage($this->link, $this->cookie, $post, $Referer);
                return $this->PrepareFree();
            }
        }
    }

    private function PrepareFree() {
        global $Referer;

        $this->cookiearr = array_merge($this->cookiearr, GetCookiesArr($this->page));
        $this->cookie = CookiesToStr($this->cookiearr);
        unset($post);
        $form = cut_str($this->page, 'id="d3_form">', '</form>');
        if (empty($form)) html_error("Error: Empty Free Form 2!");
        $post = $this->AutomatePost($form);
        if (!preg_match('%<form action="((http:\/\/s\d+\.letitbit\.net)\/[^"]+)" method="post" id="d3_form">%', $this->page, $check)) html_error("Error: Redirect link [Free] not found!");
        $this->link = $check[1];
        $this->server = $check[2];
        $this->page = $this->GetPage($this->link, $this->cookie, $post, $Referer);
        $this->link = $this->server . '/ajax/download3.php';
        // If you want, you can skip the countdown...
        if (preg_match('@(\d+)<\/span> seconds@', $this->page, $wait)) $this->CountDown($wait[1]);
        // end countdown timer...
        $this->page = $this->GetPage($this->link, $this->cookie, array(), $Referer, 0, 1); //empty array in post variable needed...
        $data = $this->DefaultParamArr($this->server . "/ajax/check_captcha.php", $this->cookie);
        $data['step'] = '1';
        //Download captcha img.
        $cap = $this->GetPage($this->server . '/captcha_new.php', $this->cookie); // Yes, the cookie is needed
        $capt_img = substr($cap, strpos($cap, "\r\n\r\n") + 4);
        $imgfile = DOWNLOAD_DIR . "letitbit_captcha.png";

        if (file_exists($imgfile)) unlink($imgfile);
        if (empty($capt_img) || !write_file($imgfile, $capt_img)) html_error("Error getting CAPTCHA image.", 0);
        // Captcha img downloaded
        $this->EnterCaptcha($imgfile, $data);
        exit();
    }

    private function Free() {
        global $Referer;

        $post['code'] = $_POST['captcha'];
        $this->link = urldecode($_POST['link']);
        $this->cookie = urldecode($_POST['cookie']);
        $this->page = $this->GetPage($this->link, $this->cookie, $post, $Referer, 0, 1); //too many XML request needed so I used default http.php function in geturl...
        is_present($this->page, "Content-Length: 0", "Error: Wrong Captcha Entered.");
        if (!preg_match('@http:\/\/[^\r|\n]+@i', $this->page, $dl)) html_error("Error: Download link [Free] not found.");
        $dlink = trim($dl[0]);
        $FileName = urldecode(basename(parse_url($dlink, PHP_URL_PATH)));
        $this->RedirectDownload($dlink, $FileName, $this->cookie, 0, $Referer);
        exit();
    }

    private function Login() {
        global $premium_acc;

        $user = $_REQUEST ["premium_user"] ? $_REQUEST ["premium_user"] : $premium_acc ["letitbit_net"] ["user"];
        $password = $_REQUEST ["premium_pass"] ? $_REQUEST ["premium_pass"] : $premium_acc ["letitbit_net"] ["pass"];
        if (empty($user) || empty($password)) {
            html_error("Login Failed: Username or Password is empty. Please check login data.");
        }

        $post = array();
        $post['act'] = 'login';
        $post['login'] = $user;
        $post['password'] = $password;
        $this->page = $this->GetPage('http://letitbit.net/', $this->cookie, $post, 'http://letitbit.net/');
        is_present($this->page, 'Authorization data is invalid');
        $this->cookie = GetCookies($this->page) . "; lang=en";
        $this->page = $this->GetPage($this->link, $this->cookie);

        return $this->Premium();
    }

    private function Premium() {

        $this->cookie = $this->cookie . "; " . GetCookies($this->page);
        if (stristr($this->page, "Location:")) {
            $this->link = trim(cut_str($this->page, "Location: ", "\r\n"));
            $this->page = $this->GetPage($this->link, $this->cookie);
        }
        $tlink = cut_str(cut_str($this->page, '<iframe', '</iframe>'), 'src="', '"');
        if (empty($tlink)) html_error('Error: Please check your premium account!');
        $this->page = $this->GetPage($tlink, $this->cookie, 0, $this->link);
        if (!preg_match('@http:\/\/.+downloadp(\d+)?\/let(\d+)?\/[^\'?"?]+@i', $this->page, $dl)) html_error('Error: Download Link [Premium] not found!');
        $dlink = trim($dl[0]);
        $FileName = urldecode(basename(parse_url($dlink, PHP_URL_PATH)));
        $this->RedirectDownload($dlink, $FileName, $this->cookie, 0, $tlink);
    }

    private function AutomatePost($form) {
        if (!preg_match_all('@<input type="hidden" name="([^"]+)" value="([^"]+)" \/>@i', $form, $match)) html_error("Error: Post Data not found!");
        $post = array();
        $match = array_combine($match[1], $match[2]);
        foreach ($match as $k => $v) {
            $post[$k] = ($v == "") ? 1 : $v;
        }

        return $post;
    }

}

/* * *********************************************************************************************\
  WRITTEN BY VinhNhaTrang 15-11-2010
  Fix the premium code by code by vdhdevil
  Fix the free download code by vdhdevil & Ruud v.Tony 25-3-2011
  Updated the premium code by Ruud v.Tony 19-5-2011
  Updated for site layout change by Ruud v.Tony 24-7-2011
  Updated for joining between premium user & pass with only single key by Ruud v.Tony 13-10-2011
  Small fix in post form by Ruud v.Tony 16-12-2011 (sorry for the delay, I'm busy with my real life)
  Fix free code by Ruud v.Tony & Th3-822 for letitbit new layout 31-12-2011 (Happy new year everyone)
  \********************************************************************************************** */
?>