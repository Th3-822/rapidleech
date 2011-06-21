<?php

	// Retrive upload ID
	echo "<table style='width:600px;margin:auto;'>\n<tr><td align='center'>\n<div id='info' width='100%' align='center'>Retrive upload ID</div>\n";

	$post = array();
	$post['Filename'] = $lname;
	$post['Upload'] = 'Submit Query';

	$up_loc = "http://upload.filefactory.com/upload.php";

	// Uploading
	echo "<script type='text/javascript'>document.getElementById('info').style.display='none';</script>\n";

	$url = parse_url($up_loc);
	$upfiles = upfile($url["host"],$url["port"] ? $url["port"] : 80, $url["path"].($url["query"] ? "?".$url["query"] : ""), 0, 0, $post, $lfile, $lname, "Filedata", '', 0, 0, "Shockwave Flash");

	// Upload Finished
	echo "<script type='text/javascript'>document.getElementById('progressblock').style.display='none';</script>";

	is_page($upfiles);

	if(!preg_match('@(\w+)$@i', $upfiles, $uid)) html_error("Upload ID not found.", 0);
	$page = geturl("www.filefactory.com", 80, "/file/complete.if.php/{$uid[1]}/", 'http://www.filefactory.com/upload/upload.if.php', $cookie);is_page($page);

	if(!preg_match('@/file/\w+/n/[^\'|"|<]+@i', $page, $dl)) html_error("Download link not found. (ID: {$uid[1]})", 0);
	$download_link = "http://www.filefactory.com{$dl[0]}";

//[17-6-2011]  Written by Th3-822.

?>