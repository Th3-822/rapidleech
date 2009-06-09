<table width=600 align=center>
</td></tr>
<tr><td align=center>
<div id=info width=100% align=center>Retrive upload ID</div>
<?php 
			$page = geturl("www.2shared.com", 80, "/", "", 0, 0, 0, "");
			is_page($page);
			preg_match('/action="(.*?)"/i', $page, $up_url);
			$action_url = $up_url[1];
			$url = parse_url($action_url);
			$upload_id = $url["query"];
			$post['mainDC'] = 1;
?>
<script>document.getElementById('info').style.display='none';</script>

<table width=600 align=center>
</td></tr>
<tr><td align=center>
<?php 
            $upfiles = upfile($url["host"],$url["port"] ? $url["port"] : 80, $url["path"].($url["query"] ? "?".$url["query"] : ""),'http://www.2shared.com/', 0, $post, $lfile, $lname, "fff");
			is_page($upfiles);
?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?php 
			if(preg_match('/Your upload has successfully completed/', $upfiles)){
				$comp_url = 'http://www.2shared.com/uploadComplete.jsp?'.$upload_id;
				$location = parse_url($comp_url);
			}
			
			$page = geturl($location['host'], 80, $location['path']."?".$location["query"], "http://www.2shared.com/", 0, 0, 0, "");
			is_page($page);
			preg_match('/action="(.*?)".*downloadForm/i', $page, $flink);
			preg_match('/action="(.*?)".*adminForm/i', $page, $adlink);
			$download_link = $flink[1];
			$adm_link = $adlink[1];

?>