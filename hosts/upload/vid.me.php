<?php
	
	echo "<center>Vid.me plugin by <b>The Devil</b></center><br />\n";
	echo "<table style='width:600px;margin:auto;'>\n<tr><td align='center'>\n<div id='info' width='100%' align='center'>Retrive upload ID</div>\n";
	$domain = 'vid.me';
	$post['title'] = $lname;
	$post['description'] =  'Uploaded with Rapidleech';
	$post['PLATFORM'] = 'web';
	$post['source'] = 'computer';
	$post['mode'] = 'chunked';
	$post['filename'] = $lname;
	$post['size'] = getSize($lfile);
	$post['public'] = '0';
	echo "<script type='text/javascript'>document.getElementById('info').style.display='none';</script>\n";
	$upfile = upfile($domain,0,'/api/video/request',0,0,$post,$lfile,$lname,'filedata','',0,0,0,'https');is_page($upfile);
	echo "<script type='text/javascript'>document.getElementById('progressblock').style.display='none';</script>\n";
	$block = strstr($upfile,'{');
	$devil = json_decode($block,true);
	$download_link = $devil['url'];


	//Written by The Devil
?>
