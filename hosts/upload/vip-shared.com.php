<?php

//////////ACCOUNT INFO//////////////////////////////////////////////////////
$upload_acc['vip-shared_com']['user'] = ""; //Set your Username Here
$upload_acc['vip-shared_com']['pass'] = ""; //Set your Password Here
////////////////////////////////////////////////////////////////////////////

//Do Not Edit Below//
////////////////////////////////////////////////////////////////////////////

$not_done = true;
$domain = 'vip-shared.com';

if (!empty($upload_acc['vip-shared_com']['user']) && !empty($upload_acc['vip-shared_com']['pass'])) {
	$default_acc = true;
	$_REQUEST['up_login'] = $upload_acc['vip-shared_com']['user'];
	$_REQUEST['up_pass'] = $upload_acc['vip-shared_com']['pass'];
	$_REQUEST['action'] = '_TD_';
	echo "<b><center>Using Default Login.</center></b>\n";
} else $default_acc = false;

if (empty($_REQUEST['action']) || $_REQUEST['action'] != '_TD_') {
	echo "<table border='0' style='width:270px;' cellspacing='0' align='center'>\n<form method='POST'>\n\t<input type='hidden' name='action' value='_TD_' />\n\t<tr><td style='white-space:nowrap;'>&nbsp;Username*</td><td>&nbsp;<input type='text' name='up_login' value='' style='width:160px;' /></td></tr>\n\t<tr><td style='white-space:nowrap;'>&nbsp;Password*</td><td>&nbsp;<input type='password' name='up_pass' value='' style='width:160px;' /></td></tr>\n";
	echo "\t<tr><td colspan='2' align='center'><br /><input type='submit' value='Upload' /></td></tr>\n";
	echo "\t<tr><td colspan='2' align='center'><small>*You can set it as default in <b>".basename(__FILE__)."</b></small></td></tr>\n";
	echo "\t<tr><td colspan='2' align='center'>**Leave Login Details Empty for Anon Upload**</td></tr>\n";
	echo "\t<tr><td colspan='2' align='center'>**NOTE: Anon Upload is Limited to 50MB**</td></tr>\n";
	echo "</form>\n</table>\n";
} else {
	
	$login = $not_done = false;
	
	echo "<center>Vip-Shared.com plugin by <b>The Devil</b></center><br />\n";
	if (!empty($_REQUEST['up_login']) && !empty($_REQUEST['up_pass'])){
	echo "<table style='width:600px;margin:auto;'>\n<tr><td align='center'>\n<div id='login' width='100%' align='center'>Login to $domain</div>\n";
		$lin = array('loginUsername'=>urlencode($_REQUEST['up_login']),'loginPassword'=>urlencode($_REQUEST['up_pass']),'submit'=>'submit','submitme'=>'1');
		$page = cURL('http://vip-shared.com/login.html',0,$lin);
		is_notpresent($page,'account_home.html','[4]Error: Login Failed, Check Login Details!');
		$cookie = GetCookiesArr($page);
		$login = true;
	} else echo "<b><center>Login not found or empty, using non member upload.</center></b>\n";
	
	//Retrieve Upload ID
	echo "<table style='width:600px;margin:auto;'>\n<tr><td align='center'>\n<div id='info' width='100%' align='center'>Retrive upload ID</div>\n";
	if($login = false){
		$fsize = getSize($lfile);
		($fsize>51380224)?html_error('Anon Upload is Limited to 50MB'):''; //Limited to 49MB to be safe
	}
	$page = cURL($domain,$cookie);
	$mchk = preg_match_all('~form action="(.*)"~U',$page,$acts);
	(!$mchk)?html_error('[1]Error: Unable to Retrieve Upload Location'):'';
	is_notpresent($acts[1][1],"file_upload_handler","[0]Error: Check Upload Location");
	$uploc = $acts[1][1];
	$mchk = preg_match_all('~data.formData = {(.*)};~',$page,$datablock);
	(!$mchk)?html_error('[2]Error: Unable to Retrieve Token Array'):'';
	is_notpresent($datablock[1][0],'sessionid','[3]Error: Tokens May Have Changed, Plugin Revision Required');
	$data = $datablock[1][0];
	$data = explode(',',$data);
	$data = str_replace("'",'',$data);
	$data = preg_replace('/\s+/', '', $data);
	$sid = explode(':',$data[0]);
	$cTrak = explode(':',$data[1]);
	$post = array($sid[0]=>$sid[1],$cTrak[0]=>$cTrak[1],'maxChunkSize'=>'100000000','folderId'=>'');

	echo "<script type='text/javascript'>document.getElementById('info').style.display='none';</script>\n";
	$url = parse_url($uploc);
	$upfiles = upfile($url['host'],0,$url['path'].'?'.$url['query'],'http://vip-shared.com/',$cookie,$post,$lfile,$lname,'files[]','',0,0,0,$url['scheme']);is_page($upfiles);

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
	
//[2016-08-01] Written by The Devil

?>

