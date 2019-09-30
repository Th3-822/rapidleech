<?php

if (!defined('RAPIDLEECH')) {
	require_once('index.html');
	exit;
}

class depositfiles_com extends DownloadClass {
	public $link, $page, $domain, $Mesg;
	private $cookie, $pA, $DLregexp, $TryFreeDLTricks;
	public function Download($link) {
		global $premium_acc;
		$this->pA = (empty($_REQUEST['premium_user']) || empty($_REQUEST['premium_pass']) ? false : true);
		$this->link = str_ireplace('http://', 'https://', $link);
		$this->cookie = array('lang_current' => 'en');
		$this->DLregexp = '@(?:https?:)?//fileshare\d+\.(?:depositfiles|dfiles)\.[^/:\r\n\t\"\'<>]+(?:\:\d+)?/auth-[^\r\n\t\"\'<>]+@i';
		$this->TryFreeDLTricks = true;
		$EnableJsCountdowns = false; // Change this to true if you can't download because server/client timeouts... Doesn't work with audl serverside.
		$this->domain = parse_url($this->link, PHP_URL_HOST);
		if (empty($_REQUEST['step'])) {
			$this->page = cURL($this->link, $this->cookie);
			$this->CheckDomain();
			is_present($this->page, 'This file does not exist', 'The requested file is not found');
		} else $this->CheckDomain(false);

		if (($_REQUEST['premium_acc'] == 'on' && ($this->pA || (!empty($premium_acc['depositfiles_com']['user']) && !empty($premium_acc['depositfiles_com']['pass']))))) {
			$user = ($this->pA ? $_REQUEST['premium_user'] : $premium_acc['depositfiles_com']['user']);
			$pass = ($this->pA ? $_REQUEST['premium_pass'] : $premium_acc['depositfiles_com']['pass']);
			if ($this->pA && !empty($_POST['pA_encrypted'])) {
				$user = decrypt(urldecode($user));
				$pass = decrypt(urldecode($pass));
				unset($_POST['pA_encrypted']);
			}
			$this->CookieLogin($user, $pass);
		} else {
			$this->cookie = GetCookiesArr($this->page, $this->cookie);
			if ($EnableJsCountdowns) $this->jsFreeDL();
			else $this->FreeDL();
		}
	}

	private function CheckDomain($reload = true) {
		if (empty($this->page)) $content = cURL($this->link, $this->cookie);
		else $content = $this->page;

		if (($hpos = strpos($content, "\r\n\r\n")) > 0) $content = substr($content, 0, $hpos);
		if (stripos($content, "\nLocation: ") !== false && preg_match('@\nLocation: (?:https?:)?//(?:[^/\r\n]+\.)?((?:depositfiles|dfiles)\.[^/:\r\n\t\"\'<>]+)(?:\:\d+)?/@i', $content, $redir_domain)) {
			$link = parse_url($this->link);
			$domain = strtolower($link['host']);
			$redir_domain = strtolower($redir_domain[1]);
			if ($domain != $redir_domain) {
				global $Referer;
				$this->domain = $link['host'] = $redir_domain;
				$Referer = $this->link = rebuild_url($link);
				if ($reload) $this->page = cURL($this->link, $this->cookie);
			}
		}
	}

