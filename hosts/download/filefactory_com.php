<?php
if (!defined('RAPIDLEECH')) {
	require_once ("index.html");
	exit();
}

class filefactory_com extends DownloadClass {
	private $page, $cookie;
	public function Download($link) {
		global $premium_acc;

		$link = str_ireplace('://filefactory.com/', '://www.filefactory.com/', $link);
		if (empty($_POST['step']) || $_POST['step'] != 1) {
			$this->page = $this->GetPage($link);
			$this->cookie = GetCookiesArr($this->page);
			if (preg_match('@Location: .*(/file/[^\r|\n]+)@i', $this->page, $RD)) {
				$link = 'http://www.filefactory.com' . $RD[1];
				$this->page = $this->GetPage($link);
				$this->cookie = GetCookiesArr($this->page, $this->cookie);
			}
			is_present($this->page, 'This file has been deleted', 'File deleted.');
			is_present($this->page, 'this file is no longer available', 'The file is no longer available.');
			if ($this->Check_TS()) return; // insert_location doesn't do exit()... So i must to stop the plugin if it's a trafficshare link.
		}

		if (($_REQUEST['cookieuse'] == 'on' && preg_match('@ff_membership\s*=\s*([\w|/|\+|=]+)@i', rawurldecode($_REQUEST['cookie']), $c)) || ($_REQUEST['premium_acc'] == 'on' && !empty($premium_acc['filefactory_com']['cookie']))) {
			$cookie = (empty($c[1]) ? rawurldecode($premium_acc['filefactory_com']['cookie']) : $c[1]);
			$this->Download_Premium($link, $cookie);
		} elseif ($_REQUEST['premium_acc'] == 'on' && ((!empty($_REQUEST['premium_user']) && !empty($_REQUEST['premium_pass'])) || (!empty($premium_acc['filefactory_com']['user']) && !empty($premium_acc['filefactory_com']['pass'])))) {
			$this->Download_Premium($link);
		} elseif (isset($_POST['step']) && $_POST['step'] == 1) {
			$this->Download_Free($link);
		} else {
			$this->Prepare_Free($link);
		}
	}

	private function Check_TS() {
		if (preg_match('@Location: .*(/trafficshare/[^\r|\n]+)@i', $this->page, $RD)) {
			$link = 'http://www.filefactory.com' . $RD[1];
			$this->page = $this->GetPage($link);
			$this->cookie = GetCookiesArr($this->page, $this->cookie);
		}
		if (preg_match('@https?://(?:[^/|\r|\n|\"|\'|<|>|\s|\t]+\.)?filefactory\.com/dlp/[^\r|\n|\"|\'|<|>|\s|\t]+@i', $this->page, $dl)) {
			$filename = urldecode(basename(parse_url($dl[0], PHP_URL_PATH)));
			if (stripos($dl[0], ';') !== false) $dl[0] = str_replace(array('&',';'), '', $dl[0]);
			$this->RedirectDownload($dl[0], $filename, $this->cookie, 0, 0, $filename);
			return true;
		}
		return false;
	}

	private function Prepare_Free($link) {
		is_present($this->page, '<strong>temporarily limited</strong>',
			'Your access to the free download service has been <strong>temporarily limited</strong> to prevent abuse... Please wait 10 minutes or more and try again.');
		if (preg_match('@Location: [^\r|\n]*/premium/index\.php\?e=([^&|\r|\n]+)@i', $this->page, $b64err)) {
			$error = base64_decode(urldecode($b64err[1]));
			is_present($error, 'All free download slots are in use.');
			html_error('Unknown redirect to premium page.');
		}

		if (!preg_match('/check\s*:\s*\'([^\']+)/i', $this->page, $ck) || !preg_match('/Recaptcha\.create\s?\([\s|\t|\r|\n]*"([^"]+)/i', $this->page, $pid)) html_error('Error getting CAPTCHA.');

		$data = $this->DefaultParamArr($link, $this->cookie);
		$data['check'] = $ck[1];
		$data['step'] = '1';
		$this->Show_reCaptcha($pid[1], $data);
	}

