<?php

if (!defined('RAPIDLEECH'))
{
  require_once("index.html");
  exit;
  }
			
$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"], $Referer, 0, 0, 0, $_GET["proxy"],$pauth);
is_page($page);
$lk = urldecode(cut_str($page, "var wannaplayagameofpong = 'fck", "';"));
$lk=str_replace("http://xxx","http://www",$lk) ;
$cookie=GetCookies($page);
$Url = parse_url($lk);
$FileName=cut_str($page,"Name: </strong>","</font>");
insert_timer("10", "Waiting link timelock.","",true);
insert_location("$PHP_SELF?filename=".urlencode($FileName)."&force_name=".urlencode($FileName)."&host=".$Url["host"]."&path=".urlencode($Url["path"].($Url["query"] ? "?".$Url["query"] : ""))."&referer=".urlencode($Referer)."&email=".($_GET["domail"] ? $_GET["email"] : "")."&partSize=".($_GET["split"] ? $_GET["partSize"] : "")."&method=".$_GET["method"]."&cookie=".urlencode($cookie)."&proxy=".($_GET["useproxy"] ? $_GET["proxy"] : "")."&saveto=".$_GET["path"]."&link=".urlencode($LINK).($_GET["add_comment"] == "on" ? "&comment=".urlencode($_GET["comment"]) : "")."&auth=".$auth.($pauth ? "&pauth=$pauth" : ""));

/*
Writted by kaox 06/05/09
Updated by szalinski 10-Sep-09
Updated by kaox 20-Sep-09
*/

?>