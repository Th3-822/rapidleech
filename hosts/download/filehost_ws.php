<?php
if (!defined('RAPIDLEECH')) {
	require_once ('index.html');
	exit();
}

class filehost_ws extends DownloadClass {
	
	public $page, $cookie, $link;
	public function Download($link) {
		global $premium_acc;

		if (!$_REQUEST['step']) {
			$this->cookie = array('lang' => 'english');
			$this->page = $this->GetPage($link, $this->cookie);
			is_present($this->page, 'The file you were looking for could not be found, sorry for any inconvenience.');
		}
		$this->link = $link;
		if ($_REQUEST['premium_acc'] == 'on' && (($_REQUEST['premium_user'] && $_REQUEST['premium_pass']) || ($premium_acc['filehost_ws']['user'] && $premium_acc['filehost_ws']['pass']))) {
			return $this->login();
		} else {
			return $this->Free();
		}
	}

	private function login() {
		global $premium_acc;

		$user = ($_REQUEST["premium_user"] ? trim($_REQUEST["premium_user"]) : $premium_acc ["filehost_ws"] ["user"]);
		$pass = ($_REQUEST["premium_pass"] ? trim($_REQUEST["premium_pass"]) : $premium_acc ["filehost_ws"] ["pass"]);
		if (empty($user) || empty($pass)) html_error("Login failed, username or password is empty!");
		
		$posturl = 'http://www.filehost.ws/';
		$post['op'] = 'login';
		$post['redirect'] = urlencode($posturl);
		$post['login'] = $user;
		$post['password'] = $pass;
		$page = $this->GetPage($posturl.'login.html', $this->cookie, $post, $posturl);
		$this->cookie = GetCookiesArr($page, $this->cookie);
		is_present($page, 'Incorrect Login or Password');
		
		//check account
		$page = $this->GetPage($posturl.'?op=my_account', $this->cookie, 0, $posturl);
		if (stripos($page, 'Premium account expire')) {
			$this->changeMesg(lang(300) . "<br />Filehost.ws Premium Download");
			return $this->Premium();
		} else {
			$this->changeMesg(lang(300) . "<br />Filehost.ws Free Member");
			return $this->Free();
		}
	}

	private function Premium() {
		$page = $this->GetPage($this->link, $this->cookie);
		if (!preg_match('/https?:\/\/[\w.]+(:\d+)?\/d\/[^\r\n"]+/', $page, $dl)) {
			$form = cut_str($page, '<Form name="F1" method="POST"', '</Form>');
			if (!preg_match_all('/<input type="hidden" name="([^"]+)" value="([^"]+)?">/', $form, $match)) html_error('Error[Post Data PREMIUM not found!]');
			$match = array_combine($match[1], $match[2]);
			$post = array();
			foreach ($match as $k => $v) {
				$post[$k] = $v;
			}
			$page = $this->GetPage($this->link, $this->cookie, $post, $this->link);
			if (!preg_match('/https?:\/\/[\w.]+(:\d+)?\/d\/[^\r\n"]+/', $page, $dl)) html_error('Error[Download Link PREMIUM not found!]');
		}
		$dlink = trim($dl[0]);
		$filename = basename(parse_url($dlink, PHP_URL_PATH));
		$this->RedirectDownload($dlink, $filename, $this->cookie);
	}

	private function Free() {
		if ($_REQUEST['step'] == 'Recaptcha') {
			$post['op'] = $_POST['op'];
			$post['id'] = $_POST['id'];
			$post['rand'] = $_POST['rand'];
			$post['referer'] = $_POST['referer'];
			$post['method_free'] = $_POST['method_free'];
			$post['method_premium'] = '';
			$post['recaptcha_challenge_field'] = $_POST['challenge'];
			$post['recaptcha_response_field'] = $_POST['captcha'];
			$post['down_direct'] = $_POST['down_direct'];
			$this->link = urldecode($_POST['link']);
			$this->cookie = urldecode($_POST['cookie']);
			$page = $this->GetPage($this->link, $this->cookie, $post, $this->link);
		} else {
			$form = cut_str($this->page, '<Form method="POST" action=\'\'>', '</Form>');
			if (!preg_match_all('/<input type="hidden" name="([^"]+)" value="([^"]+)?">/', $form, $one) || !preg_match_all('/<input type="submit" name="(\w+_free)" value="([^"]+)">/', $form, $two)) html_error('Error[Post Data 1 not found!]');
			$match = array_merge(array_combine($one[1], $one[2]), array_combine($two[1], $two[2]));
			$post = array();
			foreach ($match as $key => $value) {
				$post[$key] = $value;
			}
			$page = $this->GetPage($this->link, $this->cookie, $post, $this->link);
		}
		if (stripos($page, 'Type the two words')) {
			$form = cut_str($page, '<Form name="F1" method="POST"', '</Form>');
			if (stripos($form, cut_str($form, '<div class="err">', '</div>'))) echo ("<center><font color='red'><b>Wrong Captcha, Please rety!</b></font></center>");
			if (!preg_match('/(\d+)<\/span> seconds/', $form, $wait)) html_error('Error[Timer not found!]');
			$this->CountDown($wait[1]);
			if (!preg_match_all('/<input type="hidden" name="([^"]+)" value="([^"]+)?">/', $form, $match)) html_error('Error[Post Data 2 not found!]');
			$match = array_combine($match[1], $match[2]);
			//download the captcha image
			if (!preg_match('/\/api\/challenge\?k=([^"]+)"/', $form, $c)) html_error('Error[Captcha Data not found!]');
            $ch = cut_str($this->GetPage("http://www.google.com/recaptcha/api/challenge?k=$c[1]"), "challenge : '", "'");
            $capt = $this->GetPage("http://www.google.com/recaptcha/api/image?c=" . $ch);
            $capt_img = substr($capt, strpos($capt, "\r\n\r\n") + 4);
            $imgfile = DOWNLOAD_DIR . "filehost_ws_captcha.jpg";

            if (file_exists($imgfile)) unlink($imgfile);
            if (empty($capt_img) || !write_file($imgfile, $capt_img)) html_error("Error getting CAPTCHA image.", 0);
			
			$data = array_merge($this->DefaultParamArr($this->link, $this->cookie), $match);
			$data['step'] = 'Recaptcha';
			$data['challenge'] = $ch;
			$this->EnterCaptcha($imgfile, $data, 20);
			exit();
		}
		is_present($page, cut_str($page, '<div class="err">', '<br>'));
		if (!preg_match('/https?:\/\/[\w.]+(:\d+)?\/d\/[^\r\n"]+/', $page, $dl)) html_error('Error[Download link FREE not found!]');
		$dlink = trim($dl[0]);
		$filename = basename(parse_url($dlink, PHP_URL_PATH));
		$this->RedirectDownload($dlink, $filename, $this->cookie, 0, $this->link);
		exit();
	}

}

/*
 * Written by Ruud v.Tony 19-06-2012
 * Added premium support by Ruud v.Tony 22-06-2012
 */
?>
