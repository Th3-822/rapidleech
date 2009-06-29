<table width=600 align=center>
</td></tr>
<tr><td align=center>
	
<div id=info width=100% align=center>Retrive upload ID</div>
<?php
			$ref='http://mega.1280.com/';
			$Url=parse_url($ref);
			$page = geturl($Url["host"], $url["port"] ? $url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, 0, 0, 0, $_GET["proxy"],$pauth);
			is_page($page);

			$post["APC_UPLOAD_PROGRESS"]=cut_str($page,'fileKey" value="','"');
			$post["uploadfile"]='uploadfile';
			$post["txt_rmail"]='';
			$post["txt_ymail"]='';
			$post["txt_fdes"]=$descript;
			$post["check_accept2"]='check_accept';

			$url=parse_url($ref.'upload.php');
?>
<script>document.getElementById('info').style.display='none';</script>
<table width=600 align=center>
</td></tr>
<tr><td align=center>
<?
			$upfiles=upfile($url["host"],$url["port"] ? $url["port"] : 80, $url["path"].($url["query"] ? "?".$url["query"] : ""),$ref, 0, $post, $lfile, $lname, "fileupload");
?>
<script>document.getElementById('progressblock').style.display='none';</script>
<div id=final width=100% align=center>Get final code</div>
<?php		
			is_page($upfiles);

			if(!preg_match("#http://mega.1280.com/file/[^'\"<]+/#", $upfiles, $preg)) html_error("Error get direct link");
			$download_link=$preg[0];
?>
<script>document.getElementById('final').style.display='none';</script>
<?php

// sert 12.04.2009
?>