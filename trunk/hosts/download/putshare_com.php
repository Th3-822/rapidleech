<?php
if (!defined('RAPIDLEECH')) {
    require_once('index.html');
    exit();
}

class putshare_com extends DownloadClass {

    public function Download($link) {
        $post = array();
        $post['op'] = 'download2';
        $post['referer'] = $link;
        $post['method_free'] = '';
        $post['method_premium'] = '';
        $post['down_script'] = '1';
        if (!empty($_POST['password'])) {
            $post['password'] = $_POST['password'];
            $post['id'] = $_POST['id'];
            $post['rand'] = $_POST['rand'];
        } else {
            $page = $this->GetPage($link);
            is_present($page, '<b>File Not Found</b>');

            $id = cut_str($page, 'name="id" value="', '"');
            $rand = cut_str($page, 'name="rand" value="', '"');

            $post['id'] = $id;
            $post['rand'] = $rand;
            if (strpos($page, '<b>Password:</b> ')) {
                echo "\n" . '<form action="' . $PHP_SELF . '" method="post" >' . "\n";
                echo '<input type="hidden" name="link" value="' . $link . '" />' . "\n";
                echo '<input type="hidden" name="id" value="' . $id . '" />' . "\n";
                echo '<input type="hidden" name="rand" value="' . $rand . '" />' . "\n";
                echo '<h4>Password: <input type="text" name="password" id="password" size="13" />&nbsp;&nbsp;<input type="submit" onclick="return check()" value="Continue" /></h4>' . "\n";
                echo "<script type='text/javascript'>\nfunction check() {\nvar pass=document.getElementById('password');\nif (pass.value == '') { window.alert('You didn\'t enter the password'); return false; }\nelse { return true; }\n}\n</script>\n";
                echo "</form>\n</body>\n</html>";
                exit();
            }
        }
        $page = $this->GetPage($link, 0, $post, $link);
        is_present($page, 'Wrong password');
        
        if (!preg_match('@Location: ([^|\r|\n]+)@i', $page, $dl)) html_error('Error: Download link not found, plugin need to be updated!');
        $dlink = trim($dl[1]);
        $Url = parse_url($dlink);
        $FileName = basename($Url['path']);
        $this->RedirectDownload($dlink, $FileName, 0, 0, $link);
        exit();
    }
}

/*
 * putshare.com free download plugin by Ruud v.Tony 18-10-2011
 */
?>
