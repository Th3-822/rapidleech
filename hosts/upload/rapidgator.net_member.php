<?php
######### Account Info #########
$upload_acc['rapidgator_net']['user'] = ''; //Set your user
$upload_acc['rapidgator_net']['pass'] = ''; //Set your password
##########################

$_GET['proxy'] = !empty($proxy) ? $proxy : (!empty($_GET['proxy']) ? $_GET['proxy'] : '');
$not_done = true;

// This Plugin Requires cURL + HTTPs support
$use_curl = extension_loaded('curl') && function_exists('curl_init') && function_exists('curl_exec') ? true : false;
if ($use_curl) {
	$cV = curl_version();
	if (!in_array('https', $cV['protocols'], true)) html_error('This plugin requires cURL + HTTPs support. (Lacks HTTPs support)');
} else html_error('This plugin requires cURL + HTTPs support. (cURL is disabled)');

if ($upload_acc['rapidgator_net']['user'] && $upload_acc['rapidgator_net']['pass']) {
	$default_acc = true;
	$_REQUEST['up_login'] = $upload_acc['rapidgator_net']['user'];
	$_REQUEST['up_pass'] = $upload_acc['rapidgator_net']['pass'];
	$_REQUEST['action'] = 'FORM';
	echo "<b><center>Using Default Login.</center></b>\n";
} else $default_acc = false;

