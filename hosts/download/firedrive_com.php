<?php

if (!defined('RAPIDLEECH')) {
	require_once('index.html');
	exit;
}

class firedrive_com extends DownloadClass {
	private $link, $page, $cookie, $pA, $Getregexp, $DLregexp;
	public function Download($link) {
		global $premium_acc;
		if (!preg_match('@https?://(?:www\.)?(?:firedrive|putlocker)\.com/(?:mobile/)?file/(\w+)@i', $link, $fid)) html_error('Invalid Link?.');
		$this->link = 'http://www.firedrive.com/file/' . $fid[1];
		$this->Getregexp = '@(https?://(?:[^/\r\n\s\'\"<>]+\.)?firedrive\.com)?/\?(?:key|(stream))=([^\r\n\s\'\"<>]+)@i';
		$this->DLregexp = '@Location: (https?://(?:(?:[^/\r\n]+/(?:download|premium|stream))|(?:cdn\.[^/\r\n]+))/[^\r\n]*)@i';
		$this->vExts = '@\.(mp4|flv|mpe?g|mkv|wmv|mov|3gp|avi|(m2)?ts|rm(vb)?|webm)$@i';
		$this->pA = (empty($_REQUEST['premium_user']) || empty($_REQUEST['premium_pass']) ? false : true);
		if (empty($_REQUEST['step'])) {
			$this->page = $this->GetPage($this->link);
			is_present($this->page, '/?404', 'The requested file is not found');
			is_present($this->page, '404: This file might have been moved, replaced or deleted.', 'The requested file is not found');
			$this->cookie = GetCookiesArr($this->page);
		} else $this->cookie = array();

		$_REQUEST['cookieuse'] = (isset($_REQUEST['cookieuse']) && $_REQUEST['cookieuse'] == 'on' && !empty($_REQUEST['cookie'])) ? 'on' : false;
		if ($_REQUEST['cookieuse'] == 'on' && !empty($_POST['cookie_encrypted'])) {
			$_REQUEST['cookie'] = decrypt(urldecode($_REQUEST['cookie']));
			unset($_POST['cookie_encrypted']);
		}

		if (($_REQUEST['cookieuse'] == 'on' && preg_match('@auth\s*=\s*([\w\%\-]+);?@i', $_REQUEST['cookie'], $c)) || ($_REQUEST['premium_acc'] == 'on' && !empty($premium_acc['firedrive_com']['cookie']))) {
			$cookie = (empty($c[1]) ? urldecode($premium_acc['firedrive_com']['cookie']) : urldecode($c[1]));
			if (strpos($cookie, '%')) $cookie = urldecode($cookie);
			$this->cookie = array('auth' => urlencode($cookie));
			$spurl = 'https://www.firedrive.com/';
			$purl = 'http://www.firedrive.com/';
			$page = $this->GetPage($purl.'myfiles', $this->cookie);
			if (stripos($page, "\nLocation: $spurl") !== false) {
				$this->cookie = GetCookiesArr($page, $this->cookie);
				$page = $this->GetPage($spurl.'myfiles', $this->cookie, 0, $purl.'myfiles');
			}
			is_notpresent($page, '>Sign Out</a>', 'Cookie Error: Invalid Cookie?.');
			is_present($page, '>Go Pro<', 'Cookie Error: Account isn\'t premium');
			$this->cookie = GetCookiesArr($page, $this->cookie);
			return $this->PremiumDL(!empty($c[1]));
		}  elseif (($_REQUEST['premium_acc'] == 'on' && ($this->pA || (!empty($premium_acc['firedrive_com']['user']) && !empty($premium_acc['firedrive_com']['pass']))))) {
			$user = ($this->pA ? $_REQUEST['premium_user'] : $premium_acc['firedrive_com']['user']);
			$pass = ($this->pA ? $_REQUEST['premium_pass'] : $premium_acc['firedrive_com']['pass']);
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
			if (!preg_match('@<input type="hidden" name="confirm" value="([^"<>\t\r\n]+)"@i', $this->page, $confirm)) html_error('Confirm field not found.');
			$post = array();
			$post['confirm'] = urlencode($confirm[1]);
			$page = $this->GetPage($this->link, $this->cookie, $post);
			if (stripos($page, "\nLocation: ") !== false && preg_match('@\nLocation: ((https?://[^/\r\n]+)?/[^\r\n]*)@i', $page, $redir)) {
				$redir = (empty($redir[2])) ? 'http://www.firedrive.com'.$redir[1] : $redir[1];
				$this->cookie = GetCookiesArr($page, $this->cookie);
				$page = $this->GetPage($redir, $this->cookie);
			}
			if (!preg_match($this->Getregexp, $page, $DL)) html_error('Download-Link Not Found.');
		}

		if (stripos($page, '?key='.$DL[3]) !== false && stripos($page, '?stream='.$DL[3]) !== false) $DL = $this->FSelector($DL);

		$DL[0] = (empty($DL[1])) ? 'http://www.firedrive.com'.$DL[0] : $DL[0];
		$page = $this->GetPage($DL[0], $this->cookie);

		if (empty($DL[2])) { // File
			if (!preg_match($this->DLregexp, $page, $dlink)) html_error('Direct-Link Not Found.');
			if (stripos($page, 'Content-Disposition: attachment;') !== false) {
				$fname = cut_str($page, 'Content-Disposition: attachment; filename=', "\r\n");
				if (!empty($fname)) {
					$fname = trim(str_replace(str_split('\\:*?"<>|=;/'."\t\r\n"), '', $fname));
					if (strpos($fname, '/') !== false) $fname = basename($fname);
				}
			}
			if (empty($fname)) {
				if (preg_match('@<title>([^<>\r\n\t\"]+)\s\|\sFiredrive@i', $this->page, $title)) {
					$title = trim(html_entity_decode($title[1]));
					$fname = str_replace(str_split('\\:*?"<>|=;/'."\t\r\n"), '', $title);
				} else $fname = urldecode(basename(parse_url($dlink[1], PHP_URL_PATH)));
			}
		} else { // Stream
			// Firedrive aren't sending the stream links as they should do, so i added this workaround.
			if (preg_match('@<media:content url="(https?://[^\r\n\"\s<>]+)"@i', $page, $dlink)) $dlink[1] = html_entity_decode($dlink[1]);
			elseif (!preg_match(str_replace('\nLocation: ', '', $this->DLregexp), $page, $dlink)) html_error('Stream-Link Not Found.');
			$fname = urldecode(basename(parse_url($dlink[1], PHP_URL_PATH)));
			if (preg_match('@<title>([^<>\r\n\t\"]+)\s\|\sFiredrive@i', $this->page, $title)) {
				$title = preg_replace($this->vExts, '', trim(html_entity_decode($title[1])));
				$title .= strtolower(strrchr($fname, '.'));
			} else $title = $fname;
			$fname = substr($title, 0, strrpos($title, '.')) . '-[S]' . substr($title, strrpos($title, '.'));
			$fname = str_replace(str_split('\\:*?"<>|=;/'."\t\r\n"), '', $fname);
		}
		$this->RedirectDownload(str_ireplace('https://', 'http://', $dlink[1]), $fname, $this->cookie, 0, 0, $fname);
	}

