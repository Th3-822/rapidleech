<table width=600 align=center>
</td></tr>
<tr><td align=center>

<div id=info width=100% align=center>Retrive upload ID</div>
<?php
			$ref = 'http://asapload.com/';
?>
	<script>document.getElementById('info').style.display='none';</script>
<?

			$post["upload_description"]=$descript;
			$post["upload_password"]="";

//			$post["hosting_6"]="1";	//Badongo.com		1 Gb
			$post["hosting_4"]="1";	//Depositfiles.com	300 Mb
//			$post["hosting_5"]="1";	//Easy-share.com	100 Mb
//			$post["hosting_9"]="1";	//FileFactory.com	300 Mb
//			$post["hosting_8"]="1";	//Megaupload.com	100 Mb
			$post["hosting_2"]="1";	//Rapidshare.com	100 Mb
			$post["hosting_3"]="1";	//Sharedzilla.com	1 Gb
			$post["hosting_10"]="1";//Uploaded.to		250 Mb
//			$post["hosting_7"]="1";	//Zshare.net		100 Mb

			if (count ($post) > 6) html_error ('You may check out a maximum of 4 file hostins.');
			$fsize=getfilesize($lfile);
			if (($post["hosting_5"] || $post["hosting_8"] || $post["hosting_2"] || $post["hosting_7"]) && ($fsize>104857600)) html_error('Max file size 100 Mb');
			if (($post["hosting_10"]) && ($fsize>262144000)) html_error('Max file size 250 Mb');
			if (($post["hosting_4"] || $post["hosting_9"]) && ($fsize>314572800)) html_error('Max file size 300 Mb');
			if (($post["hosting_6"] || $post["hosting_3"]) && ($fsize>1073741824)) html_error('Max file size 1 Gb');

			$uid=mt_rand(1000000,9999999);$uid.=$uid>>1;
			$act_url = $ref.'en/file/uploaddo?load-'.$uid;
			$url=parse_url($act_url);

			$upfiles=upfile($url["host"],defport($url), $url["path"].($url["query"] ? "?".$url["query"] : ""),$ref, 0, $post, $lfile, $lname, "file");

?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?
			is_page($upfiles);

			$tmp = cut_str($upfiles,"[URL=","]");
			if (!$tmp) html_error ('File not upload'.$upfiles);
			$download_link=$tmp;
			$tmp=cut_str($upfiles,'"http://asapload.com/delete/','"');
			if ($tmp) $delete_link='http://asapload.com/delete/'.$tmp;

// sert 09.10.2008
?>