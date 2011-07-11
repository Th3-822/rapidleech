<?php
if (!defined('RAPIDLEECH')) {
	require_once ("index.html");
	exit();
}

class megashare_com extends DownloadClass {
	public function Download($link) {
		global $premium_acc;

		if (($_REQUEST["premium_acc"] == "on" && $_REQUEST["premium_user"] && $_REQUEST["premium_pass"]) ||
			($_REQUEST["premium_acc"] == "on" && $premium_acc["megashare_com"]["user"] && $premium_acc["megashare_com"]["pass"])) {
			return $this->Download_Premium($link);
		} elseif ($_POST['step'] == 1) {
			return $this->Send_Captcha($link);
		} else {
			return $this->Prepare_Free($link);
		}
	}

	private function Prepare_Free($link) {
		$page = $this->GetPage($link);
		if (preg_match('#Location: (http://(\w+\.)megashare\.com/[^\r|\n]+)#i', $page, $RD)) {
			$link = $RD[1];
			$page = $this->GetPage($link);
		}
		$cookie = GetCookies($page);

		if (!preg_match('@<input type="hidden" name="([^"]+)" value="([^"]+)">@i', $page, $HV) || !preg_match('@src="images/dwn-btn3\.gif" name="(?#Hate chunked reqs)(?:\r?\n[^\r|\n]+\r?\n)?([^"]+)" class="textfield" value="([^"]+)">@i', $page, $SP)) html_error("Error getting POST data.");

		$post = array($HV[1] => $HV[2], $SP[1] => $SP[2], $SP[1].'.x' => 1, $SP[1].'.y' => 1);
		$page = $this->GetPage($link, $cookie, $post);
		is_present($page, "This File has been DELETED", "This file was deleted.");
		is_present($page, "This File is Password Protected", "This File is Password Protected.");

		$form = cut_str($page, 'name="downloader">', "</form>");
		if (!preg_match_all('@<input type="hidden" name="([^"]+)" value="(?#Still hating chunked reqs)(?:\r?\n[^\r|\n]+\r?\n)?([^"]+)?"(?:\s?/?)>@i', $page, $hI)) html_error("Error getting POST data 2.");

		$post = array();
		$hI = array_combine($hI[1], $hI[2]);
		foreach ($hI as $k => $v) {
			$post[$k] = ($v=="") ? 1 : $v;
		}

		if (!preg_match('/var c = (\d+);/i', $page, $cD)) html_error("Error getting timer.");
		$this->CountDown($cD[1]);

		$page = $this->GetPage($link, $cookie, $post);

		$form = cut_str($page, 'name="downloader">', "</form>");
		if (!preg_match_all('@<input type="hidden" name="([^"]+)" value="(?#Still hating chunked reqs)(?:\r?\n[^\r|\n]+\r?\n)?([^"]+)?"(?:\s?/?)>@i', $page, $hI)) html_error("Error getting CAPTCHA/POST data.");

		if (!preg_match('@src="images/get-direct-link-btn\.png" name="([^"]+)" value="([^"]+)"@i', $page, $dI)) html_error("Error getting POST data 3.");
		$post = array($dI[1].'.x' => 1, $dI[1].'.y' => 1);
		$hI = array_combine($hI[1], $hI[2]);
		foreach ($hI as $k => $v) {
			$post[$k] = $v;
		}

		$usecap = (stristr($page, "is_captcha=1")) ? true : false;
		if ($usecap) {
			$POST = '';
			foreach ($post as $k => $v) {
				$POST .= "$k=$v&";
			}
			$data['post'] = urlencode(substr($POST, 0, -1));
			$data['step'] = 1;
			$data['link'] = urlencode($link);
			$data['referer'] = urlencode($Referer);
			$data['cookie'] = urlencode($cookie);

			//Download captcha img.
			$page = $this->GetPage("http://www.megashare.com/".cut_str($form, 'id="1zcimg" src="', '"'), $cookie);
			$capt_img = substr($page, strpos($page, "\r\n\r\n") + 4);
			$imgfile = DOWNLOAD_DIR . "megashare_captcha.jpg";

			if (file_exists($imgfile)) {
				unlink($imgfile);
			}
			if (! write_file($imgfile, $capt_img)) {
				html_error("Error getting CAPTCHA image.", 0);
			}
			$this->EnterCaptcha("$imgfile?".time(), $data);
			exit;
		} else {
			return $this->Download_Free($this->GetPage($link, $cookie, $post), $cookie);
		}
	}

