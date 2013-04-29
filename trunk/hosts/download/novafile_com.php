<?php
if (!defined('RAPIDLEECH')) {
	require_once ('index.html');
	exit();
}

class novafile_com extends DownloadClass {

	public function Download($link) {
		global $premium_acc;

		if (!$_REQUEST['step']) {
			$this->page = $this->GetPage($link);
			is_present($this->page, 'File could not be found due to its possible expiration or removal by the file owner.');
		}
		if ($_REQUEST['premium_acc'] == 'on' && (($_REQUEST['premium_user'] && $_REQUEST['premium_pass'])||($premium_acc['novafile_com']['user'] && $premium_acc['novafile_com']['pass']))) {
			return $this->Premium($link);
		} else {
			return $this->Free($link);
		}
	}

	private function Free($link) {
		if ($_REQUEST['step'] == '1') {
			$post['op'] = $_POST['op'];
			$post['id'] = $_POST['id'];
			$post['rand'] = $_POST['rand'];
			$post['referer'] = $_POST['referer'];
			$post['method_free'] = $_POST['method_free'];
			$post['method_premium'] = '';
			$post['recaptcha_challenge_field'] = $_POST['challenge'];
			$post['recaptcha_response_field'] = $_POST['captcha'];
			$post['down_direct'] = $_POST['down_direct'];
			$link = urldecode($_POST['link']);
			$page = $this->GetPage($link, 0, $post, $link);
		} else {
			$form = cut_str($this->page, '<div id="create-download">', '</div>');
			if (!preg_match_all('/<input type="hidden" name="([^"]+)" value="([^"]+)?">/', $form, $match)) html_error('Error[Post Data 1 not found!]');
			$match = array_combine($match[1], $match[2]);
			$post = array();
			foreach ($match as $k => $v) {
				$post[$k] = $v;
			}
			$page = $this->GetPage($link, 0, $post, $link);
		}
		is_present($page, cut_str($page, '<div class="alert alert-warning alert-separate">', '<br>'));
		if (stripos($page, 'Create Download Link')) {
			unset($post);
			if (stripos($page, 'Wrong captcha')) echo ("<center><font color='red'><b>Wrong Captcha, Please rety!</b></font></center>");
			if (preg_match('/(\d+)<\/span> seconds/', $page, $w)) $this->CountDown ($w[1]);
			$form = cut_str($page, '<div id="captcha" class="alert alert-info">', '</form>');
			if (!preg_match_all('/<input type="hidden" name="([^"]+)" value="([^"]+)?">/', $form, $match)) html_error('Error[Post Data 2 not found!]');
			$match = array_combine($match[1], $match[2]);
			//download the captcha image
			if (!preg_match('/\/api\/challenge\?k=([^"]+)"/', $page, $c)) html_error('Error[Captcha Data not found!]');
            $ch = cut_str($this->GetPage("http://www.google.com/recaptcha/api/challenge?k=$c[1]"), "challenge : '", "'");
            $capt = $this->GetPage("http://www.google.com/recaptcha/api/image?c=" . $ch);
            $capt_img = substr($capt, strpos($capt, "\r\n\r\n") + 4);
            $imgfile = DOWNLOAD_DIR . "novafile_captcha.jpg";

            if (file_exists($imgfile)) unlink($imgfile);
            if (empty($capt_img) || !write_file($imgfile, $capt_img)) html_error("Error getting CAPTCHA image.", 0);

			$data = array_merge($this->DefaultParamArr($link), $match);
			$data['step'] = '1';
			$data['challenge'] = $ch;
			$this->EnterCaptcha($imgfile, $data, 20);
			exit();
		}
		if (!preg_match('/<a href="(https?:\/\/[^\r\n]+)" class="btn btn-green">Download File<\/a>/', $page, $dl)) html_error('Error[Download Link - FREE not found!]');
		$dlink = trim($dl[1]);
		$filename = basename(parse_url($dlink, PHP_URL_PATH));
		$this->RedirectDownload($dlink, $filename, 0, 0, $link);
		exit();
	}

	private function Premium($link) {

		$cookie = $this->login();
		$page = $this->GetPage($link, $cookie);
		$form = cut_str($page, '<div id="create-download">', '</form>');
		if (!preg_match_all('/<input type="hidden" name="([^"]+)" value="([^"]+)?">/', $page, $match)) html_error('Error[Post Data - PREMIUM not found!]');
		$match = array_combine($match[1], $match[2]);
		$post = array();
		foreach ($match as $key => $value) {
			$post[$key] = $value;
		}
		$page = $this->GetPage($link, $cookie, $post, $link);
		if (!preg_match('/<a href="(https?:\/\/[^\r\n"]+)" class="btn btn-green">Download File<\/a>/', $page, $dl)) html_error('Error[Download Link - PREMIUM not found!]');
		$dlink = trim($dl[1]);
		$filename = basename(parse_url($dlink, PHP_URL_PATH));
		$this->RedirectDownload($dlink, $filename, $cookie);
	}

	private function login() {
		global $premium_acc;

        $user = ($_REQUEST["premium_user"] ? trim($_REQUEST["premium_user"]) : $premium_acc ["novafile_com"] ["user"]);
        $password = ($_REQUEST["premium_pass"] ? trim($_REQUEST["premium_pass"]) : $premium_acc ["novafile_com"] ["pass"]);
        if (empty($user) || empty($password)) html_error("Login failed, username or password is empty!");

		$posturl = 'http://novafile.com/';
		$post['login'] = $user;
		$post['password'] = $password;
		$post['op'] = 'login';
		$post['redirect'] = '';
		$post['rand'] = '';
		$page = $this->GetPage($posturl.'login', 0, $post, $posturl.'login');
		is_present($page, 'Incorrect Login or Password');
		$cookie = GetCookies($page);

		//check account
		$page = $this->GetPage($posturl.'?op=my_account', $cookie, 0, $posturl.'login');
		is_notpresent($page, $posturl.'premium.html', 'Account isn\'t Premium!');

		return $cookie;
	}
}

/*
 * Written by Ruud v.Tony 28-07-2012
 * Updated to support premium by Ruud v.Tony 08-08-2012
 */
?>
