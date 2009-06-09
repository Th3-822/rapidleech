<table width=600 align=center>
</td></tr>
<tr><td align=center>
	
<div id=info width=100% align=center>Retrive upload ID</div>
<?php
			$ref='http://4ppl.ru/';
?>
<script>document.getElementById('info').style.display='none';</script>
<table width=600 align=center>
</td></tr>
<tr><td align=center>
<?php

			$post["password"]='';
			$post["description"]=$descript;
			$post["link"]='';

			$url=parse_url($ref.'upload/');

			$upfiles=upfile($url["host"],$url["port"] ? $url["port"] : 80, $url["path"].($url["query"] ? "?".$url["query"] : ""),$ref, 0, $post, $lfile, $lname, "f");
?>
<script>document.getElementById('progressblock').style.display='none';</script>
<div id=final width=100% align=center>Get final code</div>
<?php		
			is_page($upfiles);
			is_notpresent ($upfiles,'Был загружен файл','Ошибка загрузки файла <br>'.$upfiles);
			$download_link=cut_str($upfiles,"value='","'");
?>
<script>document.getElementById('final').style.display='none';</script>
<?php

// sert 17.08.2008
?>