<table width=600 align=center>
</td></tr>
<tr><td align=center>
	
<div id=info width=100% align=center>Retrive upload ID</div>
<?php
		$ref='http://www.multiupload.com/';
		$page = geturl("ww.multiupload.com", 80, "/", "", 0, 0, 0, "");
		$cookies= "u=".cut_str($page,'name="u" value="','"');
		$UID = cut_str($page,'UPLOAD_IDENTIFIER" value="','"');
			
?>
<script>document.getElementById('info').style.display='none';</script>
<table width=600 align=center>
</td></tr>
<tr><td align=center>
<?php

		$post["description_0"]="";				
		$post["description_1"]="";
		$post["description_2"]="";				
		$post["description_3"]="";
		$post["description_4"]="";				
		$post["description_5"]="";
		$post["description_6"]="";				
		$post["description_7"]="";
		$post["description_8"]="";				
		$post["description_9"]="";
		$post["fetchfield0"]="http://";			$post["fetchdesc0"]="";
		$post["fetchfield1"]="http://";			$post["fetchdesc1"]="";
		$post["fetchfield2"]="http://";			$post["fetchdesc2"]="";
		$post["fetchfield3"]="http://";			$post["fetchdesc3"]="";
		$post["fetchfield4"]="http://";			$post["fetchdesc5"]="";
		$post["fetchfield6"]="http://";			$post["fetchdesc7"]="";
		$post["fetchfield8"]="http://";			$post["fetchdesc9"]="";
		
		$post["service_1"]="1";					
		$post["service_2"]="1";
		$post["service_3"]="1";					
		$post["service_4"]="1";
		$post["service_5"]="1";					
		$post["service_6"]="1";
		$post["service_7"]="1";					
		$post["service_8"]="1";
		$post["service_9"]="1";					
		$post["service_11"]="1";
		
		$post["fromemail"]="";	
		$post["toemail"]="";
		
		$post["rsaccount"]="";
		$post["password_5"]="";	
		$post["username_5"]="";	
		$post["remember_5"]="1";
		
		$post["rsaccount"]="";
		$post["password_1"]="";	
		$post["username_1"]="";	
		$post["remember_1"]="1";
		
		$upfrm = cut_str($page,'multipart/form-data" action="','"');
		$refup = cut_str($page,'multipart/form-data" action="','upload/?');
		$url = parse_url($upfrm);
		$upfiles = upfile($url["host"],$url["port"] ? $url["port"] : 80, $url["path"].($url["query"] ? "?".$url["query"] : ""), $refup, $cookies, $post, $lfile, $lname, "file_0");
?>
<script>document.getElementById('progressblock').style.display='none';</script>
<div id=final width=100% align=center>Get final code</div>
<?php		
			is_page($upfiles);
			$ddl=cut_str($upfiles,'downloadid":"','"');
			
			$download_link='http://www.multiupload.com/'.$ddl;
			
// Made by Baking 28/08/2009 12:16
?>