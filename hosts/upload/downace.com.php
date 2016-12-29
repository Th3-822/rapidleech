<?php

//////////ACCOUNT INFO//////////////////////////////////////////////////////
$upload_acc['downace_com']['user'] = ""; //Set your Username Here
$upload_acc['downace_com']['pass'] = ""; //Set your Password Here
////////////////////////////////////////////////////////////////////////////

//Do Not Edit Below//
////////////////////////////////////////////////////////////////////////////

$not_done = true;
if (!empty($upload_acc['downace_com']['user']) && !empty($upload_acc['downace_com']['pass'])) {
	$default_acc = true;
	$_REQUEST['up_login'] = $upload_acc['downace_com']['user'];
	$_REQUEST['up_pass'] = $upload_acc['downace_com']['pass'];
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
	echo "<center>Downace.com plugin by <b>The Devil</b></center><br />\n";
	$domain = 'downace.com';
	$https_path = 'https://'.$domain.'/';
	if (!empty($_REQUEST['up_login']) && !empty($_REQUEST['up_pass'])){

	echo "<table style='width:600px;margin:auto;'>\n<tr><td align='center'>\n<div id='login' width='100%' align='center'>Login to $domain</div>\n";
		$login_path = 'https://'.$domain.'/login.html';
		$post = array('username'=>urlencode($_REQUEST['up_login']),'password'=>urlencode($_REQUEST['up_pass']),'submitme'=>'1');
		$lin = cURL($login_path,0,$post,$login_path);
		is_notpresent($page,'account_home.html','[4]Error: Login Failed, Check Login Details!');
		$cookies = GetCookiesArr($lin);
		
		(empty($cookies['filehosting']))?html_error('Error[0]: Unable to Obtain Login Cookie'):'';
		$login = true;
	} else echo "<b><center>Login not found or empty, using non member upload.</center></b>\n";
	echo "<table style='width:600px;margin:auto;'>\n<tr><td align='center'>\n<div id='info' width='100%' align='center'>Retrive upload ID</div>\n";
	$page = cURL($https_path,$cookies);
	$ind = preg_match_all('~https?://[\d\w]+.downace.com/[\d\w/.?=&]+~',$page,$uplocs);
	(!$ind)?html_error('Error[1]: Unable to find upload location'):'';
	$uploc = $uplocs[0][0];
	$block = cut_str($page,'file upload widget','setUploadFolderId');
	$mchk = preg_match_all('~data.formData = {(.*)};~',$block,$datablock);
	(!$mchk)?html_error('Error[2]: Unable to Retrieve Token Array'):'';
	is_notpresent($datablock[1][0],'sessionid','Error[3]: Tokens May Have Changed, Plugin Revision Required');
	$data = $datablock[1][0];
	$data = explode(',',$data);
	$data = str_replace("'",'',$data);
	$data = preg_replace('/\s+/', '', $data);
	$sid = explode(':',$data[0]);
	$cTrak = explode(':',$data[1]);
	$upost = array($sid[0]=>$sid[1],$cTrak[0]=>$cTrak[1],'maxChunkSize'=>'0','folderId'=>'');
	$url = parse_url($uploc);

	echo "<script type='text/javascript'>document.getElementById('info').style.display='none';</script>\n";
	$upfiles = upfile($url['host'],0,$url['path']."?".$url['query'],$https_path."\r\nAccept:application/json, text/javascript, */*; q=0.01",$cookie,$upost,$lfile,$lname,'files[]','',0,0,0,$url['scheme']);is_page($upfiles);
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

//[17-12-2016] Written by The Devil

	
?>

