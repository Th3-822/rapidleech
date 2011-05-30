<table width=600 align=center>
</td></tr>
<tr><td align=center>
	
<div id=info width=100% align=center>Retrive upload ID</div>
<?php
			$ref='http://upload.mig33rules.com/';
			$Url=parse_url($ref);
			$page = geturl($Url["host"], defport($Url), $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, 0, 0, 0, $_GET["proxy"],$pauth);
			
?>
<script>document.getElementById('info').style.display='none';</script>
<table width=600 align=center>
</td></tr>
<tr><td align=center>
<?php

			$post["Upload"]="Submit Query";
			$u1='http://upload.mig33rules.com/upload.php?do=verify';
			$url = parse_url($u1);
			$upfiles = upfile($url["host"],$url["port"] ? $url["port"] : 80, $url["path"].($url["query"] ? "?".$url["query"] : ""), 0, 0, $post, $lfile, $lname, "upfile");
?>
<script>document.getElementById('progressblock').style.display='none';</script>
<div id=final width=100% align=center>Get final code</div>
<?php		
			
			is_page($upfiles);
			$download_link=cut_str($upfiles,'Your download link </center> <p><center> <a href="','"');
			$delete_link=cut_str($upfiles,'delete link </center> <p><center> <a href="','"');
			


// Made by Baking 21/10/2009 21:00
?>