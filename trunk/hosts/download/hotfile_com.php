<?php

if (!defined('RAPIDLEECH')) {
	require_once ("index.html");
	exit();
}

class hotfile_com extends DownloadClass {
	public function Download($link) {
		global $premium_acc;

		if (!$_GET["step"]) { // Check link
			if (preg_match("/hotfile\.com\/dl\/(\d+\/\w+)\/(.+)?/i", $link, $l)) {
				$link = "http://hotfile.com/dl/{$l[1]}/{$l[2]}";
			} else {
				html_error("Error: Malformed link?. Please check the download link.");
			}
			$page = $this->GetPage($link);
			is_present($page, "<td>This file is either removed due", "Error: This file is either removed due to copyright claim or is deleted by the uploader.");
			is_present($page, "<td>File is removed", "Error: File is removed due to copyright claim.");
			if (stristr($page, "\r\nContent-Length: 0\r\n")) {
				is_notpresent($page, "\r\nLocation:", "Error: Invalid link. Please check the download link.");
				// Check if file has enabled Hot/Direct linking.
				if (!preg_match("/(s\d+)\.hotfile\.com\/get\/(\w+\/\w+\/\w+\/\w+\/\w+)\/([^\r|\n]+)/i", $page, $l)) {
					html_error("Error: Invalid link?. Please check the download link.");
				}
				$dllink = "http://{$l[1]}.hotfile.com/get/{$l[2]}/{$l[3]}";

				$filename = parse_url($dllink);
				$filename = urldecode(basename($filename["path"]));
				return $this->RedirectDownload($dllink, $filename, GetCookies($page));
			}
			unset($page);
		}

		if (($_REQUEST["cookieuse"] == "on" && preg_match("/auth\s?=\s?(\w{64})/i", $_REQUEST["cookie"], $c)) || ($_REQUEST["premium_acc"] == "on" && $premium_acc["hotfile_com"]["cookie"])) {
			$cookie = (empty($c[1]) ? $premium_acc["hotfile_com"]["cookie"] : $c[1]);
			$this->DownloadPremium($link, $cookie);
		} elseif (($_REQUEST["premium_acc"] == "on" && $_REQUEST["premium_user"] && $_REQUEST["premium_pass"]) ||
			($_REQUEST["premium_acc"] == "on" && $premium_acc["hotfile_com"]["user"] && $premium_acc["hotfile_com"]["pass"])) {
			$this->DownloadPremium($link);
		} else {
			$this->DownloadFree($link);
		}
	}
	private function DownloadFree($link) {
		$page = $this->GetPage($link);
		if ($_GET["step"] != "1") {
			if (!preg_match_all('/timerend=d\.getTime\(\)\+(\d+)/i', $page, $t)) {
				html_error("Error getting timer.");
			}
			$t = $t[1];
			$hl = ($t[1] > 0 ? $t[1]/1000 : 0);

			if ($hl > 0) {
				$data = $this->DefaultParamArr($link);
				$data['step'] = '2';
				$this->JSCountdown($hl, $data, 'You reached your hourly traffic limit');
			} else {
				insert_timer(($t[0]/1000)+1, "Waiting captcha/link timelock:");
			}
			$post['action'] = cut_str($page, "action value=", ">");
			$post['tm'] = cut_str($page, "tm value=", ">");
			$post['tmhash'] = cut_str($page, "tmhash value=", ">");
			$post['wait'] = cut_str($page, "wait value=", ">");
			$post['waithash'] = cut_str($page, "waithash value=", ">");
			$post['upidhash'] = cut_str($page, "upidhash value=", ">");

			$page = $this->GetPage($link, 0, $post);
		}

		$lfound = (stristr($page, "hotfile.com/get/") ? true : false);
		$cfound = (stristr($page, "api.recaptcha.net/challenge?k=") ? true : false);
		if (!$lfound && !$cfound) {
			/* No captcha or link found?. Let's try resending the post */
			$post['action'] = cut_str($page, "action value=", ">");
			$post['tm'] = cut_str($page, "tm value=", ">");
			$post['tmhash'] = cut_str($page, "tmhash value=", ">");
			$post['wait'] = cut_str($page, "wait value=", ">");
			$post['waithash'] = cut_str($page, "waithash value=", ">");
			$post['upidhash'] = cut_str($page, "upidhash value=", ">");

			$page = $this->GetPage($link, 0, $post);

			$lfound = (stristr($page, "hotfile.com/get/") ? true : false);
			$cfound = (stristr($page, "api.recaptcha.net/challenge?k=") ? true : false);
		}

		if($_GET["step"] == "1" && !$lfound) {
			//Send captcha
			$post['action'] = $_POST['action'];
			$post['recaptcha_challenge_field'] = $_POST['challenge'];
			$post['recaptcha_response_field'] = $_POST['captcha'];
			$post['recaptcha_shortencode_field'] = $_POST['shortencode'];

			$page = $this->GetPage($link, 0, $post);
			is_present($page, "Wrong Code. Please try again.", "Error: Entered CAPTCHA was incorrect.");
			is_notpresent($page, 'hotfile.com/get/', 'Error: Download-link not found [2].');
		} elseif (!$lfound && $cfound) {
			//Get captcha
			$pid = cut_str($page, 'recaptcha.net/challenge?k=', '"');
			$page = $this->GetPage("http://www.google.com/recaptcha/api/challenge?k=" . $pid);
			if (preg_match('/challenge \: \'([^\']+)/i', $page, $ch)) {
				$challenge = $ch[1];
			} else {
				html_error("Error getting CAPTCHA data.");
			}

			$data = $this->DefaultParamArr($link);
			$data['challenge'] = $challenge;
			$data['shortencode'] = 'undefined';
			$data['action'] = 'checkcaptcha';
			$data['step'] = '1';

			//Download captcha img.
			$page = $this->GetPage("http://www.google.com/recaptcha/api/image?c=" . $challenge);
			$capt_img = substr($page, strpos($page, "\r\n\r\n") + 4);
			$imgfile = DOWNLOAD_DIR . "hotfile_captcha.jpg";

			if (file_exists($imgfile)) {
				unlink($imgfile);
			}
			if (! write_file($imgfile, $capt_img)) {
				html_error("Error getting CAPTCHA image.", 0);
			}

			$this->EnterCaptcha($imgfile, $data, 20);
			exit;
		} elseif (!$lfound) {
			html_error("Error getting CAPTCHA");
		}

		if (preg_match('/\/get\/(\d+\/\w+\/\w+)\/([^\'|\"]+)/i', $page, $dl)) {
			$cookie = GetCookies($page);
			$page = $this->GetPage("http://hotfile.com/get/{$dl[1]}/{$dl[2]}", $cookie);
		} else {
			html_error("Error: Download-link not found.");
		}

		is_notpresent($page, "\r\nLocation:", "Error: Direct link not found.");

		if (!preg_match("/(s\d+)\.hotfile\.com\/get\/(\w+\/\w+\/\w+\/\w+\/\w+)\/([^\r|\n]+)/i", $page, $l)) {
			is_present(cut_str($page, "\r\nLocation: ", "\r\n"), "?expire=1", "Error: Your download expired, try again.");
			html_error("Error: Direct link not found [2].");
		}
		$dllink = "http://{$l[1]}.hotfile.com/get/{$l[2]}/{$l[3]}";

		$filename = parse_url($dllink);
		$filename = urldecode(basename($filename["path"]));
		$this->RedirectDownload($dllink, $filename, $cookie);
	}

