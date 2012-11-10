<?php
######### Account Info #########
$upload_acc['rapidgator_net']['user'] = ''; //Set your user
$upload_acc['rapidgator_net']['pass'] = ''; //Set your password
##########################

$_GET['proxy'] = !empty($proxy) ? $proxy : (!empty($_GET['proxy']) ? $_GET['proxy'] : '');
$not_done = true;

if ($upload_acc['rapidgator_net']['user'] && $upload_acc['rapidgator_net']['pass']) {
	$_REQUEST['up_login'] = $upload_acc['rapidgator_net']['user'];
	$_REQUEST['up_pass'] = $upload_acc['rapidgator_net']['pass'];
	$_REQUEST['action'] = 'FORM';
	echo "<b><center>Using Default Login.</center></b>\n";
}

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
	$not_done = false;
	$domain = 'rapidgator.net';
	$referer = "http://$domain/";

	// Login
	echo "<table style='width:600px;margin:auto;'>\n<tr><td align='center'>\n<div id='login' width='100%' align='center'>Login to $domain</div>\n";

	$cookie = array('lang' => 'en');
	if (!empty($_REQUEST['up_login']) && !empty($_REQUEST['up_pass'])) {
		// Decrypt login if it was encrypted
		if (!empty($_REQUEST['A_encrypted']) && !empty($_REQUEST['up_login']) && !empty($_REQUEST['up_pass'])) {
			$_REQUEST['up_login'] = decrypt(urldecode($_REQUEST['up_login']));
			$_REQUEST['up_pass'] = decrypt(urldecode($_REQUEST['up_pass']));
			unset($_REQUEST['A_encrypted']);
		}
		$post = array();
		$post['LoginForm%5Bemail%5D'] = $_REQUEST['up_login'];
		$post['LoginForm%5Bpassword%5D'] = $_REQUEST['up_pass'];
		$post['LoginForm%5BrememberMe%5D'] = 1;

		$page = geturl($domain, 80, '/auth/login', $referer, $cookie, $post, 0, $_GET['proxy'], $pauth, 0, 'https');is_page($page);
		$cookie = GetCookiesArr($page, $cookie);

		//Redirects
		$rdc = 0;
		while (($redir = ChkRGRedirs($page, true, ($upload_acc['rapidgator_net']['user'] && $upload_acc['rapidgator_net']['pass']))) && $rdc < 5) {
			$page = geturl($redir['host'], 80, $redir['path'].(!empty($redir['query']) ? '?'.$redir['query'] : ''), $referer, $cookie, $post, 0, $_GET['proxy'], $pauth, 0, $redir['scheme']);is_page($page);
			$cookie = GetCookiesArr($page, $cookie);
			$rdc++;
		}

		is_present($page, 'Error e-mail or password', 'Login Failed: Invalid Email or Password.');
		is_present($page, 'The code from a picture does not coincide', 'Login Failed: Captcha... (T8: I will add it later)');

		if (empty($cookie['user__'])) html_error("Login Error: Cannot find 'user__' cookie.");
		$cookie['lang'] = 'en';
		$login = true;
	} else {
		echo "<b><center>Login not found or empty, using non member upload.</center></b>\n";
		$login = false;
	}

	// Retrive upload ID
	echo "<script type='text/javascript'>document.getElementById('login').style.display='none';</script>\n<div id='info' width='100%' align='center'>Retrive upload ID</div>\n";

	$page = geturl($domain, 80, '/', $referer, $cookie, 0, 0, $_GET['proxy'], $pauth);is_page($page);
	if (!$login) {
		$cookie = GetCookiesArr($page, $cookie);

		//Redirects
		$rdc = 0;
		while (($redir = ChkRGRedirs($page)) && $rdc < 5) {
			$page = geturl($redir['host'], 80, $redir['path'].(!empty($redir['query']) ? '?'.$redir['query'] : ''), $referer, $cookie, 0, 0, $_GET['proxy'], $pauth);is_page($page);
			$cookie = GetCookiesArr($page, $cookie);
			$rdc++;
		}
	}
	if (!preg_match('@var\s+form_url\s*=\s*"(https?://[^/|\"]+/[^\"]+)"\s*;@i', $page, $form_url) || !preg_match('@var\s+progress_url_web\s*=\s*"(https?://[^/|\"]+/[^\"]+)"\s*;@i', $page, $prog_url)) html_error('Error: Cannot find upload url.', 0);

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

	$url = parse_url($prog_url[1]."&data%5B0%5D%5Buuid%5D=$uuid&data%5B0%5D%5Bstart_time%5D=$starttime");
	$page = geturl($url['host'], 80, $url['path'].(!empty($url['query']) ? '?'.$url['query'] : ''), $referer, $cookie, 0, 0, $_GET['proxy'], $pauth);is_page($page);

	$body = substr($page, strpos($page, "\r\n\r\n") + 4);
	if (!preg_match_all('@"([^\"]*)":"([^\"]*)"@i', $body, $resp)) html_error("Unknown reply from server.");
	$resp = array_combine($resp[1], array_map('stripcslashes', $resp[2]));

	if (!empty($resp['download_url'])) {
		$download_link = $resp['download_url'];
		if (!empty($resp['remove_url'])) $delete_link = $resp['remove_url'];
	} else html_error("Download link not found ({$resp['state']}).", 0);
}

