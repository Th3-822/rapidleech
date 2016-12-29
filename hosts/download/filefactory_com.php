<?php
if (!defined('RAPIDLEECH')) {
	require_once ("index.html");
	exit();
}

class filefactory_com extends DownloadClass {
	private $page, $cookie = array('ff_locale' => 'en_US.utf8'), $lpass = '';
	public function Download($link) {
		global $premium_acc;

		$arr = explode('|', str_replace('%7C', '|', $link), 2);
		if (count($arr) >= 2) {
			$this->lpass = (strpos($link, '|') === false) ? rawurldecode($arr[1]) : $arr[1];
			$link = $arr[0];
		}
		unset($arr);

		$this->link = $GLOBALS['Referer'] = str_ireplace('://filefactory.com/', '://www.filefactory.com/', $link);

		$post = empty($this->lpass) ? 0 : array('password' => urlencode($this->lpass), 'Submit' => 'Continue');
		$this->page = $this->GetPage($this->link, $this->cookie, $post);
		$this->cookie = GetCookiesArr($this->page, $this->cookie);

		$this->CheckPass();

		$X = 0;
		while ($X < 3 && preg_match('@\nLocation: .*(/(?:file|preview)/[^\r\n]+)@i', $this->page, $RD)) {
			$this->link = 'http://www.filefactory.com' . $RD[1];
			$this->page = $this->GetPage($this->link, $this->cookie);
			$this->cookie = GetCookiesArr($this->page, $this->cookie);
			$X++;
		}

		if (preg_match('@/error\.php\?code=(\d+)@i', $this->page, $this->redir) && !in_array($this->redir[1], array('257', '258')) /*Forcing PPS is not fair...*/) {
			if ($this->redir[1] == '160') html_error('IP banned by FF.');
			$page = $this->GetPage('http://www.filefactory.com'.$this->redir[0], $this->cookie);
			if (preg_match('@class="alert alert-error">\s*<h2>\s*([\w\-\s]+)\s*</h2>@i', $page, $err)) html_error("[FF:{$this->redir[1]}] ".htmlspecialchars($err[1]));
			html_error("Link is not available [{$this->redir[1]}]");
		}
		if (stripos($this->page, 'error.php?code=') === false && $this->CheckTS()) return; // insert_location doesn't do exit()... So i must to stop the plugin if it's a trafficshare link.

		if (stripos($this->link, '/preview/') === false && $_REQUEST['premium_acc'] == 'on' && ((!empty($_REQUEST['premium_user']) && !empty($_REQUEST['premium_pass'])) || (!empty($premium_acc['filefactory_com']['user']) && !empty($premium_acc['filefactory_com']['pass'])))) $this->Login();
		else $this->FreeDL();
	}

	private function CheckTS() {
		if (preg_match('@https?://(?:[a-zA-Z\d\-]+\.)*filefactory\.com/get/t/[^\r\n\"\'<>\s\t]+@i', $this->page, $dl)) {
			$filename = urldecode(basename(parse_url($dl[0], PHP_URL_PATH)));
			if (stripos($dl[0], ';') !== false) $dl[0] = str_replace(array('&',';'), '', $dl[0]);
			$this->RedirectDownload($dl[0], $filename, $this->cookie, 0, 0, $filename);
			return true;
		}
		return false;
	}

	private function CheckPass() {
		if (stripos($this->page, 'This File has been password protected by the uploader.') !== false || stripos($this->page, '>Please enter the password</') !== false) {
			if (empty($this->lpass)) html_error('File is password protected, please send the password in this format: link|pass');
			else html_error('The link\'s password you have sended is not valid.');
		}
	}

	private function FreeDL() {
		if (!empty($this->redir)) {
			$page = $this->GetPage('http://www.filefactory.com'.$this->redir[0], $this->cookie);
			if (preg_match('@class="alert alert-error">\s*<h2>\s*([\w\-\s]+)\s*</h2>@i', $page, $err)) html_error("[FF:{$this->redir[1]}] ".htmlspecialchars($err[1]));
			html_error("Link is not available [{$this->redir[1]}]");
		}

		if (!preg_match('@https?://(?:[a-zA-Z\d\-]+\.)*filefactory\.com/get/f/[^\r\n\"\'<>\s\t]+@i', $this->page, $dllink)) html_error('Download Link Not Found.');
		$dllink = $dllink[0];

		if (!preg_match('@data-delay\s*=\s*"(\d+)"@i', $this->page, $C) && stripos($this->link, '/preview/') === false) html_error('Failed to Get Link Time-lock.');
		if (!empty($C[1])) $this->CountDown($C[1]);

		$filename = urldecode(basename(parse_url($dllink, PHP_URL_PATH)));
		if (stripos($dllink, ';') !== false) $dllink = str_replace(array('&',';'), '', $dllink);
		$this->RedirectDownload($dllink, $filename, $this->cookie, 0, 0, $filename);
	}

