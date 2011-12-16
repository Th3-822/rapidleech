<?php

if (!defined('RAPIDLEECH')) {
	require_once ("index.html");
	exit;
}

class megaupload_com extends DownloadClass {
	private $link, $cookie;
	public function Download($link) {
		global $premium_acc, $mu_cookie_user_value;
		$this->link = $link;
		$this->cookie = array('l'=>'en');

		//Check for folder
		$this->MuFolderToAuDl();

		if ($_REQUEST["mu_acc"] == "on" && (!empty($_REQUEST["mu_cookie"]) || !empty($mu_cookie_user_value))) $this->Login();
		elseif ($_REQUEST["premium_acc"] == "on" && ((!empty($_REQUEST["premium_user"]) && !empty($_REQUEST["premium_pass"])) || ($premium_acc["megaupload_com"]["user"] && $premium_acc["megaupload_com"]["pass"]) || !empty($premium_acc["megaupload_com"]['cookie']))) $this->Login();
		else $this->FreeDL();
	}

	private function MuFolderToAuDl() {
		$url = parse_url(trim($this->link));
		if (preg_match ("@f=(\w{8})@", $url["query"], $matches)) {
			$page = $this->GetPage('http://www.megaupload.com/xml/folderfiles.php?folderid=' . $matches[1]);
			if (!preg_match_all( '@url="(http://[^"]+)"@', $page, $links)) html_error('Folder Error: Links not found');
			$this->moveToAutoDownloader($links[1]);
		}
	}

	private function FreeDL() {
		$post = array();
		//Get password
		$arr = explode("|", $this->link, 2);
		if (count($arr) == 2) {
			$this->link = $arr[0];
			$post["filepassword"] = urlencode($arr[1]);
		}

		$page = $this->GetPage($this->link, $this->cookie, $post);
		is_present($page, "The file you are trying to access is temporarily unavailable");
		is_present($page, 'class="na_description"', "Link Not Available.");
		is_present($page, 'class="download_l_descr"', 'Only premium users can download files larger than 1 GB.');

		if (stristr($page, 'The file you are trying to download is password protected')) {
			if (!empty($post["filepassword"])) html_error("Error: Link Password is incorrect.");
			else html_error("Error: Link is password protected. Input link with password as: LINK|PASSWORD.");
		}

		if (!preg_match('@(http://www\d+\.megaupload.com/files/[^"]+)"\s+class="download_regular_usual"@i', $page, $dlink)) html_error("Error: Download link not found");

		$CD = array();
		$Ch = '\s*(?:\r?\n[^\r|\n]+\r?\n)?\s*'; // rapidleech should decode html chunked content... (Or send request with HTTP 1.0 header)
		if (!preg_match("@count$Ch=$Ch(\d+)\s*;@i", $page, $CD)) {
			$this->changeMesg(lang(300)."<br /><br /><b>Countdown not found</b><br />Setting countdown to 60.");
			$CD[1] = 60;
		}
		$this->CountDown($CD[1]);

		$url = parse_url(html_entity_decode($dlink[1]));
		$FileName = urldecode(basename($url["path"]));
		$this->RedirectDownload($dlink[1], $FileName, $this->cookie);
	}

	private function Login() {
		global $premium_acc, $mu_cookie_user_value;
		$cookie = false;
		if ($_REQUEST["mu_acc"] == "on") {
			if (!empty($_REQUEST["mu_cookie"])) $cookie = $_REQUEST["mu_cookie"];
			elseif (!empty($mu_cookie_user_value)) $cookie = $mu_cookie_user_value;
		}elseif (!empty($premium_acc["megaupload_com"]['cookie'])) $cookie = $premium_acc["megaupload_com"]['cookie'];

		if (!$cookie) {
			if (!empty($_REQUEST["premium_user"]) && !empty($_REQUEST["premium_pass"])) $pA = true;
			else $pA = false;
			$user = ($pA ? $_REQUEST["premium_user"] : $premium_acc["megaupload_com"]["user"]);
			$pass = ($pA ? $_REQUEST["premium_pass"] : $premium_acc["megaupload_com"]["pass"]);
			if (empty($user) || empty($pass)) html_error("Login Failed: Username or Password are empty. Please check login data.");

			$post = array('login'=>1,'redir'=>1);
			$post["username"] = $user;
			$post["password"] = $pass;

			$page = $this->GetPage('http://www.megaupload.com/?c=login', $this->cookie, $post, 'http://www.megaupload.com/');
			is_present($page, 'Username and password do not match', 'Login Failed: Invalid username and/or password.');
			is_notpresent($page, 'Set-Cookie: user=', 'Login Failed: Cannot get cookie.');
			$this->cookie = array_merge($this->cookie, GetCookiesArr($page));
		} else $this->cookie['user'] = $cookie;

		$page = $this->GetPage('http://www.megaupload.com/?c=account', $this->cookie, 0, 'http://www.megaupload.com/');
		is_present($page, 'class="log_main_bl"', 'Login Failed: Invalid cookie.');
		if (!stristr($page, 'class="account_txt">Premium') && !stristr($page, 'class="account_txt">Lifetime Platinum')) {
			// class="account_txt">Regular
			html_error("Login Failed: Account isn't premium"); // I don't get less wait time with free account... So, show a html_error().
			//$this->changeMesg(lang(300)."<br /><br /><b>Account isn\\\'t premium</b><br />Using Free Download.");
			//return $this->FreeDL();
		}
		return $this->PremiumDL();
	}

	private function PremiumDL() {
		$post = array();
		//Get password
		$arr = explode("|", $this->link, 2);
		if (count($arr) == 2) {
			$this->link = $arr[0];
			$post["filepassword"] = urlencode($arr[1]);
		}

		$page = $this->GetPage($this->link, $this->cookie, $post);
		is_present($page, "The file you are trying to access is temporarily unavailable");
		is_present($page, 'class="na_description"', "Link Not Available.");

		if (stristr($page, 'The file you are trying to download is password protected')) {
			if (!empty($post["filepassword"])) html_error("Error: Link Password is incorrect.");
			else html_error("Error: Link is password protected. Input link with password as: LINK|PASSWORD.");
		}

		if (stristr($page, "Location: ")) {
			if (!preg_match("@Location: (http://www\d+\.megaupload.com/files/[^\r|\n]+)@i", $page, $dlink)) {
				html_error("Error: Direct Link Not Found");
			}
		} elseif (!preg_match('@(http://www\d+\.megaupload.com/files/[^"]+)"\s+class="download_premium_but"@i', $page, $dlink)) {
			html_error("Error: Download-Link Not Found");
		}

		$url = parse_url(html_entity_decode($dlink[1]));
		$FileName = urldecode(basename($url["path"]));
		$this->RedirectDownload($dlink[1], $FileName, $this->cookie);
	}
}

//[10-Dec-2011]  Rewritten (from the older svn plugin) and fixed for the changes @ MU site. - Th3-822
//[11-Dec-2011]  Fixed countdown code... Using a regexp for get the CD time, and setting a default value if regexp doesn't match. - Th3-822

?>