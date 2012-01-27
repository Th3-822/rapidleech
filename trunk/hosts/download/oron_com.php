<?php
if (!defined('RAPIDLEECH')) {
    require_once ("index.html");
    exit();
}

class oron_com extends DownloadClass {

    public function Download($link) {
        global $premium_acc;

        $this->link = $link;
        if ($_REQUEST ["premium_acc"] == "on" && (($_REQUEST ["premium_user"] && $_REQUEST ["premium_pass"]) || ($premium_acc ["oron_com"] ["user"] && $premium_acc ["oron_com"] ["pass"]))) {
            return $this->Login();
        } elseif ($_REQUEST['step'] == "Captcha") {
            return $this->Login(true);
        } elseif ($_REQUEST['step'] == "Password") {
            return $this->DownloadPremium(true);
        } else {
            return $this->DownloadFree();
        }
    }

    private function DownloadFree() {
        if ($_REQUEST['down_direct'] == '1') {
            $this->link = urldecode($_POST['link']);

            $post['op'] = $_POST['op'];
            $post['id'] = $_POST['id'];
            $post['rand'] = $_POST['rand'];
            $post['referer'] = $this->link;
            $post['method_free'] = $_POST['method_free'];
            $post['method_premium'] = '';
            if (!empty($_POST['password'])) {
                $post['password'] = $_POST['password'];
            }
            $post['recaptcha_challenge_field'] = $_POST['challenge'];
            $post['recaptcha_response_field'] = $_POST['captcha'];
            $this->page = $this->GetPage($this->link, 0, $post, $this->link);
        } else {
            $this->page = $this->GetPage($this->link);
            is_present($this->page, "403 Forbidden", "Oron banned this server");
            is_present($this->page, "File could not be found due to its possible expiration or removal by the file owner.");
            is_present($this->page, "This file can only be downloaded by Premium Users.");

            $form = cut_str($this->page, '<form method="POST" action=\'\'>', '</form>');
            if (!preg_match_all('<input type="hidden" name="([^"]+)" value="([^"]+)?">', $form, $one) || !preg_match_all('<input type="submit" name="([^"]+)" value="([^"]+)?">', $form, $two)) html_error("Error: Post Data [FREE] 1 not found!");
            $match = array_merge(array_combine($one[1], $one[2]), array_combine($two[1], $two[2]));
            $post = array();
            foreach ($match as $key => $value) {
                $post[$key] = $value;
            }
            $this->page = $this->GetPage($this->link, 0, $post, $this->link);
        }
        if (preg_match('%<p class="err">(.*)<br>%', $this->page, $msg)) html_error($msg[1]);
        if (preg_match('#(\d+)</span> seconds#', $this->page, $wait)) $this->CountDown($wait[1]);
        if (preg_match('@api\/challenge[?]k=([^"]+)@i', $this->page, $cap) && preg_match('@api\/noscript[?]k=([^"]+)@i', $this->page, $cap)) {
            //download the captcha image (AGAIN!)
            $ch = cut_str($this->GetPage("http://www.google.com/recaptcha/api/challenge?k=$cap[1]"), "challenge : '", "'");
            $cap = $this->GetPage("http://www.google.com/recaptcha/api/image?c=" . $ch);
            $capt_img = substr($cap, strpos($cap, "\r\n\r\n") + 4);
            $imgfile = DOWNLOAD_DIR . "oron_captcha.jpg";

            if (file_exists($imgfile)) unlink($imgfile);
            if (empty($capt_img) || !write_file($imgfile, $capt_img)) html_error("Error getting CAPTCHA image.", 0);
            // Captcha img downloaded

            if (!preg_match_all('@<input type="hidden" name="([^"]+)" value="([^"]+)?">@', $this->page, $match)) html_error("Error: Post Data [FREE] 2 not found!");
            $data = array_combine($match[1], $match[2]);
            echo "\n<center><form name='dl' action='$PHP_SELF' method='post' ><br />\n";
            echo '<input type="hidden" name="link" value="' . urlencode($this->link) . '" />' . "\n";
            echo '<input type="hidden" name="challenge" value="' . $ch . '" />' . "\n";
            foreach ($data as $name => $value) {
                echo '<input type="hidden" name="' . $name . '" value="' . $value . '" />' . "\n";
            }
            if (stristr($this->page, "Password: ")) {
                echo '<h4>Enter password here: <input type="text" name="password" size="10" /></h4><br />';
            }
            echo '<h4>' . lang(301) . ' <img src="' . $imgfile . '" /> ' . lang(302) . ': <input type="text" name="captcha" id="capcus" size="20" />&nbsp;&nbsp;<br />';
            echo "<input type='submit' name='submit' onclick='javascript:return checkc();' value='Download File' />\n";
            echo "<script type='text/javascript'>/*<![CDATA[*/\nfunction checkc(){\nvar capt=document.getElementById('capcus');\nif (capt.value == '') { window.alert('You didn\'t enter the image verification code.'); return false; }\nelse { return true; }\n}\n/*]]>*/</script>\n";
            echo "</form></center>\n";
            exit();
        }
        if (!preg_match('%href="([^"]+)" class="atitle">%', $this->page, $dl)) html_error('Error: Download Link [FREE] not found!');
        $dlink = trim($dl[1]);
        $filename = basename(parse_url($dlink, PHP_URL_PATH));
        $this->RedirectDownload($dlink, $filename, 0, 0, $this->link);
        exit();
    }

