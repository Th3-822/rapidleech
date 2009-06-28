<table width=600 align=center>
</td></tr>
<tr><td align=center>
	
<div id=info width=100% align=center>Retrive upload ID</div>
<?php
			$ref='http://multiup.org/';
			$page = geturl("multiup.org", 80, "/", "", 0, 0, 0, "");
			
?>
<script>document.getElementById('info').style.display='none';</script>
<table width=600 align=center>
</td></tr>
<tr><td align=center>
<?php

			$post["Filename"]="$lname";
			echo "$lname";
			$u1='http://multiup.org/script.php';
			$url = parse_url($u1);
			$upfiles = upfile($url["host"],$url["port"] ? $url["port"] : 80, $url["path"].($url["query"] ? "?".$url["query"] : ""), 0, 0, $post, $lfile, $lname, "photoupload");
?>
<script>document.getElementById('progressblock').style.display='none';</script>
<div id=final width=100% align=center>Get final code</div>
<?php		
			is_page($upfiles);
			$form=cut_str($upfiles,'href=http:\/\/multiup.org\/?lien=','>http:');
			$download_link='http://multiup.org/?lien='.$form;
			


// Made by Baking 21/06/2009 21:37
?>