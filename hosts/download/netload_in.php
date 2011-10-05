<?php
if (!defined('RAPIDLEECH')) {
    require_once("index.html");
    exit;
}

class netload_in extends DownloadClass {

    public function Download($link) {
        global $premium_acc;

        if (($_REQUEST['premium_acc'] == 'on' && $_REQUEST['premium_user'] && $_REQUEST['premium_pass']) || ($_REQUEST['premium_acc'] == 'on' && $premium_acc['netload_in']['user'] && $premium_acc['netload_in']['pass'])) {
            $this->Login($link);
        } elseif ($_POST['pass'] == 'premium') {
            $post['file_id'] = $_POST['file_id'];
            $post['password'] = $_POST['password'];
            $post['submit'] = $_POST['submit'];
            $link = urldecode($_POST['link']);
            $cookie = decrypt(urldecode($_POST['cookie']));
            $page = $this->GetPage($link, $cookie, $post);
            is_present($page, 'The entered password is wrong.');
            return $this->Premium($link, $cookie, $page);
        } elseif ($_POST['pass'] == 'free') {
            $post['file_id'] = $_POST['file_id'];
            $post['password'] = $_POST['password'];
            $post['submit'] = $_POST['submit'];
            $link = urldecode($_POST['link']);
            $page = $this->GetPage($link, 0, $post);
            is_present($page, 'The entered password is wrong.');
            return $this->Retrieve($link, $page);
        } elseif ($_POST['step'] == '1') {
            $this->Free($link);
        } else {
            $this->Retrieve($link);
        }
    }

    private function Retrieve($link, $page = false) {
        if (!$page) {
            $page = $this->GetPage($link);
            if (preg_match('/Location: ([^|\r|\n]+)/i', $page, $temp)) {
                $link = 'http://netload.in' . $temp[1];
                $page = $this->GetPage($link);
            }
            is_present($page, 'Code: ER_NFF', 'Error[File not found]!');
        }
        if (stristr($page, 'This file is secured with a password')) {
            if (preg_match('%method="post" action="(.*)"%', $page, $pw)) {
                $link = 'http://netload.in/'.$pw[1];
            }
            if (preg_match('%value="(.*)" name="file_id"%', $page, $fi)) {
                $fid = trim($fi[1]);
            }
            $data = $this->DefaultParamArr($link);
            $data['pass'] = 'free';
            $data['file_id'] = $fid;
            $data['submit'] = 'show';
            $this->EnterPassword($page, $data);
            exit();
        }
        $cookie = GetCookies($page);
        if (preg_match('%href="(index\.php\?.+captcha.+?)"%', $page, $temp)) {
            $link = 'http://netload.in/' . html_entity_decode($temp[1], ENT_QUOTES, 'UTF-8');
        } else {
            html_error('Error[getFreeLink]');
        }
        $page = $this->GetPage($link, $cookie, 0, $link);
        if (!preg_match('#countdown\(([0-9]+),\'change\(\)\'\)#', $page, $wait)) html_error('Error[Timer 1 not found]!');
        $this->CountDown($wait[1] / 100);
        if (strpos($page, 'Please enter the Securitycode')) {
            if (preg_match('%<form method="post" action="(.*)">%', $page, $temp)) {
                $actlink = 'http://netload.in/' . $temp[1];
            }
            if (preg_match('%(share/includes/captcha\.php\?.+?)"%', $page, $cap)) {
                $img_link = 'http://netload.in/' . $cap[1];
            }
            $data = $this->DefaultParamArr($actlink, $cookie, $link);
            $data['step'] = '1';
            $data['file_id'] = cut_str($link, 'file_id=', '&');
            $this->EnterCaptcha($img_link, $data);
            exit();
        }
    }

