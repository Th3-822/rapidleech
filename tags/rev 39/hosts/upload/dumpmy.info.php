<table width=600 align=center>
</td></tr>
<tr><td align=center>
	
<div id=info width=100% align=center>Retrive upload ID</div>
<?
			$ref='http://dumpmy.info/';
?>
<script>document.getElementById('info').style.display='none';</script>
<table width=600 align=center>
</td></tr>
<tr><td align=center>
<?

			$rnd=time();
			$upurl=$ref.'ubr_link_upload.php?rnd_id='.$rnd.'000';
			$Url=parse_url($upurl);
			$page = geturl($Url["host"], defport($Url), $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $ref, 0, 0, 0, $_GET["proxy"],$pauth);
			is_page($page);
			$uid=cut_str($page,'startUpload("','",');
			if (!$uid) html_error ('Error get upload id');

			$post["mail"]='';
//			$post["flyupload"]='on';
			$post["badongo"]='on';
			$post["depositfiles"]='on';
			$post["megaupload"]='on';
			$post["sendspace"]='on';
			$post["filefactory"]='on';
//			$post["zshare"]='on';
			$post["netload"]='on';
//			$post["easyshare"]='on';
			$post["loadto"]='on';
			$post["rapidshare"]='on';
                  $post["mediafire"]='on';
                  $post["zippyshare"]='on';
                  $post["megashare"]='on';



			$url=parse_url($ref.'cgi/ubr_upload.pl?upload_id='.$uid);

			$upfiles=upfile($url["host"],$url["port"] ? $url["port"] : 80, $url["path"].($url["query"] ? "?".$url["query"] : ""), $ref, 0, $post, $lfile, $lname, "upfile_0");
?>
<script>document.getElementById('progressblock').style.display='none';</script>
<div id=final width=100% align=center>Get final code</div>
<?		
			is_page($upfiles);
			$finish_url=trim(cut_str($upfiles,'Location:',"\n"));
			if (!$finish_url) html_error ('Error get location');
			$Url=parse_url($finish_url);
			$page = geturl($Url["host"], defport($Url), $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $ref, 0, 0, 0, $_GET["proxy"],$pauth);
			is_page($page);
			$tmp=cut_str($page,'link is: <a href="','">');
			if (!$tmp) html_error ('Error get finish url');
			$download_link=$ref.$tmp;
?>
<script>document.getElementById('final').style.display='none';</script>
<?

// sert 28.02.2009 - by pL413R - www.ultrashare.info
?>