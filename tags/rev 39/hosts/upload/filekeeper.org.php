<table width=600 align=center>
</td></tr>
<tr><td align=center>
	
<div id=info width=100% align=center>Retrive upload ID</div>
<?php
			$ref='http://filekeeper.org/';
			$refu='http://filekeeper.org/upload/index.php';
			$Url=parse_url($ref.'upload/index.php');
			$page = geturl($Url["host"], defport($Url), $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $ref, 0, 0, 0, $_GET["proxy"],$pauth);
			is_page($page);
?>
<script>document.getElementById('info').style.display='none';</script>
<table width=600 align=center>
</td></tr>
<tr><td align=center>
<?php

			$tmp=cut_str($page,'<form','>');
			$url=cut_str($tmp,'action="','"');
			$urlup=$ref.'upload/'.$url;
			$url=parse_url($urlup);

			@$upfiles=upfile($url["host"],$url["port"] ? $url["port"] : 80, $url["path"].($url["query"] ? "?".$url["query"] : ""),$refu, 0, $post, $lfile, $lname, "upfile_0");
?>
<script>document.getElementById('progressblock').style.display='none';</script>
<div id=final width=100% align=center>Get final code</div>
<?php		
			is_page($upfiles);
			$redir=cut_str($upfiles,'action="','"');
			$post["temp_sid"]=cut_str($upfiles,"'temp_sid' value=\"",'"');
			$post["param_dir"]=cut_str($upfiles,"'param_dir' value=\"",'"');
			$Url=parse_url($ref.'upload/'.$redir);
			$page = geturl($Url["host"], defport($Url), $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $urlup, 0, $post, 0, $_GET["proxy"],$pauth);
			is_page($page);
			$tmp=cut_str($page,'Uploaded File','</a>');
			if ($tmp) $download_link=cut_str($tmp,'a href="','"');
?>
<script>document.getElementById('final').style.display='none';</script>
<?php

// sert 14.08.2008
?>