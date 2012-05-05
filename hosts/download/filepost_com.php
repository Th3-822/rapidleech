<?php
if (!defined('RAPIDLEECH')) {
    require_once ("index.html");
    exit();
}

class filepost_com extends DownloadClass {

    public function Download($link) {
        global $premium_acc;

        if (strstr($link, "fp.io/")) {
            $link = str_replace("fp.io/", "filepost.com/files/", $link);
        }
        $this->link = $link;
        if (!$_REQUEST['step']) {
            $this->page = $this->GetPage($this->link);
            if (preg_match('@Location: (http(s)?:\/\/[^\r\n]+)@i', $this->page, $rd)) {
                $this->link = trim($rd[1]);
                $this->page = $this->GetPage($this->link);
            }
            is_present($this->page, "File not found");
            is_present($this->page, "This IP address has been blocked on our service due to some fraudulent activity.");
            $this->Cookies = GetCookiesArr($this->page);
            if ($this->Cookies['SID'] == '') html_error("Error Cookie [SID] not found!");
        }
        if ($_REQUEST ["premium_acc"] == "on" && (($_REQUEST ["premium_user"] && $_REQUEST ["premium_pass"]) || ($premium_acc ["filepost_com"] ["user"] && $premium_acc ["filepost_com"] ["pass"]))) {
            return $this->Login();
        } elseif ($_REQUEST['step'] == 'Recaptcha') {
            return $this->Login(true);
        } elseif ($_REQUEST['step'] == 'Passpre') {
            return $this->Premium(true);
        } else {
            return $this->Free();
        }
    }

    private function Free() {
        switch ($_REQUEST['step']) {
            case 'Captcha':
                $post['code'] = $_POST['code'];
                $post['file_pass'] = '';
                $post['recaptcha_challenge_field'] = $_POST['recaptcha_challenge_field'];
                $post['recaptcha_response_field'] = $_POST['recaptcha_response_field'];
                $post['token'] = $_POST['token'];
                $Url = urldecode($_POST['link']);
                $this->Cookies = urldecode($_POST['cookie']);
                $recap = $_POST['recap'];
                $check = $this->GetPage($Url, $this->Cookies, $post, $this->link);
                break;
            case 'password':
                $post['code'] = $_POST['code'];
                $post['file_pass'] = $_POST['password'];
                $post['token'] = $_POST['token'];
                $Url = urldecode($_POST['link']);
                $this->Cookies = urldecode($_POST['cookie']);
                $check = $this->GetPage($Url, $this->Cookies, $post, $this->link);
                break;
            default:
                is_present($this->page, "Files over 1024MB can be downloaded by premium<br/ >members only. Please upgrade to premium");
                if (!preg_match("@code: '([^']+)',@", $this->page, $code) || !preg_match("@[({]token: '([^']+)',@", $this->page, $token)) html_error('Error: Post Data [FREE] not found!');
                $code = $code[1];
                $token = $token[1];
                $Url = "http://filepost.com/files/get/?SID={$this->Cookies['SID']}&JsHttpRequest=" . jstime() . "-xml";
                $post = array('action' => 'set_download', 'code' => $code, 'token' => $token);
                $check = $this->GetPage($Url, $this->Cookies, $post, $this->link);
                $this->Cookies = GetCookiesArr($check, $this->Cookies);
                if (preg_match('@"js":\{"(\w+)":\{?"([a-z|\_]+)"?:?"?(\d+)"?@i', $check, $match)) $this->CountDown($match[3]);
                if (strpos($this->page, 'var is_pass_exists = true') || strpos($this->page, 'var show_captcha = true')) {
                    $data = $this->DefaultParamArr($Url, $this->Cookies);
                    $data['code'] = $code;
                    $data['token'] = $token;
                    if (strpos($this->page, 'var is_pass_exists = true')) {
                        $data['step'] = 'password';
                        $this->EnterPassword($data);
                        exit();
                    } else {
                        if (!preg_match('@key:			\'([^\']+)@i', $this->page, $cap)) html_error('Error: Captcha Data [FREE] not found!');
                        $data['step'] = 'Captcha';
                        $data['recap'] = $cap[1]; // incase we need to load the captcha image again
                        $this->Show_reCaptcha($cap[1], $data);
                        exit();
                    }
                } else { // no captcha or password required, skip the form process
                    $post = array('code' => $code, 'file_pass' => '', 'token' => $token);
                    $check = $this->GetPage($Url, $this->Cookies, $post, $this->link);
                }
                break;
        }

        // Let's play with the regex
        if (!preg_match('@"js":\{"(\w+)":\{?"([^"]+)"?:?"?([^|\r|\n|"]+)?"\}@i', $check, $match)) html_error("Error: Unknown Post Data [FREE] page response!");
        switch ($match[1]) {
            case 'error':
                echo ("<center><font color='red'><b>$match[2]</b></font></center>");

                $data = $this->DefaultParamArr($Url, $this->Cookies);
                $data['code'] = $_POST['code'];
                $data['token'] = $_POST['token'];
                switch ($match[2]) {
                    case 'Wrong file password':
                        $data['step'] = 'password';
                        $this->EnterPassword($data);
                        break;
                    case 'You entered a wrong CAPTCHA code. Please try again.':
                        $data['step'] = 'Captcha';
                        $data['recap'] = $recap;
                        $this->Show_reCaptcha($recap, $data);
                        break;
                }
                break;
            case 'answer':
                switch ($match[2]) {
                    case 'link':
                        $dlink = str_replace('\\', '', $match[3]);
                        $filename = basename(parse_url($dlink, PHP_URL_PATH));
                        $this->RedirectDownload($dlink, $filename, $this->Cookies, 0, $this->link);
                        break;
                }
                break;
            default:
                html_error("$match[1], $match[2]");
                break;
        }
    }