    private function DownloadPremium($password = false) {

        $post = array();
        $post['op'] = 'download2';
        $post['referer'] = $this->link;
        $post['method_free'] = '';
        $post['method_premium'] = '1';
        $post['down_direct'] = '1';
        if ($password == true) {
            $post['id'] = $_POST['id'];
            $post['rand'] = $_POST['rand'];
            $post['password'] = $_POST['password'];
            $this->link = urldecode($_POST['link']);
            $this->cookie = decrypt(urldecode($_POST['cookie']));
        } else {
            $this->page = $this->GetPage($this->link, $this->cookie, 0, $this->link);
            is_present($this->page, "File could not be found due to its possible expiration or removal by the file owner.");
            is_present($this->page, "You have reached the download limit: 15000 Mb ");

            $id = cut_str($this->page, 'name="id" value="', '"');
            $rand = cut_str($this->page, 'name="rand" value="', '"');

            if (stristr($this->page, 'Password: ')) {
                echo "\n" . '<center><form action="' . $PHP_SELF . '" method="post" >' . "\n";
                echo '<input type="hidden" name="link" value="' . urlencode($this->link) . '" />' . "\n";
                echo '<input type="hidden" name="cookie" value="' . urlencode(encrypt($this->cookie)) . '" />' . "\n";
                echo '<input type="hidden" name="id" value="' . $id . '" />' . "\n";
                echo '<input type="hidden" name="rand" value="' . $rand . '" />' . "\n";
                echo '<input type="hidden" name="step" value="Password" />' . "\n";
                echo '<h4>Enter password here: <input type="text" name="password" id="filepass" size="13" />&nbsp;&nbsp;<input type="submit" onclick="return check()" value="Submit" /></h4>' . "\n";
                echo "<script type='text/javascript'>\nfunction check() {\nvar pass=document.getElementById('filepass');\nif (pass.value == '') { window.alert('You didn\'t enter the password'); return false; }\nelse { return true; }\n}\n</script>\n";
                echo "\n</form></center>\n</body>\n</html>";
                exit();
            } else {
                $post['id'] = $id;
                $post['rand'] = $rand;
            }
        }
        $this->page = $this->GetPage($this->link, $this->cookie, $post, $this->link);
        is_present($this->page, "Retype Password", "Wrong Password! Please reenter!");
        $this->cookie = $this->cookie . "; " . GetCookies($this->page);
        if (!preg_match('#http://(\w+).oron.com[^"]+#', $this->page, $dl)) html_error("Error: Download Link [PREMIUM] not found!");
        $dlink = trim($dl[0]);
        $filename = basename(parse_url($dlink, PHP_URL_PATH));
        $this->RedirectDownload($dlink, $filename, $this->cookie);
    }

