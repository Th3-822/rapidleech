<?php

	echo "<center>Filedropper.com plugin by <b>The Devil</b></center><br />\n";
	$uploc = 'http://www.filedropper.com/index.php?xml=true';
	$domain = 'http://www.filedropper.com/';
	echo "<table style='width:600px;margin:auto;'>\n<tr><td align='center'>\n<div id='info' width='100%' align='center'>Retrive upload ID</div>\n";
	$url = parse_url($uploc);
	$post = array('Filename'=>$lname,'Upload'=>'Submit Query');
	$heads = "\r\nX-Requested-With: ShockwaveFlash/23.0.0.207";
	echo "<script type='text/javascript'>document.getElementById('info').style.display='none';</script>\n";
	$upfiles = upfile($url['host'],0,$url['path']."?".$url['query'],$domain.$heads,0,$post,$lfile,$lname,'file');
	is_page($upfiles);
	echo "<script type='text/javascript'>document.getElementById('progressblock').style.display='none';</script>\n";
	$resp = cut_str($upfiles,'/index.php','\r\n');
	$page = cURL($domain.$resp);
	preg_match_all('~https?://(www)?.filedropper.com/[\d\w./]+~',$page,$redirs);
	$redir = $redirs[0][0];
	$resp = cURL($redir);
	$ind = preg_match_all('~https?://(www)?.filedropper.com/[\d\w]+~',$resp,$dlocs);
	(!$ind)?html_error('Error[0]: Unable to Retrieve Download Link'):'';
	$download_link = $dlocs[0][0];

//[15-12-2016] Re-Written by The Devil	

?>

