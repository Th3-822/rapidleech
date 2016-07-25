<?php

//////////ACCOUNT INFO//////////////////////////////////////////////////////
$upload_acc['openload_co']['user'] = ""; //Set your API Key Here
$upload_acc['openload_co']['pass'] = ""; //Set your API Password Here
////////////////////////////////////////////////////////////////////////////

//Do Not Edit Below//
////////////////////////////////////////////////////////////////////////////
if (!function_exists('json_decode')) html_error('Error: Please enable JSON in php.');
$not_done = true;
if (!empty($upload_acc['openload_co']['user']) && !empty($upload_acc['openload_co']['pass'])) {
	$default_acc = true;
	$_REQUEST['up_login'] = $upload_acc['openload_co']['user'];
	$_REQUEST['up_pass'] = $upload_acc['openload_co']['pass'];
	$_REQUEST['action'] = '_TD_';
	echo "<b><center>Using Default Login.</center></b>\n";
} else $default_acc = false;

if (empty($_REQUEST['action']) || $_REQUEST['action'] != '_TD_') {
	echo "<table border='0' style='width:270px;' cellspacing='0' align='center'>\n<form method='POST'>\n\t<input type='hidden' name='action' value='_TD_' />\n\t<tr><td style='white-space:nowrap;'>&nbsp;API Login*</td><td>&nbsp;<input type='text' name='up_login' value='' style='width:160px;' /></td></tr>\n\t<tr><td style='white-space:nowrap;'>&nbsp;API Password*</td><td>&nbsp;<input type='password' name='up_pass' value='' style='width:160px;' /></td></tr>\n";
	echo "\t<tr><td colspan='2' align='center'><br /><input type='submit' value='Upload' /></td></tr>\n";
	echo "\t<tr><td colspan='2' align='center'><small>*You can set it as default in <b>".basename(__FILE__)."</b></small></td></tr>\n";
	echo "\t<tr><td colspan='2' align='center'>**Leave Login Details Empty for Anon Upload**</td></tr>\n";
	echo "\t<tr><td colspan='2' align='center'>**NOTE: API Password is Different to Your Usual Password**</td></tr>\n";
	echo "</form>\n</table>\n";
} else {
	
	$login = $not_done = false;
	$base = 'https://api.openload.co/1';
	$domain = 'openload.co';
	echo "<center>Openload.co plugin by <b>The Devil</b></center><br />\n";
	if (!empty($_REQUEST['up_login']) && !empty($_REQUEST['up_pass'])){
		$login = true;
	} else echo "<b><center>Login not found or empty, using non member upload.</center></b>\n";
	echo "<table style='width:600px;margin:auto;'>\n<tr><td align='center'>\n<div id='info' width='100%' align='center'>Retrive upload ID</div>\n";
	if($login){
		$resp = cURL($base.'/file/ul?login='.$_REQUEST['up_login'].'&key='.$_REQUEST['up_pass']);
	} else	$resp = cURL($base.'/file/ul');
	
	$devil = strpbrk($resp,'{');
	if(empty($devil)){
		html_error('[0]Error: Bad API Response');
	}
	$devil = json_decode($devil,true);
	if(!($devil['status']=='200')){
		html_error($devil['status'].' '.$devil['msg'].': Bad API Response!');
	}
	if(empty($devil['result']['url'])){
		html_error('[50x1]Error: Plugin Update Required');
	}
	$uploc = $devil['result']['url'];
	$url = parse_url($uploc);
	echo "<script type='text/javascript'>document.getElementById('info').style.display='none';</script>\n";
	$upfiles = upfile($url['host'], 0, $url['path'], 0, 0, 0, $lfile, $lname, 'file','', 0, 0, 0, $url['scheme']);
	echo "<script type='text/javascript'>document.getElementById('progressblock').style.display='none';</script>\n";
	$dfin = strpbrk($upfiles,'{');
	if(empty($dfin)){
		html_error('[1]Error: Bad API Response');
	}
	$devil = json_decode($dfin,true);
	if(!($devil['status']=='200')){
		html_error($devil['status'].' '.$devil['msg'].': Bad API Response!');
	}
	$download_link = $devil['result']['url'];
	}

// Written by The Devil
	
?>

