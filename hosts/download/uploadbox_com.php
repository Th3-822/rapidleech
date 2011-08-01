<?php
if (!defined('RAPIDLEECH')) {
    require_once ("index.html");
    exit;
}

class uploadbox_com extends DownloadClass {

    public function Download($link) {
        if ($_POST['step'] == '1') {
            $this->Free($link);
        } else {
            $this->Retrieve($link);
        }
    }

    private function Retrieve($link) {
        $page = $this->GetPage($link);
        is_present($page, "File deleted from service", "File not found or not exist!");

        if (preg_match('%<form method="post" action="(.*)" name="f_form">%', $page, $match)) {
            $link = 'http://uploadbox.com'.$match[1];
        }
        $post = array('free' => 'yes');
        $page = $this->GetPage($link, 0, $post, $link);
        if (preg_match('#(\d+)</strong> minutes#', $page, $match)) html_error("The limit of traffic for you is exceeded. Please wait <strong>$match[1]</strong> minutes");
        if (preg_match('%<div id="time_go">(\d+)</div>%', $page, $wait)) $this->CountDown ($wait[1]);
        if (stristr($page, 'Enter CAPTCHA code here')) {
            $img = 'http://uploadbox.com'.cut_str($page, '<img id="ccaptcha" src="','"');

            $data = $this->DefaultParamArr($link);
            $data['step'] = '1';
            $data['code'] = cut_str($page, 'name="code" value="','"');
            $this->EnterCaptcha($img, $data);
            exit;
        }
    }

    private function Free($link) {
        $post = array('enter' => $_POST['captcha'], 'code' =>  $_POST['code'], 'go' => 'yes');
        $page = $this->GetPage($link, 0, $post, $link);
        if (!stristr($page, 'If downloading hasn\'t started automatically')) html_error('Wrong Captcha!');
        if (!preg_match('#http:\/\/.+get\.uploadbox\.com\/get\/[^"]+#', $page, $dl))  html_error("Error: Download link not found!");
        $Url = parse_url($dl[0]);
        $FileName = basename($Url['path']);
        $this->RedirectDownload($dl[0], $FileName, 0, 0, $link);
        exit;
    }
}

/*
 * uploadbox.com free download plugin by Ruud v.Tony 28/07/2011
 */
?>