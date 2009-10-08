<?php

if (!defined('RAPIDLEECH'))
  {
  require_once("index.html");
  exit;
  }

        $page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $Referer, 0, 0, 0, $_GET["proxy"],$pauth);
        is_page($page);
        $cookie = GetCookies($page);
        $wait = cut_str ( $page ,'var waitTime=' ,';' );
        $dwn = cut_str ( $page ,"var dlLink=unescape('" ,"'" );
        insert_timer(10);
        $Url=parse_url(urldecode($dwn));
        $page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $Referer, $cookie, 0, 0, $_GET["proxy"],$pauth);
        is_page($page);
         $locat=cut_str ($page ,"Location: ","\r");
         $FileName=basename($locat);	 
         $Url=parse_url($locat);
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
 WRITTEN BY KAOX 06-oct-09
\*************************/ 
  
?>