if (empty($_REQUEST['action']) || $_REQUEST['action'] != 'FORM') {
	echo "<table border='0' style='width:270px;' cellspacing='0' align='center'>
	<form method='POST'>
	<input type='hidden' name='action' value='FORM' />
	<tr><td style='white-space:nowrap;'>&nbsp;Email*</td><td>&nbsp;<input type='text' name='up_login' value='' style='width:160px;' /></td></tr>
	<tr><td style='white-space:nowrap;'>&nbsp;Password*</td><td>&nbsp;<input type='password' name='up_pass' value='' style='width:160px;' /></td></tr>\n";
	echo "<tr><td colspan='2' align='center'><br /><input type='submit' value='Upload' /></td></tr>\n";
	echo "<tr><td colspan='2' align='center'><small>*You can set it as default in <b>".basename(__FILE__)."</b></small></td></tr>\n";
	echo "</table>\n</form>\n";
} else {
	$not_done = $login = false;
	$domain = 'rapidgator.net';
	$referer = "http://$domain/";

	// Login
	echo "<table style='width:600px;margin:auto;'>\n<tr><td align='center'>\n<div id='login' width='100%' align='center'>Login to $domain</div>\n";

	$cookie = array('lang' => 'en');
	if (!empty($_REQUEST['up_login']) && !empty($_REQUEST['up_pass'])) {
		// Decrypt login if it was encrypted
		if (!empty($_REQUEST['A_encrypted'])) {
			$_REQUEST['up_login'] = decrypt(urldecode($_REQUEST['up_login']));
			$_REQUEST['up_pass'] = decrypt(urldecode($_REQUEST['up_pass']));
			unset($_REQUEST['A_encrypted']);
		}

		$post = array();
		$post['LoginForm%5Bemail%5D'] = urlencode($_REQUEST['up_login']);
		$post['LoginForm%5Bpassword%5D'] = urlencode($_REQUEST['up_pass']);
		$post['LoginForm%5BrememberMe%5D'] = 1;
		if (!empty($_POST['step']) && $_POST['step'] == '1') {
			if (empty($_POST['captcha'])) html_error('You didn\'t enter the image verification code.');
			$cookie = StrToCookies(decrypt(urldecode($_POST['cookie'])));
			$post['LoginForm%5BverifyCode%5D'] = urlencode($_POST['captcha']);
		}

		//Redirects
		$rdc = 0;
		$page = false; // False value for starting the loop.
		$redir = "http://$domain/auth/login";
		if (!empty($_POST['referer'])) $referer = $_POST['referer'];
		while (($redir = ChkRGRedirs($page, $redir, '/auth/login', $default_acc)) && $rdc < 15) {
			$page = cURL($redir, $cookie, $post, $referer);
			$cookie = GetCookiesArr($page, $cookie);
			$referer = $redir;
			$rdc++;
		}

		is_present($page, 'Error e-mail or password', 'Login Failed: Invalid Email or Password.');
		is_present($page, 'E-mail is not a valid email address.', 'Login Failed: Login isn\'t an email address.');
		if (stripos($page, 'The code from a picture does not coincide') !== false) {
			if (!empty($_POST['step']) && $_POST['step'] == '1') html_error('Login Failed: Incorrect CAPTCHA response.');
			if (!preg_match('@(https?://(?:[^\./\r\n\'\"\t\:]+\.)?rapidgator\.net(?:\:\d+)?)?/auth/captcha/\w+/\w+@i', $page, $imgurl)) html_error('Error: CAPTCHA not found.');
			$imgurl = (empty($imgurl[1])) ? 'http://rapidgator.net'.$imgurl[0] : $imgurl[0];
			//Download captcha img.
			$capt_page = cURL($imgurl, $cookie, 0, $referer);
			$capt_img = substr($capt_page, strpos($capt_page, "\r\n\r\n") + 4);
			$imgfile = DOWNLOAD_DIR . 'rapidgator_captcha.png';

			if (file_exists($imgfile)) unlink($imgfile);
			if (!write_file($imgfile, $capt_img)) html_error('Error getting CAPTCHA image.');
			unset($capt_page, $capt_img);

			$data = array();
			$data['step'] = '1';
			$data['cookie'] = urlencode(encrypt(CookiesToStr($cookie)));
			$data['action'] = 'FORM'; // I should add 'premium_acc' to DefaultParamArr()
			if (!$default_acc) {
				$data['A_encrypted'] = 'true';
				$data['up_login'] = urlencode(encrypt($_REQUEST['up_login'])); // encrypt() will keep this safe.
				$data['up_pass'] = urlencode(encrypt($_REQUEST['up_pass'])); // And this too.
			}
			EnterCaptcha($imgfile.'?'.time(), $data);
			exit;
		}
		//is_present($page, 'The code from a picture does not coincide', 'Login Failed: Captcha... (T8: I will add it later)');
		is_present($page, 'The code from a picture does not coincide', 'Login Failed: Captcha... (T8: I will add it later)');

		if (empty($cookie['user__'])) html_error("Login Error: Cannot find 'user__' cookie.");
		$cookie['lang'] = 'en';
		$login = true;
	} else html_error('Login failed: User/Password empty.');

	// Retrive upload ID
	echo "<script type='text/javascript'>document.getElementById('login').style.display='none';</script>\n<div id='info' width='100%' align='center'>Retrive upload ID</div>\n";

	//Redirects
	$rdc = 0;
	$page = false; // False value for starting the loop.
	$redir = "http://$domain/";
	while (($redir = ChkRGRedirs($page, $redir)) && $rdc < 15) {
		$page = cURL($redir, $cookie, 0, $referer);
		$cookie = GetCookiesArr($page, $cookie);
		$referer = $redir;
		$rdc++;
	}

	if (!preg_match('@var\s+form_url\s*=\s*"(https?://[^/|\"]+/[^\"]+)"\s*;@i', $page, $form_url) || !preg_match('@var\s+progress_url_web\s*=\s*"(https?://[^/|\"]+/[^\"]+)"\s*;@i', $page, $prog_url)) {
		is_present($page, 'Your storage space is full. Delete some files or upgrade to the new', 'Your storage space is full');
		html_error('Error: Cannot find upload url.', 0);
	}

	$starttime = time();
	$uuid = '';
	$hexchars = str_split('0123456789abcdef');
	for ($i = 0; $i < 32; $i++) $uuid .= $hexchars[array_rand($hexchars)];

	$up_url = $form_url[1]."$uuid&folder_id=0";

	// Uploading
	echo "<script type='text/javascript'>document.getElementById('info').style.display='none';</script>\n";

	$url = parse_url($up_url);
	$upfiles = upfile($url['host'], 80, $url['path'].(!empty($url['query']) ? '?'.$url['query'] : ''), $referer, $cookie, array(), $lfile, $lname, 'file', '', $_GET['proxy'], $pauth);

	// Upload Finished
	echo "<script type='text/javascript'>document.getElementById('progressblock').style.display='none';</script>";

	is_page($upfiles);

	//Redirects
	$rdc = 0;
	$page = false; // False value for starting the loop.
	$redir = $prog_url[1]."&data%5B0%5D%5Buuid%5D=$uuid&data%5B0%5D%5Bstart_time%5D=$starttime";
	while (($redir = ChkRGRedirs($page, $redir)) && $rdc < 15) {
		$page = cURL($redir, $cookie, 0, $referer);
		$cookie = GetCookiesArr($page, $cookie);
		$referer = $redir;
		$rdc++;
	}

	$body = substr($page, strpos($page, "\r\n\r\n") + 4);
	if (!preg_match_all('@"([^\"]*)":"([^\"]*)"@i', $body, $resp)) html_error("Unknown reply from server.");
	$resp = array_combine($resp[1], array_map('stripcslashes', $resp[2]));

	if (!empty($resp['download_url'])) {
		$download_link = $resp['download_url'];
		if (!empty($resp['remove_url'])) $delete_link = $resp['remove_url'];
	} else html_error("Download link not found ({$resp['state']}).", 0);
}

