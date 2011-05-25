<?php
//Created by shy2play for kaskus.us and my blog untamedsolitude.co.cc
if (!defined('RAPIDLEECH'))
  {
  require_once("index.html");
  exit;
  }

$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"], $Referer, 0, 0, 0, $_GET["proxy"],$pauth);

is_page($page);
is_present($page,"File Not Found", "File Not Found", 0);

preg_match_all('/Set-Cookie: (.*);/U',$page,$temp);
$cookie = $temp[1];
$cookie = implode(';',$cookie);
$Referer = $LINK;
	
$alias_id = cut_str($page,"var __alias_id				=	'","'");
		
$generate_download = 'http://ifile.it/download:dl_request?alias_id=' . $alias_id . '&type=na&kIjs09=845&e94fa1af87=35490';
$Url = parse_url($generate_download);
		
$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $LINK, $cookie, 0, 0, $_GET["proxy"],$pauth);
is_page($page);

$Url = parse_url($LINK);
$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $LINK, $cookie, 0, 0, $_GET["proxy"],$pauth);
$Referer = $LINK;

$Url = parse_url($LINK);
$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $LINK, $cookie, 0, 0, $_GET["proxy"],$pauth);
$Referer = $LINK;

$dwn = cut_str($page,'<a target="_blank" href="','"');

if (!$dwn) html_error( "Error getting download link" , 0 ); 

$Url=parse_url($dwn);
$FileName=basename($dwn);


$loc = "$PHP_SELF?filename=" . urlencode ( $FileName ) .
		"&host=" . $Url ["host"] . 
		"&port=" . $Url ["port"] . 
		"&path=" . urlencode ( $Url ["path"] . ($Url ["query"] ? "?" . $Url ["query"] : "") ) . 
		"&referer=" . urlencode ( $Referer ) . 
		"&email=" . ($_GET ["domail"] ? $_GET ["email"] : "") . 
		"&partSize=" . ($_GET ["split"] ? $_GET ["partSize"] : "") . 
		"&method=" . $_GET ["method"] . 
		"&proxy=" . ($_GET ["useproxy"] ? $_GET ["proxy"] : "") . 
		"&saveto=" . $_GET ["path"] . 
		"&link=" . urlencode ( $LINK ) . 
		           ($_GET ["add_comment"] == "on" ? "&comment=" . urlencode ( $_GET ["comment"] ) : "") .
		            $auth . 
		           ($pauth ? "&pauth=$pauth" : "");
		insert_location ( $loc );