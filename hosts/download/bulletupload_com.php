<?php
if (!defined('RAPIDLEECH')) {
    require_once ('index.html');
    exit();
}

class bulletupload_com extends DownloadClass {
    
    public function Download($link) {
        global $premium_acc;
        
        if (!$_REQUEST['step']) {
            $this->page = $this->GetPage($link, "lang=english");
            is_present($this->page, "The file you were looking for could not be found, sorry for any inconvenience.");
        }
        if ($_REQUEST['premium_acc'] == 'on' && (($_REQUEST['premium_user'] && $_REQUEST['premium_pass'])||($premium_acc['bulletupload_com']['user'] && $premium_acc['bulletupload_com']['pass']))) {
            html_error("Not supported [PREMIUM] now!");
        } else {
            $this->Free($link);
        }
    }
    
    private function Free($link) {
        if ($_REQUEST['down_direct'] == '1') {
            $link = urldecode($_POST['link']);
            
            $post = array();
            $post['op'] = $_POST['op'];
            $post['id'] = $_POST['id'];
            $post['rand'] = $_POST['rand'];
            $post['referer'] = $link;
            $post['method_free'] = $_POST['method_free'];
            $post['method_premium'] = '';
            $post['recaptcha_challenge_field'] = $_POST['challenge'];
            $post['recaptcha_response_field'] = $_POST['captcha'];
            $page = $this->GetPage($link, "lang=english", $post, $link);
        } else {
            $form = cut_str($this->page, '<Form method="POST" action=\'\'>', "</Form>");
            if (!preg_match_all('@<input type="hidden" name="([^"]+)" value="([^"]+)?">@', $form, $one) || !preg_match_all('@<input type="submit" name="(\w+_free)" value="([^"]+)" >@', $form, $two)) html_error("Error: Post Data 1 [FREE] not found!");
            $match = array_merge(array_combine($one[1], $one[2]), array_combine($two[1], $two[2]));
            $post = array();
            foreach ($match as $key => $value) {
                $post[$key] = $value;
            }
            $page = $this->GetPage($link, "lang=english", $post, $link);
        }
        if (strpos($page, "Type the two words:")) {
            $form = cut_str($page, '<Form name="F1" method="POST"', '</Form>');
            if (!preg_match('@(\d+)<\/span> seconds@', $form, $wait)) html_error("Error: Timer not found!");
            $this->CountDown($wait[1]);
            if (!preg_match_all('@<input type="hidden" name="([^"]+)" value="([^"]+)?">@', $form, $match)) html_error("Error: Post Data 2 [FREE] not found!");
            if (!preg_match('@\/api\/challenge\?k=([^"]+)">@', $form, $cap)) html_error("Error: Captcha Key found!");
            
            $ch = cut_str($this->GetPage("http://www.google.com/recaptcha/api/challenge?k=$cap[1]"), "challenge : '", "'");
            $capt = $this->GetPage("http://www.google.com/recaptcha/api/image?c=" . $ch);
            $capt_img = substr($capt, strpos($capt, "\r\n\r\n") + 4);
            $imgfile = DOWNLOAD_DIR . "bulletupload_captcha.jpg";

            if (file_exists($imgfile)) unlink($imgfile);
            if (empty($capt_img) || !write_file($imgfile, $capt_img)) html_error("Error getting CAPTCHA image.", 0);
            // Captcha img downloaded
            $data = array_merge($this->DefaultParamArr($link), array_combine($match[1], $match[2]));
            $data['challenge'] = $ch;
            $this->EnterCaptcha($imgfile, $data, 20);
            exit();
        }
        is_present($page, cut_str($page, '<div class="err">', '<br>'));
        if (!preg_match('@http:\/\/[\d.]+(:\d+)?\/[^|\r|\n|"]+@', $page, $dl)) html_error("Error: Download Link [FREE] not found!");
        $dlink = trim($dl[0]);
        $filename = basename(parse_url($dlink, PHP_URL_PATH));
        $this->RedirectDownload($dlink, $filename, 0, 0, $link);
        exit();
    }
}

/*
 * by Ruud v.Tony 28-01-2012
 */
?>
