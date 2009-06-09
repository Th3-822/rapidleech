<table width=600 align=center>
</td></tr>
<tr><td align=center>

<div id=info width=100% align=center>Retrive upload ID</div>
<?php
	$page = geturl("www.0upload.com", 80, "/", 0, 0, 0, 0, "");
	is_page($page);
	$ref='http://www.0upload.com/';
	$ref1=$ref.'index.php?p=upload&';
	$url=parse_url($ref1);
	$post['sessionid']=cut_str($page,'"sessionid" value="','"');
	$post['server']=cut_str($page,'"server" value="','"');
	$post['password']='';
	$post['featured']='0';
	$post['description']=$descript;
	$post['email']='';

?>
<script>document.getElementById('info').style.display='none';</script>
<?php

	$upfiles=upfile($url["host"],defport($url), $url["path"].($url["query"] ? "?".$url["query"] : ""),$ref, 0, $post, $lfile, $lname, "attached");

?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?php
	is_page($upfiles);
	$url=trim(cut_str($upfiles,"location = '","'"));
	if (!$url) html_error ('Error get location url');
	$url=parse_url($url);
	$page = geturl($url["host"],defport($url),$url['path'].($url["query"] ? "?".$url["query"] : ""),$ref1,0,0,0,0,0);
	is_page($page);

	$tmp=cut_str($page,'Download Link',true);
	$download_link=cut_str($tmp,'<a href="','"');
	$tmp=cut_str($tmp,' Delete File',true);
	$delete_link=cut_str($tmp,'<a href="','"');

// sert 26.07.2008
?>