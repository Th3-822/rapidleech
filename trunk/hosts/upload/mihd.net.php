<table width=600 align=center>
</td></tr>
<tr><td align=center>
	
<div id=info width=100% align=center>Retrive upload ID</div>
<?php
			$page = geturl("mihd.net", 80, "/", "", 0, 0, 0, "");
			is_page($page);
            $cook = trim(cut_str($page,"Cookie:",";"));
			$page = geturl("mihd.net", 80, "/upload:index_js", "http://mihd.net/", $cook, 0, 0, "");


			$post['Filename']=$lname;
            $post['Upload']= "Submit+Query";
            $tmp = cut_str($page,'var __upload_url',';');
			$action_url = cut_str($tmp,'"','"');
            $tmp = cut_str($page,'var __success_url',';');
			$result_url = cut_str($tmp,'"','"');
			if (!$action_url || !$result_url) html_error("Error retrive upload id".$page);

			$url=parse_url($action_url);

?>
	<script>document.getElementById('info').style.display='none';</script>
<?php

			$upfiles=upfile($url["host"],defport($url), $url["path"].($url["query"] ? "?".$url["query"] : ""),"", 0, $post, $lfile, $lname, "Filedata");

?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?php		
			is_page($upfiles);
            is_notpresent($upfiles,"ok","Error Upload File!".$upfiles);
//            $location = cut_str($upfiles,"Location: ","\n");
            $url=parse_url($result_url);
            $page = geturl($url["host"],defport($url),$url['path'].($url["query"] ? "?".$url["query"] : ""),"",0,0,0,0,0);
            is_page($page);
//            is_notpresent($page,"http://mihd.net/","Error Upload file");
            
//            $page = geturl("mihd.net", 80, "/","http://mihd.net/",$cook,0,0,0,0);
//            is_page($page); 
            
            $tmp = cut_str($page,"file_link",">");
            $download_link = cut_str($tmp,'="','"');
            $tmp = cut_str($page,"delete_link",">");
            $delete_link = cut_str($tmp,'value="','"'); 
// Edited by sert 18.06.2008
?>