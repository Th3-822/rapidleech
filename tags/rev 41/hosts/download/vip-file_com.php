<?php
if (!defined('RAPIDLEECH'))
  {
  require_once("index.html");
  exit;
  }
   	

	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"], $Referer, $cookie, 0, 0, $_GET["proxy"],$pauth);
	is_page($page);
	is_present($page,"File not found");
	preg_match('/http:\/\/vip-file\.com\/download[^"]+/i', $page, $loca);
	$Url = parse_url($loca[0]);
    $page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"], $Referer, $cookie, 0, 0, $_GET["proxy"],$pauth);
    preg_match('/Location:.+?\\r/i', $page, $loca);
    $redir = rtrim($loca[0]);
    preg_match('/http:.*/i', $redir, $loca);
    $Url=parse_url($loca[0]);
    $page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"], $Referer, $cookie, 0, 0, $_GET["proxy"],$pauth);
    preg_match('/Location:.+?\\r/i', $page, $loca);
    $redir = rtrim($loca[0]);
    preg_match('/http:.*/i', $redir, $loca);
    $Url=parse_url($loca[0]);
    $FileName = !$FileName ? basename($Url["path"]) : $FileName;
insert_location("$PHP_SELF?filename=".urlencode($FileName)."&host=".$Url["host"]."&port=".$Url["port"]."&path=".urlencode($Url["path"].($Url["query"] ? "?".$Url["query"] : ""))."&referer=".urlencode($Referer)."&email=".($_GET["domail"] ? $_GET["email"] : "")."&partSize=".($_GET["split"] ? $_GET["partSize"] : "")."&cookie=".urlencode($cookie)."&method=".$_GET["method"]."&proxy=".($_GET["useproxy"] ? $_GET["proxy"] : "")."&saveto=".$_GET["path"]."&link=".urlencode($LINK).($_GET["add_comment"] == "on" ? "&comment=".urlencode($_GET["comment"]) : "")."&auth=".$auth.($pauth ? "&pauth=$pauth" : "").(isset($_GET["audl"]) ? "&audl=doum" : ""));
	// written by kaox 24/05/09

?>