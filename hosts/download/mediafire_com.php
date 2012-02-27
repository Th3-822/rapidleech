<?php

if (!defined('RAPIDLEECH')) {
    require_once("index.html");
    exit;
}

class mediafire_com extends DownloadClass {

    public function Download($link) {
        if (isset($_POST['mfpassword']) && ($_POST['mfpassword'] != "")) {
            $Cookies = urldecode($_POST['cookie']);
            $link = urldecode($_POST['link']);
            $page = $this->GetPage($link, $Cookies, array("downloadp" => $_POST['mfpassword']), $link);
        } else {
            $link = preg_replace("#http://(www.)?mediafire.com/(download.php)?#", "http://www.mediafire.com/", $link);
            $page = $this->GetPage($link);
            is_present($page, "error.php?errno=320", "Link is not available");
            $Cookies = GetCookies($page);
        }
        if (strpos($page, 'name="downloadp" id="downloadp"')) {
            $DefaultParam = $this->DefaultParamArr($link, $Cookies);
            $html = '<form action="index.php" method="POST">';
            foreach ($DefaultParam as $key => $value) {
                $html.='<input type="hidden" name="' . $key . '" value="' . $value . '"/>';
            }
            $html.='Enter your password here </br><input type="text" name="mfpassword" value="" placeholder="Enter your password here" autofocus="autofocus" required="required" /><input type="submit" name="action" value="Submit"/></form>';
            echo $html;
            exit;
        }
        if (preg_match('@Location: (http:\/\/[^\r\n]+)@i', $page, $dl) || preg_match('@<a [^>]*href="(https?://[^\"]+)"[^>]*>Download@i', $page, $dl)) {
            $dlink = trim($dl[1]);
            $this->RedirectDownload($dlink, "Mediafire.com", $Cookies);
            exit;
        } else {
            html_error("Error: Download link [FREE] not found!");
        }
    }
}

/*
 * credit to farizemo [at] rapidleech forum
 * by vdhdevil
 * remove additional function for temporary fix until get finished - Ruud v.Tony 06-01-2011
 * fix for shared premium link by Ruud v.Tony 23-01-2012
 * regex fix for download link not found by Th3-822 24-02-2012
 */
?>