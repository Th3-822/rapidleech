<?php
if (!defined('RAPIDLEECH')) {
    require_once("index.html");
    exit;
}

class netload_in extends DownloadClass {

    public function Download($link) {
        global $premium_acc, $Referer;

        $this->link = $link;
        //check the link
        if (!$_REQUEST['step']) {
            $this->page = $this->GetPage($this->link);
            if (preg_match('/Location: (\/[^|\r|\n]+)/i', $this->page, $temp)) {
                $this->link = 'http://netload.in' . $temp[1];
                $this->page = $this->GetPage($this->link);
            }
            is_present($this->page, 'Code: ER_NFF', 'Error[File not found]!');
        }
        if ($_REQUEST['premium_acc'] == 'on' && (($_REQUEST['premium_user'] && $_REQUEST['premium_pass']) || ($premium_acc['netload_in']['user'] && $premium_acc['netload_in']['pass']))) {
            return $this->Login();
        } elseif ($_REQUEST['step'] == 'password') {
            $post['file_id'] = $_POST['file_id'];
            $post['password'] = $_POST['password'];
            $post['submit'] = $_POST['submit'];
            $this->link = urldecode($_POST['link']);
            if (!empty($_POST['cookie'])) {
                $this->cookie = decrypt(urldecode($_POST['cookie']));
                $this->page = $this->GetPage($this->link, $this->cookie, $post, $Referer);
                return $this->Premium();
            } else {
                $this->page = $this->GetPage($this->link, 0, $post, $Referer);
                return $this->Retrieve();
            }
        } elseif ($_REQUEST['step'] == 'captcha') {
            return $this->Free();
        } else {
            return $this->Retrieve();
        }
    }

    private function Retrieve() {
        global $Referer;

        if (stristr($this->page, 'This file is password-protected!')) {
            $form = cut_str($this->page, '<form name="form" method="post"', '</form>');
            if (!preg_match('%action="([^"]+)"%', $form, $pw)) html_error("Error[getFreePassLink]");
            $this->link = 'http://netload.in/' . $pw[1];
            if (!preg_match_all('%<input type="(hidden|submit)" value="([^"]+)?" name="([^"]+)" \/>%', $form, $match)) html_error("Error[getFreePostPass]");
            $data = array_merge($this->DefaultParamArr('http://netload.in/' . $pw[1]), array_combine($match[3], $match[2]));
            $data['step'] = 'password';
            $this->EnterPassword($data);
            exit();
        }
        $this->cookie = GetCookies($this->page);
        if (!preg_match('%<div class="Free_dl"><a href="([^"]+)">%', $this->page, $temp)) html_error('Error[getFreeLink]');
        $this->link = 'http://netload.in/' . html_entity_decode($temp[1], ENT_QUOTES, 'UTF-8');
        $this->page = $this->GetPage($this->link, $this->cookie, 0, $Referer);
        if (!preg_match('@countdown\(([0-9]+),\'change\(\)\'\)@', $this->page, $wait)) html_error('Error[getFreeTimer1]!');
        $this->CountDown($wait[1] / 100);
        if (stripos($this->page, 'Please enter the security code')) {
            $form = cut_str($this->page, '<form method="post"', '</form>');
            if (!preg_match('%action="([^"]+)">%', $form, $temp) || !preg_match('%src="([^"]+)" alt="Sicherheitsbild" \/>%', $form, $cap)) html_error("Error[getCaptchaLink/Image]");
            if (!preg_match_all('@name="([^"]+)" type="(hidden|submit)" value="([^"]+)?"@', $form, $match)) html_error("Error[getCaptchaPostData]");

            $capt = $this->GetPage('http://netload.in/' . $cap[1], $this->cookie);
            $capt_img = substr($capt, strpos($capt, "\r\n\r\n") + 4);
            $imgfile = DOWNLOAD_DIR . "netload_captcha.png";
            if (file_exists($imgfile)) unlink($imgfile);
            if (empty($capt_img) || !write_file($imgfile, $capt_img)) html_error("Error getting CAPTCHA image.");
            
            $data = array_merge($this->DefaultParamArr('http://netload.in/' . $temp[1], $this->cookie), array_combine($match[1], $match[3]));
            $data['step'] = 'captcha';
            $this->EnterCaptcha($imgfile, $data);
            exit();
        }
    }

