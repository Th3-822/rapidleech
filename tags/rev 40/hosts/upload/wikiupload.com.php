<table width=600 align=center>
</td></tr>
<tr><td align=center>
<div id=info width=100% align=center>Retrive upload ID</div>
<?php
				$ref="http://www.wikiupload.com/";
				$page = geturl("www.wikiupload.com", 80, "/", "", 0, 0, 0, "");
				is_page($page);
				$page=cut_str($page,'<form','</form>');
				$action_url=str_replace("./","/","http://www.wikiupload.com".cut_str($page,'action="','"'));
				$post['sessionid']=cut_str($action_url,'sid=','"');
				if (!$post['sessionid']) html_error ('Error get session id');
				$post['returnPath']='index.php';
				$post['action']='upload_file';
				$post['description']='';
				$post['tags']='';
				$post['agree']='on';
				$post['x']='96';
				$post['y']='11';
	
				$url=parse_url($action_url);
		
				$upfiles=upfile($url["host"],$url["port"] ? $url["port"] : 80, $url["path"].($url["query"] ? "?".$url["query"] : "") ,$ref, 0, $post, $lfile, $lname, "userfile");

?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?php		
				is_notpresent($upfiles,"Location","File not uploaded");
				$newlink=trim(cut_str($upfiles,"Location:","\n"));
				$Url=parse_url($newlink);
				$page = geturl($Url["host"], defport($Url), $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $ref, 0, 0, 0, $_GET["proxy"],$pauth);
				is_page($page);
				$location=$ref."middle_page.php?id=".cut_str($page,'middle_page.php?id=',"'");
				if (!$location) html_error ('Error get location');
				$Url=parse_url($location);
				$page = geturl($Url["host"], defport($Url), $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $newlink, 0, 0, 0, $_GET["proxy"],$pauth);
				is_page($page);
				$download_link=$ref."download_page.php?id=".cut_str($page,"download_page.php?id=","'");

// sert 27.06.2008
?>