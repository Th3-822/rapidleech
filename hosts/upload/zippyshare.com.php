<table width=600 align=center>
</td></tr>
<tr><td align=center>
	
<div id=info width=100% align=center>Retrive upload ID</div>
<?

$page = geturl("www.zippyshare.com", 80, "/", 0, 0, 0, 0, "");

if ($new_loc = trim(cut_str($page, "Location: ", "\n")))
{
	$Url = parse_url($new_loc);
	if (!$Url["path"]) $Url["path"] = "/";
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, 0, 0, 0, $_GET["proxy"],$pauth);
}
else{
$cookie=GetCookies($page);
}

?>

<?

$upladid=cut_str($page,"type=\"hidden\" name=\"uploadId\" value=\"","\"/>");
$nsv=cut_str($page,"enctype=\"multipart/form-data\" action=\"http://www",".zippyshare.com");

echo "<br>uploadId: {$uploadid} & nsv: {$nsv} <br>";

$url =parse_url("http://www".$nsv.".zippyshare.com/upload?uploadId=".$upladid);

$post["uploadId"] = $upladid;
$post["terms"] = "checkbox";
$post["x"] = rand(1,140);
$post["y"] = rand(1,20);

$upfiles = upfile($url["host"], defport($url), $url["path"].($url["query"] ? "?".$url["query"] : ""), "http://www.zippyshare.com/", $cookie, $post, $lfile, $lname, "file_0");

?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?

is_page($upfiles);

$download_link = cut_str($upfiles, 'onclick="this.select();" value="', '"');

if (!$download_link)
{
html_error("Error getting download link",0);
}

/*************************\  
written by kaox 12-sep-2009
fix by zbig992 @ 15 July 2010
fix by rapidleaks.com @ 1 DEC 2010
\*************************/

?>
