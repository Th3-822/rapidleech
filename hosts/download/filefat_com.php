<?php
if (!defined('RAPIDLEECH')) {
    require_once ("index.html");
    exit ();
}

class filefat_com extends DownloadClass {

    public function Download($link) {
        global $premium_acc;
            if ($_REQUEST['premium_acc'] == 'on' && (($_REQUEST['premium_user'] && $_REQUEST['premium_pass']) || ($premium_acc['filefat']['user'] && $premium_acc['filefat']['pass']))) {
                $this->Premium($link);
            } elseif ($_POST['step'] == '1') {
                $this->Free($link);
            } else {
                $this->Retrieve($link);
            }
    }

    private function Retrieve($link) {
        $page = $this->GetPage($link);
        is_present($page, "File Not Found", "File Not Found");

        $id = cut_str($page, 'name="id" value="','"');
        $FileName = cut_str($page, 'name="fname" value="','"');

        $post = array();
        $post['op'] = "download1";
        $post['usr_login'] = "";
        $post['id'] = $id;
        $post['fname'] = $FileName;
        $post['referer'] = $link;
        $post['method_free'] = "SLOW DOWNLOAD";
        $page = $this->GetPage($link, 0, $post, $link);
        $rand = cut_str($page, 'name="rand" value="','"');
        if (preg_match("#You have to wait (\d+) minutes, (\d+) seconds till next download#",$page,$message)){
            html_error($message[0]);
        }
        if (preg_match('#(\d+)</span> seconds#', $page, $wait)) {
            $this->CountDown($wait[1]);
        }
        $k = cut_str($page, 'api/challenge?k=', '"');
        $page = $this->GetPage("http://www.google.com/recaptcha/api/challenge?k=".$k);
        $ch = cut_str($page, "challenge : '", "'");
        $img = "http://www.google.com/recaptcha/api/image?c=".$ch;
        $page = $this->GetPage($img);
        $capt_img = substr($page, strpos($page, "\r\n\r\n") + 4);
        $imgfile = DOWNLOAD_DIR."filefat.jpg";
        if (file_exists($imgfile)) {
            unlink($imgfile);
        }
        write_file($imgfile, $capt_img);

        $data = $this->DefaultParamArr($link, 0, $link);
        $data['step'] = "1";
        $data['fname'] = $FileName;
        $data['id'] = $id;
        $data['rand'] = $rand;
        $data['recaptcha_challenge_field'] = $ch;
        $this->EnterCaptcha($imgfile, $data, 20);
        exit();
    }

    private function Free($link) {
        $post = array();
        $post['op'] = "download2";
        $post['id'] = $_POST['id'];
        $post['rand'] = $_POST['rand'];
        $post['referer'] = urldecode($_POST['referer']);
        $post['method_free'] = "SLOW DOWNLOAD";
        $post['method_premium'] = "";
        $post['recaptcha_challenge_field'] = $_POST['recaptcha_challenge_field'];
        $post['recaptcha_response_field'] = $_POST['captcha'];
        $post['down_script'] = "1";
        $FileName = $_POST['fname'];
        $page = $this->GetPage($link, 0, $post, $link);
        if (strpos($page, "Wrong captcha")) {
            return $this->Retrieve($link);
        }
        if (!stristr($page, 'Location:')) {
            html_error("Error, Download link not found!");
        }
        $dlink = trim(cut_str( $page, "Location: ", "\n" ));
        $Url = parse_url($dlink);
        if (!$FileName) $FileName = basename($Url['path']);
        $this->RedirectDownload($dlink, $FileName, 0, 0, $link);
        exit();
    }

    private function Premium($link) {
        html_error("Not supported now!");
    }
}

/*
 * Filefat free download plugin by Ruud v.Tony 25-07-2011
 */
?>
