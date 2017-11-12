<?php

if (!defined('RAPIDLEECH')) {
	require_once ('index.html');
	exit();
}

class rapidgator_net extends DownloadClass {
	private $baseUrl, $link, $page, $cookie, $referer, $DLregexp;
	public function Download($link) {
		$this->baseUrl = 'https://rapidgator.net/';

		$link = parse_url($link);
		$link['scheme'] = 'https';
		$link['host'] = 'rapidgator.net';
		$this->link = $GLOBALS['Referer'] = $link = rebuild_url($link);
		$this->cookie = array('lang' => 'en');
		$this->DLregexp = '@https?://pr\d+\.rapidgator\.net/[^\s\"\'<>]+@i';
		if ((empty($_POST['step']) || !in_array($_POST['step'], array('1', '2', 'L'))) && (empty($_GET['rgredir']) || (stripos($_GET['rgredir'], '/auth/login') === false && stripos($_GET['rgredir'], '/site/ChangeLocation/key/') === false))) {
			// Weird RG redirects.
			$rdc = 0;
			$this->page = false; // False value for starting the loop.
			$redir = $this->link;
			$this->referer = !empty($GLOBALS['Referer']) ? $GLOBALS['Referer'] : $this->link;
			while (($redir = $this->ChkRGRedirs($this->page, $redir)) && $rdc < 15) {
				$this->page = cURL($redir, $this->cookie, 0, $this->referer);
				$this->cookie = GetCookiesArr($this->page, $this->cookie);
				$this->referer = $redir;
				$rdc++;
			}

			// I haven't tested those redirects fine so i will check this too.
			if (stripos($redir, 'rapidgator.net/file/') === false) {
				$this->page = cURL($this->link, $this->cookie, 0, $this->referer);
				$this->cookie = GetCookiesArr($this->page, $this->cookie);
			}

			is_present($this->page, '>File not found<', 'File not found.');
		}

		if ($_REQUEST['premium_acc'] == 'on' && ((!empty($_REQUEST['premium_user']) && !empty($_REQUEST['premium_pass'])) || (!empty($GLOBALS['premium_acc']['rapidgator_net']['user']) && !empty($GLOBALS['premium_acc']['rapidgator_net']['pass'])))) {
			$this->Login();
		} else $this->FreeDL();
	}

