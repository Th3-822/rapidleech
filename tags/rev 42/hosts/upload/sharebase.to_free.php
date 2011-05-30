<table width=600 align=center>
</td></tr>
<tr><td align=center>
<script>document.getElementById('login').style.display='none';</script>
<div id=info width=100% align=center>Retrive upload ID</div>
<?php

	$ref='http://sharebase.to/upload';
	$Url=parse_url($ref);
	$page = geturl($Url["host"], defport($Url), $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), "http://sharebase.to/maccount", $cookies, 0, 0, $_GET["proxy"],$pauth);

	is_page($page);
	is_notpresent($page, 'mlogout', 'Error logging in - are your logins correct? Third');

	$upsrv = cut_str($page,'rt/form-data" action="','upload"');
	$umid = cut_str($page,'name="umid" type="hidden" value="','"');
	
	$post['umid']= $umid;
	$post['uptyp']= '1';
	$post['upload']= 'Upload Your Files';
	
	$refup = $upsrv;
	$url=parse_url($refup.'upload');
?>
<script>document.getElementById('info').style.display='none';</script>
<?

	$upfiles=upfile($url["host"],defport($url), $url["path"].($url["query"] ? "?".$url["query"] : ""),$refup, $cookies, $post, $lfile, $lname, "ufile[]");

?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?
	is_page($upfiles);

	$ddl=cut_str($upfiles,'downlink" size="65" value="','"');
	$del=cut_str($upfiles,'deletelink" size="65" value="','"');
	$download_link=$ddl;
	$delete_link= $del;
	}
	
// Made by Baking 15/07/2009 07:27
// Member upload plugin 16/07/2009 15:37
// Thx to "TheOnly92" for his help
// Fixed By Baking 12/12/2009 16:14
?>