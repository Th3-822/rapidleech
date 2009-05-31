<table width=600 align=center>
</td></tr>
<tr><td align=center>
	
<div id=info width=100% align=center>Retrive upload ID</div>
<?php
			$ref='http://gettyfile.ru/create/';
			$page = geturl("gettyfile.ru", 80, "/create/", 0, 0, 0, 0, "");
			is_page($page);
?>
<script>document.getElementById('info').style.display='none';</script>
<table width=600 align=center>
</td></tr>
<tr><td align=center>
<?php

			$hdd=cut_str($page,'"hdd" value="','"');
			$id=str_replace('.','',microtime(1)).'0';
			$post["hdd"]=$hdd;
			$post["header"]='my_files';
			$post["quest"]='';
			$post["answ"]='';
			$post["text"]=$descript;
			$post["tg"]='1';
			$post["term"]='1';
			$Url=parse_url($ref.'ubr/ubr_link_upload.php?rnd_id='.$id.'&hdd='.$hdd);
			$page = geturl($Url["host"], defport($Url), $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, 0, 0, 0, $_GET["proxy"],$pauth);
			$uid=cut_str($page,'startUpload("','"');
			if (!$uid) html_error ('Error get UploadID');
			$url=parse_url($ref.'cgi-bin/ubr_upload.pl?upload_id='.$uid.'&hdd='.$hdd);

			$upfiles=upfile($url["host"],$url["port"] ? $url["port"] : 80, $url["path"].($url["query"] ? "?".$url["query"] : ""),$ref, 0, $post, $lfile, $lname, "upfile_0");
?>
<script>document.getElementById('progressblock').style.display='none';</script>
<div id=final width=100% align=center>Get final code</div>
<?php		
			is_page($upfiles);
			$locat=trim(cut_str($upfiles,'Location:',"\n"));
			if (!$locat) html_error ('Error get location'.$upfiles);
			$Url=parse_url($locat);
			$page = geturl($Url["host"], defport($Url), $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $ref, 0, 0, 0, $_GET["proxy"],$pauth);
			is_page($page);
			$locat=trim(cut_str($page,'Location:',"\n"));
			if (!$locat) html_error ('Error get location2'.$page);
			$locat=$ref.str_replace('./','',$locat);
			$Url=parse_url($locat);
			$page = geturl($Url["host"], defport($Url), $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $ref, 0, 0, 0, $_GET["proxy"],$pauth);
			echo 'ftp://'.cut_str($page,'"ftp://','"').'<br>';
			$download_link=cut_str($page,"copy('","')");
?>
<script>document.getElementById('final').style.display='none';</script>
<?php

// sert 17.08.2008
?>