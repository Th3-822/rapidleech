<?php

//////////ACCOUNT INFO//////////////////////////////////////////////////////
$upload_acc['backup_bz']['user'] = ""; //Set your Username Here
$upload_acc['backup_bz']['pass'] = ""; //Set your Password Here
////////////////////////////////////////////////////////////////////////////

//Do Not Edit Below//
////////////////////////////////////////////////////////////////////////////

$not_done = true;
$domain = 'backup.bz';

if (!empty($upload_acc['backup_bz']['user']) && !empty($upload_acc['backup_bz']['pass'])) {
	$default_acc = true;
	$_REQUEST['up_login'] = $upload_acc['backup_bz']['user'];
	$_REQUEST['up_pass'] = $upload_acc['backup_bz']['pass'];
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
	echo "<center>Backup.bz plugin by <b>The Devil</b></center><br />\n";
	
	if (!empty($_REQUEST['up_login']) && !empty($_REQUEST['up_pass'])){
	echo "<table style='width:600px;margin:auto;'>\n<tr><td align='center'>\n<div id='login' width='100%' align='center'>Login to $domain</div>\n";
		$data = array('username'=>urlencode($_REQUEST['up_login']),'password'=>urlencode($_REQUEST['up_pass']),'submitme'=>'1');
		$url = 'https://backup.bz/ajax/_account_login.ajax.php';
		$url = parse_url($url);
		$page = geturl($url['host'],$url['port'],$url['path'],0,0,$data,0,0,0,0,$url['scheme'],0,1);is_page($page);
		$resp = jsonreply($page);
		($resp['login_status']!='success')?html_error('[0]Error: Login Error, Check Your Login Info'):'';
		$cookie = GetCookiesArr($page);
		
		$login = true;
	} else echo "<b><center>Login not found or empty, using non member upload.</center></b>\n";
	
	//Retrieve Upload ID
	echo "<table style='width:600px;margin:auto;'>\n<tr><td align='center'>\n<div id='info' width='100%' align='center'>Retrive upload ID</div>\n";
	$page = cURL('https://backup.bz/index.html',$cookie);
	$block = cut_str($page,'file upload widget','setUploadFolderId');
	$lck = preg_match_all('~http?s://backup.bz/core/page/ajax/file_upload_handler.ajax.php[\w\d\/\?&.=]+~',$page,$uplocs);
	$uploc = $uplocs[0][0];
	(!$lck)?html_error('[1]Error: Unable to Retrieve Upload Location'):'';
	$mchk = preg_match_all('~data.formData = {(.*)};~',$block,$datablock);
	(!$mchk)?html_error('[2]Error: Unable to Retrieve Token Array'):'';
	is_notpresent($datablock[1][0],'sessionid','[2]Error: Tokens May Have Changed, Plugin Revision Required');
	$data = $datablock[1][0];
	$data = explode(',',$data);
	$data = str_replace("'",'',$data);
	$data = preg_replace('/\s+/', '', $data);
	$sid = explode(':',$data[0]);
	$cTrak = explode(':',$data[1]);
	$post = array($sid[0]=>$sid[1],$cTrak[0]=>$cTrak[1],'maxChunkSize'=>'100000000','folderId'=>'null');
	echo "<script type='text/javascript'>document.getElementById('info').style.display='none';</script>\n";
	$url = parse_url($uploc);
	$upfiles = upfile($url['host'],0,$url['path'].'?'.$url['query'],'https://backup.bz/',$cookie,$post,$lfile,$lname,'files[]','',0,0,0,$url['scheme']);is_page($upfiles);
	echo "<script type='text/javascript'>document.getElementById('progressblock').style.display='none';</script>\n";
	preg_match('~"url":"(.*)"~U',$upfiles,$dloc);
	$dloc = $dloc[1];
	$dloc = stripslashes($dloc);
	preg_match('~"delete_url":"(.*)"~U',$upfiles,$dlt);
	$dlt = $dlt[1];
	$dlt = stripslashes($dlt);
	$download_link = $dloc;
	$delete_link = $dlt;
	}

function jsonreply($resp){
		$tmp = stristr($resp,'{');
		$json = json_decode($tmp,true);
		return $json;
}

//[2016-08-16] Written by The Devil
?>

