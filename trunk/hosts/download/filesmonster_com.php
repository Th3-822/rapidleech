<?php
if (!defined('RAPIDLEECH')) {
    require_once("index.html");
    exit;
}

class filesmonster_com extends DownloadClass {

    public function Download($link) {
        global $premium_acc;
        if ($_REQUEST['premium_acc'] == 'on' && (($_REQUEST['premium_user'] && $_REQUEST['premium_pass']) || ($premium_acc['filesmonster_com']['user'] && $premium_acc['filesmonster_com']['pass']))) {
            $this->Premium($link);
        } elseif ($_POST['step'] == '1') {
            $this->DownloadFree($link);
        } else {
            $this->Retrieve($link);
        }
    }

    private function Retrieve($link) {
        $page = $this->GetPage($link);
        is_present($page, "File was deleted");
        is_present($page, "You need Premium membership to download files larger than 1.0 GB.");
        //check the file size
        $flsize = cut_str($page, 'File size:</td>', '</tr>');
        preg_match('/(\d+)\.([0-9]) MB/', $flsize, $match);
        if (($match[0]) > 100) {
            html_error("You need to split the file first directly from filesmonster, well it's a risk of free download, sorry!");
        } else {
            $freeform = cut_str($page, "<form id='slowdownload'", "</form>");
            if (preg_match('#method="post" action="(.*)">#', $freeform, $fl)) {
                $link = $fl[1];
            }
            $page = $this->GetPage($link);
            $id = cut_str($page, "reserve_ticket('","')");
            $match = cut_str($page, "action: '","'");
            $page = $this->GetPage("http://filesmonster.com".$id);
            $dlcode = cut_str($page, '"dlcode":"','"');
            $link = "http://filesmonster.com".$match.$dlcode;
            $page = $this->GetPage($link);
            if (preg_match('/Next free download will be available in (\d+) min/', $page, $msg)) {
                html_error($msg[0]);
            }
            if (stristr($page, "Enter Captcha code below")) {
                $k = cut_str($page, 'recaptcha.net/challenge?k=', '"');
                $page = $this->GetPage("http://www.google.com/recaptcha/api/challenge?k=" . $k);
                $ch = cut_str($page, "challenge : '", "'");
                $img = "http://www.google.com/recaptcha/api/image?c=".$ch;
                $page = $this->GetPage($img);
                $capt_img = substr($page, strpos($page, "\r\n\r\n") + 4);
                $imgfile = DOWNLOAD_DIR."filesmonster.jpg";
                if (file_exists($imgfile)) {
                    unlink($imgfile);
                }
                write_file($imgfile, $capt_img);

                $data = $this->DefaultParamArr($link);
                $data['step'] = "1";
                $data['recaptcha_challenge_field'] = $ch;
                $this->EnterCaptcha($imgfile, $data, 20);
                exit();
            }
        }
    }

    private function DownloadFree($link) {
        $post = array();
        $post['recaptcha_challenge_field'] = $_POST['recaptcha_challenge_field'];
        $post['recaptcha_response_field'] = $_POST['captcha'];
        $page = $this->GetPage($link, 0, $post, $link);
        if (stristr($page, "Please wait")) {
            preg_match("%<span id='sec'>(\d+)</span>%", $page, $wait);
            $this->CountDown($wait[1]);
        }
        $tlink = cut_str($page, "get_link('","')");
        $tlink = "http://filesmonster.com".$tlink;
        $page = $this->GetPage($tlink, 0, 0, $link);
        $dlink = cut_str($page, '"url":"','"');
        if (!$dlink) html_error("Error, Free Download link not found");
        $dlink = str_replace("\/", "/", $dlink);
        $Url = parse_url($dlink);
        $FileName = basename($Url['path']);
        $this->RedirectDownload($dlink, $FileName, 0, 0, $link);
        exit();
    }

    private function Premium($link) {
        global $premium_acc;

        $post = array();
        $post['act'] = "login";
        $post['user'] = $_REQUEST["premium_user"] ? trim($_REQUEST["premium_user"]) : $premium_acc ["filesmonster_com"] ["user"];
        $post['pass'] = $_REQUEST["premium_pass"] ? trim($_REQUEST["premium_pass"]) : $premium_acc ["filesmonster_com"] ["pass"];
        $post['login'] = "Login";
        $page = $this->GetPage("http://filesmonster.com/login.php", 0, $post, "http://filesmonster.com/");
        is_present($page, 'Username/Password can not be found in our database!', 'Username/password invalid');
        $cookie = GetCookiesArr($page);

        $page = $this->GetPage($link, $cookie);
        if (preg_match('%<a href="(.+)"><span class="huge_button_green_left">%', $page, $match)) {
            $tlink = trim($match[1]);
        }
        $page = $this->GetPage($tlink, $cookie, 0, $link);
        if (preg_match('#\/dl\/gpl\/[^"]+#', $page, $temp)) {
            $prelink = 'http://filesmonster.com'.$temp[0];
        }
        $page = $this->GetPage($prelink, $cookie, 0, $tlink);
        $dlink = cut_str($page, 'url":"', '"');
        if (!$dlink) html_error("Error, Premium Download link not found");
        $dlink = str_replace('\/', '/', $dlink);
        $Url = parse_url($dlink);
        $FileName = basename($Url['path']);
        $this->RedirectDownload($dlink, $FileName, $cookie);
        exit();
    }
}

//filesmonster free download plugin by Ruud v.Tony 23-06-2011
//updated 11-07-2011 by Ruud v.Tony for checking link
//updated 30-08-2011 by Ruud v.Tony to support premium
?>
