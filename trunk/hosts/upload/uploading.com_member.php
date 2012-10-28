<?php
######## Account Info ########
$upload_acc['uploading_com']['user'] = ''; //Set your email
$upload_acc['uploading_com']['pass'] = ''; //Set your password
########################

$_GET['proxy'] = isset($_GET['proxy']) ? $_GET['proxy'] : '';
$not_done = true;
$continue_up = false;

if ($upload_acc['uploading_com']['user'] && $upload_acc['uploading_com']['pass']) {
	$_REQUEST['up_login'] = $upload_acc['uploading_com']['user'];
	$_REQUEST['up_pass'] = $upload_acc['uploading_com']['pass'];
	$_REQUEST['action'] = 'FORM';
	echo "<b><center>Using Default Login.</center></b>\n";
}

if (!empty($_REQUEST['action']) && $_REQUEST['action'] == 'FORM') $continue_up = true;
else {
	echo "<table border='0' style='width:270px;' cellspacing='0' align='center'>
	<form method='POST'>
	<input type='hidden' name='action' value='FORM' />
	<tr><td style='white-space:nowrap;'>&nbsp;Email*</td><td>&nbsp;<input type='text' name='up_login' value='' style='width:160px;' /></td></tr>
	<tr><td style='white-space:nowrap;'>&nbsp;Password*</td><td>&nbsp;<input type='password' name='up_pass' value='' style='width:160px;' /></td></tr>\n";
	echo "<tr><td colspan='2' align='center'><br /><input type='submit' value='Upload' /></td></tr>\n";
	echo "<tr><td colspan='2' align='center'><small>*You can set it as default in <b>".basename(__FILE__)."</b></small></td></tr>\n";
	echo "</table>\n</form>\n";
}

if ($continue_up) {
	$not_done = false;
	$referer = 'http://uploading.com/';
	$captcha = (isset($_REQUEST['step']) && $_REQUEST['step'] == 'captcha') ? true : false;

	// Login
	echo "<table style='width:600px;margin:auto;'>\n<tr><td align='center'>\n<div id='login' width='100%' align='center'>Login to uploading.com</div>\n";

	$cookie = array();
	if (!empty($_REQUEST['up_login']) && !empty($_REQUEST['up_pass'])) {
		if ($captcha && empty($_POST['recaptcha_response_field'])) html_error("You didn't enter the image verification code.");
		$page = geturl('uploading.com', 80, '/', '', $cookie, 0, 0, $_GET['proxy'], $pauth);is_page($page);
		$cookie = GetCookiesArr($page, $cookie);
		if (empty($cookie['SID'])) html_error('Login Error: SID cookie not found.');

		$post = array();
		$post['email'] = $_REQUEST['up_login'];
		$post['password'] = $_REQUEST['up_pass'];
		$post['remember'] = 'on';
		if ($captcha) {
			$post['recaptcha_challenge_field'] = $_POST['recaptcha_challenge_field'];
			$post['recaptcha_response_field'] = $_POST['recaptcha_response_field'];
		}
		//$post['back_url'] = 'http://uploading.com/';

		$ajax_req = geturl('uploading.com', 80, '/general/login_form/?ajax', $referer."\r\nX-Requested-With: XMLHttpRequest", $cookie, $post, 0, $_GET['proxy'], $pauth);is_page($ajax_req);
		$json = Get_Reply($ajax_req);
		if (!empty($json['error'])) html_error('Login Error... MSG: ' . $json['error']);
		if (!empty($json['answer']) && isset($json['answer']['captcha']) && $json['answer']['captcha'] == true) {
			if (!preg_match('@build_recaptcha\s*\([^,|\)]+,\s*[\'|\"](\w+)[\'|\"]\s*\)@i', $page, $pid)) html_error('Error: Login captcha data not found.');
			$data = array('action' => 'FORM', 'step' => 'captcha');
			Show_reCaptcha($pid[1], $data, ($upload_acc['uploading_com']['user'] && $upload_acc['uploading_com']['pass']));
			exit;
		}
		$cookie = GetCookiesArr($ajax_req, $cookie);
		// if (empty($cookie['SID'])) html_error('Login Error: SID cookie not found.');
		if (empty($cookie['u']) || empty($cookie['remembered_user'])) html_error('Login Error: Login cookies not found.'); // Yes.... u=1 is needed. :D
	} else html_error("Login Failed: Email or Password are empty. Please check login data.", 0);

	// Retrive upload ID
	echo "<script type='text/javascript'>document.getElementById('login').style.display='none';</script>\n<div id='info' width='100%' align='center'>Retrive upload ID</div>\n";

	$page = geturl('uploading.com', 80, '/', $referer, $cookie, 0, 0, $_GET['proxy'], $pauth);is_page($page);
	if (!preg_match('@upload_url[\s|\t]*:[\s|\t|\r|\n]*[\'|\"](https?://([^\|\'|\"|\r|\n|\s|\t]+\.)?uploading\.com/[^\'|\"|\r|\n|\s|\t]+)@i', $page, $up)) html_error('Error: Cannot find upload server.', 0);

	$post = array('name' => urlencode($lname), 'size' => getSize($lfile));
	$page = geturl('uploading.com', 80, '/files/generate/?ajax', $referer."\r\nX-Requested-With: XMLHttpRequest", $cookie, $post, 0, $_GET['proxy'], $pauth);is_page($page);
	$json = Get_Reply($page);
	if (empty($json['file']['file_id'])) html_error('Upload id not found.');
	if (empty($json['file']['link'])) html_error('Download link not found. Upload aborted.');

	$post = array();
	$post['Filename'] = $lname;
	$post['folder_id'] = 0;
	$post['file'] = $json['file']['file_id'];
	$post['SID'] = $cookie['SID'];
	$post['Upload'] = 'Submit Query';

	$up_url = $up[1];

	// Uploading
	echo "<script type='text/javascript'>document.getElementById('info').style.display='none';</script>\n";

	$url = parse_url($up_url);
	$upfiles = upfile($url['host'], 80, $url['path'].($url['query'] ? '?'.$url['query'] : ''), $referer, $cookie, $post, $lfile, $lname, 'file', '', $_GET['proxy'], $pauth, 'Shockwave Flash');

	// Upload Finished
	echo "<script type='text/javascript'>document.getElementById('progressblock').style.display='none';</script>\n";

	is_page($upfiles);

	$download_link = $json['file']['link'];
}

