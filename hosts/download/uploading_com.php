<?php
if (!defined('RAPIDLEECH')) {
    require_once ("index.html");
    exit();
}

class uploading_com extends DownloadClass {

    public function Download($link) {
        global $premium_acc;

        if (!$_REQUEST['step']) {
            $this->page = $this->GetPage($link);
            is_present($this->page, "The requested file is not found");
            $this->cookie = GetCookiesArr($this->page);
            if (empty($this->cookie['SID'])) html_error("Error: Cookie [SID] not found!");
        }
        $this->link = $link;
        if (($_REQUEST["cookieuse"] == "on" && preg_match("/remembered_user=([\w.]+);?/i", $_REQUEST["cookie"]) !== false) || ($_REQUEST["premium_acc"] == "on" && !empty($premium_acc["uploading_com"]["cookie"]))) {
            $this->changeMesg(lang(300) . "<br />Uploading.com Premium Download [Cookie]");
            return $this->Login();
        } elseif (($_REQUEST['premium_acc'] == 'on' && (($_REQUEST['premium_user'] && $_REQUEST['premium_pass']) || (!empty($premium_acc['uploading_com']['user']) && !empty($premium_acc['uploading_com']['pass']))))) {
            $this->changeMesg(lang(300) . "<br />Uploading.com Premium Download");
            return $this->Login();
        } elseif ($_REQUEST['step'] == 'captcha') {
            return $this->Login(true);
        } elseif ($_REQUEST['step'] == 'passpre') {
            return $this->Premium(true);
        } else {
            $this->changeMesg(lang(300) . "<br />Uploading.com Free Download");
            return $this->Free();
        }
    }

    private function Free() {

        if ($_REQUEST['step'] == 'passfree') {
            $post['action'] = $_POST['action'];
            $post['file_id'] = $_POST['file_id'];
            $post['code'] = $_POST['code'];
            $post['pass'] = $_POST['password'];
            $Url = urldecode($_POST['link']);
            $this->cookie = StrToCookies(urldecode($_POST['cookie']));
            $check = $this->GetPage($Url, $this->cookie, $post, $this->link);
        } else {
            is_present($this->page, "The file owner set up a limitation<br />that only premium members are<br />able download this file.");
            is_present($this->page, "Sorry, you have reached your daily download limit.<br />Please try again tomorrow or acquire a premium membership.");
            $form = cut_str($this->page, '<div class="page inner_content pageDownloadAvm">', '<div class="fixer"></div>');
            if (!preg_match('%<form action="([^"]+)" method="post" id="downloadform">%', $form, $fl)) html_error("Error [Post Link FREE not found!]");
            $Url = trim($fl[1]);
            if (!preg_match_all('@<input type="hidden" name="([^"]+)" value="([^"]+)" \/>@', $form, $c)) html_error('Error [Post Data 1 FREE not found!]');
            $match = array_combine($c[1], $c[2]);
            if (strpos($this->page, 'Please Enter Password')) {
                $data = array_merge($this->DefaultParamArr($Url, $this->cookie), $match);
                $data['step'] = 'passfree';
                $this->EnterPassword($data);
                exit();
            }
            $post = array();
            foreach ($match as $key => $value) {
                $post[$key] = $value;
            }
            $check = $this->GetPage($Url, $this->cookie, $post, $this->link);
        }
        // Dont use the existing variable cookie name, uploading.com give error message from cookie
        $cookie = GetCookiesArr($check, $this->cookie);
        if (!empty($cookie['error'])) {
            echo ("<center><font color='red'><b>" . urldecode($cookie['error']) . "</b></font></center>");
            // use previous cookie variable as it need to be checked again...
            $data = $this->DefaultParamArr($Url, $this->cookie);
            $data['action'] = $_POST['action'];
            $data['file_id'] = $_POST['file_id'];
            $data['code'] = $_POST['code'];
            $data['step'] = 'passfree';
            $this->EnterPassword($data);
            exit();
        }
        if (!preg_match('%<strong id="timer_count">(\d+)</strong>%', $check, $wait)) html_error("Error [Timer not found!]");
        $this->CountDown($wait[1]);
        if (!preg_match('@action: \'([^\']+)\',@', $check, $act) || !preg_match('@code: "([^"]+)",@', $check, $cd)) html_error("Error [Post Data 2 FREE not found!]");
        if (preg_match('%<input type="hidden" value="([^"]+)" id="pass" \/>%', $check, $pasid)) $pass_id = trim($pasid[1]);
        else $pass_id = 'undefined';
        unset($post);
        $post['action'] = $act[1];
        $post['code'] = $cd[1];
        $post['pass'] = $pass_id;
        $this->page = $this->GetPage("http://uploading.com/files/get/?SID={$cookie['SID']}&JsHttpRequest=" . jstime() . "-xml", $cookie, $post, $Url);
        if (!preg_match('@"js":\{"(\w+)":\{?"([^"]+)"?:?"?([^|\r|\n|"]+)?"\}@i', $this->page, $match)) html_error("Error [Post Page Response (FREE) UNKNOWN!]");
        switch ($match[1]) {
            case 'answer':
                if ($match[2] == 'link') {
                    $dlink = str_replace('\\', '', $match[3]);
                    $filename = basename(parse_url($dlink, PHP_URL_PATH));
                    $this->RedirectDownload($dlink, $filename, $cookie, 0, $Url);
                    exit();
                }
                break;
        }
        is_present($match[1], 'error', str_replace('\\', '', $match[2]));
    }

