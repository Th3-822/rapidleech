<table width=600 align=center>
</td></tr>
<tr><td align=center>
	
<div id=info width=100% align=center>Retrive upload ID</div>
<?php
			$ref='http://www.filedropper.com/';
			$page = geturl("www.filedropper.com", 80, "/", "", 0, 0, 0, "");
			preg_match_all('/Set-Cookie: (.*);/U',$page,$temp);
			$cookie = $temp[1];
			$cookies = implode(';',$cookie);
			
?>
<script>document.getElementById('info').style.display='none';</script>
<table width=600 align=center>
</td></tr>
<tr><td align=center>
<?php

			$post["Filename"]="$lname";
			$post["Upload"]="Submit Query";
			$u1='http://www.filedropper.com//index.php?xml=true';
			$url = parse_url($u1);
			$upagent = "Shockwave Flash";
			$upfiles = upfile($url["host"],$url["port"] ? $url["port"] : 80, $url["path"].($url["query"] ? "?".$url["query"] : ""), 0, $cookies, $post, $lfile, $lname, "file",0,$upagent);
?>
<script>document.getElementById('progressblock').style.display='none';</script>
<div id=final width=100% align=center>Get final code</div>
<?php		
			is_page($upfiles);
			$form=cut_str($upfiles,'/index.php',"\n");
			$page = geturl("www.filedropper.com", 80, "/index.php$form", "http://www.filedropper.com/", $cookies, 0, 0, $_GET["proxy"], $pauth);
			$ddl=cut_str($page,'Location: /',"\n");
			
			$download_link='http://www.filedropper.com/'.$ddl;
			


// Made by Baking 27/08/2009 21:53
?>