	private function FreeDL() {
		$capt_url = $this->baseUrl . 'download/captcha';
		if (empty($_POST['step']) || !in_array($_POST['step'], array('1', '2'))) {
			is_present($this->page, 'This file can be downloaded by premium only', 'Only Premium users can download this file.'); // F##### PPS
			is_present($this->page, 'download not more than 1 file at a time in free mode.', 'You can\'t download not more than 1 file at a time. Wait 15 minutes and try again.');
			if (preg_match('@You can download files up to \d+ MB in free mode@i', $this->page, $err)) html_error($err[0].'.');
			if (!preg_match('@fid\s*=\s*(\d+)\s*;@i', $this->page, $fid)) html_error('File-id not found.');
			if (!preg_match('@secs\s*=\s*(\d+)\s*;@i', $this->page, $cd)) html_error('Countdown not found.');

			$page = cURL($this->baseUrl . 'download/AjaxStartTimer?fid='.$fid[1], $this->cookie, 0, $this->link."\r\nX-Requested-With: XMLHttpRequest");
			if (!preg_match('@"sid":"([^"\}]+)"@i', $page, $sid)) html_error('Session id not found.');

			if ($cd[1] > 0) $this->CountDown($cd[1]);

			$page = cURL($this->baseUrl . 'download/AjaxGetDownloadLink?sid='.urlencode($sid[1]), $this->cookie, 0, $this->link."\r\nX-Requested-With: XMLHttpRequest"); // ['download_link']
			$this->cookie = GetCookiesArr($page, $this->cookie);
			is_notpresent($page, '"state":"done"', 'Error: Countdown bypassed?.');

			$page = cURL($capt_url, $this->cookie);

			if (preg_match($this->DLregexp, $page, $dlink)) return $this->RedirectDownload($dlink[0], 'rapidgatorfr2');

			$data = $this->DefaultParamArr($this->link, $this->cookie, 1, 1);
			if (preg_match('@(?:https?:)?//api\.solvemedia\.com/papi/challenge\.(?:no)?script\?k=([\w\.\-]+)@i', $page, $captcha)) {
				$data['step'] = 1;
				return $this->SolveMedia($captcha[1], $data);
			} else if (preg_match('@(?:https?:)?//(?:[\w\-]+\.)?(?:google\.com/recaptcha/api|recaptcha\.net)/(?:challenge|noscript)\?k=([\w\.\-]+)@i', $page, $captcha)) {
				$data['step'] = 2;
				return $this->reCAPTCHA($captcha[1], $data);
			}

			return html_error('CAPTCHA not found.');
		} else {
			$this->cookie = (!empty($_POST['cookie'])) ? decrypt(urldecode($_POST['cookie'])) : '';

			$post = array('DownloadCaptchaForm%5Bcaptcha%5D' => '');
			switch ($_POST['step']) {
				case '1':
					$post = array_merge($post, $this->verifySolveMedia());
					break;
				case '2':
					if (empty($_POST['recaptcha_response_field'])) html_error('You didn\'t enter the image verification code.');
					if (empty($_POST['recaptcha_challenge_field'])) html_error('Empty reCAPTCHA challenge.');
					$post['recaptcha_challenge_field'] = urlencode($_POST['recaptcha_challenge_field']);
					$post['recaptcha_response_field'] = urlencode($_POST['recaptcha_response_field']);
					break;
			}
			$_POST['step'] = false;
			$page = cURL($capt_url, $this->cookie, $post);

			is_present($page, 'The verification code is incorrect.');
			is_present($page, "\nSet-Cookie: failed_on_captcha=1", 'Captcha expired. Try again in 15 minutes.');

			if (!preg_match($this->DLregexp, $page, $dlink)) html_error('Error: Download link not found.');
			$this->RedirectDownload($dlink[0], 'rapidgatorfr');
		}
	}

	// Special Function Called by verifySolveMedia When Captcha Is Incorrect, To Allow Retry.
	protected function retrySolveMedia() {
		$data = $this->DefaultParamArr($this->link, $this->cookie, 1, 1);
		$data['step'] = 1;
		return $this->SolveMedia($_POST['sm_public_key'], $data, 0, 'Retry Download');
	}

	private function PremiumDL() {
		$page = cURL($this->link, $this->cookie);

		if (preg_match('@You have reached quota of downloaded information for premium accounts. At the moment, the quota is \d+ [GT]B(?: per \d+ day\(s\))?@i', $page, $err)) html_error($err[0]);

		if (!preg_match($this->DLregexp, $page, $dlink)) html_error('Error: Download-link not found.');

		$this->RedirectDownload($dlink[0], 'rapidgatorpr');
	}