// 4 RG: You don't have nothing to read here :D
function ChkRGRedirs($page, $login = false, $default_login = false) { // Edited for upload plugin usage.
	$hpos = strpos($page, "\r\n\r\n");
	$headers = empty($hpos) ? $page : substr($page, 0, $hpos);

	if (stripos($headers, "\r\nLocation: ") === false && stripos($page, "\r\nSet-Cookie: ") === false && !(cut_str($page, '<title>', '</title>'))) {
		if (empty($_REQUEST['rgredir'])) {
			if (!($body = cut_str($page, '<body>', '</body>'))) $body = $page;
			if (stripos($body, '<script') !== strripos($body, '<script')) html_error('Unknown error while getting redirect code.');
			$data = array('action' => 'FORM', 'rgredir' => '');
			if ($login && !$default_login) {
				$data['A_encrypted'] = 'true';
				$data['up_login'] = urlencode(encrypt($_REQUEST['up_login']));
				$data['up_pass'] = urlencode(encrypt($_REQUEST['up_pass']));
			}
			if (!($js = cut_str($body, '<script language="JavaScript">', '</script>')) && !($js = cut_str($body, '<script type="text/javascript">', '</script>'))) html_error('Cannot get the redirect code.');
			$js = str_ireplace(array('window.location.href','document.location.href'), 'document.getElementById("rgredir").value', $js);
			if (stripos($js, 'document.body.onmousemove') !== false) { // New redirect code
				$js = preg_replace('@^[\s\t]*\w+\([^\;]+;@i', '', $js);
				$js = preg_replace('@document\.body\.onmousemove[\s\t]*=[\s\t]*function[\s\t]*\(\)[\s\t]*\{@i', '', $js);
				$js = preg_replace('@document\.body\.onmousemove[\s\t]*=[\s\t]*\'\';?\};[\s\t]*window\.setTimeout\([\s\t]*((\"[^\"]+\")|(\'[^\']+\'))[^\;]+;[\s\t\r\n]*$@i', '', $js);
			}
			echo "\n<form name='rg_redir' method='POST'><br />\n";
			foreach ($data as $name => $input) echo "<input type='hidden' name='$name' id='$name' value='$input' />\n";
			echo "<noscript><span class='htmlerror'><b>Sorry, this code needs JavaScript enabled to work.</b></span></noscript><br />";
			echo "</form>\n<script type='text/javascript'>/* <![CDATA[ */\n$js\nwindow.setTimeout(\"$('form[name=rg_redir]').submit();\", 300); // 300 µs to make sure that the value was decoded and added.\n/* ]]> */</script>\n\n</body>\n</html>";
			exit;
		} else {
			$_REQUEST['rgredir'] = rawurldecode($_REQUEST['rgredir']);
			if (strpos($_REQUEST['rgredir'], '://')) $_REQUEST['rgredir'] = parse_url($_REQUEST['rgredir'], PHP_URL_PATH);
			if (empty($_REQUEST['rgredir']) || substr($_REQUEST['rgredir'], 0, 1) != '/') html_error('Invalid redirect value.');
			$redir = 'http://rapidgator.net'.$_REQUEST['rgredir'];
		}
	} elseif (preg_match('@Location: ((https?://(?:[^/|\r|\n]+\.)?rapidgator\.net)?'.($login ? '/auth/login' : '/').'[^\r|\n]*)@i', $headers, $redir)) $redir = (empty($redir[2])) ? 'http://rapidgator.net'.$redir[1] : $redir[1];

	return (empty($redir) ? false : parse_url($redir));
}

// [09-9-2012] Written by Th3-822.
// [02-10-2012] Fixed for new weird redirect code. - Th3-822
// [31-10-2012] Fixed for https on login/redirects. - Th3-822

?>