<?php
if (!defined('RAPIDLEECH')) {
    require_once("index.html");
    exit;
}

class dailymotion_com extends DownloadClass {

    public function Download($link) {
        $page = $this->GetPage($link);
        is_present($page, 'HTTP/1.1 410 Gone', 'This video has been removed by the user.');
        is_present($page, 'HTTP/1.1 403 Forbidden', 'This video is forbidden to download!');
        is_present($page, '(private)', 'This video is set to be PRIVATE!');
        $Cookies = GetCookies($page);
        $FileName = trim(cut_str($page, '<title>', '</title>'));
        $FileName = str_replace(" ", "_", $FileName) . ".mp4";
        try {
            if (!preg_match('@"hqURL":"([^|\r|\n|"]+)@i', urldecode($page), $temp)) {
                preg_match('@"(?:sd)?URL":"([^|\r|\n|"]+)@i', urldecode($page), $temp);
            }
            if (!isset($temp[1])) {
                throw new Exception("Error : Video link not found!");
            }
            $temp = str_replace("\/", "/", $temp[1]) . "&redirect=0";
            $page = $this->GetPage($temp, $Cookies);
            if (!preg_match('#http://.+dailymotion.com/video/[^\r\n]+#', $page, $dlink)) {
                throw new Exception("Error : Direct link not found!");
            }
            $this->RedirectDownload($dlink[0], $FileName, $Cookies, 0, $temp);
            exit();
        } catch (Exception $e) {
            html_error($e->getMessage());
        }
    }
}

// dailymotion download plugin by vdhdevil
// small fix in regex & filename by Ruud v.Tony 07-11-2011
// small fix in regex based on SVN patch by Th3-822 13-12-2011
?>