	private function Show_reCaptcha($pid, $inputs) {
		global $PHP_SELF;

		if (!is_array($inputs)) {
			html_error('Error parsing captcha data.');
		}

		// Themes: 'red', 'white', 'blackglass', 'clean'
		echo "<script language='JavaScript'>var RecaptchaOptions={theme:'red', lang:'en'};</script>\n";

		echo "\n<center><form name='dl' action='$PHP_SELF' method='POST' ><br />\n";
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

	private function Download_Free($link) {
		if (empty($_POST['recaptcha_response_field'])) {
			html_error('You didn\'t enter the image verification code.');
		}
		$post['recaptcha_challenge_field'] = $_POST['recaptcha_challenge_field'];
		$post['recaptcha_response_field'] = $_POST['recaptcha_response_field'];
		if(!empty($_POST['recaptcha_shortencode_field'])) $post['recaptcha_shortencode_field'] = $_POST['recaptcha_shortencode_field'];
		$post['check'] = $_POST['check'];
		$this->cookie = urldecode($_POST['cookie']);

		$page = $this->checkcaptcha($post);

		is_present($page, '<strong>temporarily limited</strong>',
			'Your access to the free download service has been <strong>temporarily limited</strong> to prevent abuse... Please wait 10 minutes or more and try again.');

		if (!preg_match('/href="([^"]+)"[^>]*>Click here to download now/i', $page, $D)) html_error('Download-link not found.');
		$dllink = $D[1];

		if (!preg_match('/"countdown">(\d+)/i', $page, $C)) html_error('Failed to get link time-lock.');
		$wait = $C[1];
		$this->CountDown($wait);

		$filename = urldecode(basename(parse_url($dllink, PHP_URL_PATH)));
		if (stripos($dllink, ';') !== false) $dllink = str_replace(array('&',';'), '', $dllink);
		$this->RedirectDownload($dllink, $filename, $this->cookie, 0, 0, $filename);
	}

	private function checkcaptcha($post) {
		$page = $this->GetPage('http://www.filefactory.com/file/checkCaptcha.php', $this->cookie, $post);
		if (!preg_match('/\{"status":"([^"]+)","(path|message)":"([^"]+)"\}/i', $page, $stat)) html_error('Error validating CAPTCHA.');

		if ($stat[1] == 'ok') return $this->GetPage('http://www.filefactory.com/' . str_replace('\\', '', $stat[3]), $this->cookie);
		elseif (stripos($stat[3], 'incorrect') !== false) html_error('Entered code was incorrect.');

		html_error("Error validating CAPTCHA: {$stat[3]}.. Please try again.");
	}

	private function Download_Premium($link, $cookie = '') {
		$this->login($cookie);

		$page = $this->GetPage($link, $this->cookie);
		if (preg_match('/Location: .*(\/file\/[^\r|\n]+)/i', $page, $RD)) { // This redirect it's useless but i will let here to be sure. :D
			$link = 'http://www.filefactory.com' . $RD[1];
			$page = $this->GetPage($link, $this->cookie);
		}

		if (!preg_match('@https?://(?:[^/|\r|\n|\"|\'|<|>|\s|\t]+\.)?filefactory\.com/dlp/[^\r|\n|\"|\'|<|>|\s|\t]+@i', $page, $dl)) html_error('Download-link not found.');

		$filename = urldecode(basename(parse_url($dl[0], PHP_URL_PATH)));
		if (stripos($dl[0], ';') !== false) $dl[0] = str_replace(array('&',';'), '', $dl[0]);
		$this->RedirectDownload($dl[0], $filename, $this->cookie, 0, 0, $filename);
	}

	private function login($cookie = '') {
		if (empty($cookie)) {
			global $premium_acc;
			$pA = !empty($_REQUEST['premium_user']) && !empty($_REQUEST['premium_pass']) ? true : false;
			$email = $pA ? $_REQUEST['premium_user'] : $premium_acc['filefactory_com']['user'];
			$password = $pA ? $_REQUEST['premium_pass'] : $premium_acc['filefactory_com']['pass'];

			if (empty($email) || empty($password)) html_error('Login Failed: Email or Password is empty. Please check login data.');

			$postURL = 'http://www.filefactory.com/member/login.php';
			$post = array();
			$post['redirect'] = '/member/';
			$post['email'] = urlencode($email);
			$post['password'] = urlencode($password);
			$page = $this->GetPage($postURL, 0, $post, $postURL);
			$this->cookie = GetCookiesArr($page);

			is_present($page, '?err=', 'Login Failed: The email or password you have entered is incorrect.');
			if (empty($this->cookie['ff_membership'])) html_error('Login Failed.');
		} else $this->cookie = array('ff_membership' => rawurlencode($cookie));

		$page = $this->GetPage('http://www.filefactory.com/member/?login=1', $this->cookie);
		if (!empty($cookie)) {
			$this->cookie = GetCookiesArr($page, $this->cookie);
			if (stripos($page, '/member/login.php') !== false || stripos($page, 'Location: /member/logout.php') !== false) html_error('Login Failed: Invalid cookie.');
		}
		is_present($page, '>Free member<', 'Login Failed: The account isn\'t premium.');
	}
}

//[26-Nov-2010] Written by Th3-822.
//[20-Feb-2011] Fixed regex for redirect & Fixed errors in filename & Changed captcha funtion for show reCaptcha. - Th3-822
//[10-Feb-2012] Added error msg at freedl & 2 Regexps edited. -Th3-822
//[11-Mar-2012] Fixed getting dlink in premium account (Sometimes the request it's redirected to freedl error page) && Fixed login function. - Th3-822
//[23-Mar-2012] Fixed free dl regexps. - Th3-822
//[22-May-2012] Added support for files with Traffic Share on freedl. - Th3-822
//[11-Jul-2012] Fixed regexp for files with Traffic Share on freedl && fixed regexp at premiumdl && minor changes. - Th3-822
//[10-Aug-2012] Added cookie support & fixed free acc check. - Th3-822
//[22-Aug-2012] Fixed Traffic Share support (Again) & added error msg. - Th3-822

?>