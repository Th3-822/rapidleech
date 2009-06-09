<table width=600 align=center>
</td></tr>
<tr><td align=center>
	
<div id=info width=100% align=center>Retrive upload ID</div>
<?php
			$ref='http://filebeam.com/';
?>
<script>document.getElementById('info').style.display='none';</script>
<table width=600 align=center>
</td></tr>
<tr><td align=center>
<?php

			$post["myemail"]='';
			$post["pprotect"]='';

			$url=parse_url($ref.'upload.php');

			$upfiles=upfile($url["host"],$url["port"] ? $url["port"] : 80, $url["path"].($url["query"] ? "?".$url["query"] : ""),$ref, 0, $post, $lfile, $lname, "upfile");
?>
<script>document.getElementById('progressblock').style.display='none';</script>
<div id=final width=100% align=center>Get final code</div>
<?php		
			is_page($upfiles);
			
			$tmp=cut_str($upfiles,'DOWNLOAD this file','</a>');
			$download_link=cut_str($tmp,'<a href="','"');
			$tmp=cut_str($upfiles,'DELETE this file','</a>');
			$delete_link=cut_str($tmp,'<a href="','"');
?>
<script>document.getElementById('final').style.display='none';</script>
<?php

// sert 11.08.2008
?>