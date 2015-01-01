<?php

if (!defined('RAPIDLEECH')) {
	require_once ('index.html');
	exit();
}

class rapidgator_net extends DownloadClass {
	private $page, $link, $cookie;
	public function Download($link) {
		global $premium_acc;
		$this->link = $GLOBALS['Referer'] = str_ireplace(array('://www.rg.to/', '://rg.to/'), '://rapidgator.net/', $link);
		$this->cookie = array('lang' => 'en');
		$this->DLregexp = '@https?://pr\d+\.rapidgator\.net/[^\s\"\'<>]+@i';
		if (empty($_POST['step']) || $_POST['step'] != '1') {
			$this->page = $this->GetPage($this->link, $this->cookie);
			$this->cookie = GetCookiesArr($this->page, $this->cookie);

			// Sometimes i see a couple of redirects that don't let do anything without doing them first, let skip them.
			$rdc = 0;
			$redir = $link;
			while (($redir = $this->ChkRGRedirs($this->page, parse_url($redir))) && $rdc < 5) {
				$this->page = $this->GetPage($redir, $this->cookie);
				$this->cookie = GetCookiesArr($this->page, $this->cookie);
				$rdc++;
			}

			// I haven't tested those redirects fine so i will check this too.
			if (!empty($redir) && stripos($redir, 'rapidgator.net/file/') === false) {
				$this->page = $this->GetPage($this->link, $this->cookie);
				$this->cookie = GetCookiesArr($this->page, $this->cookie);
			}

			is_present($this->page, '>File not found<', 'File not found.');
		}

		if ($_REQUEST['premium_acc'] == 'on' && ((!empty($_REQUEST['premium_user']) && !empty($_REQUEST['premium_pass'])) || (!empty($premium_acc['rapidgator_net']['user']) && !empty($premium_acc['rapidgator_net']['pass'])))) {
			$this->Login();
		} else $this->FreeDL();
	}

	private function FreeDL() {
		$capt_url = 'http://rapidgator.net/download/captcha';
		if (empty($_POST['step']) || $_POST['step'] != '1') {
			is_present($this->page, 'This file can be downloaded by premium only', 'Only Premium users can download this file.'); // F##### PPS
			is_present($this->page, 'download not more than 1 file at a time in free mode.', 'You can\'t download not more than 1 file at a time. Wait 15 minutes and try again.');
			if (preg_match('@You can download files up to \d+ MB in free mode@i', $this->page, $err)) html_error($err[0].'.');
			if (!preg_match('@fid\s*=\s*(\d+)\s*;@i', $this->page, $fid)) html_error('File-id not found.');
			if (!preg_match('@secs\s*=\s*(\d+)\s*;@i', $this->page, $cd)) html_error('Countdown not found.');

			$page = $this->GetPage('http://rapidgator.net/download/AjaxStartTimer?fid='.$fid[1], $this->cookie, 0, $this->link."\r\nX-Requested-With: XMLHttpRequest");
			if (!preg_match('@"sid":"([^"\}]+)"@i', $page, $sid)) html_error('Session id not found.');

			if ($cd[1] > 0) $this->CountDown($cd[1]);

			$page = $this->GetPage('http://rapidgator.net/download/AjaxGetDownloadLink?sid='.urlencode($sid[1]), $this->cookie, 0, $this->link."\r\nX-Requested-With: XMLHttpRequest"); // ['download_link']
			$this->cookie = GetCookiesArr($page, $this->cookie);
			is_notpresent($page, '"state":"done"', 'Error: Countdown bypassed?.');

			$page = $this->GetPage($capt_url, $this->cookie);

			if (preg_match($this->DLregexp, $page, $dlink)) return $this->RedirectDownload($dlink[0], 'rapidgatorfr2');

			if (!preg_match('@https?://api\.solvemedia\.com/papi/challenge\.noscript\?k=\w+@i', $page, $cframe)) html_error('CAPTCHA not found.');
			$page = $this->GetPage($cframe[0], 0, 0, $capt_url);
			if (!preg_match('@<img [^/<>]*src\s?=\s?\"((https?://[^/\"\<\>]+)?/papi/media[^\"<>]+)\"@i', $page, $imgurl)) html_error('CAPTCHA img not found.');
			$imgurl = (empty($imgurl[2])) ? 'http://api.solvemedia.com'.$imgurl[1] : $imgurl[1];

			if (!preg_match_all('@<input [^/<>]*type\s?=\s?\"?hidden\"?[^/<>]*\s?name\s?=\s?\"(\w+)\"[^/<>]*\s?value\s?=\s?\"([^\"<>]+)\"[^/<>]*/?\s*>@i', $page, $forms)) html_error('CAPTCHA data not found.');
			$forms = array_combine($forms[1], $forms[2]);

			$data = $this->DefaultParamArr($this->link, $this->cookie);
			$data['step'] = 1;
			foreach ($forms as $n => $v) $data["T8[$n]"] = urlencode($v);

			//Download captcha img.
			$page = $this->GetPage($imgurl, $this->cookie);
			$capt_img = substr($page, strpos($page, "\r\n\r\n") + 4);
			$imgfile = DOWNLOAD_DIR . 'rapidgator_captcha.gif';

			if (file_exists($imgfile)) unlink($imgfile);
			if (!write_file($imgfile, $capt_img)) html_error('Error getting CAPTCHA image.');

			$this->EnterCaptcha($imgfile.'?'.time(), $data, 20);
			exit();
		} else {
			if (empty($_POST['captcha'])) html_error('You didn\'t enter the image verification code.');
			$this->cookie = (!empty($_POST['cookie'])) ? urldecode($_POST['cookie']) : '';

			$post = array();
			foreach ($_POST['T8'] as $n => $v) $post[urlencode($n)] = $v;
			$post['adcopy_response'] = $_POST['captcha'];

			$url = 'http://api.solvemedia.com/papi/verify.noscript';
			$page = $this->GetPage($url, 0, $post, $capt_url);

			if (!preg_match('@(https?://[^/\'\"\<\>\r\n]+)?/papi/verify\.pass\.noscript\?[^/\'\"\<\>\r\n]+@i', $page, $resp)) {
				is_present($page, '/papi/challenge.noscript', 'Wrong CAPTCHA entered.');
				html_error('Error sending CAPTCHA.');
			}
			$resp = (empty($resp[1])) ? 'http://api.solvemedia.com'.$resp[0] : $resp[0];

			$page = $this->GetPage($resp, 0, 0, $url);
			if (!preg_match('@>[\s\t\r\n]*([^<>\r\n]+)[\s\t\r\n]*</textarea>@i', $page, $gibberish)) html_error('CAPTCHA response not found.');

			$post = array('DownloadCaptchaForm%5Bcaptcha%5D' => '', 'adcopy_challenge' => urlencode($gibberish[1]), 'adcopy_response' => 'manual_challenge');
			$page = $this->GetPage($capt_url, $this->cookie, $post);

			is_present($page, "\r\nSet-Cookie: failed_on_captcha=1", 'Captcha expired. Try again in 15 minutes.');

			if (!preg_match($this->DLregexp, $page, $dlink)) html_error('Error: Download link not found.');
			$this->RedirectDownload($dlink[0], 'rapidgatorfr');
		}
	}