	private function DownloadPremium($link, $cookie = false) {
		$cookie = $this->login($cookie);
		$page = $this->GetPage($link, $cookie);

		if (stristr($page, "\r\nContent-Length: 0\r\n")) {
			is_notpresent($page, "\r\nLocation:", "Error: Direct link not found.");
		} elseif (preg_match('/\/get\/(\d+\/\w+\/\w+)\/([^\'|\"]+)/i', $page, $dl)) {
			$page = $this->GetPage("http://hotfile.com/get/{$dl[1]}/{$dl[2]}", $cookie);
		} else {
			is_notpresent($page, '<span>Premium</span>', "Error: The account isn't premium?.");
			html_error("Error: Download-link not found.");
		}
		$cookie = $cookie . "; " . GetCookies($page);

		if (!preg_match("/(s\d+)\.hotfile\.com\/get\/(\w+\/\w+\/\w+\/\w+\/\w+)\/([^\r|\n]+)/i", $page, $l)) {
			html_error("Error: Direct link not found [2].");
		}
		$dllink = "http://{$l[1]}.hotfile.com/get/{$l[2]}/{$l[3]}";

		$filename = parse_url($dllink);
		$filename = urldecode(basename($filename["path"]));
		$this->RedirectDownload($dllink, $filename, $cookie);
	}

	private function login($authc = false) {
		global $premium_acc;
		
		if (!$authc) {
			$user = ($_REQUEST["premium_user"] ? $_REQUEST["premium_user"] : $premium_acc["hotfile_com"]["user"]);
			$pass = ($_REQUEST["premium_pass"] ? $_REQUEST["premium_pass"] : $premium_acc["hotfile_com"]["pass"]);
			if (empty($user) || empty($pass)) {
				html_error("Login Failed: Username or Password is empty. Please check login data.");
			}

			$postURL = "http://hotfile.com/login.php";
			$post["returnto"] = "/";
			$post["user"] = $user;
			$post["pass"] = $pass;
			$page = $this->GetPage($postURL, 0, $post, 'http://hotfile.com/');
			$cookie = GetCookies($page);

			is_present($page, '/suspended.html', "Login failed: Your account has been suspended.");
			is_notpresent($page, "Location: /?cookiecheck=1", "Login failed: Username or Password is incorrect.");
			is_notpresent($cookie, "auth=", "Login Failed: Cannot get cookie.");
		} elseif (strlen($authc) == 64) {
			$cookie = "auth=" . $authc;
		} else {
			html_error("[Cookie] Invalid cookie (" . strlen($authc) . " != 64).");
		}

		$page = $this->GetPage("http://hotfile.com/?cookiecheck=1", $cookie);
		is_present($page, '<span>Free</span>', "Login Failed: The account isn't premium.");
		is_present($page, '/howtocookies.html', "Error: Login Failed.");
		is_present($page, '/login.php', "[Cookie] Invalid cookie.");

		return $cookie;
	}
}

//[06-Feb-2011]  Plugin rewritten & added cookie support by Th3-822.
//[13-Feb-2011]  Removed old code & Fixed captcha in free download. - Th3-822
//[15-May-2011]  Added 3 error msg; 1 edited & Fixed error in 'hfwait' form & Added 1 seg to free dl Timelock & Added code to try again if captcha isn't found. - Th3-822
//[11-Jul-2011]  Using a function for the countdown & Added code for use DefaultParamArr(). -Th3-822
?>