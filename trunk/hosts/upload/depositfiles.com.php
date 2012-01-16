<table width=600 align=center> 
</td></tr> 
<tr><td align=center> 
<div id=login width=100% align=center></div> 
<?php
			$url=parse_url('http://depositfiles.com/');
			$page = geturl($url["host"], $url["port"] ? $url["port"] : 80, $url["path"] . ($url["query"] ? "?" . $url["query"] : ""), "http://depositfiles.com/", 0, 0, 0, $_GET["proxy"], $pauth);
			$cookies = GetCookies($page);
?>
<script>document.getElementById('info').style.display='none';</script>
<div id=info width=100% align=center>Retrive upload ID</div> 
<?php
			$url=parse_url('http://depositfiles.com/');
			$page = geturl($url["host"], $url["port"] ? $url["port"] : 80, $url["path"] . ($url["query"] ? "?" . $url["query"] : ""), "http://depositfiles.com/", $cookies, 0, 0, $_GET["proxy"], $pauth);
			preg_match('#enctype="multipart/form-data"[\r|\n|\s]+action="([^"]+)"#', $page, $act);
			$url = parse_url($act[1]);
			preg_match('#name="MAX_FILE_SIZE"[\r|\n|\s]+value="([^"]+)"#', $page, $mx);
			preg_match('#name="UPLOAD_IDENTIFIER"[\r|\n|\s]+value="([^"]+)"#', $page, $id);
			$post["MAX_FILE_SIZE"]=$mx[1];
			$post["UPLOAD_IDENTIFIER"]=$id[1];
			$post["go"]=1;
			$post["agree"]=1;
			$upfiles = upfile($url["host"], $url["port"] ? $url["port"] : 80, $url["path"] . ($url["query"] ? "?" . $url["query"] : ""), "http://depositfiles.com/", $cookies, $post, $lfile, $lname, "files");
?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?php
			preg_match("#parent.ud_download_url[\r|\n|\s]+=[\r|\n|\s]+'([^']+)'#", $upfiles, $link);
			preg_match("#parent.ud_delete_url[\r|\n|\s]+=[\r|\n|\s]+'([^']+)'#", $upfiles, $dele);
			if (!empty($link[1]))
			$download_link = $link[1];
			else
			html_error ("Didn't find download link!");
			if (!empty($dele[1]))
			$delete_link= $dele[1];
			else
			html_error ("Didn't find delete link!");
/**
written by simplesdescarga 13/01/2012
**/   
?>