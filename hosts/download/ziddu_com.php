<?php
if(!defined('RAPIDLEECH')) {
    require_once ('index.html');
    exit();
}

class ziddu_com extends DownloadClass {
    
    public function Download($link) {
        
        if ($_REQUEST['step'] == '1') {
            $post['fid'] = $_POST['fid'];
            $post['tid'] = urlencode($_POST['tid']);
            $post['securitycode'] = $_POST['captcha'];
            $post['fname'] = $_POST['fname'];
            $post['Keyword'] = 'Ok';
            $post['submit'] = $_POST['submit'];
            $dlink = urldecode($_POST['link']);
            $cookie = urldecode($_POST['cookie']);
            $Referer = urldecode($_POST['referer']);
            $filename = $_POST['fname'];
            $this->RedirectDownload($dlink, $filename, $cookie, $post, $Referer);
            exit();
        } else {
            $page = $this->GetPage($link);
            is_present($page, "/errortracking.php?msg=File not found", "File not found");
            if (preg_match('@Location: (http:\/\/[^\r\n]+)@i', $page, $redir)) {
                $link = trim($redir[1]);
                $page = $this->GetPage($link);
            }
            $cookie = GetCookies($page);
            if (!preg_match('%<form action="((http:\/\/[^\/]+)\/[^"]+)" method="POST"%', $page, $fr)) html_error('Error [Post Link not found!]');
            $rlink = trim($fr[1]);
            $server = trim($fr[2]);
            if (!preg_match_all('%<input type="hidden" name="([^"]+)" id="([^"]+)" value="([^"]+)?" \/>%', $page, $one) || !preg_match_all('%<input type="submit" name="([^"]+)" value="([^"]+)" \/>%', $page, $two)) html_error('Error Post Data 1 not found!');
            $match = array_merge(array_combine($one[1], $one[3]), array_combine($two[1], $two[2]));
            $post = array();
            foreach ($match as $k => $v) {
                $post[$k] = $v;
            }
            $page = $this->GetPage($rlink, $cookie, $post, $link);
            is_present($page, "File not found");
            $form = cut_str($page, '<form name="securefrm"', '</form>');
            if (!preg_match('@action="([^"]+)" method="post"@', $form, $dl)) html_error('Error [Download Link not found]');
            $dlink = $server.$dl[1];
            if (!preg_match_all('%<input type="(hidden|submit)" name="([^"]+)" id="([^"]+)" value="([^"]+)?"\/?>%', $form, $match)) html_error('Error [Post Data 2 not found!]');
            $match = array_combine($match[2], $match[4]);
            if (!preg_match('%mg src="([^"]+)"%', $form, $c)) html_error('Error [Captcha Data not found!]');
            
            // download captcha image
            $cap = $this->GetPage($server.$c[1], $cookie);
            $capt_img = substr($cap, strpos($cap, "\r\n\r\n") + 4);
            $imgfile = DOWNLOAD_DIR . "ziddu_captcha.png";

            if (file_exists($imgfile)) unlink($imgfile);
            if (empty($capt_img) || !write_file($imgfile, $capt_img)) html_error("Error getting CAPTCHA image.", 0);
            // Captcha img downloaded
            
            $data = array_merge($this->DefaultParamArr($dlink, $cookie, $rlink), $match);
            $data['step'] = '1';
            $this->EnterCaptcha($imgfile, $data);
            exit();
        }
    }
    
    public function CheckBack($header) {
        is_notpresent($header, 'ontent-Disposition: attachment', 'You have entered wrong captcha!');
    }
}
/*
 * WRITTEN by kaox 17-sep-2009
 * UPDATE by kaox 15-sep-2010
 * CONVERTED INTO OOP FORMAT by Ruud v.Tony 15-02-2012
 */

?>