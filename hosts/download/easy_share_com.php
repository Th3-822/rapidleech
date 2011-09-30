<?php
if (!defined('RAPIDLEECH')) {
    require_once ("index.html");
    exit ();
}

class easy_share_com extends DownloadClass {

    public function Download($link) {
        global $premium_acc;
        if (( $_REQUEST ["premium_acc"] == "on" && $_REQUEST ["premium_user"] && $_REQUEST ["premium_pass"] ) || ( $_REQUEST ["premium_acc"] == "on" && $premium_acc ["easyshare_com"] ["user"] && $premium_acc ["easyshare_com"] ["pass"] )) {
            $this->DownloadPremium($link);
        } else if ($_POST['step'] == "1") {
            $this->DownloadFree($link);
        } else {
            $this->Retrieve($link);
        }
    }

  /*
   * exit for terminated download
   * return for continue download
   * $content is header content before download
   */
    public function CheckBack($content) {
        if (!strpos($content, "ontent-Disposition: attachment; ")){
            html_error("You have input wrong captcha, Please try again!");
        }
        return;
    }

    private function Retrieve($link) {
      global $Referer;
        $page = $this->GetPage($link, "language=en"); // keep page in english
        is_present($page, "The file could not be found", "The file could not be found. Please check the download link.");
        is_present($page, "File not available", "File not available");
        is_present($page, "Page not found", "The file could not be found. Please check the download link.");
        is_present($page, 'There is another download in progress from your IP', 'There is another download in progress from your IP. Please try to downloading later.');
        $cookie = GetCookies($page). "; language=en";
        $FileName = trim(str_replace(" ", ".", cut_str($page, 'Download ', ',')));
        // first timer
        if (preg_match('/wf = (\d+);/', $page, $wait)) {
            $this->CountDown($wait[1]);
        }
        if (!strpos($page, 'method="post" action="')) {
            // get captcha data
            $link = "http://www.easy-share.com".cut_str($page, "u='", "';");
            $page = $this->GetPage($link, $cookie, 0, $Referer, 0, 1);
            //get new timer, then refresh the page
            if (preg_match("/w='(\d+)';/", $page, $wait)) {
                if ($wait[1] > 90) {
                     // no post, seem I've mistaken, damn it...
                    $this->JSCountdown($wait[1]);
                } else {
                    $this->CountDown($wait[1]);
                }
                $page = $this->GetPage($link, $cookie, 0, $Referer, 0, 1);
            }
            $cookie = $cookie. '; ' . GetCookies($page);
        }
        $link = cut_str($page, 'method="post" action="', '"');
        $id = cut_str($page, 'name="id" value="', '"');
        // now we start to display the captcha data
        if (!preg_match('/Recaptcha\.create\("([^"]+)/i', $page, $cid)) {
            html_error('Can\'t find chaptcha id');
        }

        $page = $this->GetPage("http://www.google.com/recaptcha/api/challenge?k=$cid[1]&cachestop=" . rand() . "&ajax=1");
        $ch = cut_str($page, "challenge : '", "'");
        if ($ch) {
            $page = $this->GetPage("http://www.google.com/recaptcha/api/image?c=".$ch);
            $capture = substr($page, strpos($page, "\r\n\r\n") + 4);
            $imgfile = DOWNLOAD_DIR . "easyshare.jpg";
            if (file_exists($imgfile)) {
                unlink($imgfile);
            }
            write_file($imgfile, $capture);
        } else {
            html_error('Can\'t find challenge data!');
        }

        $data = $this->DefaultParamArr($link, $cookie);
        $data['step'] = '1';
        $data['recaptcha_challenge_field'] = $ch;
        $data['id'] = $id;
        $data['name'] = $FileName;
        $this->EnterCaptcha($imgfile, $data, 20);
        exit();
    }

    private function DownloadFree($link) {
        global $Referer;
        $post["recaptcha_challenge_field"] = $_POST['recaptcha_challenge_field'];
        $post["recaptcha_response_field"] = $_POST['captcha'];
        $post["id"] = $_POST['id'];
        $cookie = urldecode($_POST['cookie']);
        $dlink = urldecode($_POST['link']);
        $FileName = $_POST['name'];
        $this->RedirectDownload($dlink, $FileName, $cookie, $post, $Referer);
        $this->CheckBack($dlink);
        exit();
    }

    private function DownloadPremium($link) {
        global $premium_acc, $pauth, $Referer;
        $Referer = "http://www.easy-share.com/";
        $page = $this->GetPage($link, 0, 0, 0, $pauth);
        is_present($page, 'File was deleted');
        is_present($page, 'File not found');
        $FileName = trim(cut_str($page, "<title>Download ", ","));
        $FileName = str_replace(" ", ".", $FileName);
        $login = "http://www.easy-share.com/accounts/login";
        $post = array();
        $post ["login"] = $_REQUEST ["premium_user"] ? $_REQUEST ["premium_user"] : $premium_acc ["easyshare_com"] ["user"];
        $post ["password"] = $_REQUEST ["premium_pass"] ? $_REQUEST ["premium_pass"] : $premium_acc ["easyshare_com"] ["pass"];
        $post ["remember"] = "1";
        $page = $this->GetPage($login, 0, $post, "http://www.easy-share.com/", $pauth);
        $cookies = GetCookies($page);
        if (!preg_match("#PREMIUM=[\w%]+#", $cookies, $Premium)) {
            html_error("Login Failed , Bad username/password combination");
        }
        preg_match("#PHPSESSID=\w+#", $cookies, $PhpSessId);
        $page = $this->GetPage($link, $cookies, 0, $Referer, $pauth);
        $cookies = $PhpSessId[0] . "; " . $Premium[0] . "; " . GetCookies($page);
        if (preg_match("#Location: (.*)#", $page, $prelink)) {
            if (function_exists(encrypt) && $cookies != "") {
                $cookies = encrypt($cookies);
            }
            $Url = parse_url($prelink[1]);
            insert_location("$PHP_SELF?filename=" . urlencode($FileName) .
                    "&host=" . $Url["host"] .
                    "&path=" . urlencode($Url["path"] . ($Url["query"] ? "?" . $Url["query"] : "")) .
                    "&referer=" . urlencode($Referer) .
                    "&cookie=" . urlencode($cookies) .
                    "&email=" . ($_GET["domail"] ? $_GET["email"] : "") .
                    "&partSize=" . ($_GET["split"] ? $_GET["partSize"] : "") .
                    "&method=" . $_GET["method"] . "&proxy=" . ($_GET["useproxy"] ? $_GET["proxy"] : "") .
                    "&saveto=" . $_GET["path"] . "&link=" . $link . ($_GET["add_comment"] == "on" ? "&comment=" . urlencode($_GET["comment"]) : "") .
                    "&pauth=" . (isset($_GET["audl"]) ? "&audl=doum" : ""));
        }
        exit();
    }
}

/* * ************************************************\
  FIXED by kaox 04/07/2009
  FIXED and RE-WRITTEN by rajmalhotra on 10 Jan 2010
  FIXED by rajmalhotra on 12 Feb 2010 => FIXED downloading from Premium Account
  FIXED by vdhdevil on 01 Dec 2010 => Fixed Premium for v42
  FIXED by Ruud v.Tony on 6 Feb 2011 => Fixed the free codes, my first rapidleech code made, lol :D
  FIXED by Ruud v.Tony on 25 Sept 2011 => Fixed the captcha display problem...
  \************************************************* */
?>