	private function FSelector($DL, $premium = false, $usercookie = false) {
		if (!empty($_POST['dltype']) && $_POST['dltype'] == '1') {
			$DL[0] = str_ireplace('?stream=', '?key=', $DL[0]);
			$DL[2] = '';
		} elseif (!empty($_POST['dltype']) && $_POST['dltype'] == '2') {
			$DL[0] = str_ireplace('?key=', '?stream=', $DL[0]);
			$DL[2] = 'stream';
		} elseif (empty($_GET['audl'])) {
			global $PHP_SELF;
			echo "\n<br /><br /><h3 style='text-align: center;'>Select Download Option:</h3>";
			echo "\n<br /><center><form name='Pdl' action='$PHP_SELF' method='POST'>\n";
			echo "<select name='dltype' id='dltype'>\n<option value='1' selected='selected'>Original File</option>\n<option value='2'>Video Stream</option>\n";
			echo "</select>\n";
			$data = $this->DefaultParamArr($this->link);
			if ($premium) {
				$data['premium_acc'] = 'on';
				if ($this->pA) {
					$data['pA_encrypted'] = 'true';
					$data['premium_user'] = urlencode(encrypt($_REQUEST['premium_user']));
					$data['premium_pass'] = urlencode(encrypt($_REQUEST['premium_pass']));
				} elseif ($usercookie) {
					$data['cookieuse'] = 'on';
					$data['cookie_encrypted'] = 'true';
					$data['cookie'] = urlencode(encrypt('auth='.$this->cookie['auth']));
				}
			}
			foreach ($data as $n => $v) echo("<input type='hidden' name='$n' id='$n' value='$v' />\n");
			echo "<input type='submit' name='submit' value='Download' />\n";
			echo "</form></center>\n</body>\n</html>";
			exit;
		}
		return $DL;
	}

