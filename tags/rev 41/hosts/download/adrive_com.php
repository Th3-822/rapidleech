<?php
if (!defined('RAPIDLEECH'))
  {
  require_once("index.html");
  exit;
  }
$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"], 0, 0, 0, 0, $_GET["proxy"],$pauth);
is_page($page);
if(preg_match('/Location: *(.*)/i', $page, $redir)){
	$href = trim($redir[1]);
	$Url = parse_url($href);
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"], 0, 0, 0, 0, $_GET["proxy"],$pauth);
	is_page($page);
}
if(preg_match('/location="(.*)"/i', $page, $redir)){
	$href = 'http://www.adrive.com'.trim($redir[1]);
	$Url = parse_url($href);
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"], 0, 0, 0, 0, $_GET["proxy"],$pauth);
	is_page($page);
}
if(preg_match_all('/Set-Cookie: *(.+?);/', $page, $cook)){
	$cookie = implode(";", $cook[1]);
}else{
	html_error("Cookie not found.", 0);
}
if(preg_match('/location\.href *= *"(.+)"/i', $page, $redir)){
    $href = trim($redir[1]);
}else{
	html_error("URL not found.", 0);
}
$Url = parse_url($href);
$FileName = !$FileName ? basename($Url["path"]) : $FileName;

insert_location("$PHP_SELF?filename=".urlencode($FileName)."&host=".$Url["host"]."&path=".urlencode($Url["path"].($Url["query"] ? "?".$Url["query"] : ""))."&referer=".urlencode($Referer)."&cookie=".urlencode($cookie)."&email=".($_GET["domail"] ? $_GET["email"] : "")."&partSize=".($_GET["split"] ? $_GET["partSize"] : "")."&method=".$_GET["method"]."&proxy=".($_GET["useproxy"] ? $_GET["proxy"] : "")."&saveto=".$_GET["path"]."&link=".urlencode($LINK).($_GET["add_comment"] == "on" ? "&comment=".urlencode($_GET["comment"]) : "").($pauth ? "&pauth=$pauth" : "").(isset($_GET["audl"]) ? "&audl=doum" : ""));
?>