<?php


if (!defined('RAPIDLEECH'))
{
	require_once("index.html");
	exit;
}


$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"], $Referer, 0, 0, 0, $_GET["proxy"],$pauth);
is_page($page);
preg_match_all('/Set-Cookie: (.*);/U',$page,$temp);
$cookie = $temp[1];
$cook = implode(';',$cookie);
$Referer = $LINK;
preg_match('/ocation: (.*)/',$page,$loc);
$loc = trim($loc[1]);
$Url = parse_url($loc);
$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"], 0, $cook, 0, 0, $_GET["proxy"],$pauth);
preg_match('/<input.*name="file_key".*value="(.*)"/',$page,$file_key);
$file_key = $file_key[1];
$dllink = "http://ifile.it/download:dl_request?it=".$file_key.",type=na,esn=1";
$Url = parse_url($dllink);
$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].'?'.$Url['query'], $loc, $cook, 0, 0, $_GET["proxy"],$pauth);
$Url = parse_url($loc);
$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"], $loc, $cook, 0, 0, $_GET["proxy"],$pauth);
$Referer = $loc;

preg_match('/<a name="req_btn".*href="(.*)"/', $page, $loc);
$Href = $loc[1];
$Url = parse_url($Href);
$FileName = !$FileName ? basename($Url["path"]) : $FileName;

/*preg_match('/Set-Cookie: JSESSIONID=(.*); Path/', $page, $coo);
$cookie .= "; JSESSIONID=".$coo[1];*/

insert_location("$PHP_SELF?cookie=".urlencode($cook)."&filename=".urlencode($FileName)."&host=".$Url["host"]."&path=".urlencode($Url["path"].($Url["query"] ? "?".$Url["query"] : ""))."&referer=".urlencode($Referer)."&email=".($_GET["domail"] ? $_GET["email"] : "")."&partSize=".($_GET["split"] ? $_GET["partSize"] : "")."&method=".$_GET["method"]."&proxy=".($_GET["useproxy"] ? $_GET["proxy"] : "")."&saveto=".$_GET["path"]."&link=".urlencode($LINK).($_GET["add_comment"] == "on" ? "&comment=".urlencode($_GET["comment"]) : "")."&auth=".$auth.($pauth ? "&pauth=$pauth" : "").(isset($_GET["audl"]) ? "&audl=doum" : ""));
?>