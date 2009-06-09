<table width=600 align=center>
</td></tr>
<tr><td align=center>
<script>document.getElementById('info').style.display='none';</script>
<?php
$ref="http://filesurf.ru/";
$url=parse_url($ref."upload/");
$post['password']="";
$post['description']=$descript;
$post['link']="";
			
			$upfiles=upfile($url["host"],defport($url), $url["path"].($url["query"] ? "?".$url["query"] : ""),$ref, 0, $post, $lfile, $lname, "f");
			is_page($upfiles);

?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?php
is_notpresent($upfiles,'Был загружен',"Error upload file / Ошибка загрузки файла");
	$download_link = "http".cut_str($upfiles,"value='http","'");
// sert 07.06.2008
?>