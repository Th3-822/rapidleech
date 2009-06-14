<?php
if (!defined('RAPIDLEECH'))
  {
  require_once("index.html");
  exit;
  }
$post=array();
$post["free"]  ="";
$post["x"]  =rand(10,150);
$post["y"]  =rand(10,150);
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"], $Referer, 0, $post, 0, $_GET["proxy"],$pauth);
	is_page($page);
	is_present($page,"File not found");
	preg_match('/http:\/\/.+get[^"\']+/i', $page, $loca);
	$Url = parse_url($loca[0]);
    insert_timer(60, "Wait your turn");
	$FileName = !$FileName ? basename($Url["path"]) : $FileName;
	insert_location("$PHP_SELF?filename=".urlencode($FileName)."&host=".$Url["host"]."&path=".urlencode($Url["path"].($Url["query"] ? "?".$Url["query"] : ""))."&referer=".urlencode($Referer)."&email=".($_GET["domail"] ? $_GET["email"] : "")."&partSize=".($_GET["split"] ? $_GET["partSize"] : "")."&method=".$_GET["method"]."&proxy=".($_GET["useproxy"] ? $_GET["proxy"] : "")."&saveto=".$_GET["path"]."&link=".urlencode($LINK).($_GET["add_comment"] == "on" ? "&comment=".urlencode($_GET["comment"]) : "")."&auth=".$auth.($pauth ? "&pauth=$pauth" : "").(isset($_GET["audl"]) ? "&audl=doum" : ""));
	// written by kaox 24/05/09
?>