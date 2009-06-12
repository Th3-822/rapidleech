<table width=600 align=center>
</td></tr>
<tr><td align=center>
	
<div id=info width=100% align=center>Retrive upload ID</div>
<?
//			$megacookie="user=;";
			$cookie = $megacookie ? $megacookie : "";
			$page = geturl("www.megaporn.com", 80, "/?setlang=en", 0, $cookie, 0, 0, "");
?>
	<script>document.getElementById('info').style.display='none';</script>
<?
			is_page($page);

			$url_action=cut_str($page,'multipart/form-data" action="','"');
			if (!$url_action) html_error("Error retrive upload link");
			
			$post["multimessage_0"]= $lname;
			$post["trafficurl"]="http://";
			
			$url=parse_url($url_action);
			$upfiles=upfile($url["host"],$url["port"] ? $url["port"] : 80, $url["path"].($url["query"] ? "?".$url["query"] : ""), 0, $cookie, $post, $lfile, $lname, "multifile_0");

?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?		
			is_page($upfiles);
			$download_link=cut_str($upfiles,"B', '","'");


// sert 10.04.2009 by pL413R
?>