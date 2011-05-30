<table width=600 align=center>
</td></tr>
<tr><td align=center>
	
<div id=info width=100% align=center>Retrive upload ID</div>
<?php
			$ref='http://www.rapidspread.com/';
			$Url=parse_url($ref);
			$page = geturl($Url["host"], defport($Url), $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, 0, 0, 0, $_GET["proxy"],$pauth);
			is_page($page);
			$cookies=implode("; ", GetCookies($page, true));

?>
<script>document.getElementById('info').style.display='none';</script>
<table width=600 align=center>
</td></tr>
<tr><td align=center>
<?php
			$post["Uploaded.to_d0"]='on';	// 250 MB
			$post["Mediafire_d0"]='on';		// 100 MB
			$post["Rapidshare_d0"]='on';	// 100 MB
			$post["Zippyshare_d0"]='on';	// 100 MB
			$post["Zshare_d0"]='on';		// 100 MB
			$post["Sendspace_d0"]='on';		// 300 MB
			$post["Badongo_d0"]='on';		// 1000 MB
			$post["FileFactory_d0"]='on';	// 300 MB
			$post["EasyShare_d0"]='on';		// 100 MB
			$post["Depositfiles_d0"]='on';	// 4000 MB
			$post["terms"]='checkbox';
			$post["x"]='';
			$post["y"]='';
			

			$url=parse_url($ref.'upload');

			$upfiles=upfile($url["host"],$url["port"] ? $url["port"] : 80, $url["path"].($url["query"] ? "?".$url["query"] : ""),$ref, $cookies, $post, $lfile, $lname, "d0");
?>
<script>document.getElementById('progressblock').style.display='none';</script>
<div id=final width=100% align=center>Get final code</div>
<?php		
			is_page($upfiles);
			$locat=trim(cut_str($upfiles,'Location:',"\n"));
			$download_link=cut_str($upfiles,'<a target="top" href="','"><b>');
			//if (!$locat) html_error ('Error get location<br>'.$upfiles);
			//$download_link=cut_str($upfiles,'<a target="top" href="','"><b>');
			//$Url=parse_url($locat);
			//$page = geturl($Url["host"], defport($Url), $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $ref, $cookies, 0, 0, $_GET["proxy"],$pauth);
			//is_page($page);
			//$download_link=cut_str($upfiles,'<a target="top" href="','"><b>');
?>
<script>document.getElementById('final').style.display='none';</script>
<?php

// sert 04.08.2008
//fixed by Baking 15/05/2009
?>