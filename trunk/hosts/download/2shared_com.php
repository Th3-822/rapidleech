<?php
if (!defined('RAPIDLEECH')) {
	require_once("index.html");
	exit;
}

class d2shared_com extends DownloadClass {
	public function Download($link) {
		$page = $this->GetPage($link);
		is_present($page, "file link that you requested is not valid", "The file link that you requested is not valid. Please contact link publisher or try to make a search");
		is_present($page, "File download limit has been exceeded.", "Free download limit has been exceeded. Try again later.");
		$cookie = GetCookies($page);

		if (isset($_GET ["step"]) && $_GET ["step"] == "1") {
			$post = Array();
			$post["userPass2"] = $_POST['userPass2'];
			$cookie = urldecode($_POST['cookie']);
			$page = $this->GetPage($link, $cookie, $post, $link);
			is_present($page, "enter password to access this file", "The password you have entered is not valid.");
		} elseif (stristr($page, 'enter password to access this file')) {
			global $PHP_SELF;
			$data = $this->DefaultParamArr($link, $cookie);
			$data['step'] = 1;
			echo "\n<form name='dl_password' action='$PHP_SELF' method='post'>\n";
			foreach ($data as $name => $value) echo "<input type='hidden' name='$name' id='$name' value='$input' />\n";
			echo "<h4>Enter password here: <input type='text' name='userPass2' id='filepass' size='13' />&nbsp;&nbsp;<input type='submit' onclick='return check()' value='Download File' /></h4>\n";
			echo "<script language='JavaScript'>\nfunction check() {\nvar pass=document.getElementById('filepass');\nif (pass.value == '') { window.alert('You didn\'t enter the password'); return false; }\nelse { return true; }\n}\n</script>\n";
			echo "</form>\n</body>\n</html>";
			exit;
		}

		$this->getCountDown($page);

		// Retrieve download link
		if (preg_match('/dc(\d+)\.2shared\.com\/download\/([^\'\"<>\r\n]+)/i', $page, $L)) {
			$dllink = "http://dc" . $L[1] . ".2shared.com/download/" . $L[2];
		} elseif (preg_match('@pageDownload\d/retrieveLink\.jsp\?id=[\w\-\.]+@i', $page, $RD)) {
			$page = $this->GetPage('http://www.2shared.com/'.$RD[0], $cookie, 0, $link);
			if (!preg_match('@https?://dc\d+\.2shared\.com/download/[^\'\"<>\s]+@i', $page, $L)) html_error("Download-link not found [2].");
			$dllink = $L[0];
		} else html_error("Download-link not found.");

		$this->RedirectDownload($dllink, urldecode(basename(parse_url($dllink, PHP_URL_PATH))), $cookie);
	}

	private function getCountDown($page) {
		if (preg_match('/var c = ([0-9])*;/', $page, $count)) {
			$countDown = $count[1];
			$this->CountDown($countDown);
		}
	}

	public function CheckBack($headers) {
		is_present($headers, '?errorMaxSessions=', 'User downloading session limit is reached. Please try again in few minutes.');
		//is_present($headers, '?cau2=403tNull', 'Download link has expired.');
		if (stristr($headers, '?cau2=403tNull')) {
			global $PHP_SELF;
			echo "<center><form action='$PHP_SELF' method='POST'>\n";
			$post = $this->DefaultParamArr(cut_str($headers, 'Location: ', '?cau2='));
			foreach ($post as $name => $input) echo "<input type='hidden' name='$name' id='$name' value='$input' />\n";
			echo "<input type='submit' value='Download Again' />\n";
			echo "</form></center><br />";
			html_error('Download link has expired.');
		}
	}
}

/********************************************************
Fixed by Raj Malhotra on 10 April 2010 => Fix Reloading to main page when link does not exists.

Fixed by Th3-822 on 30 October 2010 => Fixed & Added support for password protected files.
Fixed by Th3-822 on 25 December 2010 => Fixed: 2shared changed it's system (Again... Now shows dlink in same page)
Fixed by Th3-822 on 06 March 2011 => Changed regex for new dlink format & Added error msg for download limit.
Fixed by Th3-822 on 16 June 2012 => Fixed $FileName code.
Fixed by Th3-822 on 04 October 2014 => Fixed download of image files.
*********************************************************/
?>