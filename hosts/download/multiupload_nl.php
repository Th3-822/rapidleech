<?php
if (!defined('RAPIDLEECH')) {
    require_once('index.html');
    exit();
}

class multiupload_nl extends DownloadClass {

    public function Download($link) {
        global $premium_acc;

		$link = str_replace('multiupload.com/', 'multiupload.nl/', $link);
        if (preg_match('@http:\/\/.+multiupload\.nl\/([^\r\n]+)\/?@', $link, $match)) {
            $this->id = trim($match[1]);
        }
        if (!$_REQUEST['step']) {
            $this->page = $this->GetPage($link);
            is_present($this->page, "Unfortunately, the link you have clicked is not available.");
        }
        if (($_REQUEST['premium_acc'] == 'on' && $_REQUEST['premium_user'] && $_REQUEST['premium_pass']) || ($_REQUEST['premium_acc'] == 'on' && $premium_acc['multiupload_com']['user'] && $premium_acc['multiupload_com']['pass'])) {
            $this->Premium($link);
        } else {
            $this->Free($link);
        }
    }

    private function Free($link) {
        if ($_REQUEST['step'] == '1') {
            $post['recaptcha_challenge_field'] = $_POST['recaptcha_challenge_field'];
            $post['recaptcha_response_field'] = $_POST['recaptcha_response_field'];
            $postlink = urldecode($_POST['link']);
            $cookie = urldecode($_POST['cookie']);
            $challenge = $_POST['challenge'];
            $this->page = $this->GetPage($postlink, $cookie, $post, $link);
        } else {
            $cookie = GetCookies($this->page);
            if (!preg_match("@\{ xmlobject\.open\('POST', '([^']+)',@i", $this->page, $rd)) {
				echo  ("<center><font color='red'><b>No Direct Link found, proceed to filehost!</b></font></center>");
				if (!preg_match("/progress\/\?d=".$this->id."&r=/", $this->page, $dm)) html_error('Can\'t find id link!');
				$page = $this->GetPage('http://www.multiupload.nl/'.$dm[0].time(), $cookie, 0, $link);
				if (!preg_match_all('/url":"([^\r\n"]+)"/', $page, $tmp)) html_error('Error[Unknown Page Response]');
				$check = '';
				foreach ($tmp[1] as $k => $v) {
					$check .= $this->GetPage(str_replace('\\', '', $v));
				}
				if (!preg_match_all('/location: (http:\/\/[^\r\n]+)/i', $check, $match)) html_error('Can\'t find true filehost link!');
				$this->Submit($match[1]);
				exit;
			}
            $postlink = $link . $rd[1];
            if (!preg_match('@Recaptcha\.create\("([^"]+)",@i', $this->page, $cap)) html_error("Error: Captcha id not found!");
            //send the captcha data
            $data = $this->DefaultParamArr($postlink, $cookie);
            $data['step'] = '1';
            $data['challenge'] = $cap[1]; //incase we have input the wrong captcha
            $this->Show_reCaptcha($cap[1], $data);
            exit();
        }
        if (!preg_match('@\{"([^"]+)":"([^"]+)"\}@', $this->page, $check)) html_error("Error: Unknown Response Page!");
        switch ($check[1]) {
            case 'response':
                echo  ("<center><font color='red'><b>The captcha wasn't entered correctly. Please try again</b></font></center>");
                $data = $this->DefaultParamArr($postlink, $cookie);
                $data['step'] = '1';
                $data['challenge'] = $challenge;
                $this->Show_reCaptcha($challenge, $data);
                break;

            case 'href':
                $dlink = str_replace('\\', '', $check[2]);
                $filename = basename(parse_url($dlink, PHP_URL_PATH));
                $this->RedirectDownload($dlink, $filename, $cookie, 0, $link);
                break;
        }
    }

    private function Premium($link) {
        global $premium_acc;

        html_error("Not supported now!");
    }

    private function Show_reCaptcha($pid, $inputs) {
        global $PHP_SELF;

        if (!is_array($inputs)) {
            html_error("Error parsing captcha data.");
        }

        // Themes: 'red', 'white', 'blackglass', 'clean'
        echo "<script language='JavaScript'>var RecaptchaOptions={theme:'red', lang:'en'};</script>\n";

        echo "\n<center><form name='dl' action='$PHP_SELF' method='post' ><br />\n";
        foreach ($inputs as $name => $input) {
            echo "<input type='hidden' name='$name' id='$name' value='$input' />\n";
        }
        echo "<script type='text/javascript' src='http://www.google.com/recaptcha/api/challenge?k=$pid'></script>";
        echo "<noscript><iframe src='http://www.google.com/recaptcha/api/noscript?k=$pid' height='300' width='500' frameborder='0'></iframe><br />";
        echo "<textarea name='recaptcha_challenge_field' rows='3' cols='40'></textarea><input type='hidden' name='recaptcha_response_field' value='manual_challenge' /></noscript><br />";
        echo "<input type='submit' name='submit' onclick='javascript:return checkc();' value='Download File' />\n";
        echo "<script type='text/javascript'>/*<![CDATA[*/\nfunction checkc(){\nvar capt=document.getElementById('recaptcha_response_field');\nif (capt.value == '') { window.alert('You didn\'t enter the image verification code.'); return false; }\nelse { return true; }\n}\n/*]]>*/</script>\n";
        echo "</form></center>\n</body>\n</html>";
        exit;
    }

    private function Submit($links) {
        global $PHP_SELF, $L;
        if (!is_array($links) && count($links) < 1) html_error("No links found or \$links isn't an array.");
        echo "\n<center><form name='multilink_form' action='$PHP_SELF' method='post' >\n";
        echo "\n<h4>Select a host for download this file:</h4><br />\n";
        echo "<select name='link' style='width:160px;height:20px;'>\n";
        foreach ($links as $Name => $Link) echo "\t<option value='" . urlencode($Link) . "'>" . htmlentities($Link) . "</option>\n";
        echo "</select><br />\n";
        $defdata = $this->DefaultParamArr($link);
        foreach ($defdata as $name => $val) {
            echo "<input type='hidden' name='$name' id='$name' value='$val' />\n";
        }
        echo '<br /><input type="checkbox" name="premium_acc" id="premium_acc" onclick="javascript:var displ=this.checked?\'\':\'none\';document.getElementById(\'premiumblock\').style.display=displ;" checked="checked" />&nbsp;' . $L->say['use_premix'] . '<br /><div id="premiumblock" style="display: none;"><br /><table width="150" border="0"><tr><td>' . $L->say['_uname'] . ':&nbsp;</td><td><input type="text" name="premium_user" id="premium_user" size="15" value="" /></td></tr><tr><td>' . $L->say['_pass'] . ':&nbsp;</td><td><input type="password" name="premium_pass" id="premium_pass" size="15" value="" /></td></tr></table></div><br />';
        echo "<input type='submit' value='Download File' />\n";
        echo "\n</form></center>\n</body>\n</html>";
        exit;
    }
}

/*
 * Multiupload (Direct link) Free Download Plugin by Ruud v.Tony 10/01/2012
 * Add support for redirect link to another filehost by Tony Fauzi Wihana/Ruud v.Tony 13/02/2013
 */
?>
