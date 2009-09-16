<?php
if (!defined('RAPIDLEECH'))
{
	require_once("index.html");
	exit;
}
$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $Referer, 0, 0, 0, $_GET["proxy"],$pauth);
is_page($page);
preg_match('/Location:.*error/i', $page) ? html_error("Invalid File", 0) : '';
if(preg_match('/Location: (.*)/i', $page, $redir))
{
	$Href = trim($redir[1]);
	$Url = parse_url($Href);
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $Referer, 0, 0, 0, $_GET["proxy"],$pauth);
	is_page($page);
}
$cookie = GetCookies($page);
preg_match('/cu\((.*?)\);/', $page, $values);
$value = preg_split('/\',?\'?/', $values[1], -1, PREG_SPLIT_NO_EMPTY);
$qk = $value[0];
$pk = $value[1];
$r = $value[2];

$Href = "http://www.mediafire.com/dynamic/download.php?qk=$value[0]&pk=$value[1]&r=$value[2]";
$Url = parse_url($Href);
$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $Referer, $cookie, 0, 0, $_GET["proxy"],$pauth);
is_page($page);

$mL = cut_str($page, "var mL='", "';");
$mH = cut_str($page, "var mH='", "';");
$mY = cut_str($page, "var mY='", "';");
preg_match('%http://"\+mL\+\'/\'\s\+(.+)\+\s\'g/%', $page, $parts);

$temps = explode("+",$parts[1]);
foreach ($temps as $temp)
{
	if (empty($temp)) continue;
	preg_match('/'.trim($temp).' ?= ?\'(.*?)\';/', $page, $temp2);
	$mpath1.= $temp2[1];
}

$Href = 'http://'.$mL.'/'.$mpath1.'g/'.$mH.'/'.$mY;
$Url = parse_url($Href);
$FileName = !$FileName ? basename($Url["path"]) : $FileName;
insert_location("$PHP_SELF?filename=".urlencode($FileName)."&host=".$Url["host"]."&path=".urlencode($Url["path"].($Url["query"] ? "?".$Url["query"] : ""))."&referer=".urlencode($Referer)."&email=".($_GET["domail"] ? $_GET["email"] : "")."&partSize=".($_GET["split"] ? $_GET["partSize"] : "")."&method=".$_GET["method"]."&proxy=".($_GET["useproxy"] ? $_GET["proxy"] : "")."&saveto=".$_GET["path"]."&link=".urlencode($LINK).($_GET["add_comment"] == "on" ? "&comment=".urlencode($_GET["comment"]) : "")."&auth=".$auth.($pauth ? "&pauth=$pauth" : "").(isset($_GET["audl"]) ? "&audl=doum" : ""));

// edited by mrbrownee70
//updated by szalinski 15-Sep-09

?>