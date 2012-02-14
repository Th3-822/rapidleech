<?php
if (!defined('RAPIDLEECH')) {
    require_once('index.html');
    exit();
}

class easybytez_com extends DownloadClass {
    
    public function Download($link) {
        global $premium_acc;
        
        if (!$_REQUEST['step']) {
            $this->page = $this->GetPage($link, "lang=english");
            is_present($this->page, "The file you were looking for could not be found, sorry for any inconvenience.");
        }
        if ($_REQUEST['premium_acc'] == 'on' && (($_REQUEST['premium_user'] && $_REQUEST['premium_pass'])||($premium_acc['easybytez_com']['user'] && $premium_acc['easybytez_com']['pass']))) {
            return $this->Premium($link);
        } else {
            return $this->Free($link);
        }
    }
    
    private function Premium($link) {
        html_error("Not supported now!");
    }
    
    private function Free($link) {
        $form = cut_str($this->page, "<Form method=\"POST\" action=''>", "</Form>");
        if (!preg_match_all('@<input type="hidden" name="([^"]+)" value="([^"]+)?">@', $form, $one) || !preg_match_all('@<input type="submit" name="(\w+_free)" value="([^"]+)" class="btn">@', $form, $two)) html_error("Error: Post Data 1 [FREE] not found!");
        $match = array_merge(array_combine($one[1], $one[2]), array_combine($two[1], $two[2]));
        $post = array();
        foreach ($match as $key => $value) {
            $post[$key] = $value;
        }
        $page = $this->GetPage($link, "lang=english", $post, $link);
        is_present($page, cut_str($page, '<div class="err">', '<br>'));
        unset($post);
        $form = cut_str($page, '<Form name="F1" method="POST"', '</Form>');
        if (!preg_match('@(\d+)<\/span> seconds@', $form, $wait)) html_error("Error: Timer not found!");
        $this->CountDown($wait[1]);
        if (!preg_match_all('@<input type="hidden" name="([^"]+)" value="([^"]+)?">@', $form, $match)) html_error("Error: Post Data 2 [FREE] not found!");
        $match = array_combine($match[1], $match[2]);
        $post = array();
        foreach ($match as $key => $value) {
            $post[$key] = $value;
        }
        $page = $this->GetPage($link, "lang=english", $post, $link);
        if (!preg_match('@Location: (http(s)?:\/\/[^\r\n]+)@i', $page, $dl)) html_error("Error: Download Link [FREE] not found!");
        $dlink = trim($dl[1]);
        $filename = basename(parse_url($dlink, PHP_URL_PATH));
        $this->RedirectDownload($dlink, $filename, 0, 0, $link);
        exit();
    }
}

/*
 * by Ruud v.Tony 29-01-2012
 */
?>
