<?php

//////////ACCOUNT INFO//////////////////////////////////////////////////////
$upload_acc['bigfile_to']['user'] = ""; //Set your username
$upload_acc['bigfile_to']['pass'] = ""; //Set your password
////////////////////////////////////////////////////////////////////////////

//Do Not Edit Below//
////////////////////////////////////////////////////////////////////////////
if (!function_exists('json_decode')) html_error('Error: Please enable JSON in php.');
$not_done = true;
if (!empty($upload_acc['bigfile_to']['user']) && !empty($upload_acc['bigfile_to']['pass'])) {
	$default_acc = true;
	$_REQUEST['up_login'] = $upload_acc['bigfile_to']['user'];
	$_REQUEST['up_pass'] = $upload_acc['bigfile_to']['pass'];
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
	$navi = 'https://www.bigfile.to/';
	$domain = 'bigfile.to';
	echo "<center>Bigfile.to plugin by <b>The Devil</b></center><br />\n";
	echo "<table style='width:600px;margin:auto;'>\n<tr><td align='center'>\n<div id='login' width='100%' align='center'>Login to $domain</div>\n";
	if (!empty($_REQUEST['up_login']) && !empty($_REQUEST['up_pass'])){
		$post = array('userName'=>urlencode($_REQUEST['up_login']),'userPassword'=>urlencode($_REQUEST['up_pass']),
			'autoLogin'=>'on','action__login'=>'normalLogin');
		$page = cURL($navi.'login.php',0,$post,$navi.'login.php',0,0);
		$cookie = GetCookiesArr($page);
		if(empty($cookie['autologin'])) html_error('Login Failed, Auth Cookie Not Found');
		$login = true;
	} else echo "<b><center>Login not found or empty, using non member upload.</center></b>\n";
	// Retrive upload ID
	echo "<table style='width:600px;margin:auto;'>\n<tr><td align='center'>\n<div id='info' width='100%' align='center'>Retrive upload ID</div>\n";
	if(!$login){
		$tmp = 'https://up.bigfile.to/u/-1';
		$url = parse_url($tmp);
		echo "<script type='text/javascript'>document.getElementById('info').style.display='none';</script>\n";
		$pfiles = putfile($url['host'],0,$url['path'].($url["query"] ? "?" . $url["query"] : ""),$navi,0,$lfile,$lname,0,0,0,$url['scheme']);is_page($pfiles);
		echo "<script type='text/javascript'>document.getElementById('progressblock').style.display='none';</script>";
		$devil = strstr($pfiles,'{');
		if(empty($devil)){
			html_error('Error Getting Download and Delete Links');
		}
		$devil = json_decode($devil,true);
		$download_link = $navi.'file/'.$devil['shortenCode'].'/'.$devil['fileName'];
		$delete_link = $navi.'file/'.$devil['shortenCode'].'/delete/'.$devil['deleteCode'];
	}else{
		$page = cURL($navi.'upload.php',$cookie,0,$navi);is_page($page);
		$uplocs = preg_match_all('@https?://(.*).bigfile.to[\d\w/:._-]+@', $page, $TD);
		$url = parse_url($TD[0][0]);
		echo "<script type='text/javascript'>document.getElementById('info').style.display='none';</script>\n";
		$pfiles = putfile($url['host'],0,$url['path'].($url["query"] ? "?" . $url["query"] : ""),$navi,$cookie,$lfile,$lname,0,0,0,$url['scheme']);is_page($pfiles);
		echo "<script type='text/javascript'>document.getElementById('progressblock').style.display='none';</script>";
		$devil = strstr($pfiles,'{');
		if(empty($devil)){
			html_error('Error Getting Download and Delete Links');
		}
		$devil = json_decode($devil,true);
		$download_link = $navi.'file/'.$devil['shortenCode'].'/'.$devil['fileName'];
		$delete_link = $navi.'file/'.$devil['shortenCode'].'/delete/'.$devil['deleteCode'];
	}
}

// Written by The Devil

?>