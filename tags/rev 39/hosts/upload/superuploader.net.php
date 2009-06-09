<table width=600 align=center>
</td></tr>
<tr><td align=center>

<div id=info width=100% align=center>Retrive upload ID</div>
<?php
			$ref = 'http://superuploader.net/';
?>
<script>document.getElementById('info').style.display='none';</script>
<?

			$Url=parse_url($ref);
			$page = geturl($Url["host"], defport($Url), $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, 0, 0, 0, $_GET["proxy"],$pauth);
			is_page($page);
			$form=cut_str($page,'<form','</form>');
			$upurl=cut_str($form,'action="','"');
			
			$url=parse_url($ref.$upurl);

			$upfiles=upfile($url["host"],defport($url), $url["path"].($url["query"] ? "?".$url["query"] : ""),$ref, $cookies, $post, $lfile, $lname, "userfile");

?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?
	
	
			is_page($page);
			
			$tmp = cut_str($upfiles,"Location: /",true);
            $download_link = cut_str($tmp,'<textarea id="link1" onclick="this.select()" rows="2" cols="50"/>','</textarea><br/>');

			

// sert 27.12.2008
?>