	private function jsT8Trick() {
		$purl = 'https://' . $this->domain . '/';
		$this->cookie = StrToCookies(decrypt(urldecode($_POST['cookie'])));
		$page = cURL($this->link, $this->cookie, array('gateway_result' => '1'));
		if (!preg_match('@<span class="html_download_api-limit_interval">[\s\t\r\n]*(\d+)[\s\t\r\n]*</span>@i', $page, $limit) || $limit < 1) return 0;
		$try = (isset($_POST['T8']['try']) ? $_POST['T8']['try'] : false);
		if (empty($_POST['T8']['fd2']) || !is_numeric($try) || $try < 0 || $try > 3) return $limit[1];
		$try++;

		$page = cURL($purl . 'get_file.php?fd2='.urlencode($_POST['T8']['fd2']), $this->cookie);
		$page = cURL($this->link, $this->cookie, array('gateway_result' => '1'));
		if (preg_match('@<span class="html_download_api-limit_interval">[\s\t\r\n]*(\d+)[\s\t\r\n]*</span>@i', $page, $_limit)) {
			$diff = $limit[1] - $_limit[1];
			$limit[1] = $_limit[1];
			$this->Mesg .= "<br /><br />Skipped $diff secs of ip-limit wait time.";
			$this->changeMesg($this->Mesg);
			if ($diff < 1) return 0; // Error?
			$page = cURL($purl . 'get_file.php?fd=clearlimit', $this->cookie);
			if (($fd2 = cut_str($page, 'name="fd2" value="', '"')) == false) return $limit[1];
			$data = $this->DefaultParamArr($this->link, $this->cookie, true, true);
			$data['step'] = '2';
			$data['T8[fd2]'] = htmlentities($fd2);
			$data['T8[try]'] = $try;
			$this->JSCountdown(30, $data, 'Try ' . ($try + 1) . ' to reduce ip-limit waiting time');
			exit;
		} else {
			$this->Mesg .= "<br /><br />Skipped the remaining {$limit[1]} secs of ip-limit wait time.";
			$this->changeMesg($this->Mesg);
			return 0;
		}
	}

