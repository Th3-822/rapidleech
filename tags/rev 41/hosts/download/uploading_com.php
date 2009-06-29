<?php
if (!defined('RAPIDLEECH'))
{
	require_once("index.html");
	exit;
}

$uploading_username="";  // username
$uploading_password="";  // password

if (!uploading_username || !$uploading_password){

$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, 0, 0, 0, $_GET["proxy"],$pauth);
//file_put_contents("uploading_1.txt", $page);
is_page($page);
is_present($page, "Sorry, the file requested by you does not exist on our servers.");

$cookie = GetCookies($page);

$post = Array();
$post["free"] = 1;
$cookie = 'redirect=1';
$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $LINK, $cookie, $post, 0, $_GET["proxy"],$pauth);
//file_put_contents("uploading_2.txt", $page);
is_page($page);
while(strpos($page,"Your IP address")){
$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $LINK, $cookie, $post, 0, $_GET["proxy"],$pauth);
//file_put_contents("uploading_2.txt", $page);
is_page($page);
$cc++; 
insert_timer(10,$cc." attempt to bypass message "."- Your IP address is already downloading a file."  );
if ($cc>=8) {html_error("Not succes the server still report - Your IP address is already downloading a file. Please wait until the download is completed. wait for more minutes and reattempt",0);
}    

}
/*
is_present($page,"Your IP address","Your IP address is already downloading a file. Please wait until the download is completed.");
*/
$countDown = trim(cut_str($page, "var c2168=", ";"));
if ($countDown==""){$countDown = 91;}
$FileName = substr(basename($Url["path"]), 0, -5);
$post = Array();
$post["free"] = 1;
$post["x"] = 1;
$coo = "";
$temp = "";
preg_match_all('/Set-Cookie: (.*);/U',$page,$temp);
$cookie = $temp[1];
$cookie = implode(';',$cookie);
insert_timer($countDown,"Wait for your turn");
$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $LINK, $cookie, $post, 0, $_GET["proxy"],$pauth);
$loc = "";
preg_match('/ocation: (.*)/',$page,$loc);
$Href = $loc[1];
$Url = parse_url($Href);
$FileName = !$FileName ? basename($Url["path"]) : $FileName;

insert_location("$PHP_SELF?cookie=".urlencode($cookie)."&filename=".urlencode($FileName)."&host=".$Url["host"]."&path=".urlencode($Url["path"].($Url["query"] ? "?".$Url["query"] : ""))."&referer=".urlencode($Referer)."&email=".($_GET["domail"] ? $_GET["email"] : "")."&partSize=".($_GET["split"] ? $_GET["partSize"] : "")."&method=".$_GET["method"]."&proxy=".($_GET["useproxy"] ? $_GET["proxy"] : "")."&saveto=".$_GET["path"]."&link=".urlencode($LINK).($_GET["add_comment"] == "on" ? "&comment=".urlencode($_GET["comment"]) : "")."&auth=".$auth.($pauth ? "&pauth=$pauth" : "").(isset($_GET["audl"]) ? "&audl=doum" : ""));
}else{
$in=parse_url("http://uploading.com/login/");
$post=array();
$post["log_ref"]="";
$post["login"]=$uploading_username;
$post["pwd"]=$uploading_password;
$page = geturl($in["host"], $in["port"] ? $in["port"] : 80, $in["path"].($in["query"] ? "?".$in["query"] : ""), "http://uploading.com/login/", 0, $post, 0, $_GET["proxy"],$pauth);
$cook=biscottiDiKaox($page);
$in=parse_url("http://uploading.com/");
$page = geturl($in["host"], $in["port"] ? $in["port"] : 80, $in["path"].($in["query"] ? "?".$in["query"] : ""), "http://uploading.com/login/", $cook, 0, 0, $_GET["proxy"],$pauth);
if(!strpos($page,"Logout")){
html_error("Login Failed , Bad username/password combination.",0);
}
$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $referer, $cook, 0, 0, $_GET["proxy"],$pauth);
$cook=biscottiDiKaox($page);
$tmp = basename($Url["path"]);
$FileName=str_replace(".html","",$tmp);
unset($post);
$post["premium"]="1";
$post["x"]="1";
$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $referer, $cook, $post, 0, $_GET["proxy"],$pauth);
is_present($page,"Location: /premium/","You are Free members,please insert a Premium account");
preg_match('/Location:.+?\\r/i', $page, $loca);
$redir = rtrim($loca[0]);
preg_match('/http:.*/i', $redir, $loca);
$Url=parse_url($loca[0]);

insert_location("$PHP_SELF?filename=".urlencode($FileName)."&host=".$Url["host"]."&path=".urlencode($Url["path"].($Url["query"] ? "?".$Url["query"] : ""))."&referer=".urlencode($Referer)."&email=".($_GET["domail"] ? $_GET["email"] : "")."&partSize=".($_GET["split"] ? $_GET["partSize"] : "")."&method=".$_GET["method"]."&proxy=".($_GET["useproxy"] ? $_GET["proxy"] : "")."&saveto=".$_GET["path"]."&link=".urlencode($LINK)."&cookie=".urlencode($cookie).($_GET["add_comment"] == "on" ? "&comment=".urlencode($_GET["comment"]) : "")."&auth=".$auth.($pauth ? "&pauth=$pauth" : "").(isset($_GET["audl"]) ? "&audl=doum" : ""));
}
function biscottiDiKaox($content)
 {
 preg_match_all("/Set-Cookie: (.*)\n/",$content,$matches);
 foreach ($matches[1] as $coll) {
 $bis0=split(";",$coll);
 $bis1=$bis0[0]."; ";
 if(strpos($bis1,"=deleted") || strpos($bis1,"=;")) {
 }else{
 $bis2=split("=",$bis1);
    $cek=" ".$bis2[0]."=";
if  (substr_count($bis,$cek)>0)
{$patrn=" ".$bis2[0]."=[^ ]+";
$bis=preg_replace("/$patrn/"," ".$bis1,$bis);     
} else {$bis.=$bis1;}}}  
$bis=str_replace("  "," ",$bis);     
return rtrim($bis);}
// written by kaox 24/05/2009
?>