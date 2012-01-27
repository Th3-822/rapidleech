<?php

if (!defined('RAPIDLEECH')) {
	require_once ("index.html");
	exit();
}

class rapidshare_com extends DownloadClass {
	public $lastmesg, $apiurl;
	private $fileid, $filename;
	public function Download($link) {
		global $premium_acc;
		$this->apiurl = "https://api.rapidshare.com/cgi-bin/rsapi.cgi";

		$link = str_replace('http://', 'https://', $link);
		$URl = parse_url(trim($link));
		if (preg_match("/!download\|([^\|]+)\|(\d+)\|([^\|]+)/i", $URl["fragment"], $m)) $link = "https://rapidshare.com/files/{$m[2]}/{$m[3]}";
		$page = $this->GetPage($link);

		is_present($page, "ERROR: Filename invalid.", "Filename invalid. Please check the download link.");
		is_present($page, "ERROR: File ID invalid.", "File ID invalid. Please check the download link.");
		is_present($page, "ERROR: Unassigned file limit of 10 downloads reached.", "Unassigned file limit of 10 downloads reached.");
		is_present($page, "ERROR: Server under repair.", "Server under repair. Please try again later");

		if ($linkb = $this->ReLocation($page, 0)) $page = $this->GetPage($linkb);
		if (!preg_match("/!download\|([^\|]+)\|(\d+)\|([^\|]+)/i", $page, $m)) html_error("Cannot check link");
		$this->fileid = $m[2];
		$this->filename = $m[3];

		$rserrors = array("This file was not found on our server.",
			"The file was deleted by the owner or the administrators.",
			"The file was deleted due to our inactivity-rule (no downloads).",
			"The file is suspected to be contrary to our terms and conditions and has been locked up for clarification.",
			"The file has been removed from the server due of infringement of the copyright-laws.",
			"The file is corrupted or incomplete.");
		$errors = array("ERROR: File not found." => 0, "ERROR: File physically not found." => 0,
			"ERROR: File deleted R1." => 1, "ERROR: File deleted R2." => 1,
			"ERROR: File deleted R3." => 2, "ERROR: File deleted R5." => 2,
			"ERROR: File deleted R4." => 3, "ERROR: File deleted R8." => 3,
			"ERROR: File deleted R10." => 4, "ERROR: File deleted R11." => 4,
			"ERROR: File deleted R12." => 4, "ERROR: File deleted R13." => 4,
			"ERROR: File deleted R14." => 4, "ERROR: File deleted R15." => 4,
			"ERROR: This file is marked as illegal." => 4, // R10=Game;R11=Movie/Video;R12=Music;R13=Software;R14=Image;R15=Literature
			"ERROR: raid error on server." => 5, "ERROR: File incomplete." => 5);
		foreach ($errors as $err => $errn) {
			is_present($page, $err, $rserrors[$errn]);
		}
		unset($page);

		if (($_REQUEST["cookieuse"] == "on" && preg_match("/enc\s?=\s?(\w+)/i", $_REQUEST["cookie"], $c)) || ($_REQUEST["premium_acc"] == "on" && $premium_acc["rapidshare_com"]["cookie"])) {
			$cookie = (empty($c[1]) ? $premium_acc["rapidshare_com"]["cookie"] : $c[1]);
			$this->lastmesg = lang(300)."<br />RS Premium Download [Cookie]";
			$this->changeMesg($this->lastmesg);

			return $this->PremiumCookieDownload($cookie);
		}elseif ($_REQUEST["premium_acc"] == "on" && (($_REQUEST["premium_user"] && $_REQUEST["premium_pass"]) || ($premium_acc["rapidshare_com"]['user'] && $premium_acc["rapidshare_com"]['pass']))) {
			$this->lastmesg = lang(300)."<br />RS Premium Download";
			$this->changeMesg($this->lastmesg);

			return $this->DownloadPremium();
		} else {
			$this->lastmesg = lang(300).'<br />RS Free Download';
			$this->changeMesg($this->lastmesg);

			return $this->DownloadFree($link);
		}
	}
	private function DownloadFree($link) {
		$page = $this->GetPage($this->apiurl."?sub=download&fileid={$this->fileid}&filename={$this->filename}&try=1");

		is_present($page, "ERROR: This file is too big to download it for free.", "This file is too big to download it for free.");
		is_present($page, "ERROR: You need RapidPro to download more files from your IP address.", "Too many parallel downloads from your IP address.");
		is_present($page, "ERROR: Please stop flooding our download servers.", "Flood: Please try again in 5 minutes or later.");
		is_present($page, "ERROR: Too many users downloading", "Too many users downloading right now. Please try again later.");
		is_present($page, "ERROR: All free download slots are full.", "All free download slots are full. Please try again later.");

		if (stristr($page, "ERROR: You need to wait ")) {
			$seconds = trim(cut_str($page, "ERROR: You need to wait ", " seconds until"));
			return $this->JSCountdown($seconds,0,'Download limit exceeded');
		}

		$data = substr(strrchr($page, "\n"), 1);
		$data = explode(":", $data);
		if ($data[0] == "DL") {
			$details = explode(",", $data[1]);
			$host = $details[0];
			$dlauth = $details[1];
			$countdown = $details[2];

			$dllink = "http://$host/cgi-bin/rsapi.cgi?sub=download&editparentlocation=0&bin=1&fileid={$this->fileid}&filename={$this->filename}&dlauth=$dlauth";
			if ($countdown == 0) $this->RedirectDownload($dllink, $this->filename);
			else {
				$url = parse_url($dllink);

				$data = $this->DefaultParamArr($dllink);
				unset($data['audl']); // Can't use audl in free dl.
				$data['filename'] = urlencode($this->filename);
				$data['host'] = $url["host"];
				$data['port'] = $url["port"];
				$data['path'] = urlencode($url["path"] . ($url["query"] ? "?" . $url["query"] : ""));
				$data['saveto'] = $_GET["path"];
				$this->JSCountdown($countdown, $data);
			}
		} else {
			html_error("Download link not found.");
		}
	}
	private function DownloadPremium() {
		global $premium_acc;
		if (!extension_loaded('openssl')) html_error("Need OpenSSL enabled for premium download.");

		if (!empty($_REQUEST["premium_user"]) && !empty($_REQUEST["premium_pass"])) {
			$pA = true;
			$user = $_REQUEST["premium_user"];
			$pass = $_REQUEST["premium_pass"];
		} else {
			$user = $premium_acc["rapidshare_com"]["user"];
			$pass = $premium_acc["rapidshare_com"]["pass"];
		}
		$user = urlencode($user);
		$pass = urlencode($pass);

		$cookie = $this->ChkAccInfo('login', $user, $pass, $pA);
		$cookie = "enc=$cookie;";
		$sendauth = 1;
		if ($pA) $sendauth = 0;
		else $cookie = 0;

		$page = $this->GetPage("https://rapidshare.com/files/{$this->fileid}/{$this->filename}", $cookie, 0, 0, ($sendauth) ? base64_encode("$user:$pass") : '');
		if (!stristr($page, "Location:")) html_error("Cannot use premium account", 0);

		$Href = $this->ReLocation($page);
		$this->RedirectDownload($Href, $this->filename, $cookie, 0, 0, 0, $sendauth);
	}
	private function PremiumCookieDownload($cookie) {
		if (!extension_loaded('openssl')) html_error("Need OpenSSL enabled for premium download.");
		$this->ChkAccInfo($cookie);
		$cookie = "enc=$cookie;";

		$page = $this->GetPage("https://rapidshare.com/files/{$this->fileid}/{$this->filename}", $cookie);
		if (!stristr($page, "Location:")) html_error("Cannot use premium account", 0);

		$Href = $this->ReLocation($page);
		$this->RedirectDownload($Href, $this->filename, $cookie);
	}
	private function ChkAccInfo($cookie, $user='', $pass='', $pA=false) {
		if ($cookie != "login") {
			$page = $this->GetPage($this->apiurl."?sub=getaccountdetails&cookie=$cookie");
			$t1 = 'Cookie';$t2 = 'cookie';
		} elseif (!empty($user) && !empty($pass)) {
			$page = $this->GetPage($this->apiurl."?sub=getaccountdetails&withcookie=1&login=$user&password=$pass");
			$t1 = 'Error';$t2 = 'login details';
		} else html_error("Login failed. User/Password empty.");

		is_present($page, "ERROR: IP blocked.", "[ERROR] Rapidshare has locked your IP. (Too many failed logins sended)");
		is_present($page, "ERROR: Login failed. Login data invalid.",
			"[$t1] Invalid $t2.");
		is_present($page, "ERROR: Login failed. Password incorrect or account not found.",
			"[$t1] Login failed. User/Password incorrect or could not be found.");
		is_present($page, "ERROR: Login failed. Account not validated.",
			"[$t1] Login failed. Account not validated.");
		is_present($page, "ERROR: Login failed. Account locked.",
			"[$t1] Login failed. Account locked.");
		is_present($page, "ERROR: Login failed.",
			"[$t1] Login failed. Invalid $t2?");

		$page = substr($page, strpos($page, "\r\n\r\n") + 4);
		$arr1 = explode("\n", $page);
		$info = array();
		foreach ($arr1 as $key => $val) {
			$arr2 = explode("=", $val);
			foreach ($arr2 as $key2 => $val2) {
				$arr3[] = $val2;
			}
		}
		for ($i = 0; $i <= count($arr3); $i += 2) {
			if (array_key_exists($i, $arr3)) {
				if ($arr3[$i] != "") {
					$info[trim($arr3[$i])] = trim($arr3[$i + 1]);
				}
			}
		}

		if ($info['servertime'] >= $info['billeduntil']) {
			html_error("[$t1] RapidPro has expired or is inactive.");
		} elseif ($info['directstart'] == 0 && (!$user || $pA)) {
			if ($pA) $cookie = $info['cookie'];
			$dd = $this->GetPage($this->apiurl."?cookie=$cookie&sub=setaccountdetails&directstart=1");
			if (substr(strrchr($dd, "\n"), 1) != 'OK') {
				html_error("Error enabling direct downloads. Please do it manually.");
			}
			$this->changeMesg($this->lastmesg.'<br />Direct downloads has been enabled in your account');
		}
		if ($user) return $info['cookie'];
	}
	private function ReLocation($page, $stop=1) {
		if (!preg_match('@Location: https?://((\w+\.)?rapidshare\.com/[^\r|\n]+)@i', $page, $rloc)) {
			if ($stop) html_error("Redirection not found.");
			else return false;
		}
		return "https://" . $rloc[1];
	}
}
// updated by rajmalhotra  on 17 Dec 09 :  added some error messages
// Fixed by rajmalhotra  on 28 Dec 09
//updated 08-jun-2010 for standard auth system (szal)
//[07-OCT-10]  Free download rewritten/fixed by Th3-822
//[30-OCT-10]  Premium download fixed for new links/error msg support & Added 4 error msg to free download. -Th3-822
//[13-NOV-10]  Added error msg for "Account locked" in premium download && Fixed + Added 1 error msg to free download && Fixed regex for get link. -Th3-822
//[13-JAN-11]  & [22-JAN-11]  Added full support for premium cookie & Added function for check RS-API limits &  Minor change in 'ChkAccInfo'. -Th3-822
//[17-MAR-11]  Premium: Add var ($DisSSL) and code for Disable SSL downloads && Changed limit to 1000 & Err Msg in 'Check_Limit'. - Th3-822
//[18-MAR-11]  Premium: Now SSL downloads will be disabled if OpenSSL isn't loaded && Added 5 status msgs with changeMesg() :D - Th3-822
//[19-APR-11]  Plugin checked & fixed for work with new changes at RS && SSL support is needed (It will show error if OpenSSL isn't loaded)... Including for get data in Free download :( . - Th3-822
//[20-APR-11]  FreeDL: Added new functions for use https with cURL if OpenSSL isn't loaded. - Th3-822
//[21-APR-11]  Fixed $post in cURL function. (Oops, but isn't used by the plugin)... - Th3-822
//[29-MAY-11]  Premium: Removed support for multi RS logins (Isn't needed now) & changed the login process using 'ChkAccInfo'. Free: Changed countdown, added new function. And plugin checked. - Th3-822
//[01-JUN-11]  Premium: Fixed error in login. - Th3-822
//[10-JUL-11]  Check_Limit() function isn't needed now, removed & Thinking about delete the old fixes info (Too long for read. :D ). - Th3-822
//[15-OCT-11] JSCountdown was added in DownloadClass.php... Removed declaration from plugin && Some edits in free dl countdown. - Th3-822

?>