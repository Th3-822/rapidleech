<?php
if (!defined('RAPIDLEECH')) {
    require_once ('index.html');
    exit();
}

class slingfile_com extends DownloadClass {

    public function Download($link) {
        if ($_REQUEST['step'] == '1') {
            $post['recaptcha_challenge_field'] = $_POST['recaptcha_challenge_field'];
            $post['recaptcha_response_field'] = $_POST['recaptcha_response_field'];
            $post['show_dl_link'] = $_POST['show_dl_link'];
            $link = urldecode($_POST['link']);
            $cookie = urldecode($_POST['cookie']);
            $page = $this->GetPage($link, $cookie, $post, $link);
        } else {
            $page = $this->GetPage($link);
            is_present($page, "The file you have requested has been deleted.");
            $cookie = GetCookies($page);
            $page = $this->GetPage($link, $cookie, array('show_captcha' => 'yes'), $link);
            if (!preg_match('@id="dltimer">(\d+)<\/span>@', $page, $wait)) html_error("Error: Timer not found!");
            $this->CountDown ($wait[1]);
        }
        if (preg_match('@http:\/\/api\.recaptcha\.net\/challenge[?]k=([^"]+)@', $page, $cap) || strpos($page, 'Invalid captcha entered. Please try again.')) {
            echo "<script language='JavaScript'>var RecaptchaOptions={theme:'white', lang:'en'};</script>\n";
            echo "\n<center><form name='dl' action='$PHP_SELF' method='post' ><br />\n";
            echo '<input type="hidden" name="link" value="' . urlencode($link) . '" />' . "\n";
            echo '<input type="hidden" name="cookie" value="' . urlencode($cookie) . '" />' . "\n";
            echo '<input type="hidden" name="show_dl_link" value="' . cut_str($page,  'name="show_dl_link" value="','"') . '" />' . "\n";
            echo '<input type="hidden" name="step" value="1" />' . "\n";
            echo "<script type='text/javascript' src='http://www.google.com/recaptcha/api/challenge?k=$cap[1]'></script>";
            echo "<noscript><iframe src='http://www.google.com/recaptcha/api/noscript?k=$cap[1]' height='300' width='500' frameborder='0'></iframe><br />";
            echo "<textarea name='recaptcha_challenge_field' rows='3' cols='40'></textarea><input type='hidden' name='recaptcha_response_field' value='manual_challenge' /></noscript><br />";
            echo "<input type='submit' name='submit' onclick='javascript:return checkc();' value='Enter Captcha' />\n";
            echo "<script type='text/javascript'>/*<![CDATA[*/\nfunction checkc(){\nvar capt=document.getElementById('recaptcha_response_field');\nif (capt.value == '') { window.alert('You didn\'t enter the image verification code.'); return false; }\nelse { return true; }\n}\n/*]]>*/</script>\n";
            echo "</form></center>\n</body>\n</html>";
            exit();
        }
        if (!preg_match('@http:\/\/sf\d+[-]\d+\.slingfile\.com\/gdl\/[^"]+@', $page, $dl)) html_error("Error: Download link not found, result : $dl[0]");
        $dlink = trim($dl[0]);
        $FileName = basename(parse_url($dlink, PHP_URL_PATH));
        $this->RedirectDownload($dlink, $FileName, $cookie, 0, $link);
        exit();
    }
}

/*
 * slingfile.com free download plugin by Ruud v.Tony 23-Aug-2011
 * fix the countdown numeration(I should have know this when qqqw mention that before, sorry mate :( ) by Ruud v.Tony 30-Dec-2011
 */
?>
