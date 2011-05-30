<table width=600 align=center>
</td></tr>
<tr><td align=center>
<?php

	$url=parse_url('http://www.imagehost.org/');
	$post["a"]="upload";
	$upfiles=upfile($url["host"],defport($url), $url["path"].($url["query"] ? "?".$url["query"] : ""),'http://www.imagehost.org/', 0, $post, $lfile, $lname, "file[]");
	
?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?
	is_page($upfiles);
	
	$tmp = cut_str($upfiles,'Download Link</td>','</tr>');
	$ddl = cut_str($tmp,'size="50" value="','"');
	$download_link= $ddl;
	
// Made by Baking 05/09/2009 14:12
?>