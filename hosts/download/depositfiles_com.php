<?php
if (!defined('RAPIDLEECH')) {
    require_once ("index.html");
    exit ();
}

class depositfiles_com extends DownloadClass {

    public function Download($link) {
        global $premium_acc, $Referer;
        if (preg_match('@http:\/\/depositfiles\.com\/folders\/[^|\r|\n]+@i', $link, $dir)) {
            if (!$dir[0]) html_error('Invalid depositfiles folder link!');
            $page = $this->GetPage($link, "lang_current=en");
            preg_match_all('/<a class="hrefs" href="(.*)">/i', $page, $check);
            if (!$check[1]) html_error('Can\'t find any depositfiles single link!');
            $this->moveToAutoDownloader($check[1]);
        }
        if ($_POST['step'] == 'password') {
            $post['file_password'] = $_POST['file_password'];
            $link = urldecode($_POST['link']);
            if ($_POST['pass'] == 'premium') {
                $cookie = decrypt(urldecode($_POST['cookie']));
                return $this->DownloadPremium($link, $cookie, $this->GetPage($link, $cookie, $post, $Referer));
            } else {
                $cookie = urldecode($_POST['cookie']);
                return $this->DownloadFree($link, $cookie, $this->GetPage($link, $cookie, $post, $Referer));
            }
        } elseif (($_REQUEST["cookieuse"] == "on" && preg_match("/autologin\s?=\s?(\w{32})/i", $_REQUEST["cookie"], $c)) || ($_REQUEST["premium_acc"] == "on" && $premium_acc["depositfiles_com"]["cookie"])) {
            $cookie = (empty($c[1]) ? $premium_acc["depositfiles_com"]["cookie"] : $c[1]);
            return $this->Login($link, $cap, $cookie);
        } elseif (($_REQUEST ["premium_acc"] == "on" && $_REQUEST ["premium_user"] && $_REQUEST ["premium_pass"]) || ($_REQUEST ["premium_acc"] == "on" && $premium_acc ["depositfiles_com"] ["user"] && $premium_acc ["depositfiles_com"] ["pass"])) {
            return $this->Login($link, $cap);
        } elseif ($_POST['step'] == 'CaptchaPre') {
            return $this->Login($link, 1);
        } elseif ($_POST['step'] == 'CaptchaFree') {
            return $this->DownloadFree($link);
        } else {
            $page = $this->GetPage($link, "lang_current=en");
            $cookie = GetCookies($page) . "; lang_current=en";
            return $this->Retrieve($link, $cookie, $this->GetPage($link, $cookie, array('gateway_result' => '1'), $Referer));
        }
    }

    private function Retrieve($link, $cookie, $page) {
        global $Referer;

        is_present($page, "Such file does not exist or it has been removed for infringement of copyrights.");
        is_present($page, "all downloading slots for your country are busy", "All download slots for your country are busy!");
        if (preg_match('/Your IP ([\d.]+) is already downloading a file from our system/', $page, $msg)) html_error($msg[0]);
        if (preg_match('%html_download_api-limit_interval">(\d+)<\/span>%', $page, $limit)) html_error("Download limit exceeded. Try again in " . round($limit[1] / 60) . " minutes");
        if (stristr($page, 'Please, enter the password for this file')) {
            $data = array_merge($this->DefaultParamArr($link, $cookie), array('step' => 'password'));
            $this->EnterPassword($data);
            exit();
        }
        if (!preg_match('%<span id="download_waiter_remain">(\d+)<\/span>%', $page, $wait)) html_error('Error 0x01:Plugin is out of date');
        $this->CountDown($wait[1]);
        $cookie = $cookie . '; ' . GetCookies($page);
        if (!preg_match("@var fid = '([^|']+)@i", $page, $fid)) html_error('Error 0x02:Plugin is out of date');
        if (!preg_match("@Recaptcha\.create\('([^|']+)@i", $page, $cid)) html_error('Error 0x03:Plugin is out of date');
        if (!preg_match("@\/get_file\.php[^|']+@i", $page, $temp)) html_error('Error 0x04:Plugin is out of date');

        $data = $this->DefaultParamArr("http://depositfiles.com$temp[0]", $cookie);
        $data['step'] = 'CaptchaFree';
        $data['fid'] = $fid[1];
        $data['check'] = $cid[1];
        $this->Show_reCaptcha($cid[1], $data);
        exit();
    }

