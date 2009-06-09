<table width=600 align=center>
</td></tr>
<tr><td align=center>

<div id=info width=100% align=center>Retrive upload ID</div>
<?php
			$ref = 'http://www.uploadground.com/';
?>
	<script>document.getElementById('info').style.display='none';</script>
<?

			$rnd=time();
			$upurl=$ref.'ubr_link_upload.php?rnd_id='.$rnd.'000';
			$Url=parse_url($upurl);
			$page = geturl($Url["host"], defport($Url), $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $ref, 0, 0, 0, $_GET["proxy"],$pauth);
			is_page($page);
			$uid=cut_str($page,'startUpload("','",');
			if (!$uid) html_error ('Error get upload id');

			$post["rapidshare"]="on";
			$post["megaupload"]="on";
			$post["filefactory"]="on";
//			$post["easyshare"]="on";
			$post["zshare"]="on";
			$post["depositfiles"]="on";
			$post["badongo"]="on";
			$post["loadto"]="on";
//			$post["flyupload"]="on";
			$post["netload"]="on";
			$post["sendspace"]="on";
			$post["sharedzilla"]="on";

			if (count ($post) > 10) html_error ('You may check out a maximum of 10 file hostins.');
			$url=parse_url($ref.'cgi/ubr_upload.pl?upload_id='.$uid);

			$upfiles=upfile($url["host"],defport($url), $url["path"].($url["query"] ? "?".$url["query"] : ""),$ref, 0, $post, $lfile, $lname, "upfile_0");

?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?
			is_page($upfiles);

			$finish_url=trim(cut_str($upfiles,'Location:',"\n"));
			if (!$finish_url) html_error ('Error get location');
			$Url=parse_url($finish_url);
			$page = geturl($Url["host"], defport($Url), $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $ref, 0, 0, 0, $_GET["proxy"],$pauth);
			is_page($page);
			$tmp=cut_str($page,'<a href="files/','"');
			if (!$tmp) html_error ('Error get finish url');
			$download_link=$ref.'files/'.$tmp;

// sert 10.10.2008
?>