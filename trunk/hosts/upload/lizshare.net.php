<table width=600 align=center>
</td></tr>
<tr><td align=center>
	
<div id=info width=100% align=center>Retrive upload ID</div>
<?php
			$ref='http://lizshare.net/';
			$page = geturl("lizshare.net", 80, "/", "", 0, 0, 0, "");
			$uid=cut_str($page,'UPLOAD_IDENTIFIER" value="','"');
?>
<script>document.getElementById('info').style.display='none';</script>
<table width=600 align=center>
</td></tr>
<tr><td align=center>
<?php

			$post["UPLOAD_IDENTIFIER"]="$uid";
			$post["rec_email"]="";
			$post["your_email"]="";
			$post["message"]="";
			$post["description"]="";
			$post["agree"]="agree";
			$u1='http://lizshare.net/index.php';
			$url = parse_url($ref."index.php");
			$upfiles = upfile($url["host"],$url["port"] ? $url["port"] : 80, $url["path"].($url["query"] ? "?".$url["query"] : ""), $ref, 0, $post, $lfile, $lname, "file1");
?>
<script>document.getElementById('progressblock').style.display='none';</script>
<div id=final width=100% align=center>Get final code</div>
<?php		
			is_page($upfiles);
			$ddl=cut_str($upfiles,'Download Link: <a href="','"');
			$download_link=$ddl;
			


// Made by Baking 14/09/2009 14:48
?>