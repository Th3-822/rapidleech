<?php
if (!defined('RAPIDLEECH')) {
    require_once('index.html');
    exit();
}

class file4sharing_com extends DownloadClass {

    public function Download($link) {
        global $premium_acc;
        
        if (!$_REQUEST['step']) {
            $this->page = $this->GetPage($link, "lang=english");
            is_present($this->page, "The file you were looking for could not be found, sorry for any inconvenience");
            $this->cookie = "lang=english";
        }
        $this->link = $link;
        if ($_REQUEST['premium_acc'] == 'on' && (($_REQUEST['premium_user'] && $_REQUEST['premium_pass'])||(!empty($premium_acc['file4sharing_com']['user']) && !empty($premium_acc['file4sharing_com']['pass'])))) {
            return $this->Premium();
        } elseif ($_REQUEST['step'] == 'password') {
            return $this->Premium(1);
        } else {
            return $this->Free();
        }
    }
    
    private function Free() {
        global $PHP_SELF;
        
        if ($_REQUEST['step'] == '1') {
            $post['op'] = $_POST['op'];
            $post['id'] = $_POST['id'];
            $post['rand'] = $_POST['rand'];
            $post['referer'] = $_POST['referer'];
            $post['method_free'] = $_POST['method_free'];
            $post['method_premium'] = '';
            if (!empty($_POST['password'])) {
                $post['password'] = $_POST['password'];
            }
            $post['recaptcha_challenge_field'] = $_POST['challenge'];
            $post['recaptcha_response_field'] = $_POST['captcha'];
            $post['down_direct'] = $_POST['down_direct'];
            $this->link = urldecode($_POST['link']);
            $this->cookie = urldecode($_POST['cookie']);
            $page = $this->GetPage($this->link, $this->cookie, $post, $this->link);
        } else {
            $form = cut_str($this->page, "<Form method=\"POST\" action=''>", '</Form>');
            if (!preg_match_all('%<input type="hidden" name="([^"]+)" value="([^"]+)?">%', $form, $one) || !preg_match_all('%<input type="submit" name="(\w+_free)" value="([^"]+)?" class="regular">%', $form, $two)) html_error('Error [Post Data 1 FREE not found!]');
            $match = array_merge(array_combine($one[1], $one[2]), array_combine($two[1], $two[2]));
            $post = array();
            foreach ($match as $key => $value) {
                $post[$key] = $value;
            }
            $page = $this->GetPage($this->link, $this->cookie, $post, $this->link);
        }
        if (strpos($page, 'Type the two words:') || strpos($page, 'Wrong captcha')) {
            $form = cut_str($page, '<Form name="F1" method="POST"', '</Form>');
            if (preg_match('/(\d+)<\/span>/', $form, $w)) $this->CountDown ($w[1]);
            if (!preg_match_all('%<input type="hidden" name="([^"]+)" value="([^"]+)?">%', $form, $ck)) html_error('Error [Post Data 2 FREE not found!]');
            if (!preg_match('@\/api\/challenge\?k=([^"]+)@', $form, $c)) html_error('Error [Captcha Data not found!]');
            //Download the captcha image
            $ch = cut_str($this->GetPage('http://www.google.com/recaptcha/api/challenge?k='.$c[1], $this->cookie), "challenge : '", "'");
            $cap = $this->GetPage("http://www.google.com/recaptcha/api/image?c=" . $ch);
            $capt_img = substr($cap, strpos($cap, "\r\n\r\n") + 4);
            $imgfile = DOWNLOAD_DIR . "file4share_captcha.png";

            if (file_exists($imgfile)) unlink($imgfile);
            if (empty($capt_img) || !write_file($imgfile, $capt_img)) html_error("Error getting CAPTCHA image.", 0);
            // Captcha image downloaded
            $data = array_merge($this->DefaultParamArr($this->link, $this->cookie), array_combine($ck[1], $ck[2]));
            $data['challenge'] = $ch;
            $data['step'] = '1';
            // Build the form for submitting captcha or password file...
            echo "\n<center><form name='dl' action='$PHP_SELF' method='post' ><br />\n";
            foreach ($data as $name => $value) {
                echo '<input type="hidden" name="' . $name . '" value="' . $value . '" />' . "\n";
            }
            if (strpos($form, 'Password:') || strpos($form, 'Wrong Password')) {
                echo '<h4>Enter password here: <input type="text" name="password" size="10" /></h4><br />';
            }
            echo '<h4>' . lang(301) . ' <img src="' . $imgfile . '" /> ' . lang(302) . ': <input type="text" name="captcha" id="capcus" size="20" />&nbsp;&nbsp;<br />';
            echo "<input type='submit' name='submit' onclick='javascript:return checkc();' value='Download File' />\n";
            echo "<script type='text/javascript'>/*<![CDATA[*/\nfunction checkc(){\nvar capt=document.getElementById('capcus');\nif (capt.value == '') { window.alert('You didn\'t enter the image verification code.'); return false; }\nelse { return true; }\n}\n/*]]>*/</script>\n";
            echo "</form></center>\n</body>\n</html>";
            exit();
        }
        is_present($page, cut_str($page, "<b class='err'>", "</b>"));
        if (!preg_match('@http:\/\/[\w.]+(:\d+)?\/d\/[^"]+@i', $page, $dl)) html_error("Error [Download link FREE not found!]");
        $dlink = trim($dl[0]);
        $FileName = basename(parse_url($dlink, PHP_URL_PATH));
        $this->RedirectDownload($dlink, $FileName, $this->cookie, 0, $this->link);
        exit();
    }
    