    private function DownloadFree($link) {
        global $Referer;

        $fid = $_POST['fid'];
        $check = $_POST['check'];
        $challenge = $_POST['recaptcha_challenge_field'];
        $response = $_POST['recaptcha_response_field'];
        $link = urldecode($_POST['link']);
        $cookie = urldecode($_POST['cookie']);
        $page = $this->GetPage($link . "$fid&challenge=$challenge&response=$response", $cookie, 0, $Referer);
        if (preg_match("@check_recaptcha\('$fid'@i", $page)) {
            $data = $this->DefaultParamArr($link, $cookie);
            $data['step'] = 'CaptchaFree';
            $data['fid'] = $fid;
            $this->Show_reCaptcha($check, $data);
            exit();
        }
        if (!preg_match('%<form action="(.*)" method="get"%U', $page, $dl)) html_error("Error 0x05:Plugin is out of date");
        $dlink = trim($dl[1]);
        $FileName = basename(parse_url($dlink, PHP_URL_PATH));
        $this->RedirectDownload($dlink, $FileName, $cookie, 0, $Referer);
        exit();
    }

    private function Login($link, $cap = 0, $autolog = false) {
        global $premium_acc;
        
        $posturl = 'http://depositfiles.com/';
        if (!$autolog) {
            $user = ($_REQUEST["premium_user"] ? $_REQUEST["premium_user"] : $premium_acc["depositfiles_com"]["user"]);
            $pass = ($_REQUEST["premium_pass"] ? $_REQUEST["premium_pass"] : $premium_acc["depositfiles_com"]["pass"]);
            if (empty($user) || empty($pass)) html_error("Login Failed: Username[$user] or Password[$pass] is empty. Please check login data.");

            $post['go'] = "1";
            $post['login'] = $user;
            $post['password'] = $pass;
            if ($cap == 1) {
                $post['recaptcha_challenge_field'] = $_POST['recaptcha_challenge_field'];
                $post['recaptcha_response_field'] = $_POST['recaptcha_response_field'];
                $loginurl = urldecode($_POST['link']);
            } else {
                $loginurl = $posturl."login.php?return=/";
            }
            $page = $this->GetPage($loginurl, "lang_current=en", $post, $posturl);
            if (strpos($page, 'Enter security code')) {
                if (!preg_match('@api\/challenge[?]k=([^"]+)@i', $page, $cap) && !preg_match('@api\/noscript[?]k=([^"]+)@i', $page, $cap)) html_error('Error [Captcha Data Login not found!]');
                $data = $this->DefaultParamArr($loginurl);
                $data['step'] = 'CaptchaPre';
                $this->Show_reCaptcha($cap[1], $data);
                exit();
            }
            $cookie = GetCookies($page) . '; lang_current=en';
            // WANNA GET YOUR PREMIUM COOKIE? UNCOMMENT THE CODE BELOW, COPY THE COOKIE VALUE IN THE TEXTAREA!
            // $autolog = cut_str($cookie, 'autologin=', ';');
            // textarea($autolog, 0, 0, true);
            is_notpresent($cookie, "autologin", "Login Failed , Bad username/password combination");
        } elseif (strlen($autolog) == 32) {
            $cookie = "autologin=$autolog; lang_current=en";
        } else {
            html_error("[Cookie] Invalid cookie (" . strlen($autolog) . " != 32).");
        }

        //IMPORTANT, WE NEED TO CHECK THIS FIRST!
        $page = $this->GetPage($posturl.'gold/', $cookie, 0, $posturl.'gold/payment.php');
        is_present($page, 'FREE - member', 'Account free, login not validated!');
        is_notpresent($page, '<div class="goldmembership">', 'Login failed, account is not valid?');

        return $this->DownloadPremium($link, $cookie, $this->GetPage($link, $cookie));
    }

    private function DownloadPremium($link, $cookie, $page) {

        is_present($page, "You have exceeded the 20 GB 24-hour limit");
        is_present($page, "Such file does not exist or it has been removed for infringement of copyrights.");
        if (stristr($page, 'Please, enter the password for this file')) {
            $data = array_merge($this->DefaultParamArr($link, encrypt($cookie)), array('step' => 'password', 'pass' => 'premium'));
            $this->EnterPassword($data);
            exit();
        }
        if (!preg_match('@http:\/\/.+depositfiles\.com\/auth-[^|\r|\n|\'"]+@i', $page, $dl)) html_error("Error 0x06:Plugin is out of date");
        $dlink = trim($dl[0]);
        $FileName = basename(parse_url($dlink, PHP_URL_PATH));
        $this->RedirectDownload($dlink, $FileName, $cookie);
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
        exit();
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

//Written by VinhNhaTrang 12-08-2010
//Updated by vdhdevil & Ruud v.Tony 19-3-2011
//Updated by vdhdevil 30-April-2011: Updated Download Premium
//Updated by Ruud v.Tony 16-May-2011: Updated Download Free
//Updated by Ruud v.Tony 03-Okt-2011: Updated for depositfiles new layout, support captcha in login, password protected file, folder file
//Updated by Ruud v.Tony 05-Okt-2011: Updated in password protected files so we dont need to start over the page :D
//Updated by Ruud v.Tony 26-Okt-2011: Add captcha function in free download
?>