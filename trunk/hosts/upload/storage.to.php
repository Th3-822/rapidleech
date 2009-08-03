<script>document.getElementById('login').style.display='none';</script>
<table width=600 align=center>
</td></tr>
<tr><td align=center>
<div id=info width=100% align=center>Retrive upload ID</div>
<?php
	$ref='http://www.storage.to/';
	$Url=parse_url($ref);
	$page = geturl($Url["host"], defport($Url), $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, 0, 0, 0, $_GET["proxy"],$pauth);
	is_page($page);
	preg_match_all('/Set-Cookie: (.*);/U',$page,$temp);
	$cookie = $temp[1];
	$cookies = implode(';',$cookie);
	$upfrm = cut_str($page,'form-data" action="/upload/','"');
	$upfrm = 'http://www.storage.to/upload/'.$upfrm;
	$maxfz = cut_str($page,'MAX_FILE_SIZE" value="','"');
	$post['MAX_FILE_SIZE']=$maxfz;
	$url=parse_url($upfrm);
?>
<script>document.getElementById('info').style.display='none';</script>
<?

	$upfiles=upfile($url["host"],defport($url), $url["path"].($url["query"] ? "?".$url["query"] : ""),$ref, $cookies, $post, $lfile, $lname, "files[]");

?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?
	is_page($upfiles);
	$locat='http://www.storage.to/uploadresult';
		
	$Url=parse_url($locat);
	$page = geturl($Url["host"], defport($Url), $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $ref, $cookies, 0, 0, $_GET["proxy"],$pauth);
	is_page($page);
	$ddl=cut_str($page,'name="" value="http://www.storage.to/get/','"');
	$del=cut_str($page,'name="" value="http://www.storage.to/delete/','"');
	$download_link= 'http://www.storage.to/get/'.$ddl;
	$delete_link= 'http://www.storage.to/delete/'.$del;
	
// Made by Baking 30/06/2009 01:37
?>