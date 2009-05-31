<table width=600 align=center>
</td></tr>
<tr><td align=center>
<div id=info width=100% align=center>Retrive upload ID</div>
<?php
			$page = geturl("zshare.net", 80, "/", "", 0, 0, 0, "");
			is_page($page);
			$action_url = cut_str($page,'action="http://','"');
			if (empty($action_url)) html_error("Error retrive action url!");
			$url = parse_url("http://".$action_url);
			$post['TOS'] = 1;
?>
<script>document.getElementById('info').style.display='none';</script>

<table width=600 align=center>
</td></tr>
<tr><td align=center>
<?php
            $upfiles=upfile($url['host'],$url['port'],$url['path']."?".$url["query"],"http://zshare.net", 0, $post, $lfile, $lname, "file");
			is_page($upfiles);
			is_notpresent($upfiles,'www.zshare.net','Error upload file!');
?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?php
			$location = parse_url(trim(cut_str($upfiles,"location: ","\n")));
			$page = geturl($location['host'], 80, $location['path']."?".$location["query"], "http://zshare.net", 0, 0, 0, "");
			is_page($page);
			is_notpresent($page,'Download Link','Error Get Download Link!!!');
			$tmp = cut_str($page,"Download Link","Link for forums:");
			$download_link = "http://www.zshare.net".cut_str($tmp,"http://www.zshare.net",'"');

?>