<?php
	
	echo "<center>Nippyshare.com plugin by <b>The Devil</b></center><br />\n";
	echo "<table style='width:600px;margin:auto;'>\n<tr><td align='center'>\n<div id='info' width='100%' align='center'>Retrive upload ID</div>\n";
	$domain = 'nippyshare.com';
	$TD = geturl($domain,0,'/', 'https://nippyshare.com', 0, 0, 0, 0, 0, 0, 'https');
	$block = cut_str($TD,'id="browse"','</form>');
	preg_match('@[^\r\n\s\t]..nippyshare.com/[^\r\n\s\t<>\'\"]+@',$block,$uplocs);
	$req = 'https://'.$uplocs[0];
	$url = parse_url($req);
	echo "<script type='text/javascript'>document.getElementById('info').style.display='none';</script>\n";
	$post = array();
	$post['upload'] = 'Upload';
	$upfiles = upfile($url['host'],0,$url['path'],'https://nippyshare.com',0,$post,$lfile,$lname,'file[]','',0,0,0,$url['scheme']);
	echo "<script type='text/javascript'>document.getElementById('progressblock').style.display='none';</script>\n";
	$download_link = cut_str($upfiles,"href='","'"); //Lazy
	
//Written by The Devil

?>