<?php
if (!defined('RAPIDLEECH'))
{
  require_once("index.html");
  exit;
}
	$post = array();
	$post["download"] = 1;

	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $Referer, 0, $post, 0, $_GET["proxy"],$pauth);
	is_page($page);

	is_present($page,"File Not Found");

	if(preg_match('%location: (.+)\/%', $page, $loc)){
		$newurl = $loc[1];
		$Url = parse_url($newurl);
	}

	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $Referer, 0, $post, 0, $_GET["proxy"],$pauth);
	is_page($page);
	
	if (preg_match('/Array\((.+)\);link/', $page, $jsaray)) {
		$linkenc = $jsaray[1];
		$zsharelink = preg_replace('/\'[, ]?[ ,]?/six', '', $linkenc);
		$Url = parse_url($zsharelink);
	}elseif(preg_match('/<param name="URL" value="(.+)?">/', $page, $audio)){
		$zsharelink =$audio[1];
		$Url = parse_url($zsharelink);
	}
	$FileName = basename($Url["path"]);
	

insert_location("$PHP_SELF?filename=".urlencode($FileName)."&host=".$Url["host"]."&path=".urlencode($Url["path"].($Url["query"] ? "?".$Url["query"] : ""))."&referer=".urlencode($Referer)."&email=".($_GET["domail"] ? $_GET["email"] : "")."&partSize=".($_GET["split"] ? $_GET["partSize"] : "")."&method=".$_GET["method"]."&proxy=".($_GET["useproxy"] ? $_GET["proxy"] : "")."&saveto=".$_GET["path"]."&link=".urlencode($LINK).($_GET["add_comment"] == "on" ? "&comment=".urlencode($_GET["comment"]) : "").($pauth ? "&pauth=$pauth" : "").(isset($_GET["audl"]) ? "&audl=doum" : ""));
?>