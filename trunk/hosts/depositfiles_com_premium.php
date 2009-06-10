<?php
if (!defined('RAPIDLEECH'))
  {
  require_once("index.html");
  exit;
  }
  
  ####### Free Account Info. ###########
$depositfiles_Premium_user = ""; //  Set you username
$depositfiles_Premium_pass = ""; //  Set your password
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
$login="http://depositfiles.com/en/login.php";
$urlg=parse_url($login);
			$post["login"]=$depositfiles_Premium_user;
			$post["password"]=$depositfiles_Premium_pass;
			$post["go"]="1";
$page = geturl($urlg["host"], $urlg["port"] ? $urlg["port"] : 80, $urlg["path"].($urlg["query"] ? "?".$urlg["query"] : ""), "http://depositfiles.com/en/", 0, $post, 0, $_GET["proxy"],$pauth);			
$cook=BiscottiDiKaox($page);

// end login 

is_notpresent($cook, "autologin", "Login failed<br>Wrong login/password?");

$Url["path"]=preg_replace("/\/.*files/","/en/files",$Url["path"]);
$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, $cook, 0, 0, $_GET["proxy"],$pauth); 
is_present($page,'has been removed',"The file has been removed");
preg_match("/http:\/\/.+auth-[^'\"]+/i", $page, $dw);
$Url=parse_url($dw[0]);
    $FileName = basename($Url["path"]); 
	    
	insert_location("$PHP_SELF?filename=".urlencode($FileName)."&host=".$Url["host"]."&path=".urlencode($Url["path"].($Url["query"] ? "?".$Url["query"] : ""))."&referer=".urlencode($Referer)."&cookie=".urlencode($cookie)."&email=".($_GET["domail"] ? $_GET["email"] : "")."&partSize=".($_GET["split"] ? $_GET["partSize"] : "")."&method=".$_GET["method"]."&proxy=".($_GET["useproxy"] ? $_GET["proxy"] : "")."&saveto=".$_GET["path"]."&link=".$_POST["link2"].($_GET["add_comment"] == "on" ? "&comment=".urlencode($_GET["comment"]) : "").($pauth ? "&pauth=$pauth" : ""));

?>