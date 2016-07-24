<?php  

$upload_acc['anafile_com']['user'] = ""; //Set your username
$upload_acc['anafile_com']['pass'] = ""; //Set your password
////////////////////////////////////////////////////////////////////////////

//Do Not Edit Below//
////////////////////////////////////////////////////////////////////////////

$not_done = true;

if (!empty($upload_acc['anafile_com']['user']) && !empty($upload_acc['anafile_com']['pass'])) {
	$default_acc = true;
	$_REQUEST['up_login'] = $upload_acc['anafile_com']['user'];
	$_REQUEST['up_pass'] = $upload_acc['anafile_com']['pass'];
	$_REQUEST['action'] = '_TD_';
	echo "<b><center>Using Default Login.</center></b>\n";
} else $default_acc = false;

if (empty($_REQUEST['action']) || $_REQUEST['action'] != '_TD_') {
	echo "<table border='0' style='width:270px;' cellspacing='0' align='center'>\n<form method='POST'>\n\t<input type='hidden' name='action' value='_TD_' />\n\t<tr><td style='white-space:nowrap;'>&nbsp;Username*</td><td>&nbsp;<input type='text' name='up_login' value='' style='width:160px;' /></td></tr>\n\t<tr><td style='white-space:nowrap;'>&nbsp;Password*</td><td>&nbsp;<input type='password' name='up_pass' value='' style='width:160px;' /></td></tr>\n";
	echo "\t<tr><td colspan='2' align='center'><br /><input type='submit' value='Upload' /></td></tr>\n";
	echo "\t<tr><td colspan='2' align='center'><small>*You can set it as default in <b>".basename(__FILE__)."</b></small></td></tr>\n";
	echo "\t<tr><td colspan='2' align='center'>**Leave Login Details Empty for Anon Upload**</td></tr>\n";
	echo "</form>\n</table>\n";
} else {

	$login = $not_done = false;
	$domain = 'anafile.com';
	$navi = 'http://www.anafile.com/';
	$cookie = array('lang'=>'english');
	$sid = '';
	echo "<center>Anafile.com plugin by <b>The Devil</b></center><br />\n";
	echo "<table style='width:600px;margin:auto;'>\n<tr><td align='center'>\n<div id='login' width='100%' align='center'>Login to $domain</div>\n";
	if (!empty($_REQUEST['up_login']) && !empty($_REQUEST['up_pass'])){
		$devil = array('op'=>'login','redirect'=>urlencode($navi),
			'login'=>urlencode($_REQUEST['up_login']),'password'=>urlencode($_REQUEST['up_pass']));
		$logs = cURL($navi,$cookie,$devil,$navi.'login.html');
		$cookie = GetCookiesArr($logs,$cookie);
		(empty($cookie['xfss']) || empty($cookie['login'])) ? html_error('Login Failed, Auth Cookie Not Found') : $login = true;
		$logs = cURL($navi,$cookie,0,$navi);
		$sid = cut_str($logs,'name="sess_id" value="','"');
	}elseif((!empty($_REQUEST['up_login']) && empty($_REQUEST['up_pass'])) || (empty($_REQUEST['up_login']) && !empty($_REQUEST['up_pass']))){
		html_error('Login Details Were Only Partially Entered!');
	}else{
		echo "<b><center>Login not found or empty, using non member upload.</center></b>\n";
		$devil = getSize($lfile);
		($devil>51904512) ? html_error('Anon Upload is Limited at 50MB') : '';
	}

	// Retrive upload ID
	echo "<table style='width:600px;margin:auto;'>\n<tr><td align='center'>\n<div id='info' width='100%' align='center'>Retrieve Upload ID</div>\n";

	$page = cURL($domain);
	$uploc = 'http://anaflz.net/cgi-bin/upload.cgi';
	for($x=0; $x<=11; $x++){
		$uid = $uid.mt_rand(0,9);
	}
	$qdata = array('upload_id'=>$uid,'js_on'=>'1','utype'=>'anon','upload_type'=>'file');
	$query = http_build_query($qdata);
	$pdata = array('upload_type'=>'file','sess_id'=>$sid,'srv_tmp_url'=>'http://anaflz.net/tmp','link_rcpt'=>'',
		'link_pass'=>'','tos'=>'1','submit_btn'=>'');
	$url = parse_url($uploc);
	echo "<script type='text/javascript'>document.getElementById('info').style.display='none';</script>\n";
	$upfiles = upfile($url['host'],0,$url['path'].'?'.$query,$navi,$cookie,$pdata,$lfile,$lname,'file_0','','',0,0,0,$url['scheme']);
	echo "<script type='text/javascript'>document.getElementById('progressblock').style.display='none';</script>";
	is_notpresent($upfiles,"<textarea name='st'>OK</textarea>",'Error: Upload Failed');
	$fid = cut_str($upfiles,"<textarea name='fn'>",'<');
	$download_link = 'http://www.anafile.com/'.$fid;

}

// Written by The Devil

?>