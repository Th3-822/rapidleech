<table width=600 align=center>
</td></tr>
<tr><td align=center>

<div id=info width=100% align=center>Retrive upload ID</div>
<?php
			$ref = 'http://dl4.ru/';
?>
	<script>document.getElementById('info').style.display='none';</script>
<?

			$Url=parse_url($ref);
			$page = geturl($Url["host"], defport($Url), $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, 0, 0, 0, $_GET["proxy"],$pauth);
			is_page($page);
			$upurl=cut_str($page,'upload_url: "/','"');
			if (!$upurl) html_error ('Error get upload url');
			$cookies=GetCookies($page);

			$post["Filename"]=$lname;
			$post["MAX_FILE_SIZE"]="104857600";
			$post["moduls"]="3#4#5#6";
			$post["Upload"]="Submit Query";

			$url=parse_url($ref.$upurl);

			$upfiles=upfile($url["host"],defport($url), $url["path"].($url["query"] ? "?".$url["query"] : ""),$ref, $cookies, $post, $lfile, $lname, "Filedata");

?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?
			is_page($upfiles);

			$tmp=cut_str($upfiles,'["','"]');
			if (!is_numeric($tmp)) html_error ('Error get finish url <br>'.$upfiles);
			$download_link=$ref.'file/view/'.$tmp;

// sert 10.10.2008
?>