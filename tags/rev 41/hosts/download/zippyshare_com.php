<?php
if (!defined('RAPIDLEECH'))
{
  require_once("index.html");
  exit;
  }
 function biscotti($content) {
        is_page($content);
        preg_match_all("/Set-Cookie: (.*)\n/",$content,$matches);
        foreach ($matches[0] as $coll) {
        $bis.=cut_str($coll,"Set-Cookie: ","; ")."; ";    
        }return $bis;}

			
$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"], $Referer, $cookie, 0, 0, $_GET["proxy"],$pauth);
is_page($page);
$cookie=biscotti($page);
$tmp= cut_str($page,"var comeonguys = '","';");
$tmp=urldecode($tmp);
preg_match("/http.+/i",$tmp,$link);


$Url = parse_url($link[0]);

$FileName = cut_str($page,"Name: </strong>","</font>");
insert_timer("10", "Waiting link timelock.","",true);

insert_location("$PHP_SELF?filename=".urlencode($FileName)."&force_name=".urlencode($FileName)."&host=".$Url["host"]."&path=".urlencode($Url["path"].($Url["query"] ? "?".$Url["query"] : ""))."&referer=".urlencode($Referer)."&email=".($_GET["domail"] ? $_GET["email"] : "")."&partSize=".($_GET["split"] ? $_GET["partSize"] : "")."&method=".$_GET["method"]."&cookie=".urlencode($cookie)."&proxy=".($_GET["useproxy"] ? $_GET["proxy"] : "")."&saveto=".$_GET["path"]."&link=".urlencode($LINK).($_GET["add_comment"] == "on" ? "&comment=".urlencode($_GET["comment"]) : "")."&auth=".$auth.($pauth ? "&pauth=$pauth" : ""));
/*
Writted by kaox 06/05/09
*/

?>