<table width=600 align=center>
</td></tr>
<tr><td align=center>
	
<div id=info width=100% align=center>Retrive upload ID</div>
<?
			$ref='http://sharedzilla.com/';
			$page = geturl("sharedzilla.com", 80, "/", 0, 0, 0, 0, "");
			is_page($page);
?>
<script>document.getElementById('info').style.display='none';</script>
<table width=600 align=center>
</td></tr>
<tr><td align=center>
<?

			$post["upload_category_id"]='0';
			$post["upload_description"]=$descript;
			$post["upload_password"]='';

			$uid=mt_rand(1000000,9999999).mt_rand(1000000,9999999);
			$url=parse_url($ref.'en/uploaddo?load-'.$uid);

			$upfiles=upfile($url["host"],$url["port"] ? $url["port"] : 80, $url["path"].($url["query"] ? "?".$url["query"] : ""),$ref, 0, $post, $lfile, $lname, "file");
?>
<script>document.getElementById('progressblock').style.display='none';</script>
<div id=final width=100% align=center>Get final code</div>
<?		
			is_page($upfiles);
			$tmp=cut_str($upfiles,'[URL=',']');
			if (!$tmp) html_error ('Error upload file');
			$download_link=$tmp;
?>
<script>document.getElementById('final').style.display='none';</script>
<?

// sert 31.10.2008
?>