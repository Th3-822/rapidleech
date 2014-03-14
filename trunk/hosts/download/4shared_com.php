<?php

if (!defined('RAPIDLEECH')) {
	require_once('index.html');
	exit();
}

class d4shared_com extends DownloadClass {
	private $page, $cookie, $pA, $long_regexp, $noTrafficFreeDl;
	public $link;
	public function Download($link) {
		global $premium_acc;
		$this->long_regexp = '@https?://dc\d+\.4shared\.com/download/[^/\"\'\r\n<>\s\t]+/(?:tsid[^/\"\'\r\n<>\s\t]+/)?[^/\"\'\r\n<>\s\t]+@i';
		$this->cookie = array('4langcookie' => 'en');
		$this->noTrafficFreeDl = true; // Set to true to switch to free download when premium traffic used is over the limit.

		if (stristr($link, '.com/get/')) $link = str_ireplace('.com/get/', '.com/file/', $link);
		$this->link = $link;
		$this->page = $this->GetPage($this->link, $this->cookie);
		$this->cookie = GetCookiesArr($this->page, $this->cookie);
		is_present($this->page, 'The file link that you requested is not valid.');
		is_present($this->page, 'The file is suspected of illegal or copyrighted content.');
		is_present($this->page, 'This file is no longer available because it\'s identical to file banned because of claim.'); // MD5 banned

		if ($_REQUEST['premium_acc'] == 'on' && ((!empty($_REQUEST['premium_user']) && !empty($_REQUEST['premium_pass'])) || (!empty($premium_acc['4shared_com']['user']) && !empty($premium_acc['4shared_com']['pass'])))) {
			$this->pA = (!empty($_REQUEST['premium_user']) && !empty($_REQUEST['premium_pass']) ? true : false);
			return $this->login();
		} else return $this->FreeDownload();
	}

	private function FreeDownload() {
		$this->CheckForPass();

		// Direct link downloadable without login or countdown...
		if (preg_match('@window\.location[\s\t]*=[\s\t]*"(https?://dc\d+\.4shared\.com/download/[^/\"\';]+/[^/\"\'\?;]+)"@i', $this->page, $DL)) {
			if (preg_match('/[\?&]dirPwdVerified=(\w+)/i', $this->link, $pwd)) $DL[1] .= '&dirPwdVerified='.$pwd[1];
			return $this->RedirectDownload($DL[1], urldecode(basename(parse_url($DL[1], PHP_URL_PATH))), $this->cookie);
		}

		if (!preg_match('@\.com/[^/]+/([^/]+)/?(.*)@i', $this->link, $L)) html_error('Invalid link?');
		$getLink = "http://www.4shared.com/get/{$L[1]}/{$L[2]}";
		$page = $this->GetPage("http://www.4shared.com/get/{$L[1]}/{$L[2]}", $this->cookie);
		is_present($page, 'You should log in to download this file. Sign up for free if you don\'t have an account yet.', 'You need to be logged in for download this file.');
		$GLOBALS['Referer'] = $getLink;

		if (!preg_match($this->long_regexp, $page, $DL)) html_error('Download-link not found.');
		$this->cookie = GetCookiesArr($page, $this->cookie);
		$dllink = $DL[0];
		if (preg_match('/[\?&]dirPwdVerified=(\w+)/i', $this->link, $pwd)) $dllink .= '&dirPwdVerified='.$pwd[1];

		if (!preg_match('@id="secondsLeft"[\s\t]+value="(\d+)"@i', $page, $count) && !preg_match('@id="downloadDelayTimeSec"[^<>/]*>(\d+)<@i', $page, $count)) html_error('Timer not found.');

		$FileName = urldecode(basename(parse_url($dllink, PHP_URL_PATH)));
		if ($count[1] <= 120) $this->CountDown($count[1]);
		else {
			$data = $this->DefaultParamArr($dllink, encrypt($this->cookie));
			$data['filename'] = urlencode($FileName);
			$data['host'] = $url['host'];
			$data['port'] = $url['port'];
			$data['path'] = urlencode($url['path'] . ($url['query'] ? '?' . $url['query'] : ''));
			$data['saveto'] = $_GET['path'];
			$this->JSCountdown($count[1], $data);
		}

		$this->RedirectDownload($dllink, $FileName, $this->cookie);
	}

