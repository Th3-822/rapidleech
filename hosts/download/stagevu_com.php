<?php
if (!defined('RAPIDLEECH')) {
    require_once("index.html");
    exit;
}

class stagevu_com extends DownloadClass {
    
    public function Download($link) {
        $page = $this->GetPage($link);
        is_present($page, 'Error: No video with the provided information exists');
        is_present($page, 'Restricted Content', 'Error: This video has been categorized as \'mature\' therefore can\'t be downloaded without an account!');
        if (!preg_match('@http:\/\/n\d+\.stagevu.com\/v\/[^\']+@i', $page, $dl)) html_error('Error: Download link not found!');
        $dlink = trim($dl[0]);
        $FileName = basename(parse_url($dlink, PHP_URL_PATH));
        $this->RedirectDownload($dlink, $FileName);
    }
}

/* stagevu download plug-in written by mrbrownee70 07/05/09
 * fixed by Ruud v.Tony 20-12-2011
 */
?>