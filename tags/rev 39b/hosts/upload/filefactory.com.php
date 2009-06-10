<?php
	$page = geturl("www.filefactory.com", 80, "/", 0, 0, 0, 0, "");
	preg_match('/<form accept-charset="UTF-8" id="uploader" action="(.*)" method="post"/',$page,$tmp);
	$upload_to = trim($tmp[1]);
	$url = parse_url($upload_to);
	$post['redirect'] = 1;
	$post['enabled'] = 1;
	
	$upfiles = upfile ( $url ["host"], $url ["port"] ? $url ["port"] : 80, $url ["path"] . ($url ["query"] ? "?" . $url ["query"] : ""), "http://www.filefactory.com/", 0, $post, $lfile, $lname, "file" );
	preg_match('/ocation: (.*)/',$upfiles,$tmp);
	$loc = $tmp[1];
	$Url = parse_url(trim($loc));
	$page = geturl($Url["host"], defport($Url), $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 'http://filefactory.com', 0, 0, 0, $_GET["proxy"],$pauth);
	preg_match('/<div class="metadata">\n(.*)<\/div>/',$page,$tmp);
	$download_link = trim($tmp[1]);
?>