    private function Login($captcha = false) {
        global $premium_acc;

        $email = ($_REQUEST["premium_user"] ? trim($_REQUEST["premium_user"]) : $premium_acc ["filepost_com"] ["user"]);
        $password = ($_REQUEST["premium_pass"] ? trim($_REQUEST["premium_pass"]) : $premium_acc ["filepost_com"] ["pass"]);
        if (empty($email) || empty($password)) html_error("Login failed, username or password is empty!");

        $post = array();
        $post['email'] = $email;
        $post['password'] = $password;
        $post['remember'] = 'on';
        if ($captcha == true) {
            $post['recaptcha_challenge_field'] = $_POST['recaptcha_challenge_field'];
            $post['recaptcha_response_field'] = $_POST['recaptcha_response_field'];
            $posturl = urldecode($_POST['link']);
            $this->Cookies = StrToCookies(urldecode($_POST['cookie']));
            $check = $this->GetPage($posturl, $this->Cookies, $post, "http://filepost.com/");
        } else {
            $posturl = "http://filepost.com/general/login_form/?SID={$this->Cookies['SID']}&JsHttpRequest=" . jstime() . "-xml";
            $check = $this->GetPage($posturl, $this->Cookies, $post, "http://filepost.com/");
        }
        if (!preg_match('@"js":\{"(\w+)":\{?"([^"]+)"?:?(\w+)?\}?,?"?(\w+)?"?:?"?([^"]+)?"?\}@i', $check, $match)) html_error("Error: Unknown Login Page Response, Plugin need to be updated!");
        if ($match[1] == 'answer' && $match[4] !== 'error') {
            switch ($match[2]) {
                case 'captcha':
                    if (!preg_match('@key:			\'([^\']+)@i', $this->page, $cap)) html_error('Error: Captcha Data [Premium] not found!');

                    $data = $this->DefaultParamArr($posturl, $this->Cookies);
                    $data['step'] = 'Recaptcha';
                    $this->Show_reCaptcha($cap[1], $data);
                    break;
                case 'success':
                    //check account, we need to convert to array since we have pass the captcha, I also made mistake, should be array_merge, not array_replace, stupid...
                    $this->Cookies = GetCookiesArr($check, $this->Cookies);
                    $check = $this->GetPage("http://filepost.com/partners/", $this->Cookies, 0, 'http://filepost.com/');
                    is_present($check, "Account type: <span>Free</span>");
                    break;
            }
        }
        is_present($match[1], 'error', str_replace('\\', '', $match[2]));
        is_present($match[4], 'error', $match[5]);

        return $this->Premium();
    }

