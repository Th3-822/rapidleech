<?php

if (!defined('RAPIDLEECH')) {
	require_once("index.html");
	exit;
}

class ifile_it extends DownloadClass {
	private $cookie, $_url, $_ukey, $dlreq, $captcha, $usecurl;
	public function Download($link) {
		global $premium_acc, $Referer;
		$Referer = $link;
		$this->cookie = array();
		if (!function_exists('json_decode')) /* Load a class?... Or maybe we can add a 'json_decode' function in others.php... */ html_error("Error: Please enable JSON in php.");

		// Check https support for login.
		$this->usecurl = $cantlogin = false;
		if (!extension_loaded('openssl')) {
			if (extension_loaded('curl')) {
				$cV = curl_version();
				if (in_array('https', $cV['protocols'], true)) $this->usecurl = true;
				else $cantlogin = true;
			} else $cantlogin = true;
			if ($_REQUEST["premium_acc"] == "on" && !empty($premium_acc["ifile_it"]["apikey"])) $cantlogin = false;
			if ($cantlogin) $this->changeMesg(lang(300)."<br /><br />Https support: NO<br />Login disabled.");
		}

		if (!$cantlogin && $_REQUEST["premium_acc"] == "on" && (((!empty($_REQUEST["premium_user"]) && !empty($_REQUEST["premium_pass"])) || ($premium_acc["ifile_it"]["user"] && $premium_acc["ifile_it"]["pass"])) || !empty($premium_acc["ifile_it"]["apikey"]))) $this->Login($link);
		elseif ($_POST["skip"] == "true") $this->chkcaptcha($link);
		else $this->Prepare($link);
	}

	private function Prepare($link) {
		$page = $this->GetPage($link, $this->cookie);
		is_present($page, "ifile.it/?error=", "File not Found or Deleted");
		$this->cookie = array_merge($this->cookie, GetCookiesArr($page));
		$_s = '[\s|\t]'; // I'm too lazy :D

		if (!preg_match("@var$_s+_url$_s*=$_s*'([^']+)'@i", $page, $this->_url)) html_error("Error: Cannot find url for post.");
		$this->_url = $this->_url[1];

		if (!preg_match("@var$_s+__ukey$_s*=$_s*'([^']+)'@i", $page, $this->_ukey)) html_error("Error: File id not found.");
		$this->_ukey = $this->_ukey[1];

		if (!preg_match("@var$_s+__site$_s*=$_s*'([^']+)'@i", $page, $_site)) $_site = array('','http://ifile.it/');
		$this->dlreq = $_site[1]."download-request.json?ukey=".$this->_ukey;

		if (!preg_match("@var$_s+__recaptcha_public$_s*=$_s*'([^']+)'@i", $page, $this->captcha)) $this->captcha = false;
		$this->captcha = $this->captcha[1];

		$this->chkcaptcha($this->_url);
	}

	private function FreeDL($link) {
		$this->GetPage($link, $this->cookie, 0, $link);
		$page = $this->GetPage($link, $this->cookie, 0, $this->_url);

		if (!preg_match('@href="(http://i\d+.ifile.it/'.$this->_ukey.'[^"]+)"@i', $page, $dllink)) html_error("Error: Download-link not found.");
		$filename = parse_url($dllink[1]);$filename = urldecode(basename($filename["path"]));
		return $this->RedirectDownload($dllink[1], $filename, $this->cookie);
	}

	private function Get_Reply($page) {
		// First time using json_decode in plugins. :)
		$json = substr($page, strpos($page,"\r\n\r\n") + 4);
		$json = substr($json, strpos($json, "{"));$json = substr($json, 0, strrpos($json, "}") + 1);
		$rply = json_decode($json, true);
		if (!$rply || count($rply) == 0) html_error("Error getting json data.");
		return $rply;
	}

	private function chkcaptcha($link) {
		if ($_POST["skip"] == "true") {
			if (empty($_POST['captcha'])) html_error("You didn't enter the image verification code.");
			$post = array();
			$post['ctype'] = 'recaptcha';
			$post['recaptcha_response'] = $_POST['captcha'];
			$post['recaptcha_challenge'] = $_POST['challenge'];
			$this->_url = urldecode($_POST['_link']);
			$this->dlreq = urldecode($_POST['_dlreq']);
			$this->cookie = urldecode($_POST['cookie']);

			$page = $this->GetPage($this->dlreq, $this->cookie, $post);
			$rply = $this->Get_Reply($page);

			if ($rply['captcha'] == 0) $this->FreeDL($link);
			else {
				if ($rply['retry'] == 1) html_error("Error: Wrong Captcha Entered.");
				html_error("Error Sending Captcha.");
			}
		} else {
			$page = $this->GetPage($this->dlreq, $this->cookie);
			$rply = $this->Get_Reply($page);

			if ($rply['status'] == 'ok') {
				if ($rply['captcha'] == 0) {
					$this->FreeDL($link);
				} else {
					if (!$this->captcha || empty($this->captcha)) html_error("Error: Captcha not found.");
					$data = $this->DefaultParamArr($link, CookiesToStr($this->cookie));
					$data['_link'] = urlencode($this->_url);
					$data['_dlreq'] = urlencode($this->dlreq);
					$data['skip'] = 'true';
					$this->DL_reCaptcha($this->captcha, $data);
				}
			} else html_error("Error getting download data ('{$rply['status']}' => '{$rply['message']}').");
		}
		return false;
	}

