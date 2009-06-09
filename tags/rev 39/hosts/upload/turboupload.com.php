<table width=600 align=center>
</td></tr>
<tr><td align=center>

<div id=info width=100% align=center>Retrive upload ID</div>
<?
			$ref='http://www.turboupload.com/home';
			$page = geturl("www.turboupload.com", 80, "/home", "", 0, 0, 0, "");
			is_page($page);
?>
	<script>document.getElementById('info').style.display='none';</script>
<?


			$tmp=cut_str($page,'___serverUrl','}');
			$upserver=trim(cut_str($tmp,"return '","'"));
			$upserver=$upserver?$upserver:'http://s2.turboupload.com/';
			$uid=md5(microtime());
			$post["Filename"]=$lname;
			$post["Upload"]="Submit Query";

			$url=parse_url($upserver.'upload/process/'.$uid.'/_/_/0/?');
			$agent1=$agent;
			$agent='Shockwave Flash';
			$upfiles=upfile($url["host"],$url["port"] ? $url["port"] : 80, $url["path"].($url["query"] ? "?".$url["query"] : "") , $ref, 0, $post, $lfile, $lname, "Filedata");
?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?
			is_page($upfiles);
			$agent=$agent1;
			$cookies=GetCookies($upfiles);

			$Url=parse_url('http://www.turboupload.com//upload/getLinks/'.$uid);
			$page = geturl($Url["host"], defport($Url), $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $ref, $cookies, 0, 0, $_GET["proxy"],$pauth);
			is_page($page);
			$tmp=cut_str($page,"Download Link","/>");
			$d1=cut_str($tmp,'value="','"');
			if (!$d1) html_error ('Error upload file'.$page);
			$download_link=$d1;

			$tmp=cut_str($page,'"http://www.turboupload.com/files/delete/','"');
			if ($tmp) $delete_link='http://www.turboupload.com/files/delete/'.$tmp;

// sert 13.10.2008
?>