<?php
if (!defined('RAPIDLEECH')) {
    require_once("index.html");
    exit();
}

class ifile_it extends DownloadClass {

    public function Download($link) {
        global $premium_acc;
        if (($_REQUEST['premium_acc'] == 'on' && $_REQUEST['premium_acc']['user'] && $_REQUEST['premium_acc']['pass']) || ($_REQUEST['premium_acc'] == 'on' && $premium_acc['ifile_it']['user'] && $premium_acc['ifile_it']['pass'])) {
            $this->Member($link);
        } else {
            $this->NonMember($link);
        }
    }

    private function NonMember($link) {
        $page = $this->GetPage($link);
        is_present($page, "File Not Found", "File Not Found");
        is_present($page, "no such file", "File Not Found");
        $cookie = GetCookies($page);

        $posturl = cut_str($page, "var _url = '", "';");
        $ch = cut_str($page, "var __recaptcha_public		=	'", "';");
        if (preg_match('@http:\/\/ifile\.it\/(\w+)\/@', $posturl, $ids)) {
            $id = trim($ids[1]);
        }
        $FileName = basename($posturl);
        $nextlink = 'http://ifile.it/download-request.json?ukey=' . $id . '&3a1d96df2de71a63f10c89926ed13c72=aa0b66124b9b69f8c944eb2bfbe7c004bb573aad&55fa0b323cd79944023bb4e061e043ef=99c4bdfb02a89e01d7b88780beb519396572e0ab';
        // request download ticket
        // required xml request added, u can use the available xml in GetPage function or added in referer,
        // for example added xml request in referer, u could look in http://www.rapidleech.com/topic/11552-freepremium-turbobitnet/page__view__findpost__p__54776
        $page = $this->GetPage($nextlink, $cookie, 0, $link, 0, 1);
        if (strpos($page, '"status":"ok"')) {
            if (strpos($page, '"captcha":1')) { // must be set to '0' the captcha value
                html_error('I still dont know where this damn captcha should get to the next post, will ask vdhdevil or Th3-822 for this (oh well, u know...)!');
            } else {
                $page = $this->GetPage($posturl, $cookie, 0, $link);
            }
        }
        $page = $this->GetPage($posturl, $cookie, 0, $link);
        $dlink = cut_str($page, '<a target="_blank" href="', '"');
        if (!$dlink) html_error("Error: Plugin is out of date!");
        $Url = parse_url($dlink);
        if (!$FileName) $FileName = basename($Url['path']);
        $this->RedirectDownload($dlink, $FileName, $cookie, 0, $link);
        exit();
    }

    private function Member($link) {
        html_error('I haven\'t made yet, sorry, :P');
    }

}

/*
 * ifile.it free download plugin by Shy2play(untamedsolitude.co.cc) for kaskus.us
 * rewritten in OOP format by Ruud v.Tony 25-07-2011
 * updated by Ruud v.Tony 19-09-2011 for new ifile.it format (json format, oh no... :P)
 */
?>