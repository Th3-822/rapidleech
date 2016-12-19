<?php

	echo "<center>Load.to plugin by <b>The Devil</b></center><br />\n";
	$page = cURL('https://www.load.to/');
	echo "<table style='width:600px;margin:auto;'>\n<tr><td align='center'>\n<div id='info' width='100%' align='center'>Retrive upload ID</div>\n";
	$ind = preg_match_all('~action="(.*)"~U',$page,$uplocs);
	(!$ind)?html_error('Error[0]: Unable to Retrieve Upload Location'):'';
	$uploc = $uplocs[1][0];
	$url = parse_url($uploc);
	is_notpresent($url['query'],'tmp_sid','Error[1]: Plugin Update Required');
	$sid = cut_str($url['query'],'tmp_sid=','&');
	$post = array('imbedded_progress_bar'=>'1','upload_range'=>'1','email'=>'','filecomment'=>'Uploaded with Rapidleech');
	echo "<script type='text/javascript'>document.getElementById('info').style.display='none';</script>\n";
	$upfiles = upfile($url['host'],0,$url['path'].($url["query"] ? "?".$url["query"] : ""),'http://www.load.to/',0,$post,$lfile,$lname,'upfile_0','');
	is_page($upfiles);
	$ind = preg_match('~https?://[\d\w]+.load.to/[\d\w?.=]+~',$upfiles,$redirs);
	(!$ind)?html_error('Error[1]: Unable to Get Completed Upload Redirect'):'';
	$resp = cURL($redirs[0]);
	$ind = preg_match_all('~(https?://(www.)?load.to/(.*))"~U',$resp,$rlinks);
	(!$ind)?html_error('Error[2]: Unable to Get Download and Delete Links'):'';
	echo "<script type='text/javascript'>document.getElementById('progressblock').style.display='none';</script>\n";
	$download_link = $rlinks[1][0];
	$delete_link = $rlinks[1][1];

//[15-12-2016] - Re-Written by The Devil

?>
