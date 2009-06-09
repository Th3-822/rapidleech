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
//<script>document.getElementById('progressblock').style.display='none';</script>
<?php
		$endlink1 = cut_str($upfiles,'    <a href="http://www.filepub.com','" class="special no_underline2"><h1>View Your Upload');
		$page = geturl("www.filepub.com", 80, "$endlink1", "", 0, 0, 0, "");
		$endlink = cut_str($page,'http://www.filepub.com/pthumbs/small/',"$lname");
		$download_link = 'http://www.filepub.com/pfiles/'."$endlink"."$lname";
// Made by Baking 15/05/2009
// Fixed by Baking 17/05/2009
?>