	private function jsFreeDL() {
		$purl = 'https://' . $this->domain . '/';
		if ($this->TryFreeDLTricks) $this->Mesg = lang(300);
		if (!empty($_POST['step'])) switch ($_POST['step']) {
			case '1': $response = urldecode($this->verifySolveMedia(true));
				$this->cookie = StrToCookies(decrypt(urldecode($_POST['cookie'])));
				if (empty($_POST['fid'])) html_error('FileID not found after POST.');

				$query = array('fid' => $_POST['fid'], 'challenge' => $response, 'response' => 'manual_challenge', 'acpuzzle' => '1');
				$page = cURL($purl . 'get_file.php?'.http_build_query($query), $this->cookie);
				is_present($page, 'load_recaptcha()', 'Error: Wrong CAPTCHA entered.');

				if (!preg_match($this->DLregexp, $page, $dlink)) html_error('Download link Not Found.');
				return $this->RedirectDownload((substr($dlink[0], 0, 1) == '/' ? 'https:' : '') . $dlink[0], basename(urldecode(parse_url($dlink[0], PHP_URL_PATH))));
			case '2': if (!$this->TryFreeDLTricks) break;
				$limit = $this->jsT8Trick();
				if ($limit > 0) return $this->JSCountdown($limit, $this->DefaultParamArr($this->link), 'Connection limit has been exhausted for your IP address');
				break;
			case '3': if (empty($_POST['T8']['fid']) || empty($_POST['T8']['cpid'])) html_error('Empty values after countdown.');
				$this->cookie = StrToCookies(decrypt(urldecode($_POST['cookie'])));
				if ($this->TryFreeDLTricks) {
					$page = cURL($purl . 'get_file.php?fd2='.urlencode($_POST['T8']['fid']), $this->cookie);
					if (preg_match($this->DLregexp, $page, $dlink)) return $this->RedirectDownload((substr($dlink[0], 0, 1) == '/' ? 'https:' : '') . $dlink[0], basename(urldecode(parse_url($dlink[0], PHP_URL_PATH))));
					$this->Mesg .= '<br /><br /><b>Cannot skip captcha.</b>';
					$this->changeMesg($this->Mesg);
				}

				$page = cURL($purl . 'get_file.php?fid='.urlencode($_POST['T8']['fid']), $this->cookie);
				is_notpresent($page, 'load_recaptcha()', 'Error: Countdown skipped?.');

				if (!preg_match('@var\s*+ACPuzzleKey\s*=\s*[\'\"]([\w\.\-]+)[\'\"]\s*;@', $page, $cpkey)) html_error('FreeDL: CAPTCHA Not Found.');

				$data = $this->DefaultParamArr($this->link, $this->cookie, true, true);
				$data['step'] = '1';
				$data['fid'] = urlencode($fid[1]);
				return $this->SolveMedia($cpkey[1], $data);
		}

		$page = cURL($this->link, $this->cookie, array('gateway_result' => '1'));
		is_present($page, 'This file does not exist', 'The requested file is not found');
		$this->cookie = GetCookiesArr($this->page, $this->cookie);

		if (stripos($page, 'Connection limit has been exhausted for your IP address!') !== false) {
				if (preg_match('@<span class="html_download_api-limit_interval">[\s\t\r\n]*(\d+)[\s\t\r\n]*</span>@i', $page, $limit)) {
					if ($limit[1] > 45) {
						$page = cURL($purl . 'get_file.php?fd=clearlimit', $this->cookie);
						if (($fd2 = cut_str($page, 'name="fd2" value="', '"')) == false) return $limit[1];
						$data = $this->DefaultParamArr($this->link, $this->cookie, true, true);
						$data['step'] = '2';
						$data['T8[fd2]'] = htmlentities($fd2);
						$data['T8[try]'] = '0';
						return $this->JSCountdown(30, $data, 'Try 1 to reduce ip-limit waiting time');
					}
					return $this->JSCountdown($limit[1], $this->DefaultParamArr($this->link), 'Connection limit has been exhausted for your IP address');
				} else html_error('Connection limit has been exhausted for your IP address. Please try again later.');
			}

			if (!preg_match('@var\s+fid\s*=\s*\'(\w+)\'@i', $page, $fid)) html_error('FileID not found.');
			if (!preg_match('@setTimeout\(\'load_form\(fid, msg\)\',\s*(\d+)(\s*\*\s*1000)?\s*\);@i', $page, $cd)) html_error('Countdown not found.');
			$cd = empty($cd[2]) ? $cd[1] / 1000 : $cd[1];
			if ($cd > 0) {
				$data = $this->DefaultParamArr($this->link, $this->cookie, true, true);
				$data['step'] = '3';
				$data['T8[fid]'] = htmlentities($fid[1]);
				$data['T8[cpid]'] = $cpid[1];
				return $this->JSCountdown($cd, $data);
			}

			if ($this->TryFreeDLTricks) {
				$page = cURL($purl . 'get_file.php?fd2='.urlencode($fid[1]), $this->cookie);
				if (preg_match($this->DLregexp, $page, $dlink)) return $this->RedirectDownload((substr($dlink[0], 0, 1) == '/' ? 'https:' : '') . $dlink[0], basename(urldecode(parse_url($dlink[0], PHP_URL_PATH))));
				$this->Mesg .= '<br /><br /><b>Cannot skip captcha.</b>';
				$this->changeMesg($this->Mesg);
			}

			$page = cURL($purl . 'get_file.php?fid='.urlencode($fid[1]), $this->cookie);
			is_notpresent($page, 'load_recaptcha()', 'Error: Countdown skipped?.');

			if (!preg_match('@var\s*+ACPuzzleKey\s*=\s*[\'\"]([\w\.\-]+)[\'\"]\s*;@', $page, $cpkey)) html_error('FreeDL: CAPTCHA Not Found.');

			$data = $this->DefaultParamArr($this->link, $this->cookie, true, true);
			$data['step'] = '1';
			$data['fid'] = urlencode($fid[1]);
			return $this->SolveMedia($cpkey[1], $data);
	}

