<?php
if (!defined('RAPIDLEECH')) {
    require_once ("index.html");
    exit();
}

class cramit_in extends DownloadClass {

    public function Download($link) {
        global $premium_acc;
        // check & replace for other cramit domain
        if (preg_match('@http:\/\/((cramitin\.eu)|(cramitin\.net)|(cramitin\.us))\/@', $link, $match)) {
            $link = str_replace($match[1], "cramit.in", $link);
        }
        if ($_REQUEST['premium_acc'] == 'on' && (($_REQUEST['premium_user'] && $_REQUEST['premium_pass'])|| ($premium_acc['cramit_in']['user'] && $premium_acc['cramit_in']['pass']))) {
            hmtl_error("Not support Premium Now!");
        } else {
            $this->Free($link);
        }
    }
    
    private function Free($link) {
        if ($_REQUEST['down_direct'] == '1') {
            $link = urldecode($_POST['link']);

            $post['op'] = $_POST['op'];
            $post['id'] = $_POST['id'];
            $post['rand'] = $_POST['rand'];
            $post['referer'] = $_POST['referer'];
            $post['method_free'] = $_POST['method_free'];
            $post['method_premium'] = '';
            if (!empty($_POST['password'])) {
                $post['password'] = $_POST['password'];
            }
            $post['code'] = $_POST['captcha'];
            $page = $this->GetPage($link, 0, $post, $link);
        } else {
            $page = $this->GetPage($link);
            is_present($page, "File Not Found");
            
            $form = cut_str($page, '<Form method="POST" action=\'\'>', '</form>');
            if (empty($form)) html_error("Error: Form Data[1] not found!");
            if (!preg_match_all('@<input type="hidden" name="([^"]+)" value="([^"]+)?">@i', $form, $one) || !preg_match_all('@<input type="submit" name="([^"]+)" value="([^"]+)" alt="([^"]+)">@i', $form, $two)) html_error("Error: Post Data[1] not found!");
            $post = array();
            $match = array_merge(array_combine($one[1], $one[2]),array_combine($two[1], $two[2]));
            foreach ($match as $k => $v) {
                $post[$k] = ($v == "") ? 1 : $v;
            }
            $page = $this->GetPage($link, 0, $post, $link);
        }
        if (strpos($page, "Enter the code below:") || strpos($page, "Wrong captcha")) {
            $form = cut_str($page, '<Form name="F1" method="POST"', '</Form>');
            if (empty($form)) html_error("Error: Form Data[2] not found!");
            if (!preg_match_all('@<input type="hidden" name="([^"]+)" value="([^"]+)?">@i', $form, $match)) html_error("Error: Post Data[2] not found!");
            $data = array_combine($match[1], $match[2]);
            echo "\n<center><form name='dl' action='$PHP_SELF' method='post' ><br />\n";
            echo '<input type="hidden" name="link" value="' . urlencode($link) . '" />' . "\n";
            foreach ($data as $name => $value) {
                echo '<input type="hidden" name="' . $name . '" value="' . $value . '" />' . "\n";
            }
            if (stristr($form, "Password :") || strpos($page, "Wrong password")) {
                echo '<h4>Enter password here: <input type="text" name="password" size="10" /></h4><br />';
            }
            echo '<h4>' . lang(301) . ' <img src="' . cut_str($form, '<img src="', '">') . '" /> ' . lang(302) . ': <input type="text" name="captcha" id="capcus" size="20" />&nbsp;&nbsp;<br />';
            echo "<input type='submit' name='submit' onclick='javascript:return checkc();' value='Download File' />\n";
            echo "<script type='text/javascript'>/*<![CDATA[*/\nfunction checkc(){\nvar capt=document.getElementById('capcus');\nif (capt.value == '') { window.alert('You didn\'t enter the image verification code.'); return false; }\nelse { return true; }\n}\n/*]]>*/</script>\n";
            echo "</form></center>\n";
            exit;
        }
        is_present($page, '<p class="err">', cut_str($page, '<p class="err">', '<br>'));
        if (!preg_match('#http://cramit.in/file_download/[^"]+#i', $page, $rd)) html_error("Error: Redirect Link [FREE] not found!");
        $rlink = trim($rd[0]);
        $page = $this->GetPage($rlink, 0, 0, $link);
        if (!preg_match('@Location: (http:\/\/[^\r\n]+)@i', $page, $dl)) html_error("Error: Download Link [FREE] not found!");
        $dlink = trim($dl[1]);
        $filename = basename(parse_url($dlink, PHP_URL_PATH));
        $this->RedirectDownload($dlink, $filename, 0, 0, $link);
        exit();
    }

}

//Cramit.in Free Download Plugin by Ruud v.Tony 2-4-2011
//Updated 11-5-2011 to support password protected files by help vdhdevil
//Fixed for site layout change by Ruud v.Tony 24-06-2011
//Update for the captcha failure n the error message 06-07-2011
//Update again for redirect location in download link 25-07-2011 :hammer:
//Update for including other cramit domain also fix the password form 14/01/2012
?>