	private function Send_Captcha($link) {
		if (empty($_POST['captcha'])) {
			html_error("You didn't enter the image verification code.");
		}
		$post = "captcha_code={$_POST['captcha']}&email=&".urldecode($_POST['post']);
		$arr1 = explode("&", $post);
		$post = array();
		foreach ($arr1 as $key => $val) {
			$arr2 = explode("=", $val);
			foreach ($arr2 as $key2 => $val2) {
				$arr3[] = $val2;
			}
		}
		for ($i = 0; $i <= count($arr3); $i += 2) {
			if (array_key_exists($i, $arr3)) {
				if ($arr3[$i] != "") {
					$post[trim($arr3[$i])] = trim($arr3[$i + 1]);
				}
			}
		}
		$cookie = urldecode($_POST['cookie']);

		$page = $this->GetPage($link, $cookie, $post);
		is_present($page, "Location: mslink.html", "Error checking CAPTCHA.");
		is_present($page, "Invalid Captcha Value", "Entered CAPTCHA was incorrect.");

		$this->Download_Free($page, $cookie);
	}
	private function Download_Free($page, $cookie) {
		if (!preg_match('@(http://(www\.)?megashare\.com/dnd/\d+/[^/]+/[^"|\']+)("|\')@i', $page, $D)) html_error("Download-link not found.");
		$dllink = $D[1];

		$filename = parse_url($dllink);
		$filename = basename($filename["path"]);

		$this->RedirectDownload($dllink, $filename, $cookie);
	}

	private function Download_Premium($link) {
		$cookie = $this->login();

		$page = $this->GetPage($link, $cookie);
		if (preg_match('#Location: (http://(\w+\.)megashare\.com/[^\r|\n]+)#i', $page, $RD)) {
			$link = $RD[1];
			$page = $this->GetPage($link);
		}
		$cookie = "$cookie; " . GetCookies($page);

		if (!preg_match('@<input type="hidden" name="([^"]+)" value="([^"]+)">@i', $page, $HV) || !preg_match('@src="images/download-btn\.gif" name="([^"]+)" class="textfield" value="([^"]+)">@i', $page, $SP)) html_error("[Premium] Error getting POST data.");

		$post = array($HV[1] => $HV[2], $SP[1] => $SP[2], $SP[1].'.x' => 1, $SP[1].'.y' => 1);
		$page = $this->GetPage($link, $cookie, $post);
		is_present($page, "This File has been DELETED.", "This file was deleted.");
		is_present($page, "This File is Password Protected.", "This File is Password Protected.");
		is_present($page, "is_captcha=1", "Error: Your account isn't premium?.");

		$form = cut_str($page, 'name="downloader">', "</form>");
		if (!preg_match_all('@<input type="hidden" name="([^"]+)" value="(?#Still hating chunked reqs)(?:\r?\n[^\r|\n]+\r?\n)?([^"]+)?"(?:\s?/?)>@i', $page, $hI)) html_error("[Premium] Error getting POST data 2.");

		if (!preg_match('@src="images/get-direct-link-btn.png" name="([^"]+)" value="([^"]+)"@i', $page, $dI)) html_error("[Premium] Error getting POST data 3.");
		$post = array($dI[1].'.x' => 1, $dI[1].'.y' => 1);
		$hI = array_combine($hI[1], $hI[2]);
		foreach ($hI as $k => $v) {
			$post[$k] = $v;
		}

		$page = $this->GetPage($link, $cookie, $post);

		if (!preg_match('@(http://(www\.)?megashare\.com/dnd/\d+/[^/]+/[^"|\']+)("|\')@i', $page, $D)) html_error("Download-link not found.");
		$dllink = $D[1];

		$filename = parse_url($dllink);
		$filename = basename($filename["path"]);

		$this->RedirectDownload($dllink, $filename, $cookie);
	}

	private function login() {
		global $premium_acc;
		$pA = ($_REQUEST["premium_user"] && $_REQUEST["premium_pass"] ? true : false);
		$email = ($pA ? $_REQUEST["premium_user"] : $premium_acc["megashare_com"]["user"]);
		$pass = ($pA ? $_REQUEST["premium_pass"] : $premium_acc["megashare_com"]["pass"]);

		if (empty($email) || empty($pass)) {
			html_error("Login Failed: Email or Password is empty. Please check login data.");
		}

		$postURL = "http://www.megashare.com/login.php";
		$post["yes"] = "!";
		$post["loginid"] = $email;
		$post["passwd"] = $pass;
		$page = $this->GetPage($postURL, 0, $post, $postURL);
		$cookie = GetCookies($page);

		is_present($page, "Invalid Username or Password", "Login Failed: Invalid Username or Password.");
		is_notpresent($cookie, "username=", "Login Failed. Cookie 'username' not found.");
		is_notpresent($cookie, "password=", "Login Failed. Cookie 'password' not found.");

		return $cookie;
	}
}

//[28-4-2011] Written by Th3-822.
//[09-7-2010] Fixed regexps. (No more chunked content.) - Th3-822

?>