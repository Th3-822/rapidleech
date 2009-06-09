<table width=600 align=center>
</td></tr>
<tr><td align=center>

<div id=info width=100% align=center>Retrive upload ID</div>
<?php
			$ref='http://ifile.it/';
//			$page = geturl("ifile.it", 80, "/", "", 0, 0, 0, "");
//			is_page($page);
//            $cook = trim(cut_str($page,"Cookie:",";"));
			$page = geturl("ifile.it", 80, "/upload:web_js", $ref, 0, 0, 0, "");

			preg_match('/<input.*name="uuid".+value="(.*)">/',$page,$uuid);
			$post['uuid'] = $uuid[1];
			$post['Filename']=$lname;
            $post['uploadBtn']= "Upload selected file(s)";
            $tmp = cut_str($page,'var __upload_url',';');
			$action_url = cut_str($tmp,'"','"');
            $tmp = cut_str($page,'var __success_url',';');
			$result_url = cut_str($tmp,'"','"');
			if (!$action_url || !$result_url) html_error("Error retrive upload id".$page);

			$url=parse_url($action_url);

?>
	<script>document.getElementById('info').style.display='none';</script>
<?php

			$upfiles=upfile($url["host"],defport($url), $url["path"].($url["query"] ? "?".$url["query"] : ""),"", 0, $post, $lfile, $lname, "uploadFileInput_1");

?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?php
			is_page($upfiles);
            is_notpresent($upfiles,"ok","Error Upload File!".$upfiles);
            $url=parse_url($result_url);
            $page = geturl($url["host"],defport($url),$url['path'].($url["query"] ? "?".$url["query"] : ""),"",0,0,0,0,0);
            is_page($page);

            $tmp = cut_str($page,"file_link",">");
            $download_link = cut_str($tmp,'="','"');
            $tmp = cut_str($page,"delete_link",">");
            $delete_link = cut_str($tmp,'value="','"');
			if (strpos($page,'Duplicate Alert! file already exists on the system')) echo '<br>Duplicate Alert! file already exists on the system<br>';

// sert 19.07.2008
?>