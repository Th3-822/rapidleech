<?php

if (!defined('RAPIDLEECH')) {
	require_once('index.html');
	exit;
}

class putlocker_com extends DownloadClass {
	private $link, $page, $cookie, $pA, $Getregexp, $DLregexp;
	public function Download($link) {
		global $premium_acc;
		$this->link = str_ireplace(array('://putlocker.com/', '/mobile/file/'), array('://www.putlocker.com', '/file/'), $link);
		$this->Getregexp = '@(https?://(?:[^/\r\n\t\s\'\"<>]+\.)?putlocker\.com)?/get_file\.php\?(?:(?:id)|(?:file)|(stream))=[^\r\n\t\s\'\"<>]+@i';
		$this->DLregexp = '@Location: (https?://(?:(?:[^/\r\n]+/(?:(?:download)|(?:premium)))|(?:cdn\.[^/\r\n]+))/[^\r\n]*)@i';
		$this->pA = (empty($_REQUEST['premium_user']) || empty($_REQUEST['premium_pass']) ? false : true);
		if (empty($_REQUEST['step'])) {
			$this->page = $this->GetPage($this->link);
			is_present($this->page, '/?404', 'The requested file is not found');
			$this->cookie = GetCookiesArr($this->page);
		} else $this->cookie = array();

		$_REQUEST['cookieuse'] = (isset($_REQUEST['cookieuse']) && $_REQUEST['cookieuse'] == 'on' && !empty($_REQUEST['cookie'])) ? 'on' : false;
		if ($_REQUEST['cookieuse'] == 'on' && !empty($_POST['cookie_encrypted'])) {
			$_REQUEST['cookie'] = decrypt(urldecode($_REQUEST['cookie']));
			unset($_POST['cookie_encrypted']);
		}

		if (($_REQUEST['cookieuse'] == 'on' && preg_match('@auth[\s\t]*=[\s\t]*([\w\%\-]+);?@i', $_REQUEST['cookie'], $c)) || ($_REQUEST['premium_acc'] == 'on' && !empty($premium_acc['putlocker_com']['cookie']))) {
			$cookie = (empty($c[1]) ? urldecode($premium_acc['putlocker_com']['cookie']) : urldecode($c[1]));
			if (strpos($cookie, '%')) $cookie = urldecode($cookie);
			$this->cookie = array('auth' => urlencode($cookie));
			$page = $this->GetPage('http://www.putlocker.com/', $this->cookie);
			is_notpresent($page, '>Sign Out</a>', 'Cookie Error: Invalid Cookie?.');
			is_present($page, '>( Free )<', 'Cookie Error: Account isn\'t premium');
			$this->cookie = GetCookiesArr($page, $this->cookie);
			return $this->PremiumDL(!empty($c[1]));
		}  elseif (($_REQUEST['premium_acc'] == 'on' && ($this->pA || (!empty($premium_acc['putlocker_com']['user']) && !empty($premium_acc['putlocker_com']['pass']))))) {
			$user = ($this->pA ? $_REQUEST['premium_user'] : $premium_acc['putlocker_com']['user']);
			$pass = ($this->pA ? $_REQUEST['premium_pass'] : $premium_acc['putlocker_com']['pass']);
			if ($this->pA && !empty($_POST['pA_encrypted'])) {
				$user = decrypt(urldecode($user));
				$pass = decrypt(urldecode($pass));
				unset($_POST['pA_encrypted']);
			}
			return $this->CookieLogin($user, $pass);
		} else {
			return $this->FreeDL();
		}
	}