	private function PremiumDL() {
		$page = $this->GetPage($this->link, $this->cookie);

		if (preg_match('@You have reached quota of downloaded information for premium accounts. At the moment, the quota is \d+ [GT]B(?: per \d+ day\(s\))?@i', $page, $err)) html_error($err[0]);

		if (!preg_match($this->DLregexp, $page, $dlink)) html_error('Error: Download-link not found.');

		$this->RedirectDownload($dlink[0], 'rapidgatorpr');
	}

	private function Login() {
		global $premium_acc;
		if (!empty($_REQUEST['pA_encrypted']) && !empty($_REQUEST['premium_user']) && !empty($_REQUEST['premium_pass'])) {
			$_REQUEST['premium_user'] = decrypt(urldecode($_REQUEST['premium_user']));
			$_REQUEST['premium_pass'] = decrypt(urldecode($_REQUEST['premium_pass']));
			unset($_REQUEST['pA_encrypted']);
		}
		$pA = (empty($_REQUEST['premium_user']) || empty($_REQUEST['premium_pass']) ? false : true);
		$user = ($pA ? $_REQUEST['premium_user'] : $premium_acc['rapidgator_net']['user']);
		$pass = ($pA ? $_REQUEST['premium_pass'] : $premium_acc['rapidgator_net']['pass']);

		if (empty($user) || empty($pass)) html_error('Login Failed: User or Password is empty. Please check login data.', 0);
		$this->cookie = array('lang' => 'en'); // Account is always showed as free if it comes from a file, as i don't send file's link as referer, lets reset the cookies.

		$post = array();
		$post['LoginForm%5Bemail%5D'] = urlencode($user);
		$post['LoginForm%5Bpassword%5D'] = urlencode($pass);
		$post['LoginForm%5BrememberMe%5D'] = '1';
		if (!empty($_POST['step']) && $_POST['step'] == '1') {
			if (empty($_POST['captcha'])) html_error('You didn\'t enter the image verification code.');
			$this->cookie = StrToCookies(decrypt(urldecode($_POST['cookie'])));
			$post['LoginForm%5BverifyCode%5D'] = urlencode($_POST['captcha']);
		}

		$purl = 'http://rapidgator.net/';
		$page = $this->GetPage($purl.'auth/login', $this->cookie, $post, $purl);
		$this->cookie = GetCookiesArr($page, $this->cookie);

		// There are more of those redirects at login
		$rdc = 0;
		$redir = $purl.'auth/login';
		while (($redir = $this->ChkRGRedirs($page, parse_url($redir), '/auth/login')) && $rdc < 5) {
			$page = $this->GetPage($redir, $this->cookie, $post, $purl);
			$this->cookie = GetCookiesArr($page, $this->cookie);
			$rdc++;
		}

		is_present($page, 'Error e-mail or password.', 'Login Failed: Email/Password incorrect.');
		is_present($page, 'E-mail is not a valid email address.', 'Login Failed: Login isn\'t an email address.');
		if (stripos($page, 'The code from a picture does not coincide') !== false) {
			if (!empty($_POST['step']) && $_POST['step'] == '1') html_error('Login Failed: Incorrect CAPTCHA response.');
			if (!preg_match('@(https?://(?:[^\./\r\n\'\"\t\:]+\.)?rapidgator\.net(?:\:\d+)?)?/auth/captcha/\w+/\w+@i', $page, $imgurl)) html_error('Error: CAPTCHA not found.');
			$imgurl = (empty($imgurl[1])) ? 'http://rapidgator.net'.$imgurl[0] : $imgurl[0];
			//Download captcha img.
			$capt_page = $this->GetPage($imgurl, $this->cookie);
			$capt_img = substr($capt_page, strpos($capt_page, "\r\n\r\n") + 4);
			$imgfile = DOWNLOAD_DIR . 'rapidgator_captcha.png';

			if (file_exists($imgfile)) unlink($imgfile);
			if (!write_file($imgfile, $capt_img)) html_error('Error getting CAPTCHA image.');
			unset($capt_page, $capt_img);

			$data = $this->DefaultParamArr($this->link, encrypt(CookiesToStr($this->cookie)));
			$data['step'] = '1';
			$data['premium_acc'] = 'on'; // I should add 'premium_acc' to DefaultParamArr()
			if ($pA) {
				$data['pA_encrypted'] = 'true';
				$data['premium_user'] = urlencode(encrypt($user)); // encrypt() will keep this safe.
				$data['premium_pass'] = urlencode(encrypt($pass)); // And this too.
			}
			$this->EnterCaptcha($imgfile.'?'.time(), $data);
			exit;
		}
		//is_present($page, 'The code from a picture does not coincide', 'Login Failed: Captcha... (T8: I will add it later)');

		if (empty($this->cookie['user__'])) html_error("Login Error: Cannot find 'user__' cookie.");
		$this->cookie['lang'] = 'en';

		$page = $this->GetPage($purl, $this->cookie, 0, $purl.'auth/login');
		is_present($page, '>Free</a>', 'Account isn\'t premium');

		$this->PremiumDL();
	}