	private function DL_reCaptcha($pid, $data) {
		$page = $this->GetPage("http://www.google.com/recaptcha/api/challenge?k=" . $pid);
		if (!preg_match('/challenge \: \'([^\']+)/i', $page, $ch)) html_error("Error getting CAPTCHA data.");
		$challenge = $ch[1];

		$data['challenge'] = $challenge;

		//Download captcha img.
		$page = $this->GetPage("http://www.google.com/recaptcha/api/image?c=" . $challenge);
		$capt_img = substr($page, strpos($page, "\r\n\r\n") + 4);
		$imgfile = DOWNLOAD_DIR . "ifile_it_captcha.jpg";

		if (file_exists($imgfile)) {
			unlink($imgfile);
		}
		if (! write_file($imgfile, $capt_img)) {
			html_error("Error getting CAPTCHA image.", 0);
		}

		$this->EnterCaptcha($imgfile, $data, 20);
		exit;
	}

	private function GetPageS($link, $cookie = 0, $post = 0, $referer = 0, $auth = 0) {
		if (!$referer) {
			global $Referer;
			$referer = $Referer;
		}
		$url = parse_url(trim($link));

		if ($this->usecurl && $url['scheme'] == 'https') $page = $this->cURL($link, $cookie, $post, $referer, base64_decode($auth));
		else $page = $this->GetPage($link, $cookie, $post, $referer, $auth);
		return $page;
	}

	private function cURL($link, $cookie = 0, $post = 0, $referer = 0, $auth = 0) {
		if (is_array($cookie)) $cookie = CookiesToStr($cookie);
		$opt = array(CURLOPT_HEADER => 1, CURLOPT_COOKIE => $cookie, CURLOPT_REFERER => $referer,
			CURLOPT_SSL_VERIFYPEER => 0, CURLOPT_SSL_VERIFYHOST => 0, CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_USERAGENT => "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.7.6) Gecko/20050317 Firefox/1.0.2");
		if ($post != '0') {
			$opt[CURLOPT_POST] = 1;
			$opt[CURLOPT_POSTFIELDS] = formpostdata($post);
		}
		if ($auth) {
			$opt[CURLOPT_HTTPAUTH] = CURLAUTH_BASIC;
			$opt[CURLOPT_USERPWD] = $auth;
		}
		$ch = curl_init($link);
		foreach ($opt as $O => $V) { // Using this instead of 'curl_setopt_array'
			curl_setopt($ch, $O, $V);
		}
		$page = curl_exec($ch);
		$errz = curl_errno($ch);
		$errz2 = curl_error($ch);
		curl_close($ch);

		if ($errz != 0) html_error("IF:[cURL:$errz] $errz2");
		return $page;
	}

	private function Login($link) {
		global $premium_acc;

		// Ping api
		$page = $this->GetPage('http://ifile.it/api-ping.api');
		is_notpresent($page, '"message":"pong"', "Error: ifile.it api is down?.");

		if (empty($premium_acc["ifile_it"]["apikey"])) {
			if (!empty($_REQUEST["premium_user"]) && !empty($_REQUEST["premium_pass"])) $pA = true;
			else $pA = false;
			$user = ($pA ? $_REQUEST["premium_user"] : $premium_acc["ifile_it"]["user"]);
			$pass = ($pA ? $_REQUEST["premium_pass"] : $premium_acc["ifile_it"]["pass"]);
			if (empty($user) || empty($pass)) html_error("Login Failed: Username or Password are empty. Please check login data.");

			$post = array();
			$post["username"] = urlencode($user);
			$post["password"] = urlencode($pass);
			$page = $this->GetPageS('https://secure.ifile.it/api-fetch_apikey.api', 0, $post);
			$rply = $this->Get_Reply($page);

			if ($rply['status'] != 'ok') html_error("Login Failed: ".htmlentities($rply['message']));
			if (empty($rply['akey'])) html_error("Login Failed: Akey not found.");
		} else {
			$rply = array();
			$rply['akey'] = $premium_acc["ifile_it"]["apikey"];
		}

		$this->cookie = array();
		$this->cookie['if_akey'] = $rply['akey'];

		$page = $this->GetPage('http://ifile.it/api-fetch_account_info.api', 0, array('akey'=>$rply['akey']));
		$rply = $this->Get_Reply($page);

		if ($rply['status'] != 'ok') html_error("Cannot check acc status: ".htmlentities($rply['message']));
		if (empty($rply['user_group']))  html_error("Cannot check acc type");

		// If isn't a normal account, should be premium/tester/vip...
		if ($rply['user_group'] != 'normal') return $this->PremiumDL($link);
		else {
			$this->changeMesg(lang(300)."<br /><b>Account isn\\\'t premium</b><br />Using it as member.");
			return $this->Prepare($link);
		}
	}

	private function PremiumDL($link) {
		$page = $this->GetPage($link);
		is_present($page, "ifile.it/?error=", "File not Found or Deleted");
		if (!preg_match("@var[\s|\t]+__ukey[\s|\t]*=[\s|\t]*'([^']+)'@i", $page, $ukey)) html_error("Error: FileID not found.");

		$page = $this->GetPage('http://ifile.it/api-fetch_download_url.api', 0, array('akey' => $this->cookie['if_akey'], 'ukey' => $ukey[1]));
		$rply = $this->Get_Reply($page);

		if ($rply['status'] != 'ok') html_error("Error getting premium dlink: ".htmlentities($rply['message']));
		if (empty($rply['download_url'])) html_error("Error getting premium dlink... Empty?");

		$filename = parse_url($rply['download_url']);$filename = urldecode(basename($filename["path"]));
		return $this->RedirectDownload($rply['download_url'], $filename);
	}
}

//[16-Oct-2011] Written by Th3-822.
//[21-Nov-2011] Captcha now doesn't allow hotlinking, fixed. - Th3-822
//[09-Dec-2011] Added premium acc support (non tested) & apikey support. - Th3-822

?>