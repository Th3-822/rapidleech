<?php
if (!defined('RAPIDLEECH'))
{
	require_once("index.html");
	exit;
}

$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, 0, 0, 0, $_GET["proxy"],$pauth);
//file_put_contents("sharedzilla_1.txt", $page);
is_page($page);
is_present($page, "            Upload not found", "File not found");
is_present($page, '<input type="password" name="upload_password" id="upload_password" value="">', "The file is password protected");
is_present($page, "File has expired.");

if(preg_match('/<form action="(.+?)" name="download" method="POST"/', $page, $nextPageArray))
{
	$Referer = $LINK;
	$nextPage = $nextPageArray[1];
}
else
{
	html_error("Could not find download form.", 0);
}
preg_match('/<input type="hidden" name="id" value="(.+?)">/', $page, $idArray);

$post = Array();
$post["id"] = $idArray[1];

$Url = parse_url($nextPage);
$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $Referer, 0, $post, 0, $_GET["proxy"],$pauth);
//file_put_contents("sharedzilla_2.txt", $page);
is_page($page);

preg_match('/Location: (.*)/i', $page, $nextPageArray);
$nextPage = trim($nextPageArray[1]);
$Url = parse_url($nextPage);
$FileName = basename($Url["path"]);

insert_location("$PHP_SELF?filename=".urlencode($FileName)."&host=".$Url["host"]."&port=".$Url["port"]."&path=".urlencode($Url["path"].($Url["query"] ? "?".$Url["query"] : ""))."&referer=".urlencode($Referer)."&email=".($_GET["domail"] ? $_GET["email"] : "")."&partSize=".($_GET["split"] ? $_GET["partSize"] : "")."&method=".$_GET["method"]."&proxy=".($_GET["useproxy"] ? $_GET["proxy"] : "")."&saveto=".$_GET["path"]."&link=".urlencode($LINK).($_GET["add_comment"] == "on" ? "&comment=".urlencode($_GET["comment"]) : "").($pauth ? "&pauth=$pauth" : "").(isset($_GET["audl"]) ? "&audl=doum" : ""));
?>