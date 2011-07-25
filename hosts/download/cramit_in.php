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
        global $PHP_SELF;
            $page = $this->GetPage($link);
            is_present($page, "File Not Found", "The file expired");

            $id = cut_str($page, 'name="id" value="','"');
            $FileName = cut_str($page, 'name="fname" value="','"');

            $post = array();
            $post['rand_input'] = "";
            $post['op'] = "download1";
            $post['usr_login'] = "";
            $post['id'] = $id;
            $post['fname'] = $FileName;
            $post['referer'] = $link;
            $post['method_free'] = "FREE DOWNLOAD";
            $page = $this->GetPage($link, 0, $post, $link);
            if (stristr($page, 'class="err"')) {
                $errmsg = cut_str($page, 'class="err">', '<br>');
                html_error($errmsg);
            }
            $rand = cut_str($page,'name="rand" value="','"');
            if (strpos ($page, "Password :") && !isset($password)) {
                echo "\n" . '<form action="' . $PHP_SELF . '" method="post" >' . "\n";
                echo '<input type="hidden" name="link" value="' . $link . '" />' . "\n";
                echo '<input type="hidden" name="id" value="' . $id . '" />' . "\n";
                echo '<input type="hidden" name="rand" value="' . $rand . '" />' . "\n";
                echo '<input type="hidden" name="referer" value="' . $link . '" />' . "\n";
                echo '<h4>Enter password here: <input type="text" name="password" id="filepass" size="13" />&nbsp;&nbsp;<input type="submit" onclick="return check()" value="Submit" /></h4>' . "\n";
                echo "<script language='JavaScript'>\nfunction check() {\nvar pass=document.getElementById('filepass');\nif (pass.value == '') { window.alert('You didn\'t enter the password'); return false; }\nelse { return true; }\n}\n</script>\n";
                echo "</form>\n</body>\n</html>";
                exit();
            }
            if (strpos($page, "Enter the code below:")) {
                preg_match('#(http:\/\/.+captchas\/[^"]+)">#', $page, $temp);

                $data = $this->DefaultParamArr($link, 0, $link);
                $data['step'] = '1';
                $data['id'] = $id;
                $data['rand'] = $rand;
                $data['password'] = $password;
                $data['filename'] = $FileName;
                $this->EnterCaptcha($temp[1], $data, 20);
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
        $post['code'] = $_POST['captcha'];
        $post['down_direct'] = "1";
        $post['password'] = $_POST['password'];
        $FileName = $_POST['filename'];
        $page = $this->GetPage($link, 0, $post, $link);
        if (strpos($page, "Wrong password") || strpos($page, "Wrong captcha")) {
            return $this->PrepareFree($link, $password);
        }
        if (!preg_match('/(http:\/\/cramit\.in\/file_download\/\w+\/\w+\/free\/[^"]+)"/', $page, $match)) {
            html_error("Error 1: Redirect location cant be found!");
        }
        $tlink = trim($match[1]);
        $page = $this->GetPage($tlink, 0, 0, $link);
        if (!stristr($page, 'Location:')) {
            html_error("Error 2: Download link cant be found!");
        }
        $dlink = trim(cut_str( $page, "Location: ", "\n" ));
        $Url = parse_url($dlink);
        if (!$FileName) $FileName = basename($Url['path']);
        $this->RedirectDownload($dlink, $Filename, 0, 0, $link);
        exit;
    }

    private function DownloadPremium($link) {
        html_error("Please donate premium account to build downloading Premium");
    }
}

//Cramit.in Free Download Plugin by Ruud v.Tony 2-4-2011
//Updated 11-5-2011 to support password protected files by help vdhdevil
//Fixed for site layout change by Ruud v.Tony 24-06-2011
//Update for the captcha failure n the error message 06-07-2011
//Update again for redirect location in download link 25-07-2011 :hammer:
?>