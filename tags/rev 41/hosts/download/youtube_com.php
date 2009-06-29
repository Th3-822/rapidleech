<?php
if (!defined('RAPIDLEECH'))
{
	require_once("index.html");
	exit;
}

$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, 0, 0, 0, $_GET["proxy"], $pauth);
is_page($page);

if (preg_match("/\r\nLocation: [^\r\n]+/", $page))
{
	html_error("File not found", 0);
}
preg_match('/"t": "([^\"]+)/', $page, $video_t);
preg_match('/"video_id": "([^\"]+)/', $page, $video_id);
if (preg_match("/<title>YouTube - ([^<]+)/", $page, $title))
{
	$filename = str_replace(Array("\\", "/", ":", "*", "?", "\"", "<", ">", "|"), "_", html_entity_decode($title[1]))."_".(isset($_POST["ytube_mp4"]) ? "HQ.mp4" : "LQ.flv");
}
if (preg_match_all("/Set-Cookie: ([^\r\n;]+)/i", $page, $cookies))
{
	$cookie = implode("; ", $cookies[1]);
}
preg_match('%var swfUrl = canPlayV9Swf\(\) \? "(.+)\.swf" :%U', $page, $refmatch);
$Url = parse_url("http://www.youtube.com/get_video?video_id=".$video_id[1]."&t=".$video_t[1].(isset($_POST["ytube_mp4"]) && isset($_POST['yt_fmt']) ? "&el=detailpage&ps=&fmt=$_POST[yt_fmt]" : ""));

insert_location("$PHP_SELF?filename=".urlencode($filename)."&force_name=".urlencode($filename)."&host=".$Url["host"]."&path=".urlencode($Url["path"].($Url["query"] ? "?".$Url["query"] : ""))."&referer=".urlencode($refmatch[1])."&email=".($_GET["domail"] ? $_GET["email"] : "")."&partSize=".($_GET["split"] ? $_GET["partSize"] : "")."&method=".$_GET["method"]."&cookie=".urlencode($cookie)."&proxy=".($_GET["useproxy"] ? $_GET["proxy"] : "")."&saveto=".$_GET["path"]."&link=".urlencode($LINK).($_GET["add_comment"] == "on" ? "&comment=".urlencode($_GET["comment"]) : "").($pauth ? "&pauth=$pauth" : "").(isset($_GET["audl"]) ? "&audl=doum" : ""));
?>