<?php
if (!defined('RAPIDLEECH')) {
    require_once ('index.html');
    exit();
}

class files_to extends DownloadClass {
    
    public function Download($link) {
        if ($_REQUEST['step'] == '1') {
            $post['txt_ccode'] = $_POST['captcha'];
            $link = urldecode($_POST['link']);
            $cookie = urldecode($_POST['cookie']);
            $page = $this->GetPage($link, $cookie, $post, $link);
        } else {
            $page = $this->GetPage($link);
            is_present($page, 'The requested file couldn\'t be found.');
            $cookie = GetCookies($page);
        }
        if (strpos($page, 'To download the requested file, please retype the following numbers:')) {
            $form = cut_str($page, '<form id="confirmform"', '</form>');
            if (strpos($form, cut_str($form, '<span class="red">', '</span>'))) echo ("<center><font color='red'><b>Wrong Captcha, Please Retry!</b></font></center>");
            //Download captcha img.
            $cap = $this->GetPage(cut_str($form, '<img src="', '"'), $cookie);
            $capt_img = substr($cap, strpos($cap, "\r\n\r\n") + 4);
            $imgfile = DOWNLOAD_DIR . "files_to_captcha.png";

            if (file_exists($imgfile)) unlink($imgfile);
            if (empty($capt_img) || !write_file($imgfile, $capt_img)) html_error("Error getting CAPTCHA image.", 0);
            // Captcha img downloaded
            $data = $this->DefaultParamArr($link.  cut_str($form, 'action="', '"'), $cookie);
            $data['step'] = '1';
            $this->EnterCaptcha($imgfile, $data);
            exit();
        }
        if (!preg_match('@http:\/\/\w+\.files\.to\/dl\/[^|\r|\n|"]+@', $page, $dl)) html_error('Error [Download Link not found!]');
        $filename = basename(parse_url($dl[0], PHP_URL_PATH));
        $this->RedirectDownload($dl[0], $filename, $cookie, 0, $link);
        exit();
    }
}

/*
 * by Ruud v.Tony 10-02-2012
 */
?>