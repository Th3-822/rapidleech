<table width=600 align=center>
</td></tr>
<tr><td align=center>
	
<div id=info width=100% align=center>Retrive upload ID</div>
<?php
			$ref='http://bitroad.net/index.php';
			$page = geturl("bitroad.net", 80, "/index.php", 0, 0, 0, 0, "");
			is_page($page);
?>
<script>document.getElementById('info').style.display='none';</script>
<table width=600 align=center>
</td></tr>
<tr><td align=center>
<?php

			$uid = rand(1000,9999).'0'.rand(1000,9999);
			$post["owner"]='';
			$post["self_desc"]='';//$descript
			$post["upload_a"]='Upload';
			$post["accept"]='on';
			$post["MAX_FILE_SIZE"]=cut_str($page,'"MAX_FILE_SIZE" value="','"');
			$post["tmpl_name"]='';
			$post["css_name"]='';
			$post["sessionid"]=$uid;
			$post["uid"]='123';
			$up_url=cut_str($page,'multipart/form-data" action="','"');
			if (!$up_url) html_error ('Error get Upload Url');
			$ref1=$up_url.$uid;
			$url=parse_url($ref1);

			$upfiles=upfile($url["host"],$url["port"] ? $url["port"] : 80, $url["path"].($url["query"] ? "?".$url["query"] : ""),$ref, 0, $post, $lfile, $lname, "myfile");
?>
<script>document.getElementById('progressblock').style.display='none';</script>
<div id=final width=100% align=center>Get final code</div>
<?php		
			is_page($upfiles);
			is_notpresent($upfiles,"myfile_status'>OK<","Error upload file".$upfiles);
			unset($post);
			$post["myfile"]=cut_str($upfiles,"name='myfile'>","</");
			$post["myfile_status"]=cut_str($upfiles,"<textarea name='myfile_status'>","</textarea>");
			$post["owner"]='';
			$post["self_desc"]=cut_str($upfiles,"name='self_desc'>","</");;
			$post["upload_a"]=cut_str($upfiles,"<textarea name='upload_a'>","</textarea>");
			$post["accept"]=cut_str($upfiles,"name='accept'>","</");
			$post["MAX_FILE_SIZE"]=cut_str($upfiles,"<textarea name='MAX_FILE_SIZE'>","</textarea>");
			$post["tmpl_name"]=cut_str($upfiles,"name='tmpl_name'>","</");
			$post["css_name"]="";
			$post["sessionid"]=cut_str($upfiles,"<textarea name='sessionid'>","</textarea>");
			$post["uid"]=cut_str($upfiles,"<textarea name='uid'>","</textarea>");
			$post["target_dir"]=trim(cut_str($upfiles,"<textarea name='target_dir'>","</textarea>"));			
			
			$final_url=trim(cut_str($upfiles,"action='","'"));
			$Url=parse_url($final_url);
			$page = geturl($Url["host"], defport($Url), $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $ref1, 0, $post, 0, $_GET["proxy"],$pauth);
			is_page($page);

			$locat=trim(cut_str($page,'Location:',"\n"));
			if (!$locat) html_error ('Error get location'.$page);
			$Url=parse_url($locat);
			$page = geturl($Url["host"], defport($Url), $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $ref1, 0, 0, 0, $_GET["proxy"],$pauth);
			is_page($page);
			$tmp=cut_str($page,'http://bitroad.net/download/',"'");
			if (!$tmp) html_error ('Error get download link'.$page);
			$download_link='http://bitroad.net/download/'.$tmp;
			$tmp=cut_str($page,'http://bitroad.net/download/delete','"');
			if ($tmp) $delete_link='http://bitroad.net/download/delete'.$tmp;
?>
<script>document.getElementById('final').style.display='none';</script>
<?php

// sert 17.08.2008
?>