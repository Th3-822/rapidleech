<?php
if (!defined('RAPIDLEECH')) {
    require_once ("index.html");
    exit ();
}

class filehook_com extends DownloadClass {

    public function Download($link) {
        global $premium_acc;
            if (($_REQUEST['premium_acc'] == 'on' && $_REQUEST['premium_user'] && $_REQUEST['premium_pass']) || ($_REQUEST['premium_acc'] == 'on' && $premium_acc['filehook_com'] ['user'] && $premium_acc['filehook_com'] ['pass'])) {
                $this->DownloadPremium($link);
            } elseif ($_POST['step'] == '1') {
                $this->DownloadFree($link);
            } else {
                $this->Retrieve($link);
            }
    }

    private function Retrieve($link) {
        global $Referer;
            $page = $this->GetPage($link);
            is_present($page, "File Not Found", "Have u check ur link? The file doesn't exist!");

            $id = cut_str($page, 'name="id" value="','"');
            $fname = cut_str($page, 'name="fname" value="','"');

            $post = array();
            $post['op'] = "download1";
            $post['usr_login'] = "";
            $post['id'] = $id;
            $post['fname'] = $fname;
            $post['referer'] = $link;
            $post['op0'] = "download1";
            $post['usr_login0'] = "";
            $post['id0'] = "";
            $post['fname0'] = $fname;
            $post['referer0'] = $link;
            $post['method_free'] = "Free Download";
            $page = $this->GetPage($link, 0, $post, $link);
            $rand = cut_str($page, 'name="rand" value="','"');
            if (preg_match("#You have to wait (\d+) minutes, (\d+) seconds till next download#",$page,$message)){
                html_error($message[0]);
            }
            if (preg_match('#(\d+)</span>seconds#', $page, $wait)) {
                $this->CountDown($wait[1]);
            }
            if (stristr($page, "Enter code below:")) {
                preg_match('#(http://filehook.com/captchas/.+)"#', $page, $temp);
                
                $data = $this->DefaultParamArr($link, 0, $link);
                $data['step'] = '1';
                $data['id'] = $id;
                $data['rand'] = $rand;
                $this->EnterCaptcha($temp[1], $data, 10);
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
        $post['code'] = $_POST['captcha'];
        $post['down_script'] = "1";
        $page = $this->GetPage($link, 0, $post, $link);
        if (strpos($page, "Wrong captcha")) {
            return $this->Retrieve($link);
        }
        if (!preg_match('#(http:\/\/.+(:\d+)?\/d\/[^"]+)"#', $page, $dl)) {
            html_error("Sorry, Download link not found, contact the author n post the link which u have this error");
        }
        $dlink = trim($dl[1]);
        $Url = parse_url($dlink);
        $FileName = basename($Url['path']);
        $this->RedirectDownload($dlink, $FileName, 0, 0, $link);
        exit();
    }

    private function DownloadPremium($link) {
        html_error("This plugin doesn't support premium!");
    }
}

//filehook free download plugin by Ruud v.Tony 21-06-2011 (for fun :D)
?>
