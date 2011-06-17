<?php

if (!defined('RAPIDLEECH')) {
    require_once ("index.html");
    exit ();
}

class jumbofiles_com extends DownloadClass {

    public function Download($link) {
        $post = array();
        $post['op'] = "download2";
        $post['method_free'] = "";
        $post['method_premium'] = "";
        $post['down_direct'] = "1";
        if (!empty($_POST['password'])) {
            //get password from user
            $post['id'] = $_POST['id'];
            $post['rand'] = $_POST['rand'];
            $post['referer'] = $link;
            $post['password'] = $_POST['password'];
            $page = $this->GetPage($link, 0, $post, $link); //submit password to filehost
            is_present($page, "Wrong password", "Wrong password");
            if (!preg_match('#http://www.+:443[^"]+#', $page, $dlink)) {
                html_error("Error 0x11: Plugin is out of date");
            }
        } else {
            $page = $this->GetPage($link);
            if (!preg_match('#http://www\d+[^"]+#', $page, $dlink)) {
                //non multimedia file
                $id = cut_str($page, 'name="id" value="', '"');
                $rand = cut_str($page, 'name="rand" value="', '"');
                $post['id'] = $id;
                $post['rand'] = $rand;
                $post['referer'] = $link;
                $post['x'] = rand(0, 70);
                $post['y'] = rand(0, 11);
                if (strpos($page, "Password")) {
                    echo "\n" . '<form name="F1" action="' . $PHP_SELF . '" method="post" >' . "\n";
                    echo '<input type="hidden" name="link" value="' . $link . '" />' . "\n";
                    echo '<input type="hidden" name="id" value="' . $id . '" />' . "\n";
                    echo '<input type="hidden" name="rand" value="' . $rand . '" />' . "\n";
                    echo '<h4>Password: <input type="text" name="password" id="password" size="13" />&nbsp;&nbsp;<input type="hidden" name="down_direct" value="1">&nbsp;&nbsp;<input type="submit" onclick="return check()" value="Submit" /></h4>' . "\n";
                    echo "<script language='JavaScript'>\nfunction check() {\nvar pass=document.getElementById('password');\nif (pass.value == '') { window.alert('You didn\'t enter the password'); return false; }\nelse { return true; }\n}\n</script>\n";
                    echo "</form>\n</body>\n</html>";
                    exit;
                }
                $page = $this->GetPage($link, 0, $post, $link);
                if (!preg_match('#http://www.+:443[^"]+#', $page, $dlink)) {
                    html_error("Error 0x01: Plugin is out of date");
                }
            }
        }
        $Url = parse_url(trim($dlink[0]));
        $FileName = basename($Url['path']);
        $this->RedirectDownload(trim($dlink[0]), $FileName, 0, 0, $link);
        exit;
    }

}

//Practice makin' download plugin with POST methode, support password protected files,multimedia/non multimedia files by VDHDEVIL

?>

