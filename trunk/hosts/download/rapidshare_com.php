<?php

if (!defined('RAPIDLEECH')) {
	require_once ("index.html");
	exit();
}

class rapidshare_com extends DownloadClass {
	public function Download($link) {
		global $premium_acc;
		// Check for https support.
		$this->usecurl = $cantuse = false;
		if (!extension_loaded('openssl')) {
			if (extension_loaded('curl')) {
				$cV = curl_version();
				if (!in_array('https', $cV['protocols'], true)) $cantuse = true;
				$this->usecurl = true;
			} else $cantuse = true;
			if ($cantuse) html_error("Need OpenSSL enabled or cURL (with SSL) to use this plugin.");
		}

		$link = str_replace('http://', 'https://', $link);
		$URl = parse_url(trim($link));
		if (preg_match("/!download\|([^\|]+)\|(\d+)\|([^\|]+)/i", $URl["fragment"], $m)) $link = "https://rapidshare.com/files/{$m[2]}/{$m[3]}";
		$page = $this->page = $this->GetPageS($link);

		is_present($page, "ERROR: Filename invalid.", "Filename invalid. Please check the download link.");
		is_present($page, "ERROR: File ID invalid.", "File ID invalid. Please check the download link.");
		is_present($page, "ERROR: Unassigned file limit of 10 downloads reached.", "Unassigned file limit of 10 downloads reached.");

		if ($link = $this->ReLocation($page, 0)) {
			$page = $this->page = $this->GetPageS($link);
		}
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

		$this->Check_Limit($page);
		unset($page);

		if (($_REQUEST["cookieuse"] == "on" && preg_match("/enc\s?=\s?(\w+)/i", $_REQUEST["cookie"], $c)) || ($_REQUEST["premium_acc"] == "on" && $premium_acc["rapidshare_com"]["cookie"])) {
			$cookie = (empty($c[1]) ? $premium_acc["rapidshare_com"]["cookie"] : $c[1]);
			$this->lastmesg = lang(300)."<br />RS Premium Download [Cookie]";
			$this->changeMesg($this->lastmesg);
			return $this->PremiumCookieDownload($cookie);
		}elseif (($_REQUEST["premium_acc"] == "on" && $_REQUEST["premium_user"] && $_REQUEST["premium_pass"]) ||
			($_REQUEST["premium_acc"] == "on" && ($premium_acc["rapidshare_com"]['user'] || is_array($premium_acc["rapidshare_com"][0])))) {
			$this->lastmesg = lang(300)."<br />RS Premium Download";
			$this->changeMesg($this->lastmesg);
			return $this->DownloadPremium();
		} else {
			$this->lastmesg = lang(300).'<br />RS Free Download';
			$this->changeMesg($this->lastmesg);
			return $this->DownloadFree();
		}
	}
	private function DownloadFree() {
		$page = $this->page;
		is_present($page, "ERROR: You need RapidPro to download more files from your IP address.",
			"Too many parallel downloads from your IP address.");
		is_present($page, "ERROR: This file is too big to download it for free.",
			"This file is too big to download it for free.");
		is_present($page, "ERROR: Please stop flooding our download servers.",
			"Flood: Please try again in 5 minutes or later.");
		is_present($page, "ERROR: Too many users downloading",
			"Too many users downloading right now. Please try again later.");
		is_present($page, "ERROR: All free download slots are full.",
			"All free download slots are full. Please try again later.");

		if (!stristr($page, "ERROR: You need to wait ")) {
			$page = $this->GetPageS("https://api.rapidshare.com/cgi-bin/rsapi.cgi?sub=download&fileid={$this->fileid}&filename={$this->filename}&try=1");
			$this->Check_Limit($page);
		}

		is_present($page, "ERROR: Please stop flooding our download servers.",
			"Flood: Please try again in 2 minutes or later.");
		is_present($page, "ERROR: Too many users downloading",
			"Too many users downloading right now. Please try again later.");
		is_present($page, "ERROR: All free download slots are full.",
			"All free download slots are full. Please try again later.");

		if (stristr($page, "ERROR: You need to wait ")) {
			$seconds = trim(cut_str($page, "ERROR: You need to wait ", " seconds until"));
			if ($seconds) {
				echo ('<script type="text/javascript">');
				echo ('wait_time = ' . ($seconds + 1) . ';');
				echo ('function waitLoop() {');
				echo ('if (wait_time == 0) {');
				echo ('location.reload();');
				echo ('}');
				echo ('wait_time = wait_time - 1;');
				echo ('document.getElementById("waitTime").innerHTML = wait_time;');
				echo ('setTimeout("waitLoop()",1000);');
				echo ('}');
				echo ('</script>');
				echo '<br /><img src="http://images3.rapidshare.com/img/waitingdude.png" alt="" /><br /><br />'; // Foto fea. XD
				html_error("Download limit exceeded. You have to wait <font color=black><span id='waitTime'>$seconds</span></font> second(s) until the next download.<script>waitLoop();</script>");
			}
		}

		$data = substr(strrchr($page, "\n"), 1);
		$data = explode(":", $data);
		if ($data[0] == "DL") {
			$details = explode(",", $data[1]);
			$host = $details[0];
			$dlauth = $details[1];
			$countdown = $details[2];

			$this->CountDown($countdown);
			$link = "http://" . $host . "/cgi-bin/rsapi.cgi?sub=download&editparentlocation=0&bin=1&fileid=" . $this->fileid .
				"&filename=" . urlencode($this->filename) . "&dlauth=$dlauth";
			$this->RedirectDownload($link, $this->filename);
		} else {
			html_error("Download link not found.");
		}
	}
	private function DownloadPremium() {
		global $premium_acc;

		if (isset($premium_acc["rapidshare_com"]['user']) || ($_REQUEST["premium_user"] && $_REQUEST['premium_pass'])) {
			$auth = $_REQUEST["premium_user"] ? base64_encode($_REQUEST["premium_user"] . ":" . $_REQUEST["premium_pass"]) :
				base64_encode($premium_acc["rapidshare_com"]["user"] . ":" . $premium_acc["rapidshare_com"]["pass"]);

			$page = $this->GetPageS("https://rapidshare.com/files/{$this->fileid}/{$this->filename}", 0, 0, 0, $auth);

			is_present($page, "ERROR: Login failed. Password incorrect.",
				"Login failed. User/Password incorrect.");
			is_present($page, "ERROR: Login failed. Password incorrect or account not found.",
				"Login failed. User/Password incorrect or could not be found.");
			is_present($page, "ERROR: Login failed. Account not validated.",
				"Login failed. Account not validated.");
			is_present($page, "ERROR: Login failed. Account locked.",
				"Login failed. Your account has been locked.");
			//is_present($page, "ERROR: No traffic left.", "You don't have enough traffic to download this file.");
			is_present($page, "ERROR: RapidPro expired.", "RapidPro has expired or is inactive.");

			$this->Check_Limit($page);

			if (stristr($page, "Location:")) {
				$Href = $this->ReLocation($page);
				$sendauth = ($_REQUEST["premium_user"] && $_REQUEST["premium_pass"]) ? encrypt($auth) : 1;
				$this->RedirectDownload($Href, $this->filename, 0, 0, 0, 0, $sendauth);
			} else {
				html_error("Cannot use premium account", 0);
			}
		} else {
			$totalpremium = count($premium_acc["rapidshare_com"]);
			$success = 0;
			for ($i = 0; $i < $totalpremium; $i++) {
				$acc = $premium_acc["rapidshare_com"][$i]['user'];
				$pass = $premium_acc["rapidshare_com"][$i]['pass'];
				$auth = base64_encode($acc . ":" . $pass);
				$page = $this->GetPageS("https://rapidshare.com/files/{$this->fileid}/{$this->filename}", 0, 0, 0, $auth);

				if (stristr($page, "ERROR: Login failed.")) continue;
				//if (stristr($page, "ERROR: No traffic left.")) continue;
				if (stristr($page, "ERROR: RapidPro expired.")) continue;

				$this->Check_Limit($page);

				if (stristr($page, "Location:")) {
					$Href = $this->ReLocation($page);
					$success = 1;
					$this->RedirectDownload($Href, $this->filename, 0, 0, 0, 0, encrypt($auth));
					break;
				}
			}
			if (!$success) {
				html_error("No usable premium account", 0);
			}
		}
	}
	private function PremiumCookieDownload($cookie) {
		$this->ChkAccInfo($cookie);
		$cookie = "enc=$cookie;";

		$page = $this->GetPageS("https://rapidshare.com/files/{$this->fileid}/{$this->filename}", $cookie);
		$this->Check_Limit($page);
		//is_present($page, "ERROR: No traffic left.", "You don't have enough traffic to download this file.");
		if (stristr($page, "Location:")) {
			$Href = $this->ReLocation($page);
			$this->RedirectDownload($Href, $this->filename, $cookie);
		} else {
			html_error("Cannot use premium account");
		}
	}
	private function Check_Limit($page, $ret = false) {
		list($header, $page) = explode("\r\n\r\n", $page, 2);
		// X-APICPU: 0/10000
		if (preg_match("/X-APICPU: (\d+)\/(\d+)/i", $header, $x)) {
			$api_used = $x[1];
			$api_remaining = $x[2];
			$api_min = 1000;
			if (($api_remaining - $api_used) <= $api_min) {
				$this->changeMesg('Warning: Too much Rapidshare APICPU usage.');
				html_error("RS-API Limit reached. Wait 5 minutes or more and try again.");
			}
		} else {
			html_error("Cannot check RS-API Limit.");
		}

		if ($ret != false) {
			return $page;
		}
	}
	private function ChkAccInfo($cookie) {
		$page = $this->Check_Limit($this->GetPageS("https://api.rapidshare.com/cgi-bin/rsapi.cgi?sub=getaccountdetails&cookie=" . $cookie), 1);

		is_present($page, "ERROR: Login failed. Login data invalid.",
			"[Cookie] Invalid cookie.");
		is_present($page, "ERROR: Login failed. Password incorrect or account not found.",
			"[Cookie] Login failed. User/Password incorrect or could not be found.");
		is_present($page, "ERROR: Login failed. Account not validated.",
			"[Cookie] Login failed. Account not validated.");
		is_present($page, "ERROR: Login failed. Account locked.",
			"[Cookie] Login failed. Account locked.");
		is_present($page, "ERROR: Login failed.",
			"[Cookie] Login failed. Invalid cookie?");

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
			html_error("[Cookie] RapidPro has expired or is inactive.");
		} elseif ($info['directstart'] == 0) {
			$dd = $this->GetPageS("https://api.rapidshare.com/cgi-bin/rsapi.cgi?cookie=" . $cookie . "&sub=setaccountdetails&directstart=1");
			if (substr(strrchr($dd, "\n"), 1) != 'OK') {
				html_error("[Cookie] Error enabling direct downloads. Please do it manually.");
			}
			$this->changeMesg($this->lastmesg.'<br />Direct downloads has been enabled in your account');
		}
	}
	private function ReLocation($page, $stop=1) {
		if (!preg_match('@Location: https?://((\w+\.)?rapidshare.com/[^\r|\n]+)@i', $page, $rloc)) {
			if ($stop) html_error("Redirection not found.");
			else return false;
		}
		return "https://" . $rloc[1];
	}
	private function GetPageS($link, $cookie = 0, $post = 0, $referer = 0, $auth = 0) {
		if (!$referer) {
			global $Referer;
			$referer = $Referer;
		}
		$url = parse_url(trim($link));

		if ($this->usecurl && strstr($this->lastmesg, "RS Premium")) html_error("Need OpenSSL enabled for premium download.");
		if ($this->usecurl) $page = $this->cURL($link, $cookie, $post, $referer, base64_decode($auth));
		else $page = $this->GetPage($link, $cookie, $post, $referer, $auth);
		return $page;
	}
	private function cURL($link, $cookie = 0, $post = 0, $referer = 0, $auth = 0) {
		$ch = curl_init($link);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_COOKIE, $cookie);
		curl_setopt($ch, CURLOPT_REFERER, $referer);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.7.6) Gecko/20050317 Firefox/1.0.2");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		if($post != '0') {
			$POST = "";
			foreach ($post as $k => $v) {
				$POST .= "$k=$v&";
			}
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, substr($POST, 0, -1));
		}
		if ($auth) {
			curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
			curl_setopt($ch, CURLOPT_USERPWD, $auth);
		}
		$page = curl_exec($ch);
		$errz = curl_errno($ch);
		$errz2 = curl_error($ch);
		curl_close($ch);
		if ($errz != 0) {
			html_error("[cURL:$errz] ".curl_error($ch));
		}
		return $page;
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
//[21.APR-11]  Fixed $post in cURL function. (Oops, but isn't used by the plugin)... - Th3-822

?>