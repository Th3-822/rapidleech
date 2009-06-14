<?php
if (!defined('RAPIDLEECH'))
  {
  require_once("index.html");
  exit;
  }
$hotfile_username="";  // username
$hotfile_password="";  // password

if (!$hotfile_username || !$hotfile_password){
  
  $page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"], $Referer, 0, 0, 0, $_GET["proxy"],$pauth);
  preg_match('/http:\/\/.+get[^\'"]+/', $page, $down);
  if ($down['0'] == ""){
  	$action=cut_str($page,"action value=",">");
  	$tm=cut_str($page,"tm value=",">");
  	$tmhash=cut_str($page,"tmhash value=",">");
  	$wait=cut_str($page,"wait value=",">");
  	$waithash=cut_str($page,"waithash value=",">");
  	$post=array();
  	$post["action"] =$action;
    $post["tm"] = $tm;
  	$post["tmhash"] = $tmhash;
  	$post["wait"] = $wait;
  	$post["waithash"] = $waithash;
    insert_timer($wait, "Waiting link timelock"); 
  	 $page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"], $Referer, 0, $post, 0, $_GET["proxy"],$pauth);
  }else{
  	is_present($page,"File not found","File not found, the file is not present or bad link","0");
  }
  preg_match('/http:\/\/.+get[^\'"]+/i', $page, $down);
  $LINK=rtrim($down['0']);
   $Url =parse_url($LINK);
   $FileName = basename($Url["path"]);
   $page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"], $Referer, 0, 0, 0, $_GET["proxy"],$pauth); 
     preg_match('/Location: *(.+)/', $page, $redir);
     if (strpos($redir[1],"http://")===false) {html_error("Server problem. Please try again after",0);}
     $redirect=rtrim($redir[1]);
     $Url = parse_url($redirect);
	insert_location("$PHP_SELF?filename=".urlencode($FileName)."&host=".$Url["host"]."&path=".urlencode($Url["path"].($Url["query"] ? "?".$Url["query"] : ""))."&referer=".urlencode($Referer)."&email=".($_GET["domail"] ? $_GET["email"] : "")."&partSize=".($_GET["split"] ? $_GET["partSize"] : "")."&method=".$_GET["method"]."&proxy=".($_GET["useproxy"] ? $_GET["proxy"] : "")."&saveto=".$_GET["path"]."&link=".urlencode($LINK).($_GET["add_comment"] == "on" ? "&comment=".urlencode($_GET["comment"]) : "")."&auth=".$auth.($pauth ? "&pauth=$pauth" : "").(isset($_GET["audl"]) ? "&audl=doum" : ""));
}else{
$in=parse_url("http://hotfile.com/login.php");
$post=array();
$post["returnto"]="/";
$post["user"]=$hotfile_username;
$post["pass"]=$hotfile_password;
$page = geturl($in["host"], $in["port"] ? $in["port"] : 80, $in["path"].($in["query"] ? "?".$in["query"] : ""), "http://hotfile.com/", 0, $post, 0, $_GET["proxy"],$pauth);	
preg_match('/auth=\w{64}/i', $page, $ook);
$cook=$ook[0];
if(!$cook){
html_error("Login Failed , Bad username/password combination.",0);
}
$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"], $Referer, $cook, 0, 0, $_GET["proxy"],$pauth); 
preg_match('/http:\/\/.+get[^"\']+/i', $page, $dwn);
if (!$dwn){
html_error("Not premium login inserted. If you want download as a free membership delete username and password in hotfile.com.php plugin",0);       
}
$Url =parse_url($dwn[0]);
$FileName = basename($Url["path"]);
insert_location("$PHP_SELF?filename=".urlencode($FileName)."&host=".$Url["host"]."&path=".urlencode($Url["path"].($Url["query"] ? "?".$Url["query"] : ""))."&referer=".urlencode($Referer)."&email=".($_GET["domail"] ? $_GET["email"] : "")."&partSize=".($_GET["split"] ? $_GET["partSize"] : "")."&method=".$_GET["method"]."&proxy=".($_GET["useproxy"] ? $_GET["proxy"] : "")."&saveto=".$_GET["path"]."&link=".urlencode($LINK).($_GET["add_comment"] == "on" ? "&comment=".urlencode($_GET["comment"]) : "")."&auth=".$auth.($pauth ? "&pauth=$pauth" : "").(isset($_GET["audl"]) ? "&audl=doum" : ""));

}

//written by kaox 09/06/09
	
?>

