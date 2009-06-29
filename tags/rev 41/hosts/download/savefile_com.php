<?php
if (!defined('RAPIDLEECH'))
  {
  require_once("index.html");
  exit;
  }
		$filed=split("/",$Url["path"]);		
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"], $Referer, 0, 0, 0, $_GET["proxy"],$pauth);
	is_page($page);
	is_present($page,"File not found");
	
	preg_match_all('/Set-Cookie: *(.+);/', $page, $cook);
	$cookie = implode(';', $cook[1]);
    $dwnlk="http://www.savefile.com/downloadmin/".$filed["2"]."?".$cookie;
	$Url = parse_url($dwnlk);
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"], $Referer, $cookie, 0, 0, $_GET["proxy"],$pauth);
	is_page($page);
	preg_match('/url=[^\'"]+/', $page, $down);
	$downf = str_replace("url=","",$down[0]);
	$Url = parse_url($downf);
	$FileName = !$FileName ? basename($Url["path"]) : $FileName;

	insert_location("$PHP_SELF?filename=".urlencode($FileName)."&host=".$Url["host"]."&path=".urlencode($Url["path"].($Url["query"] ? "?".$Url["query"] : ""))."&referer=".urlencode($Referer)."&email=".($_GET["domail"] ? $_GET["email"] : "")."&partSize=".($_GET["split"] ? $_GET["partSize"] : "")."&method=".$_GET["method"]."&proxy=".($_GET["useproxy"] ? $_GET["proxy"] : "")."&saveto=".$_GET["path"]."&link=".urlencode($LINK).($_GET["add_comment"] == "on" ? "&comment=".urlencode($_GET["comment"]) : "")."&auth=".$auth.($pauth ? "&pauth=$pauth" : "").(isset($_GET["audl"]) ? "&audl=doum" : ""));
	// fixed by kaox
?>