	private function CheckForPass($predl=false) {
		global $PHP_SELF;
		if (isset($_GET['step']) && $_GET['step'] == '1') {
			$post = array();
			$post['userPass2'] = $_POST['userPass2'];
			$post['dsid'] = trim($_POST['dsid']);
			$this->page = $this->GetPage($this->link, $this->cookie, $post, $this->link);
			is_present($this->page, 'Please enter a password to access this file', 'The password you have entered is not valid.');
		} elseif (stristr($this->page, 'Please enter a password to access this file')) {
			echo "\n<center><form name='dl_password' action='$PHP_SELF' method='POST'>\n";
			$data = $this->DefaultParamArr($this->link);
			$data['step'] = 1;
			$data['dsid'] = cut_str($this->page, 'name="dsid" value="', '"');
			foreach ($data as $name => $val) echo "<input type='hidden' name='$name' id='$name' value='$val' />\n";
			if ($predl) echo '<br /><input type="checkbox" name="premium_acc" id="premium_acc" onclick="javascript:var displ=this.checked?\'\':\'none\';document.getElementById(\'premiumblock\').style.display=displ;" '.(!$this->pA?'checked="checked"':'').' />&nbsp;'.lang(249).'<br /><div id="premiumblock" style="display: none;"><br /><table width="150" border="0"><tr><td>'.lang(250).':&nbsp;</td><td><input type="text" name="premium_user" id="premium_user" size="15" value="" /></td></tr><tr><td>'.lang(251).':&nbsp;</td><td><input type="password" name="premium_pass" id="premium_pass" size="15" value="" /></td></tr></table></div><br />';
			echo '<h4>Enter password here: <input type="text" name="userPass2" id="filepass" size="13" />&nbsp;&nbsp;<input type="submit" onclick="return check()" value="Download File" /></h4>' . "\n";
			echo "<script type='text/javascript'>\nfunction check() {\nvar pass=document.getElementById('filepass');\nif (pass.value == '') { window.alert('You didn\'t enter the password'); return false; }\nelse { return true; }\n}\n</script>\n";
			echo "\n</form></center>\n</body>\n</html>";
			exit;
		}
		$this->cookie = GetCookiesArr($this->page, $this->cookie, true, array('','deleted','""'));
	}

	private function PremiumDownload() {
		$page = $this->GetPage($this->link, $this->cookie);
		$this->CheckForPass(true);
		$this->cookie = GetCookiesArr($page, $this->cookie);

		if (stripos($page, "\nContent-Length: 0\n") !== false) is_notpresent($page, "\nLocation: ", 'Error: Direct link not found.');
		if (!preg_match($this->long_regexp, $page, $DL)) html_error('Error: Download link not found.');
		$dllink = $DL[0];
		if (preg_match('/[\?&]dirPwdVerified=(\w+)/i', $this->link, $pwd)) $dllink .= '&dirPwdVerified='.$pwd[1];

		$FileName = urldecode(basename(parse_url($dllink, PHP_URL_PATH)));
		$this->RedirectDownload($dllink, $FileName, $this->cookie);
	}

