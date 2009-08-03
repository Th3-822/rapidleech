<table width=600 align=center>
</td></tr>
<tr><td align=center>
<script>document.getElementById('login').style.display='none';</script>
<div id=info width=100% align=center>Retrive upload ID</div>
<?php

	$ref='http://sharebase.to/upload/';
	$Url=parse_url($ref);
	$page = geturl($Url["host"], defport($Url), $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, 0, 0, 0, $_GET["proxy"],$pauth);
	is_page($page);
	
	$upsrv = cut_str($page,'name="usrv" type="hidden" value="','"');
	$tmp01 = cut_str($page,'<input name="umid" type="hidden" value="">','</div>');
	$upbtn = cut_str($tmp01,'class="fform" size="45"> <input name="','"');
	
	$post['usrv']= $upsrv;
	$post['umid']= '';
	$post[$upbtn]= 'Upload Now !';
	
	$refup = 'http://'.$upsrv.'.sharebase.to/';
	$url=parse_url($refup.'upload/');
	

?>
<script>document.getElementById('info').style.display='none';</script>
<?

	$upfiles=upfile($url["host"],defport($url), $url["path"].($url["query"] ? "?".$url["query"] : ""),$refup, 0, $post, $lfile, $lname, "ufile");

?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?
	is_page($upfiles);

	$ddl=cut_str($upfiles,'downlink" size="65" value="','"');
	$del=cut_str($upfiles,'deletelink" size="65" value="','"');
	$download_link=$ddl;
	$delete_link= $del;
	
// Made by Baking 15/07/2009 07:27
?>