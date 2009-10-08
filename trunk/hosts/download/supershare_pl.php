<?php

if (!defined('RAPIDLEECH'))
  {
  require_once("index.html");
  exit;
  }
$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, 0, 0, 0, $_GET["proxy"],$pauth);
is_page($page);
$locat=cut_str ($page ,"Location: ","\r");

if($locat){
$snap = cut_str ( $page ,"&code=" ,"\r" );
if($snap== "DL_FileNotFound"){
html_error( "Your requested file is not found" , 0 );
}else{
html_error( "The file cannot be downloaded , please check it in your browser for detail" , 0 );
}
} 
$cookie = GetCookies($page);
$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $Referer, $cookie, 0, 0, $_GET["proxy"],$pauth);
is_page($page);

$dwn = cut_str ( $page ,"downloadlink = '" ,"'" );
$FileName= urldecode(cut_str( $dwn ,"name=" ,"\r" ));
insert_timer(10);
$Url=parse_url($dwn);

$loc = "$PHP_SELF?filename=" . urlencode ( $FileName ) . 
	"&force_name=".urlencode($FileName) .
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
	           ($pauth ? "&pauth=$pauth" : "") . 
	"&cookie=" . urlencode($cookie) ;
insert_location ( $loc );

/*************************\
 WRITTEN BY KAOX 07-oct-09
\*************************/
?>