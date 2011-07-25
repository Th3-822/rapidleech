<?php
if (!defined('RAPIDLEECH')) {
    require_once("index.html");
    exit;
}

class filesmonster_com extends DownloadClass {

    public function Download($link) {
        if (!stristr($link, '/free/2/')) {
            $page = $this->GetPage($link);
            is_present($page, "File was deleted", "File was deleted by owner or it was deleted for violation of copyrights");
            //check the file size
            $flsize = cut_str($page, 'File size:</td>', '</tr>');
            preg_match('/(\d+)\.([0-9]) MB/', $flsize, $match);
            if (($match[0]) > 100) {
                html_error("You need to split the file first directly from filesmonster, well it's a risk of free download, sorry!");
            } else {
                $freeform = cut_str($page, "<form id='slowdownload'", "</form>");
                if (preg_match('#method="post" action="(.*)">#', $freeform, $fl)) {
                    $link = $fl[1];
                }
                $page = $this->GetPage($link);
                $id = cut_str($page, "reserve_ticket('","')");
                $match = cut_str($page, "action: '","'");
                $page = $this->GetPage("http://filesmonster.com".$id);
                $dlcode = cut_str($page, '"dlcode":"','"');
                $link = "http://filesmonster.com".$match.$dlcode;
                $page = $this->GetPage($link);
            }
            unset($page);
        }
        if ($_POST['step'] == '1') {
            $this->DownloadFree($link);
        } else {
            $this->Retrieve($link);
        }
    }

    private function Retrieve($link) {
        $page = $this->GetPage($link);
        if (preg_match('/Next free download will be available in (\d+) min/', $page, $msg)) {
            html_error($msg[0]);
        }
        if (stristr($page, "Enter Captcha code below")) {
            $k = cut_str($page, 'recaptcha.net/challenge?k=', '"');
            $page = $this->GetPage("http://www.google.com/recaptcha/api/challenge?k=" . $k);
            $ch = cut_str($page, "challenge : '", "'");
            $img = "http://www.google.com/recaptcha/api/image?c=".$ch;
            $page = $this->GetPage($img);
            $capt_img = substr($page, strpos($page, "\r\n\r\n") + 4);
            $imgfile = DOWNLOAD_DIR."filesmonster.jpg";
            if (file_exists($imgfile)) {
                unlink($imgfile);
            }
            write_file($imgfile, $capt_img);

            $data = $this->DefaultParamArr($link);
            $data['step'] = "1";
            $data['recaptcha_challenge_field'] = $ch;
            $this->EnterCaptcha($imgfile, $data, 20);
            exit();
        }
    }

    private function DownloadFree($link) {
        $post = array();
        $post['recaptcha_challenge_field'] = $_POST['recaptcha_challenge_field'];
        $post['recaptcha_response_field'] = $_POST['captcha'];
        $page = $this->GetPage($link, 0, $post, $link);
        if (stristr($page, "Please wait")) {
            preg_match("%<span id='sec'>(\d+)</span>%", $page, $wait);
            $this->CountDown($wait[1]);
        }
        $tlink = cut_str($page, "get_link('","')");
        $tlink = "http://filesmonster.com".$tlink;
        $page = $this->GetPage($tlink, 0, 0, $link);
        $dlink = cut_str($page, '"url":"','"');
        if (!$dlink) html_error("Sorry, Download link not found");
        $dlink = str_replace("\/", "/", $dlink);
        $Url = parse_url($dlink);
        $FileName = basename($Url['path']);
        $this->RedirectDownload($dlink, $FileName, 0, 0, $link);
        exit();
    }
}

//filesmonster free download plugin by Ruud v.Tony 23-06-2011
//updated 11-07-2011 by Ruud v.Tony for checking link
?>
