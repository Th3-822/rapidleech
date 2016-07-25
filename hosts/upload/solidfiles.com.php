<?php
	
	$domain = 'solidfiles.com';
	$url = 'https://www.solidfiles.com';
	echo "<center>Solidfiles.com plugin by <b>The Devil</b></center><br />\n";
	echo "<table style='width:600px;margin:auto;'>\n<tr><td align='center'>\n<div id='info' width='100%' align='center'>Retrieve Upload ID</div>\n";
	$page = cURL($url);
	$cookie = GetCookiesArr($page);
	$prep['name'] = $lname;
	$pay = json_encode($prep);
	$heads = array('X-Upload-Content-Length: '.getSize($lfile),
		'Content-Length: '.strlen($pay),
		'Content-Type: application/json');
	$opts[CURLOPT_HTTPHEADER] = $heads;
	$page = cURL('https://www.solidfiles.com/api/upload/nodes',$cookie,$pay,'https://www.solidfiles.com/',0,$opts);
	$devil = preg_match('@https?://www.solidfiles.com/[\d\w/?_=]+@',$page,$uplocs);
	if(!$devil==1){
		html_error('[1]Error: Unable to Retrieve Upload ID');
	}
	$url = parse_url($uplocs[0]);
	$srang = getSize($lfile);
	$gg = $srang-1;
	echo "<script type='text/javascript'>document.getElementById('info').style.display='none';</script>\n";
	$pfile = putfile($url['host'],0,$url['path'].($url["query"] ? "?" . $url["query"] : ""),"https://www.solidfiles.com/\r\nContent-Range: bytes 0-$gg/$srang\r\nContent-Type: application/octet-stream",$cookie,$lfile,$lname,0,0,0,$url['scheme']);is_page($pfile);
	echo "<script type='text/javascript'>document.getElementById('progressblock').style.display='none';</script>";
	$devil = jsonreply($pfile);
	(empty($devil['view_url'])) ? html_error('[1]Error: Cannot Find Download Link') : $download_link = $devil['view_url'];

	function jsonreply($resp){
		$tmp = stristr($resp,"{");
		(empty($tmp)) ? html_error('[0]Error: Cannot Find API Response') : '';
		$devil = json_decode($tmp,true);
		return $devil;
	}

// Written by The Devil	

?>
