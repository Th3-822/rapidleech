<table width=600 align=center>
</td></tr>
<tr><td align=center>
	
<div id=info width=100% align=center>Retrive upload ID</div>
<?php
			$page = geturl("files.to", 80, "/upload");

			is_page($page);
            
            $action_url = cut_str($page,'progress_key" value="','"');
            if (!$action_url) html_error("Error Get Upload Id / Ошибка получения идентификатора");
            
            $post['APC_UPLOAD_PROGRESS']=$action_url;
//            $post['MAX_FILE_SIZE']=104857600;
//            $post['description']=$descript;
            $post['txt_email'] = '';
			$post['txt_email_t'] = '';
            $post['cb_agb'] = 'true';
//            $post['sessionid'] = ''; 

            $url = parse_url("http://files.to/upload");         


?>
	<script>document.getElementById('info').style.display='none';</script>
<?php			
			
			$upfiles=upfile($url["host"],defport($url), $url["path"].($url["query"] ? "?".$url["query"] : ""),"http://files.to/upload", 0, $post, $lfile, $lname, "file[]");

?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?php		
			is_page($upfiles);
            #print_r("<!--".$upfiles."-->");
			is_notpresent($upfiles,'successfully','File not received / Ошибка аплоада файла!');            
            
            $tmp = cut_str($upfiles,'Download: <a href="','"');
            if (!$tmp)html_error("Error Get Download Link / Ошибка получения ссылки на скачку");
            $download_link = $tmp;
            $tmp2= cut_str($upfiles,'Delete: <a href="','"');
            if($tmp) $delete_link = $tmp2;

            // Upload plug'in from Director Of Zoo (member ru-board <kamyshew>). Only for Rapid Get script. 2007. No Rapidkill!!!!!!!!!!!!!
// Edited by sert 26.05.2008
?>