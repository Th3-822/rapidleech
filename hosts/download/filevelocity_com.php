<?php
if (!defined('RAPIDLEECH')) {
    require_once ('index.html');
    exit();
}

class filevelocity_com extends DownloadClass {
    
    public function Download($link) {
        if ($_REQUEST['down_script'] == '1') {
            $post['op'] = $_POST['op'];
            $post['id'] = $_POST['id'];
            $post['rand'] = $_POST['rand'];
            $post['referer'] = $_POST['referer'];
            $post['method_free'] = $_POST['method_free'];
            $post['method_premium'] = '';
            $post['recaptcha_challenge_field'] = $_POST['recaptcha_challenge_field'];
            $post['recaptcha_response_field'] = $_POST['recaptcha_response_field'];
            $link = urldecode($_POST['link']);
            $page = $this->GetPage($link, "lang=english", $post, $link);
        } else {
            $page = $this->GetPage($link, "lang=english");
            is_present($page, 'The file you were looking for could not be found, sorry for any inconvenience.');
            $form = cut_str($page, '<Form method="POST" action=\'\'>', '</Form>');
            if (!preg_match_all('@<input type="hidden" name="([^"]+)" value="([^"]+)?">@', $form, $ck1) || !preg_match_all('@<input type="submit" name="(\w+_free)" value="([^"]+)">@', $form, $ck2)) html_error("Error[PostData1-FREE] not found!");
            $match = array_merge(array_combine($ck1[1], $ck1[2]), array_combine($ck2[1], $ck2[2]));
            $post = array();
            foreach ($match as $key => $value) {
                $post[$key] = $value;
            }
            $page = $this->GetPage($link, "lang=english", $post, $link);
        }
        if (strpos($page, 'Type the two words:') || strpos($page, 'Wrong captcha')) {
            $form = cut_str($page, '<Form name="F1" method="POST"', '</Form>');
            if (preg_match('@(\d+)<\/span> seconds@', $form, $wait)) $this->CountDown ($wait[1]);
            if (!preg_match('@api\/challenge\?k=([^"]+)"@', $form, $c)) html_error("Error[CAPTCHA Data] not found!");
            if (!preg_match_all('@<input type="hidden" name="([^"]+)" value="([^"]+)?">@', $form, $ck)) html_error ("Error[PostData2-FREE] not found!");
            $data = array_merge($this->DefaultParamArr($link), array_combine($ck[1], $ck[2]));
            
            echo "<script language='JavaScript'>var RecaptchaOptions={theme:'white', lang:'en'};</script>\n";
            echo "\n<center><form name='dl' action='$PHP_SELF' method='post' ><br />\n";
            foreach ($data as $name => $input) {
                echo "<input type='hidden' name='$name' id='$name' value='$input' />\n";
            }
            echo "<script type='text/javascript' src='http://www.google.com/recaptcha/api/challenge?k=$c[1]'></script>";
            echo "<noscript><iframe src='http://www.google.com/recaptcha/api/noscript?k=$c[1]' height='300' width='500' frameborder='0'></iframe><br />";
            echo "<textarea name='recaptcha_challenge_field' rows='3' cols='40'></textarea><input type='hidden' name='recaptcha_response_field' value='manual_challenge' /></noscript><br />";
            echo "<input type='submit' name='submit' onclick='javascript:return checkc();' value='Enter Captcha' />\n";
            echo "<script type='text/javascript'>/*<![CDATA[*/\nfunction checkc(){\nvar capt=document.getElementById('recaptcha_response_field');\nif (capt.value == '') { window.alert('You didn\'t enter the image verification code.'); return false; }\nelse { return true; }\n}\n/*]]>*/</script>\n";
            echo "</form></center>\n</body>\n</html>";
            exit();
        }
        is_present($page, cut_str($page, '<div class="err">', '<br>'));
        if (!preg_match('@Location: (http(s)?:\/\/[^\r\n]+)@i', $page, $dl)) html_error("Error[DownloadLink-FREE] not found!");
        $dlink = trim($dl[1]);
        $filename = basename(parse_url($dlink, PHP_URL_PATH));
        $this->RedirectDownload($dlink, $filename, 0, 0, $link);
        exit();
    }
}

/*
 * by Ruud v.Tony 06-02-2012
 */
?>
