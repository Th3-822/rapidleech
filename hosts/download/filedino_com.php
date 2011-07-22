<?php
if (!defined('RAPIDLEECH')) {
    require_once ("index.html");
    exit ();
}

class filedino_com extends DownloadClass {

    public function Download($link) {
        if ($_POST['step'] == "1") {
            $this->DownloadFree($link);
        } else {
            $this->Retrieve($link);
        }
    }

    private function Retrieve($link) {
            $page = $this->GetPage($link);
            is_present($page, "The file was removed by administrator", "The file was removed by administrator");

            $id = cut_str($page, 'name="id" value="','"');
            $fname = cut_str($page, 'name="fname" value="','"');

            $post = array();
            $post['op'] = "download1";
            $post['usr_login'] = "";
            $post['id'] = $id;
            $post['fname'] = $fname;
            $post['referer'] = $link;
            $post['method_free'] = "Free Download";
            $page = $this->GetPage($link, 0, $post, $link);
            if (preg_match('/You have to wait (\d+) minute, (\d+) seconds till next download/', $page, $msg)) {
                html_error($msg[0]);
            }
            if (preg_match('#(\d+)</span> seconds#', $page, $wait)) {
                $this->CountDown($wait[1]);
            }
            $rand = cut_str($page, 'name="rand" value="','"');
            if (stristr($page, 'recaptcha')) {
                $k = cut_str($page, 'api/challenge?k=', '"');
                $page = $this->GetPage("http://www.google.com/recaptcha/api/challenge?k=".$k);
                $ch = cut_str($page, "challenge : '", "'");
                $img = "http://www.google.com/recaptcha/api/image?c=".$ch;
                $page = $this->GetPage($img);
                $capt_img = substr($page, strpos($page, "\r\n\r\n") + 4);
                $imgfile = DOWNLOAD_DIR."filedino.jpg";
                if (file_exists($imgfile)) {
                    unlink($imgfile);
                }
                write_file($imgfile, $capt_img);

                $data = $this->DefaultParamArr($link, 0, $link);
                $data['step'] = "1";
                $data['id'] = $id;
                $data['rand'] = $rand;
                $data['recaptcha_challenge_field'] = $ch;
                $this->EnterCaptcha($imgfile, $data, 20);
                exit();
            }
    }

    private function DownloadFree($link) {
        $post = array();
        $post['op'] = "download2";
        $post['id'] = $_POST['id'];
        $post['rand'] = $_POST['rand'];
        $post['referer'] = urldecode($_POST['referer']);
        $post['method_free'] = "Free Download";
        $post['method_premium'] = "";
        $post['recaptcha_challenge_field'] = $_POST['recaptcha_challenge_field'];
        $post['recaptcha_response_field'] = $_POST['captcha'];
        $post['down_script'] = "1";
        $link = $_POST['link'];
        $page = $this->GetPage($link, 0, $post, $link);
        if (strpos($page, 'Wrong captcha')) {
            return $this->Retrieve($link);
        }
        if (!stristr($page, 'Location:')) {
            html_error("Sorry, Download link not found, plugin need to be updated!");
        }
        $dlink = trim(cut_str( $page, "Location: ", "\n" ));
        $Url = parse_url($dlink);
        $FileName = basename($url['path']);
        $this->RedirectDownload($dlink, $FileName, 0, 0, $link);
        exit();
    }
}

/**********************************************************\
  filedino free download plugin by Ruud v.Tony 11-07-2011 
\**********************************************************/
?>
