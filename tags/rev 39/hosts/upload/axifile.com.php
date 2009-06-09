<table width=600 align=center>
</td></tr>
<tr><td align=center>
	
<div id=info width=100% align=center>Retrive upload ID</div>
<?php
			$page = geturl("www.axifile.com", 80, "/", "", 0, 0, 0, "");
?>
	<script>document.getElementById('info').style.display='none';</script>
<?php
			is_page($page);
			
			$id=cut_str($page,'name="dlid" value="','">');
			
			$url_action='http://www.axifile.com/upload.php';

			if (!$url_action || !$id)
				{	
					html_error("Error retrive upload id".$page);
				}
			
			$post["dlid"]=$id;
			$post["MAX_FILE_SIZE"]=153600000;

			$url=parse_url($url_action);

			$upfiles=upfile($url["host"],$url["port"] ? $url["port"] : 80, $url["path"].($url["query"] ? "?".$url["query"] : ""),"http://www.axifile.com/", 0, $post, $lfile, $lname, "file");
?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?php		
			is_page($upfiles);
			is_notpresent($upfiles,'name="fldFileName"','File not upload');
			
			$fileid=trim(cut_str($upfiles,'name="fldFileId" value="','"'));
			$admid=trim(cut_str($upfiles,'name="fldFileId1" value="','"'));
			
			$download_link="http://www.axifile.com/?$fileid";
			$adm_link="http://www.axifile.com/fi.php?f=$fileid&s=$admid";  
?>