function Get_Reply($page) {
	if (!function_exists('json_decode')) html_error('Error: Please enable JSON in php.');
	$json = substr($page, strpos($page, "\r\n\r\n") + 4);
	$json = substr($json, strpos($json, '{'));$json = substr($json, 0, strrpos($json, '}') + 1);
	$rply = json_decode($json, true);
	if (!$rply || (is_array($rply) && count($rply) == 0)) html_error('Error getting json data.');
	return $rply;
}

// Edited For upload.php usage.
function Show_reCaptcha($pid, $inputs, $default_login) { 
	if (!is_array($inputs)) html_error("Error parsing captcha data.");

	// Themes: 'red', 'white', 'blackglass', 'clean'
	echo "<script language='JavaScript'>var RecaptchaOptions={theme:'white', lang:'en'};</script>\n";
	echo "\n<center><form name='dl' method='post' ><br />\n";
	foreach ($inputs as $name => $input) echo "<input type='hidden' name='$name' id='$name' value='$input' />\n";
	if (!$default_login) echo "\t<table border='0' style='width:270px;' cellspacing='0' align='center'>\n\t\t<tr><td style='white-space:nowrap;'>&nbsp;Email</td><td>&nbsp;<input type='text' id='up_login' name='up_login' value='".htmlentities($_REQUEST['up_login'])."' style='width:160px;' /></td></tr>\n\t\t<tr><td style='white-space:nowrap;'>&nbsp;Password</td><td>&nbsp;<input type='password' id='up_password' name='up_pass' value='' style='width:160px;' /></td></tr>\n\t</table><br /><br />";
	echo "<script type='text/javascript' src='http://www.google.com/recaptcha/api/challenge?k=$pid'></script>";
	echo "<noscript><iframe src='http://www.google.com/recaptcha/api/noscript?k=$pid' height='300' width='500' frameborder='0'></iframe><br />";
	echo "<textarea name='recaptcha_challenge_field' rows='3' cols='40'></textarea><input type='hidden' name='recaptcha_response_field' value='manual_challenge' /></noscript><br />";
	echo "<input type='submit' name='submit' onclick='javascript:return checks();' value='Enter Captcha' />\n";
	echo "<script type='text/javascript'>/*<![CDATA[*/\n\tfunction checks() {\n\t\tif ($('#recaptcha_response_field').val() == '') {\n\t\t\twindow.alert('You didn\'t enter the image verification code.');\n\t\t\treturn false;\n\t\t} " . ($default_login ? '' : "else if ($('#up_login').val() == '' || $('#up_password').val() == '') {\n\t\t\twindow.alert('Please fill login fields.');\n\t\t\treturn false;\n\t\t} ") . "else return true;\n\t}\n/*]]>*/</script>\n";
	echo "</form></center>\n</body>\n</html>";
	exit;
}

//[14-7-2012] Written by Th3-822.

?>