    private function Free($link) {

        $post['file_id'] = $_POST['file_id'];
        $post['captcha_check'] = $_POST['captcha'];
        $post['start'] = '';
        $link = urldecode($_POST['link']);
        $cookie = urldecode($_POST['cookie']);
        $Referer = urldecode($_POST['referer']);
        $page = $this->GetPage($link, $cookie, $post, $Referer);
        if (!preg_match('#countdown\(([0-9]+),\'change\(\)\'\)#', $page, $wait)) html_error('Error[Timer 2 not found]!');
        $timer = $wait[1] / 100;
        if ($timer > 20) {
            html_error("Error[Limit reach, you can download your next file in " . $timer / 60 . " minute]!");
        } else {
            $this->CountDown($timer);
        }
        if (!preg_match('%href="(.*)">Click here for the download%', $page, $dl)) html_error('Error[getFreeDownloadLink]');
        $Url = parse_url($dl[1]);
        $FileName = basename($Url['path']);
        $this->RedirectDownload($dl[1], $FileName, $cookie, 0, $Referer);
        exit();
    }

    private function Login($link) {
        global $premium_acc;

        $user = ($_REQUEST["premium_user"] ? $_REQUEST["premium_user"] : $premium_acc["netload_in"]["user"]);
        $pass = ($_REQUEST["premium_pass"] ? $_REQUEST["premium_pass"] : $premium_acc["netload_in"]["pass"]);

        $postlogin = 'http://netload.in/index.php';
        $post['txtuser'] = $user;
        $post['txtpass'] = $pass;
        $post['txtcheck'] = 'login';
        $post['txtlogin'] = '';
        $page = $this->GetPage($postlogin, 0, $post, 'http://netload.in/');
        is_present($page, '/index.php?id=15', 'Login failed, invalid username or password???');
        $cookie = GetCookies($page);
        //check the premium account (IMPORTANT!)
        $page = $this->GetPage($postlogin . '?id=2', $cookie);
        is_present($page, 'No premium</span>', 'Account Free, login not validated!');

        return $this->Premium($link, $cookie);
    }

    private function Premium($link, $cookie, $page = false) {
        if (!$page) {
            $page = $this->GetPage($link, $cookie);
            if (preg_match('/Location: ([^|\r|\n]+)/i', $page, $temp)) {
                $link = 'http://netload.in' . $temp[1];
                $page = $this->GetPage($link, $cookie);
            }
            is_present($page, 'Code: ER_NFF', 'Error[File not found]!');
        }
        if (stristr($page, 'This file is secured with a password')) {
            if (preg_match('%method="post" action="(.*)"%', $page, $pw)) {
                $link = 'http://netload.in/'.$pw[1];
            }
            if (preg_match('%value="(.*)" name="file_id"%', $page, $fi)) {
                $fid = trim($fi[1]);
            }
            $data = $this->DefaultParamArr($link, encrypt($cookie));
            $data['pass'] = 'premium';
            $data['file_id'] = $fid;
            $data['submit'] = 'show';
            $this->EnterPassword($page, $data);
            exit();
        }
        if (!preg_match('@http:\/\/[\d.]+\/[^|\r|\n|\'?"?]+@i', $page, $dl)) html_error('Error[getPremiumDownloadLink]');
        $dlink = trim($dl[0]);
        $Url = parse_url($dlink);
        $FileName = basename($Url['path']);
        $this->RedirectDownload($dlink, $FileName, $cookie);
    }

    private function EnterPassword($page, $inputs) {
        global $PHP_SELF;

        echo "\n" . '<center><form action="' . $PHP_SELF . '" method="post" >' . "\n";
        foreach ($inputs as $name => $input) {
            echo "<input type='hidden' name='$name' id='$name' value='$input' />\n";
        }
        echo '<h4>Enter password here: <input type="text" name="password" id="filepass" size="13" />&nbsp;&nbsp;<input type="submit" onclick="return check()" value="Continue" /></h4>' . "\n";
        echo "<script type='text/javascript'>\nfunction check() {\nvar pass=document.getElementById('filepass');\nif (pass.value == '') { window.alert('You didn\'t enter the password'); return false; }\nelse { return true; }\n}\n</script>\n";
        echo "\n</form></center>\n</body>\n</html>";
        exit();
    }
}

//updated 05-jun-2010 for standard auth system (szal)
//updated 05-Okt-2011 for premium & free, password protected files by Ruud v.Tony
?>