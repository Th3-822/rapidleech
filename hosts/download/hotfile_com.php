<?php
if (!defined('RAPIDLEECH'))
  {
  require_once("index.html");
  exit;
  }
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
  	is_present($page,"File not found","File not found, the file is not present or bud link","0");
  }
  preg_match('/http:\/\/.+get[^\'"]+/', $page, $down);
  $LINK=rtrim($down['0']);
   $Url =parse_url($LINK);
   $FileName = basename($Url["path"]);
   $page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"], $Referer, 0, 0, 0, $_GET["proxy"],$pauth); 
     preg_match('/Location: *(.+)/', $page, $redir);
     $redirect=rtrim($redir[1]);
     $Url = parse_url($redirect);
	insert_location("$PHP_SELF?filename=".urlencode($FileName)."&host=".$Url["host"]."&path=".urlencode($Url["path"].($Url["query"] ? "?".$Url["query"] : ""))."&referer=".urlencode($Referer)."&email=".($_GET["domail"] ? $_GET["email"] : "")."&partSize=".($_GET["split"] ? $_GET["partSize"] : "")."&method=".$_GET["method"]."&proxy=".($_GET["useproxy"] ? $_GET["proxy"] : "")."&saveto=".$_GET["path"]."&link=".urlencode($LINK).($_GET["add_comment"] == "on" ? "&comment=".urlencode($_GET["comment"]) : "")."&auth=".$auth.($pauth ? "&pauth=$pauth" : "").(isset($_GET["audl"]) ? "&audl=doum" : ""));

// kaox
	
?>

