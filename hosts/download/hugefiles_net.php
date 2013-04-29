<?php
if (!defined('RAPIDLEECH')) {
    require_once ('index.html');
    exit();
}

class hugefiles_net extends DownloadClass {
	public function Download($link) {
        global $options;
        if ($_REQUEST['captcha'] != '') {
            $post['op'] = $_POST['op'];
            $post['id'] = $_POST['id'];
            $post['rand'] = $_POST['rand'];
            $post['referer'] = $_POST['referer'];
            $post['method_free'] = 'Free Download';
			$post['method_premium'] = '';
            $post['down_direct'] = '1';
            $post['recaptcha_challenge_field'] = $_POST['recaptcha_challenge_field'];
            $post['recaptcha_response_field'] = $_POST['captcha'];
            $link = urldecode($_POST['link']);
			$cookies = urldecode($_POST['cookies']);
            $page = $this->GetPage($link, $cookies, $post);
        } else {
            $page = $this->GetPage($link);
		    is_present($page, 'http://www.hugefiles.net/404.html', 'File Not Found');
			$cookies = GetCookies($page);
            $form = cut_str($page, '<Form method="POST"', '</Form>');
            if (!preg_match_all('#<input type="hidden" name="([^"]+)" value="([^"]+)">#', $form, $dt)) html_error ("Error get Data of Download");
			$post = array_combine($dt[1], $dt[2]);
			$post['method_free'] = "Free Download";
            $page = $this->GetPage($link, $cookies, $post, $link);
        }
        if (strpos($page, 'Type the two words:') || strpos($page, 'Wrong captcha')) {
            if(strpos($page, 'Wrong captcha'))echo '<br><div style="color:#F00; font-size:16px; font-weight:bold;">Wrong Captcha</div>';
            $form = cut_str($page, '<Form name="F1" method="POST"', '</Form>');
            if (!preg_match('#api\/challenge\?k=([^"]+)"#', $page, $cp)) html_error("Error get Data Captcha");
            if (!preg_match_all('#<input type="hidden" name="([^"]+)" value="([^"]+)?">#', $form, $dt)) html_error ("Error get Data Form Download");
            $data = array_combine($dt[1], $dt[2]);
		    $page = $this->GetPage("http://www.google.com/recaptcha/api/challenge?k=".$cp[1]);
	        $sr = cut_str($page, "challenge : '", "'");
            $img = "http://www.google.com/recaptcha/api/image?c=".$sr;
            $page = $this->GetPage($img);
            $head = strpos($page, "\r\n\r\n");
            $img = substr($page, $head + 4);
            write_file($options['download_dir'] . "hugefiles_captcha.jpg", $img);
            $data['recaptcha_challenge_field'] = $sr;
			$data['link'] = urlencode($link);
			$data['cookies'] = urlencode($cookies);
            $this->EnterCaptcha($options['download_dir'] . "hugefiles_captcha.jpg", $data, 20);
            exit();
        }
        is_present($page, cut_str($page, '<div class="err">', '</div>'));
        if (!preg_match('#Location: (.*)#', $page, $dl)) html_error("Cannot find Download Link");
        $dlink = trim($dl[1]);
        $filename = basename(parse_url($dlink, PHP_URL_PATH));
        $this->RedirectDownload($dlink, $filename, 0, 0, $link);
        exit();
    }
}

/*
 * by SD-88 09.04.2013
 */
?>