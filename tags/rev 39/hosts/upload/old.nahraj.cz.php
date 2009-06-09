<table width=600 align=center>
</td></tr>
<tr><td align=center>

<div id=info width=100% align=center>Retrive upload ID</div>
<?php
			$ref = 'http://www.old.nahraj.cz/';
?>
	<script>document.getElementById('info').style.display='none';</script>
<?

			$Url=parse_url($ref);
			$page = geturl($Url["host"], defport($Url), $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, 0, 0, 0, $_GET["proxy"],$pauth);
			is_page($page);
			$form=cut_str($page,'<form','</form>');
			$upurl=cut_str($form,'action="','"');
			if (!$upurl) html_error ('Error get upload url');
			$cookies=GetCookies($page);

			$post["UPLOAD_IDENTIFIER"]=$lname;
			$post["agree"]="yes";
			$post["upload"]="Nahraj soubor";
			$post["description[]"]=$descript;

			$url=parse_url($ref.$upurl);

			$upfiles=upfile($url["host"],defport($url), $url["path"].($url["query"] ? "?".$url["query"] : ""),$ref, $cookies, $post, $lfile, $lname, "upload_file[]");

?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?
			is_page($upfiles);
			$finish_url=trim(cut_str($upfiles,'Location:',"\n"));
			if (!$finish_url) html_error ('Error get location');
			$Url=parse_url($finish_url);
			$page = geturl($Url["host"], defport($Url), $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $ref, $cookies, 0, 0, $_GET["proxy"],$pauth);
			is_page($page);
			$tmp=cut_str($page,'[url=',']');
			if (!$tmp) html_error ('Error get download url <br>');
			$download_link=$tmp;

// sert 15.10.2008
?>