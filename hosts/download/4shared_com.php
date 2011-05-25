<?php

if (!defined('RAPIDLEECH')) {
	require_once ("index.html");
	exit();
}

class d4shared_com extends DownloadClass {
	private $cookie;
	public function Download($link) {
		global $premium_acc;

		if (stristr($link, ".com/get/")) {
			$link = str_replace('.com/get/', '.com/file/', $link);
		}
		$page = $this->GetPage($link, "4langcookie=en");
		$this->cookie = GetCookies($page) . "; 4langcookie=en"; //Keep page in english
		is_present($page, "The file link that you requested is not valid.");
		is_present($page, "The file is suspected of illegal or copyrighted content.");

		if ($_REQUEST["premium_acc"] == "on" && (($_REQUEST["premium_user"] && $_REQUEST["premium_pass"]) || ($premium_acc["4shared_com"]["user"] && $premium_acc["4shared_com"]["pass"]))) {
			return $this->PremiumDownload($link);
		} else {
			return $this->FreeDownload($page, $link);
		}
	}

	private function FreeDownload($page, $link) {
		$page = $this->CheckForPass($page, $link);

		preg_match('/.com\/[^\/]+\/([^\/]+)\/?(.*)/i', $link, $L);
		$page = $this->GetPage("http://www.4shared.com/get/{$L[1]}/{$L[2]}", $this->cookie);

		if (preg_match('/href=\'(http:\/\/dc[^\']+)\'>Download file now/i', $page, $D)) {
			$this->cookie = $this->cookie."; ".GetCookies($page);
			$dllink = $D[1];
		} else {
			html_error("Download-link not found.");
		}

		if (!preg_match('/var c = (\d+)/', $page, $count)) html_error("Timer not found.");
		$this->CountDown($count[1]);

		$url = parse_url($dllink);
		$FileName = basename($url["path"]);

		$this->RedirectDownload($dllink, $FileName, $this->cookie);
	}

	private function CheckForPass($page, $link, $predl=false, $pA=false) {
		global $Referer, $PHP_SELF;
		if ($_GET["step"] == "1") {
			$post = array();
			$post["userPass2"] = $_POST['userPass2'];
			$post["dsid"] = $_POST['dsid'];
			$page = $this->GetPage($link, $this->cookie, $post, $link);
			is_present($page, "Please enter a password to access this file", "The password you have entered is not valid.");
			$this->cookie = $this->cookie."; ".GetCookies($page);
			return $page;
		} elseif (stristr($page, 'Please enter a password to access this file')) {
			echo "\n" . '<center><form name="dl_password" action="' . $PHP_SELF . '" method="post" >' . "\n";
			echo '<input type="hidden" name="link" value="' . urlencode($link) . '" />' . "\n";
			echo '<input type="hidden" name="referer" value="' . urlencode($Referer) . '" />' . "\n";
			echo '<input type="hidden" name="step" value="1" />' . "\n";

			$defdata = array("comment" => $_GET ["comment"], "email" => $_GET ["email"], "partSize" => $_GET ["partSize"], "method" => $_GET ["method"], "proxy" => $_GET ["proxy"], "proxyuser" => $_GET ["proxyuser"], "proxypass" => $_GET ["proxypass"], "path" => $_GET ["path"]);
			foreach ($defdata as $name => $val) {
				echo "<input type='hidden' name='$name' id='$name' value='$val' />\n";
			}
			echo '<input type="hidden" name="dsid" value="' . trim(cut_str($page, 'name="dsid" value="', '"')) . '" />' . "\n";
			if ($predl) echo '<br /><input type="checkbox" name="premium_acc" id="premium_acc" onclick="javascript:var displ=this.checked?\'\':\'none\';document.getElementById(\'premiumblock\').style.display=displ;" '.(!$pA?'checked="checked"':'').' />&nbsp;'.lang(249).'<br /><div id="premiumblock" style="display: none;"><br /><table width="150" border="0"><tr><td>'.lang(250).':&nbsp;</td><td><input type="text" name="premium_user" id="premium_user" size="15" value="" /></td></tr><tr><td>'.lang(251).':&nbsp;</td><td><input type="password" name="premium_pass" id="premium_pass" size="15" value="" /></td></tr></table></div><br />';
			echo '<h4>Enter password here: <input type="text" name="userPass2" id="filepass" size="13" />&nbsp;&nbsp;<input type="submit" onclick="return check()" value="Download File" /></h4>' . "\n";
			echo "<script type='text/javascript'>\nfunction check() {\nvar pass=document.getElementById('filepass');\nif (pass.value == '') { window.alert('You didn\'t enter the password'); return false; }\nelse { return true; }\n}\n</script>\n";
			echo "\n</form></center>\n</body>\n</html>";
			exit;
		} else {
			return $page;
		}
	}

