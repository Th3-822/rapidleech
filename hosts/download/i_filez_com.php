<?php
if (!defined('RAPIDLEECH')) {
    require_once ('index.html');
    exit();
}

class i_filez_com extends DownloadClass {

    public function Download($link) {
        if ($_POST['step'] == '1') {
            $post['vvcid'] = $_POST['vvcid'];
            $post['verifycode'] = $_POST['captcha'];
            $post['FREE'] = 'Regular+download';
            $link = urldecode($_POST['link']);
            $cookie = urldecode($_POST['cookie']);
            $page = $this->GetPage($link, $cookie, $post, $link);
            if (preg_match("@<p class='notice'>(.*)<\/p>@", $page, $msg)) html_error($msg[1]);
            if (preg_match('@var sec=(\d+)@', $page, $wait)) $this->CountDown ($wait[1]);
            //actually we can get the download link directly without having to wait, but eeergh maybe they gave that for a reason...
            $dlink = urldecode(cut_str($page, "wait_input\").value= unescape('", "')"));
            $filename = parse_url($dlink);
            $FileName = basename($filename['path']);
            $this->RedirectDownload($dlink, $FileName, $cookie, 0, $link);
            exit();
        } else {
            $page = $this->GetPage($link);
            is_present($page, 'File was not found in the i-filez.com database. ');
            $cookie = GetCookies($page);

            if (!preg_match('@\/includes\/vvc\.php[?]vvcid=(\d+)@', $page, $cap)) html_error ('Captcha link not found!');
            $imglink = 'http://i-filez.com'.$cap[0];

            $data = $this->DefaultParamArr($link, $cookie);
            $data['step'] = '1';
            $data['vvcid'] = $cap[1];
            $this->EnterCaptcha($imglink, $data);
            exit();
        }
    }
}

/*
 * i-filez.com free download plugin by Ruud v.Tony 17-10-2011
 */
?>