    private function Premium($password = false) {
        if ($password == true) {
            $post['code'] = $_POST['code'];
            $post['file_pass'] = $_POST['password'];
            $post['token'] = $_POST['token'];
            $Url = urldecode($_POST['link']);
            $this->Cookies = decrypt(urldecode($_POST['cookie']));
            $this->page = $this->GetPage($Url, $this->Cookies, $post, $this->link);
            if (!preg_match('@"js":\{"(\w+)":\{?"([^"]+)"?:?"?([^|\r|\n|"]+)?"\}@i', $this->page, $match)) html_error("Error: Unknown Password Link [PREMIUM] page response, plugin need to be updated!");
            switch ($match[1]) {
                case 'error':
                    echo ("<center><font color='red'><b>$match[2]</b></font></center>");

                    $data = $this->DefaultParamArr($Url, encrypt($this->Cookies), $this->link);
                    $data['step'] = 'Passpre';
                    $data['code'] = $_POST['code'];
                    $data['token'] = $_POST['token'];
                    $this->EnterPassword($data);
                    break;
                case 'answer':
                    switch ($match[2]) {
                        case 'link':
                            $dlink = str_replace('\\', '', $match[3]);
                            $filename = basename(parse_url($dlink, PHP_URL_PATH));
                            $this->RedirectDownload($dlink, $filename, $this->Cookies);
                            break;
                    }
                    break;
            }
        } else {
            $this->page = $this->GetPage($this->link, $this->Cookies, 0, $this->link);
			is_present($this->page, "You can only download 50GB a day");
            if (strpos($this->page, 'var is_pass_exists = true')) {
                if (!preg_match("@code: '([^']+)',@", $this->page, $code) || !preg_match("@[({]token: '([^']+)',@", $this->page, $token)) html_error('Error: Post Password Data [Premium] not found!');
                $Url = "http://filepost.com/files/get/?SID={$this->Cookies['SID']}&JsHttpRequest=" . jstime() . "-xml";

                $data = $this->DefaultParamArr($Url, encrypt(CookiesToStr($this->Cookies)), $this->link);
                $data['code'] = $code[1];
                $data['token'] = $token[1];
                $data['step'] = 'Passpre';
                $this->EnterPassword($data);
                exit();
            }
            if (!preg_match('@http(s)?:\/\/fs\d+\.filepost\.com\/get_file\/[^|\r|\n|\']+@i', $this->page, $dl)) html_error("Error: Download Link [PREMIUM] non password not found!");
            $dlink = trim($dl[0]);
            $filename = basename(parse_url($dlink, PHP_URL_PATH));
            $this->RedirectDownload($dlink, $filename, $this->Cookies);
        }
    }

    private function Show_reCaptcha($pid, $inputs) {
        global $PHP_SELF;
        if (!is_array($inputs)) {
            html_error("Error parsing captcha data.");
        }
        // Themes: 'red', 'white', 'blackglass', 'clean'
        echo "<script language='JavaScript'>var RecaptchaOptions={theme:'white', lang:'en'};</script>\n";
        echo "\n<center><form name='dl' action='$PHP_SELF' method='post' ><br />\n";
        foreach ($inputs as $name => $input) {
            echo "<input type='hidden' name='$name' id='$name' value='$input' />\n";
        }
        echo "<script type='text/javascript' src='http://www.google.com/recaptcha/api/challenge?k=$pid'></script>";
        echo "<noscript><iframe src='http://www.google.com/recaptcha/api/noscript?k=$pid' height='300' width='500' frameborder='0'></iframe><br />";
        echo "<textarea name='recaptcha_challenge_field' rows='3' cols='40'></textarea><input type='hidden' name='recaptcha_response_field' value='manual_challenge' /></noscript><br />";
        echo "<input type='submit' name='submit' onclick='javascript:return checkc();' value='Enter Captcha' />\n";
        echo "<script type='text/javascript'>/*<![CDATA[*/\nfunction checkc(){\nvar capt=document.getElementById('recaptcha_response_field');\nif (capt.value == '') { window.alert('You didn\'t enter the image verification code.'); return false; }\nelse { return true; }\n}\n/*]]>*/</script>\n";
        echo "</form></center>\n</body>\n</html>";
        exit;
    }

    private function EnterPassword($inputs) {
        global $PHP_SELF;
        if (!is_array($inputs)) {
            html_error("Error parsing password data.");
        }
        echo "\n" . '<center><form action="' . $PHP_SELF . '" method="post" >' . "\n";
        foreach ($inputs as $name => $input) {
            echo "<input type='hidden' name='$name' id='$name' value='$input' />\n";
        }
        echo '<h4>Enter password here: <input type="text" name="password" id="filepass" size="13" />&nbsp;&nbsp;<input type="submit" onclick="return check()" value="Submit" /></h4>' . "\n";
        echo "<script type='text/javascript'>\nfunction check() {\nvar pass=document.getElementById('filepass');\nif (pass.value == '') { window.alert('You didn\'t enter the password'); return false; }\nelse { return true; }\n}\n</script>\n";
        echo "\n</form></center>\n</body>\n</html>";
        exit();
    }

}

/*
 * Filepost.com free download plugin by Ruud v.Tony 29-09-2011
 * Updated to support premium by vdhdevil 12-10-2011
 * Updated the free download code by Ruud v.Tony 02-11-2011 for multiple option error
 * Small regex fix in free download by Ruud v.Tony 10-01-2012
 * Updated for including short link (fp.io) in check link by Ruud v.Tony 13-01-2012
 * Updated to support captcha also password protected files in premium by Ruud v.Tony 21-01-2012
 * Small fix in checking account also free download code by Ruud v.Tony 01-02-2012
 * Implement new function for an example by Ruud v.Tony 04-02-2012
 */
?>
