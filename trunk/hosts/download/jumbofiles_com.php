<?php
if (!defined('RAPIDLEECH')) {
    require_once ("index.html");
    exit();
}

class jumbofiles_com extends DownloadClass {

    public function Download($link) {
        global $premium_acc;

        if (!$_REQUEST['step']) {
            $this->page = $this->GetPage($link);
            is_present($this->page, "File is deleted or not found");
            //jumbofiles direct link, no need login or post
            if (preg_match('@http:\/\/www\d+\.jumbofiles\.com\/files\/[^"]+@', $this->page, $dl)) {
                $dlink = trim($dl[0]);
                $filename = basename(parse_url($dlink, PHP_URL_PATH));
                $this->RedirectDownload($dlink, $filename, 0, 0, $link);
            }
        }
        if ($_REQUEST['premium_acc'] == 'on' && (($_REQUEST['premium_user'] && $_REQUEST['premium_pass']) || ($premium_acc['jumbofiles_com']['user'] && $premium_acc['jumbofiles_com']['pass']))) {
            return $this->Premium($link);
        } elseif ($_REQUEST['step'] == 'passpre') {
            return $this->Premium($link, true);
        } else {
            return $this->Free($link);
        }
    }

    private function Free($link) {

        $post = array();
        $post['op'] = 'download2';
        $post['method_free'] = '';
        $post['method_premium'] = '';
        $post['down_direct'] = '1';
        if ($_REQUEST['step'] == 'passfree') {
            $post['id'] = $_POST['id'];
            $post['rand'] = $_POST['rand'];
            $post['referer'] = urldecode($_POST['link']);
            $post['password'] = $_POST['password'];
            $link = urldecode($_POST['link']);
        } else {
            $id = cut_str($this->page, 'name="id" value="', '"');
            $rand = cut_str($this->page, 'name="rand" value="', '"');

            if (stristr($this->page, '<b>Password:</b>')) {
                $data = $this->DefaultParamArr($link);
                $data['id'] = $id;
                $data['rand'] = $rand;
                $data['step'] = 'passfree';
                $this->EnterPassword($data);
                exit();
            } else {
                $post['id'] = $id;
                $post['rand'] = $rand;
                $post['referer'] = $link;
            }
        }
        $this->page = $this->GetPage($link, 0, $post, $link);
        if (strpos($this->page, "Wrong password")) {
            echo "<center><font color='red'><b>Wrong password entered, please retry!</b></font></center>";
            $data = $this->DefaultParamArr($link);
            $data['id'] = cut_str($this->page, 'name="id" value="', '"');
            $data['rand'] = cut_str($this->page, 'name="rand" value="', '"');
            $data['step'] = 'passfree';
            $this->EnterPassword($data);
            exit();
        }
        if (!preg_match('@http:\/\/www\d+\.jumbofiles\.com(:\d+)?\/[^"]+@', $this->page, $dl)) html_error("Error: [Download Link - FREE not found!]");
        $dlink = trim($dl[0]);
        $filename = basename(parse_url($dlink, PHP_URL_PATH));
        $this->RedirectDownload($dlink, $filename, 0, 0, $link);
        exit;
    }

    private function Premium($link, $password = false) {
        if ($password == true) {
            $post['op'] = $_POST['op'];
            $post['id'] = $_POST['id'];
            $post['rand'] = $_POST['rand'];
            $post['referer'] = $_POST['referer'];
            $post['method_free'] = '';
            $post['method_premium'] = $_POST['method_premium'];
            $post['password'] = $_POST['password'];
            $post['down_direct'] = $_POST['down_direct'];
            $link = urldecode($_POST['link']);
            $cookie = decrypt(urldecode($_POST['cookie']));
            $this->page = $this->GetPage($link, $cookie, $post, $link);
        } else {
            $cookie = $this->login();
            $this->page = $this->GetPage($link, $cookie);
        }
        if (!preg_match('@http:\/\/www\d+\.jumbofiles\.com\/files\/[^|\r|\n|"?]+@', $this->page, $dl)) {
            $form = cut_str($this->page, '<Form name="F1" method="POST"', '</Form>');
            if (!preg_match_all('@<input type="hidden" name="([^"]+)" value="([^"]+)?">@i', $form, $match)) html_error("Error [Post Data - PREMIUM not found!]");
            $match = array_combine($match[1], $match[2]);
            if (stristr($form, '<b>Password:</b>') || strpos($this->page, "Wrong password")) {
                $data = array_merge($this->DefaultParamArr($link, encrypt($cookie)), $match);
                $data['step'] = 'passpre';
                $this->EnterPassword($data);
                exit();
            } else {
                $post = array();
                foreach ($match as $key => $value) {
                    $post[$key] = $value;
                }
                $this->page = $this->GetPage($link, $cookie, $post, $link);
                if (!preg_match('@http:\/\/www\d+\.jumbofiles\.com\/files\/[^"]+@', $this->page, $dl)) html_error("Error [Download Link - PREMIUM not found!]");
            }
        }
        $dlink = trim($dl[0]);
        $filename = basename(parse_url($dlink, PHP_URL_PATH));
        $this->RedirectDownload($dlink, $filename, $cookie, 0, $link);
    }

    private function login() {
        global $premium_acc;

        $user = ($_REQUEST["premium_user"] ? trim($_REQUEST["premium_user"]) : $premium_acc ["jumbofiles_com"] ["user"]);
        $pass = ($_REQUEST["premium_pass"] ? trim($_REQUEST["premium_pass"]) : $premium_acc ["jumbofiles_com"] ["pass"]);
        if (empty($user) || empty($pass)) html_error("Login failed, username[$user] or password[$pass] is empty!");

        $posturl = 'http://jumbofiles.com/';
        $post['op'] = 'login';
        $post['redirect'] = '';
        $post['login'] = $user;
        $post['password'] = $pass;
        $post['x'] = rand(11, 99);
        $post['y'] = rand(0, 10);
        $page = $this->GetPage($posturl, 0, $post, $posturl . "login.html");
        $cookie = GetCookies($page);
        is_present($page, "Incorrect Login or Password");

        //check account
        $page = $this->GetPage($posturl . "?op=my_account", $cookie, 0, $posturl . "?op=my_files");
        is_notpresent($page, '<TR><TD>Username:</TD><TD>', 'Invalid Account'); //strange???
        is_notpresent($page, '<TR><TD>Premium-Account expire:</TD><TD>', 'Account Type: FREE!');

        return $cookie;
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

//Practice makin' download plugin with POST methode, support password protected files,multimedia/non multimedia files by VDHDEVIL
//Updated to support premium also checking link by Ruud v.Tony 09-02-2012
?>