    private function Login($captcha = false) {
        global $premium_acc;

        if (($_REQUEST["cookieuse"] == "on" && preg_match("/remembered_user=([\w.]+);?/i", $_REQUEST["cookie"], $c)) || ($_REQUEST["premium_acc"] == "on" && !empty($premium_acc["uploading_com"]["cookie"]))) {
            $usecookie = (empty($c[1]) ? !empty($premium_acc["uploading_com"]["cookie"]) : $c[1]);
        } else {
            $usecookie = false;
        }

        $posturl = 'http://uploading.com/';
        if (!$usecookie) {
            $email = ($_REQUEST["premium_user"] ? trim($_REQUEST["premium_user"]) : $premium_acc ["uploading_com"] ["user"]);
            $password = ($_REQUEST["premium_pass"] ? trim($_REQUEST["premium_pass"]) : $premium_acc ["uploading_com"] ["pass"]);
            // This check is important incase there's conflict in post account data, do look in the bracket at error message...
            if (empty($email) || empty($password)) html_error("Login failed, email[$email] or password[$password] is empty!");

            $post['email'] = $_REQUEST["premium_user"] ? trim($_REQUEST["premium_user"]) : $premium_acc ["uploading_com"] ["user"];
            $post['password'] = $_REQUEST["premium_user"] ? trim($_REQUEST["premium_user"]) : $premium_acc ["uploading_com"] ["pass"];
            if ($captcha == true) {
                $post['remember'] = "1";
                $post['recaptcha_challenge_field'] = $_POST['recaptcha_challenge_field'];
                $post['recaptcha_response_field'] = $_POST['recaptcha_response_field'];
                $loginurl = urldecode($_POST['referer']);
                $this->cookie = StrToCookies(decrypt(urldecode($_POST['cookie'])));
            } else {
                $post['remember'] = "on";
                $loginurl = $posturl . "general/login_form/?SID={$this->cookie['SID']}&JsHttpRequest=" . jstime() . "-xml";
            }
            $check = $this->GetPage($loginurl, $this->cookie, $post, $posturl);
            if (!preg_match('@"js":\{"(\w+)":\{?"([^"]+)"?:?(\w+)?\}?@', $check, $match)) html_error("Error [Login Page Response UNKNOWN!]");
            switch ($match[1]) {
                case 'answer':
                    if ($match[2] == 'captcha' && $match[3] == 'true') {
                        // UNCOMMENT THIS CODE BELOW TO CHECK POST DATA HAVE CORRECT
                        // textarea($post, 0, 0, true);
                        if (!preg_match('@\(\'recaptcha_block\', \'([^\']+)\'\);@', $this->page, $c)) html_error("Error[CAPTCHA Data not found!]");
                        $data = $this->DefaultParamArr($this->link, encrypt(CookiesToStr($this->cookie)), $loginurl);
                        $data['step'] = 'captcha';
                        $this->Show_reCaptcha($c[1], $data);
                        exit();
                    }
                    break;

                case 'redirect':
                    $cba = str_replace('\\', '', $match[2]);
                    if ($cba !== 'http://uploading.com/') html_error('This Account Has Been Blocked/Banned/Expired!');
                    $this->cookie = GetCookiesArr($check, $this->cookie);
                    // GET YOUR PREMIUM COOKIE IF YOU WANT TO USE THAT AS LOGIN...DO REMEMBER DO NOT USE COOKIE OR ACCOUNT(USER & PASS) IN THE SAME ACCOUNT.PHP, USE ONLY ONE!
                    // UNCOMMENT THIS LINE BELOW
                    // textarea($this->cookie['remembered_user'], 0, 0, true);
                    break;
            }
            is_present($match[1], "error", str_replace('\\', '', $match[2]));
        } else {
            $this->cookie['remembered_user'] = $usecookie;
        }

        $check = $this->GetPage($posturl . "profile", $this->cookie, 0, $posturl);
        $this->cookie = GetCookiesArr($check, $this->cookie);
        is_present($check, "<dd>Basic (<a href=\"http://uploading.com/premium/\">Upgrade</a>)</dd>", "Account Type: FREE!");
        if (!array_key_exists('cache', $this->cookie)) html_error('Invalid Account!');

        return $this->Premium();
    }

