<script>document.getElementById('login').style.display='none';</script>
<table width=600 align=center>
</td></tr>
<tr><td align=center>
<div id=info width=100% align=center>Retrive upload ID</div>
<?php
			$ref='http://ugotfile.com/';
			$Url=parse_url($ref);
			$page = geturl($Url["host"], defport($Url), $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, 0, 0, 0, $_GET["proxy"],$pauth);
			is_page($page);
?>
<script>document.getElementById('login').style.display='none';</script>
<div id=info width=100% align=center>Retrive upload ID</div>
<?php
			$upload_srv = cut_str($page, 'uploadServer = "', '"');
			$upload_sid = cut_str($page, 'upload_url: uploadServer+"/upload/web?PHPSESSID=', '"');

			$upload_form = $upload_srv."/upload/web?PHPSESSID=".$upload_sid;
			
			$url = parse_url($upload_form);
			$post['Filename'] = $lname;
			$post['destinationFolder'] = 'You must login to save files to your destination folder';
			$post['Upload'] = 'Submit Query';

?>
<script>document.getElementById('info').style.display='none';</script>
<?php	
			$upagent = "Shockwave Flash";
			$upfiles = upfile($url["host"], defport($url), $url["path"].($url["query"] ? "?".$url["query"] : ""), 0, $cookies, $post, $lfile, $lname, "Filedata",0,$upagent);
			is_page($upfiles);
?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?php
			$ddl = cut_str($upfiles,"/file",'"');
			$del = cut_str($upfiles,'?remove=','"');
			$ddl1 = 'http://ugotfile.com/file'.str_replace('\\', "", $ddl);
			$download_link = $ddl1;
			$delete_link = $ddl1.'?remove='.$del;
		
// Made by Baking 15/12/2009 17:42
?>