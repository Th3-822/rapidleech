<script>document.getElementById('login').style.display='none';</script>
<table width=600 align=center>
</td></tr>
<tr><td align=center>
<div id=info width=100% align=center>Retrive upload ID</div>
<?php

	$ref='http://freakshare.net/';
	$Url=parse_url($ref);
	$page = geturl($Url["host"], defport($Url), $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, 0, 0, 0, $_GET["proxy"],$pauth);
	is_page($page);
	
	$upfrm0 = cut_str($page,'<div id="tabcontent_file">','<fieldset>');
	$upfrm = cut_str($upfrm0,'<form action="','"');
	$refup = cut_str($upfrm0,'<form action="','/upload.php');
	
	$AUP = cut_str($page,'id="progress_key"  value="','"');
	$AUG = cut_str($page,'id="usergroup_key"  value="','"');
	$UID = cut_str($page,'name="UPLOAD_IDENTIFIER" value="','"');
		
	$post['APC_UPLOAD_PROGRESS']= $AUP;
	$post['APC_UPLOAD_USERGROUP']= $AUG;
	$post['UPLOAD_IDENTIFIER']= $UID;
	$url=parse_url($upfrm);

?>
<script>document.getElementById('info').style.display='none';</script>
<?
	$upfiles=upfile($url["host"],defport($url), $url["path"].($url["query"] ? "?".$url["query"] : ""),$refup, 0, $post, $lfile, $lname, "file[]", "file[]");
	is_page($upfiles);
	$rand = mt_rand();
	$id = time().'+'.(rand() * 1000000);
	$json = cut_str($page,'$.getJSON("' ,'"').$id;
	$Url=parse_url($json);
	echo $upfiles;
	$page = geturl($Url["host"], defport($Url), $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 'http://freakshare.net/', 0, 0, 0, $_GET["proxy"],$pauth);

?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?

	$locat=trim(cut_str($upfiles,'Location: ',"\n"));
	$Url=parse_url($locat);
	$page = geturl($Url["host"],  80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 'http://freakshare.net/', 0, 0, 0, $_GET["proxy"],$pauth);
	is_page($page);		
	
	$ddl=cut_str($page,'<td><input type="text" value="','"');
	$del=cut_str($page,'http://freakshare.net/delete/','"');
	$download_link=$ddl;
	$delete_link= 'http://freakshare.net/delete/'.$del;
	
// Made by Baking 17/09/2009 14:16
// Big thanks to Szalinski :)
?>