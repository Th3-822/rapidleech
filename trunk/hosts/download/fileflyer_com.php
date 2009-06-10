<?php
if (!defined('RAPIDLEECH'))
{
  require_once("index.html");
  exit;
}
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $Referer, 0, 0, 0, $_GET["proxy"],$pauth);
	is_page($page);
	
	if(preg_match_all('/Set-Cookie: *(.+);/', $page, $cook)){
		$cookie = implode(';', $cook[1]);
	}else{
		html_error("Cookie not found.", 0);
	}
	
	if(preg_match('/CONTENT WARNING/', $page, $war)){
		preg_match('/var txt = "(.*)" ;/', $page, $reloc);
		preg_match('/__VIEWSTATE.*value="(.*?)"/', $page, $view);
		preg_match('/__EVENTVALIDATION.*value="(.*?)"/', $page, $even);
		preg_match('/__EVENTARGUMENT.*value="(.*?)"/', $page, $argu);
		$post = array();
		$post["__EVENTTARGET"] = 'Continue';
		$post["__VIEWSTATE"] = $view[1];
		$post["__EVENTVALIDATION"] = $even[1];
		$post["__EVENTARGUMENT"] = $argu[1];
		
		$newlink = "http://".$reloc[1].".fileflyer.com".$Url["path"];
		$Url = parse_url($newlink);
		$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $Referer, $cookie, $post, 0, $_GET["proxy"],$pauth);
	}
	
	preg_match('/handlink.*href="(http.*?)"/', $page, $dllink);
	$flink = $dllink[1];
	$Url = parse_url($flink);
	
	$FileName = !$FileName ? basename($Url["path"]) : $FileName;
	$FileName = preg_replace('/(%20| )/', '_', $FileName);

insert_location("$PHP_SELF?filename=".urlencode($FileName)."&host=".$Url["host"]."&path=".urlencode($Url["path"].($Url["query"] ? "?".$Url["query"] : ""))."&referer=".urlencode($Referer)."&cookie=".urlencode($cookie)."&email=".($_GET["domail"] ? $_GET["email"] : "")."&partSize=".($_GET["split"] ? $_GET["partSize"] : "")."&method=".$_GET["method"]."&proxy=".($_GET["useproxy"] ? $_GET["proxy"] : "")."&saveto=".$_GET["path"]."&link=".urlencode($LINK).($_GET["add_comment"] == "on" ? "&comment=".urlencode($_GET["comment"]) : "").($pauth ? "&pauth=$pauth" : "").(isset($_GET["audl"]) ? "&audl=doum" : ""));
?>