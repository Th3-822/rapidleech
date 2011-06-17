<?php

if (! defined ( 'RAPIDLEECH' )) {
        require_once ("index.html");
        exit ();
}

class cramit_in extends DownloadClass {

    public function Download($link) {
        global $premium_acc, $Referer;
        if (($_GET ["premium_acc"] == "on" && $_GET ["premium_user"] && $_GET ["premium_pass"]) ||
            ($_GET ["premium_acc"] == "on" && $premium_acc ["cramit_in"] ["user"] && $premium_acc ["cramit_in"] ["pass"]))
        {
            $this->DownloadPremium($link);
        } elseif ($_POST['step'] == "1") {
            $this->DownloadFree($link);
        } else {
            if (isset($_POST['password'])) {
                $password=$_POST['password'];
            }
            $this->PrepareFree($link, $password);
        }
    }

    private function PrepareFree($link, $password) {
        global $Referer;
        $page = $this->GetPage($link);
        is_present($page, "File Not Found", "The file expired");

        $id = cut_str($page, 'name="id" value="','"');
        $fname = cut_str($page, 'name="fname" value="','"');

        $post = array();
        $post['rand_input'] = "";
        $post['op'] = "download1";
        $post['usr_login'] = "";
        $post['id'] = $id;
        $post['fname'] = $fname;
        $post['referer'] = "";
        $post['method_free'] = "FREE DOWNLOAD";
        $page = $this->GetPage($link, 0, $post, $link);
        $rand = cut_str($page,'name="rand" value="','"');

        if (strpos ($page, "Password :") && !isset($password)) {
            echo "\n" . '<form action="' . $PHP_SELF . '" method="post" >' . "\n";
            echo '<input type="hidden" name="link" value="' . $link . '" />' . "\n";
            echo '<input type="hidden" name="id" value="' . $id . '" />' . "\n";
            echo '<input type="hidden" name="rand" value="' . $rand . '" />' . "\n";
            echo '<input type="hidden" name="referer" value="' . urlencode($link) . '" />' . "\n";
            echo '<h4>Enter password here: <input type="text" name="password" id="filepass" size="13" />&nbsp;&nbsp;<input type="submit" onclick="return check()" value="Submit" /></h4>' . "\n";
            echo "<script language='JavaScript'>\nfunction check() {\nvar pass=document.getElementById('filepass');\nif (pass.value == '') { window.alert('You didn\'t enter the password'); return false; }\nelse { return true; }\n}\n</script>\n";
            echo "</form>\n</body>\n</html>";
            exit;
        }

        if (strpos($page, "recaptcha")) {
            $k = cut_str($page, 'recaptcha.net/challenge?k=', '"');
            $page = $this->GetPage("http://www.google.com/recaptcha/api/challenge?k=" . $k);
            $ch = cut_str($page, "challenge : '", "'");
            $img = "http://www.google.com/recaptcha/api/image?c=".$ch;
            $page = $this->GetPage($img);
            $capt_img = substr($page, strpos($page, "\r\n\r\n") + 4);
            $imgfile = DOWNLOAD_DIR."cramit.jpg";
            if (file_exists($imgfile)) {
                unlink($imgfile);
            }
            write_file($imgfile, $capt_img);

            $data = array();
            $data['step'] = '1';
            $data['link'] = $link;
            $data['id'] = $id;
            $data['rand'] = $rand;
            $data['referer'] = urlencode($link);
            $data['recaptcha_challenge_field'] = $ch;
            $data['password'] = $password;
            $this->EnterCaptcha($imgfile, $data, 20);
            exit();
        }
    }

    private function DownloadFree($link) {
        $post = array();
        $post['op'] = "download2";
        $post['id'] = $_POST['id'];
        $post['rand'] = $_POST['rand'];
        $post['referer'] = urldecode($_POST['referer']);
        $post['method_free'] = 'FREE DOWNLOAD';
        $post['method_premium'] = "";
        $post['recaptcha_challenge_field'] = $_POST['recaptcha_challenge_field'];
        $post['recaptcha_response_field'] = $_POST['captcha'];
        $post['down_direct'] = "1";
        $post['password'] = $_POST['password'];
        $link = $_POST['link'];
        $page = $this->GetPage($link, 0, $post, $link);
        is_present($page, "Wrong password", "The Password you have entered is incorrect, go back & reattempt");
        is_present($page, "Wrong captcha", "The captcha you have entered is incorrect, go back & reattempt");
        if (!preg_match('#(http:\/\/.+cramit\.in\/d\/[^"]+)">click here#', $page, $dlink)) {
            html_error("Error : Download link not found!");
        }
        $dwn = trim($dlink[1]);
        $Url = parse_url($dwn);
        $Filename = basename($Url['path']);
        $this->RedirectDownload($dwn, $Filename, 0, 0, $link);
        exit;
    }

    private function DownloadPremium($link) {
        html_error("Please donate premium account to build downloading Premium");
    }
}

//Cramit.in Free Download Plugin by Ruud v.Tony 2-4-2011
//Updated 11-5-2011 to support password protected files by help vdhdevil
?>