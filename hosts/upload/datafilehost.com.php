<?php

	echo "<center>Datafilehost.com plugin by <b>The Devil</b></center><br />\n";

	$domain = 'datafilehost.com';
	$page = cURL('https://www.'.$domain);
	$cookies = GetCookiesArr($page);
	$ind = preg_match_all('~"max_file_size" value="(.*)"~i',$page,$mfs);
	(!$ind)?html_error('Error[0]: Unable to Retrieve Max File Size of Upload - Check Host'):'';
	$max_fs = $mfs[1][0];

	//Retrieve Upload ID
	echo "<table style='width:600px;margin:auto;'>\n<tr><td align='center'>\n<div id='info' width='100%' align='center'>Retrive upload ID</div>\n";
	$uploc = 'https://www.'.$domain.'/upload.php';
	$url = parse_url($uploc);
	$post = array('MAX_FILE_SIZE'=>$max_fs);
	echo "<script type='text/javascript'>document.getElementById('info').style.display='none';</script>\n";
	$upfiles = upfile($url['host'],0,$url['path'],0,$cookie,$post,$lfile,$lname,'upfile','',0,0,0,$url['scheme']);is_page($upfiles);
	unset($cookies);
	$cookies = GetCookiesArr($upfiles);
	echo "<script type='text/javascript'>document.getElementById('progressblock').style.display='none';</script>\n";
	$upfiles = cURL('https://www.'.$domain.'/codes.php',$cookies);
	$ind = preg_match_all('~clip[\d].setText\(\s\'([\d\w:/.-]+)~',$upfiles,$links);
	(!$ind)?html_error('Error[1]: Unable to Retrieve Download and Delete Links'):'';
	$download_link = $links[1][0];
	$delete_link = $links[1][1];


//[23-3-2014]  Written by Th3-822.
//[2016-09-28] Re-Written by The Devil

?>