    private function Free() {
        global $Referer;

        $post['file_id'] = $_POST['file_id'];
        $post['captcha_check'] = $_POST['captcha'];
        $post['start'] = $_POST['start'];
        $this->link = urldecode($_POST['link']);
        $this->cookie = urldecode($_POST['cookie']);
        $this->page = $this->GetPage($this->link, $this->cookie, $post, $Referer);
        if (!preg_match('#countdown\(([0-9]+),\'change\(\)\'\)#', $this->page, $wait)) html_error('Error[getFreeTimer2]!');
        $timer = trim($wait[1]) / 100;
        if ($timer > 20) html_error("Error[Limit reach, you can download your next file in " . round($timer / 60) . " minute]!");
        $this->CountDown($timer);
        if (!preg_match('@http:\/\/[\d.]+\/[^|\r|\n|"]+@', $this->page, $dl)) html_error('Error[getFreeDownloadLink]');
        $dlink = trim($dl[0]);
        $filename = basename(parse_url($dlink, PHP_URL_PATH));
        $this->RedirectDownload($dlink, $filename, $this->cookie, 0, $Referer);
        exit();
    }

    private function Login() {
        global $premium_acc;

        $user = ($_REQUEST["premium_user"] ? trim($_REQUEST["premium_user"]) : $premium_acc["netload_in"]["user"]);
        $pass = ($_REQUEST["premium_pass"] ? trim($_REQUEST["premium_pass"]) : $premium_acc["netload_in"]["pass"]);
        if (empty($user) || empty($pass)) html_error("Login Failed: User [$user] or Password [$pass] is empty. Please check login data.");

        $posturl = 'http://netload.in/';
        $post['txtuser'] = $user;
        $post['txtpass'] = $pass;
        $post['txtcheck'] = 'login';
        $post['txtlogin'] = 'Login';
        $this->page = $this->GetPage($posturl . 'index.php', 0, $post, $posturl);
        is_present($this->page, '/index.php?id=15', 'Login failed, invalid username or password???');
        $this->cookie = GetCookies($this->page);
        //check the premium account (IMPORTANT!)
        $this->page = $this->GetPage($posturl . 'index.php?id=2', $this->cookie);
        is_present($this->page, 'Order Premium Account now', 'Account Status : FREE!');
        //start download the link
        $this->page = $this->GetPage($this->link, $this->cookie, 0, $this->link);

        return $this->Premium();
    }

    private function Premium() {
        if (stristr($this->page, 'This file is password-protected!')) {
            $form = cut_str($this->page, '<form name="form" method="post"', '</form>');
            if (!preg_match('%action="([^"]+)"%', $form, $pw)) html_error("Error[getPrePassLink]");
            if (!preg_match_all('%<input type="(hidden|submit)" value="([^"]+)?" name="([^"]+)" \/>%', $form, $match)) html_error("Error[getPrePostPass]");
            
            $data = array_merge($this->DefaultParamArr('http://netload.in/' . $pw[1], encrypt($this->cookie)), array_combine($match[3], $match[2]));
            $data['step'] = 'password';
            $this->EnterPassword($data);
            exit();
        }
        if (!preg_match('@http:\/\/[\d.]+\/[^|\r|\n|\'"]+@i', $this->page, $dl)) html_error('Error[getPremiumDownloadLink]');
        $dlink = trim($dl[0]);
        $filename = basename(parse_url($dlink, PHP_URL_PATH));
        $this->RedirectDownload($dlink, $filename, $this->cookie);
    }

    private function EnterPassword($inputs) {
        global $PHP_SELF;

        if (!is_array($inputs)) {
            html_error("Error parsing password data!");
        }
        echo "\n" . '<center><form action="' . $PHP_SELF . '" method="post" >' . "\n";
        foreach ($inputs as $name => $val) {
            echo "<input type='hidden' name='$name' id='$name' value='$val' />\n";
        }
        echo '<h4>Enter password here: <input type="text" name="password" id="filepass" size="13" />&nbsp;&nbsp;<input type="submit" onclick="return check()" value="Continue" /></h4>' . "\n";
        echo "<script type='text/javascript'>\nfunction check() {\nvar pass=document.getElementById('filepass');\nif (pass.value == '') { window.alert('You didn\'t enter the password'); return false; }\nelse { return true; }\n}\n</script>\n";
        echo "\n</form></center>\n</body>\n</html>";
        exit();
    }

}

//updated 05-jun-2010 for standard auth system (szal)
//updated 05-Okt-2011 for premium & free, password protected files by Ruud v.Tony
//small fix in checkin' link 10-Okt-2011 by Ruud v.Tony
//fix password & captcha form layout by Ruud v.Tony 02-02-2012
?>