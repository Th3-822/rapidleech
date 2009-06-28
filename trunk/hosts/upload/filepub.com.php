<?php

$page = geturl("www.filepub.com", 80, "/public", "", 0, 0, 0, "");
$fpost['filename'] = $lname;
$fpost["action"] = "upload";
$fpost["file_1"] = "";
$fpost['upload[public]'] = "1";
$ref = 'http://www.filepub.com/';
$url = parse_url($ref.'public');
?>
<table width=600 align=center>
</td></tr>
<tr><td align=center>
<script>document.getElementById('info').style.display='none';</script>
<?php	

		$upfiles = upfile($url["host"],$url["port"] ? $url["port"] : 80, $url["path"].($url["query"] ? "?".$url["query"] : ""), $ref, 0, $fpost, $lfile, $lname, "file_0");
?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?php
		$endlink = cut_str($upfiles,'    <a href="','" class="special no_underline2"><h1>View Your Upload');
		$download_link = $endlink;
		
// Made by Baking 15/05/2009
?>