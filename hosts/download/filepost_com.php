<?php
if (!defined('RAPIDLEECH')) {
    require_once ("index.html");
    exit();
}

class filepost_com extends DownloadClass {

    public function Download($link) {
        global $premium_acc;
        $this->link = $link;
        if (!$_REQUEST['step']) {
            $this->tid = round(microtime(true) * 1000);
            $Url = "http://filepost.com/files/checker/?JsHttpRequest={$this->tid}-xml";
            $check = $this->GetPage($Url, 0, array('urls' => $this->link));
            is_present($check, "File has been deleted");
            $this->page = $this->GetPage($this->link);
            preg_match_all("#Set-Cookie: (\w+)=([^;]+)#", $this->page, $tmp);
            $this->CookiesArr = array_combine($tmp[1], $tmp[2]);
            $this->Cookies = urldecode(http_build_query($this->CookiesArr, "", ";"));
            if (!preg_match("#SID=(\w+)#", $this->Cookies, $sid)) html_error("Error 0x01: Plugin is out of date");
            $this->sid = $sid[1];
        }
        if (($_REQUEST ["premium_acc"] == "on" && $_REQUEST ["premium_user"] && $_REQUEST ["premium_pass"]) || ($_REQUEST ["premium_acc"] == "on" && $premium_acc ["filepost_com"] ["user"] && $premium_acc ["filepost_com"] ["pass"])) {
            return $this->DownloadPremium();
        } else {
            return $this->Free();
        }
    }

    private function DownloadPremium() {
        global $premium_acc;
        $post = array();
        $post['email'] = $_GET ["premium_user"] ? $_GET ["premium_user"] : $premium_acc['filepost_com']['user'];
        $post['password'] = $_GET ["premium_user"] ? $_GET ["premium_user"] : $premium_acc['filepost_com']['pass'];
        $post['recaptcha_response_field'] = "";
        $page = $this->GetPage("http://filepost.com/general/login_form/?SID={$this->sid}&JsHttpRequest={$this->tid}-xml", $this->Cookies, $post, $this->link);
        if (!strpos($page, '"answer":{"success":true')) {
            html_error("Login Failed");
        }
        preg_match_all("#Set-Cookie: (\w+)=([^;]+)#", $page, $tmp);
        $CookiesArr = array_merge($this->CookiesArr, array_combine($tmp[1], $tmp[2]));
        $Cookies = urldecode(http_build_query($CookiesArr, "", ";"));
        $page = $this->GetPage($this->link, $Cookies, 0, $this->link);
        if (!preg_match('#http://filepost.com/files/\w+/([^/"]+)#', $page, $tmp)) {
            html_error("Error 1x02: Plugin is out of date");
        }
        $FileName = $tmp[1] ? $tmp[1] : "FilePost";
        $dlink = cut_str($page, "download_file('", "'");
        $this->RedirectDownload($dlink, $FileName, $Cookies, 0, $this->link, $FileName);
    }

    private function Free() {
        //use case instead elseif for multiple condition
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
                if (!preg_match("@\{action: '([\w\-]+)', code: '(\w+)'\}@", $this->page, $tmp)) html_error('Error 0x02: Plugin is out of date!');
                $action = $tmp[1];
                $code = $tmp[2];
                if (!preg_match("@\('token', '(\w+)'\)@", $this->page, $tmp)) html_error('Error 0x03: Plugin is out of date!');
                $token = $tmp[1];
                $Url = "http://filepost.com/files/get/?SID={$this->sid}&JsHttpRequest={$this->tid}-xml";
                $post = array('action' => $action, 'code' => $code, 'token' => $token);
                $check = $this->GetPage($Url, $this->Cookies, $post, $this->link);
                if (!preg_match('@"js":\{"(\w+)":\{?"([a-z|\_]+)"?:?"?(\d+)"?@i', $check, $match)) html_error('Error : timer not found!');
                $this->CountDown($match[3]);
                if (strpos($this->page, 'var is_pass_exists = true') || strpos($this->page, 'var show_captcha = true')) {
                    $data = $this->DefaultParamArr($Url, $this->Cookies);
                    $data['code'] = $code;
                    $data['token'] = $token;
                    if (strpos($this->page, 'var is_pass_exists = true')) {
                        $data['step'] = 'password';
                        $this->EnterPassword($data);
                    } else {
                        if (!preg_match('@key:			\'([^\']+)@i', $this->page, $cap)) html_error('Error 0x04: Plugin is out of date!');
                        $data['step'] = 'Captcha';
                        $data['recap'] = $cap[1]; // incase we need to load the captcha image again
                        $this->Show_reCaptcha($cap[1], $data);
                    }
                } else { // no captcha or password required, skip the form process
                    $post = array('code' => $code, 'file_pass' => '', 'token' => $token);
                    $check = $this->GetPage($Url, $this->Cookies, $post, $this->link);
                }
                break;
        }

        // Let's play with the regex
        if (preg_match('@"js":\{"(\w+)":\{?"(([^"]+)"?:?"?([^|\r|\n|\"]+)?)"\}@i', $check, $match)) {
            switch ($match[1]) {
                case 'answer':
                    switch ($match[3]) {
                        case 'link':
                            $dlink = str_replace('\/', '/', $match[4]);
                            $FileName = basename($dlink, PHP_URL_PATH);
                            $this->RedirectDownload($dlink, $FileName, $this->Cookies, 0, $this->link);
                            break;
                    }
                    break;
                case 'error':
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
                default:
                    html_error("$match[1], $match[2]");
                    break;
            }
        } else {
            echo "<pre>"; var_dump(nl2br(htmlentities($check))); echo "</pre>"; exit;
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
 */
?>