	private function Login() {
		if (!empty($_REQUEST['pA_encrypted']) && !empty($_REQUEST['premium_user']) && !empty($_REQUEST['premium_pass'])) {
			$_REQUEST['premium_user'] = decrypt(urldecode($_REQUEST['premium_user']));
			$_REQUEST['premium_pass'] = decrypt(urldecode($_REQUEST['premium_pass']));
			unset($_REQUEST['pA_encrypted']);
		}
		$pA = (empty($_REQUEST['premium_user']) || empty($_REQUEST['premium_pass']) ? false : true);
		$user = ($pA ? $_REQUEST['premium_user'] : $GLOBALS['premium_acc']['rapidgator_net']['user']);
		$pass = ($pA ? $_REQUEST['premium_pass'] : $GLOBALS['premium_acc']['rapidgator_net']['pass']);

		if (empty($user) || empty($pass)) html_error('Login Failed: User or Password is empty. Please check login data.');
		$this->cookie = array('lang' => 'en'); // Account is always showed as free if it comes from a file, as i don't send file's link as referer, lets reset the cookies.

		$post = array();
		$post['LoginForm%5Bemail%5D'] = urlencode($user);
		$post['LoginForm%5Bpassword%5D'] = urlencode($pass);
		$post['LoginForm%5BrememberMe%5D'] = '1';
		if (!empty($_POST['step']) && $_POST['step'] == 'L') {
			$_POST['step'] = false;
			if (empty($_POST['captcha'])) html_error('You didn\'t enter the image verification code.');
			$this->cookie = StrToCookies(decrypt(urldecode($_POST['cookie'])));
			$post['LoginForm%5BverifyCode%5D'] = urlencode($_POST['captcha']);
		}

		// There are more of those redirects at login
		$rdc = 0;
		$page = false; // False value for starting the loop.
		$redir = $this->baseUrl . 'auth/login';
		$this->referer = !empty($GLOBALS['Referer']) && $GLOBALS['Referer'] != $this->link ? $GLOBALS['Referer'] : $this->baseUrl;
		while (($redir = $this->ChkRGRedirs($page, $redir, '(?:/auth/login|/site/ChangeLocation/key/)')) && $rdc < 15) {
			$page = cURL($redir, $this->cookie, $post, $this->referer);
			$this->cookie = GetCookiesArr($page, $this->cookie);
			$this->referer = $redir;
			$rdc++;
		}

		is_present($page, 'Wrong e-mail or password.', 'Login Failed: Email/Password incorrect.');
		is_present($page, 'E-mail is not a valid email address.', 'Login Failed: Login isn\'t an email address.');
		is_present($page, 'We discovered that you try to access your account from unusual location.', 'Login Failed: Login Blocked By IP, Check Account Email And Follow The Steps To Add IP to Whitelist.');
		if (stripos($page, 'The code from a picture does not coincide') !== false) {
			if (!empty($post['LoginForm%5BverifyCode%5D'])) html_error('Login Failed: Incorrect CAPTCHA response.');
			if (!preg_match('@(https?://(?:[^\./\r\n\'\"\t\:]+\.)?rapidgator\.net(?:\:\d+)?)?/auth/captcha/\w+/\w+@i', $page, $imgurl)) html_error('Error: CAPTCHA not found.');
			$imgurl = (empty($imgurl[1])) ? $this->baseUrl . substr($imgurl[0], 1) : $imgurl[0];
			//Download captcha img.
			$captcha = explode("\r\n\r\n", cURL($imgurl, $this->cookie), 2);
			if (substr($captcha[0], 9, 3) != '200') html_error('Error downloading captcha img.');
			$mimetype = (preg_match('@image/[\w+]+@', $captcha[0], $mimetype) ? $mimetype[0] : 'image/png');

			$data = $this->DefaultParamArr($this->link, CookiesToStr($this->cookie), 1, 1);
			$data['step'] = 'L';
			$data['premium_acc'] = 'on'; // I should add 'premium_acc' to DefaultParamArr()
			if ($pA) {
				$data['pA_encrypted'] = 'true';
				$data['premium_user'] = urlencode(encrypt($user)); // encrypt() will keep this safe.
				$data['premium_pass'] = urlencode(encrypt($pass)); // And this too.
			}
			$this->EnterCaptcha("data:$mimetype;base64,".base64_encode($captcha[1]), $data, 5, 'Login');
			exit;
		}
		//is_present($page, 'The code from a picture does not coincide', 'Login Failed: Captcha... (T8: I will add it later)');

		if (empty($this->cookie['user__'])) html_error("Login Error: Cannot find 'user__' cookie.");
		$this->cookie['lang'] = 'en';

		$page = cURL($this->baseUrl, $this->cookie, 0, $this->baseUrl . 'auth/login');
		if (stripos($page, '>Free</a>') !== false) {
			$this->changeMesg('<br /><b>Account isn\'t premium?</b>', true);
			return $this->FreeDL();
		}

		return $this->PremiumDL();
	}

