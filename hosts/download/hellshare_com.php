<?php
if (!defined('RAPIDLEECH')) {
    require_once("index.html");
    exit();
}

class hellshare_com extends DownloadClass {
    
    public function Download($link) {
        if ($_REQUEST['step'] == '1') {
            $post['captcha'] = $_POST['captcha'];
            $post['submit'] = 'Download';
            $dlink = urldecode($_POST['referer']);
            $cookie = urldecode($_POST['cookie']);
            $link = urldecode($_POST['link']);
            $FileName = basename(parse_url($dlink, PHP_URL_PATH));
            $this->RedirectDownload($dlink, $FileName, $cookie, $post, $link);
			exit;
        } else {
            $page = $this->GetPage($link);
            is_present($page, "File not found.");
            $cookie = CookiesToStr(GetCookiesArr($page));
            if (preg_match('%<a href="([^"]+)" class="ajax button button-devil">%', $page, $match)) $free = "http://download.hellshare.com".$match[1];
            $page = $this->GetPage($free, $cookie, 0, $link."\r\nX-Requested-With: XMLHttpRequest");
            is_present($page, "The server is under the maximum load.");
            is_present($page, "You exceeded your today's limit for free download. You can download only 1 files per 24 hours.");
            $postlink = str_replace("\\", "", cut_str($page, 'style=\"margin-bottom:0\" action=\"', '\" method=\"POST\">'));
            $caplink = str_replace("\\", "", cut_str($page, 'id=\"captcha-img\"src=\"', '\" \/>'));

            $data = $this->DefaultParamArr($link, $cookie, $postlink);
            $data['step'] = '1';
            $this->EnterCaptcha($caplink, $data);
            exit();
        }
    }
}

/*
 * Hellshare free download plugin by Ruud v.Tony 31/12/2011
 */
?>