	private function FreeDL() {
		$purl = 'https://' . $this->domain . '/';
		if (!empty($_POST['step']) && $_POST['step'] == 1) {
			$response = urldecode($this->verifySolveMedia(true));
			$this->cookie = StrToCookies(decrypt(urldecode($_POST['cookie'])));
			if (empty($_POST['fid'])) html_error('FileID not found after POST.');

			$query = array('fid' => $_POST['fid'], 'challenge' => $response, 'response' => 'manual_challenge', 'acpuzzle' => '1');
			$page = cURL($purl . 'get_file.php?'.http_build_query($query), $this->cookie);
			is_present($page, 'load_recaptcha()', 'Error: Wrong CAPTCHA entered.');

			if (!preg_match($this->DLregexp, $page, $dlink)) html_error('Download link Not Found.');
			$this->RedirectDownload((substr($dlink[0], 0, 1) == '/' ? 'https:' : '') . $dlink[0], basename(urldecode(parse_url($dlink[0], PHP_URL_PATH))));
		} else {
			$page = cURL($this->link, $this->cookie, array('gateway_result' => '1'));
			is_present($page, 'This file does not exist', 'The requested file is not found');
			$this->cookie = GetCookiesArr($this->page, $this->cookie);
			if ($this->TryFreeDLTricks) $this->Mesg = lang(300);

			if (stripos($page, 'Connection limit has been exhausted for your IP address!') !== false) {
				if (preg_match('@<span class="html_download_api-limit_interval">[\s\t\r\n]*(\d+)[\s\t\r\n]*</span>@i', $page, $limit)) {
					$x = 0;
					if ($this->TryFreeDLTricks && $limit[1] > 45) while ($x < 3) {
						$page = cURL($purl . 'get_file.php?fd=clearlimit', $this->cookie);
						if (($fd2 = cut_str($page, 'name="fd2" value="', '"')) == false) break;
						insert_timer(30, 'Trying to reduce ip-limit waiting time.');
						$page = cURL($purl . 'get_file.php?fd2='.urlencode($fd2), $this->cookie);
						$page = cURL($this->link, $this->cookie, array('gateway_result' => '1'));
						if (!preg_match('@<span class="html_download_api-limit_interval">[\s\t\r\n]*(\d+)[\s\t\r\n]*</span>@i', $page, $_limit)) {
							$this->Mesg .= '<br /><br />Skipped the remaining '.($limit[1] - 30).' secs of ip-limit wait time.';
							$this->changeMesg($this->Mesg);
							$limit[1] = 0;
							break;
						}
						$diff = ($limit[1] - 30) - $_limit[1];
						$limit[1] = $_limit[1];
						$this->Mesg .= "<br /><br />Skipped $diff secs of ip-limit wait time.";
						$this->changeMesg($this->Mesg);
						if ($diff < 1) break; // Error?
						$x++;
					}
					if ($limit[1] > 0) return $this->JSCountdown($limit[1], $this->DefaultParamArr($this->link), 'Connection limit has been exhausted for your IP address');
				} else html_error('Connection limit has been exhausted for your IP address. Please try again later.');
			}

			if (!preg_match('@var\s+fid\s*=\s*\'(\w+)\'@i', $page, $fid)) html_error('FileID not found.');
			if (!preg_match('@setTimeout\(\'load_form\(fid, msg\)\',\s*(\d+)(\s*\*\s*1000)?\s*\);@i', $page, $cd)) html_error('Countdown not found.');
			$cd = empty($cd[2]) ? $cd[1] / 1000 : $cd[1];
			if ($cd > 0) $this->CountDown($cd);

			if ($this->TryFreeDLTricks) {
				$page = cURL($purl . 'get_file.php?fd2='.urlencode($fid[1]), $this->cookie);
				if (preg_match($this->DLregexp, $page, $dlink)) return $this->RedirectDownload((substr($dlink[0], 0, 1) == '/' ? 'https:' : '') . $dlink[0], basename(urldecode(parse_url($dlink[0], PHP_URL_PATH))));
				$this->Mesg .= '<br /><br /><b>Cannot skip captcha.</b>';
				$this->changeMesg($this->Mesg);
			}

			$page = cURL($purl . 'get_file.php?fid='.urlencode($fid[1]), $this->cookie);
			is_notpresent($page, 'load_recaptcha()', 'Error: Countdown skipped?.');

			if (!preg_match('@var\s*+ACPuzzleKey\s*=\s*[\'\"]([\w\.\-]+)[\'\"]\s*;@', $page, $cpkey)) html_error('FreeDL: CAPTCHA Not Found.');

			$data = $this->DefaultParamArr($this->link, $this->cookie, true, true);
			$data['step'] = '1';
			$data['fid'] = urlencode($fid[1]);
			return $this->SolveMedia($cpkey[1], $data);
		}
	}

