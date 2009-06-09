<table width=600 align=center>
<tr><td align=center>
	
<?php
			$ref='http://maxload.ru/';
			$page = geturl("maxload.ru", 80, "/", 0, 0, 0, 0, "");
			is_page($page);
			$agent='Shockwave Flash';
?>
<script>document.getElementById('info').style.display='none';</script>
<table width=600 align=center>
<tr><td align=center>
<?php

			$post["Filename"]=$lname;
			$post["Upload"]='Submit Query';

			$url=parse_url($ref.'sup/upload.php');

			$upfiles=upfile($url["host"],$url["port"] ? $url["port"] : 80, $url["path"].($url["query"] ? "?".$url["query"] : ""),0, 0, $post, $lfile, $lname, "Filedata");
?>
<script>document.getElementById('progressblock').style.display='none';</script>
<div id=final width=100% align=center>Get final code</div>
<?php		
			is_page($upfiles);
			if (strpos($upfiles,'<div class="error">')) echo "Error: ".cut_str($upfiles,'<div class="error">','</div>')."<br><br>$upfiles";
			$download_link=cut_str($upfiles,'value="','"');;
?>
<script>document.getElementById('final').style.display='none';</script>
<?php

// sert 11.08.2008
?>