<?php
if (!defined('RAPIDLEECH')) {
    require_once ('index.html');
    exit();
}

class necroupload_com extends DownloadClass {
	public function Download($link) {
        global $premium_acc, $options;
        if (($_REQUEST ["premium_acc"] == "on" && $_REQUEST ["premium_user"] && $_REQUEST ["premium_pass"]) || ($_REQUEST ["premium_acc"] == "on" && $premium_acc ["necroupload_com"] ["user"] && $premium_acc ["necroupload_com"] ["pass"])) {
            $this->Premium($link);
        } else {
            $this->Free($link);
        }
    }
    private function Free($link) {
        if ($_REQUEST['down_script'] == '1') {
            $post['op'] = $_POST['op'];
            $post['id'] = $_POST['id'];
            $post['rand'] = $_POST['rand'];
            $post['referer'] = $_POST['referer'];
            $post['method_free'] = $_POST['method_free'];
            $post['method_premium'] = '';
            $post['recaptcha_challenge_field'] = $_POST['recaptcha_challenge_field'];
            $post['recaptcha_response_field'] = $_POST['recaptcha_response_field'];
            $link = urldecode($_POST['link']);
            $page = $this->GetPage($link, "lang=english", $post, $link);
        } else {
            $page = $this->GetPage($link, "lang=english");
            is_present($page, cut_str($page, '<div class="err">', '</div>'));
            is_present($page, '<b>File Not Found</b>');
            $form = cut_str($page, "<Form method=\"POST\" action=''>", "</form>");
            if(!preg_match_all('#input type="hidden" name="([^"]+)" value="([^"]+)"#', $form, $preg))html_error('Cannot get data in post');
            $post = array_combine($preg[1], $preg[2]);
			$post['method_free'] = 'Free Download';
            $page = $this->GetPage($link, "lang=english", $post, $link);
        }
        if (strpos($page, 'Type the two words')) {
            $form = cut_str($page, '<Form name="F1" method="POST"', '</Form>');
            if (preg_match('#(\d+)<\/span> seconds#', $form, $wait)) $this->CountDown ($wait[1]);
            if (!preg_match('#api\/challenge\?k=([^"]+)"#', $form, $captcha)) html_error("Cannot get data of Captcha");
            if (!preg_match_all('#<input type="hidden" name="([^"]+)" value="([^"]+)?">#', $form, $preg)) html_error ("Cannot get data form");
            $post = array_merge($this->DefaultParamArr($link), array_combine($preg[1], $preg[2]));
			$this->ReCaptcha($post, $captcha[1]);
            exit();
        }
        is_present($page, cut_str($page, '<div class="err">', '</div>'));
        if (!preg_match('#ocation: (.*)#', $page, $dl)) html_error("Cannot get download link free");
        $dlink = trim($dl[1]);
        $filename = basename(parse_url($dlink, PHP_URL_PATH));
        $this->RedirectDownload($dlink, $filename, 0, 0, $link);
        exit();
    }
	private function Premium($link) {
        global $premium_acc;
        $username = $_REQUEST["premium_user"] ? trim($_REQUEST["premium_user"]) : $premium_acc ["necroupload_com"] ["user"];
        $password = $_REQUEST["premium_pass"] ? trim($_REQUEST["premium_pass"]) : $premium_acc ["necroupload_com"] ["pass"];
        if(empty($username) or empty($password))html_error('Usuer and/or Password this empty.');
        $post = array();
        $post["login"] = $username;
        $post["password"] = $password;
        $post["redirect"] = "http://necroupload.com/";
        $post["op"] = 'login';
        $url = 'http://necroupload.com/';
        $page = $this->GetPage($url, 0, $post);
        is_page($page);
        is_present($page, "Incorrect Login or Password");
        $cookies = GetCookies($page);
        $page = $this->GetPage($url.'?op=my_account', $cookies); 
        is_present($page, 'Upgrade to premium', 'Account Expired or NOT PREMIUM');
        $page = $this->GetPage($link, $cookies);
        is_present($page, cut_str($page, '<div class="err">', '</div>'));
        is_present($page, '<b>File Not Found</b>');
        if (preg_match("#ocation: (.*)#", $page, $dl)) {
            $name = basename(parse_url(trim($dl[1]), PHP_URL_PATH));
            if(empty($name))html_error('Cannot Get Filename [0x01]');
            $this->RedirectDownload(trim($dl[1]), $name, $cookies, 0, $link);
            exit();
        }
        $form = cut_str($page, '<Form name="F1" method="POST"', '</form>');
        if(!preg_match_all('#input type="hidden" name="([^"]+)" value="([^"]+)"#', $form, $preg))html_error('Cannot get data in post');
        $post = array_combine($preg[1], $preg[2]);
        $page = $this->GetPage($link, $cookies, $post, $link);
        is_present($page, cut_str($page, '<div class="err">', '</div>'));
        if(!preg_match('#http\:\/\/necroupload.com\/files\/([^"]+)#', $page, $dl))html_error('Failed to recover download link premium.');
        $name = basename(parse_url(trim($dl[0]), PHP_URL_PATH));
        if(empty($name))html_error('Cannot Get Filename [0x02]');
        $this->RedirectDownload(trim($dl[0]), $name, $cookies, 0, $link);
        exit();
    }
	private function ReCaptcha($post, $captcha){
		global $PHP_SELF;
		if(is_array($post) && !empty($captcha)){
			echo "<script language='JavaScript'>var RecaptchaOptions={theme:'white', lang:'en'};</script>\n";
            echo "\n<center><form name='dl' action='$PHP_SELF' method='post' ><br />\n";
            foreach ($post as $name => $input) {
                echo "<input type='hidden' name='$name' id='$name' value='$input' />\n";
            }
            echo "<script type='text/javascript' src='http://www.google.com/recaptcha/api/challenge?k=$captcha'></script>";
            echo "<noscript><iframe src='http://www.google.com/recaptcha/api/noscript?k=$captcha' height='300' width='500' frameborder='0'></iframe><br />";
            echo "<textarea name='recaptcha_challenge_field' rows='3' cols='40'></textarea><input type='hidden' name='recaptcha_response_field' value='manual_challenge' /></noscript><br />";
            echo "<input type='submit' name='submit' onclick='javascript:return checkc();' value='Enter Captcha' />\n";
            echo "<script type='text/javascript'>/*<![CDATA[*/\nfunction checkc(){\nvar capt=document.getElementById('recaptcha_response_field');\nif (capt.value == '') { window.alert('You didn\'t enter the image verification code.'); return false; }\nelse { return true; }\n}\n/*]]>*/</script>\n";
            echo "</form></center>\n</body>\n</html>";
		}else{
			html_error('Error generating the CAPTCHA');
		}
	}
}

/*
 * by SD-88 08.09.2012
 */
?>