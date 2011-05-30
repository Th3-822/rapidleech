<table width=600 align=center>
</td></tr>
<tr><td align=center>
<script>document.getElementById('login').style.display='none';</script>
<div id=info width=100% align=center>Retrive upload ID</div>
<?php
		
	$page = geturl("extabit.com", 80, "/", $ref, $cookies, 0, 0, $_GET["proxy"], $pauth);
	is_page($page);
				
	$upfrom = cut_str($page,' block;" action="' ,'"');
	$APC = cut_str($page,'name="APC_UPLOAD_PROGRESS" value="' ,'"');
	$UPK = cut_str($page,'name="upload_key" value="' ,'"');
	$DIR = cut_str($page,'name="folder" value="' ,'"');
	$UID = cut_str($page,'name="uid" value="' ,'"');
	$MAX = cut_str($page,'name="MAX_FILE_SIZE" value="' ,'"');
	
	$upost['APC_UPLOAD_PROGRESS'] = $APC;
	$upost['upload_key'] = $UPK;
	$upost['folder'] = $DIR;
	$upost['uid'] = $UID;
	$upost['MAX_FILE_SIZE'] = $MAX;
	$upost['checkbox_terms'] = 'on';
	if(!$_REQUEST['private']) {$_REQUEST['private'] = 0;}
		else{$_REQUEST['private'] = 1;}
	$upost['private'] = $_REQUEST['private'];
	
	$url=parse_url($upfrom);
?>
<script>document.getElementById('info').style.display='none';</script>
<?php

	$upfiles = upfile($url["host"], defport($url), $url["path"].($url["query"] ? "?".$url["query"] : ""), 0, 0, $upost, $lfile, $lname, "my_file");
?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?php

	$download_link= trim(cut_str($upfiles,'Location:',"\n"));
	
// Made by Baking 27/12/2009 18:23
?>