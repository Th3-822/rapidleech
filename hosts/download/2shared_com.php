<?php
if (!defined('RAPIDLEECH')) {
	require_once("index.html");
	exit;
}

class d2shared_com extends DownloadClass 
{
	public function Download( $link ) 
	{
		$page = $this->GetPage($link);
		is_present( $page, "file link that you requested is not valid", "The file link that you requested is not valid. Please contact link publisher or try to make a search" );
		is_present( $page, "File download limit has been exceeded.", "Free download limit has been exceeded. Try again later." );
		$cookie = GetCookies($page);

		if ($_GET ["step"] == "1") {
			$post = Array();
			$post["userPass2"] = $_POST['userPass2'];
			$cookie = urldecode($_POST['cookie']);
			$page = $this->GetPage($link,$cookie,$post,$link);
			is_present($page, "enter password to access this file", "The password you have entered is not valid.");
		} elseif (stristr($page, 'enter password to access this file')) {
			echo "\n" . '<form name="dl_password" action="' . $PHP_SELF . '" method="post" >' . "\n";
			echo '<input type="hidden" name="link" value="' . urlencode ($link) . '" />' . "\n";
			echo '<input type="hidden" name="referer" value="' . urlencode ($Referer) . '" />' . "\n";
			echo '<input type="hidden" name="cookie" value="' . urlencode ($cookie) . '" />' . "\n";
			echo '<input type="hidden" name="step" value="1" />' . "\n";
			echo '<input type="hidden" name="comment" id="comment" value="' . $_GET ["comment"] . '" />' . "\n";
			echo '<input type="hidden" name="email" id="email" value="' . $_GET ["email"] . '" />' . "\n";
			echo '<input type="hidden" name="partSize" id="partSize" value="' . $_GET ["partSize"] . '" />' . "\n";
			echo '<input type="hidden" name="method" id="method" value="' . $_GET ["method"] . '" />' . "\n";
			echo '<input type="hidden" name="proxy" id="proxy" value="' . $_GET ["proxy"] . '" />' . "\n";
			echo '<input type="hidden" name="proxyuser" id="proxyuser" value="' . $_GET ["proxyuser"] . '" />' . "\n";
			echo '<input type="hidden" name="proxypass" id="proxypass" value="' . $_GET ["proxypass"] . '" />' . "\n";
			echo '<input type="hidden" name="path" id="path" value="' . $_GET ["path"] . '" />' . "\n";
			echo '<h4>Enter password here: <input type="text" name="userPass2" id="filepass" size="13" />&nbsp;&nbsp;<input type="submit" onclick="return check()" value="Download File" /></h4>' . "\n";
			echo "<script language='JavaScript'>\nfunction check() {\nvar pass=document.getElementById('filepass');\nif (pass.value == '') { window.alert('You didn\'t enter the password'); return false; }\nelse { return true; }\n}\n</script>\n";
			echo "</form>\n</body>\n</html>";
			exit;
		}

		$this->getCountDown($page);
		$FileName = trim(cut_str($page, 'name="Description" content="', ' download free at 2shared.'));

		// Retrieve download link
		if (preg_match ('/dc(\d+)\.2shared\.com\/download\/([^\'|\"|\<]+)/i', $page, $L)) {
			$dllink = "http://dc" . $L[1] . ".2shared.com/download/" . $L[2];
		} else {
			html_error("Download-link not found.");
		}

		$this->RedirectDownload($dllink, $FileName, $cookie);
	}
	
	private function getCountDown($page) 
	{
		if (preg_match ( '/var c = ([0-9])*;/', $page, $count ) ) 
		{
			$countDown = $count [1];
			$this->CountDown($countDown);
		}
	}
}

/********************************************************
Fixed by Raj Malhotra on 10 April 2010 => Fix Reloading to main page when link does not exists.

Fixed by Th3-822 on 30 October 2010 => Fixed & Added support for password protected files.
Fixed by Th3-822 on 25 December 2010 => Fixed: 2shared changed it's system (Again... Now shows dlink in same page)
Fixed by Th3-822 on 06 March 2011 => Changed regex for new dlink format & Added error msg for download limit.
*********************************************************/
?>