<?php
if (!defined('RAPIDLEECH'))
{
	require_once("index.html");
	exit;
}

$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, 0, 0, 0, $_GET["proxy"],$pauth);
//file_put_contents("minifilehost_1.txt", $page);
is_page($page);
is_present($page, "You are not allowed to download files.");
is_present($page, "Invalid download link.");
is_present($page, "Please Enter The Correct Password To Acess The Download", "The file is password protected");
is_present($page, "You're trying to download again too soon!", "Download limit reached"); // 1.1
is_present($page, "Download Time Limit", "Download limit reached"); // 1.5

preg_match('%http:.*download2.php\?a=.*&b=[[:alnum:]]{32}%i', $page, $redir);
$Url = parse_url($redir[0]);
$FileName = "none";

insert_location("$PHP_SELF?filename=".urlencode($FileName)."&host=".$Url["host"]."&path=".urlencode($Url["path"].($Url["query"] ? "?".$Url["query"] : ""))."&referer=".urlencode($Referer)."&email=".($_GET["domail"] ? $_GET["email"] : "")."&partSize=".($_GET["split"] ? $_GET["partSize"] : "")."&method=".$_GET["method"]."&proxy=".($_GET["useproxy"] ? $_GET["proxy"] : "")."&saveto=".$_GET["path"]."&link=".urlencode($LINK).($_GET["add_comment"] == "on" ? "&comment=".urlencode($_GET["comment"]) : "")."&auth=".$auth.($pauth ? "&pauth=$pauth" : "").(isset($_GET["audl"]) ? "&audl=doum" : ""));
?>