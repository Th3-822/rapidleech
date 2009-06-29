<?php
if (!defined('RAPIDLEECH'))
  {
  require_once("index.html");
  exit;
  }
				
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"], $Referer, 0, 0, 0, $_GET["proxy"],$pauth);
	is_page($page);
	//preg_match_all('/Set-Cookie: *(.+);/', $page, $cook);
	//$cookie = implode(';', $cook[1]);
	preg_match_all('/Set-Cookie: ([^;]+)/', $page, $cook);
	$cookie = implode('; ', $cook[1]);
	
if(preg_match('/Location: (.*)/', $page, $newredir))
{
	$Url = parse_url($newredir[1]);
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"], $Referer, $cookie, 0, 0, $_GET["proxy"],$pauth);
	is_page($page);
//}elseif(preg_match('/href="(.*html)".*Download Now/', $page, $newredir))
}elseif(preg_match('/href="([^"]+)".*Download Now/', $page, $newredir))
{
	$redir = 'http://'.$Url["host"].$newredir[1];
	//$Url = parse_url($redir);
	$Url = parse_url($newredir[1]);
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"], $Referer, $cookie, 0, 0, $_GET["proxy"],$pauth);
	is_page($page);
}
	if(preg_match('/var c = ([0-9]+);/', $page, $count))
	{
	$countDown = $count[1];
	insert_timer($countDown, "Waiting link timelock");
	}
//die($page);
if(preg_match('%window\.location = "(http://.+?)";%', $page, $redir)){
	$link = $redir[1];
}elseif(preg_match('/(http.*?)\'.*Click here to download/', $page, $redir)){
	$link = $redir[1];
}else{
	html_error("Download-link not found.", 0);
}

$Url = parse_url($link);
$FileName = !$FileName ? basename($Url["path"]) : $FileName;

insert_location("$PHP_SELF?filename=".urlencode($FileName)."&host=".$Url["host"]."&path=".urlencode($Url["path"].($Url["query"] ? "?".$Url["query"] : ""))."&referer=".urlencode($Referer)."&email=".($_GET["domail"] ? $_GET["email"] : "")."&partSize=".($_GET["split"] ? $_GET["partSize"] : "")."&method=".$_GET["method"]."&proxy=".($_GET["useproxy"] ? $_GET["proxy"] : "")."&saveto=".$_GET["path"]."&link=".urlencode($LINK).($_GET["add_comment"] == "on" ? "&comment=".urlencode($_GET["comment"]) : "")."&auth=".$auth.($pauth ? "&pauth=$pauth" : "").(isset($_GET["audl"]) ? "&audl=doum" : ""));
?>