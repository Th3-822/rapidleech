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
        } elseif (($_REQUEST["cookieuse"] == "on" && preg_match("/autologin\s?=\s?(\w{32})/i", $_REQUEST["cookie"], $c)) || ($_REQUEST["premium_acc"] == "on" && $premium_acc["depositfiles"]["cookie"])) {
            $cookie = (empty($c[1]) ? $premium_acc["depositfiles"]["cookie"] : $c[1]);
            return $this->Login($link, $cap, $cookie);
        } elseif (($_REQUEST ["premium_acc"] == "on" && $_REQUEST ["premium_user"] && $_REQUEST ["premium_pass"]) || ($_REQUEST ["premium_acc"] == "on" && $premium_acc ["depositfiles"] ["user"] && $premium_acc ["depositfiles"] ["pass"])) {
            return $this->Login($link, $cap);
        } elseif ($_POST['step'] == 'Captcha') {
            return $this->Login($link, 1);
        } else {
            $page = $this->GetPage($link, "lang_current=en");
            $cookie = GetCookies($page) . "; lang_current=en";
            return $this->DownloadFree($link, $cookie, $this->GetPage($link, $cookie, array('gateway_result' => '1'), $Referer));
        }
    }

    private function DownloadFree($link, $cookie, $page) {
        global $Referer;

        is_present($page, "Such file does not exist or it has been removed for infringement of copyrights.");
        is_present($page, "all downloading slots for your country are busy", "All download slots for your country are busy!");
        if (preg_match('/Your IP ([\d.]+) is already downloading a file from our system/', $page, $msg)) html_error($msg[0]);
        if (preg_match('%html_download_api-limit_interval">(\d+)<\/span>%', $page, $limit)) html_error("Download limit exceeded. Try again in " . round($limit[1] / 60) . " minutes");
        if (stristr($page, 'Please, enter the password for this file')) {
            echo "\n" . '<center><form action="' . $PHP_SELF . '" method="post" >' . "\n";
            echo '<input type="hidden" name="link" value="' . urlencode($link) . '" />' . "\n";
            echo '<input type="hidden" name="cookie" value="' . urlencode($cookie) . '" />' . "\n";
            echo '<input type="hidden" name="step" value="password" />' . "\n";
            echo '<h4>Enter password here: <input type="text" name="file_password" id="password" size="13" />&nbsp;&nbsp;<input type="submit" onclick="return check()" value="Continue" /></h4>' . "\n";
            echo "<script type='text/javascript'>\nfunction check() {\nvar pass=document.getElementById('password');\nif (pass.value == '') { window.alert('You didn\'t enter the password'); return false; }\nelse { return true; }\n}\n</script>\n";
            echo "\n</form></center>\n</body>\n</html>";
            exit();
        }
        if (preg_match('%<span id="download_waiter_remain">(\d+)<\/span>%', $page, $wait)) $this->CountDown($wait[1]);
        $cookie = $cookie . '; '.GetCookies($page);
        if (!preg_match("#/get_file.php[^&|']+#", $page, $temp)) html_error("Error 1x03:Plugin is out of date");
        $page = $this->GetPage("http://depositfiles.com$temp[0]", $cookie, 0, $Referer);
        if (!preg_match('%<form action="(.*)" method="get"%U', $page, $dl)) html_error("Error 1x04:Plugin is out of date");
        $dlink = trim($dl[1]);
        $filename = parse_url($dlink);
        $FileName = basename($filename['path']);
        $this->RedirectDownload($dlink, $FileName, $cookie, 0, $Referer);
        exit();
    }

    private function Login($link, $cap, $autolog = false) {
        global $premium_acc, $Referer;

        if (!$autolog) {
            $user = ($_REQUEST["premium_user"] ? $_REQUEST["premium_user"] : $premium_acc["depositfiles"]["user"]);
            $pass = ($_REQUEST["premium_pass"] ? $_REQUEST["premium_pass"] : $premium_acc["depositfiles"]["pass"]);
            if (empty($user) || empty($pass)) {
                html_error("Login Failed: Username or Password is empty. Please check login data.");
            }

            $postlog = 'http://depositfiles.com/login.php?return=%2F';
            $post['go'] = "1";
            $post['login'] = $user;
            $post['password'] = $pass;
            if ($cap == 1) {
                $post['recaptcha_challenge_field'] = $_POST['challenge'];
                $post['recaptcha_response_field'] = $_POST['captcha'];
            }
            $page = $this->GetPage($postlog, "lang_current=en", $post, 'http://depositfiles.com/');
            if (strpos($page, 'Enter security code') && ($cap == 0)) {
                if (preg_match('@api\/challenge[?]k=([^"]+)@i', $page, $cap) && preg_match('@api\/noscript[?]k=([^"]+)@i', $page, $cap)) {
                    $k = $cap[1];
                }
                $page = $this->GetPage("http://www.google.com/recaptcha/api/challenge?k=$k");
                $ch = cut_str($page, "challenge : '", "'");
                if ($ch) {
                    $page = $this->GetPage("http://www.google.com/recaptcha/api/image?c=$ch");
                    $capture = substr($page, strpos($page, "\r\n\r\n") + 4);
                    $imgfile = DOWNLOAD_DIR . 'depositfiles_captcha.jpg';
                    if (file_exists($imgfile)) unlink($imgfile);
                    write_file($imgfile, $capture);
                } else {
                    html_error('Can\'t find captcha data!');
                }

                $data = $this->DefaultParamArr($postlog);
                $data['step'] = 'Captcha';
                $data['challenge'] = $ch;
                $this->EnterCaptcha($imgfile, $data, 20);
                exit();
            }
            $cookie = GetCookies($page) . '; lang_current=en';
            // WANNA GET YOUR PREMIUM COOKIE? UNCOMMENT THE CODE BELOW, COPY THE COOKIE VALUE IN THE TEXTAREA!
            // $autolog = cut_str($cookie, 'autologin=', ';');
            // textarea($autolog, $cols, $rows, true);
            is_notpresent($cookie, "autologin", "Login Failed , Bad username/password combination");
        } elseif (strlen($autolog) == 32) {
            $cookie = "autologin=$autolog; lang_current=en";
        } else {
            html_error("[Cookie] Invalid cookie (" . strlen($autolog) . " != 32).");
        }

        //IMPORTANT, WE NEED TO CHECK THIS FIRST!
        $page = $this->GetPage('http://depositfiles.com/gold/', $cookie, 0, 'http://depositfiles.com/gold/payment.php');
        is_present($page, 'FREE - member', 'Account free, login not validated!');
        is_notpresent($page, '<div class="goldmembership">', 'Login failed, account is not valid?');

        return $this->DownloadPremium($link, $cookie, $this->GetPage($link, $cookie, 0, $Referer));
    }

    private function DownloadPremium($link, $cookie, $page) {
        global $Referer;

        is_present($page, "You have exceeded the 15 GB 24-hour limit");
        is_present($page, "Such file does not exist or it has been removed for infringement of copyrights.");
        if (stristr($page, 'Please, enter the password for this file')) {
            echo "\n" . '<center><form action="' . $PHP_SELF . '" method="post" >' . "\n";
            echo '<input type="hidden" name="link" value="' . urlencode($link) . '" />' . "\n";
            echo '<input type="hidden" name="cookie" value="' . urlencode(encrypt($cookie)) . '" />' . "\n";
            echo '<input type="hidden" name="step" value="password" />' . "\n";
            echo '<input type="hidden" name="pass" value="premium" />' . "\n";
            echo '<h4>Enter password here: <input type="text" name="file_password" id="password" size="13" />&nbsp;&nbsp;<input type="submit" onclick="return check()" value="Continue" /></h4>' . "\n";
            echo "<script type='text/javascript'>\nfunction check() {\nvar pass=document.getElementById('password');\nif (pass.value == '') { window.alert('You didn\'t enter the password'); return false; }\nelse { return true; }\n}\n</script>\n";
            echo "\n</form></center>\n</body>\n</html>";
            exit();
        }
        if (!preg_match('@http:\/\/.+depositfiles\.com\/auth-[^|\r|\n|\'"]+@i', $page, $dl)) {
            html_error("Error 1x01:Plugin is out of date");
        }
        $dlink = trim($dl[0]);
        $Url = parse_url($dlink);
        $FileName = basename($Url['path']);
        $this->RedirectDownload($dlink, $FileName, $cookie, 0, $Referer);
    }

}

//Written by VinhNhaTrang 12-08-2010
//Updated by vdhdevil & Ruud v.Tony 19-3-2011
//Updated by vdhdevil 30-April-2011: Updated Download Premium
//Updated by Ruud v.Tony 16-May-2011: Updated Download Free
//Updated by Ruud v.Tony 03-Okt-2011: Updated for depositfiles new layout, support captcha in login, password protected file, folder file
//Updated by Ruud v.Tony 05-Okt-2011: Updated in password protected files so we dont need to start over the page :D
?>