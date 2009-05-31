<table width=600 align=center>
</td></tr>
<tr><td align=center>
<div id=info width=100% align=center>Retrive upload ID</div>

<script>document.getElementById('info').style.display='none';</script>
<table width=600 align=center>
</td></tr>
<tr><td align=center>
<?php
# Передаю привет всем кого знаю)
# t3ma, давай еще ченить пробуй сделать)
# Сейчас смотрю: Nana Episode 05
	$page = geturl("filestock.ru", 80, "/");
	$cookie = GetCookies($page);
	$action = cut_str($page, "action='", "'");
	if (!$action) html_error("Can't found upload id. Some changes occured on this site.");
	$url = parse_url ('http://www.filestock.ru' .$action);
	$post['session']=cut_str($page,'session" value="','"');
	$post['cmd']='upload';
	//echo $url;
	$upfiles = upfile ($url["host"],$url["port"] ? $url["port"] : 80, $url["path"].($url["query"] ? "?".$url["query"] : ""), 'http://www.filestock.ru/', $cookie, $post, $lfile, $lname, "file");

	echo "<script>document.getElementById('progressblock').style.display='none';</script><div id=final width=100% align=center>Get Final Code</div>";
	$downlink = cut_str ($upfiles, "size='80' value='", "'");
	if (!$downlink) html_error("Download Link Error");
    $download_link = $downlink;
	echo "<script>document.getElementById('final').style.display='none';</script>";

	// t3ma (11.08.2008)
	// Done by ValdikSS
?>