	private function PremiumDL() {
		$this->page = $this->GetPage($this->link, $this->cookie);
		$this->cookie = GetCookiesArr($this->page, $this->cookie);

		$X = 0;
		while ($X < 3 && preg_match('@\nLocation: .*(/(?:file|preview)/[^\r\n]+)@i', $this->page, $RD)) {
			$this->link = 'http://www.filefactory.com' . $RD[1];
			$this->page = $this->GetPage($this->link, $this->cookie);
			$this->cookie = GetCookiesArr($this->page, $this->cookie);
			$X++;
		}

		if (preg_match('@/error\.php\?code=(\d+)@i', $this->page, $this->redir)) {
			$this->page = $this->GetPage('http://www.filefactory.com'.$this->redir[0], $this->cookie);
			if (preg_match('@class="alert alert-error">\s*<h2>\s*([\w\-\s]+)\s*</h2>@i', $this->page, $err)) html_error("[FF:{$this->redir[1]}]-".htmlspecialchars($err[1]));
			html_error("Link is not available. [{$this->redir[1]}]");
		}

		if (!preg_match('@https?://(?:[a-zA-Z\d\-]+\.)*filefactory\.com/get/p/[^\r\n\"\'<>\s\t]+@i', $this->page, $dl)) html_error('Download-Link Not Found.');

		$filename = urldecode(basename(parse_url($dl[0], PHP_URL_PATH)));
		if (stripos($dl[0], ';') !== false) $dl[0] = str_replace(array('&',';'), '', $dl[0]);
		$this->RedirectDownload($dl[0], $filename, $this->cookie, 0, 0, $filename);
	}

	private function Login() {
		global $premium_acc;
		$pA = !empty($_REQUEST['premium_user']) && !empty($_REQUEST['premium_pass']) ? true : false;
		$email = $pA ? $_REQUEST['premium_user'] : $premium_acc['filefactory_com']['user'];
		$password = $pA ? $_REQUEST['premium_pass'] : $premium_acc['filefactory_com']['pass'];

		if (empty($email) || empty($password)) html_error('Login Failed: Email or Password is empty. Please check login data.');

		$postURL = 'http://www.filefactory.com/member/signin.php';
		$post = array();
		$post['loginEmail'] = urlencode($email);
		$post['loginPassword'] = urlencode($password);
		$post['Submit'] = 'Sign+In';
		$page = $this->GetPage($postURL, 0, $post, $postURL);
		is_present($page, 'The Email Address submitted was invalid', 'Login Failed: Invalid email address.');
		is_present($page, 'The email address or password you have entered is incorrect.', 'Login Failed: The Email/Password you have entered is incorrect.');

		$this->cookie = GetCookiesArr($page, $this->cookie);
		if (empty($this->cookie['auth'])) html_error('Login Failed, auth cookie not found.');

		$page = $this->GetPage('http://www.filefactory.com/account/', $this->cookie, 0, $postURL);
		is_present($page, "\nLocation: /member/settos.php", 'TOS have changed and need to be approved at the site.');
		is_present($page, "\nLocation: /member/setpwd.php", 'Your password has expired, please change it.');
		if (stripos($page, '>Free Member<') !== false) {
			$this->changeMesg(lang(300).'<br /><b>Account isn\'t premium</b><br />Using it as member.');
			$this->page = $this->GetPage($this->link, $this->cookie);
			preg_match('@/error\.php\?code=(\d+)@i', $this->page, $this->redir);
			return $this->FreeDL();
		}
		return $this->PremiumDL();
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
//[14-May-2013] Fixed freedl (ff removed the captcha... But the code still at page, so i will let it on the plugin too). - Th3-822
//[17-Sep-2013] Rewritten for make it work with the new site & Cookie support removed & Added password protected links support. - Th3-822
//[24-Oct-2013] Added a error at login & fixed redirect on premium download. - Th3-822
//[18-Nov-2013] Fixed premium-dl error msgs. - Th3-822
//[02-Dec-2016] Fixed login error msgs. - Th3-822

?>