	private function PremiumDL($usercookie = false) {
		$page = $this->GetPage($this->link, $this->cookie);
		if (stripos($page, "\nLocation: https://www.firedrive.com/file/") !== false) {
			$this->link = str_ireplace('http://', 'https://', $this->link);
			$this->cookie = GetCookiesArr($page, $this->cookie);
			$page = $this->GetPage($this->link, $this->cookie);
		}
		if (!preg_match($this->Getregexp, $page, $DL)) html_error('Download Link Not Found.');

		if (stripos($page, '?key='.$DL[3]) !== false && stripos($page, '?stream='.$DL[3]) !== false) $DL = $this->FSelector($DL, true, $usercookie);

		$DL[0] = (empty($DL[1])) ? parse_url($this->link, PHP_URL_SCHEME).'://www.firedrive.com'.$DL[0] : $DL[0];
		$page = $this->GetPage($DL[0], $this->cookie);
		if (empty($DL[2])) { // File
			if (!preg_match($this->DLregexp, $page, $dlink)) html_error('Direct Link Not Found.');
			if (stripos($page, 'Content-Disposition: attachment;') !== false) {
				$fname = cut_str($page, 'Content-Disposition: attachment; filename=', "\r\n");
				if (!empty($fname)) {
					$fname = trim(str_replace(str_split('\\:*?"<>|=;/'."\t\r\n"), '', $fname));
					if (strpos($fname, '/') !== false) $fname = basename($fname);
				}
			}
			if (empty($fname)) {
				if (preg_match('@<title>([^<>\r\n\t\"]+)\s\|\sFiredrive@i', $this->page, $title)) {
					$title = trim(html_entity_decode($title[1]));
					$fname = str_replace(str_split('\\:*?"<>|=;/'."\t\r\n"), '', $title);
				} else $fname = urldecode(basename(parse_url($dlink[1], PHP_URL_PATH)));
			}
		} else { // Stream
			// Firedrive aren't sending the stream links as they should do, so i added this workaround.
			if (preg_match('@<media:content url="(https?://[^\r\n\"\s<>]+)"@i', $page, $dlink)) $dlink[1] = html_entity_decode($dlink[1]);
			elseif (!preg_match(str_replace('\nLocation: ', '', $this->DLregexp), $page, $dlink)) html_error('Stream Link Not Found.');
			$fname = urldecode(basename(parse_url($dlink[1], PHP_URL_PATH)));
			if (preg_match('@<title>([^<>\r\n\t\"]+)\s\|\sFiredrive@i', $this->page, $title)) {
				$title = preg_replace($this->vExts, '', trim(html_entity_decode($title[1])));
				$title .= strtolower(strrchr($fname, '.'));
			} else $title = $fname;
			$fname = substr($title, 0, strrpos($title, '.')) . '-[PS]' . substr($title, strrpos($title, '.'));
			$fname = str_replace(str_split('\\:*?"<>|=;/'."\t\r\n"), '', $fname);
		}
		$this->RedirectDownload(str_ireplace('https://', 'http://', $dlink[1]), $fname, $this->cookie, 0, 0, $fname);
	}

