<?php
if (!defined('RAPIDLEECH'))
{
	require_once("index.html");
	exit;
}

if (preg_match('%^/ru/files/%', $Url["path"]) != 0)
{
	$Url["path"] = preg_replace('%^/ru/files/%', '/en/files/', $Url["path"]);
} elseif (preg_match('%^/de/files/%', $Url["path"]) != 0) {
	$Url["path"] = preg_replace('%^/de/files/%', '/en/files/', $Url["path"]);
} elseif (preg_match('%^/es/files/%', $Url["path"]) != 0) {
	$Url["path"] = preg_replace('%^/es/files/%', '/en/files/', $Url["path"]);
} elseif (preg_match('%^/files/%', $Url["path"]) != 0) {
	$Url["path"] = preg_replace('%^/files/%', '/en/files/', $Url["path"]);
}
$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, 0, 0, 0, $_GET["proxy"],$pauth);
//file_put_contents("depositfiles_1.txt", $page);
is_page($page);
is_present($page, "Such file does not exist or it has been removed for infringement of copyrights.");
is_present($page, "Your IP is already downloading a file from our system.");

if (stristr($page,'You used up your limit for file downloading!')) {
	preg_match('/([0-9]+) minute\(s\)/',$page,$minutes);
	html_error("Download limit exceeded. Try again in ".trim($minutes[1])." minute(s)", 0);
}

if (preg_match('/<input type="submit" class="button" value="FREE downloading"/', $page))
{
	$post = Array();
	$post["gateway_result"] = 1;
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $LINK, 0, $post, 0, $_GET["proxy"],$pauth);
	//file_put_contents("depositfiles_2.txt", $page);
	is_page($page);
}

preg_match('/<span id="download_waiter_remain">(.*)<\/span>/',$page,$countDown);
$countDown = (int) $countDown[1];

insert_timer($countDown, "The file is being prepared.","",true);

if (preg_match('/<form action="(.*)" method="get" onSubmit="download_started()/U',$page,$dlink)) {
	$Url = parse_url(trim($dlink[1]));
	$FileName = basename($Url["path"]);
} else {
	html_error("Error getting download link", 0);
}

insert_location("$PHP_SELF?filename=".urlencode($FileName)."&host=".$Url["host"]."&path=".urlencode($Url["path"].($Url["query"] ? "?".$Url["query"] : ""))."&referer=".urlencode($LINK)."&email=".($_GET["domail"] ? $_GET["email"] : "")."&partSize=".($_GET["split"] ? $_GET["partSize"] : "")."&method=".$_GET["method"]."&proxy=".($_GET["useproxy"] ? $_GET["proxy"] : "")."&saveto=".$_GET["path"]."&link=".urlencode($LINK).($_GET["add_comment"] == "on" ? "&comment=".urlencode($_GET["comment"]) : "").($pauth ? "&pauth=$pauth" : "").(isset($_GET["audl"]) ? "&audl=doum" : ""));
?>