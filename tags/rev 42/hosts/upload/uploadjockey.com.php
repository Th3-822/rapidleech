<table width=600 align=center>
</td></tr>
<tr><td align=center>
	
<div id=info width=100% align=center>Retrive upload ID</div>
<?php
			$ref='http://uploadjockey.com/';
			$rand = mt_rand(1000000000000, 1999999999999);
			$page = geturl("www.uploadjockey.com", 80, "/", "", 0, 0, 0, "");
			preg_match_all('/Set-Cookie: (.*);/U',$page,$temp);
			$cookie = $temp[1];
			$cookies = implode(';',$cookie);

			$page2 = geturl("uploadjockey.com", 80, "/uj_link_upload.php?_=$rand", "", 0, 0, 0, "");
			$uid=cut_str($page2,'UberUpload.startUpload("','"');

			$url = parse_url("http://uploadjockey.com/cgi-bin/uj_upload.pl?upload_id=$uid");


?>
<script>document.getElementById('info').style.display='none';</script>
<table width=600 align=center>
</td></tr>
<tr><td align=center>
<?php

			$post["megaupload"]="on";
			$post["uploading"]="on";
			$post["depositfiles"]="on";
			$post["terms"]="1";
			$upfiles = upfile($url["host"], defport($url), $url["path"].($url["query"] ? "?".$url["query"] : ""), $ref, $cookies, $post, $lfile, $lname, "upfile_".microtime(true));

?>
<script>document.getElementById('progressblock').style.display='none';</script>
<div id=final width=100% align=center>Get final code</div>
<?php		

			is_page($upfiles);
			$locat=trim(cut_str($upfiles,'Location:',"\n"));
			$LOCAT="http://".cut_str($locat,'http://','&');
			
			$Url = parse_url($LOCAT);
			$page = geturl($Url["host"], defport($Url), $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $ref, $cookies, 0, 0, $_GET["proxy"],$pauth);

			$ddl=cut_str($page,'http://www.uploadjockey.com/download/','"');
						
			$download_link="http://www.uploadjockey.com/download/".$ddl;
			

// Made by Baking 13/09/2009 23:39
?>