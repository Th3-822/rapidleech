<?php
if (!defined('RAPIDLEECH'))
  {
  require_once("index.html");
  exit;
  }
  
  ####### Free Account Info. ###########
$easyshare_Premium_user = ""; //  Set you username
$easyshare_Premium_pass = ""; //  Set your password
##############################
	function BiscottiDiKaox($content)
 {
 preg_match_all("/Set-Cookie: (.*)\n/",$content,$matches);
 foreach ($matches[1] as $coll) {
 $bis0=split(";",$coll);
 $bis1=$bis0[0]."; ";
 $bis2=split("=",$bis1);
if  (substr_count($bis,$bis2[0])>0)
{$patrn=$bis2[0]."[^ ]+"; 
$bis=preg_replace("/$patrn/",$bis1,$bis);     
} else{$bis.=$bis1 ; }}
$bis=str_replace("  "," ",$bis);     
return rtrim($bis);}

// login 
$login="http://www.easy-share.com/accounts/login";
$urlg=parse_url($login);
			$post["login"]=$easyshare_Premium_user;
			$post["password"]=$easyshare_Premium_pass;
			$post["remember"]="1";
$page = geturl($urlg["host"], $urlg["port"] ? $urlg["port"] : 80, $urlg["path"].($urlg["query"] ? "?".$urlg["query"] : ""), "http://www.easy-share.com/", 0, $post, 0, $_GET["proxy"],$pauth);			
$cook=BiscottiDiKaox($page);

// end login 

is_notpresent($cook, "PREMIUMSTATUS", "Login failed<br>Wrong login/password?");
	 
$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, $cook, 0, 0, $_GET["proxy"],$pauth);
    $FileName = !$FileName ? basename($Url["path"]) : $FileName;  	
	is_present($page,'File was deleted');
	is_present($page,'File not found');
    preg_match('/Location:.+?\\r/i', $page, $loca);
    $redir = rtrim($loca[0]);
    preg_match('/http:.+/i', $redir, $loca);
    $Url = parse_url($loca[0]);   
	$cookie=$cook."; ".BiscottiDiKaox($page);
	    
	insert_location("$PHP_SELF?filename=".urlencode($FileName)."&host=".$Url["host"]."&path=".urlencode($Url["path"].($Url["query"] ? "?".$Url["query"] : ""))."&referer=".urlencode($Referer)."&cookie=".urlencode($cookie)."&email=".($_GET["domail"] ? $_GET["email"] : "")."&partSize=".($_GET["split"] ? $_GET["partSize"] : "")."&method=".$_GET["method"]."&proxy=".($_GET["useproxy"] ? $_GET["proxy"] : "")."&saveto=".$_GET["path"]."&link=".$_POST["link2"].($_GET["add_comment"] == "on" ? "&comment=".urlencode($_GET["comment"]) : "").($pauth ? "&pauth=$pauth" : ""));

?>