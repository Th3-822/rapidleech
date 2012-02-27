<?php
if (!defined('RAPIDLEECH')) {
    require_once ('index.html');
    exit();
}

class transferbigfiles_com extends DownloadClass {

    public function Download($link) {
        $page = $this->GetPage($link);
        is_present($page, "This transfer has expired and the files are no longer available for download.");
        if (preg_match('@Location: (http(s)?:\/\/[^\r\n]+)@i', $page, $dl)) { //this is link that we have submit from autodownload
            $dlink = trim($dl[1]);
            $FileName = urldecode(cut_str($dlink, 'fn=', '\r\n'));
            $this->RedirectDownload($dlink, $FileName, 0, 0, $link);
        } else {
            if (!preg_match_all('@"DownloadUrl":"([^"]+)",@', $page, $temp)) html_error("Error [Redirect Link not found!]");
            $redir = array();
            foreach ($temp[1] as $k) {
                $redir[] = 'https://www.transferbigfiles.com' . $k;
            }
            if (count($redir) > 1) {
                $this->moveToAutoDownloader($redir);
                exit();
            } else {
                $page = $this->GetPage($redir[0], 0, 0, $link);
                if (!preg_match('@Location: (http(s)?:\/\/[^\r\n]+)@i', $page, $dl)) html_error('Error [Download Link not found!]');
                $dlink = trim($dl[1]);
                $FileName = urldecode(cut_str($dlink, 'fn=', '\r\n'));
                $this->RedirectDownload($dlink, $FileName, 0, 0, $link);
                exit();
            }
        }
    }
}

/*
 * written by Ruud v.Tony 15/02/2012
 */
?>