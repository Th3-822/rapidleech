<?php

//////////ACCOUNT INFO//////////////////////////////////////////////////////
$upload_acc['filecloud_io']['apikey'] = ""; //Set your filecloud.io apikey here.
////////////////////////////////////////////////////////////////////////////

//Do Not Edit Below//
////////////////////////////////////////////////////////////////////////////
if (!function_exists('json_decode')) html_error('[2]Error: Please enable JSON in php.');
$minz = 2000; // Minimum bytes to upload is 1023 bytes, but should increase this just to be safe
$ss = getSize($lfile); ($ss<$minz) ? html_error('Minimum File Size to Upload is '.$minz.' bytes'):'';
$not_done = true;

if (!empty($upload_acc['filecloud_io']['apikey'])) {
	$default_acc = true;
	$_REQUEST['apikey'] = $upload_acc['filecloud_io']['apikey'];
	$_REQUEST['action'] = '_TD_';
	echo "<b><center>Using Default Login.</center></b>\n";
} else $default_acc = false;

if (empty($_REQUEST['action']) || $_REQUEST['action'] != '_TD_') {
	echo "<table border='0' style='width:270px;' cellspacing='0' align='center'>\n<form method='POST'>\n\t<input type='hidden' name='action' value='_TD_' />\n\t<tr><td style='white-space:nowrap;'>&nbsp;API Key*</td><td>&nbsp;<input type='text' name='apikey' value='' style='width:160px;' /></td></tr>\n";
	echo "\t<tr><td colspan='2' align='center'><br /><input type='submit' value='Upload' /></td></tr>\n";
	echo "\t<tr><td colspan='2' align='center'><small>*You can set it as default in <b>".basename(__FILE__)."</b></small></td></tr>\n";
	echo "\t<tr><td colspan='2' align='center'>**NOTE: Create API Key in Account Settings and Allow Upload!**</td></tr>\n";
	echo "</form>\n</table>\n";
} else {

	$login = $not_done = false;
	$domain = 'filecloud.io';
	$aloc = 'https://filecloud.io/?m=api&a=upload&akey=';
	$cloc = 'https://filecloud.io/?m=api&a=fetch_account_info&akey=';
	echo "<center>Filecloud.io plugin by <b>The Devil</b></center><br/>\n";
	(empty($_REQUEST['apikey']))?html_error('API Key Was Not Entered'):'';
	echo "<table style='width:600px;margin:auto;'>\n<tr><td align='center'>\n<div id='login' width='100%' align='center'>Login to $domain</div>\n";
	$resp = jsonreply(cURL($cloc.$_REQUEST['apikey']));
	if(!($resp['status']=='ok')){
		is_present($resp['message'],'No permission granted','Unable to Check Login, Please Allow Account Info Fetch in Account/API Settings');
		is_present($resp['message'],'no akey parameter provided','Login Failed, Please Check You Login Details');
		html_error('[1]Error: Bad API Response: '.$resp['message']);
	}else $login=true;
	echo "<table style='width:600px;margin:auto;'>\n<tr><td align='center'>\n<div id='info' width='100%' align='center'>Retrive upload ID</div>\n";
	$devil = jsonreply(cURL($aloc.$_REQUEST['apikey']));
	if((!($devil['status']=='ok')) || empty($devil['upload_url'])){
		is_present($devil['message'],'No permission granted','Please Allow Uploading in Account/API Settings');
		html_error('[3]Error: Unable to Retrieve Upload Link - '.$devil['message']);
	}
	$url = parse_url($devil['upload_url']);
	echo "<script type='text/javascript'>document.getElementById('info').style.display='none';</script>\n";
	$upfiles = upfile($url['host'], 0, $url["path"].($url["query"] ? "?".$url["query"] : ""), 0, 0, 0, $lfile, $lname, 'Filedata', '', 0, 0, 0, $url['scheme']);is_page($upfiles);
	echo "<script type='text/javascript'>document.getElementById('progressblock').style.display='none';</script>\n";
	$devil = jsonreply($upfiles);
	if(!($devil['status']=='ok')){
		html_error('[4]Error: Upload Failed '.$devil['message']);
	}
	$download_link = $devil['furl'];
}

function jsonreply($resp){
		$tmp = stristr($resp,"{");
		(empty($tmp)) ? html_error('[0]Error: Cannot Find API Response') : '';
		$devil = json_decode($tmp,true);
		return $devil;
}

// Written by The Devil

?>