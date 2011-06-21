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
		$post["fetchfield1"]="http://";			$post["fetchdesc1"]="";
		$post["fetchfield2"]="http://";			$post["fetchdesc2"]="";
		$post["fetchfield3"]="http://";			$post["fetchdesc3"]="";
		$post["fetchfield4"]="http://";			$post["fetchdesc4"]="";
		$post["fetchfield5"]="http://";			$post["fetchdesc5"]="";
		$post["fetchfield6"]="http://";			$post["fetchdesc6"]="";
		$post["fetchfield7"]="http://";			$post["fetchdesc7"]="";
		$post["fetchfield8"]="http://";			$post["fetchdesc8"]="";
		$post["fetchfield9"]="http://";			$post["fetchdesc9"]="";

		$post["service_0"]="1";		
		$post["service_1"]="1";					
		$post["service_5"]="1";					
		$post["service_6"]="1";
		$post["service_7"]="1";					
		$post["service_9"]="1";					
		$post["service_10"]="1";
		$post["service_14"]="1";
		$post["service_15"]="1";
		
		$post["fromemail"]="";	
		$post["toemail"]="";
		
		$post["details_RS"]="";     //Rapidshare Account
		$post["username_5"]="";	  
		$post["password_5"]="";	  
		$post["remember_5"]="1";

		$post["details_MU"]=""; //Megaupload Account
		$post["username_1"]="";	
		$post["password_1"]="";	
		$post["remember_1"]="1";

		$post["details_DF"]=""; //Depositfiles Account
		$post["username_7"]="";	
		$post["password_7"]="";	
		$post["remember_7"]="1";

		$post["details_HF"]=""; //Hotfile Account
		$post["username_9"]="";	
		$post["password_9"]="";	
		$post["remember_9"]="1";

		$post["details_ZS"]=""; //Zshare Account
		$post["username_6"]="";	
		$post["password_6"]="";	
		$post["remember_6"]="1";

		$post["details_UP"]=""; //Uploading Account
		$post["username_10"]="";	
		$post["password_10"]="";	
		$post["remember_10"]="1";
		
		$post["details_FC"]=""; //Filesonic Account
		$post["username_15"]="";	
		$post["password_15"]="";	
		$post["remember_15"]="1";

		$post["details_FS"]=""; //Fileserve Account
		$post["username_14"]="";	
		$post["password_14"]="";	
		$post["remember_14"]="1";

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
// Updated by anniyan07 18/06/2011 20:40
?>
