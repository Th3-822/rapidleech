<table width=600 align=center>
</td></tr>
<tr><td align=center>

<div id=info width=100% align=center>Retrive upload ID</div>
<?php
			$ref = 'http://www.filezzz.com/';
?>
<script>document.getElementById('info').style.display='none';</script>
<?

			$Url=parse_url($ref);
			$page = geturl($Url["host"], defport($Url), $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, 0, 0, 0, $_GET["proxy"],$pauth);
			is_page($page);
			$form=cut_str($page,'<form','</form>');
			$upurl=cut_str($form,'action="','"');
			if (!$upurl) html_error ('Error get upload url');
			$cookies=implode("; ", GetCookies($page, true));
//			$cookies='PHPSESSID='.cut_str($page,'PHPSESSID=',';').'; uid='.cut_str($page,'uid=',';');

//			$post["submit"]='Upload';

			$url=parse_url($upurl);

			$upfiles=@upfile($url["host"],defport($url), $url["path"].($url["query"] ? "?".$url["query"] : ""),$ref, $cookies, $post, $lfile, $lname, "userfile");

?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?
			is_page($upfiles);
			$cookies.='; '.implode("; ", GetCookies($page, true));
			$finish_url=trim(cut_str($upfiles,'Location:',"\n"));
			if (!$finish_url) html_error ('Error get location 1');
			$Url=parse_url($finish_url);
			$page = geturl($Url["host"], defport($Url), $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $ref, $cookies, 0, 0, $_GET["proxy"],$pauth);
			is_page($page);
			$finish_url=trim(cut_str($page,'Location: /',"\n"));
			if (!$finish_url) html_error ('Error get location 2');
			$Url=parse_url($ref.$finish_url);
			$page = geturl($Url["host"], defport($Url), $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $ref, $cookies, 0, 0, $_GET["proxy"],$pauth);
			is_page($page);
			$tmp=cut_str($page,'Download link',true);
			$tmp=cut_str($tmp,'href="','"');
			if (!$tmp) html_error ('Error get download url <br>'.$page);
			$download_link=$tmp;
			$tmp=cut_str($page,'Delete link',true);
			$tmp=cut_str($tmp,'href="','"');
			$delete_link=$tmp;

// sert 27.12.2008
?>