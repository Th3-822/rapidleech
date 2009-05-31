<table width=600 align=center>
</td></tr>
<tr><td align=center>
	
<div id=info width=100% align=center>Retrive upload ID</div>
<?php
            $action_url = "http://jamber.info/testing_upload/ubr_link_upload.php?rnd_id=".mt_rand();
            $url = parse_url($action_url); 
			$page = geturl($url["host"],defport($url), $url["path"].($url["query"] ? "?".$url["query"] : ""), "http://ifile.it/", 0, 0, 0, $_REQUEST['proxy']);
			is_page($page);
            
            $sid = cut_str($page,'startUpload("','")');
            $cook = GetCookies($page);

            $post['category']= 4;
            $post['access_lvl']= 1;
            $post['correct']= "";
            $post['discription']= $descript;
            $post['tags'] = "";
            $post['user_id'] = 0;
            $action_url = "http://jamber.info/testing_upload/ubr_ini_progress.php?upload_id=".$sid;
            $url = parse_url($action_url); 
            $page = geturl($url["host"],defport($url), $url["path"].($url["query"] ? "?".$url["query"] : ""), "http://ifile.it/", $cook, 0, 0, $_REQUEST['proxy']);
            is_page($page);   
            
            $action_url = "http://jamber.info/cgi-bin/ubr_upload.pl?upload_id=".$sid;
			//if (!$action_url) html_error("Error retrive upload id".$page);

			$url=parse_url($action_url);

?>
	<script>document.getElementById('info').style.display='none';</script>
<?php
			$upfiles=upfile($url["host"],defport($url), $url["path"].($url["query"] ? "?".$url["query"] : ""),$action_url, $cook, $post, $lfile, $lname, "upfile_0",$_REQUEST['proxy']);

?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?php		
			is_page($upfiles);
         
            
            
            
            $action_url = "http://jamber.info/post_upload.php?upload_id=".$sid;
            $url = parse_url($action_url);

            $page = geturl($url["host"],defport($url), $url["path"].($url["query"] ? "?".$url["query"] : ""), "http://ifile.it/", $cook, 0, 0, $_REQUEST['proxy']);           
            is_page($page);

            
            $download_link = "http://jamber.info/files/download/".cut_str($page,'/files/download/','">');
?>
    