	private function PremiumDownload($link) {
		$pA = ($_REQUEST["premium_user"] && $_REQUEST["premium_pass"] ? true : false);
		$this->cookie = $this->login($pA);
		$page = $this->CheckForPass($this->GetPage($link, $this->cookie), $link, true, $pA);
		$this->cookie = GetCookies($page);

		if (stristr($page, "\r\nContent-Length: 0\r\n")) {
			is_notpresent($page, "\r\nLocation:", "Error: Direct link not found.");
			if (!preg_match('@Location: (http://dc\d+.4shared.com/download/[^\r|\n]+)@i', $page, $dl)) html_error("Error: Download-link not found 2.");
		} elseif (!preg_match('@type="text" value="(http://dc\d+.4shared.com/download/[^"]+)"@i', $page, $dl)) {
			html_error("Error: Download-link not found.");
		}
		$dllink = $dl[1];

		$url = parse_url($dllink);
		$FileName = basename($url["path"]);

		$this->RedirectDownload($dllink, $FileName, $this->cookie);
	}

	private function login($pA=false) {
		global $premium_acc;
		$email = ($pA ? $_REQUEST["premium_user"] : $premium_acc["4shared_com"]["user"]);
		$pass = ($pA ? $_REQUEST["premium_pass"] : $premium_acc["4shared_com"]["pass"]);

		if (empty($email) || empty($pass)) {
			html_error("Login Failed: EMail or Password is empty. Please check login data.");
		}

		$postURL = "http://www.4shared.com/login";
		$post["login"] = $email;
		$post["password"] = $pass;
		$post["remember"] = "false";
		$post["doNotRedirect"] = "true";
		$page = $this->GetPage($postURL, "4langcookie=en", $post, $postURL);
		$cookie = GetCookies($page) . "; 4langcookie=en";

		is_present($page, "Invalid e-mail address or password", "Login Failed: Invalid Username/Email or Password.");
		if (stristr($page, '"ok":false') && $err=cut_str($page, '"rejectReason":"', '"')) html_error("Login Failed: 4S says: '$err'.");
		is_notpresent($cookie, "Login=", "Login Failed. Cookie 'Login' not found.");
		is_notpresent($cookie, "Password=", "Login Failed. Cookie 'Password' not found.");

		// Chk Acc.
		$redir = cut_str($page, '"loginRedirect":"', '"');
		if (!$redir) html_error("Redirection 1 not found.");
		$page = $this->GetPage($redir, $cookie, 0, "http://www.4shared.com/");
		if (!preg_match('@Location: (http://(www\.)?4shared\.com/[^\r|\n]+)@i', $page, $rloc)) html_error("Redirection 2 not found.");
		$cookie = "$cookie; " . GetCookies($page);
		$page = $this->GetPage($rloc[1], $cookie, 0, $redir);
		is_present($page, "HTTP/1.1 302 Moved Temporarily", "Error: Unknown redirect found.");
		$quota = cut_str($page, 'Bandwidth:', "</div>");
		if (!preg_match('/"quotausagebar" title="([\d|\.]+)% of ([\d|\.]+) (\w+)"/i', $quota, $qm)) html_error("Cannot get Bandwidth info. Acc. is not premium?");
		$used = floatval($qm[1]);
		$total = floatval($qm[2]);
		// I have to check the BW... I will show it too :)
		$this->changeMesg(lang(300)."<br />4S Premium Download<br />Bandwidth: $used% of $total {$qm[3]}.");
		if ($used >= 95) html_error("Bandwidth limit trigered: Bandwidth: $used% - Limit: 95%");

		return $cookie;
	}
}

//[21-Nov-2010] Rewritten by Th3-822 & Using some code from the 2shared plugin.
//[26-Jan-2011] Fixed cookies for download pass-protected files. - Th3-822
//[02-Apr-2011] Fixed error when downloading pass-protected files & Added 1 Error Msg. - Th3-822
//[07-May-2011] Some edits to the plugin && Added Premium download support. - Th3-822

?>