	private function FreeDL() {
		if (!preg_match($this->Getregexp, $this->page, $DL)) {
			if (!preg_match('@var\scountdownNum\s?=\s?(\d+);@i', $this->page, $wait)) html_error('Countdown not found.');
			elseif ($wait[1] > 0) $this->CountDown($wait[1]);

			if (!preg_match('@<input type="hidden" value="(\w+)" name="hash"@i', $this->page, $hash)) html_error('Filehash not found.');
			$post = array();
			$post['hash'] = $hash[1];
			$post['confirm'] = cut_str($this->page, 'name="confirm" type="submit" value="', '"');
			$page = $this->GetPage($this->link, $this->cookie, $post);
			if (stripos($page, "\r\nLocation: ") !== false && preg_match('@Location: ((https?://[^/\r\n]+)?/[^\r\n]*)@i', $page, $redir)) {
				$redir = (empty($redir[2])) ? 'http://www.putlocker.com'.$redir[1] : $redir[1];
				$page = $this->GetPage($redir, $this->cookie);
			}
			if (!preg_match($this->Getregexp, $page, $DL)) html_error('Download-Link Not Found.');
		}

		$DL[0] = (empty($DL[1])) ? 'http://www.putlocker.com'.$DL[0] : $DL[0];
		$page = $this->GetPage($DL[0], $this->cookie);
		if (empty($DL[2])) { // File
			if (!preg_match($this->DLregexp, $page, $dlink)) html_error('Direct-Link Not Found.');
			if (stripos($page, 'Content-Disposition: attachment;') !== false) {
				$fname = cut_str($page, 'Content-Disposition: attachment; filename=', "\r\n");
				if (!empty($fname)) {
					$fname = trim(str_replace(str_split('\\:*?"<>|=;'."\t\r\n"), '', $fname));
					if(strpos($fname, '/') !== false) $fname = basename($fname);
				}
			}
			if (empty($fname)) {
				if (preg_match('@<title>([^<>\r\n\t\"]+)\s\|\sPutLocker@i', $this->page, $title)) {
					$title = trim(html_entity_decode($title[1]));
					$fname = str_replace(str_split('\\:*?"<>|=;'."\t\r\n"), '', $title);
				} else $fname = urldecode(basename(parse_url($dlink[1], PHP_URL_PATH)));
			}
		} else { // Stream
			if (!preg_match('@<media:content url="(https?://[^\r\n\"\t\s<>]+)"@i', $page, $dlink)) html_error('Stream-Link Not Found.');
			$dlink[1] = html_entity_decode($dlink[1]);
			$fname = urldecode(basename(parse_url($dlink[1], PHP_URL_PATH)));
			if (preg_match('@<title>([^<>\r\n\t\"]+)\s\|\sPutLocker@i', $this->page, $title)) {
				$title = trim(html_entity_decode($title[1]));
				if (strrpos($title, '.') !== false) $title = substr($title, 0, strrpos($title, '.'));
				$title .= strtolower(strrchr($fname, '.'));
			} else $title = $fname;
			$fname = substr($title, 0, strrpos($title, '.')) . '-[S]' . substr($title, strrpos($title, '.'));
			$fname = str_replace(str_split('\\:*?"<>|=;'."\t\r\n"), '', $fname);
		}
		$this->RedirectDownload($dlink[1], $fname, $this->cookie, 0, 0, $fname);
	}

