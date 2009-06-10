<table width=600 align=center>
</td></tr>
<tr><td align=center>
<div id=info width=100% align=center>Retrive upload ID</div>
<?php            
            $page = geturl("upload.com.ua", 80, "/", "", 0, 0, 0, "");
            is_page($page);
            $cookie = GetCookies($page);
            $action_url = cut_str($page,'form-data" action="','"');
            if (empty($action_url)) html_error("Error retrive action url!");
            $url = parse_url($action_url);
            $post['UPLOAD_IDENTIFIER'] = cut_str($page,'DENTIFIER" type="hidden" value="','"');
            $post['description'] = $descript;
            $post['agreed'] = 1;
            $post['ispublic'] = 0;  // файл в паблик, если приват - закоментировать
?>
<script>document.getElementById('info').style.display='none';</script>
    
<table width=600 align=center>
</td></tr>
<tr><td align=center>
<?php         
        
        $upfiles=upfile($url['host'],defport($url),$url['path']."?".$url["query"], "http://upload.com.ua", $cookie, $post, $lfile, $lname, "file2upload0");

?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?php
		$download_link = trim(cut_str($upfiles,"ocation:","\n"));
		if (empty($download_link)){
			print_r($upfiles);
			html_error("Error retrive download link!");
		}
?>