    private function Login($captcha = false) {
        global $premium_acc;

        $user = ($_REQUEST["premium_user"] ? trim($_REQUEST["premium_user"]) : $premium_acc ["oron_com"] ["user"]);
        $password = ($_REQUEST["premium_pass"] ? trim($_REQUEST["premium_pass"]) : $premium_acc ["oron_com"] ["pass"]);
        if (empty($user) || empty($password)) html_error("Login failed, username or password is empty!");

        $post['login'] = $user;
        $post['password'] = $password;
        $post['op'] = "login";
        $post['redirect'] = "";
        $post['rand'] = "";
        if ($captcha == true) {
            $post['rand'] = $_POST['rand'];
            $post['recaptcha_challenge_field'] = $_POST['challenge'];
            $post['recaptcha_response_field'] = $_POST['captcha'];
        }
        $this->page = $this->GetPage("http://oron.com/login", 0, $post, "http://oron.com/login");
        is_present($this->page, "Incorrect Login or Password");
        is_present($this->page, "403 Forbidden", "Oron banned this server");
        if (strpos($this->page, "Enter correct captcha")) {
            if (!preg_match('%action="([^"]+)"%', $form, $pl)) html_error("Error: Premium [Captcha] link not found!");
            //download the captcha image
            if (!preg_match('@api\/challenge[?]k=([^"]+)@i', $this->page, $cap) && !preg_match('@api\/noscript[?]k=([^"]+)@i', $this->page, $cap)) html_error("Error: Premium [Captcha] Image not found!");
            $ch = cut_str($this->GetPage("http://www.google.com/recaptcha/api/challenge?k=$cap[1]"), "challenge : '", "'");
            $cap = $this->GetPage("http://www.google.com/recaptcha/api/image?c=" . $ch);
            $capt_img = substr($cap, strpos($cap, "\r\n\r\n") + 4);
            $imgfile = DOWNLOAD_DIR . "oron_captcha.jpg";

            if (file_exists($imgfile)) unlink($imgfile);
            if (empty($capt_img) || !write_file($imgfile, $capt_img)) html_error("Error getting CAPTCHA image.", 0);
            // Captcha img downloaded

            $data = $this->DefaultParamArr($pl[1]);
            $data['rand'] = cut_str($this->page, 'name="rand" value="', '"');
            $data['step'] = 'Captcha';
            $data['challenge'] = $ch;
            $this->EnterCaptcha($imgfile, $data, 20);
            exit();
        }
        $this->cookie = GetCookies($this->page);
        //check account
        $this->page = $this->GetPage("http://oron.com/?op=my_account", $this->cookie, 0, "http://oron.com/login");
        is_present($this->page, "Become a PREMIUM Member", "Account Free, Login not validated!");

        return $this->DownloadPremium();
    }

}

/* * ************************************************\
  WRITTEN BY KAOX 03-oct-09
  UPDATE BY KAOX 06-oct-09 ADD SUPPORT TO CAPTCHA
  UPDATE BY Slider324 17-oct-10 UPDATE SUPPORT TO CAPTCHA
  UPDATE BY vdhdevil  04-Nov-10 UPDATE SUPPORT PREMIUM ACCOUNT
  UPDATE BY vdhdevil  19-March-10 [FIX]Login premium account
  UPDATE BY Ruud v.Tony 29-Sept-2011 [FIX] Free Download code
  UPDATE BY Ruud v.Tony 16-Jan-2012 Add support for password protected files
 * \************************************************* */
?>