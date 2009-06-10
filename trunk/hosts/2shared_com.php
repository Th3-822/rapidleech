<?php
if (!defined('RAPIDLEECH'))
{
	require_once("index.html");
	exit;
}

$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"], $Referer, 0, 0, 0, $_GET["proxy"],$pauth);
is_page($page);

if (preg_match('/var c = ([0-9]+);/', $page, $count))
{
	$countDown = $count[1];
	insert_timer($countDown, "Waiting link timelock");
}

preg_match('/location = "(.*)";/', $page, $loc);
$Href = $loc[1];
$Url = parse_url($Href);
$FileName = !$FileName ? basename($Url["path"]) : $FileName;

preg_match('/Set-Cookie: hostid=(.*); Expires/', $page, $coo);
$cookie = "hostid=".$coo[1];
/*preg_match('/Set-Cookie: JSESSIONID=(.*); Path/', $page, $coo);
$cookie .= "; JSESSIONID=".$coo[1];*/

insert_location("$PHP_SELF?cookie=".urlencode($cookie)."&filename=".urlencode($FileName)."&host=".$Url["host"]."&path=".urlencode($Url["path"].($Url["query"] ? "?".$Url["query"] : ""))."&referer=".urlencode($Referer)."&email=".($_GET["domail"] ? $_GET["email"] : "")."&partSize=".($_GET["split"] ? $_GET["partSize"] : "")."&method=".$_GET["method"]."&proxy=".($_GET["useproxy"] ? $_GET["proxy"] : "")."&saveto=".$_GET["path"]."&link=".urlencode($LINK).($_GET["add_comment"] == "on" ? "&comment=".urlencode($_GET["comment"]) : "")."&auth=".$auth.($pauth ? "&pauth=$pauth" : "").(isset($_GET["audl"]) ? "&audl=doum" : ""));
?>