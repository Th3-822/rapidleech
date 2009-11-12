<table width=600 align=center>
</td></tr>
<tr><td align=center>
<div id=info width=100% align=center>Retrive upload ID</div>
<?php
			$url = "http://www.shragle.com/";
			$Url = parse_url($url);
			$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, 0, 0, 0, $_GET["proxy"],$pauth);
			is_page($page);
			
			$up=cut_str($page,'name="upload" action="','"');
			$maxsize=cut_str($page,'name="MAX_FILE_SIZE" value="','"');
			$uid=cut_str($page,'name="UPLOAD_IDENTIFIER" value="','"');
			
			$fpost["MAX_FILE_SIZE"] = $maxsize;
			$fpost['UPLOAD_IDENTIFIER'] = $uid;
			$fpost["description"] = "";
			$fpost['recipient'] = "";
			$fpost['sender'] = "";
			$fpost['x'] = mt_rand(1, 100);
			$fpost['y'] = mt_rand(1, 50);
			
			
?>
<script>document.getElementById('info').style.display='none';</script>
<?php 		
			$url = parse_url($up);
			$upfiles = upfile($url["host"],$url["port"] ? $url["port"] : 80, $url["path"].($url["query"] ? "?".$url["query"] : ""),$up, 0, $fpost, $lfile, $lname, "file_1");
?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?php 	
			is_page($upfiles);
			
			$id=cut_str($upfiles,'name="files" value="','"');
			$upost['files'] = $id;
			$page = geturl("www.shragle.com", 80, "/", "http://www.shragle.com/", 0, $upost, 0, "");
			is_page($page);
			
			$download_link = cut_str($upfiles,'Link: <a href="','"');
			$delete_link = cut_str($upfiles,'sch-Link: <a href="','"');

// Made by Baking 26/09/2009 13:44
?>