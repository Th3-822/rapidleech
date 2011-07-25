<?php    
if (!defined('RAPIDLEECH')) {
    require_once ("index.html");
    exit ();
}

class azsharing_com extends DownloadClass {

    public function Download($link) {
        global $premium_acc;
        if (($_REQUEST ["premium_acc"] == "on" && $_REQUEST ["premium_user"] && $_REQUEST ["premium_pass"]) || ($_REQUEST ["premium_acc"] == "on" && $premium_acc ["azsharing_com"] ["user"] && $premium_acc ["azsharing_com"] ["pass"])) {
            $this->DownloadPremium($link);
        } elseif ($_POST['step'] == "1") {
            $this->DownloadFree($link);
        } else {
            $this->PrepareFree($link);
        }
    }

    private function PrepareFree($link) {
        $page = $this->GetPage($link);
        is_present($page, "File Not Found", "The file you were looking for could not be found, sorry for any inconvenience.");

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
        if (preg_match('%<span id="countdown">([0-9]+)</span>%', $page, $countd)) {
            $this->CountDown($countd[1]);
        }
        $rand = cut_str($page, 'name="rand" value="','"');
        $temp = cut_str($page, '<img alt="captcha" src="','" />');
        $data = $this->DefaultParamArr($link, 0, $link);
        $data['step'] = "1";
        $data['id'] = $id;
        $data['rand'] = $rand;
        $this->EnterCaptcha($temp, $data);
        exit;
    }

    private function DownloadFree($link) {
        $post = array();
        $post['op'] = "download2";
        $post['id'] = $_POST['id'];
        $post['rand'] = $_POST['rand'];
        $post['referer'] = $_POST['referer'];
        $post['method_free'] = "Free Download";
        $post['method_premium'] = "";
        $post['code'] = $_POST['captcha'];
        $post['down_direct'] = "1";
        $page = $this->GetPage($link, 0, $post, $link);
        if (!preg_match('#http://www\d+[^"]+#', $page, $dl)) {
            html_error("Error, Download link not found");
        }
        $dlink = trim($dl[0]);
        $Url = parse_url($dlink);
        $FileName = basename($Url['path']);
        $this->RedirectDownload($dlink, $FileName, 0, 0, $link);
        exit;
    }

    private function DownloadPremium($link) {
        html_error("Not supported now, please donate your premium account to support premium!");
    }
}

//Azsharing Free Download Plugin by Ruud v.Tony 11-04-2011
?>