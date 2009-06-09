<table width=600 align=center>
</td></tr>
<tr><td align=center>
<div id=info width=100% align=center>Retrive upload ID</div>
<?php 


			$page = geturl("zshare.net", 80, "/", "", 0, 0, 0, "");
			is_page($page);
			$srv=cut_str($page,'action="http://','"');
			if (empty($srv)) html_error("Error retrive action url!");
			$rnd=time().rand(10000,99999);
			$rlink=$srv."uberupload/ubr_link_upload.php?rnd_id=".$rnd;
            $Url = parse_url($rlink);
			$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), "http://zshare.net/", 0, 0, 0, $_GET["proxy"],$pauth);
			$upid=cut_str($page,'startUpload("','"');
			$action_url = $srv."cgi-bin/ubr_upload.pl?upload_id=".$upid."&multiple=0&descr=" ;
			$url = parse_url($action_url);
			$post['TOS'] = 1;
?>
<script>document.getElementById('info').style.display='none';</script>

<table width=600 align=center>
</td></tr>
<tr><td align=center>
<?php 
            $upfiles=upfile($url['host'],$url['port'],$url['path']."?".$url["query"],"http://zshare.net", 0, $post, $lfile, $lname, "file");
			is_page($upfiles);
			is_notpresent($upfiles,'f_id=','Error upload file!');
?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?php 
			$Url = parse_url($srv.trim(cut_str($upfiles,"Location: ../","\n")));
            $page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), "http://zshare.net/", 0, 0, 0, $_GET["proxy"],$pauth);  
			is_page($page);
			$dwn=trim(cut_str($page,"Location: ","\n"));
			
			$Url=parse_url($dwn);
            $page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), "http://zshare.net/", 0, 0, 0, $_GET["proxy"],$pauth);  		
			is_notpresent($page,'Download Link','Error Get Download Link!!!');
			$download_link = cut_str($page,"[URL=","]");
		
// written by kaox 31/05/09
?>