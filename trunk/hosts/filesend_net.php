<?php
if (!defined('RAPIDLEECH'))
  {
  require_once("index.html");
  exit;
  }
				
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $Referer, 0, 0, 0, $_GET["proxy"],$pauth);
	is_page($page);
	
	is_present($page,"File Not Found");
	
	if(preg_match('%FileSend -(\r|\n)*(.*)(\r|\n)*</title>%i', $page, $fname))
		{
		$FileName = $fname[2];
		}
		
	preg_match('/action="(.*dl\.php\?.*?)"/i', $page, $loc);
	preg_match('/sid"\s*value="(.*?)"/i', $page, $sid);
	preg_match('/country"\s*value="(.*?)"/i', $page, $coun);
	$Href = $loc[1];
	$Url = parse_url($Href);
	$FileName = !$FileName ? basename($Url["path"]) : $FileName;
	$post = array();
	$post["sid"] = $sid[1];
	$post["country"] = $coun[1];

	insert_location("$PHP_SELF?filename=".urlencode($FileName)."&force_name=".urlencode($FileName)."&host=".$Url["host"]."&path=".urlencode($Url["path"].($Url["query"] ? "?".$Url["query"] : ""))."&referer=".urlencode($Referer)."&post=".urlencode(serialize($post))."&email=".($_GET["domail"] ? $_GET["email"] : "")."&partSize=".($_GET["split"] ? $_GET["partSize"] : "")."&method=".$_GET["method"]."&proxy=".($_GET["useproxy"] ? $_GET["proxy"] : "")."&saveto=".$_GET["path"]."&link=".urlencode($LINK).($_GET["add_comment"] == "on" ? "&comment=".urlencode($_GET["comment"]) : "")."&auth=".$auth.($pauth ? "&pauth=$pauth" : "").(isset($_GET["audl"]) ? "&audl=doum" : ""));
?>