<?php
if (!defined('RAPIDLEECH')) {
    require_once("index.html");
    exit();
}

class fileband_com extends DownloadClass {

    public function Download($link) {
        if ($_REQUEST['step'] == '1') {
            $link = urldecode($_POST['link']);
            $post['op'] = 'download2';
            $post['id'] = $_POST['id'];
            $post['rand'] = $_POST['rand'];
            $post['referer'] = $link;
            $post['method_free'] = 'Free Download';
            $post['method_premium'] = '';
            $post['code'] = $_POST['captcha'];
            $post['down_script'] = '1';
            $page = $this->GetPage($link, 0, $post, $link);
        } else {
            $page = $this->GetPage($link);
            is_present($page, "File Not Found");

            $id = cut_str($page, 'name="id" value="', '"');
            $FileName = cut_str($page, 'name="fname" value="', '"');

            $post['op'] = 'download1';
            $post['usr_login'] = '';
            $post['id'] = $id;
            $post['fname'] = $FileName;
            $post['referer'] = $link;
            $post['method_free'] = 'Free Download';
            $page = $this->GetPage($link, 0, $post, $link);
        }
        if (preg_match('@<p class="err">(.*)<br>@', $page, $match)) html_error($match[1]);
        if (preg_match('@(\d+)<\/span>&nbsp;<b>seconds@', $page, $wait)) $this->CountDown($wait[1]);
        if (strpos($page, "Enter code below:")) {
            if (preg_match('@http:\/\/fileband\.com\/captchas\/[^"]+@', $page, $cap)) $imglink = trim($cap[0]);
            $data = $this->DefaultParamArr($link);
            $data['id'] = $id;
            $data['rand'] = cut_str($page, 'name="rand" value="', '"');
            $data['step'] = '1';
            $this->EnterCaptcha($imglink, $data);
            exit();
        }
        if (!preg_match('@http:\/\/fileband\.com\/cgi\-bin\/dl\.cgi/[^\r\n]+@i', $page, $dl)) html_error("Error: Download link not found!");
        $FileName = basename(parse_url($dl[0], PHP_URL_PATH));
        $this->RedirectDownload($dl[0], $FileName, 0, 0, $link);
        exit();
    }
}

/*
 * Fileband.com Free Download plugin by Ruud v.Tony 02-01-2012
 */

?>