    private function Premium($password = false) {
        if ($password == true) {
            $post['action'] = $_POST['action'];
            $post['code'] = $_POST['code'];
            $post['pass'] = $_POST['password'];
            $Url = urldecode($_POST['link']);
            $this->cookie = decrypt(urldecode($_POST['cookie']));
            $page = $this->GetPage($Url, $this->cookie, $post, $this->link);
        } else {
            $this->page = $this->GetPage($this->link, $this->cookie, 0, $this->link);
            is_present($this->page, 'Your account premium traffic has been limited');
            if (preg_match('@Location: (http(s)?:\/[^\r\n]+)@i', $this->page, $dl)) { //this for direct link file
                $dlink = trim($dl[1]);
                $filename = basename(parse_url($dlink, PHP_URL_PATH));
                $this->RedirectDownload($dlink, $filename, $this->cookie, 0, $this->link);
                exit(); //must use this or not???
            } else {
                if (!preg_match('@action: \'([^\']+)\',@', $this->page, $act) || !preg_match('@code: "([^"]+)",@', $this->page, $cd)) html_error("Error [Post Data - PREMIUM not found!]");
                $Url = "http://uploading.com/files/get/?SID={$this->cookie['SID']}&JsHttpRequest=" . jstime() . "-xml";
                if (strpos($this->page, 'Please Enter Password')) {
                    $data = $this->DefaultParamArr($Url, encrypt(CookiesToStr($this->cookie)));
                    $data['action'] = $act[1];
                    $data['code'] = $cd[1];
                    $data['step'] = 'passpre';
                    $this->EnterPassword($data);
                    exit();
                } else { // no password
                    $post['action'] = $act[1];
                    $post['code'] = $cd[1];
                    $post['pass'] = 'undefined';
                    $page = $this->GetPage($Url, $this->cookie, $post, $this->link);
                }
            }
        }
        if (!preg_match('@"js":\{"(\w+)":\{?"([^"]+)"?:?"?([^|\r|\n|"]+)?"\}@', $page, $match)) html_error("Error [Unknown Post Data (PREMIUM) Page Response!]");
        switch ($match[1]) {
            case 'error':
                echo ("<center><font color='red'><b>$match[2]</b></font></center>");
                $data = $this->DefaultParamArr($Url, encrypt($this->cookie));
                $data['action'] = $_POST['action'];
                $data['code'] = $_POST['code'];
                $data['step'] = 'passpre';
                $this->EnterPassword($data);
                break;
            case 'answer':
                if ($match[2] == 'link') {
                    $dlink = str_replace('\\', '', $match[3]);
                    $filename = basename(parse_url($dlink, PHP_URL_PATH));
                    $this->RedirectDownload($dlink, $filename, $this->cookie, 0, $Url);
                }
                break;
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

    public function CheckBack($header) {
        is_present($header, 'HTTP/1.1 302 Moved', urldecode(cut_str($header, "Set-Cookie: error=", ";")));
    }

}

/*
 * Written by Ruud v.Tony 10-02-2012
 */
?>