	private function PremiumDL($usercookie = false) {
		$page = $this->GetPage($this->link, $this->cookie);
		$Mob = stripos($page, '>Download for Mobile</a>') !== false;
		if (!empty($_REQUEST['dltype']) && $_REQUEST['dltype'] == '3' && $Mob) {
			$this->link = str_ireplace('/file/', '/mobile/file/', $this->link);
			$page = $this->GetPage($this->link, $this->cookie);
		}

		preg_match(str_replace('|(stream)', '', $this->Getregexp), $page, $DLf); //File
		preg_match(str_replace('(?:id)|(?:file)|', '', $this->Getregexp), $page, $DLs); //Stream
		if (empty($DLf) && empty($DLs)) html_error('Download Link Not Found.');
		elseif (empty($DLf) xor empty($DLs)) $DL = empty($DLf) ? $DLs : $DLf;
		elseif (!empty($_GET['audl']) || (!empty($_REQUEST['dltype']) && $_REQUEST['dltype'] == '1')) $DL = $DLf;
		elseif (!empty($_REQUEST['dltype']) && $_REQUEST['dltype'] == '2') $DL = $DLs;
		else {
			global $PHP_SELF;
			echo "\n<br /><br /><h3 style='text-align: center;'>Select Download Option:</h3>";
			echo "\n<br /><center><form name='Pdl' action='$PHP_SELF' method='POST'>\n";
			echo "<select name='dltype' id='dltype'>\n<option value='1' selected='selected'>Original File</option>\n<option value='2'>Video Stream</option>\n";
			if ($Mob) echo "<option value='3'>Mobile</option>\n";
			echo "</select>\n";
			$data = $this->DefaultParamArr($this->link);
			$data['premium_acc'] = 'on'; // I should add 'premium_acc' to DefaultParamArr()
			if ($this->pA) {
				$data['pA_encrypted'] = 'true';
				$data['premium_user'] = urlencode(encrypt($_REQUEST['premium_user']));
				$data['premium_pass'] = urlencode(encrypt($_REQUEST['premium_pass']));
			} elseif ($usercookie) {
				$data['cookieuse'] = 'on';
				$data['cookie_encrypted'] = 'true';
				$data['cookie'] = urlencode(encrypt('auth='.$this->cookie['auth']));
			}
			foreach ($data as $n => $v) echo("<input type='hidden' name='$n' id='$n' value='$v' />\n");
			echo "<input type='submit' name='submit' value='Download' />\n";
			echo "</form></center>\n</body>\n</html>";
			exit;
		}
		unset($DLf, $DLs);

		$DL[0] = (empty($DL[1])) ? 'http://www.putlocker.com'.$DL[0] : $DL[0];
		$page = $this->GetPage($DL[0], $this->cookie);
		if (empty($DL[2])) { // File
			if (!preg_match($this->DLregexp, $page, $dlink)) html_error('Direct Link Not Found.');
			if (stripos($page, 'Content-Disposition: attachment;') !== false) {
				$fname = cut_str($page, 'Content-Disposition: attachment; filename=', "\r\n");
				if (!empty($fname)) {
					$fname = trim(str_replace(str_split('\\:*?"<>|=;'."\t\r\n"), '', $fname));
					if(strpos($fname, '/') !== false) $fname = basename($fname);
				}
			}
			if (empty($fname)) {
				if (preg_match('@<title>([^<>\r\n\t\"]+)\s\|\sPutLocker@i', $this->page, $title)) {
					$title = trim(html_entity_decode($title[1]));
					$fname = str_replace(str_split('\\:*?"<>|=;'."\t\r\n"), '', $title);
				} else $fname = urldecode(basename(parse_url($dlink[1], PHP_URL_PATH)));
			}
			if (stripos($DL[0], '&mobile=1')) { // Add a -[M] and correct the fileext on mobile videos.
				if (strrpos($fname, '.') !== false) $fname = substr($fname, 0, strrpos($fname, '.'));
				$fname .= '-[M].mp4';
			}
		} else { // Stream
			if (!preg_match('@<media:content url="(https?://[^\r\n\"\t\s<>]+)"@i', $page, $dlink)) html_error('Stream Link Not Found.');
			$dlink[1] = html_entity_decode($dlink[1]);
			$fname = urldecode(basename(parse_url($dlink[1], PHP_URL_PATH)));
			if (preg_match('@<title>([^<>\r\n\t\"]+)\s\|\sPutLocker@i', $this->page, $title)) {
				$title = trim(html_entity_decode($title[1]));
				if (strrpos($title, '.') !== false) $title = substr($title, 0, strrpos($title, '.'));
				$title .= strtolower(strrchr($fname, '.'));
			} else $title = $fname;
			$fname = substr($title, 0, strrpos($title, '.')) . '-[PS]' . substr($title, strrpos($title, '.'));
			$fname = str_replace(str_split('\\:*?"<>|=;'."\t\r\n"), '', $fname);
		}
		$this->RedirectDownload($dlink[1], $fname, $this->cookie, 0, 0, $fname);
	}

	private function Login($user, $pass) {
		$purl = 'http://www.putlocker.com/';
		if (!empty($_POST['step']) && $_POST['step'] == '1') {
			if (empty($_POST['captcha'])) html_error('You didn\'t enter the image verification code.');
			$this->cookie = StrToCookies(decrypt(urldecode($_POST['cookie'])));

			$post = array();
			$post['user'] = urlencode($user);
			$post['pass'] = urlencode($pass);
			$post['captcha_code'] = urlencode($_POST['captcha']);
			$post['remember'] = 1;
			$post['login_submit'] = 'Login';

			$page = $this->GetPage($purl.'authenticate.php?login', $this->cookie, $post, $purl.'authenticate.php?login');
			$this->cookie = GetCookiesArr($page, $this->cookie);

			if (stripos($page, "\r\nLocation: ") !== false && preg_match('@Location: ((https?://[^/\r\n]+)?/authenticate\.php[^\r\n]*)@i', $page, $redir)) {
				$redir = (empty($redir[2])) ? 'http://www.putlocker.com'.$redir[1] : $redir[1];
				$page = $this->GetPage($redir, $this->cookie);
				$this->cookie = GetCookiesArr($page, $this->cookie);
			}

			is_present($page, 'No such username or wrong password', 'Login Failed: Email/Password incorrect.');
			is_present($page, 'Please re-enter the captcha code', 'Login Failed: Wrong CAPTCHA entered.');

			if (empty($this->cookie['auth'])) html_error('Login Error: Cannot find "auth" cookie.');

			$page = $this->GetPage($purl, $this->cookie, 0, $purl.'authenticate.php?login');
			is_present($page, '>( Free )<', 'Account isn\'t premium');

			$this->SaveCookies($user, $pass); // Update cookies file
			return $this->PremiumDL();
		} else {
			$page = $this->GetPage($purl.'authenticate.php?login', $this->cookie, 0, $purl);
			$this->cookie = GetCookiesArr($page, $this->cookie);

			if (!preg_match('@(https?://[^/\r\n\t\s\'\"<>]+)?/include/captcha\.php\?[^/\r\n\t\s\'\"<>]+@i', $page, $imgurl)) html_error('CAPTCHA not found.');
			$imgurl = (empty($imgurl[1])) ? 'http://www.putlocker.com'.$imgurl[0] : $imgurl[0];
			$imgurl = html_entity_decode($imgurl);

			//Download captcha img.
			$page = $this->GetPage($imgurl, $this->cookie);
			$capt_img = substr($page, strpos($page, "\r\n\r\n") + 4);
			$imgfile = DOWNLOAD_DIR . 'putlocker_captcha.png';

			if (file_exists($imgfile)) unlink($imgfile);
			if (!write_file($imgfile, $capt_img)) html_error('Error getting CAPTCHA image.');

			$data = $this->DefaultParamArr($this->link, encrypt(CookiesToStr($this->cookie)));
			$data['step'] = 1;
			$data['premium_acc'] = 'on'; // I should add 'premium_acc' to DefaultParamArr()
			if ($this->pA) {
				$data['pA_encrypted'] = 'true';
				$data['premium_user'] = urlencode(encrypt($user)); // encrypt() will keep this safe.
				$data['premium_pass'] = urlencode(encrypt($pass)); // And this too.
			}
			$this->EnterCaptcha($imgfile.'?'.time(), $data);
			exit;
		}
	}

