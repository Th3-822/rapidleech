<?php

if (!defined('RAPIDLEECH')) {
    require_once ("index.html");
    exit ();
}

class fileserving_com extends DownloadClass {
	
	    public function Download($link) {
        global $premium_acc, $options;
        if (($_REQUEST ["premium_acc"] == "on" && $_REQUEST ["premium_user"] && $_REQUEST ["premium_pass"]) || ($_REQUEST ["premium_acc"] == "on" && $premium_acc ["fileserving_com"] ["user"] && $premium_acc ["fileserving_com"] ["pass"])) {
            $this->DownloadPremium($link);
        } elseif ($_POST['step'] == "1") {
            $this->DownloadFree($link);
        } else {
            $this->Retrieve($link);
        }
    }
	
   private function Retrieve($link) {
        global $options;
        $page = $this->GetPage($link);
		if (preg_match("#Location: (.*)#", $page)) {
            html_error("File Not Available or Not Found: Error 01.");
        }
		preg_match("#HTTP\/1\.1 ([0-9]+)#", $page, $err);
		if($err[1] == '400'){
			html_error('Error 400: Bad Request');
		}
		if (!preg_match("#verify_url = '([^']+)'#", $page, $pth)) {
            html_error("Cannot get URL ReCaptcha");
        }
		if (!preg_match("#fid:'([^']+)'#", $page, $fid)) {
            html_error("File Not Available or Not Found. Error 02");
        }
		if (!preg_match("#server:'([^']+)'#", $page, $server)) {
            html_error("Cannot get URL of Download");
        }
        $Cookies = GetCookies($page);
		$page = $this->GetPage("http://www.google.com/recaptcha/api/challenge?k=6LfrS8kSAAAAAIMFkpPQ3wacYcW2EbM7mC53RvvD");
	    $sr = cut_str($page, "challenge : '", "'");
        $img = "http://www.google.com/recaptcha/api/image?c=".$sr;
		$url = $pth[1].'?fid='.$fid[1].'&sid=1&server='.$server[1].'&recaptcha_challenge_field='.$sr;
        $page = $this->GetPage($img);
        $head = strpos($page, "\r\n\r\n");
        $img = substr($page, $head + 4);
        write_file($options['download_dir'] . "fileserving_captcha.jpg", $img);
        $data = array();
        $data['link'] = urlencode($link);
        $data['step'] = '1';
        $data['cookie'] = urlencode($Cookies);
        $data['url']= urlencode(trim($url));
        $this->EnterCaptcha($options['download_dir'] . "fileserving_captcha.jpg", $data, 20);
        exit;
    }
	    private function DownloadFree($link) {
        $enter = $_POST['captcha'];
        $Cookies = urldecode($_POST['cookie']);
		$go = urldecode($_POST['url']);
		$url = $go.'&recaptcha_response_field='.$enter;
        $page = $this->GetPage($url, $Cookies, 0, $link);
        if (!preg_match('#"rs":"([^"]+)"#', $page, $rs)) {
            html_error("Cannot get response of ReCaptcha");
        }
		if($rs[1] != 'ok'){
			html_error("Invalid Captcha! Please try again.");
		}
		$dl = explode('"', $page);
		$dlink = str_replace("\\","",$dl[8]);
		if(empty($dlink)){
			html_error('Cannot get download link');
		}
        $this->RedirectDownload(trim($dlink), "FileName", $Cookies, 0, $link);
        exit;
    }
	
	    private function DownloadPremium($link) {
        global $premium_acc;
		$username = $_REQUEST["premium_user"] ? trim($_REQUEST["premium_user"]) : $premium_acc ["fileserving_com"] ["user"];
		$password = $_REQUEST["premium_pass"] ? trim($_REQUEST["premium_pass"]) : $premium_acc ["fileserving_com"] ["pass"];
		if(empty($username) or empty($password)){
			html_error('Usuer and/or Password this empty.');
		}
		$url = 'http://www.fileserving.com/Public/login';
		$page = $this->GetPage($url, 0, 0, "http://www.fileserving.com");
		if (!preg_match('#name="__hash__"[\r|\n|\s]+value="([^"]+)"#', $page, $hash)){
			html_error("Cannot get Hash.");
		}
        $post = array();
        $post["username"] = $username;
        $post["password"] = $password;
        $post["dpt_id"] = "0";
		$post["from_dpt"] = "3016";
		$post["fail_callback"] = "http://www.fileserving.com/Public/login/error/[msg]";
        $post["success_callback"] = "http://www.fileserving.com/Api/central_login";
		$post["__hash__"] = $hash[1];
		$urllogin = "https://account.yesup.com/login/client";
        $page = $this->GetPage($urllogin, 0, $post, "http://www.fileserving.com");
		if (!preg_match("#Location: (.*)#", $page, $sucess)) {
			html_error('Cannot get redirection of login.');
		}
		$error = explode('/error/', $sucess[1]);
		if (!empty($error[1])) {
			html_error('Error in login: '.$error[1]);
		}
		$cookie = GetCookies($page);
		if(empty($cookie)){
			html_error('Error in login, update plugin, or try later.');
		}
		$htt = 'https://account.yesup.com/'.$sucess[1];
		$page = $this->GetPage($htt, $cookie, 0, "http://www.fileserving.com/Public/login");
		if (!preg_match("#Location: (.*)#", $page, $mov)) {
			html_error('Unable to retrieve authentication.');
		}
		$page = $this->GetPage($mov[1], $cookie, 0, $htt);
		$lang = GetCookies($page);
		$cookie .= '; '.$lang;
		$my = 'http://www.fileserving.com/';
		$page = $this->GetPage($my, $cookie, 0, "http://www.fileserving.com");
		if (!preg_match("#Account[\r|\n|\s]+Type:[\r|\n|\s]+<span>(.*)</span>#", $page, $acc)) {
			html_error('Unable to verify if the account is premium.');
		}
		if (trim($acc[1]) != 'Premium') {
			html_error('Your account is not premium.');
		}
        $page = $this->GetPage($link, $cookie, 0, "http://www.fileserving.com");
        if (!preg_match("#Location: (.*)#", $page, $dl)) {
			html_error('Failed to recover download link premium.');
		}
		if (trim($dl[1]) == 'http://www.fileserving.com') {
			html_error('File Not Available or Not Found.');
		}
		preg_match("#HTTP\/1\.1 ([0-9]+)#", $page, $err);
		if($err[1] == '400'){
			html_error('Error 400: Bad Request');
		}
      $this->RedirectDownload(trim($dl[1]), "Fileserving");
        exit();
    }

}
//work by simplesdescarga 11/02/2012
?>