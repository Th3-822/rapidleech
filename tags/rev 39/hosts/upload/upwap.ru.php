<table width=600 align=center>
</td></tr>
<tr><td align=center>
	
<div id=info width=100% align=center>Retrive upload ID</div>
<?php
			$ref='http://upwap.ru/upload/';
			$url=parse_url($ref);
			$page = geturl("upwap.ru", 80, "/upload/", 0, 0, 0, 0, "");
			is_page($page);
			$cookies=GetCookies($page);
?>
<script>document.getElementById('info').style.display='none';</script>
<table width=600 align=center>
</td></tr>
<tr><td align=center>
<?php

			$post["desc"]=$descript;
			$post["password"]='';
			$post["send"]='Отправить!';

			$upfiles=upfile($url["host"],$url["port"] ? $url["port"] : 80, $url["path"].($url["query"] ? "?".$url["query"] : ""),$ref, $cookies, $post, $lfile, $lname, "file");
?>
<script>document.getElementById('progressblock').style.display='none';</script>
<div id=final width=100% align=center>Get final code</div>
<?php		
			is_page($upfiles);
			$tmp1=cut_str($upfiles,'value="http://upwap.ru/','"');
			$tmp2=cut_str($upfiles,'<a href="/','"');
			if (is_numeric($tmp1)) $id=$tmp1;
			if (is_numeric($tmp2)) $id=$tmp2;
			if (!$id) html_error ('Upload error <br>'.$upfiles);
			else $download_link='http://upwap.ru/'.$id.'/';
?>
<script>document.getElementById('final').style.display='none';</script>
<?php

// sert 17.08.2008
?>