<?php
if (!defined('RAPIDLEECH')) {
    require_once ('index.html');
    exit();
}

class slingfile_com extends DownloadClass {

    public function Download($link) {
        if ($_POST['step'] == '1') {
            $this->Free($link);
        } else {
            $this->Retrieve($link);
        }
    }
    
    private function Retrieve($link) {
        $page = $this->GetPage($link);
        is_present($page, 'The file you have requested has been deleted', 'The file you have requested has been deleted');
        $cookie = GetCookies($page);

        if (preg_match('#id="dltimer">(\d+)</span>#', $page, $wait)) {
            $this->CountDown($wait[1]);
        } else {
            html_error('Timer ID not found!');
        }
        $post = array('show_captcha' => 'yes');
        $page = $this->GetPage($link, $cookie, $post, $link);
        is_present($page, 'The file you have requested has been deleted', 'The file you have requested has been deleted');
        
        if (strpos($page, 'recaptcha')) {
            $showdl = cut_str($page,  'name="show_dl_link" value="','"');
            $k = cut_str($page, 'api.recaptcha.net/challenge?k=', '"');
            $page = $this->GetPage('http://www.google.com/recaptcha/api/challenge?k=' . $k);
            $ch = cut_str($page, "challenge : '", "'");
            if ($ch) {
                $page = $this->GetPage('http://www.google.com/recaptcha/api/image?c=' . $ch);
                $capture = substr($page, strpos($page, "\r\n\r\n") + 4);
                $imgfile = DOWNLOAD_DIR . "slingfile.jpg";
                if (file_exists($imgfile)) {
                    unlink($imgfile);
                }
                 write_file($imgfile, $capture);
            } else {
                html_error("Error getting CAPTCHA image.");
            }
            
            $data = $this->DefaultParamArr($link, $cookie, $link);
            $data['step'] = '1';
            $data['recaptcha_challenge_field'] = $ch;
            $data['show_dl_link'] = $showdl;
            $this->EnterCaptcha($imgfile, $data, 10);
            exit();
        }
    }
    
    private function Free($link) {
        $post = array();
        $post['recaptcha_challenge_field'] = $_POST['recaptcha_challenge_field'];
        $post['recaptcha_response_field'] = $_POST['captcha'];
        $post['show_dl_link'] = $_POST['show_dl_link'];
        $cookie = urldecode($_POST['cookie']);
        $link = urldecode($_POST['link']); //if we want to insert link, must use urldecode first otherwise it will failed to detect
        $page = $this->GetPage($link, $cookie, $post, $link);
        if (!preg_match('/http:\/\/.+slingfile\.com\/gdl\/[^\"]+/', $page, $dl)) {
            html_error('Error, Download link not found!');
        }
        $Url = parse_url($dl[0]);
        $FileName = basename($Url['path']);
        $this->RedirectDownload($dl[0], $FileName, $cookie, 0, $link);
        exit();
    }
}

/*
 * slingfile.com free download plugin by Ruud v.Tony 23-Aug-2011
 */
?>
