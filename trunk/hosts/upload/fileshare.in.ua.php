<table width=600 align=center>
</td></tr>
<tr><td align=center>
	
<div id=info width=100% align=center>Retrive upload ID</div>
<?
			$ref='http://fileshare.in.ua/';
?>
<script>document.getElementById('info').style.display='none';</script>
<table width=600 align=center>
</td></tr>
<tr><td align=center>
<?

			$Url=parse_url($ref);
			$page = geturl($Url["host"], defport($Url), $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, 0, 0, 0, $_GET["proxy"],$pauth);
			is_page($page);
			$cookies=GetCookies($page);
			$uid=cut_str($page,'"UPLOAD_IDENTIFIER" value="','"');
//			$tmp=cut_str($page,'multipart/form-data"','</form>');
			$upurl='http://ul.fileshare.in.ua/';//cut_str($tmp,'action="','"');
			if (!$uid) html_error ('Error get upload id');

			$post["UPLOAD_IDENTIFIER"]=$uid;
			$post["MAX_FILE_SIZE"]='2097152000';
			$post["description"]=$descript;
			$post["tag"]='';
//			$post["private"]='on';
			$post["terms"]='on';

			$url=parse_url($upurl);

			$upfiles=upfile($url["host"],$url["port"] ? $url["port"] : 80, $url["path"].($url["query"] ? "?".$url["query"] : ""), $ref, $cookies, $post, $lfile, $lname, "my_file");
?>
<script>document.getElementById('progressblock').style.display='none';</script>
<div id=final width=100% align=center>Get final code</div>
<?		
			is_page($upfiles);
			$finish_url=trim(cut_str($upfiles,'location:',"\n"));
			if (!$finish_url) html_error ('Error get location 1');
			$Url=parse_url($finish_url);
			$page = geturl($Url["host"], defport($Url), $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $ref, $cookies, 0, 0, $_GET["proxy"],$pauth);
			is_page($page);
			$finish_url=trim(cut_str($page,'Location:',"\n"));
			if (!$finish_url) html_error ('Error get location 2');

			$download_link=$finish_url;
?>
<script>document.getElementById('final').style.display='none';</script>
<?

// sert 03.10.2008
?>