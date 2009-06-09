<table width=600 align=center>
</td></tr>
<tr><td align=center>
	
<div id=info width=100% align=center>Retrive upload ID</div>
<?php
			$ref='http://openfile.ru/';
			$page = geturl("openfile.ru", 80, "/", "", 0, 0, 0, "");
?>
	<script>document.getElementById('info').style.display='none';</script>
<?php
			is_page($page);
			
			$tmp=cut_str($page,'multipart/form-data','</form>');
			$url_action=cut_str($tmp,'action="','"');
			$id=cut_str($tmp,'"APC_UPLOAD_PROGRESS" value="','"');
			if (!$url_action || !$id)
				{	
					html_error("Error retrive upload id".$page);
				}
			
			$post["USER_LOGIN"]='';
			$post["APC_UPLOAD_PROGRESS"]=$id;
			$post["MAX_FILE_SIZE"]=943718400;
			$post["rules"]='on';

			$url=parse_url($url_action);

			$upfiles=upfile($url["host"],$url["port"] ? $url["port"] : 80, $url["path"].($url["query"] ? "?".$url["query"] : ""),$ref, 0, $post, $lfile, $lname, "file");
?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?php		
			is_page($upfiles);
			$locat=trim(cut_str($upfiles,'window.location.href = "','"'));
			$Url=parse_url($locat);
			$page = geturl($Url["host"], defport($Url), $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $url_action, 0, 0, 0, $_GET["proxy"],$pauth);
			is_page($page);
			is_notpresent($page,'Файл успешно загружен','File not upload'.$upfiles);
			
			$download_link=cut_str($page,'[url=',']');

// sert 30.07.2008
?>