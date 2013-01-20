<?php
if (!defined('RAPIDLEECH')) {
    require_once ('index.html');
    exit();
}

class uplly_com extends DownloadClass {
	public function Download($link) {
        global $premium_acc, $options;
        if (($_REQUEST ["premium_acc"] == "on" && $_REQUEST ["premium_user"] && $_REQUEST ["premium_pass"]) || ($_REQUEST ["premium_acc"] == "on" && $premium_acc ["uplly_com"] ["user"] && $premium_acc ["uplly_com"] ["pass"])) {
            $this->Premium($link);
        } else {
            $this->Free($link);
        }
    }
	    private function Free($link){
        $page = $this->GetPage($link);
		$cookie = 'lang=english';
        is_present($page, '<b>File Not Found</b>');
        is_present($page, 'This file downloading are located on server maintenance. Please try again after few hours.');
        $form = cut_str($page, "<Form method=\"POST\" action='", '</form>');
        if(!preg_match_all('#input type="hidden" name="([^"]+)" value="([^"]+)"#', $form, $preg))html_error('Cannot get data in post');
        $post = array_combine($preg[1], $preg[2]);
		$post['method_free'] = 'Free Download';
		$page = $this->GetPage($link, $cookie, $post, $link);
        if(preg_match('#You have to wait (\d+) minutes#', $page, $minutes)) $wait1 = $minutes[1]*60;
        if(preg_match('#(\d+) seconds till next download#', $page, $seconds)){
            $wait = $wait1 + $seconds[1];
            unset($post);
            $post['link'] = urlencode($link);
            $post['stop'] = '1';
            $this->JSCountdown($wait, $post, 'You did more than a download in a short time');
            exit();
        }
        if(preg_match('#<p class="err">([^<>]+)</p>#', $page, $error))html_error($error[1]);
        if(!preg_match('#">(\d+)</span> seconds</span>#', $page, $time))html_error('Cannot get wait time');
        insert_timer($time[1]);
        $form = cut_str($page, '<Form name="F1" method="POST"', "</form>");
		unset($post);
        if(!preg_match_all('#input type="hidden" name="([^"]+)" value="([^"]+)"#', $form, $preg))html_error('Cannot get data in post');
        $post = array_combine($preg[1], $preg[2]);
        $cap = cut_str($page, 'Enter code below:</b></td></tr>', '</table>');
        if(!preg_match_all("@padding-left:(\d+)px;padding-top:(\d+)px;'>([^<>]+)@", $cap, $parametro))html_error('Cannot get data of Captcha');
        $code = array_combine($parametro[1], $parametro[3]);
        ksort($code);
        foreach ($code as $valor) {
            $captcha .= $this->decode_utf8_number($valor);
        }
        $post['code'] = $captcha;
        $page = $this->GetPage($link, $cookie, $post, $link);
		if(!preg_match("#(.*)http*s*://.+/files/[^\r|\n|\s|'|\"]+#", $page, $dl))html_error('Cannot get Download Link');
		$dlink = trim(str_replace($dl[1], '', $dl[0]));
        $filename = basename(parse_url($dlink, PHP_URL_PATH));
		$this->RedirectDownload($dlink, $filename);
		exit();
    }
	private function decode_utf8_number($str){
    $car = array(
         '0' => '/&#48;/',
		 '1' => '/&#49;/',
		 '2' => '/&#50;/',
	     '3' => '/&#51;/',
		 '4' => '/&#52;/',
		 '5' => '/&#53;/',
		 '6' => '/&#54;/',
		 '7' => '/&#55;/',
		 '8' => '/&#56;/',
	     '9' => '/&#57;/',
		);
   return preg_replace($car, array_keys($car), $str);
}
	private function Premium($link) {
        global $premium_acc;
        $username = $_REQUEST["premium_user"] ? trim($_REQUEST["premium_user"]) : $premium_acc ["uplly_com"] ["user"];
        $password = $_REQUEST["premium_pass"] ? trim($_REQUEST["premium_pass"]) : $premium_acc ["uplly_com"] ["pass"];
        if(empty($username) or empty($password))html_error('Usuer and/or Password this empty.'); 
        $url = 'http://uplly.com/';
        $post = array();
        $post["login"] = $username;
        $post["password"] = $password;
        $post["op"] = "login";
        $post["redirect"] = "";
        $page = $this->GetPage($url, 0, $post, $url);
        is_page($page);
        is_present($page, "Incorrect Login or Password");
        $cookies = GetCookies($page);
        if (!preg_match("#ocation: (.*)#", $page))html_error('Error in login.');
        $page = $this->GetPage($link, $cookies, 0, $url);
        if(strpos($page, 'Upgrade to premium'))html_error('Premium account not valid or expired');
        if (!preg_match("#http*s*://.+/files/[^\r|\n|\s|'|\"]+#", $page, $dl)) {
            $form = cut_str($page, '<Form name="F1" method="POST"', '</Form>');
            if (!preg_match_all('@<input type="hidden" name="([^"]+)" value="([^"]+)?">@', $form, $ck)) html_error ("Cannot get data of form");
            $post = array_merge(array_combine($ck[1], $ck[2]));
            $page = $this->GetPage($link, $cookies, $post, $url);
            if(!preg_match('#http*s*://.+/files/[^\r|\n|\s|\'|"]+#', $page, $dl))html_error('Failed to recover download link premium.');
	}
        $filename = basename(parse_url(trim($dl[0]), PHP_URL_PATH));
        if(empty($filename))html_error('Cannot Get Filename');
        $this->RedirectDownload(trim($dl[0]), $filename, $cookies, 0, $link);
        exit();
    }
}

/*
 * by SD-88 08.11.2012
 */
?>