    private function Premium($password = 0) {
        if ($password == 1) {
            $post['op'] = $_POST['op'];
            $post['id'] = $_POST['id'];
            $post['rand'] = $_POST['rand'];
            $post['referer'] = $_POST['referer'];
            $post['method_free'] = '';
            $post['method_premium'] = $_POST['method_premium'];
            $post['password'] = $_POST['password'];
            $post['down_direct'] = $_POST['down_direct'];
            $this->link = urldecode($_POST['link']);
            $cookie = decrypt(urldecode($_POST['cookie']));
            $page = $this->GetPage($this->link, $cookie, $post, $this->link);
        } else {
            $cookie = $this->login();
            $page = $this->GetPage($this->link, $cookie);
        }
        if (!preg_match('@(http:\/\/[\w.]+(:\d+)?\/d\/[^|\r|\n|"]+)@', $page, $dl)) {
            $form = cut_str($page, '<Form name="F1" method="POST"', '</Form>');
            if (!preg_match_all('%<input type="hidden" name="([^"]+)" value="([^"]+)?">%', $form, $ck)) html_error('Error [Post Data PREMIUM not found!]');
            $match = array_combine($ck[1], $ck[2]);
            if (strpos($form, 'Password:')) {
                if (strpos($form, cut_str($form, '<div class="err">', '</div>'))) echo ("<center><font color='red'><b>Wrong Password, Please Retry!</b></font></center>");
                $data = array_merge($this->DefaultParamArr($this->link, encrypt($cookie)), $match);
                $data['step'] = 'password';
                // build form for submitting password file
                echo "\n" . '<center><form action="' . $PHP_SELF . '" method="post" >' . "\n";
                foreach ($data as $name => $input) {
                    echo "<input type='hidden' name='$name' id='$name' value='$input' />\n";
                }
                echo '<h4>Enter password here: <input type="text" name="password" id="filepass" size="13" />&nbsp;&nbsp;<input type="submit" onclick="return check()" value="Submit" /></h4>' . "\n";
                echo "<script type='text/javascript'>\nfunction check() {\nvar pass=document.getElementById('filepass');\nif (pass.value == '') { window.alert('You didn\'t enter the password'); return false; }\nelse { return true; }\n}\n</script>\n";
                echo "\n</form></center>\n</body>\n</html>";
                exit();
            }
            $post = array();
            foreach ($match as $key => $value) {
                $post[$key] = $value;
            }
            $page = $this->GetPage($this->link, $cookie, $post, $this->link);
            if (!preg_match('@(http:\/\/[\w.]+(:\d+)?\/d\/[^"]+)@', $page, $dl)) html_error("Error[Download Link PREMIUM not found!]");
        }
        $dlink = trim($dl[1]);
        $filename = basename(parse_url($dlink, PHP_URL_PATH));
        $this->RedirectDownload($dlink, $filename, $cookie, 0, $this->link);
    }
    
    private function login() {
        global $premium_acc;
        
        $user = ($_REQUEST["premium_user"] ? trim($_REQUEST["premium_user"]) : $premium_acc ["file4sharing_com"] ["user"]);
        $pass = ($_REQUEST["premium_pass"] ? trim($_REQUEST["premium_pass"]) : $premium_acc ["file4sharing_com"] ["pass"]);
        if (empty($user) || empty($pass)) html_error("Login failed, username[$user] or password[$pass] is empty!");
        
        $posturl = 'http://file4sharing.com/';
        $post['op'] = 'login';
        $post['redirect'] = '';
        $post['checker'] = 'on';
        $post['login'] = $user;
        $post['password'] = $pass;
        $check = $this->GetPage($posturl, $this->cookie, $post, $posturl);
        is_present($check, cut_str($check, '<b class=\'err\'>', '</b>'));
        $cookie = $this->cookie . "; ". GetCookies($check);
        // check account
        $check = $this->GetPage($posturl."?op=my_account", $cookie, 0, $posturl."?op=my_files");
        is_notpresent($check, '<TD>Username:</TD>', 'Invalid account!'); // weird, very weird
        is_notpresent($check, '<TD>Premium account expire:</TD>', 'Account Type : FREE!');
        
        return $cookie;
    }
}

/*
 * file4sharing.com free download plugin by Ruud v.Tony 06/01/2012
 * updated to support premium & password file by Ruud v.Tony 13/02/2012
 */
?>
