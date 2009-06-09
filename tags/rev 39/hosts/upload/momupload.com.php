<table width=600 align=center>
</td></tr>
<tr><td align=center>
	
<div id=info width=100% align=center>Retrive upload ID</div>
<?php
			$page = geturl("momupload.com", 80, "/qupload/", "", 0, 0, 0, "");

			is_page($page);
			
			$post['desc']=$descript;
			$post['submit']="Upload Now!";
			$post['css_name']="";
			$post['tmpl_name']="";
			$post['dir']="";
			$post['currst']="08";
			$post['checkbox']="checkbox";
			
			$sid = rand(1000,9999).'0'.rand(1000,9999);
			$action_url = cut_str($page,'form-data" action="','"');
            
			if (!$action_url){	
				html_error("Error retrive upload id".$page);
			}

			$action_url .=$sid;  
			$url=parse_url($action_url);
			$ref1=$url;

?>
	<script>document.getElementById('info').style.display='none';</script>
<?php

			$upfiles=upfile($url["host"],defport($url), $url["path"].($url["query"] ? "?".$url["query"] : ""),"http://momupload.com/qupload/", 0, $post, $lfile, $lname, "file1x");

?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?php		
			is_page($upfiles);
            is_notpresent($upfiles,"upload_result.php","Error Upload File!");
            
            unset($post);
            $post['key']=cut_str($upfiles,"key' value=",">");
            $post['attach_ip']=cut_str($upfiles,"attach_ip' value=",">");
            $post['attach_file']=cut_str($upfiles,"attach_file' value='","'>");
            $post['attach_ext']=cut_str($upfiles,"attach_ext' value='","'>");
            $post['attach_location']=cut_str($upfiles,"attach_location' value='","'>");
            $post['attach_filesize']=cut_str($upfiles,"attach_filesize' value=",">");  
            $post['attach_desc'] = $descript;
            $post['attach_session']=cut_str($upfiles,"attach_session' value='","'>");
            $post['attach_dir']="";
			$post['attach_currst']="08";
            
            $page = geturl("momupload.com", 80, "/upload_result.php", $ref1, 0, $post, 0, ""); 
            is_page($page);
            is_notpresent($page,"Please wait, redirect","Error Get Page with download link / Ошибка олучения страницы с ссылками!");
            $tmp = cut_str($page,"action='", "'");
            $url = parse_url($tmp);
            $page = geturl($url["host"],defport($url), $url["path"].($url["query"] ? "?".$url["query"] : ""), "http://momupload.com/upload_result.php", 0, $post, 0, ""); 
                        
            $tmp = cut_str($page,'http://momupload.com/files/','"');
            if (empty($tmp))html_error("Error Get Download Link / Ошибка получения ссылки на скачку!");
            $download_link = "http://momupload.com/files/".$tmp;
            $tmp= cut_str($page,'remfile.php',' ');
            $delete_link = "http://".$url["host"]."/remfile.php".$tmp;
            # Upload plug'in написан Директором Зоопарка (ru-board member kamyshew). Only for Rapidget Pro!!! Rapidkill - отстой!!! Нет плагиату!!!

// Edited by sert 17.08.2008
?>