	private function IWillNameItLater($cookie, $decrypt=true) {
		if (!is_array($cookie)) {
			if (!empty($cookie)) return $decrypt ? decrypt(urldecode($cookie)) : urlencode(encrypt($cookie));
			return '';
		}
		if (count($cookie) < 1) return $cookie;
		$keys = array_keys($cookie);
		$values = array_values($cookie);
		$keys = $decrypt ? array_map('decrypt', array_map('urldecode', $keys)) : array_map('urlencode', array_map('encrypt', $keys));
		$values = $decrypt ? array_map('decrypt', array_map('urldecode', $values)) : array_map('urlencode', array_map('encrypt', $values));
		return array_combine($keys, $values);
	}

	private function CookieLogin($user, $pass, $filename = 'putlocker_dl.php') {
		global $maxdays, $secretkey;
		$maxdays = 3; // Max days to keep cookies saved
		if (empty($user) || empty($pass)) html_error('Login Failed: User or Password is empty.');

		$filename = DOWNLOAD_DIR . basename($filename);
		if (!file_exists($filename) || (!empty($_POST['step']) && $_POST['step'] == '1')) return $this->Login($user, $pass);

		$file = file($filename);
		$savedcookies = unserialize($file[1]);
		unset($file);

		$hash = hash('crc32b', $user.':'.$pass);
		if (array_key_exists($hash, $savedcookies)) {
			if (time() - $savedcookies[$hash]['time'] >= ($maxdays * 24 * 60 * 60)) return $this->Login($user, $pass); // Ignore old cookies
			$_secretkey = $secretkey;
			$secretkey = sha1($user.':'.$pass);
			$this->cookie = (decrypt(urldecode($savedcookies[$hash]['enc'])) == 'OK') ? $this->IWillNameItLater($savedcookies[$hash]['cookie']) : '';
			$secretkey = $_secretkey;
			if (empty($this->cookie) || (is_array($this->cookie) && count($this->cookie) < 1)) return $this->Login($user, $pass);

			$page = $this->GetPage('http://www.putlocker.com/', $this->cookie);
			if (stripos($page, '>Sign Out</a>') === false) return $this->Login($user, $pass);
			is_present($page, '>( Free )<', 'Account isn\'t premium');
			$this->SaveCookies($user, $pass); // Update cookies file
			return $this->PremiumDL();
		}
		return $this->Login($user, $pass);
	}

	private function SaveCookies($user, $pass, $filename = 'putlocker_dl.php') {
		global $maxdays, $secretkey;
		$filename = DOWNLOAD_DIR . basename($filename);
		if (file_exists($filename)) {
			$file = file($filename);
			$savedcookies = unserialize($file[1]);
			unset($file);

			// Remove old cookies
			foreach ($savedcookies as $k => $v) if (time() - $v['time'] >= ($maxdays * 24 * 60 * 60)) unset($savedcookies[$k]);
		} else $savedcookies = array();
		$hash = hash('crc32b', $user.':'.$pass);
		$_secretkey = $secretkey;
		$secretkey = sha1($user.':'.$pass);
		$savedcookies[$hash] = array('time' => time(), 'enc' => urlencode(encrypt('OK')), 'cookie' => $this->IWillNameItLater($this->cookie, false));
		$secretkey = $_secretkey;

		file_put_contents($filename, "<?php exit(); ?>\r\n" . serialize($savedcookies), LOCK_EX);
	}
}

//[16-9-2012]  Written by Th3-822.

?>