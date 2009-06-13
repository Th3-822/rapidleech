<?php
if (!defined('RAPIDLEECH'))
  {
  require_once("index.html");
  exit;
  }
if (($_GET["premium_acc"] == "on" && $_GET["premium_user"] && $_GET ["premium_pass"]) || ($_GET["premium_acc"] == "on" && $premium_acc["hotfile"]["user"] && $premium_acc["hotfile"]["pass"]))
  {
	$post = array();
	$post["user"] = $_GET["premium_user"] ? $_GET["premium_user"] : $premium_acc["hotfile"]["user"];
	$post["pass"] = $_GET["premium_pass"] ? $_GET["premium_pass"] : $premium_acc["hotfile"]["pass"];
	$post["returnto"] = "/premium.html";
	
        preg_match("/([0-9]+)/", $Url[path], $finalcook);
        $id = $finalcook[1];
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, "/premium.html", 0, 0, 0, 0, $_GET["proxy"],$pauth);
	is_page($page);

	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, "/login.php", 0, 0, $post, 0, $_GET["proxy"],$pauth);
	is_page($page);

preg_match_all("/Set-Cookie: ([^\;]+)/", $page, $matches);
	$cookies = implode("; ", $matches[1]);
$Referer = "http://hotfile.com/login.php";
        $page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, "/premium.html", $Referer, $cookies, 0, 0, $_GET["proxy"],$pauth);
	is_page($page);	

	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"], 0, $cookies, 0, 0, $_GET["proxy"],$pauth);
                if (preg_match('/Location: *(.+)/', $page, $redir)) {
        $redirect=rtrim($redir[1]);
        $Url = parse_url($redirect);
     	$FileName = !$FileName ? basename($Url["path"]) : $FileName;
	insert_location("$PHP_SELF?filename=".urlencode($FileName)."&host=".$Url["host"]."&path=".urlencode($Url["path"].($Url["query"] ? "?".$Url["query"] : ""))."&referer=".urlencode($Referer)."&email=".($_GET["domail"] ? $_GET["email"] : "")."&partSize=".($_GET["split"] ? $_GET["partSize"] : "")."&method=".$_GET["method"]."&proxy=".($_GET["useproxy"] ? $_GET["proxy"] : "")."&saveto=".$_GET["path"]."&link=".urlencode($link).($_GET["add_comment"] == "on" ? "&comment=".urlencode($_GET["comment"]) : "")."&auth=".$auth.($pauth ? "&pauth=$pauth" : "").(isset($_GET["audl"]) ? "&audl=doum" : ""));
}  else  {
        preg_match('/<h3 style=\'margin-top: 20px\'><a href="*(.+)"/', $page, $redir);
        $redirect=rtrim($redir[1]);
        $Url = parse_url($redirect);
        $FileName = !$FileName ? basename($Url["path"]) : $FileName;
	insert_location("$PHP_SELF?filename=".urlencode($FileName)."&host=".$Url["host"]."&path=".urlencode($Url["path"].($Url["query"] ? "?".$Url["query"] : ""))."&referer=".urlencode($Referer)."&email=".($_GET["domail"] ? $_GET["email"] : "")."&partSize=".($_GET["split"] ? $_GET["partSize"] : "")."&method=".$_GET["method"]."&proxy=".($_GET["useproxy"] ? $_GET["proxy"] : "")."&saveto=".$_GET["path"]."&link=".urlencode($link).($_GET["add_comment"] == "on" ? "&comment=".urlencode($_GET["comment"]) : "")."&auth=".$auth.($pauth ? "&pauth=$pauth" : "").(isset($_GET["audl"]) ? "&audl=doum" : ""));


}


  }
else
  {
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

}

?>