	private function PremiumDL() {
		$page = cURL($this->link, $this->cookie);
		is_present($page, 'This file does not exist', 'The requested file is not found');

		if (!preg_match_all($this->DLregexp, $page, $dlink)) html_error('Download-link Not Found.');
		$dlink = $dlink[0][array_rand($dlink[0])];
		$fname = basename(urldecode(parse_url($dlink, PHP_URL_PATH)));
		$this->RedirectDownload((substr($dlink[0], 0, 1) == '/' ? 'https:' : '') . $dlink, $fname);
	}

	private function Login($user, $pass) {
		$purl = 'https://' . $this->domain . '/';
		$errors = array('CaptchaInvalid' => 'Wrong CAPTCHA entered.', 'LoginInvalid' => 'Invalid Login/Pass.', 'InvalidLogIn' => 'Invalid Login/Pass.', 'CaptchaRequired' => 'Captcha Required.');
		if (!empty($_POST['step']) && $_POST['step'] == '1') {
			$post = $this->verifyReCaptchav2();
			$post['login'] = urlencode($user);
			$post['password'] = urlencode($pass);

			$page = cURL($purl.'api/user/login', $this->cookie, $post, $purl.'login.php?return=%2F');
			$json = $this->json2array($page);
			if (!empty($json['error'])) html_error('Login Error'. (!empty($errors[$json['error']]) ? ': ' . $errors[$json['error']] : '.. ['.htmlentities($json['error']).']'));
			elseif ($json['status'] != 'OK') html_error('Login Failed');

			$this->cookie = GetCookiesArr($page, $this->cookie);
			if (empty($this->cookie['autologin'])) html_error('Login Error: Cannot find "autologin" cookie');

			$this->SaveCookies($user, $pass); // Update cookies file
			if ($json['data']['mode'] == 'free') html_error('Login Error: Account isn\'t gold');

			return $this->PremiumDL();
		} else {
			$post = array();
			$post['login'] = urlencode($user);
			$post['password'] = urlencode($pass);

			$page = cURL($purl.'api/user/login', $this->cookie, $post, $purl.'login.php?return=%2F');
			$json = $this->json2array($page);
			if (!empty($json['error']) && $json['error'] != 'CaptchaRequired') html_error('Login Error'. (!empty($errors[$json['error']]) ? ': ' . $errors[$json['error']] : '. ['.htmlentities($json['error']).']'));
			elseif ($json['status'] == 'OK') {
				$this->cookie = GetCookiesArr($page, $this->cookie);
				if (empty($this->cookie['autologin'])) html_error('Login Error: Cannot find "autologin" cookie.');
				$this->SaveCookies($user, $pass); // Update cookies file
				if ($json['data']['mode'] == 'free') html_error('Login Error: Account isn\'t gold.');
				return $this->PremiumDL();
			} elseif (empty($json['error']) || $json['error'] != 'CaptchaRequired') html_error('Login Failed.');

			// Captcha Required
			$page = cURL($purl.'login.php?return=%2F', $this->cookie, 0, $purl);
			$this->cookie = GetCookiesArr($page, $this->cookie);

			if (!preg_match('@(https?://([\w\-]+\.)?(?:depositfiles|dfiles)\.[^/:\r\n\t\"\'<>]+(?:\:\d+)?)/js/base2\.js@i', $page, $jsurl)) html_error('Cannot find captcha.');
			$jsurl = (empty($jsurl[1])) ? 'https://' . $this->domain . $jsurl[0] : $jsurl[0];
			$page = cURL($jsurl, $this->cookie, 0, $purl.'login.php?return=%2F');

			if (!preg_match('@recaptcha2PublicKey\s*=\s*[\'\"]([\w\.\-]+)@i', $page, $cpid)) html_error('reCAPTCHA2 Not Found.');

			$data = $this->DefaultParamArr($this->link);
			$data['step'] = '1';
			$data['premium_acc'] = 'on';
			if ($this->pA) {
				$data['pA_encrypted'] = 'true';
				$data['premium_user'] = urlencode(encrypt($user));
				$data['premium_pass'] = urlencode(encrypt($pass));
			}
			$this->reCAPTCHAv2($cpid[1], $data, 0, 'Login');
		}
	}