	private function ChkRGRedirs($page, $lasturl, $rgpath = '/') {
		$hpos = strpos($page, "\r\n\r\n");
		$headers = empty($hpos) ? $page : substr($page, 0, $hpos);

		if (stripos($headers, "\nLocation: ") === false && stripos($headers, "\nSet-Cookie: ") === false && !(cut_str($page, '<title>', '</title>'))) {
			if (empty($_GET['rgredir'])) {
				global $PHP_SELF;
				if (!($body = cut_str($page, '<body>', '</body>'))) $body = $page;
				if (stripos($body, '<script') !== strripos($body, '<script')) html_error('Unknown error while getting redirect code.');
				$login = ($_REQUEST['premium_acc'] == 'on' && (!empty($_REQUEST['premium_user']) && !empty($_REQUEST['premium_pass'])));
				$data = $this->DefaultParamArr($this->link);
				$data['rgredir'] = $pform = $sform = '';
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
				echo "\n<form name='rg_redir' action='$PHP_SELF' method='POST'><br />\n";
				foreach ($data as $name => $input) echo "<input type='hidden' name='$name' id='$name' value='$input' />\n";
				echo "<noscript><span class='htmlerror'><b>Sorry, this code needs JavaScript enabled to work.</b></span></noscript><br />";
				echo "</form>\n<script type='text/javascript'>/* <![CDATA[Th3-822 */\n$js\nwindow.setTimeout(\"$('form[name=rg_redir]').submit();\", 300); // 300 µs to make sure that the value was decoded and added.\n/* ]]> */</script>\n\n</body>\n</html>";
				exit;
			} else {
				$_GET['rgredir'] = rawurldecode($_GET['rgredir']);
				if (strpos($_GET['rgredir'], '://')) $_GET['rgredir'] = parse_url($_GET['rgredir'], PHP_URL_PATH);
				if (empty($_GET['rgredir']) || substr($_GET['rgredir'], 0, 1) != '/') html_error('Invalid redirect value.');
				$redir = (!empty($lasturl['scheme']) && strtolower($lasturl['scheme']) == 'https' ? 'https' : 'http').'://rapidgator.net'.$_GET['rgredir'];
			}
		} elseif (preg_match('@Location: ((https?://(?:[^/\r\n]+\.)?rapidgator\.net)?'.$rgpath.'[^\r\n]*)@i', $headers, $redir)) $redir = (empty($redir[2])) ? (!empty($lasturl['scheme']) && strtolower($lasturl['scheme']) == 'https' ? 'https' : 'http').'://rapidgator.net'.$redir[1] : $redir[1];

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

?>