	private function login() {
		global $premium_acc;
		$email = ($this->pA ? $_REQUEST['premium_user'] : $premium_acc['4shared_com']['user']);
		$pass = ($this->pA ? $_REQUEST['premium_pass'] : $premium_acc['4shared_com']['pass']);
		if (empty($email) || empty($pass)) html_error('Login Failed: EMail or Password is empty. Please check login data.');

		$postURL = 'http://www.4shared.com/web/login';
		$post['login'] = urlencode($email);
		$post['password'] = urlencode($pass);
		$post['remember'] = 'false';
		$post['doNotRedirect'] = 'true';
		$page = $this->GetPage($postURL, $this->cookie, $post, $postURL);

		is_present($page, 'Invalid e-mail address or password', 'Login Failed: Invalid Username/Email or Password.');
		if (stripos($page, '"ok":false') !== false) {
			if ($err=cut_str($page, '"rejectReason":"', '"')) html_error("Login Failed: 4S says: '$err'.");
			else html_error('Login Failed.');
		}
		$this->cookie = GetCookiesArr($page, $this->cookie, true, array('','deleted','""'));
		if (empty($this->cookie['Login'])) html_error('Login Error: Cannot find session cookie.');

		// Chk Acc.
		$page = $this->GetPage('http://www.4shared.com/account/home.jsp', $this->cookie);
		if (substr($page, 9, 3) != '200') html_error('Login Error.');
		$this->cookie = GetCookiesArr($page, $this->cookie, true, array('','deleted','""'));

		$quota = cut_str($page, 'Premium Traffic:</div>', '</div>');
		$Mesg = lang(300);

		if (empty($quota) || !preg_match('@>([\d|\.]+)\s?(%|(?:\wB)) of ([\d|\.]+)\s?(\wB)@i', $quota, $qm)) {
			$Mesg .= "<br /><b>Account isn't premium?</b><br />Using it as member.";
			$this->changeMesg($Mesg);

			$this->page = $this->GetPage($this->link, $this->cookie);
			return $this->FreeDownload();
		}

		$used = floatval($qm[1]);
		$total = floatval($qm[3]);

		// I have to check the BW... I will show it too :)
		$Mesg .= "<br />4S Premium Download<br />Bandwidth: $used {$qm[2]} of $total {$qm[4]}.";
		$this->changeMesg($Mesg);
		if ($qm[2] != '%' && $total != 100) $used = ($used * 100)/$total;
		if ($used >= 95) {
			if ($this->noTrafficFreeDl) {
				$Mesg .= "<br /><b>Bandwidth limit trigered: $used% - Limit: 95%</b><br />Switching to FreeDL.";
				$this->changeMesg($Mesg);
				$this->page = $this->GetPage($this->link, $this->cookie);
				$this->cookie = GetCookiesArr($page, $this->cookie);
				return $this->FreeDownload();
			}
			html_error("Bandwidth limit trigered: Bandwidth: $used% - Limit: 95%");
		}

		return $this->PremiumDownload();
	}

	public function CheckBack($header) {
		is_present($header, 'cau2=dow-lim', 'The download limit has been reached.');
	}
}

//[21-Nov-2010] Rewritten by Th3-822 & Using some code from the 2shared plugin.
//[26-Jan-2011] Fixed cookies for download pass-protected files. - Th3-822
//[02-Apr-2011] Fixed error when downloading pass-protected files & Added 1 Error Msg. - Th3-822
//[07-May-2011] Some edits to the plugin && Added Premium download support. - Th3-822
//[25-Jul-2011] Using a function for longer link timelock at free download ( I should add it to DownloadClass. :D ). -Th3-822
//[12-Sep-2011] Fixed regex for get BW usage in Premium && Password in files can be skiped with '?dirPwdVerified=xxxxxxxx' in the url. -Th3-822
//[15-Oct-2011] JSCountdown was added in DownloadClass.php... Removed declaration from plugin. - Th3-822
//[21-Nov-2011] Fixed regexp for get dlink in FreeDL. - Th3-822
//[23-Mar-2012] Added support for member (its needed for some links) & some changes & small fixes. - Th3-822
//[18-Apr-2012] Fixed login needed msg, direct download link regexp (freedl) && removed 2 error msgs from login function. - Th3-822
//[01-Aug-2012] Fixed for changes at 4S. - Th3-822
//[20-Feb-2014] Fixed Login/FreeDL. - Th3-822

?>