	// Special Function Called by verifyReCaptchav2 When Captcha Is Incorrect, To Allow Retry. - Required for NoScript reCAPTCHA2
	protected function retryReCaptchav2() {
		$data = $this->DefaultParamArr($this->link);
		$data['step'] = '1';
		$data['premium_acc'] = 'on';
		if ($this->pA && !empty($_GET['pA_encrypted']) && !empty($_REQUEST['premium_user']) && !empty($_REQUEST['premium_pass'])) {
			$data['pA_encrypted'] = 'true';
			$data['premium_user'] = $_REQUEST['premium_user'];
			$data['premium_pass'] = $_REQUEST['premium_pass'];
		}
		return $this->reCAPTCHAv2($_POST['recaptcha2_public_key'], $data, 0, 'Retry Login');
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

	private function CookieLogin($user, $pass, $filename = 'depositfiles_dl.php') {
		global $secretkey;
		if (empty($user) || empty($pass)) html_error('Login Failed: User or Password is empty.');

		$filename = DOWNLOAD_DIR . basename($filename);
		if (!file_exists($filename) || (!empty($_POST['step']) && $_POST['step'] == '1')) return $this->Login($user, $pass);

		$file = file($filename);
		$savedcookies = unserialize($file[1]);
		unset($file);

		$hash = hash('crc32b', $user.':'.$pass);
		if (array_key_exists($hash, $savedcookies)) {
			$_secretkey = $secretkey;
			$secretkey = sha1($user.':'.$pass);
			$testCookie = (decrypt(urldecode($savedcookies[$hash]['enc'])) == 'OK') ? $this->IWillNameItLater($savedcookies[$hash]['cookie']) : '';
			$secretkey = $_secretkey;
			if (empty($testCookie) || (is_array($testCookie) && count($testCookie) < 1)) return $this->Login($user, $pass);

			$page = cURL('https://' . $this->domain . '/', $testCookie);
			if (stripos($page, 'style="display: none;" data-type="guest"') === false) return $this->Login($user, $pass);
			$this->cookie = GetCookiesArr($page, $testCookie); // Update cookies
			$this->SaveCookies($user, $pass); // Update cookies file
			is_present($page, 'user_icon user_member', 'Account isn\'t premium');
			return $this->PremiumDL();
		}
		return $this->Login($user, $pass);
	}

	private function SaveCookies($user, $pass, $filename = 'depositfiles_dl.php') {
		global $secretkey;
		$maxdays = 31; // Max days to keep cookies saved
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

	public function CheckBack($header) {
		$statuscode = intval(substr($header, 9, 3));
		if ($statuscode == 400) {
			if (stripos($header, "\nGuest-Limit: Wait") !== false) html_error('[DepositFiles] FreeDL Limit Reached, try downloading again for countdown.');
			elseif (stripos($header, "\nDownload-Error: No such voucher") !== false) html_error('[DepositFiles] Expired download link.');
			else html_error('Error: 400 Bad Request');
		} elseif ($statuscode == 404) {
			textarea($header);
			html_error('[DepositFiles] Your IP was banned?.');
		}
	}

	// Fix For HTTPS links.
	public function RedirectDownload($link, $FileName = 0, $cookie = 0, $post = 0, $referer = 0, $force_name = 0, $auth = 0, $addon = array()) {
		return parent::RedirectDownload(str_ireplace('https://', 'http://', $link), $FileName, $cookie, $post, $referer, $force_name, $auth, $addon);
	}
}

//[13-1-2013]  Written by Th3-822.
//[20-1-2013] Updated for df's new domains. - Th3-822
//[20-2-2013] Added new functions and a var for using JSCountdown at freedl for avoid timeouts. - Th3-822
//[23-7-2013] Fixed freedl countdown on both freedl functions. - Th3-822
//[17-9-2013] Fixed redirect not working with https links. - Th3-822
//[21-6-2015] Fixed CAPTCHAs. - Th3-822
//[25-3-2017] Switched to cURL/https, fixed CheckDomain & fixed download regexp/url. - Th3-822