	private function Login($user, $pass) {
		$spurl = 'https://www.firedrive.com/';
		$purl = 'http://www.firedrive.com/';
		$post = array();
		$post['user'] = urlencode($user);
		$post['pass'] = urlencode($pass);
		$post['json'] = $post['remember'] = 1;

		$page = $this->GetPage('https://auth.firedrive.com/', $this->cookie, $post, $purl);
		$this->cookie = GetCookiesArr($page, $this->cookie);

		is_present($page, '"status":0', 'Login Failed: Email/Password incorrect.');
		if (empty($this->cookie['auth'])) html_error('Login Error: Cannot find "auth" cookie.');

		$page = $this->GetPage($purl.'myfiles', $this->cookie, 0, $purl);
		if (stripos($page, "\nLocation: $spurl") !== false) {
			$this->cookie = GetCookiesArr($page, $this->cookie);
			$page = $this->GetPage($spurl.'myfiles', $this->cookie, 0, $purl.'myfiles');
		}
		is_present($page, '>Go Pro<', 'Account isn\'t premium');

		$this->SaveCookies($user, $pass); // Update cookies file
		return $this->PremiumDL();
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

	private function CookieLogin($user, $pass, $filename = 'firedrive_dl.php') {
		global $secretkey;
		if (empty($user) || empty($pass)) html_error('Login Failed: User or Password is empty.');
		$user = strtolower($user);

		$filename = DOWNLOAD_DIR . basename($filename);
		if (!file_exists($filename) || (!empty($_POST['step']) && $_POST['step'] == '1')) return $this->Login($user, $pass);

		$file = file($filename);
		$savedcookies = unserialize($file[1]);
		unset($file);

		$hash = hash('crc32b', $user.':'.$pass);
		if (array_key_exists($hash, $savedcookies)) {
			$_secretkey = $secretkey;
			$secretkey = hash('crc32b', $pass).sha1($user.':'.$pass).hash('crc32b', $user); // A 56 char key should be safer. :D
			$this->cookie = (decrypt(urldecode($savedcookies[$hash]['enc'])) == 'OK') ? $this->IWillNameItLater($savedcookies[$hash]['cookie']) : '';
			$secretkey = $_secretkey;
			if (empty($this->cookie) || (is_array($this->cookie) && count($this->cookie) < 1)) return $this->Login($user, $pass);

			$spurl = 'https://www.firedrive.com/';
			$purl = 'http://www.firedrive.com/';
			$page = $this->GetPage($purl.'myfiles', $this->cookie);
			if (stripos($page, "\nLocation: $spurl") !== false) {
				$this->cookie = GetCookiesArr($page, $this->cookie);
				$page = $this->GetPage($spurl.'myfiles', $this->cookie, 0, $purl.'myfiles');
			}
			if (stripos($page, '>Sign Out</a>') === false) return $this->Login($user, $pass);
			$this->SaveCookies($user, $pass); // Update cookies file
			is_present($page, '>Go Pro<', 'Account isn\'t premium');
			return $this->PremiumDL();
		}
		return $this->Login($user, $pass);
	}

	private function SaveCookies($user, $pass, $filename = 'firedrive_dl.php') {
		global $secretkey;
		$maxdays = 31; // Max days to keep extra cookies saved
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
		$secretkey = hash('crc32b', $pass).sha1($user.':'.$pass).hash('crc32b', $user); // A 56 char key should be safer. :D
		$savedcookies[$hash] = array('time' => time(), 'enc' => urlencode(encrypt('OK')), 'cookie' => $this->IWillNameItLater($this->cookie, false));
		$secretkey = $_secretkey;

		file_put_contents($filename, "<?php exit(); ?>\r\n" . serialize($savedcookies), LOCK_EX);
	}
}

//[13-2-2014]  Re-Written by Th3-822.
//[20-2-2014]  Fixed Link Regexp. - Th3-822

?>