	private function ChkRGRedirs($page, $lasturl, $rgpath = '/') {
		if (!is_array($lasturl)) $lasturl = parse_url($lasturl);
		if ($page === false) return rebuild_url($lasturl);
		$hpos = strpos($page, "\r\n\r\n");
		$headers = empty($hpos) ? $page : substr($page, 0, $hpos);

		if (stripos($headers, "\nLocation: ") === false && stripos($headers, "\nSet-Cookie: ") === false && stripos($headers, '<script') !== false && !(cut_str($page, '<title>', '</title>'))) {
			if (empty($_GET['rgredir'])) {
				if (!($body = cut_str($page, '<body>', '</body>'))) $body = $page;
				if (stripos($body, '<script') !== strripos($body, '<script')) html_error('Unknown error while getting redirect code.');
				$login = ($_REQUEST['premium_acc'] == 'on' && (!empty($_REQUEST['premium_user']) && !empty($_REQUEST['premium_pass'])));
				$data = $this->DefaultParamArr($this->link, 0, rebuild_url($lasturl));
				$data['rgredir'] = '';
				$data['premium_acc'] = $_REQUEST['premium_acc']; // I should add 'premium_acc' to DefaultParamArr()
				if ($login) {
					$data['pA_encrypted'] = 'true';
					$data['premium_user'] = urlencode(encrypt($_REQUEST['premium_user'])); // encrypt() will keep this safe.
					$data['premium_pass'] = urlencode(encrypt($_REQUEST['premium_pass'])); // And this too.
				}
				if (!($js = cut_str($body, '<script language="JavaScript">', '</script>')) && !($js = cut_str($body, '<script type="text/javascript">', '</script>'))) html_error('Cannot get the redirect code.');
				$js = str_ireplace(array('window.location.href','document.location.href'), 'document.getElementById("rgredir").value', $js);
				if (strpos($js, 'document.body.onmousemove') !== false) { // New redirect code
					$js = preg_replace('@^[\s\t]*\w+\([^\;]+;@i', '', $js);
					$js = preg_replace('@document\.body\.onmousemove[\s\t]*=[\s\t]*function[\s\t]*\(\)[\s\t]*\{@i', '', $js);
					$js = preg_replace('@document\.body\.onmousemove[\s\t]*=[\s\t]*\'\';?\};[\s\t]*window\.setTimeout\([\s\t]*((\"[^\"]+\")|(\'[^\']+\'))[^\;]+;[\s\t\r\n]*$@i', '', $js);
				} elseif (($funcPos = stripos($js, 'function WriteA(')) !== false) { // JS + aaaaaaaaaaaaaaaaaaaaaaaaa
					$links = array();
					if (preg_match_all('@<a\s*[^>]*\shref="((?:https?://(?:www\.)?rapidgator\.net)?/[^\"]+)"[^>]*\sid="([A-Za-z][\w\.\-]*)"@i', $body, $a)) $links = array_merge($links, array_combine($a[2], $a[1]));
					if (preg_match_all('@<a\s*[^>]*\sid="([A-Za-z][\w\.\-]*)"[^>]*\shref="((?:https?://(?:www\.)?rapidgator\.net)?/[^\"]+)"@i', $body, $a)) $links = array_merge($links, array_combine($a[1], $a[2]));
					if (empty($links)) html_error('Cannot get the redirect fields');
					unset($a);

					$jsLinks = '';
					foreach ($links as $key => $link) {
						if (strpos($link, '://') === false) $link = (!empty($lasturl['scheme']) && strtolower($lasturl['scheme']) == 'https' ? 'https' : 'http').'://rapidgator.net' . $link;
						$jsLinks .= "$key: '".addslashes($link)."', ";
					}
					unset($links, $key, $link);
					$jsLinks = '{' . substr($jsLinks, 0, -2) . '}';
					$func = substr($js, $funcPos);
					if (!preg_match('@\.getElementById\(([\$_A-Za-z][\$\w]*)\)@i', $func, $linkVar)) html_error('Cannot edit redirect JS');
					$linkVar = $linkVar[1];
					unset($func);
					$js = substr($js, 0, $funcPos)."\nvar T8RGLinks = $jsLinks;\nif ($linkVar in T8RGLinks) document.getElementById('rgredir').value = T8RGLinks[$linkVar];";
					unset($jsLinks, $funcPos, $linkVar);
				}
				echo "\n<form name='rg_redir' action='{$_SERVER['SCRIPT_NAME']}' method='POST'><br />\n";
				foreach ($data as $name => $input) echo "<input type='hidden' name='$name' id='$name' value='" . htmlspecialchars($input, ENT_QUOTES) . "' />\n";
				echo "</form>\n<span id='T8_emsg' class='htmlerror' style='text-align:center;display:none;'></span>\n<noscript><span class='htmlerror'><b>Sorry, this code needs JavaScript enabled to work.</b></span></noscript>\n<script type='text/javascript'>/* <![CDATA[ Th3-822 */\n\tvar T8 = true;\n\ttry {{$js}\n\t} catch(e) {\n\t\t$('#T8_emsg').html('<b>Cannot decode challenge: ['+e.name+'] '+e.message+'</b>').show();\n\t\tT8 = false;\n\t}\n\tif (T8) window.setTimeout(\"$('form[name=rg_redir]').submit();\", 300); // 300 µs to make sure that the value was decoded and added.\n/* ]]> */</script>\n\n</body>\n</html>";
				exit;
			} else {
				$_GET['rgredir'] = rawurldecode($_GET['rgredir']);
				if (strpos($_GET['rgredir'], '://')) $_GET['rgredir'] = parse_url($_GET['rgredir'], PHP_URL_PATH);
				if (empty($_GET['rgredir']) || substr($_GET['rgredir'], 0, 1) != '/') html_error('Invalid redirect value.');
				$redir = (!empty($lasturl['scheme']) && strtolower($lasturl['scheme']) == 'https' ? 'https' : 'http') . '://rapidgator.net' . $_GET['rgredir'];
				unset($_GET['rgredir']);
			}
		} elseif (preg_match('@Location: ((https?://(?:[^/\r\n]+\.)?rapidgator\.net)?'.$rgpath.'[^\r\n]*)@i', $headers, $redir)) $redir = (empty($redir[2])) ? (!empty($lasturl['scheme']) && strtolower($lasturl['scheme']) == 'https' ? 'https' : 'http') . '://rapidgator.net' . $redir[1] : $redir[1];

		return (empty($redir) ? false : $redir);
	}
}

// [14-8-2012]  Written by Th3-822.
// [26-8-2012]  Fixed regexp on redirect code. -Th3-822
// [04-9-2012]  Added error msg in free dl. -Th3-822
// [09-9-2012]  Fixed redirect issues, more code added & small edits. -Th3-822
// [02-10-2012]  Fixed for new weird redirect code. - Th3-822
// [28-1-2013]  Added Login captcha support. - Th3-822
// [10-8-2013] Fixed redirects (again). - Th3-822
// [25-11-2013] Fixed redirects function (aagain :D ). - Th3-822
// [03-1-2014] Added support for rg.to domain. - Th3-822
// [24-3-2014] Fixed FreeDL. - Th3-822
// [20-5-2014] Fixed Login, bw error msg. - Th3-822
// [16-12-2015][WIP] Fixing Blocks, Redirect Handling & Forcing Plugin To Use cURL. - Th3-822
// [01-8-2016] Fixed FreeDL Captchas. - Th3-822
// [28-8-2017] Switched to HTTPs (Untested) - Th3-822