// Edited For upload.php usage.
function EnterCaptcha($captchaImg, $inputs, $captchaSize = '5') {
	echo "\n<form name='captcha' method='POST'>\n";
	foreach ($inputs as $name => $input) echo "\t<input type='hidden' name='$name' id='$name' value='$input' />\n";
	echo "\t<h4>" . lang(301) . " <img alt='CAPTCHA Image' src='$captchaImg' /> " . lang(302) . ": <input type='text' name='captcha' size='$captchaSize' />&nbsp;&nbsp;\n\t\t<input type='submit' onclick='return check();' value='Enter Captcha' />\n\t</h4>\n\t<script type='text/javascript'>/* <![CDATA[ */\n\t\tfunction check() {\n\t\t\tvar captcha=document.dl.captcha.value;\n\t\t\tif (captcha == '') {\n\t\t\t\twindow.alert('You didn\'t enter the image verification code');\n\t\t\t\treturn false;\n\t\t\t} else return true;\n\t\t}\n\t/* ]]> */</script>\n</form>\n</body>\n</html>";
}

function ChkRGRedirs($page, $lasturl, $rgpath = '/', $default_login = false) { // Edited for upload plugin usage.
	if (!is_array($lasturl)) $lasturl = parse_url($lasturl);
	if ($page === false) return $lasturl;
	$hpos = strpos($page, "\r\n\r\n");
	$headers = empty($hpos) ? $page : substr($page, 0, $hpos);

	if (stripos($headers, "\nLocation: ") === false && stripos($headers, "\nSet-Cookie: ") === false && !(cut_str($page, '<title>', '</title>'))) {
		if (empty($_REQUEST['rgredir'])) {
			if (!($body = cut_str($page, '<body>', '</body>'))) $body = $page;
			if (stripos($body, '<script') !== strripos($body, '<script')) html_error('Unknown error while getting redirect code.');
			$data = array('action' => 'FORM', 'referer' => $lasturl, 'rgredir' => '');
			if (!$default_login) {
				$data['A_encrypted'] = 'true';
				$data['up_login'] = urlencode(encrypt($_REQUEST['up_login']));
				$data['up_pass'] = urlencode(encrypt($_REQUEST['up_pass']));
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
			echo "\n<form name='rg_redir' method='POST'><br />\n";
			foreach ($data as $name => $input) echo "<input type='hidden' name='$name' id='$name' value='" . htmlspecialchars($input, ENT_QUOTES) . "' />\n";
			echo "</form>\n<span id='T8_emsg' class='htmlerror' style='text-align:center;display:none;'></span>\n<noscript><span class='htmlerror'><b>Sorry, this code needs JavaScript enabled to work.</b></span></noscript>\n<script type='text/javascript'>/* <![CDATA[ Th3-822 */\n\tvar T8 = true;\n\ttry {{$js}\n\t} catch(e) {\n\t\t$('#T8_emsg').html('<b>Cannot decode challenge: ['+e.name+'] '+e.message+'</b>').show();\n\t\tT8 = false;\n\t}\n\tif (T8) window.setTimeout(\"$('form[name=rg_redir]').submit();\", 300); // 300 µs to make sure that the value was decoded and added.\n/* ]]> */</script>\n\n</body>\n</html>";
			exit;
		} else {
			$_REQUEST['rgredir'] = rawurldecode($_REQUEST['rgredir']);
			if (strpos($_REQUEST['rgredir'], '://')) $_REQUEST['rgredir'] = parse_url($_REQUEST['rgredir'], PHP_URL_PATH);
			if (empty($_REQUEST['rgredir']) || substr($_REQUEST['rgredir'], 0, 1) != '/') html_error('Invalid redirect value.');
			$redir = (!empty($lasturl['scheme']) && strtolower($lasturl['scheme']) == 'https' ? 'https' : 'http').'://rapidgator.net'.$_REQUEST['rgredir'];
			unset($_REQUEST['rgredir']);
		}
	} elseif (preg_match('@Location: ((https?://(?:[^/\r\n]+\.)?rapidgator\.net)?'.$rgpath.'[^\r\n]*)@i', $headers, $redir)) $redir = (empty($redir[2])) ? (!empty($lasturl['scheme']) && strtolower($lasturl['scheme']) == 'https' ? 'https' : 'http').'://rapidgator.net'.$redir[1] : $redir[1];

	return (empty($redir) ? false : $redir);
}

// [09-9-2012] Written by Th3-822.
// [02-10-2012] Fixed for new weird redirect code. - Th3-822
// [31-10-2012] Fixed for https on login/redirects. - Th3-822
// [28-1-2013] Added Login captcha support. - Th3-822
// [14-6-2013] Removed https from first login try, for avoid block. - Th3-822
// [10-8-2013] Fixed redirects (again). - Th3-822
// [05-10-2013] Removed anon user support. - Th3-822
// [25-11-2013] Fixed redirects function (aagain :D ). - Th3-822
// [16-11-2015][WIP] Fixing Blocks, Redirect Handling & Forcing Plugin To Use cURL. - Th3-822

?>